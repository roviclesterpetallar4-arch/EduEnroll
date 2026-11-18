<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Course Enrollment Portal') - Northeastern Cebu Colleges</title>
    <!-- Favicon / Seal -->
    <link rel="icon" type="image/png" href="{{ asset('images/ncc-logo.png') }}">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN with NCC Theme Colors -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            DEFAULT: '#002147',
                            light: '#003166',
                            dark: '#001530',
                            soft: '#F0F4FA',
                        },
                        gold: {
                            DEFAULT: '#FFB800',
                            light: '#FFCF47',
                            dark: '#CC9300',
                            soft: '#FFF8E6',
                        },
                        ncc: {
                            maroon: '#7B1113',
                            maroonHover: '#650D0F',
                            maroonDark: '#4A080A',
                            maroonLight: '#991B1F',
                            maroonSoft: '#FDF2F2',
                            gold: '#EFA31D',
                            goldHover: '#DA8F10',
                            goldDark: '#B87208',
                            goldSoft: '#FEF8EC',
                            goldBorder: '#F9E2B5',
                        }
                    },
                    boxShadow: {
                        card: '0 4px 24px 0 rgba(0,33,71,0.10)',
                        'card-hover': '0 8px 40px 0 rgba(0,33,71,0.18)',
                        glow: '0 0 0 4px rgba(255,184,0,0.23)',
                    }
                }
            }
        }
    </script>
    @stack('head')
    <style>
        @media (prefers-reduced-motion: no-preference) {
            html.motion-ready [data-reveal] {
                opacity: 0;
                filter: blur(4px);
                transform: translate3d(0, 2rem, 0) scale(.99);
                transition: opacity 600ms ease, transform 600ms cubic-bezier(.2, .7, .2, 1), filter 600ms ease;
                transition-delay: var(--reveal-delay, 0ms);
                will-change: opacity, transform;
            }

            html.motion-ready [data-reveal].is-visible {
                opacity: 1;
                filter: blur(0);
                transform: translate3d(0, 0, 0) scale(1);
                will-change: auto;
            }

            html.motion-ready [data-reveal].course-card.is-visible:hover,
            html.motion-ready [data-reveal].course-card.is-visible:focus-within {
                transform: translate3d(0, -0.375rem, 0) scale(1.01);
            }

            [data-scroll-exit] {
                view-timeline-name: --scroll-exit;
                view-timeline-axis: block;
            }

            @supports (animation-timeline: view()) {
                [data-scroll-exit] {
                    animation: scroll-exit-fade linear both;
                    animation-timeline: view();
                    animation-range: exit 0% exit 100%;
                }

                @keyframes scroll-exit-fade {
                    to {
                        opacity: 0;
                        transform: translate3d(0, -20px, 0) scale(.95);
                    }
                }
            }

        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto !important;
            }

            [data-reveal],
            [data-scroll-exit] {
                opacity: 1 !important;
                filter: none !important;
                transform: none !important;
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body class="bg-[#FAF8F5] text-slate-800 min-h-screen flex flex-col font-sans antialiased selection:bg-ncc-gold selection:text-ncc-maroonDark">

    <!-- Top Institution Maroon Navigation Header -->
    <header class="bg-ncc-maroon text-white border-b-4 border-ncc-gold shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- NCC Brand & Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3.5 group">
                    <img 
                        src="{{ asset('images/ncc-logo.png') }}" 
                        alt="Northeastern Cebu Colleges Seal" 
                        class="w-13 h-13 sm:w-14 sm:h-14 object-contain rounded-full shadow-sm ring-2 ring-ncc-gold/80 bg-white p-0.5 group-hover:scale-105 transition duration-200"
                    >
                    <div class="flex flex-col">
                        <span class="font-extrabold text-base sm:text-lg tracking-wide uppercase leading-tight text-white group-hover:text-ncc-gold transition">
                            Northeastern Cebu Colleges, Inc.
                        </span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] sm:text-xs text-amber-200 font-semibold tracking-wider uppercase">
                                Danao City, Cebu
                            </span>
                            <span class="text-amber-300 text-xs">•</span>
                            <span class="text-[11px] sm:text-xs text-amber-100/90 font-medium hidden sm:inline">
                                EduEnroll
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Navigation Links & Academic Year -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <span class="hidden md:inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-amber-200 border border-amber-300/30">
                        {{ $activeAcademicPeriod?->label() ?? 'Academic period not set' }}
                    </span>
                    <a 
                        href="{{ route('courses.index') }}" 
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold {{ request()->routeIs('courses.*') ? 'bg-ncc-gold text-ncc-maroonDark shadow' : 'text-white hover:bg-white/10' }} transition"
                    >
                        Course Catalog
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 text-xs font-bold text-white transition hover:bg-white/10 sm:text-sm">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-ncc-maroon transition hover:bg-amber-50 sm:text-sm">Register</a>
                    @else
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.enrollment-requests') }}" class="rounded-xl px-3 py-2 text-xs font-bold text-white transition hover:bg-white/10 sm:text-sm">Admin Dashboard</a>
                        @elseif (auth()->user()->hasVerifiedEmail())
                            <a href="{{ route('student.dashboard') }}" class="rounded-xl px-3 py-2 text-xs font-bold text-white transition hover:bg-white/10 sm:text-sm">Student Portal</a>
                        @else
                            <a href="{{ route('verification.notice') }}" class="rounded-xl px-3 py-2 text-xs font-bold text-white transition hover:bg-white/10 sm:text-sm">Verify Email</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" data-single-submit>
                            @csrf
                            <button type="submit" class="rounded-xl border border-white/25 px-3 py-2 text-xs font-bold text-white transition hover:border-ncc-gold hover:text-ncc-gold sm:text-sm">Sign Out</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 w-full">
        @if (session('success'))
            <div class="flash-alert mb-6 flex items-start p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm transition">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-bold text-emerald-900 text-sm">Success</h4>
                    <p class="text-xs sm:text-sm mt-0.5 text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="flash-alert mb-6 flex items-start p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 shadow-sm transition">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-bold text-red-900 text-sm">Enrollment Notice</h4>
                    <p class="text-xs sm:text-sm mt-0.5 text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="flash-alert mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm">
                <div class="flex items-center space-x-2 font-bold text-sm">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Please verify the submission:</span>
                </div>
                <ul class="list-disc list-inside mt-2 text-xs sm:text-sm text-amber-800 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="@yield('main-class', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex-grow w-full')">
        @yield('content')
    </main>

    <!-- Institution Footer -->
    <footer id="contact" class="bg-ncc-maroon text-white border-t-4 border-ncc-gold py-10 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-white/15">
                <div class="flex items-center space-x-4">
                    <img 
                        src="{{ asset('images/ncc-logo.png') }}" 
                        alt="Northeastern Cebu Colleges Seal" 
                        class="w-16 h-16 object-contain rounded-full bg-white p-1 shadow-md ring-2 ring-ncc-gold"
                    >
                    <div>
                        <h3 class="font-extrabold text-lg tracking-wide uppercase text-white">Northeastern Cebu Colleges, Inc.</h3>
                        <p class="text-xs text-amber-200 mt-0.5">P.G. Almendras St., Danao City, 6004 Cebu, Philippines</p>
                        <p class="text-[11px] text-amber-100/70 mt-0.5">Accredited Academic Programs &bull; Excellence &bull; Service &bull; Character</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-xs text-amber-100/80">
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg border border-amber-300/20">Education and Society Organization (EDSO)</span>
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg border border-amber-300/20">Information Technology Organization (ITO)</span>
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg border border-amber-300/20">Tourism Management Society (TMS)</span>
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg border border-amber-300/20">Hospitality Management Society (HMS)</span>
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg border border-amber-300/20">Junior Management Society (JMS)</span>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-amber-200/80 text-center sm:text-left">
                <p>&copy; {{ date('Y') }} Northeastern Cebu Colleges, Inc. &bull; Online Course Enrollment System</p>
            </div>
        </div>
    </footer>

    <script>
        document.querySelectorAll('form[data-single-submit]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.submitting === 'true') {
                    event.preventDefault();
                    return;
                }

                form.dataset.submitting = 'true';
                const button = form.querySelector('button[type="submit"]');

                if (button) {
                    button.disabled = true;
                    button.textContent = 'Please wait...';
                }
            });
        });

        window.addEventListener('pageshow', () => {
            document.querySelectorAll('form[data-clear-on-load]').forEach((form) => form.reset());
        });
    </script>
    <script>
        (() => {
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (reducedMotion || !('IntersectionObserver' in window)) {
                return;
            }

            const revealTargets = document.querySelectorAll(
                'main > *, main section, main article, main form, main .grid > *, main .program-card, main .course-card, main .admin-metric, main table tbody tr, .flash-alert, footer'
            );

            if (revealTargets.length === 0) {
                return;
            }

            document.documentElement.classList.add('motion-ready');

            const groupDelays = new Map();
            revealTargets.forEach((element) => {
                element.setAttribute('data-reveal', '');

                const group = element.parentElement;
                const index = groupDelays.get(group) ?? 0;
                element.style.setProperty('--reveal-delay', `${Math.min(index * 75, 225)}ms`);
                groupDelays.set(group, index + 1);
            });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    entry.target.classList.toggle('is-visible', entry.isIntersecting);
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -40px 0px',
            });

            revealTargets.forEach((element) => observer.observe(element));
        })();
    </script>
    @stack('scripts')
</body>
</html>
