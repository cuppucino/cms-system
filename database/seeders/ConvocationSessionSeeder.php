<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConvocationSession;
use Carbon\Carbon;

class ConvocationSessionSeeder extends Seeder
{
    public function run(): void
    {
        $baseDate = Carbon::parse('2025-11-20'); // Example convocation start date

        $sessions = [
            [
                'name' => 'Day 1 Morning Session',
                'date' => $baseDate->copy()->setTime(9, 0),
                'location' => 'Main Hall',
                'quota' => 300,
                'guest_quota' => 600,
                'registered' => 0,
                'guest_registered' => 0,
            ],
            [
                'name' => 'Day 1 Afternoon Session',
                'date' => $baseDate->copy()->setTime(14, 0),
                'location' => 'Main Hall',
                'quota' => 300,
                'guest_quota' => 600,
                'registered' => 0,
                'guest_registered' => 0,
            ],
            [
                'name' => 'Day 2 Morning Session',
                'date' => $baseDate->copy()->addDay()->setTime(9, 0),
                'location' => 'Main Hall',
                'quota' => 300,
                'guest_quota' => 600,
                'registered' => 0,
                'guest_registered' => 0,
            ],
            [
                'name' => 'Day 2 Afternoon Session',
                'date' => $baseDate->copy()->addDay()->setTime(14, 0),
                'location' => 'Main Hall',
                'quota' => 300,
                'guest_quota' => 600,
                'registered' => 0,
                'guest_registered' => 0,
            ],
        ];

        foreach ($sessions as $session) {
            ConvocationSession::create($session);
        }
    }
}
