<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;
use App\Policies\Concerns\AllowsAdmin;

class PaymentPolicy
{
    use AllowsAdmin;

    public function view(User $user, Payment $p): bool
    {
        return $p->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated student can attempt to create for their own gown; controller validates ownership
    }
}
