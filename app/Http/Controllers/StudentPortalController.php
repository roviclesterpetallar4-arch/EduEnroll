<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.enrollment-requests');
        }

        $student = $request->user()->load([
            'courses.department',
            'courseEnrollmentRequests.course.department',
        ]);

        return view('student.portal', compact('student'));
    }
}
