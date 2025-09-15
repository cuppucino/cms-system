<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;


class CourseController extends Controller
{

    // List courses
    public function index(Request $request)
    {
        $q = $request->input('q');

        $courses = Course::with('convocationSession')
            ->when($q, function ($query, $q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('faculty', 'like', "%{$q}%");
                })->orWhereHas('convocationSession', function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%");
                });
            })
            ->paginate(10)
            ->withQueryString(); // keep q in pagination links

        return Inertia::render('admin/Courses/Index', [
            'courses' => $courses,
            'filters' => [
                'q' => $q,
            ],
        ]);
    }

    // Show create form
    public function create()
    {
        $sessions = \App\Models\ConvocationSession::all(['id', 'name']);
        return Inertia::render('admin/Courses/Create', [
            'sessions' => $sessions
        ]);
    }

    // Store new course
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'faculty' => 'nullable|string|max:255',
            'convocation_session_id' => 'nullable|exists:convocation_sessions,id',
        ]);

        Course::create($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    // Show edit form
    public function edit(Course $course)
    {
        $sessions = \App\Models\ConvocationSession::all(['id', 'name']);
        return Inertia::render('admin/Courses/Edit', [
            'course'   => $course,
            'sessions' => $sessions
        ]);
    }

    // Update course
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'faculty' => 'nullable|string|max:255',
            'convocation_session_id' => 'nullable|exists:convocation_sessions,id',
        ]);

        $course->update($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    // Delete course
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}
