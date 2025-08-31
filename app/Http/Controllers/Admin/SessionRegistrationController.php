<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use App\Models\AttendanceRecord;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GownStock;
use App\Models\GownCollection;



class SessionRegistrationController extends Controller
{
    public function index()
    {
        $registrations = SessionRegistration::with(['user', 'session'])->get();

        return Inertia::render('admin/Sessions/Registrations', [
            'registrations' => $registrations->map(fn($r) => [
                'id' => $r->id,
                'student_name' => $r->user->name,
                'session_name' => $r->session->name,
                'guest_count' => $r->guest_count,
            ])
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // ✅ Check if user already registered
        $existingRegistration = SessionRegistration::where('user_id', $user->id)->first();
        if ($existingRegistration) {
            return back()->withErrors(['registration' => 'You have already registered for a session.']);
        }

        $data = $request->validate([
            'convocation_session_id' => 'required|exists:convocation_sessions,id',
            'guest_count' => 'required|integer|min:0',
            'attendance_confirmed' => 'required|boolean',
            'gown_size' => 'nullable|in:XS,S,M,L,XL',
            'collection_date' => 'nullable|date',
        ]);

        $session = ConvocationSession::findOrFail($data['convocation_session_id']);

        // ✅ Check quota
        $registeredCount = SessionRegistration::where('convocation_session_id', $session->id)->count();
        if ($registeredCount >= $session->quota) {
            return back()->withErrors(['convocation_session_id' => 'This session is full. Please choose another.']);
        }

        DB::transaction(function () use ($user, $data, $session) {
            // 1. Save registration
            SessionRegistration::create(array_merge($data, ['user_id' => $user->id]));

            // 2. Save attendance record
            AttendanceRecord::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'session_id' => $session->id,
                ],
                [
                    'status' => $data['attendance_confirmed'] ? 'registered' : 'pending',
                ]
            );

            // 3. Reserve gown if selected
            if (!empty($data['gown_size'])) {
                $stock = GownStock::where('size', $data['gown_size'])->first();
                if ($stock && $stock->available > 0) {
                    GownCollection::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'size' => $data['gown_size'],
                            'status' => 'reserved',
                            'collection_date' => $data['collection_date'],
                        ]
                    );

                    $stock->decrement('available');
                }
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
                // Restore gown stock if reserved
                if (!empty($registration->gown_size)) {
                    $stock = GownStock::where('size', $registration->gown_size)->first();
                    if ($stock) {
                        $stock->increment('available');
                    }

                    // Remove gown collection
                    GownCollection::where('user_id', $user->id)->delete();
                }

                // Delete attendance record
                AttendanceRecord::where('user_id', $user->id)->delete();

                // Delete registration
                $registration->delete();
            }
        });

        return redirect()
            ->route('student.registration.index')
            ->with('message', 'Your registration has been cancelled and gown stock restored.');
    }
}
