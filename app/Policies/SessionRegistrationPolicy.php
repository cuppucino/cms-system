<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SessionRegistration;
use App\Policies\Concerns\AllowsAdmin;

class SessionRegistrationPolicy
{
    use AllowsAdmin;

    public function view(User $user, SessionRegistration $reg): bool
    {
        return $reg->user_id === $user->id;
    }

    public function create(User $user): bool { return true; }

    public function update(User $user, SessionRegistration $reg): bool
    {
        return $reg->user_id === $user->id;
    }

    public function delete(User $user, SessionRegistration $reg): bool
    {
        return $reg->user_id === $user->id;
    }
}
