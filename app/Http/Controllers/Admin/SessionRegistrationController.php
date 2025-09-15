<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use App\Models\AttendanceRecord;
use App\Models\GownStock;
use App\Models\GownCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Guest;

class SessionRegistrationController extends Controller
{
    public function index()
    {
        $regs = SessionRegistration::with(['user', 'session'])->get();

        return Inertia::render('admin/Sessions/Registrations', [
            'registrations' => $regs->map(function ($r) {
                $status = AttendanceRecord::where('user_id', $r->user_id)
                    ->where('session_id', $r->convocation_session_id)
                    ->value('status');

                return [
                    'id'           => $r->id,
                    'student_name' => $r->user->name,
                    'session_name' => $r->session->name,
                    'guest_count'  => $r->guest_count,
                    'gown_size'    => $r->gown_size,
                    'status'       => $status ?? 'pending',
                    'created_at'   => $r->created_at?->toDateTimeString(),
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'guest_count'    => 'required|integer|min:0|max:' . config('convocation.max_guest_per_student', 2),
            'gown_size'      => 'nullable|in:XS,S,M,L,XL',
            'collection_date'=> 'nullable|date',
        ]);

        // Find the student
        $user = \App\Models\User::with('course.convocationSession')->findOrFail($data['user_id']);

        // Auto-detect session from their course
        $session = $user->course?->convocationSession;
        if (!$session) {
            abort(422, 'This student’s course is not mapped to a convocation session yet.');
        }

        $data['convocation_session_id'] = $session->id;

        DB::transaction(function () use ($data) {
            // Lock target session
            $session = ConvocationSession::where('id', $data['convocation_session_id'])
                ->lockForUpdate()->firstOrFail();

            // Enforce student quota
            if ($session->registered >= $session->quota) {
                abort(422, 'This session is full.');
            }

            // Enforce guest quota
            if ($session->guest_registered + $data['guest_count'] > $session->guest_quota) {
                abort(422, 'Guest quota for this session is exceeded.');
            }

            // Existing registration?
            $existing       = SessionRegistration::where('user_id', $data['user_id'])->first();
            $oldSessionId   = $existing?->convocation_session_id;
            $oldGown        = $existing?->gown_size;
            $oldGuestCount  = $existing?->guest_count ?? 0;

            // Move / create registration
            SessionRegistration::updateOrCreate(
                ['user_id' => $data['user_id']],
                [
                    'convocation_session_id' => $session->id,
                    'guest_count'            => $data['guest_count'],
                    'attendance_confirmed'   => true,
                    'gown_size'              => $data['gown_size'] ?? null,
                    'collection_date'        => $data['collection_date'] ?? null,
                ]
            );

            // Recreate guest rows if the count changed
            if ($data['guest_count'] > 0 && (!$existing || $oldGuestCount !== $data['guest_count'])) {
                Guest::where('user_id', $data['user_id'])->delete();
                for ($i = 1; $i <= $data['guest_count']; $i++) {
                    Guest::create([
                        'user_id' => $data['user_id'],
                        'name'    => "Guest $i for {$data['user_id']}",
                    ]);
                }
            }

            // Adjust counters if new or moved
            if (!$existing || $oldSessionId !== $session->id) {
                if ($oldSessionId) {
                    $old = ConvocationSession::where('id', $oldSessionId)->lockForUpdate()->first();
                    if ($old) {
                        $old->decrement('registered');
                        $old->decrement('guest_registered', $oldGuestCount);
                    }
                }
                $session->increment('registered');
                $session->increment('guest_registered', $data['guest_count']);
            } elseif ($oldGuestCount !== $data['guest_count']) {
                $session->increment('guest_registered', $data['guest_count'] - $oldGuestCount);
            }

            // --- Attendance record (DO NOT rotate token) ---
            // Keep any existing token so previously generated QR codes remain valid.
            $record = AttendanceRecord::firstOrCreate(
                ['user_id' => $data['user_id']],
                [
                    'attendance_token' => (string) Str::uuid(),
                    'status'           => 'pending',
                    'session_id'       => $session->id,
                ]
            );

            // Align session & promote to registered without changing the token
            $record->update([
                'session_id' => $session->id,
                'status'     => 'registered',
            ]);

            // Gown stock
            if (!empty($data['gown_size'])) {
                if ($oldGown && $oldGown !== $data['gown_size']) {
                    $oldStock = GownStock::where('size', $oldGown)->lockForUpdate()->first();
                    if ($oldStock) {
                        $oldStock->increment('available');
                    }
                }

                $newStock = GownStock::where('size', $data['gown_size'])->lockForUpdate()->first();
                if (!$newStock || $newStock->available <= 0) {
                    abort(422, 'Selected gown size is no longer available.');
                }
                if (!$existing || $oldGown !== $data['gown_size']) {
                    $newStock->decrement('available');
                }

                GownCollection::updateOrCreate(
                    ['user_id' => $data['user_id']],
                    [
                        'size'            => $data['gown_size'],
                        'status'          => 'reserved',
                        'collection_date' => $data['collection_date'] ?? null,
                    ]
                );
            }
        });

        return back()->with('message', 'Registration saved.');
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $r = SessionRegistration::withTrashed()->findOrFail($id);

            // Session counters
            $session = ConvocationSession::where('id', $r->convocation_session_id)
                ->lockForUpdate()->first();
            if ($session) {
                $session->decrement('registered');
                $session->decrement('guest_registered', $r->guest_count);
            }

            // Gown restore
            if ($r->gown_size) {
                $stock = GownStock::where('size', $r->gown_size)->lockForUpdate()->first();
                if ($stock) {
                    $stock->increment('available');
                }
                GownCollection::where('user_id', $r->user_id)->delete();
            }

            // Attendance cleanup (cancelled = QR intentionally invalid)
            AttendanceRecord::where('user_id', $r->user_id)
                ->where('session_id', $r->convocation_session_id)
                ->delete();

            $r->delete();
        });

        return back()->with('message', 'Registration cancelled.');
    }
}
