<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Attendance list with filters & search
     */
    public function index(Request $request)
    {
        $query = AttendanceRecord::with(['user', 'session']);

        //  Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        //  Search by user name or email
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $records = $query->get()->map(fn($r) => [
            'id'            => $r->id,
            'user_name'     => $r->user->name,
            'user_email'    => $r->user->email,
            'session_name'  => $r->session->name ?? 'N/A',
            'status'        => $r->status,
            'checked_in_at' => $r->checked_in_at,
        ]);

        return Inertia::render('admin/Attendance/Index', [
            'records' => $records,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Auto check-in via QR token
     */
    public function checkIn($token)
    {
        $record = AttendanceRecord::where('attendance_token', $token)->firstOrFail();

        $record->status = 'checked_in';
        $record->checked_in_at = now();
        $record->save();

        return response()->json(['message' => 'Attendance confirmed!']);
    }

    /**
     * Manual check-in by admin
     */
    public function manualCheckIn($id)
    {
        $record = AttendanceRecord::findOrFail($id);

        $record->status = 'checked_in';
        $record->checked_in_at = now();
        $record->save();

        return back()->with('message', 'Student checked in manually.');
    }

    public function export()
    {
        return Excel::download(new AttendanceExport, 'attendance_report.xlsx');
    }
}
