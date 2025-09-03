<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GownCollection;
use App\Policies\Concerns\AllowsAdmin;

class GownCollectionPolicy
{
    use AllowsAdmin;

    public function view(User $user, GownCollection $gc): bool
    {
        return $gc->user_id === $user->id;
    }

    public function update(User $user, GownCollection $gc): bool
    {
        // Student can change while not collected
        return $gc->user_id === $user->id && $gc->status !== 'collected';
    }

    public function delete(User $user, GownCollection $gc): bool
    {
        // Student can cancel while not collected
        return $gc->user_id === $user->id && $gc->status !== 'collected';
    }
}
