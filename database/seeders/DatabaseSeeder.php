<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ConvocationSessionSeeder::class,
            CourseSeeder::class,
            UserSeeder::class,
            SessionRegistrationSeeder::class,
            GownStockSeeder::class,
            GownSessionSeeder::class,
        ]);
    }
}
