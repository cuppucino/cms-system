<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class InvitationController extends Controller
{
    /**
     * Show the latest active invitation in the browser
     */
    public function show()
    {
        $invitation = Invitation::where('user_id', Auth::id())
            ->whereNull('revoked_at')   // ✅ check if still active
            ->latest()
            ->first();

        if (!$invitation) {
            return back()->with('error', 'No active invitation found.');
        }

        return inertia('student/Invitation/Show', [
            'invitation' => $invitation,
        ]);
    }

    /**
     * Download the invitation letter as PDF
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invitation', [
            'user' => Auth::user(),
            'invitation' => $invitation,
        ]);

        // Show inline in browser
        return $pdf->stream('invitation.pdf', ['Attachment' => false]);
    }

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
            QrCode::format('png')->size(150)->margin(1)->generate($url)
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invitation', [
            'user'       => Auth::user(),
            'invitation' => $invitation,
            'qrCode'     => $qrCode,
        ]);

        return $pdf->stream('invitation.pdf');
    }
}
