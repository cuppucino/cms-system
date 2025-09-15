<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GownSession;
use Carbon\Carbon;

class GownSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old test data
        GownSession::truncate();

        // Insert a few dummy gown collection sessions
        GownSession::insert([
            [
                'date'       => Carbon::today()->addDays(10),
                'start_time' => '09:00',
                'end_time'   => '11:00',
                'location'   => 'Hall A - Main Campus',
                'quota'      => 50,
                'note'       => 'Morning slot for early collections',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date'       => Carbon::today()->addDays(10),
                'start_time' => '13:00',
                'end_time'   => '15:00',
                'location'   => 'Hall A - Main Campus',
                'quota'      => 40,
                'note'       => 'Afternoon slot',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date'       => Carbon::today()->addDays(12),
                'start_time' => '10:00',
                'end_time'   => '12:00',
                'location'   => 'Hall B - City Campus',
                'quota'      => 60,
                'note'       => 'Secondary campus session',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
