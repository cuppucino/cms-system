<?php

// database/seeders/SessionRegistrationSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionRegistration;
use App\Models\User;
use App\Models\ConvocationSession;

class SessionRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('is_admin', false)->first();
        $session = ConvocationSession::first();

        if ($student && $session) {
            SessionRegistration::create([
                'user_id' => $student->id,
                'convocation_session_id' => $session->id,
                'guest_count' => 2, // booked 2 guest tickets
            ]);
        }
    }
}
