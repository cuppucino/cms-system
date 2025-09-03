<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConvocationSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConvocationSessionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }

    public function index()
    {
        $sessions = ConvocationSession::withCount('registrations')->get();

        return Inertia::render('admin/Sessions/Index', [
            'sessions' => $sessions,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/Sessions/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'quota' => 'required|integer|min:1',
            'guest_quota' => 'required|integer|min:0',
        ]);

        ConvocationSession::create([
            'name' => $request->name,
            'date' => $request->date,
            'location' => $request->location,
            'quota' => $request->quota,
            'registered' => 0,
            'guest_quota' => $request->guest_quota,
            'guest_registered' => 0,
        ]);


        return redirect()->route('admin.sessions.index')->with('message', 'Session created successfully!');
    }

    public function edit(ConvocationSession $session)
    {
        return Inertia::render('admin/Sessions/Edit', [
            'session' => $session,
        ]);
    }

    public function update(Request $request, ConvocationSession $session)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'quota' => 'required|integer|min:1',
        ]);

        $session->update($request->all());

        return redirect()->route('admin.sessions.index')->with('message', 'Session updated successfully!');
    }

    public function destroy(ConvocationSession $session)
    {
        $session->delete();

        return redirect()->route('admin.sessions.index')->with('message', 'Session deleted successfully!');
    }
}
