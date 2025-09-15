<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\ConvocationSession;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // fetch sessions by name for assignment
        $day1Morning   = ConvocationSession::where('name', 'Day 1 Morning Session')->first();
        $day1Afternoon = ConvocationSession::where('name', 'Day 1 Afternoon Session')->first();
        $day2Morning   = ConvocationSession::where('name', 'Day 2 Morning Session')->first();
        $day2Afternoon = ConvocationSession::where('name', 'Day 2 Afternoon Session')->first();

        $courses = [
            // ===== SESSION 1 =====
            // Doctoral Programmes
            ['name' => 'Doctor of Philosophy (Engineering)', 'code' => 'PHDENG', 'faculty' => 'FEGT', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Bright Orange'],
            ['name' => 'Doctor of Philosophy (Computer Science)', 'code' => 'PHDCOMP', 'faculty' => 'FICT', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Golden Yellow'],
            ['name' => 'Doctor of Philosophy (Science)', 'code' => 'PHDSCI', 'faculty' => 'FSc', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Purple'],
            ['name' => 'Doctor of Philosophy (Chinese Studies)', 'code' => 'PHDCHN', 'faculty' => 'ICS', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Burgundy Red'],
            ['name' => 'Doctor of Philosophy (Medical Science)', 'code' => 'PHDMED', 'faculty' => 'MK FMHS', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Crimson & White'],

            // Masters
            ['name' => 'Master of Strategic Communication', 'code' => 'MSC', 'faculty' => 'FAS', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Burgundy Red'],
            ['name' => 'Master of Engineering (Electronic Systems)', 'code' => 'MEE', 'faculty' => 'FEGT', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Bright Orange'],
            ['name' => 'Master of Sustainable Construction Management', 'code' => 'MSCM', 'faculty' => 'FEGT', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Purple'],
            ['name' => 'Master of Information Systems', 'code' => 'MIS', 'faculty' => 'FICT', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Golden Yellow'],
            ['name' => 'Master of Business Administration', 'code' => 'MBA', 'faculty' => 'FAM', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Blue'],

            // Bachelors
            ['name' => 'Bachelor of Science (Hons) Agricultural Science', 'code' => 'BSCAGR', 'faculty' => 'FSc', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Purple'],
            ['name' => 'Bachelor of Science (Hons) Biochemistry', 'code' => 'BSCBIOC', 'faculty' => 'FSc', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Purple'],
            ['name' => 'Bachelor of Science (Hons) Biotechnology', 'code' => 'BSCBIO', 'faculty' => 'FSc', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Purple'],
            ['name' => 'Bachelor of Science (Hons) Chemistry', 'code' => 'BSCCHEM', 'faculty' => 'FSc', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Purple'],
            ['name' => 'Bachelor of Science (Hons) Food Science', 'code' => 'BSCFS', 'faculty' => 'FSc', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Purple'],
            ['name' => 'Bachelor of Communication (Hons) Journalism', 'code' => 'BCOMMJ', 'faculty' => 'FAS', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Burgundy Red'],
            ['name' => 'Bachelor of Social Science (Hons) Psychology', 'code' => 'BSSPSY', 'faculty' => 'FAS', 'convocation_session_id' => $day1Morning?->id, 'hood_color' => 'Burgundy Red'],

            // ===== SESSION 2 =====
            ['name' => 'Bachelor of Accounting (Hons)', 'code' => 'BACC', 'faculty' => 'FAM', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Blue'],
            ['name' => 'Bachelor of Business Administration (Hons)', 'code' => 'BBA', 'faculty' => 'FAM', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Blue'],
            ['name' => 'Bachelor of Economics (Hons) Global Economics', 'code' => 'BECO', 'faculty' => 'FAM', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Blue'],
            ['name' => 'Bachelor of International Business (Hons)', 'code' => 'BIB', 'faculty' => 'FAM', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Blue'],
            ['name' => 'Bachelor of Arts (Hons) Chinese Studies', 'code' => 'BCHN', 'faculty' => 'ICS', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Burgundy Red'],
            ['name' => 'Bachelor of Biomedical Engineering (Hons)', 'code' => 'BBME', 'faculty' => 'LKC FES', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Bright Orange'],
            ['name' => 'Bachelor of Chemical Engineering (Hons)', 'code' => 'BCE', 'faculty' => 'LKC FES', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Bright Orange'],
            ['name' => 'Bachelor of Mechanical Engineering (Hons)', 'code' => 'BME', 'faculty' => 'LKC FES', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Bright Orange'],
            ['name' => 'Bachelor of Software Engineering (Hons)', 'code' => 'BSE', 'faculty' => 'FICT', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Golden Yellow'],
            ['name' => 'Bachelor of Science (Hons) Physics', 'code' => 'BPHYS', 'faculty' => 'FSc', 'convocation_session_id' => $day2Morning?->id, 'hood_color' => 'Purple'],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
