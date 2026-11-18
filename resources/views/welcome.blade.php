@extends('layouts.app')

@section('title', 'Home')
@section('main-class', 'w-full flex-grow')

@push('head')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
        [x-cloak] { display: none !important; }

        /* Hero gradient overlay */
        .hero-overlay {
            background: linear-gradient(
                to right,
                rgba(0, 21, 48, 0.90) 0%,
                rgba(0, 33, 71, 0.72) 55%,
                rgba(0, 33, 71, 0.25) 100%
            );
        }

        .hero-slide-transition {
            transition: opacity 1s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 1.2s cubic-bezier(0.22, 1, 0.36, 1);
            will-change: opacity, transform;
        }
        .hero-slide-enter-start,
        .hero-slide-leave-end {
            opacity: 0;
            transform: scale(1.035);
        }
        .hero-slide-enter-end,
        .hero-slide-leave-start {
            opacity: 1;
            transform: scale(1);
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-slide-transition {
                transition-duration: 0.01ms;
                will-change: auto;
            }
        }

        /* Program card hover */
        .program-card {
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .program-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 48px 0 rgba(0,33,71,0.16);
        }

</style>
@endpush

@section('content')
<!-- ══════════════════════════════════════════════════════════
     HERO SECTION — Campus Slideshow
══════════════════════════════════════════════════════════ -->
<section
    id="hero"
    data-scroll-exit
    class="relative min-h-[88vh] flex items-center overflow-hidden bg-[#001225]"
    x-data="{
        current: 0,
        slides: [
            { src: '{{ asset('images/ncc-campus.jpg') }}', alt: 'Northeastern Cebu Colleges campus' },
            { src: '{{ asset('images/ncc.jpg') }}', alt: 'Northeastern Cebu Colleges graduates' }
        ],
        timer: null,
        startAutoplay() {
            clearInterval(this.timer);
            this.timer = setInterval(() => {
                this.current = (this.current + 1) % this.slides.length;
            }, 4000);
        },
        goTo(index) {
            this.current = index;
            this.startAutoplay();
        }
    }"
    x-init="startAutoplay()"
    aria-label="Northeastern Cebu Colleges photo slideshow"
