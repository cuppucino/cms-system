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

    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }
    public function index(Request $request)
    {
        $q    = trim((string) $request->input('search', ''));
        $read = $request->input('read'); // 'read' | 'unread' | null

        $list = \App\Models\Notification::with('user')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                        ->orWhere('message', 'like', "%{$q}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%"));
                });
            })
            ->when($read === 'read', fn($qb) => $qb->where('is_read', true))
            ->when($read === 'unread', fn($qb) => $qb->where('is_read', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $notifications = $list->getCollection()->map(fn($n) => [
            'id'         => $n->id,
            'user_name'  => $n->user?->name ?? 'All Students',
            'title'      => $n->title,
            'message'    => $n->message,
            'is_read'    => (bool) $n->is_read,
            'created_at' => $n->created_at->toDateTimeString(),
        ]);

        return Inertia::render('admin/Notifications/Index', [
            'notifications' => $notifications,
            'pagination'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
            'filters'       => $request->only(['search', 'read']),
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
        $data = $request->validate([
            'user_ids'     => 'array',
            'user_ids.*'   => 'exists:users,id',
            'send_to_all'  => 'boolean',
            'title'        => 'required|string|max:255',
            'message'      => 'required|string|max:5000',
            'send_email'   => 'boolean',
        ]);

        // Resolve recipients
        $recipientIds = [];
        if ($request->boolean('send_to_all')) {
            $recipientIds = User::where('is_admin', false)->pluck('id')->all();
        } else {
            $recipientIds = $data['user_ids'] ?? [];
            if (empty($recipientIds)) {
                return back()->withErrors(['user_ids' => 'Select at least one recipient or choose "Send to all".']);
            }
        }

        // Build rows (batch insert)
        $now  = now();
        $rows = array_map(fn($uid) => [
            'user_id'    => $uid,
            'title'      => $data['title'],
            'message'    => $data['message'],
            'is_read'    => false,
            'created_at' => $now,
            'updated_at' => $now,
        ], $recipientIds);

        \App\Models\Notification::insert($rows);

        // Optional email (queue for scale)
        if ($request->boolean('send_email')) {
            foreach (array_chunk($recipientIds, 200) as $chunk) {
                $users = User::whereIn('id', $chunk)->get(['email']);
                foreach ($users as $u) {
                    \Mail::to($u->email)->queue(new \App\Mail\PlainNotificationMail(
                        subject: $data['title'],
                        body: $data['message']
                    ));
                }
            }
        }

        return redirect()->route('admin.notifications.index')->with('message', 'Notifications sent successfully.');
    }
}
