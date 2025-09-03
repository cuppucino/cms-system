<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionRegistration;
use App\Models\User;
use App\Models\ConvocationSession;
use Illuminate\Support\Facades\DB;

class SessionRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        // Get all non-admin users (students) and sessions
        $students = User::where('is_admin', false)->take(5)->get();
        $sessions = ConvocationSession::all();

        if ($students->isEmpty() || $sessions->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($students, $sessions) {
            foreach ($students as $student) {
                // Randomly assign a session
                $session = $sessions->random();

                // Check quotas
                if ($session->registered >= $session->quota) {
                    return; // Skip if student quota is full
                }

                // Random guest count (0 to max_guest_per_student)
                $maxGuests = config('convocation.max_guest_per_student', 2);
                $guestCount = rand(0, min($maxGuests, $session->guest_quota - $session->guest_registered));

                if ($guestCount > 0 && $session->guest_registered + $guestCount > $session->guest_quota) {
                    return; // Skip if guest quota would be exceeded
                }

                // Create registration
                SessionRegistration::create([
                    'user_id' => $student->id,
                    'convocation_session_id' => $session->id,
                    'guest_count' => $guestCount,
                    'gown_size' => collect(['XS', 'S', 'M', 'L', 'XL'])->random(),
                    'attendance_confirmed' => true,
                    'collection_date' => now()->addDays(rand(1, 7)),
                ]);

                // Update session counters
                $session->increment('registered');
                $session->increment('guest_registered', $guestCount);
            }
        });
    }
}
