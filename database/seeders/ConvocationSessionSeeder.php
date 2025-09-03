<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConvocationSession;

class ConvocationSessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            [
                'name' => 'Morning Session',
                'date' => now()->addDays(10),
                'location' => 'Main Hall',
                'quota' => 100,
                'guest_quota' => 200,
                'registered' => 0,
                'guest_registered' => 0,
            ],
            [
                'name' => 'Afternoon Session',
                'date' => now()->addDays(10),
                'location' => 'Auditorium',
                'quota' => 150,
                'guest_quota' => 300,
                'registered' => 0,
                'guest_registered' => 0,
            ],
        ];

        foreach ($sessions as $session) {
            ConvocationSession::create($session);
        }
    }
}
