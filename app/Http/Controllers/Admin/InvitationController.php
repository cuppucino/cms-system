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
use Illuminate\Support\Facades\DB;

use App\Models\SessionRegistration;


class InvitationController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }

    public function index()
    {
        $invitations = Invitation::with(['user', 'session', 'issuer'])
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
        $students = User::where('is_admin', false)->get(['id', 'name', 'email']);
        $sessions = ConvocationSession::all(['id', 'name', 'quota', 'registered']);

        return Inertia::render('admin/Invitations/Create', [
            'students' => $students,
            'sessions' => $sessions,
            'prefill_user_id' => $request->query('user_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_ids'               => 'required|array|min:1',
            'user_ids.*'             => 'exists:users,id',
            'convocation_session_id' => 'nullable|exists:convocation_sessions,id',
            'force'                  => 'sometimes|boolean', // allow override from UI
        ]);

        foreach ($data['user_ids'] as $uid) {
            DB::transaction(function () use ($uid, $data, $request) {
                // 1) Resolve target session (explicit > registered)
                $sessionId = $data['convocation_session_id']
                    ?: SessionRegistration::where('user_id', $uid)->value('convocation_session_id');

                if (!$sessionId) {
                    abort(422, 'Please pick a session, or the student must be registered first.');
                }

                // 2) Capacity guard (block if full unless override)
                $sess = ConvocationSession::lockForUpdate()->findOrFail($sessionId);
                if ($sess->registered >= $sess->quota && !$request->boolean('force')) {
                    abort(422, 'Session is full. Enable override to proceed.');
                }

                // 3) Registration-session alignment (block unless override)
                $regSession = SessionRegistration::where('user_id', $uid)->value('convocation_session_id');
                if ($regSession && (int)$regSession !== (int)$sessionId && !$request->boolean('force')) {
                    abort(422, 'Student is registered to a different session. Enable override to proceed.');
                }

                // 4) Ensure a single AttendanceRecord per user (rotate to this session with new token)
                AttendanceRecord::where('user_id', $uid)->delete();
                $record = AttendanceRecord::create([
                    'user_id'          => $uid,
                    'session_id'       => $sessionId,
                    'attendance_token' => (string) Str::uuid(),
                    'status'           => 'pending', // will move to 'registered' after RSVP
                ]);

                // 5) Revoke any existing active invitations for this user (no duplicates)
                Invitation::where('user_id', $uid)
                    ->whereNull('revoked_at')
                    ->update(['revoked_at' => now()]);

                // 6) Create new invitation using the attendance token as the QR code
                $code = $record->attendance_token;
                $url  = route('attendance.checkIn', ['token' => $code]);

                Invitation::create([
                    'user_id'                => $uid,
                    'convocation_session_id' => $sessionId,
                    'code'                   => $code,
                    'payload'                => [
                        'url'        => $url,
                        'code'       => $code,
                        'user_id'    => $uid,
                        'session_id' => $sessionId,
                        'issued_at'  => now()->toIso8601String(),
                    ],
                    'issued_at'              => now(),
                    'issued_by'              => auth()->id(),
                ]);
            });
        }

        return redirect()->route('admin.invitations.index')
            ->with('message', 'Invitations generated successfully.');
    }
    public function revoke(Invitation $invitation)
    {
        if ($invitation->revoked_at) {
            return back()->with('message', 'Invitation already revoked.');
        }

        // Revoke the invitation
        $invitation->update(['revoked_at' => now()]);

        // Rotate the attendance token for this user+session
        if ($invitation->convocation_session_id) {
            AttendanceRecord::where('user_id', $invitation->user_id)
                ->where('session_id', $invitation->convocation_session_id)
                ->update(['attendance_token' => (string) Str::uuid()]);
        }

        return back()->with('message', 'Invitation revoked.');
    }
}
