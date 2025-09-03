<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\GownCollection;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index()
    {
        $attendance = [
            'total_registered' => SessionRegistration::count(),
            'total_checked_in' => AttendanceRecord::where('status', 'checked_in')->count(),
        ];

        $gowns = GownCollection::select([
            'size',
            DB::raw("SUM(CASE WHEN status IN ('collected','late','returned') THEN 1 ELSE 0 END) AS issued"),
            DB::raw("SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) AS returned"),
            DB::raw("SUM(CASE WHEN status IN ('collected','late') THEN 1 ELSE 0 END) AS outstanding"),
        ])
            ->groupBy('size')
            ->orderBy('size')
            ->get()
            ->map(function ($r) {
                return [
                    'size' => $r->size,
                    'issued' => (int) $r->issued,
                    'returned' => (int) $r->returned,
                    'outstanding' => (int) $r->outstanding,
                ];
            });

        $guests = ConvocationSession::select([
            'id',
            'name',
            'guest_quota',
            'guest_registered',
        ])
            ->get()
            ->map(function ($s) {
                return [
                    'session' => ['name' => $s->name, 'guest_quota' => $s->guest_quota],
                    'total_guests' => (int) $s->guest_registered,
                ];
            });

        $guestRegistrations = SessionRegistration::with(['user', 'session'])
            ->where('guest_count', '>', 0)
            ->get()
            ->map(function ($r) {
                return [
                    'student_name' => $r->user->name,
                    'session_name' => $r->session->name,
                    'guest_count' => $r->guest_count,
                ];
            });

        $sessions = ConvocationSession::withCount('registrations')
            ->get()
            ->map(function ($s) {
                return [
                    'name' => $s->name,
                    'quota' => (int) $s->quota,
                    'registered' => (int) $s->registrations_count,
                ];
            });

        return Inertia::render('admin/Reports/Index', compact('attendance', 'gowns', 'guests', 'guestRegistrations', 'sessions'));
    }

    public function export(string $kind): StreamedResponse
    {
        $filename = match ($kind) {
            'attendance' => 'attendance.csv',
            'registrations' => 'registrations.csv',
            'gowns' => 'gowns.csv',
            'guests' => 'guests_by_session.csv',
            'sessions' => 'sessions.csv',
            default => 'export.csv',
        };

        $callback = function () use ($kind) {
            $out = fopen('php://output', 'w');

            switch ($kind) {
                case 'attendance':
                    fputcsv($out, ['User Name', 'Email', 'Session', 'Status', 'Checked In At']);
                    AttendanceRecord::with(['user:id,name,email', 'session:id,name'])
                        ->orderBy('id')
                        ->chunk(1000, function ($rows) use ($out) {
                            foreach ($rows as $r) {
                                fputcsv($out, [
                                    $r->user?->name,
                                    $r->user?->email,
                                    $r->session?->name,
                                    $r->status,
                                    optional($r->checked_in_at)->toDateTimeString(),
                                ]);
                            }
                        });
                    break;

                case 'registrations':
                    fputcsv($out, ['User Name', 'Email', 'Session', 'Guests', 'Gown Size', 'Created At']);
                    SessionRegistration::with(['user:id,name,email', 'session:id,name'])
                        ->orderBy('id')
                        ->chunk(1000, function ($rows) use ($out) {
                            foreach ($rows as $r) {
                                fputcsv($out, [
                                    $r->user?->name,
                                    $r->user?->email,
                                    $r->session?->name,
                                    $r->guest_count,
                                    $r->gown_size,
                                    optional($r->created_at)->toDateTimeString(),
                                ]);
                            }
                        });
                    break;

                case 'gowns':
                    fputcsv($out, ['Size', 'Issued', 'Returned', 'Outstanding']);
                    GownCollection::select([
                        'size',
                        DB::raw("SUM(CASE WHEN status IN ('collected','late','returned') THEN 1 ELSE 0 END) AS issued"),
                        DB::raw("SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) AS returned"),
                        DB::raw("SUM(CASE WHEN status IN ('collected','late') THEN 1 ELSE 0 END) AS outstanding"),
                    ])
                        ->groupBy('size')->orderBy('size')->get()
                        ->each(function ($g) use ($out) {
                            fputcsv($out, [$g->size, (int)$g->issued, (int)$g->returned, (int)$g->outstanding]);
                        });
                    break;

                case 'guests':
                    fputcsv($out, ['Session', 'Student Name', 'Guests']);
                    SessionRegistration::with(['user:id,name', 'session:id,name'])
                        ->where('guest_count', '>', 0)
                        ->orderBy('convocation_session_id')
                        ->chunk(1000, function ($rows) use ($out) {
                            foreach ($rows as $r) {
                                fputcsv($out, [
                                    $r->session?->name ?? 'Unknown Session',
                                    $r->user?->name,
                                    $r->guest_count,
                                ]);
                            }
                        });
                    break;

                case 'sessions':
                    fputcsv($out, ['Session', 'Quota', 'Registered']);
                    ConvocationSession::withCount('registrations')
                        ->orderBy('date')->orderBy('name')
                        ->get()
                        ->each(function ($s) use ($out) {
                            fputcsv($out, [$s->name, (int)$s->quota, (int)$s->registrations_count]);
                        });
                    break;
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
