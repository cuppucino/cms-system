<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/Users/Index', [
            "users" => User::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'is_admin' => 'required|boolean', // safely converts 0/1 to boolean
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_admin'] = (bool) $request->is_admin;

        User::create($validated);

        return redirect()->route('users.index')->with('message', 'User created successfully.');
    }

    public function edit(string $id)
    {
        return Inertia::render("admin/Users/Edit", [
            "user" => User::find($id),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'is_admin' => 'required|boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_admin'] = (bool) $request->is_admin;

        $user->update($validated);

        return redirect()->route('users.index')->with('message', 'User updated successfully.');
    }

    public function show (string $id)
    {
        return Inertia::render("admin/Users/Show", [
            "user" => User::find($id),
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('users.index')->with('message', 'User deleted successfully.');
        }

        return redirect()->route('users.index')->withErrors(['message' => 'User not found.']);
    }
}
