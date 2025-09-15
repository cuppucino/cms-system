<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class InvitationController extends Controller
{
    /**
     * Show the latest active invitation in the browser.
     * Returns a mapped object compatible with student/Invitation/Show.vue.
     */
    public function show()
    {
        $inv = Invitation::with('session')
            ->where('user_id', Auth::id())
            ->whereNull('revoked_at')              // only active
            ->latest()
            ->first();

        if (!$inv) {
            // Let the page render the empty state instead of redirecting
            return inertia('student/Invitation/Show', ['invitation' => null]);
        }

        $url = route('attendance.checkIn', ['token' => $inv->code]);

        $qrBase64 = base64_encode(
            QrCode::format('png')
                ->size(160)
                ->margin(1)
                ->generate($url)
        );

        // Map to the exact shape the Vue page expects
        $payload = [
            'session'    => $inv->session?->name,
            'created_at' => optional($inv->issued_at ?: $inv->created_at)->toDateTimeString(),
            'status'     => $inv->revoked_at ? 'Revoked' : 'Active',
            'qr_base64'  => $qrBase64,
            'code'       => $inv->code,
        ];

        return inertia('student/Invitation/Show', ['invitation' => $payload]);
    }

    /**
     * Show the invitation letter as a PDF in the browser (inline preview).
     */
    public function preview()
    {
        $invitation = Invitation::where('user_id', Auth::id())
            ->whereNull('revoked_at')
            ->latest()
            ->first();

        if (!$invitation) {
            return back()->with('error', 'No active invitation found.');
        }

        $pdf = Pdf::loadView('invitation', [
            'user'       => Auth::user(),
            'invitation' => $invitation,
        ]);

        // Inline preview
        return $pdf->stream('invitation.pdf', ['Attachment' => false]);
    }

    /**
     * Download/stream the invitation PDF with an embedded QR code.
     */
    public function download()
    {
        $invitation = Invitation::where('user_id', Auth::id())
            ->whereNull('revoked_at')
            ->latest()
            ->first();

        if (!$invitation) {
            return back()->with('error', 'No active invitation to download.');
        }

        $url = route('attendance.checkIn', ['token' => $invitation->code]);

        $qrCode = base64_encode(
            QrCode::format('png')
                ->size(150)
                ->margin(1)
                ->generate($url)
        );

        $pdf = Pdf::loadView('invitation', [
            'user'       => Auth::user(),
            'invitation' => $invitation,
            'qrCode'     => $qrCode,
        ]);

        // Stream (browser decides save/open)
        return $pdf->stream('invitation.pdf');
    }
}