>
    <template x-for="(slide, index) in slides" :key="slide.src">
        <img
            x-cloak
            x-show="current === index"
            x-transition:enter="hero-slide-transition"
            x-transition:enter-start="hero-slide-enter-start"
            x-transition:enter-end="hero-slide-enter-end"
            x-transition:leave="hero-slide-transition"
            x-transition:leave-start="hero-slide-leave-start"
            x-transition:leave-end="hero-slide-leave-end"
            :src="slide.src"
            :alt="slide.alt"
            class="absolute inset-0 h-full w-full object-cover object-center"
            :aria-hidden="current !== index"
        >
    </template>

    <!-- Dark Gradient Overlay -->
    <div class="absolute inset-0 hero-overlay pointer-events-none"></div>

    <!-- Gold Top Accent -->
    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#FFB800] via-[#FFCF47] to-[#FFB800] opacity-90 pointer-events-none"></div>

    <!-- Hero Content -->
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28">
        <div class="max-w-2xl">

            <div class="inline-flex items-center gap-2 mb-7 px-4 py-1.5 rounded-full border border-white/25 bg-white/10 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-[#FFB800]"></span>
                <span class="text-white text-[11px] font-bold uppercase tracking-widest">Northeastern Cebu Colleges · Danao City</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-[58px] font-bold text-white leading-[1.12]">
                Your future<br>
                starts with a<br>
                <span class="text-[#FFB800]">strong foundation.</span>
            </h1>

            <p class="mt-7 text-white/80 text-[15px] sm:text-base leading-relaxed max-w-xl">
                Explore academic programs at Northeastern Cebu Colleges and find a course that supports your goals.
            </p>

            <div class="mt-9 flex flex-wrap gap-4">
                <a href="{{ route('courses.index') }}"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-[#FFB800] text-[#002147] font-bold text-sm shadow-lg hover:bg-[#FFCF47] transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Browse Courses
                </a>
                <a href="#about"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl border border-white/45 text-white font-semibold text-sm hover:bg-white/10 transition-colors duration-200">
                    About the College
                </a>
            </div>

            <div class="mt-14 flex flex-wrap items-center gap-8">
                <div>
                    <p class="text-3xl font-bold text-white leading-none">9</p>
                    <p class="text-white/65 text-xs font-medium mt-2">Academic Programs</p>
                </div>
                <div class="w-px h-10 bg-white/15"></div>
                <div>
                    <p class="text-3xl font-bold text-white leading-none">5</p>
                    <p class="text-white/65 text-xs font-medium mt-2">Academic Organizations</p>
                </div>
                <div class="w-px h-10 bg-white/15"></div>
                <div>
                    <p class="text-lg font-bold text-white leading-none">Danao City</p>
                    <p class="text-white/65 text-xs font-medium mt-2">Cebu, Philippines</p>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-7 left-1/2 z-20 flex -translate-x-1/2 items-center gap-3"
         role="group" aria-label="Slideshow navigation">
        <template x-for="(slide, index) in slides" :key="slide.src">
            <button
                type="button"
                @click="goTo(index)"
                :aria-label="`Show slide ${index + 1}: ${slide.alt}`"
                :aria-current="current === index ? 'true' : 'false'"
                :class="current === index
                    ? 'w-9 bg-[#FFB800] shadow-[0_0_8px_2px_rgba(255,184,0,0.50)]'
                    : 'w-3 bg-white/45 hover:bg-white/75'"
                class="h-3 rounded-full border border-white/20 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#FFB800] focus:ring-offset-2 focus:ring-offset-[#001530]"
            ></button>
        </template>
    </div>
</section>


<!-- ══════════════════════════════════════════════════════════
     ACADEMIC PROGRAMS SECTION — 3 Column Cards
══════════════════════════════════════════════════════════ -->
<section id="programs" class="py-24 bg-[#FAF8F5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="text-center mb-16">
            <span class="inline-block mb-3 px-4 py-1.5 rounded-full bg-[#FFB800]/12 text-[#CC9300] text-xs font-bold uppercase tracking-widest border border-[#FFB800]/30">
                Academic Programs
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#002147]">
                Explore Our Degree Programs
            </h2>
            <p class="mt-4 text-slate-500 max-w-xl mx-auto text-[15px] leading-relaxed">
                Northeastern Cebu Colleges offers quality, accredited programs across multiple disciplines
                designed to equip students for the professional world.
            </p>
            <div class="mt-5 mx-auto w-14 h-1.5 rounded-full bg-[#FFB800]"></div>
        </div>

        <!-- 3-Column Program Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- ── Card 1: Information Technology ── -->
            <div class="program-card bg-white rounded-3xl p-8 shadow-card border border-[#002147]/6 cursor-pointer group">
                <!-- Icon -->
                <div class="w-14 h-14 rounded-2xl bg-[#002147] flex items-center justify-center mb-6 shadow-sm group-hover:bg-[#FFB800] transition-colors duration-300">
                    <svg class="w-7 h-7 text-[#FFB800] group-hover:text-[#002147] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <!-- Org Tag -->
                <span class="inline-block mb-3 px-3 py-1 rounded-full bg-[#002147]/7 border border-[#002147]/12 text-[#002147] text-[11px] font-bold uppercase tracking-wider">ITO</span>
                <!-- Title -->
                <h3 class="text-[18px] font-extrabold text-[#002147] mb-2 leading-tight">
                    Information Technology Organization
                </h3>
                <p class="text-slate-400 text-sm mb-6 leading-relaxed">
                    Cutting-edge technology programs preparing students for the dynamic IT and computing industry.
                </p>
                <!-- Course List -->
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-3 p-3 rounded-xl bg-[#F0F4FA] border border-[#002147]/8 hover:border-[#FFB800]/50 transition">
                        <span class="w-8 h-8 rounded-lg bg-[#FFB800] flex items-center justify-center text-[#002147] font-extrabold text-xs shrink-0">IT</span>
                        <div>
                            <p class="text-sm font-bold text-[#002147]">BSIT</p>
                            <p class="text-[11px] text-slate-400">BS in Information Technology</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-3 p-3 rounded-xl bg-[#F0F4FA] border border-[#002147]/8 hover:border-[#FFB800]/50 transition">
                        <span class="w-8 h-8 rounded-lg bg-[#FFCF47] flex items-center justify-center text-[#002147] font-extrabold text-xs shrink-0">AC</span>
                        <div>
                            <p class="text-sm font-bold text-[#002147]">ACT</p>
                            <p class="text-[11px] text-slate-400">Associate in Computer Technology</p>
                        </div>
                    </li>
                </ul>
                <a href="{{ route('courses.index') }}"
                   class="mt-6 inline-flex items-center gap-1.5 text-[#FFB800] font-bold text-sm hover:gap-3 transition-all duration-200">
                    View Courses
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- ── Card 2: Business & Hospitality ── -->
            <div class="program-card bg-white rounded-3xl p-8 shadow-card border border-[#002147]/6 cursor-pointer group">
                <div class="w-14 h-14 rounded-2xl bg-[#002147] flex items-center justify-center mb-6 shadow-sm group-hover:bg-[#FFB800] transition-colors duration-300">
                    <svg class="w-7 h-7 text-[#FFB800] group-hover:text-[#002147] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="inline-block mb-3 px-3 py-1 rounded-full bg-[#002147]/7 border border-[#002147]/12 text-[#002147] text-[11px] font-bold uppercase tracking-wider">JMS · HMS · TMS</span>
                <h3 class="text-[18px] font-extrabold text-[#002147] mb-2 leading-tight">
                    College of Business &amp; Hospitality
                </h3>
                <p class="text-slate-400 text-sm mb-6 leading-relaxed">
                    Business administration, hospitality management, and tourism programs for future industry leaders.
                </p>
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-3 p-3 rounded-xl bg-[#F0F4FA] border border-[#002147]/8 hover:border-[#FFB800]/50 transition">
                        <span class="w-8 h-8 rounded-lg bg-[#FFB800] flex items-center justify-center text-[#002147] font-extrabold text-xs shrink-0">JMS</span>
                        <div>
                            <p class="text-sm font-bold text-[#002147]">BSBA</p>
                            <p class="text-[11px] text-slate-400">BS in Business Administration</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-3 p-3 rounded-xl bg-[#F0F4FA] border border-[#002147]/8 hover:border-[#FFB800]/50 transition">
                        <span class="w-8 h-8 rounded-lg bg-[#FFCF47] flex items-center justify-center text-[#002147] font-extrabold text-xs shrink-0">HMS</span>
                        <div>
                            <p class="text-sm font-bold text-[#002147]">BSHM</p>
                            <p class="text-[11px] text-slate-400">BS in Hospitality Management</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-3 p-3 rounded-xl bg-[#F0F4FA] border border-[#002147]/8 hover:border-[#FFB800]/50 transition">
                        <span class="w-8 h-8 rounded-lg bg-[#FFB800]/50 flex items-center justify-center text-[#002147] font-extrabold text-xs shrink-0">TMS</span>
                        <div>
                            <p class="text-sm font-bold text-[#002147]">BSTM</p>
                            <p class="text-[11px] text-slate-400">BS in Tourism Management</p>
                        </div>
                    </li>
                </ul>
                <a href="{{ route('courses.index') }}"
                   class="mt-6 inline-flex items-center gap-1.5 text-[#FFB800] font-bold text-sm hover:gap-3 transition-all duration-200">
                    View Courses
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- ── Card 3: Teacher Education ── -->
            <div class="program-card bg-white rounded-3xl p-8 shadow-card border border-[#002147]/6 cursor-pointer group">
                <div class="w-14 h-14 rounded-2xl bg-[#002147] flex items-center justify-center mb-6 shadow-sm group-hover:bg-[#FFB800] transition-colors duration-300">
                    <svg class="w-7 h-7 text-[#FFB800] group-hover:text-[#002147] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                              d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                </div>
                <span class="inline-block mb-3 px-3 py-1 rounded-full bg-[#002147]/7 border border-[#002147]/12 text-[#002147] text-[11px] font-bold uppercase tracking-wider">EDSO</span>
                <h3 class="text-[18px] font-extrabold text-[#002147] mb-2 leading-tight">
                    Education and Society Organization
                </h3>
                <p class="text-slate-400 text-sm mb-6 leading-relaxed">
                    Education programs developing passionate, skilled teachers ready to shape the next generation.
                </p>
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-3 p-3 rounded-xl bg-[#F0F4FA] border border-[#002147]/8 hover:border-[#FFB800]/50 transition">
                        <span class="w-8 h-8 rounded-lg bg-[#FFB800] flex items-center justify-center text-[#002147] font-extrabold text-xs shrink-0">EE</span>
                        <div>
                            <p class="text-sm font-bold text-[#002147]">BEEd</p>
                            <p class="text-[11px] text-slate-400">Bachelor of Elementary Education</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-3 p-3 rounded-xl bg-[#F0F4FA] border border-[#002147]/8 hover:border-[#FFB800]/50 transition">
                        <span class="w-8 h-8 rounded-lg bg-[#FFCF47] flex items-center justify-center text-[#002147] font-extrabold text-[10px] shrink-0">SE</span>
                        <div>
                            <p class="text-sm font-bold text-[#002147]">BSEd</p>
                            <p class="text-[11px] text-slate-400">BS Secondary Ed (ENG / FIL / MATH)</p>
                        </div>
                    </li>
                </ul>
                <a href="{{ route('courses.index') }}"
                   class="mt-6 inline-flex items-center gap-1.5 text-[#FFB800] font-bold text-sm hover:gap-3 transition-all duration-200">
                    View Courses
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

        </div>

        <!-- View All CTA -->
        <div class="text-center mt-14">
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-2.5 px-9 py-4 rounded-2xl bg-[#002147] text-white font-bold text-sm shadow-card hover:bg-[#003166] hover:shadow-card-hover transition-all duration-300">
                <svg class="w-5 h-5 text-[#FFB800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                View All 9 Courses in EduEnroll
            </a>
        </div>
    </div>
</section>


<!-- ══════════════════════════════════════════════════════════
     ABOUT NCC SECTION
══════════════════════════════════════════════════════════ -->
<section id="about" class="py-24 bg-[#FAF8F5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            <!-- Left Text -->
            <div>
                <span class="inline-block mb-3 px-4 py-1.5 rounded-full bg-[#FFB800]/12 text-[#CC9300] text-xs font-bold uppercase tracking-widest border border-[#FFB800]/30">
                    About NCCI
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#002147] leading-tight">
                    A Legacy of Excellence<br>in Cebu, Philippines
                </h2>
                <p class="mt-6 text-slate-500 text-[15px] leading-relaxed">
                    Northeastern Cebu Colleges, Inc. (NCCI) is a private higher education institution located in
                    <strong class="text-[#002147]">Danao City, Cebu, Philippines</strong>. We are committed to providing
                    quality, affordable, and accessible education to the youth of Northeastern Cebu.
                </p>
                <p class="mt-4 text-slate-500 text-[15px] leading-relaxed">
                    Our institution offers diverse academic programs across technology, business, hospitality,
                    tourism, and education — all aligned with industry standards and regional development needs.
                </p>
                <!-- Values -->
                <div class="mt-8 grid grid-cols-3 gap-4">
                    <div class="text-center p-4 rounded-2xl bg-[#F0F4FA] border border-[#002147]/8">
                        <p class="text-base font-extrabold text-[#002147]">Excellence</p>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">Academic Standard</p>
                    </div>
                    <div class="text-center p-4 rounded-2xl bg-[#FFF8E6] border border-[#FFB800]/25">
                        <p class="text-base font-extrabold text-[#CC9300]">Service</p>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">Community First</p>
                    </div>
                    <div class="text-center p-4 rounded-2xl bg-[#F0F4FA] border border-[#002147]/8">
                        <p class="text-base font-extrabold text-[#002147]">Character</p>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">Integrity &amp; Values</p>
                    </div>
                </div>
            </div>

            <!-- Right Visual Card -->
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-card-hover border-4 border-white ring-1 ring-[#002147]/10">
                    <img src="{{ asset('images/ncc-campus.jpg') }}"
                         alt="NCC Campus"
                         class="w-full h-72 object-cover">
                    <div class="bg-[#002147] p-6">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/ncc-logo.png') }}"
                                 alt="NCC"
                                 class="w-12 h-12 rounded-full object-contain bg-white p-1 ring-2 ring-[#FFB800]">
                            <div>
                                <h4 class="text-white font-extrabold text-sm">Northeastern Cebu Colleges, Inc.</h4>
                                <p class="text-white/45 text-xs mt-0.5">P.G. Almendras St., Danao City, 6004 Cebu</p>
                            </div>
                        </div>
                        <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                            <div class="bg-white/8 rounded-xl py-3 border border-white/10">
                                <p class="text-[#FFB800] font-extrabold text-xl leading-none">9</p>
                                <p class="text-white/45 text-[10px] mt-1">Programs</p>
                            </div>
                            <div class="bg-white/8 rounded-xl py-3 border border-white/10">
                                <p class="text-[#FFB800] font-extrabold text-xl leading-none">5</p>
                                <p class="text-white/45 text-[10px] mt-1">Organizations</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Floating Gold Badge -->
                <div class="absolute -top-5 -right-5 bg-[#FFB800] rounded-2xl px-4 py-2.5 shadow-lg border-2 border-white">
                    <p class="text-[#002147] font-extrabold text-xs uppercase tracking-wide">Est. Danao City</p>
                    <p class="text-[#002147]/60 text-[10px] mt-0.5">Cebu, Philippines 🇵🇭</p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ══════════════════════════════════════════════════════════
     FULL-WIDTH CTA BANNER
══════════════════════════════════════════════════════════ -->
<section class="relative py-24 bg-[#002147] overflow-hidden">
    <!-- Background decorative blobs -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#FFB800]/6 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#FFB800]/6 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block mb-5 px-4 py-1.5 rounded-full bg-[#FFB800]/14 text-[#FFB800] text-xs font-bold uppercase tracking-widest border border-[#FFB800]/30">
            Academic Programs at NCCI
        </span>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight">
            Find the program that<br class="hidden sm:block"> moves you forward.
        </h2>
        <p class="mt-6 text-white/55 text-[15px] max-w-xl mx-auto leading-relaxed">
            Join students at Northeastern Cebu Colleges. Browse programs, enroll in courses,
            and begin your journey toward a brighter tomorrow.
        </p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-[#FFB800] text-[#002147] font-extrabold text-sm shadow-lg hover:bg-[#FFCF47] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Enroll Now
            </a>
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl border-2 border-white/22 text-white font-bold text-sm hover:border-[#FFB800] hover:text-[#FFB800] transition-all duration-200">
                Browse Course Catalog
            </a>
        </div>
    </div>
</section>
@endsection
