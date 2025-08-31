<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Course;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/Users/Index', [
            "users" => User::with('course')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/Users/Create', [
            "courses" => Course::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'student_id' => 'nullable|string|max:20',
            'course_id' => 'nullable|exists:courses,id',
            'is_admin' => 'required|boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_admin'] = (bool) $request->is_admin;

        User::create($validated);

        return redirect()->route('admin.users.index')->with('message', 'User created successfully.');
    }

    public function edit(string $id)
    {
        return Inertia::render("admin/Users/Edit", [
            "user" => User::findOrFail($id),
            "courses" => Course::all(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'student_id' => 'nullable|string|max:20',
            'course_id' => 'nullable|exists:courses,id',
            'is_admin' => 'required|boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_admin'] = (bool) $request->is_admin;

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('message', 'User updated successfully.');
    }

    public function show(string $id)
    {
        return Inertia::render("admin/Users/Show", [
            "user" => User::with(['course', 'registrations.session'])->findOrFail($id),
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('admin.users.index')->with('message', 'User deleted successfully.');
        }

        return redirect()->route('admin.users.index')->withErrors(['message' => 'User not found.']);
    }
}
