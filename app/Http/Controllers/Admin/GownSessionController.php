<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GownSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GownSessionController extends Controller
{
    public function index()
    {
        $sessions = GownSession::withCount('collections')
            ->orderBy('date')->orderBy('start_time')
            ->get();

        return Inertia::render('admin/GownSessions/Index', [
            'sessions' => $sessions->map(fn ($s) => [
                'id'                => $s->id,
                'date'              => optional($s->date)->toDateString(),
                'start_time'        => $s->start_time ? date('H:i', strtotime($s->start_time)) : null,
                'end_time'          => $s->end_time ? date('H:i', strtotime($s->end_time)) : null,
                'location'          => $s->location,
                'quota'             => (int) $s->quota,
                'collections_count' => (int) $s->collections_count,
                'note'              => $s->note,
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/GownSessions/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location'   => 'required|string|max:255',
            'quota'      => 'required|integer|min:1',
            'note'       => 'nullable|string',
        ]);

        GownSession::create($data);

        return redirect()->route('admin.gown-sessions.index')->with('message', 'Gown session created!');
    }

    public function edit(GownSession $gown_session)
    {
        return Inertia::render('admin/GownSessions/Edit', [
            'session' => [
                'id'         => $gown_session->id,
                'date'       => optional($gown_session->date)->toDateString(),
                'start_time' => $gown_session->start_time ? date('H:i', strtotime($gown_session->start_time)) : null,
                'end_time'   => $gown_session->end_time ? date('H:i', strtotime($gown_session->end_time)) : null,
                'location'   => $gown_session->location,
                'quota'      => (int) $gown_session->quota,
                'note'       => $gown_session->note,
            ],
        ]);
    }

    public function update(Request $request, GownSession $gown_session)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after_or_equal:start_time',
            'location'   => 'required|string|max:255',
            'quota'      => 'required|integer|min:1',
            'note'       => 'nullable|string',
        ]);

        $gown_session->update($data);

        return redirect()->route('admin.gown-sessions.index')->with('message', 'Gown session updated!');
    }

    public function destroy(GownSession $gown_session)
    {
        $gown_session->delete();

        return redirect()->route('admin.gown-sessions.index')->with('message', 'Gown session deleted.');
    }
}
