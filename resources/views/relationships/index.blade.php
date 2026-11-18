@extends('layouts.app')

@section('title', 'Database Relationships Demonstration')

@section('content')
<div class="space-y-8">
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-ncc-maroonDark via-ncc-maroon to-[#8e2529] p-6 text-white shadow-xl sm:p-10">
        <div class="max-w-4xl">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-200">School Project Presentation</p>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Database Relationships Demonstration</h1>
            <p class="mt-4 max-w-3xl text-sm leading-7 text-white/80 sm:text-base">
                The Course Enrollment Portal lets students browse programs and request enrollment, while administrators review those requests. This project also demonstrates how students, profiles, departments, and courses are connected in a database.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-ncc-maroon transition hover:bg-amber-50">
                    Browse course catalog
                    <span aria-hidden="true">&rarr;</span>
                </a>
                <span class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white/90">Laravel · Eloquent · SQLite</span>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Database totals">
        @foreach ([
            ['label' => 'Students with profiles', 'value' => $metrics['students'], 'mark' => 'ST'],
            ['label' => 'Departments', 'value' => $metrics['departments'], 'mark' => 'DP'],
            ['label' => 'Academic programs', 'value' => $metrics['courses'], 'mark' => 'CR'],
            ['label' => 'Course enrollments', 'value' => $metrics['enrollments'], 'mark' => 'EN'],
        ] as $metric)
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">{{ $metric['label'] }}</p>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-ncc-maroonSoft text-xs font-extrabold text-ncc-maroon">{{ $metric['mark'] }}</span>
                </div>
                <p class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($metric['value']) }}</p>
                <p class="mt-1 text-xs text-slate-400">Live database records</p>
            </article>
        @endforeach
    </section>

    <section class="grid gap-4 xl:grid-cols-2" aria-label="Relationship examples">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-ncc-maroon">Example 01 · One-to-One</p>
                    <h2 class="mt-2 text-xl font-extrabold text-slate-900">Student &amp; profile</h2>
                </div>
                <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700">1 : 1</span>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-500"><strong class="text-slate-700">Meaning:</strong> One student has one profile. <strong class="text-slate-700">Why:</strong> Login details stay separate from school details like student number and year level.</p>
            <div class="mt-5 overflow-hidden rounded-xl border border-slate-100">
                <div class="grid grid-cols-[1.2fr_1fr_1fr] bg-slate-50 px-4 py-2.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                    <span>Student</span><span>Student no.</span><span>Program</span>
                </div>
                @forelse ($studentProfiles->take(4) as $profile)
                    <div class="grid grid-cols-[1.2fr_1fr_1fr] gap-2 border-t border-slate-100 px-4 py-3 text-xs">
                        <span class="truncate font-semibold text-slate-800">{{ $profile->user->name }}</span>
                        <span class="truncate text-slate-500">{{ $profile->student_number }}</span>
                        <span class="truncate text-slate-500">{{ $profile->course?->code ?? 'Not assigned' }}</span>
                    </div>
                @empty
                    <p class="px-4 py-5 text-sm text-slate-500">No student profiles are available yet. Run the database seeder to load demonstration data.</p>
                @endforelse
            </div>
            <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 font-mono text-xs text-slate-600">User::hasOne(StudentProfile::class)</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-ncc-maroon">Example 02 · One-to-Many</p>
                    <h2 class="mt-2 text-xl font-extrabold text-slate-900">Department &amp; programs</h2>
                </div>
                <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">1 : N</span>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-500"><strong class="text-slate-700">Meaning:</strong> One department can have many programs. <strong class="text-slate-700">Why:</strong> Programs are grouped under the organization responsible for them.</p>
            <div class="mt-5 space-y-3">
                @forelse ($departments->take(4) as $department)
                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $department->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $department->code }} · {{ $department->courses_count }} programs</p>
                            </div>
                            <div class="flex shrink-0 -space-x-2">
                                @foreach ($department->courses->take(3) as $course)
                                    <span title="{{ $course->title }}" class="grid h-8 min-w-8 place-items-center rounded-full border-2 border-white bg-ncc-maroonSoft px-1.5 text-[10px] font-extrabold text-ncc-maroon">{{ $course->code }}</span>
                                @endforeach
                            </div>
                        </div>
                        <p class="mt-2 truncate text-xs text-slate-500">{{ $department->courses->pluck('title')->join(' · ') ?: 'No programs assigned' }}</p>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">No departments are available.</p>
                @endforelse
            </div>
            <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 font-mono text-xs text-slate-600">Department::hasMany(Course::class)</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-ncc-maroon">Example 03 · Many-to-Many</p>
                    <h2 class="mt-2 text-xl font-extrabold text-slate-900">Students &amp; courses</h2>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">N : N</span>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-500"><strong class="text-slate-700">Meaning:</strong> The database connects students and courses through <code class="rounded bg-slate-100 px-1">course_user</code>. <strong class="text-slate-700">Why:</strong> It records which students have course enrollments. The portal limits each student to one active course at a time.</p>
            <div class="mt-5 space-y-3">
                @php($shownCourses = $departments->flatMap->courses->take(4))
                @forelse ($shownCourses as $course)
                    <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 p-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $course->title }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $course->code }} · {{ $course->department->code }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">{{ $course->students->count() }} students</span>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">No courses are available.</p>
                @endforelse
            </div>
            <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 font-mono text-xs text-slate-600">User::belongsToMany(Course::class, 'course_user')</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-ncc-maroon">Example 04 · Has-Many-Through</p>
                    <h2 class="mt-2 text-xl font-extrabold text-slate-900">Department &amp; students</h2>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Advanced</span>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-500"><strong class="text-slate-700">Meaning:</strong> A department can find student profiles through its programs. <strong class="text-slate-700">Why:</strong> This lets the system show the students connected to a department without manually searching each program.</p>
            <div class="mt-5 space-y-3">
                @forelse ($departments->take(4) as $department)
                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $department->name }}</p>
                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $department->students->count() }} students</span>
                        </div>
                        <p class="mt-2 truncate text-xs text-slate-500">{{ $department->students->take(3)->map(fn ($profile) => $profile->user->name)->join(' · ') ?: 'No student profiles assigned' }}</p>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">No departments are available.</p>
                @endforelse
            </div>
            <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 font-mono text-xs text-slate-600">Department::hasManyThrough(StudentProfile::class, Course::class)</p>
        </article>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-ncc-maroon">Project guide</p>
            <h2 class="mt-2 text-xl font-extrabold text-slate-900">What each part does</h2>
            <p class="mt-1 text-sm text-slate-500">Think of these as different tools, each with one clear job.</p>
        </div>
        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['title' => 'Models', 'text' => 'Represent database tables. Their relationship methods explain how records connect.'],
                ['title' => 'Migrations', 'text' => 'Create and update tables, columns, and foreign keys in a repeatable way.'],
                ['title' => 'Controllers', 'text' => 'Get the data a page needs and send it to the view.'],
                ['title' => 'Factories', 'text' => 'Make realistic sample records quickly for tests and development.'],
                ['title' => 'Seeders', 'text' => 'Fill the database with ready-to-show sample departments, programs, and students.'],
                ['title' => 'Public images', 'text' => 'Store pictures and logos that visitors can see on the website.'],
                ['title' => 'Views', 'text' => 'Show the data as web pages using Blade and HTML.'],
                ['title' => 'Routes', 'text' => 'Connect a web address, such as /relationships, to the right controller.'],
            ] as $step)
                <article class="rounded-2xl bg-slate-50 p-4">
                    <h3 class="font-bold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">{{ $step['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
