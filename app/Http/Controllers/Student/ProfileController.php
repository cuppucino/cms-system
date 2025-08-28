<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->only([
            'name','email','student_id','course_id','gown_size','status','convocation_session_id'
        ]);

        return Inertia::render('student/Profile/Edit', [
            'user'    => $user,
            'courses' => Course::select('id','name')->orderBy('name')->get(),
            'sizes'   => ['XS','S','M','L','XL'],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'student_id'=> ['nullable','string','max:50'],
            'course_id' => ['nullable','exists:courses,id'],
            'gown_size' => ['nullable','in:XS,S,M,L,XL'],
        ]);

        $user = Auth::user();
        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }
}
