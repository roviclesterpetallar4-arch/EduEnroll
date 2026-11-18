@extends('layouts.app')

@section('title', 'Student Portal')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-semibold">{{ session('status') }}</span>
            </div>
            <a href="{{ route('courses.index') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition">
                Enroll Now &rarr;
            </a>
        </div>
    @endif

    <section data-scroll-exit class="rounded-3xl bg-gradient-to-r from-ncc-maroon to-ncc-maroonDark p-6 text-white shadow-lg sm:p-9">
        <p class="text-xs font-bold uppercase tracking-widest text-amber-200">Student Portal</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Welcome, {{ $student->name }}</h1>
        <p class="mt-2 text-sm text-white/75">{{ $student->email }} <span class="ml-2 rounded-full bg-emerald-400/15 px-2.5 py-1 text-xs font-semibold text-emerald-100">Email verified</span></p>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900">My Courses</h2>
                <p class="mt-1 text-sm text-slate-500">Courses confirmed by NCC administration.</p>
            </div>
            <a href="{{ route('courses.index') }}" class="rounded-xl bg-ncc-maroon px-4 py-2.5 text-sm font-bold text-white transition hover:bg-ncc-maroonHover">
                Browse Course Catalog
            </a>
        </div>

        @if ($student->courses->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="font-semibold text-slate-700">No courses are linked to your account yet.</p>
                <p class="mt-1 text-sm text-slate-500">Your course information will appear here when it is added to your student record.</p>
            </div>
        @else
            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                            <th class="pb-3 pr-4">Course</th>
                            <th class="pb-3 pr-4">Department</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($student->courses as $course)
                            <tr class="border-b border-slate-100 last:border-0">
                                <td class="py-4 pr-4">
                                    <span class="font-bold text-ncc-maroon">{{ $course->code }}</span>
                                    <span class="ml-2 text-slate-700">{{ $course->title }}</span>
                                </td>
                                <td class="py-4 pr-4 text-slate-600">{{ $course->department->name }}</td>
                                <td class="py-4">
                                    <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">
                                        Accepted — Awaiting Exam &amp; Interview
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @php
        $unapprovedCourseRequests = $student->courseEnrollmentRequests
            ->whereIn('status', [
                \App\Models\CourseEnrollmentRequest::STATUS_PENDING,
                \App\Models\CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING,
                \App\Models\CourseEnrollmentRequest::STATUS_REJECTED,
            ]);
    @endphp

    @if ($unapprovedCourseRequests->isNotEmpty())
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="course-requests-heading">
            <div class="mb-5">
                <h2 id="course-requests-heading" class="text-xl font-bold text-slate-900">Course Requests</h2>
                <p class="mt-1 text-sm text-slate-500">Track your course requests and cancellation reviews.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                            <th class="pb-3 pr-4">Course</th>
                            <th class="pb-3 pr-4">Department</th>
                            <th class="pb-3 pr-4">Requested</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unapprovedCourseRequests as $courseRequest)
                            <tr class="border-b border-slate-100 last:border-0">
                                <td class="py-4 pr-4">
                                    <a href="{{ route('courses.show', $courseRequest->course) }}" class="font-bold text-ncc-maroon hover:underline">
                                        {{ $courseRequest->course->code }}
                                    </a>
                                    <span class="ml-2 text-slate-700">{{ $courseRequest->course->title }}</span>
                                </td>
                                <td class="py-4 pr-4 text-slate-600">{{ $courseRequest->course->department->name }}</td>
                                <td class="whitespace-nowrap py-4 pr-4 text-slate-600">{{ $courseRequest->created_at->format('M j, Y') }}</td>
                                <td class="py-4">
                                    @if ($courseRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_PENDING)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Pending</span>
                                    @elseif ($courseRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Cancellation Pending</span>
                                    @elseif ($courseRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_REJECTED)
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
@endsection
