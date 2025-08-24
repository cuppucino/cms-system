<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class InvitationLetterController extends Controller
{
    /**
     * Generate and download invitation letter for a student.
     */
    public function show($id)
    {
        $user = User::with(['course', 'convocationSession'])->findOrFail($id);

        // Load Blade template into PDF
        $pdf = Pdf::loadView('admin.pdf.invitation', compact('user'));

        // Force download
        $filename = 'Invitation_' . ($user->student_id ?? $user->id) . '.pdf';
        return $pdf->download($filename);
    }
}

