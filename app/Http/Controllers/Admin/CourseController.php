<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;


class CourseController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }

    // List courses
    public function index()
    {
        $courses = Course::latest()->paginate(10);
        return Inertia::render('admin/Courses/Index', [
            'courses' => $courses
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('admin/Courses/Create');
    }

    // Store new course
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'faculty' => 'nullable|string|max:255',
        ]);

        Course::create($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    // Show edit form
    public function edit(Course $course)
    {
        return Inertia::render('admin/Courses/Edit', [
            'course' => $course
        ]);
    }

    // Update course
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'faculty' => 'nullable|string|max:255',
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
