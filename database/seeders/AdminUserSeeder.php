<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@cms.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@cms.com',
                'password' => Hash::make('password'), // Change this if needed
                 'is_admin' => 1,
            ]
        );
    }
}
