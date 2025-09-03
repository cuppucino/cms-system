<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }
    /**
     * Attendance list with filters & search
     */
    public function index(Request $request)
    {
        $query = AttendanceRecord::with(['user', 'session']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by user name or email
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
        $record = AttendanceRecord::where('attendance_token', $token)->with(['user', 'session'])->first();

        if (!$record) {
            return response()->view('checkin_result', [
                'message' => '❌ Invalid or revoked invitation.',
                'status'  => 'error',
            ]);
        }

        // Must be registered first
        if ($record->status !== 'registered') {
            return response()->view('checkin_result', [
                'message' => '⚠️ Attendance not confirmed.',
                'status'  => 'warning',
            ]);
        }


        // Session date guard (same-day check-in)
        if ($record->session && $record->session->date) {
            $today = Carbon::today(); // Initialize today before usage
            $sessionDay = Carbon::parse($record->session->date)->startOfDay(); // Make sure session date is at the start of the day

            // Compare only the date (without time)
            if (!$today->isSameDay($sessionDay)) {
                return response()->view('checkin_result', [
                    'message' => '⏳ Check-in only available on your session day.',
                    'status'  => 'warning',
                ]);
            }
        }

        // Already checked in?
        if ($record->status === 'checked_in') {
            return response()->view('checkin_result', [
                'message' => '⚠️ Already checked in!',
                'status'  => 'warning',
            ]);
        }

        $record->update([
            'status'        => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return response()->view('checkin_result', [
            'message' => '✅ You have successfully checked in!',
            'status'  => 'success',
        ]);
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

    /**
     * Export attendance records
     */
    public function export()
    {
        return Excel::download(new AttendanceExport, 'attendance_report.xlsx');
    }
}
