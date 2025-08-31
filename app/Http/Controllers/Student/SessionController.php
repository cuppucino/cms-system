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
        $user = $request->user();

        // If you store session at users table: convocation_session_id
        $assigned = null;
        if ($user->convocation_session_id) {
            $assigned = ConvocationSession::query()
                ->withCount('registrations') // or your relationship name
                ->find($user->convocation_session_id);
        }

        // Show alternatives if not assigned (or you always can show both)
        $available = [];
        if (!$assigned) {
            $available = ConvocationSession::query()
                ->withCount('registrations')
                ->orderBy('date')
                ->get()
                ->map(function ($s) {
                    return [
                        'id'         => $s->id,
                        'name'       => $s->name,
                        'date'       => optional($s->date)->toDateString(),
                        'time'       => optional($s->date)->format('H:i'),
                        'location'   => $s->location,
                        'quota'      => (int) $s->quota,
                        'registered' => (int) $s->registrations_count,
                    ];
                });
        }

        // Normalize assigned shape for the UI
        $assignedData = $assigned ? [
            'id'         => $assigned->id,
            'name'       => $assigned->name,
            'date'       => optional($assigned->date)->toDateString(),
            'time'       => optional($assigned->date)->format('H:i'),
            'location'   => $assigned->location,
            'quota'      => (int) $assigned->quota,
            'registered' => (int) $assigned->registrations_count,
            // Optional extra meta
            'notes'      => $assigned->notes,
            'arrival_at' => $assigned->arrival_at, // if you have these
            'dress_code' => $assigned->dress_code,
        ] : null;

        return Inertia::render('student/Session', [
            'assigned'  => $assignedData,
            'available' => $available,
        ]);
    }

    public function select(Request $request)
    {
        $data = $request->validate([
            'session_id' => ['required', 'exists:convocation_sessions,id'],
        ]);

        $user = $request->user();
        $session = \App\Models\ConvocationSession::withCount('registrations')->findOrFail($data['session_id']);

        // capacity guard
        if ($session->registrations_count >= $session->quota) {
            return back()->with(['message' => 'This session is already full.']);
        }

        // Set user's session id (or create registration row)
        $user->convocation_session_id = $session->id;
        $user->save();

        return redirect()->route('student.session.show')->with(['message' => 'Session selected successfully.']);
    }
}
