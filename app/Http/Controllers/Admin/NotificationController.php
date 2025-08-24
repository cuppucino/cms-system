<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('user')
            ->latest()
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'user_name' => $n->user->name,
                'title' => $n->title,
                'message' => $n->message,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at->toDateTimeString(),
            ]);

        return Inertia::render('admin/Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function create()
    {
        $students = User::where('is_admin', false)->get(['id', 'name', 'email']);
        return Inertia::render('admin/Notifications/Create', [
            'students' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'send_email' => 'boolean',
        ]);

        foreach ($request->user_ids as $userId) {
            $notification = Notification::create([
                'user_id' => $userId,
                'title' => $request->title,
                'message' => $request->message,
            ]);

            // Optionally send email
            if ($request->send_email) {
                $user = User::find($userId);
                if ($user) {
                    Mail::raw($request->message, function ($mail) use ($user, $request) {
                        $mail->to($user->email)
                            ->subject($request->title);
                    });
                }
            }
        }

        return redirect()->route('admin.notifications.index')
            ->with('message', 'Notifications sent successfully.');
    }
}
