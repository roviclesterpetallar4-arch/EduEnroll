<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Course;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Presentation dashboard for the portal's 1:1, 1:N, N:N, and has-many-through examples.
 * It eager-loads each relationship so the demonstration view does not trigger N+1 queries.
 */
class RelationshipsController extends Controller
{
    public function index(): View
    {
        $departments = Department::query()
            ->with([
                'courses.studentProfiles.user',
                'courses.department',
                'courses.students.studentProfile',
                'students.user',
            ])
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        $studentProfiles = StudentProfile::query()
            ->with(['user.courses', 'course.department'])
            ->orderBy('student_number')
            ->limit(8)
            ->get();

        $metrics = [
            'students' => User::query()->whereHas('studentProfile')->count(),
            'departments' => Department::query()->count(),
            'courses' => Course::query()->count(),
            'enrollments' => DB::table('course_user')->count(),
        ];

        return view('relationships.index', compact('departments', 'studentProfiles', 'metrics'));
    }
}
