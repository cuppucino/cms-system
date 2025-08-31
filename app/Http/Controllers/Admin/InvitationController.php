<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use App\Models\ConvocationSession;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with(['user','session','issuer'])
            ->latest()
            ->get()
            ->map(function ($inv) {
                $url = route('attendance.checkIn', ['token' => $inv->code]);

                $qrBase64 = base64_encode(
                    QrCode::format('png')->size(100)->margin(1)->generate($url)
                );

                return [
                    'id' => $inv->id,
                    'user_name' => $inv->user?->name,
                    'user_email' => $inv->user?->email,
                    'session_name' => $inv->session?->name,
                    'code' => $inv->code,
                    'issued_by' => $inv->issuer?->name,
                    'issued_at' => optional($inv->issued_at)->toDateTimeString(),
                    'revoked_at' => optional($inv->revoked_at)->toDateTimeString(),
                    'is_active' => $inv->revoked_at === null,
                    'qr_base64' => $qrBase64,
                ];
            });

        return Inertia::render('admin/Invitations/Index', [
            'invitations' => $invitations,
        ]);
    }

    public function create(Request $request)
    {
        $students = User::where('is_admin', false)->get(['id','name','email']);
        $sessions = ConvocationSession::all(['id','name']);

        return Inertia::render('admin/Invitations/Create', [
            'students' => $students,
            'sessions' => $sessions,
            'prefill_user_id' => $request->query('user_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'convocation_session_id' => 'nullable|exists:convocation_sessions,id',
        ]);

        foreach ($data['user_ids'] as $uid) {
            $code = (string) Str::uuid();
            $url = route('attendance.checkIn', ['token' => $code]);

            $payload = [
                'url'       => $url,
                'code'      => $code,
                'user_id'   => $uid,
                'session_id'=> $data['convocation_session_id'] ?? null,
                'issued_at' => now()->toIso8601String(),
            ];

            Invitation::create([
                'user_id' => $uid,
                'convocation_session_id' => $data['convocation_session_id'] ?? null,
                'code' => $code,
                'payload' => $payload,
                'issued_at' => now(),
                'issued_by' => auth()->id(),
            ]);

            // ✅ also create AttendanceRecord
            AttendanceRecord::create([
                'user_id' => $uid,
                'session_id' => $data['convocation_session_id'] ?? null,
                'attendance_token' => $code,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('admin.invitations.index')
            ->with('message', 'Invitations generated successfully.');
    }

    public function revoke(Invitation $invitation)
    {
        if ($invitation->revoked_at) {
            return back()->with('message', 'Invitation already revoked.');
        }

        $invitation->update([
            'revoked_at' => now(),
        ]);

        return back()->with('message', 'Invitation revoked.');
    }
}
