@extends('layouts.app')

@section('title', $course->code . ' - ' . $course->title . ' | Northeastern Cebu Colleges')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Back Link -->
    <div>
        <a href="{{ route('courses.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-ncc-maroon transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            &larr; Back to NCC Course Catalog
        </a>
    </div>

    <!-- Main Grid: Course Details & Enrollment Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Course Details Column (Span 2) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Course Hero Overview Card -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <!-- Badges Row -->
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-extrabold bg-ncc-maroon/10 text-ncc-maroon border border-ncc-maroon/20 tracking-wider">
                        {{ $course->code }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Open for Enrollment
                    </span>
                </div>

                <!-- Course Title -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                    {{ $course->title }}
                </h1>

                <!-- Key Details Spec Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Course Units</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $course->units }} units</div>
                    </div>

                    <!-- Department -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Academic Department</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $course->department->name }}</div>
                        <div class="text-xs text-ncc-maroon font-semibold mt-0.5">Code: {{ $course->department->code }}</div>
                    </div>

                    <!-- Department Head -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Department Chairperson</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $course->department->head ?? 'Dean of Academic Affairs' }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">Department Chairman/Chairwoman</div>
                    </div>

                    <!-- Instructor -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Program Chairperson</span>
                        <div class="font-bold text-ncc-maroon text-sm">{{ $course->instructor ?? 'Assigned Faculty' }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">Program Chairman/Chairwoman</div>
                    </div>

                    <!-- Prerequisites -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Prerequisites</span>
                        <div class="font-bold text-slate-800 text-sm">
                            @if ($course->prerequisites && $course->prerequisites !== 'None')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-900 text-xs font-bold border border-amber-300">
                                    {{ $course->prerequisites }}
                                </span>
                            @else
                                <span class="text-slate-500 text-xs">None (Open to all eligible students)</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Curricular Requirement</div>
                    </div>
                </div>

                <!-- Course Description -->
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Course Syllabus &amp; Description</h3>
                    <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $course->description }}</p>
                </div>
            </div>

        </div>

        <!-- Enrollment Form Column (Span 1) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm sticky top-28">
                <div class="border-b border-slate-100 pb-4 mb-5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-ncc-maroon/10 text-ncc-maroon flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 leading-tight">Course Enrollment</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Request enrollment in this course</p>
                    </div>
                </div>

                @auth
                    @if (auth()->user()->isAdmin())
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            Student course requests can be reviewed from the <a href="{{ route('admin.enrollment-requests') }}" class="font-bold text-ncc-maroon underline">admin dashboard</a>.
                        </div>
                    @elseif ($courseEnrollmentRequest?->status === \App\Models\CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING)
                        <div role="status" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                            <p class="font-bold">Cancellation pending review</p>
                            <p class="mt-1 text-xs text-amber-800">You remain enrolled until NCC administration approves your cancellation.</p>
                        </div>
                    @elseif ($isEnrolled)
                        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                            <p class="font-bold">Accepted — Awaiting Exam &amp; Interview Date</p>
                            <p class="mt-1 text-xs text-emerald-700">Your course request has been accepted. Please wait for NCC to contact you with your exam and interview schedule.</p>
                            <form action="{{ route('courses.enrollment.cancel', $course->id) }}" method="POST" class="mt-4" data-single-submit>
                                @csrf
                                @include('courses.partials.cancellation-fields')
                                <button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Request Course Cancellation
                                </button>
                            </form>
                        </div>
                    @elseif ($courseEnrollmentRequest?->status === \App\Models\CourseEnrollmentRequest::STATUS_PENDING)
                        <div role="status" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                            <p class="font-bold">Request pending review</p>
                            <p class="mt-1 text-xs text-amber-800">NCC administration will review your course enrollment request.</p>
                            <form action="{{ route('courses.enrollment.cancel', $course->id) }}" method="POST" class="mt-4" data-single-submit>
                                @csrf
                                @include('courses.partials.cancellation-fields')
                                <button type="submit" class="rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-bold text-amber-900 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    Cancel Request
                                </button>
                            </form>
                        </div>
                    @elseif ($courseEnrollmentRequest?->status === \App\Models\CourseEnrollmentRequest::STATUS_CANCELLED)
                        <div role="status" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-700">
                            <p class="font-bold">Your previous course request was cancelled.</p>
                            <p class="mt-1 text-xs text-slate-600">You may submit a new request if you want to enroll in this course.</p>
                        </div>
                    @elseif ($courseEnrollmentRequest?->status === \App\Models\CourseEnrollmentRequest::STATUS_APPROVED)
                        <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                            <p class="font-bold">Your request was approved.</p>
                            <p class="mt-1 text-xs text-emerald-700">Your course enrollment is being finalized. Contact NCC administration if it does not appear in your Student Portal.</p>
                        </div>
                    @elseif ($activeCourseRequest && $activeCourseRequest->course_id !== $course->id)
                        <div role="status" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                            <p class="font-bold">You can request one course at a time.</p>
                            <p class="mt-1 text-xs text-amber-800">
                                Your active {{ $activeCourseRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING ? 'cancellation' : 'request' }} is for
                                <a href="{{ route('courses.show', $activeCourseRequest->course) }}" class="font-bold underline">{{ $activeCourseRequest->course->code }}</a>.
                                @if ($activeCourseRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING)
                                    Wait for admin approval before requesting another course.
                                @else
                                    Cancel that request first to choose another course.
                                @endif
                            </p>
                        </div>
                    @elseif ($studentCourse && $studentCourse->id !== $course->id)
                        <div role="status" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                            <p class="font-bold">You are already enrolled in one course.</p>
                            <p class="mt-1 text-xs text-amber-800">
                                To switch courses, <a href="{{ route('courses.show', $studentCourse) }}" class="font-bold underline">request cancellation of {{ $studentCourse->code }}</a>.
                                An administrator must approve the cancellation before you can request this course.
                            </p>
                        </div>
                    @else
                        @if ($courseEnrollmentRequest?->status === \App\Models\CourseEnrollmentRequest::STATUS_REJECTED)
                            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-900">
                                <p class="font-bold">Your previous request was not approved.</p>
                                <p class="mt-1 text-xs text-red-800">You may submit a new request for review.</p>
                            </div>
                        @endif

                        <form action="{{ route('courses.enroll', $course->id) }}" method="POST" class="space-y-4" data-single-submit>
                        @csrf
                        <p class="text-xs leading-relaxed text-slate-500">Your request will be sent to NCC administration for approval.</p>
                        <button 
                            type="submit" 
                            class="w-full rounded-xl bg-ncc-maroon px-4 py-3 text-sm font-bold text-white shadow-md transition duration-150 hover:bg-ncc-maroonHover hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-ncc-gold focus:ring-offset-2"
                        >
                            Enroll in Course
                        </button>
                        </form>
                    @endif
                @else
                    <p class="mb-4 text-sm leading-relaxed text-slate-600">Sign in to your student account to request enrollment. NCC administration will review your request.</p>
                    <a
                        href="{{ route('courses.enroll.sign-in', $course->id) }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-ncc-maroon px-4 py-3 text-sm font-bold text-white shadow-md transition hover:bg-ncc-maroonHover hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-ncc-gold focus:ring-offset-2"
                    >
                        Sign In to Enroll
                    </a>
                @endauth
            </div>
        </div>

    </div>
</div>
@endsection
