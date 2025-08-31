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

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $registration = SessionRegistration::where('user_id', $user->id)->first();

        return Inertia::render('student/Registration', [
            'registration' => $registration,
            'sessions' => ConvocationSession::all(),
            'gowns' => GownStock::all(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'convocation_session_id' => 'required|exists:convocation_sessions,id',
            'guest_count' => 'required|integer|min:0',
            'attendance_confirmed' => 'required|boolean',
            'gown_size' => 'nullable|in:XS,S,M,L,XL',
            'collection_date' => 'nullable|date',
        ]);

        if (!$data['attendance_confirmed']) {
            return back()->withErrors(['attendance_confirmed' => 'You must confirm attendance to register.']);
        }

        DB::transaction(function () use ($user, $data) {
            $session = ConvocationSession::where('id', $data['convocation_session_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Check quota safely under lock
            if ($session->registered >= $session->quota) {
                throw ValidationException::withMessages([
                    'convocation_session_id' => 'This session is full. Please choose another.',
                ]);
            }

            $existing = SessionRegistration::where('user_id', $user->id)->first();
            $oldGownSize = $existing->gown_size ?? null;

            // If changing session, adjust registered count
            if ($existing && $existing->convocation_session_id != $session->id) {
                // Decrement old session count
                ConvocationSession::where('id', $existing->convocation_session_id)->decrement('registered');
            }

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

            // Increment new session count (only if it's a new registration or changed session)
            if (!$existing || $existing->convocation_session_id != $session->id) {
                $session->increment('registered');
            }

            // Handle gown stock
            if (!empty($data['gown_size'])) {
                $newStock = GownStock::where('size', $data['gown_size'])->lockForUpdate()->first();

                if (!$newStock || $newStock->available <= 0) {
                    throw ValidationException::withMessages([
                        'gown_size' => 'Selected gown size is no longer available.',
                    ]);
                }

                // If changing gown size, restore old stock
                if ($oldGownSize && $oldGownSize !== $data['gown_size']) {
                    GownStock::where('size', $oldGownSize)->increment('available');
                }

                // Reserve new gown
                $newStock->decrement('available');

                GownCollection::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'size' => $data['gown_size'],
                        'status' => 'reserved',
                        'collection_date' => $data['collection_date'],
                    ]
                );
            }
        });

        return redirect()
            ->route('student.registration.index')
            ->with('message', 'Registration saved successfully!');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            $registration = SessionRegistration::where('user_id', $user->id)->first();

            if ($registration) {
                // Decrement session count
                ConvocationSession::where('id', $registration->convocation_session_id)->decrement('registered');

                // Restore gown stock if reserved
                if ($registration->gown_size) {
                    GownStock::where('size', $registration->gown_size)->increment('available');
                }

                // Delete records
                $registration->delete();
                GownCollection::where('user_id', $user->id)->delete();
            }
        });

        return redirect()
            ->route('student.registration.index')
            ->with('message', 'Your registration has been cancelled.');
    }
}
