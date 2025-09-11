<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\User;
use App\Models\SessionRegistration;
use App\Models\ConvocationSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::with('user')->get();
        $guestData = [];

        if ($guests->isEmpty()) {
            // Fallback: use session registrations
            $registrations = SessionRegistration::with('user')->get();
            $guestData = $registrations->map(function ($registration) {
                $totalRegistered = $registration->guest_count;
                return [
                    'id' => $registration->id,
                    'student_name' => $registration->user?->name ?? 'Unknown Student',
                    'student_id' => $registration->user?->student_id ?? '—', // ✅ added
                    'guests_registered' => $totalRegistered,
                ];
            });
        } else {
            // Normal: use actual guests
            $guestData = $guests->map(function ($guest) {
                $totalRegistered = Guest::where('user_id', $guest->user_id)->count();
                return [
                    'id' => $guest->id,
                    'student_name' => $guest->user?->name ?? 'Unknown Student',
                    'student_id' => $guest->user?->student_id ?? '—', // ✅ added
                    'guests_registered' => $totalRegistered,
                ];
            });
        }

        // Calculate totals
        $sessions = ConvocationSession::all();
        $totalRegistered = $sessions->sum('guest_registered');
        $totalSlots = $sessions->sum('guest_quota');
        $slotsLeft = $totalSlots - $totalRegistered;

        return Inertia::render('admin/Guest/Index', [
            'guests' => $guestData,
            'totals' => [
                'total_registered' => $totalRegistered,
                'slots_left' => $slotsLeft,
            ],
        ]);
    }

    public function edit($id)
    {
        $guest = Guest::with('user')->findOrFail($id);
        $students = User::where('is_admin', false)->get(['id', 'name', 'email']);
        return Inertia::render('admin/Guest/Edit', [
            'guest' => $guest,
            'students' => $students,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
        ]);

        $guest = Guest::findOrFail($id);
        $guest->update($data);

        return redirect()->route('admin.guest.index')->with('message', 'Guest updated successfully.');
    }

    public function destroy($id)
    {
        $guest = Guest::findOrFail($id);
        $userId = $guest->user_id;
        $guest->delete();

        $registration = SessionRegistration::where('user_id', $userId)->first();
        if ($registration) {
            $registeredGuests = Guest::where('user_id', $userId)->count();
            $maxGuests = config('convocation.max_guest_per_student', 2);
            $registration->update(['guest_count' => $registeredGuests]);
        }

        return redirect()->route('admin.guest.index')->with('message', 'Guest removed successfully.');
    }
}
