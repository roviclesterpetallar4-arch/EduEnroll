@extends('layouts.app')

@section('title', 'Academic Course Catalog - Northeastern Cebu Colleges')

@push('head')
    <style>
        @media (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference) {
            .course-card {
                transform-style: preserve-3d;
            }

            .course-card.is-tilting:hover,
            html.motion-ready [data-reveal].course-card.is-visible.is-tilting:hover {
                transform: perspective(900px)
                    rotateX(var(--card-tilt-x, 0deg))
                    rotateY(var(--card-tilt-y, 0deg))
                    translate3d(0, -0.375rem, 0)
                    scale(1.01);
                transition: transform 120ms ease-out, border-color 300ms, box-shadow 300ms, background-color 300ms;
            }
        }
    </style>
@endpush

@section('content')
<div class="space-y-8">
    
    <!-- Hero School Banner Section -->
    <div data-scroll-exit class="relative bg-gradient-to-r from-ncc-maroon via-ncc-maroonDark to-ncc-maroon text-white rounded-3xl p-6 sm:p-10 shadow-lg border-2 border-ncc-gold overflow-hidden">
        <!-- Background Decorative Watermark/Pattern -->
        <div class="absolute right-0 top-0 bottom-0 opacity-10 pointer-events-none flex items-center pr-6">
            <img src="{{ asset('images/ncc-logo.png') }}" alt="NCC Seal Watermark" class="w-96 h-96 object-contain">
        </div>

        <a href="{{ route('home') }}"
           class="relative z-10 mb-6 inline-flex items-center gap-2 rounded-lg border border-white/30 px-3.5 py-2 text-sm font-semibold text-white transition hover:border-ncc-gold hover:text-ncc-gold focus:outline-none focus:ring-2 focus:ring-ncc-gold focus:ring-offset-2 focus:ring-offset-ncc-maroonDark">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Home
        </a>

        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-ncc-gold text-ncc-maroonDark shadow-sm">
                <img src="{{ asset('images/ncc-logo.png') }}" alt="Seal" class="w-4 h-4 object-contain rounded-full bg-white">
                <span>Northeastern Cebu Colleges &bull; Danao City, Cebu</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight text-white">
                Academic Course Catalog &amp; Enrollment
            </h1>
            
            <p class="text-sm sm:text-base text-amber-100/90 leading-relaxed">
                Official course offerings of <strong class="text-ncc-gold">Northeastern Cebu Colleges, Inc.</strong>:
                Explore all 9 collegiate courses across five academic organizations: Education and Society Organization (EDSO), Information Technology Organization (ITO), Tourism Management Society (TMS), Hospitality Management Society (HMS), and Junior Management Society (JMS).
            </p>

            <div class="pt-2 flex flex-wrap items-center gap-4 text-xs font-semibold text-amber-200">
                <span class="inline-flex items-center gap-1.5 bg-black/25 px-3 py-1.5 rounded-xl border border-amber-400/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Available Courses: {{ $courses->count() }} of 9
                </span>
                <span class="inline-flex items-center gap-1.5 bg-black/25 px-3 py-1.5 rounded-xl border border-amber-400/20">
                    Accredited Collegiate Curricula
                </span>
                <a href="{{ route('relationships.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/10 px-3 py-1.5 text-white transition hover:border-ncc-gold hover:text-ncc-gold">
                    Explore database relationships <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Department Filter Bar -->
    <form action="{{ route('courses.index') }}" method="GET" class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center">
        <!-- Search Input -->
        <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4 text-ncc-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search course title (e.g. Information Technology, English), code (e.g. ACT, BSIT), or chairperson..." 
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ncc-maroon focus:bg-white transition"
            >
        </div>

        <!-- Program/Department Filter -->
        <div class="md:w-96">
            <select 
                name="department" 
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-ncc-maroon focus:bg-white transition"
            >
                <option value="">All Academic Organizations / Courses (9 Courses)</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }} ({{ $dept->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter Submit Button -->
        <div class="flex items-center gap-2">
            <button 
                type="submit" 
                class="px-5 py-2.5 bg-ncc-maroon hover:bg-ncc-maroonHover text-white font-bold text-sm rounded-xl shadow-sm hover:shadow transition duration-150 inline-flex items-center justify-center gap-1.5"
            >
                <svg class="w-4 h-4 text-ncc-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Filter Courses</span>
            </button>

            @if (request()->filled('search') || request()->filled('department'))
                <a 
                    href="{{ route('courses.index') }}" 
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition duration-150 inline-flex items-center justify-center"
                    title="Clear filter criteria"
                >
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Courses Grid -->
    @if ($courses->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 bg-red-50 text-ncc-maroon rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No Courses Found</h3>
            <p class="text-sm text-slate-500 mt-1">No course matches your search query. Try removing filters or searching by a different term.</p>
            <div class="mt-5">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-4 py-2 bg-ncc-maroon text-white text-xs font-bold rounded-xl hover:bg-ncc-maroonHover transition shadow">
                    View Complete Catalog
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($courses as $course)
                <article
                    class="course-card group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300 ease-out hover:-translate-y-1.5 hover:scale-[1.01] hover:border-ncc-maroon/30 hover:shadow-xl hover:shadow-ncc-maroonDark/5 hover:bg-gradient-to-br hover:from-white hover:to-ncc-maroonSoft/40 focus-within:border-ncc-maroon/80 focus-within:ring-2 focus-within:ring-ncc-maroon/20 focus-within:shadow-xl focus-within:shadow-ncc-maroonDark/5 motion-reduce:transition-none motion-reduce:hover:transform-none motion-reduce:focus-within:transform-none {{ $selectedCourseId === $course->id ? 'border-ncc-maroon/80 ring-2 ring-ncc-maroon/20' : '' }}"
                    aria-labelledby="course-title-{{ $course->id }}"
                >
                    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-0.5 origin-left scale-x-0 bg-gradient-to-r from-ncc-maroon via-ncc-gold to-transparent transition-transform duration-300 ease-out group-hover:scale-x-100 group-focus-within:scale-x-100"></div>
                    <div class="p-6">
                        <!-- Top Meta Header -->
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full border border-ncc-maroon/15 bg-ncc-maroon/10 px-2.5 py-1 text-xs font-semibold tracking-wide text-ncc-maroon">
                                    {{ $course->code }}
                                </span>
                                @if ($selectedCourseId === $course->id)
                                    <span class="inline-flex items-center rounded-full bg-ncc-maroonSoft px-2.5 py-1 text-xs font-bold text-ncc-maroon">Your selection</span>
                                @endif
                            </div>

                            <!-- Status Pill Badge -->
                            <span role="status" aria-label="Open for enrollment" class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-emerald-200/80 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                <span class="relative flex h-2 w-2" aria-hidden="true">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60 motion-reduce:animate-none"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                </span>
                                Open for Enrollment
                            </span>
                        </div>

                        <!-- Course Title -->
                        <h2 id="course-title-{{ $course->id }}" class="text-lg font-bold leading-snug tracking-tight text-slate-900 transition-colors group-hover:text-ncc-maroonDark">
                            <a href="{{ route('courses.show', $course->id) }}" class="rounded-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ncc-maroon">
                                {{ $course->title }}
                            </a>
                        </h2>

                        <!-- Department Information -->
                        <div class="mt-2.5 flex items-center text-xs font-medium leading-5 text-slate-600">
                            <svg class="mr-1.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>{{ $course->department->name }}</span>
                        </div>

                        <p class="mt-1.5 text-xs font-medium text-slate-600">{{ $course->units }} units</p>

                        <!-- Instructor Field -->
                        @if ($course->instructor)
                            <div class="mt-1.5 flex items-center text-xs font-medium leading-5 text-slate-600">
                                <svg class="mr-1.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Program Chairperson: <strong class="font-semibold text-ncc-maroon">{{ $course->instructor }}</strong></span>
                            </div>
                        @endif

                        <!-- Description Snippet -->
                        <p class="mt-3 line-clamp-3 text-xs leading-relaxed text-slate-500">
                            {{ $course->description }}
                        </p>
                    </div>

                    <!-- Card Footer: Course Details Link -->
                    <div class="flex items-center justify-end border-t border-slate-100 bg-slate-50/80 px-6 py-3.5">
                        <a
                            href="{{ route('courses.show', $course->id) }}"
                            aria-label="View and enroll in {{ $course->title }}"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-ncc-maroon/10 bg-ncc-maroon/5 px-3 py-2 text-xs font-bold text-ncc-maroon transition-all duration-200 ease-in-out hover:border-ncc-maroon hover:bg-ncc-maroon hover:text-white hover:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ncc-maroon group-hover:border-ncc-maroon group-hover:bg-ncc-maroon group-hover:font-extrabold group-hover:text-white group-hover:shadow-sm"
                        >
                            <span>View &amp; Enroll</span>
                            <svg class="h-3.5 w-3.5 transition-transform duration-200 ease-in-out group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        (() => {
            const supportsTilt = window.matchMedia(
                '(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)'
            ).matches;

            if (!supportsTilt) {
                return;
            }

            document.querySelectorAll('.course-card').forEach((card) => {
                card.addEventListener('pointermove', (event) => {
                    if (event.pointerType === 'touch') {
                        return;
                    }

                    const bounds = card.getBoundingClientRect();
                    const x = (event.clientX - bounds.left) / bounds.width;
                    const y = (event.clientY - bounds.top) / bounds.height;
                    const rotateX = (0.5 - y) * 10;
                    const rotateY = (x - 0.5) * 10;

                    card.style.setProperty('--card-tilt-x', `${rotateX.toFixed(2)}deg`);
                    card.style.setProperty('--card-tilt-y', `${rotateY.toFixed(2)}deg`);
                    card.classList.add('is-tilting');
                });

                const resetTilt = () => {
                    card.classList.remove('is-tilting');
                    card.style.removeProperty('--card-tilt-x');
                    card.style.removeProperty('--card-tilt-y');
                };

                card.addEventListener('pointerleave', resetTilt);
                card.addEventListener('pointercancel', resetTilt);
            });
        })();
    </script>
@endpush
