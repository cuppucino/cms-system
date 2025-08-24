<?php

namespace Database\Factories;

use App\Models\SessionRegistration;
use App\Models\User;
use App\Models\ConvocationSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionRegistrationFactory extends Factory
{
    protected $model = SessionRegistration::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'session_id' => ConvocationSession::factory(),
            'guest_count' => $this->faker->numberBetween(0, 3),
        ];
    }
}
