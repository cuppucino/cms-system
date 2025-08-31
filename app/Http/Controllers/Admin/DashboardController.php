<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use App\Models\Payment;
use App\Models\AttendanceRecord;
use App\Models\GownCollection;
use App\Models\Notification;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'students'        => User::where('is_admin', false)->count(),
                'registrations'   => SessionRegistration::count(),
                'sessions'        => ConvocationSession::count(),
                'gownCollections' => GownCollection::count(),
                'payments'        => Payment::count(),
                'attendance'      => AttendanceRecord::count(),
            ],
            'recentRegistrations' => SessionRegistration::with('user', 'session')
                ->latest()
                ->take(5)
                ->get(),
            'recentPayments' => Payment::with('user')
                ->latest()
                ->take(5)
                ->get(),
            'notifications' => Notification::latest()
                ->take(5)
                ->get(),
        ]);
    }
}
