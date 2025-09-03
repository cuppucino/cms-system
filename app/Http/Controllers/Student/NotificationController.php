<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Show notifications (global + user-specific), newest first.
     * Read state is computed from user->last_notif_seen_at.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Global (user_id null) OR addressed to this user
        $notifications = Notification::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->latest()
            ->get();

        $lastSeen = $user->last_notif_seen_at;

        $payload = $notifications->map(function ($n) use ($lastSeen) {
            $isReadByLastSeen = $lastSeen ? $n->created_at->lte($lastSeen) : false;

            // Fallback: if you still want to respect the legacy is_read column for user-specific rows
            // (won’t help for global rows), we OR them:
            $legacyIsRead = (bool)($n->is_read ?? false);

            return [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'is_read'    => $isReadByLastSeen || $legacyIsRead,
                'created_at' => $n->created_at->toDateTimeString(),
            ];
        });

        // Render to your existing page (ensure the case matches your file path)
        return Inertia::render('student/Notifications/Index', [
            'notifications' => $payload,
        ]);
    }

    /**
     * Legacy per-item read (kept so your existing route keeps working).
     * If you migrate fully to last_notif_seen_at, prefer markAllSeen().
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        // Only affects user-specific rows (global rows would flip for everyone if you updated them)
        if ($notification && $notification->isFillable('is_read')) {
            $notification->update(['is_read' => true]);
        }
        return back();
    }

    public function seen(Request $request)
    {
        $user = $request->user();
        $user->forceFill(['last_notif_seen_at' => now()])->save();

        return back(); // Inertia will refresh shared props, updating the badge
    }

    /**
     * Mark ALL as seen by stamping user's last_notif_seen_at.
     * Add a POST route to: student/notifications/seen
     */
    public function markAllSeen(Request $request)
    {
        $user = $request->user();
        $user->forceFill(['last_notif_seen_at' => now()])->save();

        return back(); // or return redirect()->route('student.notifications.index');
    }

    /**
     * Small JSON feed for the bell dropdown (no sockets/polling required if
     * you only want updates on navigation; still useful to share a single function).
     * Unread = created_at > user.last_notif_seen_at
     */
    public function feed(Request $request)
    {
        $user = $request->user();
        $lastSeen = $user->last_notif_seen_at;

        $base = Notification::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            });

        $unread = (clone $base)
            ->when($lastSeen, fn($q) => $q->where('created_at', '>', $lastSeen))
            ->count();

        $recent = (clone $base)
            ->latest()
            ->limit(10)
            ->get(['id', 'title', 'message', 'created_at', 'is_read']) // is_read optional/legacy
            ->map(function ($n) use ($lastSeen) {
                $readByLastSeen = $lastSeen ? $n->created_at->lte($lastSeen) : false;
                $legacy = (bool)($n->is_read ?? false);
                return [
                    'id'         => $n->id,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'is_read'    => $readByLastSeen || $legacy,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread' => $unread,
            'recent' => $recent,
        ]);
    }
}
