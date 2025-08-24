<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConvocationSession;

class ConvocationSessionSeeder extends Seeder
{
    public function run(): void
    {
        ConvocationSession::create([
            'name' => 'Morning Session',
            'date' => '2025-09-15 09:00:00',
            'location' => 'Main Hall',
            'quota' => 200,
        ]);

        ConvocationSession::create([
            'name' => 'Afternoon Session',
            'date' => '2025-09-15 14:00:00',
            'location' => 'Main Hall',
            'quota' => 150,
        ]);
    }
}
