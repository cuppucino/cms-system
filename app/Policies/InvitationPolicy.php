<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invitation;
use App\Policies\Concerns\AllowsAdmin;

class InvitationPolicy
{
    use AllowsAdmin;

    // Students may view their own invitation (read-only)
    public function view(User $user, Invitation $inv): bool
    {
        return $inv->user_id === $user->id;
    }

    // Admin-only actions handled by AllowsAdmin before()
    public function create(User $user): bool { return false; }
    public function revoke(User $user, Invitation $inv): bool { return false; }
}
