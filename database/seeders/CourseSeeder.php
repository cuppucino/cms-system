<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Software Engineering',
                'code' => 'SE',
                'faculty' => 'Faculty of Computing and Information Technology',
            ],
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'faculty' => 'Faculty of Computing and Information Technology',
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BA',
                'faculty' => 'Faculty of Business and Management',
            ],
            [
                'name' => 'Mechanical Engineering',
                'code' => 'ME',
                'faculty' => 'Faculty of Engineering',
            ],
            [
                'name' => 'Accounting',
                'code' => 'ACC',
                'faculty' => 'Faculty of Business and Finance',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
