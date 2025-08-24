<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\GownCollection;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        // 1. Attendance Report
        $attendance = [
            'total_registered' => AttendanceRecord::count(),
            'total_checked_in' => AttendanceRecord::where('status', 'checked_in')->count(),
        ];

        // 2. Gown Report
        $gowns = GownCollection::selectRaw('size, COUNT(*) as total, SUM(status = "returned") as returned')
            ->groupBy('size')
            ->get();

        // 3. Guest Report
        $guests = SessionRegistration::selectRaw('convocation_session_id, SUM(guest_count) as total_guests')
            ->groupBy('convocation_session_id')
            ->with('session:id,name')
            ->get()
            ->map(function ($g) {
                return [
                    'session_name' => $g->session?->name ?? 'Unknown Session',
                    'total_guests' => $g->total_guests,
                ];
            });

        // 4. Session Report
        $sessions = ConvocationSession::withCount('registrations')
            ->get()
            ->map(function ($s) {
                return [
                    'name' => $s->name,
                    'quota' => $s->quota,
                    'registered' => $s->registrations_count,
                ];
            });

        return Inertia::render('admin/Reports/Index', [
            'attendance' => $attendance,
            'gowns' => $gowns,
            'guests' => $guests,
            'sessions' => $sessions,
        ]);
    }
}
