<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();
        $studentCounter = 1; // global counter for IDs

        foreach ($courses as $course) {
            for ($i = 0; $i < 30; $i++) {
                User::create([
                    'name'       => fake()->name(),
                    'email'      => fake()->unique()->safeEmail(),
                    'password'   => Hash::make('password'), // default
                    'is_admin'   => false,
                    'course_id'  => $course->id,
                    'student_id' => str_pad($studentCounter, 5, '0', STR_PAD_LEFT),
                ]);

                $studentCounter++;
            }
        }
    }
}
