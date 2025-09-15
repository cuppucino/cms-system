<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     *
     * Note: we will update last_notif_seen_at via ->forceFill(), so it does not
     * need to be fillable. If you prefer mass-assignment, add it here.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'student_id',
        'course_id',
        // 'last_notif_seen_at', // optional
    ];

    /**
     * Hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'last_notif_seen_at'=> 'datetime', // 👈 add this
        ];
    }

    // -------------------------
    // Relationships
    // -------------------------

    public function gownCollection()
    {
        return $this->hasOne(GownCollection::class);
    }

    public function registrations()
    {
        return $this->hasMany(SessionRegistration::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * A user's own notifications (NOT including global).
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Base query for notifications visible to this user:
     * - user-specific OR global (user_id is null)
     */
    public function visibleNotifications(): Builder
    {
        return Notification::query()
            ->where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', $this->id);
            });
    }

    // -------------------------
    // Accessors for header bell
    // -------------------------

    /**
     * Unread count based on "last seen" timestamp.
     * Unread = notifications created AFTER last_notif_seen_at.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->visibleNotifications()
            ->when($this->last_notif_seen_at, function ($q) {
                $q->where('created_at', '>', $this->last_notif_seen_at);
            })
            ->count();
    }

    /**
     * Recent notifications list (merge global + user-specific).
     * Returns last 5 by created_at desc, with computed is_read.
     */
    public function getRecentNotificationsAttribute()
    {
        $lastSeen = $this->last_notif_seen_at;

        return $this->visibleNotifications()
            ->latest()
            ->limit(5)
            ->get(['id', 'title', 'message', 'created_at'])
            ->map(function ($n) use ($lastSeen) {
                $isRead = $lastSeen ? $n->created_at->lte($lastSeen) : false;

                return [
                    'id'         => $n->id,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'is_read'    => $isRead,
                    'created_at' => $n->created_at->toDateTimeString(),
                ];
            });
    }
}
