<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use Inertia\Inertia;

class SessionRegistrationController extends Controller
{
    public function index()
    {
        $registrations = SessionRegistration::with(['user', 'session'])->get();

        return Inertia::render('admin/Sessions/Registrations', [
            'registrations' => $registrations->map(fn($r) => [
                'id' => $r->id,
                'student_name' => $r->user->name,
                'session_name' => $r->session->name,
                'guest_count' => $r->guest_count,
            ])
        ]);
    }
}
