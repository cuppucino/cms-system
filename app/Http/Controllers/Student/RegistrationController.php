<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use App\Models\GownStock;
use App\Models\GownCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use App\Models\AttendanceRecord;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $registration = SessionRegistration::with('session')
            ->where('user_id', $user->id)
            ->first();

        return Inertia::render('student/Registration', [
            'registration' => $registration ? [
                'attendance_confirmed' => $registration->attendance_confirmed,
                'convocation_session_id' => $registration->convocation_session_id,
                'guest_count' => $registration->guest_count,
                'gown_size' => $registration->gown_size,
                'collection_date' => $registration->collection_date?->toDateString(),
            ] : null,
            'sessions' => ConvocationSession::select([
                'id',
                'name',
                'date',
                'location',
                'quota',
                'registered',
                'guest_quota',
                'guest_registered',
            ])->get(),
            'gowns' => GownStock::select(['id', 'size', 'total', 'available'])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'convocation_session_id' => 'required|exists:convocation_sessions,id',
            'guest_count' => 'required|integer|min:0|max:' . config('convocation.max_guest_per_student', 2),
            'attendance_confirmed' => 'required|boolean',
            'gown_size' => 'required|in:XS,S,M,L,XL',
            'collection_date' => 'nullable|date',
        ]);

        if (!$data['attendance_confirmed']) {
            return back()->withErrors(['attendance_confirmed' => 'You must confirm attendance to register.']);
        }

        DB::transaction(function () use ($user, $data) {
            $session = ConvocationSession::where('id', $data['convocation_session_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Check student quota
            if ($session->registered >= $session->quota) {
                throw ValidationException::withMessages([
                    'convocation_session_id' => 'This session is full. Please choose another.',
                ]);
            }

            // Check guest quota
            if ($session->guest_registered + $data['guest_count'] > $session->guest_quota) {
                throw ValidationException::withMessages([
                    'guest_count' => 'Guest quota for this session is exceeded.',
                ]);
            }

            $existing = SessionRegistration::where('user_id', $user->id)->first();
            $oldSessionId = $existing?->convocation_session_id;
            $oldGownSize = $existing?->gown_size;
            $oldGuestCount = $existing?->guest_count ?? 0;

            // Create or update registration
            SessionRegistration::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'convocation_session_id' => $session->id,
                    'guest_count' => $data['guest_count'],
                    'attendance_confirmed' => true,
                    'gown_size' => $data['gown_size'] ?? null,
                    'collection_date' => $data['collection_date'] ?? null,
                ]
            );

            // Adjust session counters
            if (!$existing || $oldSessionId != $session->id) {
                // Decrement old session counters
                if ($existing && $oldSessionId) {
                    ConvocationSession::where('id', $oldSessionId)
                        ->lockForUpdate()
                        ->decrement('registered');
                    ConvocationSession::where('id', $oldSessionId)
                        ->lockForUpdate()
                        ->decrement('guest_registered', $oldGuestCount);
                }
                // Increment new session counters
                $session->increment('registered');
                $session->increment('guest_registered', $data['guest_count']);
            } elseif ($oldGuestCount !== $data['guest_count']) {
                // Adjust guest count if changed
                $session->increment('guest_registered', $data['guest_count'] - $oldGuestCount);
            }

            // Handle gown stock
            if (!empty($data['gown_size'])) {
                $newStock = GownStock::where('size', $data['gown_size'])->lockForUpdate()->first();
                if (!$newStock || $newStock->available <= 0) {
                    throw ValidationException::withMessages([
                        'gown_size' => 'Selected gown size is no longer available.',
                    ]);
                }

                // Restore old stock (if changing size)
                if ($oldGownSize && $oldGownSize !== $data['gown_size']) {
                    $oldStock = GownStock::where('size', $oldGownSize)->lockForUpdate()->first();
                    if ($oldStock) {
                        $oldStock->increment('available');
                    }
                }

                // Reserve new stock
                if (!$existing || $oldGownSize !== $data['gown_size']) {
                    $newStock->decrement('available');
                }

                GownCollection::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'size' => $data['gown_size'],
                        'status' => 'reserved',
                        'collection_date' => $data['collection_date'],
                    ]
                );
            }

            // Attendance record handling
            AttendanceRecord::where('user_id', $user->id)->delete();
            AttendanceRecord::create([
                'user_id' => $user->id,
                'session_id' => $session->id,
                'attendance_token' => Str::uuid(),
                'status' => 'registered',
            ]);
        });

        return redirect()
            ->route('student.registration.index')
            ->with('message', 'Registration saved successfully!');
    }

    public function destroy(Request $request)
    {
        $userId = $request->user()->id;

        DB::transaction(function () use ($userId) {
            $registration = SessionRegistration::where('user_id', $userId)->first();

            if ($registration) {
                // Decrement session counters
                $session = ConvocationSession::where('id', $registration->convocation_session_id)
                    ->lockForUpdate()
                    ->first();
                if ($session) {
                    $session->decrement('registered');
                    $session->decrement('guest_registered', $registration->guest_count);
                }

                // Restore gown stock if reserved
                if ($registration->gown_size) {
                    $stock = GownStock::where('size', $registration->gown_size)->lockForUpdate()->first();
                    if ($stock) {
                        $stock->increment('available');
                    }
                }

                // Delete records
                $registration->delete();
                GownCollection::where('user_id', $userId)->delete();
                AttendanceRecord::where('user_id', $userId)->delete();
            }
        });

        return redirect()
            ->route('student.registration.index')
            ->with('message', 'Your registration has been cancelled.');
    }
}
