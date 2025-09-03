<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SessionRegistration;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Registration info
        $registration = SessionRegistration::with('session')
            ->where('user_id', $user->id)
            ->first();

        // Attendance info
        $attendance = AttendanceRecord::where('user_id', $user->id)->first();

        // Gown info (from registration for size, or from gown collection if you track status there)
        $gown = $registration?->gown_size ? [
            'status' => $registration->attendance_confirmed ? 'reserved' : 'pending',
            'size'   => $registration->gown_size,
        ] : null;

        // Session info
        $session = $registration?->session ? [
            'id'       => $registration->session->id,
            'name'     => $registration->session->name,
            'date'     => $registration->session->date,
            'location' => $registration->session->location,
        ] : null;

        // Build summary for UI
        $summary = [
            'status'     => $registration
                ? ($registration->attendance_confirmed ? 'confirmed' : 'registered')
                : 'pending',
            'session'    => $session,
            'gown'       => $gown,
            'invitation' => $registration && $registration->attendance_confirmed, // unlocked if confirmed
            'attended'   => $attendance && $attendance->status === 'checked_in',
        ];



        return Inertia::render('student/Dashboard', [
            'studentSummary' => $summary,
            // 'auth' => [
            //     'user' => $user,
            // ],
        ]);
    }
}
