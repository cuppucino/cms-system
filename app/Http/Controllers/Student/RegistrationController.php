<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use App\Models\GownStock;
use App\Models\GownCollection;
use App\Models\GownSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use App\Models\AttendanceRecord;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load(['course.convocationSession']);

        // Existing registration (if any)
        $registration = SessionRegistration::with('session')
            ->where('user_id', $user->id)
            ->first();

        // Assigned convocation session via the user's course
        $assigned = $user->course?->convocationSession;

        // Existing gown collection (so we can preselect their chosen gown session)
        $existingCollection = GownCollection::with('session')
            ->where('user_id', $user->id)
            ->first();

        // All gown sessions (for student to choose)
        $gownSessions = GownSession::withCount('collections')
            ->orderBy('date')->orderBy('start_time')
            ->get()
            ->map(fn ($s) => [
                'id'         => $s->id,
                'date'       => optional($s->date)?->toDateString(),
                'start_time' => $s->start_time ? date('H:i', strtotime($s->start_time)) : null,
                'end_time'   => $s->end_time   ? date('H:i', strtotime($s->end_time))   : null,
                'location'   => $s->location,
                'quota'      => (int) $s->quota,
                'booked'     => (int) $s->collections_count,
                'note'       => $s->note,
            ]);

        return Inertia::render('student/Registration', [
            // Registration payload (prefill the form)
            'registration' => $registration ? [
                'attendance_confirmed'   => $registration->attendance_confirmed,
                'convocation_session_id' => $registration->convocation_session_id,
                'guest_count'            => $registration->guest_count,
                'gown_size'              => $registration->gown_size,
                // display only; we no longer accept arbitrary dates from the form
                'collection_date'        => $existingCollection?->collection_date?->toDateTimeString(),
                'gown_session_id'        => $existingCollection?->gown_session_id,
                'session'                => $assigned ? [
                    'id'         => $assigned->id,
                    'name'       => $assigned->name,
                    'date'       => optional($assigned->date)?->toDateString(),
                    'location'   => $assigned->location,
                    'quota'      => (int) $assigned->quota,
                    'registered' => (int) $assigned->registrations()->count(),
                ] : null,
            ] : null,

            // Assigned convocation session (read-only card in the UI)
            'sessions' => $assigned ? [[
                'id'         => $assigned->id,
                'name'       => $assigned->name,
                'date'       => optional($assigned->date)?->toDateString(),
                'location'   => $assigned->location,
                'quota'      => (int) $assigned->quota,
                'registered' => (int) $assigned->registrations()->count(),
            ]] : [],

            // Gown sizes/stock
            'gowns' => GownStock::select(['id', 'size', 'total', 'available'])->get(),

            // Gown collection sessions to pick from
            'gown_sessions' => $gownSessions,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user()->load(['course.convocationSession']);
        $assignedConvocation = $user->course?->convocationSession;

        if (!$assignedConvocation) {
            return back()->withErrors([
                'session' => 'Your course is not assigned to any convocation session yet.',
            ]);
        }

        $data = $request->validate([
            'attendance_confirmed' => 'required|boolean',
            'guest_count'          => 'required|integer|min:0|max:' . config('convocation.max_guest_per_student', 2),
            'gown_size'            => 'required|in:XS,S,M,L,XL',
            'gown_session_id'      => 'required|exists:gown_sessions,id',
            // kept for back-compat; no longer honored as free input
            'collection_date'      => 'nullable|date',
        ]);

        if (!$data['attendance_confirmed']) {
            return back()->withErrors([
                'attendance_confirmed' => 'You must confirm attendance to register.',
            ]);
        }

        DB::transaction(function () use ($user, $assignedConvocation, $data) {
            // Lock assigned convocation session
            $lockedConvocation = ConvocationSession::where('id', $assignedConvocation->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Current registration & gown collection
            $existingReg = SessionRegistration::where('user_id', $user->id)->first();
            $existingCollection = GownCollection::where('user_id', $user->id)->lockForUpdate()->first();

            $oldGuestCount     = $existingReg?->guest_count ?? 0;
            $oldGownSize       = $existingReg?->gown_size;
            $oldGownSessionId  = $existingCollection?->gown_session_id;

            // --- Convocation capacity checks ---
            if ($lockedConvocation->registered >= $lockedConvocation->quota && !$existingReg) {
                // If the student is already registered to this session, allow updating guests/gown
                throw ValidationException::withMessages([
                    'session' => 'This session is full. Please contact the convocation desk.',
                ]);
            }

            if ($lockedConvocation->guest_registered + ($data['guest_count'] - $oldGuestCount) > $lockedConvocation->guest_quota) {
                throw ValidationException::withMessages([
                    'guest_count' => 'Guest quota for this session would be exceeded.',
                ]);
            }

            // --- Gown session capacity check ---
            $gownSession = GownSession::where('id', $data['gown_session_id'])->lockForUpdate()->firstOrFail();

            $booked = $gownSession->collections()->count();

            // If student is switching to a DIFFERENT gown session, enforce capacity strictly;
            // If staying on the same session, allow even if fully booked now.
            $switchingSession = $oldGownSessionId && ((int)$oldGownSessionId !== (int)$gownSession->id);
            if ($booked >= $gownSession->quota && ($switchingSession || !$existingCollection)) {
                throw ValidationException::withMessages([
                    'gown_session_id' => 'This gown session is full. Please pick another time slot.',
                ]);
            }

            // --- Create/Update registration ---
            SessionRegistration::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'convocation_session_id' => $lockedConvocation->id,
                    'guest_count'            => $data['guest_count'],
                    'attendance_confirmed'   => true,
                    'gown_size'              => $data['gown_size'] ?? null,
                    'collection_date'        => null, // derived below
                ]
            );

            // Adjust convocation counters
            if (!$existingReg) {
                $lockedConvocation->increment('registered');
                $lockedConvocation->increment('guest_registered', $data['guest_count']);
            } elseif ($oldGuestCount !== $data['guest_count']) {
                $lockedConvocation->increment('guest_registered', $data['guest_count'] - $oldGuestCount);
            }

            // --- Gown stock handling ---
            if (!empty($data['gown_size'])) {
                $newStock = GownStock::where('size', $data['gown_size'])->lockForUpdate()->first();
                if (!$newStock || $newStock->available <= 0) {
                    throw ValidationException::withMessages([
                        'gown_size' => 'Selected gown size is no longer available.',
                    ]);
                }

                // If size changed, release old size and reserve new one
                if ($oldGownSize && $oldGownSize !== $data['gown_size']) {
                    $oldStock = GownStock::where('size', $oldGownSize)->lockForUpdate()->first();
                    if ($oldStock) {
                        $oldStock->increment('available');
                    }
                }

                if (!$existingReg || $oldGownSize !== $data['gown_size']) {
                    $newStock->decrement('available');
                }
            }

            // --- Update/Create GownCollection & link to gown session ---
            // Derive collection datetime from session date + start_time (or midnight if no time)
            $derivedCollectionDate = null;
            if ($gownSession->date) {
                // Start from a Carbon date, then set time safely (prevents "Double time specification")
                $base = $gownSession->date instanceof Carbon
                    ? $gownSession->date->copy()
                    : Carbon::parse($gownSession->date);

                $time = $gownSession->start_time ?: '00:00:00';
                $derivedCollectionDate = $base->setTimeFromTimeString($time);
            }

            GownCollection::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'size'             => $data['gown_size'],
                    'status'           => $existingCollection?->status ?? 'reserved',
                    'gown_session_id'  => $gownSession->id,
                    'collection_date'  => $derivedCollectionDate,
                    // keep existing return_date as-is
                ]
            );

            // --- Attendance record ---
            AttendanceRecord::where('user_id', $user->id)->delete();
            AttendanceRecord::create([
                'user_id'          => $user->id,
                'session_id'       => $lockedConvocation->id,
                'attendance_token' => Str::uuid(),
                'status'           => 'registered',
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
                // Decrement convocation counters
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

                // Delete dependent records
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
