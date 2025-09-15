<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ConvocationSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SessionController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load(['course.convocationSession', 'course.convocationSession.registrations']);

        $session = $user->course?->convocationSession;

        $assignedData = $session ? [
            'id'         => $session->id,
            'name'       => $session->name,
            'date'       => optional($session->date)->toDateString(),
            'time'       => null, // you don't store a separate time; omit or add if you later add a column
            'location'   => $session->location,
            'quota'      => (int) $session->quota,
            'registered' => (int) $session->registrations()->count(), // reliable count
            'notes'      => null,
            'arrival_at' => null,
            'dress_code' => null,
        ] : null;

        return Inertia::render('student/Session', [
            'assigned'  => $assignedData,
            'available' => [], // view-only design: no alternatives
        ]);
    }

    public function select(Request $request)
    {
        abort(403, 'Session selection is disabled. Your session is set by your course.');
    }
}
