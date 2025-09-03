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
    \App\Models\User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'is_admin' => false,
    ]);

    $this->call([
        AdminUserSeeder::class,
        ConvocationSessionSeeder::class,
        SessionRegistrationSeeder::class,
        GownStockSeeder::class,
        CourseSeeder::class,
    ]);
}



}
