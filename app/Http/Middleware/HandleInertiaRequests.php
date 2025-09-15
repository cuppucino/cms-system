<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Notification;
use Illuminate\Support\Arr;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        $shared = parent::share($request);
        $user = $request->user();

        if (!$user) {
            return array_merge($shared, [
                'auth' => ['user' => null],
            ]);
        }

        // Notifications visible to this user or global (user_id null)
        $base = Notification::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            });

        $unread = (clone $base)
            ->when(
                $user->last_notif_seen_at,
                fn($q) =>
                $q->where('created_at', '>', $user->last_notif_seen_at)
            )
            ->count();

        $recent = (clone $base)
            ->latest()
            ->limit(10)
            ->get(['id', 'title', 'message', 'created_at'])
            ->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'is_read'    => $user->last_notif_seen_at
                    ? $n->created_at->lte($user->last_notif_seen_at)
                    : false,
                'created_at' => $n->created_at->diffForHumans(),
                'created_ago' => $n->created_at->diffForHumans(),
            ]);

        return array_merge($shared, [
            'auth' => [
                'user' => array_merge(
                    Arr::only($user->toArray(), ['id', 'name', 'email']),
                    [
                        // ✅ Explicit cast so Vue sees true/false
                        'is_admin'             => (bool) $user->is_admin,
                        'unread_notifications' => $unread,
                        'recent_notifications' => $recent,
                        'course' => $user->course()
                            ->select('id', 'name', 'code', 'hood_color', 'convocation_session_id')
                            ->with('convocationSession:id,name')
                            ->first(),
                    ]
                ),
            ],

            // Optional: expose Ziggy routes to JS
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }
}
