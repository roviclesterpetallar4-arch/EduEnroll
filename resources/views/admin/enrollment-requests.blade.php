@extends('layouts.app')

@section('title', 'Enrollment Requests')

@section('content')
<section class="space-y-6">
    <div data-scroll-exit class="rounded-3xl bg-gradient-to-r from-ncc-maroon to-ncc-maroonDark p-6 text-white shadow-lg sm:p-8">
        <p class="text-xs font-bold uppercase tracking-widest text-amber-200">Administration</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Enrollment Requests</h1>
        <p class="mt-2 text-sm text-white/75">Review admission applications and course enrollment requests.</p>
    </div>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="academic-period-heading">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-ncc-maroon">Academic Calendar</p>
                <h2 id="academic-period-heading" class="mt-1 text-xl font-extrabold text-slate-900">Academic year</h2>
                <p class="mt-1 text-sm text-slate-500">Keep one record for each academic year. The active record is displayed in the portal header.</p>
                <p class="mt-3 inline-flex rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-900">
                    Current: {{ $activeAcademicPeriod?->label() ?? 'Not set' }}
                </p>
            </div>

            <form method="POST" action="{{ route('admin.academic-periods.store') }}" class="grid w-full gap-3 sm:grid-cols-[1fr_auto] lg:max-w-xl" data-single-submit>
                @csrf
                <label class="text-xs font-semibold text-slate-600">
                    Academic year starts
                    <input type="number" name="year_start" min="2000" max="2099" value="{{ old('year_start', now()->year) }}" required
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
                </label>
                <button type="submit" class="self-end rounded-xl bg-ncc-maroon px-4 py-2.5 text-sm font-bold text-white transition hover:bg-ncc-maroonHover focus:outline-none focus:ring-2 focus:ring-ncc-maroon focus:ring-offset-2">
                    Add Record
                </button>
            </form>
        </div>

        @if ($errors->any())
            <div role="alert" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div role="status" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full min-w-[34rem] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Academic Year</th>
                        <th class="px-4 py-3">Record</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($academicPeriods as $academicPeriod)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $academicPeriod->label() }}</td>
                            <td class="px-4 py-3">
                                @if ($academicPeriod->is_active)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">Active</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">History / Planned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @unless ($academicPeriod->is_active)
                                    <form method="POST" action="{{ route('admin.academic-periods.activate', $academicPeriod) }}" data-single-submit>
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-ncc-maroon/20 px-3 py-2 text-xs font-bold text-ncc-maroon transition hover:bg-ncc-maroon hover:text-white">
                                            Set Active
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Currently displayed</span>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Applications', 'value' => $metrics['total'], 'style' => 'border-slate-200 text-slate-900'],
            ['label' => 'Pending Review', 'value' => $metrics['pending'], 'style' => 'border-amber-200 text-amber-800'],
            ['label' => 'Confirmed', 'value' => $metrics['approved'], 'style' => 'border-emerald-200 text-emerald-800'],
            ['label' => 'Rejected', 'value' => $metrics['rejected'], 'style' => 'border-red-200 text-red-800'],
        ] as $metric)
            <article class="admin-metric rounded-2xl border bg-white p-5 shadow-sm {{ $metric['style'] }}">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $metric['label'] }}</p>
                <p class="mt-2 text-3xl font-extrabold">{{ $metric['value'] }}</p>
            </article>
        @endforeach
    </div>

    <section class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="accepted-history-heading">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-ncc-maroon">Enrollment History</p>
                <h2 id="accepted-history-heading" class="mt-1 text-xl font-extrabold text-slate-900">Accepted Students by Organization</h2>
                <p class="mt-1 text-sm text-slate-500">Review students accepted into each organization for an academic year.</p>
            </div>
            <form method="GET" action="{{ route('admin.enrollment-requests') }}" class="w-full sm:max-w-xs">
                <label for="history-period" class="text-xs font-semibold text-slate-600">Academic year</label>
                <select id="history-period" name="history_period" onchange="this.form.submit()"
                    class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
                    @foreach ($academicPeriods as $academicPeriod)
                        <option value="{{ $academicPeriod->id }}" @selected($selectedHistoryPeriod?->id === $academicPeriod->id)>{{ $academicPeriod->label() }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($legacyAcceptedCount > 0)
            <div class="space-y-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
                <div>
                    <h3 class="font-bold text-amber-950">{{ $legacyAcceptedCount }} accepted {{ \Illuminate\Support\Str::plural('student', $legacyAcceptedCount) }} need history details</h3>
                    <p class="mt-1 text-sm text-amber-900">Older approvals may not have an academic year or organization saved. Choose the missing details below to add them to the correct history list. Existing details will be kept.</p>
                </div>
                <div class="divide-y divide-amber-200 rounded-xl border border-amber-200 bg-white">
                    @foreach ($legacyAcceptedRequests as $legacyEnrollment)
                        @php
                            $hasRecordedOrganization = $organizations->contains('id', $legacyEnrollment->department_id);
                        @endphp
                        <div class="flex flex-col gap-4 p-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">{{ $legacyEnrollment->user->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $legacyEnrollment->user->email }}</p>
                                <p class="mt-1 text-xs text-slate-500">Accepted {{ $legacyEnrollment->accepted_at?->format('M j, Y') ?? 'date not recorded' }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.enrollment-history.record', $legacyEnrollment) }}" class="grid w-full gap-3 sm:grid-cols-2 lg:max-w-2xl" data-single-submit>
                                @csrf
                                <label class="text-xs font-semibold text-slate-600">
                                    @if ($legacyEnrollment->academicPeriod)
                                        Academic year (already recorded)
                                        <input type="hidden" name="academic_period_id" value="{{ $legacyEnrollment->academic_period_id }}">
                                        <span class="mt-1 block rounded-lg bg-slate-50 px-3 py-2.5 text-sm text-slate-700">{{ $legacyEnrollment->academicPeriod->label() }}</span>
                                    @else
                                        Academic year
                                        <select name="academic_period_id" required class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
                                            <option value="">Choose academic year</option>
                                            @foreach ($academicPeriods as $academicPeriod)
                                                <option value="{{ $academicPeriod->id }}" @selected(old('academic_period_id', $selectedHistoryPeriod?->id) == $academicPeriod->id)>{{ $academicPeriod->label() }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </label>
                                <label class="text-xs font-semibold text-slate-600">
                                    @if ($hasRecordedOrganization)
                                        Organization (already recorded)
                                        <input type="hidden" name="department_id" value="{{ $legacyEnrollment->department_id }}">
                                        <span class="mt-1 block rounded-lg bg-slate-50 px-3 py-2.5 text-sm text-slate-700">{{ $legacyEnrollment->department->name }} ({{ $legacyEnrollment->department->code }})</span>
                                    @else
                                        Organization
                                        <select name="department_id" required class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
                                            <option value="">Choose organization</option>
                                            @foreach ($organizations as $organization)
                                                <option value="{{ $organization->id }}" @selected(old('department_id') == $organization->id)>{{ $organization->name }} ({{ $organization->code }})</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </label>
                                <div class="sm:col-span-2 sm:text-right">
                                    <button type="submit" @disabled($academicPeriods->isEmpty() || $organizations->isEmpty())
                                        class="w-full rounded-lg bg-ncc-maroon px-4 py-2.5 text-sm font-bold text-white transition hover:bg-ncc-maroonHover focus:outline-none focus:ring-2 focus:ring-ncc-maroon focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">
                                        Add to history
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
                @if ($academicPeriods->isEmpty() || $organizations->isEmpty())
                    <p class="text-sm text-amber-900">Add an academic year and seed the academic organizations before recording these students.</p>
                @endif
            </div>
        @endif

        @if (! $selectedHistoryPeriod)
            <p class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">Add an academic year to start recording accepted-student history.</p>
        @elseif ($organizations->isEmpty())
            <p class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">No academic organizations were found. Seed the five organizations to view accepted-student lists.</p>
        @else
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach ($organizations as $organization)
                    @php
                        $organizationAcceptedStudents = $acceptedStudentsByOrganization->get($organization->id, collect());
                        $organizationDialogId = 'accepted-students-' . $organization->id;
                    @endphp
                    <button type="button" data-organization-dialog="{{ $organizationDialogId }}"
                        aria-haspopup="dialog" aria-controls="{{ $organizationDialogId }}"
                        class="group flex w-full items-center justify-between gap-3 overflow-hidden rounded-2xl border border-slate-200 bg-white text-left transition hover:border-ncc-maroon/40 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-ncc-maroon focus:ring-offset-2">
                        <span class="flex min-w-0 flex-1 items-center justify-between gap-3 bg-slate-50 px-5 py-6">
                            <span class="min-w-0">
                                <span class="block truncate font-extrabold text-slate-900">{{ $organization->name }}</span>
                                <span class="mt-0.5 block text-xs font-semibold text-slate-500">{{ $organization->code }}</span>
                            </span>
                            <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-800">{{ $organizationAcceptedStudents->count() }} accepted</span>
                        </span>
                        <span class="sr-only">View accepted students</span>
                    </button>

                    <dialog id="{{ $organizationDialogId }}" aria-labelledby="{{ $organizationDialogId }}-title"
                        class="m-auto max-h-[85vh] w-[calc(100%-2rem)] max-w-xl overflow-hidden rounded-2xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/50">
                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
                            <div>
                                <h3 id="{{ $organizationDialogId }}-title" class="font-extrabold text-slate-900">{{ $organization->name }}</h3>
                                <p class="mt-0.5 text-xs font-semibold text-slate-500">{{ $organization->code }} · {{ $selectedHistoryPeriod->label() }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-800">{{ $organizationAcceptedStudents->count() }} accepted</span>
                                <form method="dialog">
                                    <button type="submit" aria-label="Close accepted students dialog" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-200 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-ncc-maroon">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if ($organizationAcceptedStudents->isEmpty())
                            <p class="px-5 py-8 text-center text-sm text-slate-500 sm:px-6">No students accepted for {{ $selectedHistoryPeriod->label() }}.</p>
                        @else
                            <ul class="max-h-[calc(85vh-5rem)] divide-y divide-slate-100 overflow-y-auto">
                                @foreach ($organizationAcceptedStudents as $acceptedEnrollment)
                                    <li class="flex flex-wrap items-center justify-between gap-2 px-5 py-4 sm:px-6">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $acceptedEnrollment->user->name }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $acceptedEnrollment->user->email }}</p>
                                        </div>
                                        <p class="text-xs text-slate-500">{{ $acceptedEnrollment->accepted_at?->format('M j, Y') ?? 'Date not recorded' }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </dialog>
                @endforeach
            </div>
        @endif
    </section>

    @if (session('warning'))
        <div role="status" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('error'))
        <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <section class="space-y-4" aria-labelledby="students-heading">
        <div>
            <h2 id="students-heading" class="text-xl font-extrabold text-slate-900">Students</h2>
            <p class="mt-1 text-sm text-slate-500">Open a student profile to review their application and course requests.</p>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Student Name</th>
                            <th class="px-5 py-4">Application Status</th>
                            <th class="px-5 py-4 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($students as $student)
                            @php
                                $studentApplicationStatus = $student->enrollmentRequest?->status;
                                [$studentStatusLabel, $studentStatusClass] = match ($studentApplicationStatus) {
                                    'pending' => ['Pending review', 'border-amber-200 bg-amber-50 text-amber-800'],
                                    'approved' => ['Confirmed', 'border-emerald-200 bg-emerald-50 text-emerald-800'],
                                    'rejected' => ['Rejected', 'border-red-200 bg-red-50 text-red-800'],
                                    default => ['No application', 'border-slate-200 bg-slate-50 text-slate-600'],
                                };
                            @endphp
                            <tr data-student-row="{{ $student->id }}">
                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $student->name }}</td>
                                <td class="px-5 py-4">
                                    <span aria-label="Application status: {{ $studentStatusLabel }}"
                                        class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-extrabold {{ $studentStatusClass }}">
                                        <span aria-hidden="true" class="h-2 w-2 rounded-full {{ $studentApplicationStatus === 'pending' ? 'animate-pulse bg-amber-500' : ($studentApplicationStatus === 'approved' ? 'bg-emerald-500' : ($studentApplicationStatus === 'rejected' ? 'bg-red-500' : 'bg-slate-400')) }}"></span>
                                        {{ $studentStatusLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button" onclick="document.getElementById('student-details-{{ $student->id }}').showModal()"
                                        class="rounded-lg border border-ncc-maroon/20 px-3 py-2 text-xs font-bold text-ncc-maroon transition hover:bg-ncc-maroon hover:text-white">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-slate-500">There are no student requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $students->links() }}</div>
            @endif
        </div>
    </section>

    @foreach ($students as $student)
        <dialog id="student-details-{{ $student->id }}" aria-labelledby="student-details-title-{{ $student->id }}"
            class="w-[min(48rem,calc(100%-2rem))] max-h-[90vh] rounded-3xl p-0 shadow-2xl backdrop:bg-slate-900/50">
            <div class="flex max-h-[90vh] flex-col">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-ncc-maroon">Student Details</p>
                        <h2 id="student-details-title-{{ $student->id }}" class="mt-1 text-xl font-extrabold text-slate-900">{{ $student->name }}</h2>
                    </div>
                    <button type="button" aria-label="Close student details" onclick="this.closest('dialog').close()"
                        class="rounded-lg px-3 py-2 text-sm font-bold text-slate-500 hover:bg-slate-100 hover:text-slate-900">Close</button>
                </div>

                <div class="space-y-6 overflow-y-auto px-6 py-5">
                    <section aria-labelledby="application-details-{{ $student->id }}">
                        <h3 id="application-details-{{ $student->id }}" class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Admission Application</h3>
                        @if ($student->enrollmentRequest)
                            @php
                                $enrollmentRequest = $student->enrollmentRequest;
                                [$applicationStatusLabel, $applicationStatusClass] = match ($enrollmentRequest->status) {
                                    'approved' => ['Confirmed', 'border-emerald-200 bg-emerald-50 text-emerald-800'],
                                    'rejected' => ['Rejected', 'border-red-200 bg-red-50 text-red-800'],
                                    default => ['Pending review', 'border-amber-200 bg-amber-50 text-amber-800'],
                                };
                            @endphp
                            <dl class="mt-3 grid gap-4 rounded-2xl bg-slate-50 p-4 sm:grid-cols-2">
                                <div><dt class="text-xs font-semibold text-slate-500">Email Address</dt><dd class="mt-1 break-all text-sm text-slate-900">{{ $student->email }}</dd></div>
                                <div><dt class="text-xs font-semibold text-slate-500">Contact Number</dt><dd class="mt-1 text-sm text-slate-900">{{ $enrollmentRequest->contact_number }}</dd></div>
                                <div><dt class="text-xs font-semibold text-slate-500">Student Type</dt><dd class="mt-1 text-sm text-slate-900">{{ $enrollmentRequest->studentTypeLabel() }}</dd></div>
                                <div><dt class="text-xs font-semibold text-slate-500">Application Status</dt><dd class="mt-1"><span class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-extrabold {{ $applicationStatusClass }}"><span aria-hidden="true" class="h-2 w-2 rounded-full {{ $enrollmentRequest->status === 'pending' ? 'animate-pulse bg-amber-500' : ($enrollmentRequest->status === 'approved' ? 'bg-emerald-500' : 'bg-red-500') }}"></span>{{ $applicationStatusLabel }}</span></dd></div>
                                <div><dt class="text-xs font-semibold text-slate-500">Submission Date</dt><dd class="mt-1 text-sm text-slate-900">{{ $enrollmentRequest->created_at->format('M j, Y g:i A') }}</dd></div>
                                @if ($enrollmentRequest->department && $enrollmentRequest->academicPeriod)
                                    <div><dt class="text-xs font-semibold text-slate-500">Accepted Organization</dt><dd class="mt-1 text-sm text-slate-900">{{ $enrollmentRequest->department->name }} ({{ $enrollmentRequest->department->code }})</dd></div>
                                    <div><dt class="text-xs font-semibold text-slate-500">Accepted Academic Year</dt><dd class="mt-1 text-sm text-slate-900">{{ $enrollmentRequest->academicPeriod->label() }}</dd></div>
                                    @if ($enrollmentRequest->accepted_at)
                                        <div><dt class="text-xs font-semibold text-slate-500">Accepted Date</dt><dd class="mt-1 text-sm text-slate-900">{{ $enrollmentRequest->accepted_at->format('M j, Y g:i A') }}</dd></div>
                                    @endif
                                @endif
                                <div><dt class="text-xs font-semibold text-slate-500">Email Verification</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->hasVerifiedEmail() ? 'Verified' : 'Not verified' }}</dd></div>
                                @if ($student->studentProfile)
                                    <div><dt class="text-xs font-semibold text-slate-500">Student Number</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->studentProfile->student_number }}</dd></div>
                                    <div><dt class="text-xs font-semibold text-slate-500">Program / Major</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->studentProfile->major }}</dd></div>
                                @endif
                            </dl>
                            @if ($enrollmentRequest->status === \App\Models\EnrollmentRequest::STATUS_PENDING)
                                <div class="mt-4 grid gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4 sm:grid-cols-2">
                                    <form method="POST" action="{{ route('admin.enrollment.accept', $enrollmentRequest) }}" class="contents" data-single-submit>
                                        @csrf
                                        <label class="text-xs font-semibold text-slate-600">
                                            Organization to accept into
                                            <select name="department_id" required class="mt-1 block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
                                                <option value="">Choose organization</option>
                                                @foreach ($organizations as $organization)
                                                    <option value="{{ $organization->id }}">{{ $organization->name }} ({{ $organization->code }})</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label class="text-xs font-semibold text-slate-600">
                                            Academic year
                                            <select name="academic_period_id" required class="mt-1 block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
                                                <option value="">Choose academic year</option>
                                                @foreach ($academicPeriods as $academicPeriod)
                                                    <option value="{{ $academicPeriod->id }}" @selected($academicPeriod->is_active)>{{ $academicPeriod->label() }}{{ $academicPeriod->is_active ? ' (Active)' : '' }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <div class="flex flex-wrap items-center gap-2 sm:col-span-2">
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">Accept Student</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.enrollment.reject', $enrollmentRequest) }}" data-single-submit>
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-50">Reject Application</button>
                                    </form>
                                        </div>
                                </div>
                            @endif
                        @else
                            <p class="mt-3 rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">No admission application has been submitted.</p>
                        @endif
                    </section>

                    <section aria-labelledby="course-requests-{{ $student->id }}">
                        <h3 id="course-requests-{{ $student->id }}" class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Course Enrollment Requests</h3>
                        <div class="mt-3 space-y-3">
                            @forelse ($student->courseEnrollmentRequests as $courseEnrollmentRequest)
                                @php
                                    $courseStatusClass = match ($courseEnrollmentRequest->status) {
                                        \App\Models\CourseEnrollmentRequest::STATUS_APPROVED => 'bg-emerald-100 text-emerald-800',
                                        \App\Models\CourseEnrollmentRequest::STATUS_REJECTED => 'bg-red-100 text-red-800',
                                        \App\Models\CourseEnrollmentRequest::STATUS_CANCELLED => 'bg-slate-100 text-slate-700',
                                        default => 'bg-amber-100 text-amber-800',
                                    };
                                @endphp
                                <article class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $courseEnrollmentRequest->course->code }} · {{ $courseEnrollmentRequest->course->title }}</p>
                                            <p class="mt-1 text-xs text-slate-500">Requested {{ $courseEnrollmentRequest->created_at->format('M j, Y g:i A') }}</p>
                                        </div>
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $courseStatusClass }}">{{ ucwords(str_replace('_', ' ', $courseEnrollmentRequest->status)) }}</span>
                                    </div>
                                    @if ($courseEnrollmentRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING)
                                        <p class="mt-3 text-sm font-semibold text-slate-800">Cancellation reason: {{ $courseEnrollmentRequest->cancellationReasonLabel() ?? 'Not provided' }}</p>
                                        @if ($courseEnrollmentRequest->cancellation_details)
                                            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $courseEnrollmentRequest->cancellation_details }}</p>
                                        @endif
                                    @endif
                                    @if ($courseEnrollmentRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_PENDING)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('admin.course-enrollment.approve', $courseEnrollmentRequest) }}" data-single-submit>
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.course-enrollment.reject', $courseEnrollmentRequest) }}" data-single-submit>
                                                @csrf
                                                <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-50">Reject</button>
                                            </form>
                                        </div>
                                    @elseif ($courseEnrollmentRequest->status === \App\Models\CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('admin.course-enrollment.approve-cancellation', $courseEnrollmentRequest) }}" data-single-submit>
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">Approve Cancellation</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.course-enrollment.reject-cancellation', $courseEnrollmentRequest) }}" data-single-submit>
                                                @csrf
                                                <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-50">Reject Cancellation</button>
                                            </form>
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">No course enrollment requests.</p>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </dialog>
    @endforeach
</section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('click', (event) => {
            const openButton = event.target.closest('[data-organization-dialog]');

            if (openButton) {
                document.getElementById(openButton.dataset.organizationDialog).showModal();
                return;
            }

            if (event.target instanceof HTMLDialogElement && event.target.open) {
                event.target.close();
            }
        });
    </script>
@endpush
