<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AllowsAdmin
{
    // Admins can do everything on these resources
    public function before(User $user, string $ability): bool|null
    {
        return $user->is_admin ? true : null;
    }
}
