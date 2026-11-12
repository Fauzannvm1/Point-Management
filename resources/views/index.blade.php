<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.text-size-head')
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Point Management System</title>
    <meta name="description" content="A modern web-based system for managing student points, tracking task completion, and generating performance reports for schools." />
    <link rel="icon" type="image/png" href="{{ asset('storage_backup/Icon.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Inter (modern + clean) + Sora (display) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Sora', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        /* Sky Blue Palette — the primary color system */
                        primary: {
                            50:  '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',  /* Primary action */
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        /* Accent — warm amber for highlights */
                        accent: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                        },
                    },
                },
            },
        }
    </script>

    <style>
        /* ── Base Typography ── */
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Sora', sans-serif; }

        /* ── User-select control ── */
        body { user-select: none; -webkit-user-select: none; }
        input, textarea, select { user-select: text; -webkit-user-select: text; }

        /* ── Scroll-triggered fade-in ── */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Floating animation ── */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%      { transform: translateY(-14px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }

        /* ── Subtle glow pulse on hero image ── */
        @keyframes glowPulse {
            0%, 100% { opacity: 0.35; transform: scale(0.96); }
            50%      { opacity: 0.55; transform: scale(1.02); }
        }
        .glow-pulse { animation: glowPulse 4s ease-in-out infinite; }

        /* ── Gradient border for feature cards ── */
        .feature-card {
            position: relative;
            background: white;
            transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -12px rgba(14, 165, 233, 0.15);
        }

        /* ── Timeline vertical connector ── */
        .timeline-line {
            position: absolute;
            left: 23px;
            top: 48px;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #38bdf8, #bae6fd, transparent);
        }

        /* ── Mobile hamburger menu ── */
        .mobile-menu { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .mobile-menu.open { max-height: 300px; }

        /* ── Smooth scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f0f9ff; }
        ::-webkit-scrollbar-thumb { background: #7dd3fc; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #38bdf8; }
    </style>
</head>
<body class="bg-[#f8faff] text-slate-700 antialiased">


    <!-- ════════════════════════════════════════════
         NAVBAR — Glassmorphism sticky header
         ════════════════════════════════════════════ -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between h-16 md:h-[72px]">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ asset('storage_backup/Icon.png') }}" alt="PointMS" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform">
                    <span class="font-display font-bold text-slate-800 text-lg hidden sm:block">Point<span class="text-primary-500">MS</span></span>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="#features" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition-all duration-200">Features</a>
                    <a href="#how-it-works" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition-all duration-200">How It Works</a>
                    <a href="#tutorial" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition-all duration-200">Tutorial</a>
                </div>

                <!-- CTA + Mobile Toggle -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/login') }}"
                       class="bg-primary-500 hover:bg-primary-600 active:scale-95 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-primary-200/50 hover:shadow-primary-300/50 transition-all duration-200">
                        Login
                    </a>
                    <!-- Mobile hamburger -->
                    <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-primary-50 transition-colors" aria-label="Toggle menu">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div id="mobileMenu" class="mobile-menu md:hidden pb-4">
                <div class="flex flex-col gap-1 pt-2 border-t border-primary-100">
                    <a href="#features" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition-all">Features</a>
                    <a href="#how-it-works" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition-all">How It Works</a>
                    <a href="#tutorial" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition-all">Tutorial</a>
                </div>
            </div>
        </div>
    </nav>


    <!-- ════════════════════════════════════════════
         HERO SECTION
         ════════════════════════════════════════════ -->
    <section class="relative overflow-hidden pt-28 md:pt-36 pb-24 md:pb-32">
        <!-- Background gradient + dot pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-50 via-white to-blue-50"></div>
        <div class="absolute inset-0 opacity-[0.35]" style="background-image: radial-gradient(circle, #bae6fd 1px, transparent 1px); background-size: 32px 32px;"></div>

        <!-- Decorative blobs -->
        <div class="absolute -top-24 -left-24 w-[500px] h-[500px] bg-primary-200 rounded-full blur-[120px] opacity-30 pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-[400px] h-[400px] bg-blue-200 rounded-full blur-[100px] opacity-25 pointer-events-none"></div>
        <!-- Small floating orbs -->
        <div class="absolute top-1/4 right-1/4 w-3 h-3 bg-primary-300 rounded-full opacity-60 animate-float" style="animation-delay: -1s;"></div>
        <div class="absolute top-1/3 left-1/3 w-2 h-2 bg-primary-400 rounded-full opacity-40 animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute bottom-1/4 right-1/3 w-4 h-4 bg-accent-400 rounded-full opacity-30 animate-float" style="animation-delay: -2s;"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col-reverse lg:flex-row items-center gap-16">

                <!-- Hero Text -->
                <div class="flex-1 text-center lg:text-left reveal">
                    <!-- Badge -->
                    <span class="inline-flex items-center gap-2 bg-white/80 backdrop-blur text-primary-600 text-xs font-bold px-4 py-2 rounded-full mb-6 border border-primary-100 shadow-sm">
                        <span class="w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
                        School Project
                    </span>

                    <h1 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold text-slate-800 leading-[1.15] mb-6 tracking-tight">
                        Student Point<br/>
                        <span class="bg-gradient-to-r from-primary-500 to-primary-400 bg-clip-text text-transparent">Management</span> System
                    </h1>

                    <p class="text-slate-500 text-lg leading-relaxed max-w-xl mx-auto lg:mx-0 mb-10">
                        A web-based system designed to manage student points based on assigned tasks.
                        It helps teachers monitor performance and track task completion efficiently.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ url('/login') }}"
                           class="group bg-primary-500 hover:bg-primary-600 active:scale-[0.97] text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-primary-200/50 hover:shadow-primary-300/60 transition-all duration-200 text-base flex items-center justify-center gap-2">
                            Get Started
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                        <a href="#how-it-works"
                           class="bg-white hover:bg-primary-50 text-primary-600 font-bold px-8 py-4 rounded-2xl border-2 border-primary-100 hover:border-primary-200 transition-all duration-200 text-base text-center">
                            Learn More
                        </a>
                    </div>

                    <!-- Stats row -->
                    <div class="flex gap-8 mt-12 justify-center lg:justify-start">
                        <div class="text-center">
                            <p class="text-2xl font-extrabold bg-gradient-to-r from-primary-500 to-primary-400 bg-clip-text text-transparent">100%</p>
                            <p class="text-xs text-slate-400 font-semibold mt-1">Web-based</p>
                        </div>
                        <div class="w-px bg-primary-100"></div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold bg-gradient-to-r from-primary-500 to-primary-400 bg-clip-text text-transparent">Easy</p>
                            <p class="text-xs text-slate-400 font-semibold mt-1">To Use</p>
                        </div>
                        <div class="w-px bg-primary-100"></div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold bg-gradient-to-r from-primary-500 to-primary-400 bg-clip-text text-transparent">Real-time</p>
                            <p class="text-xs text-slate-400 font-semibold mt-1">Tracking</p>
                        </div>
                    </div>
                </div>

                <!-- Hero Image Carousel -->
                <div class="flex-1 w-full max-w-lg mx-auto animate-float reveal" style="transition-delay: 0.15s;">
                    <div class="relative">
                        <!-- Glow ring -->
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-primary-300 to-blue-300 blur-2xl glow-pulse"></div>
                        <!-- Dashboard preview card -->
                        <div class="relative rounded-3xl shadow-2xl shadow-primary-200/40 border border-white/60 w-full aspect-video overflow-hidden bg-white">
                            <img id="heroPreview"
                                 src="{{ asset('storage_backup/adminDashboardPreview.png') }}"
                                 alt="Dashboard Preview"
                                 class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out opacity-100">
                        </div>
                        <!-- Floating badge decoration -->
                        <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-lg shadow-primary-100/50 border border-primary-100 px-4 py-3 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Points Tracking</p>
                                <p class="text-[10px] text-slate-400 font-medium">Real-time updates</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Hero image carousel script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const images = [
                    "{{ asset('storage_backup/adminDashboardPreview.png') }}",
                    "{{ asset('storage_backup/teacherDashboardPreview.png') }}",
                    "{{ asset('storage_backup/studentDashboardPreview.png') }}"
                ];
                let currentIndex = 0;
                const imgElement = document.getElementById('heroPreview');

                setInterval(() => {
                    imgElement.classList.replace('opacity-100', 'opacity-0');
                    setTimeout(() => {
                        currentIndex = (currentIndex + 1) % images.length;
                        imgElement.src = images[currentIndex];
                        imgElement.classList.replace('opacity-0', 'opacity-100');
                    }, 350);
                }, 4000);
            });
        </script>
    </section>


    <!-- ════════════════════════════════════════════
         FEATURES SECTION
         ════════════════════════════════════════════ -->
    <section id="features" class="py-24 md:py-28 bg-white relative overflow-hidden">
        <!-- Subtle background decoration -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-primary-50 rounded-full blur-[120px] opacity-50 pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <!-- Section header -->
            <div class="text-center mb-16 reveal">
                <span class="inline-flex items-center gap-2 bg-primary-50 text-primary-500 text-xs font-bold px-4 py-2 rounded-full mb-5 uppercase tracking-widest border border-primary-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
                    Features
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-4 tracking-tight">Everything You Need</h2>
                <p class="text-slate-400 text-base max-w-xl mx-auto leading-relaxed">Packed with tools that make managing student points simple, fast, and accurate.</p>
            </div>

            <!-- Feature cards grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Card 1: Manage Students -->
                <div class="feature-card rounded-2xl p-7 border border-slate-100 hover:border-primary-200 reveal">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-400 to-primary-500 flex items-center justify-center mb-5 shadow-lg shadow-primary-200/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Manage Student Data</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Easily add, update, and organize all student profiles in one centralized place.</p>
                </div>

                <!-- Card 2: Create & Manage Tasks -->
                <div class="feature-card rounded-2xl p-7 border border-slate-100 hover:border-primary-200 reveal" style="transition-delay: 0.08s;">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-500 flex items-center justify-center mb-5 shadow-lg shadow-blue-200/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Create & Manage Tasks</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Define tasks with descriptions, deadlines, and point values for structured assignment management.</p>
                </div>

                <!-- Card 3: Assign Points -->
                <div class="feature-card rounded-2xl p-7 border border-slate-100 hover:border-primary-200 reveal" style="transition-delay: 0.16s;">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center mb-5 shadow-lg shadow-amber-200/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Assign Points</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Award points to students based on task completion with a simple and intuitive process.</p>
                </div>

                <!-- Card 4: Track Records -->
                <div class="feature-card rounded-2xl p-7 border border-slate-100 hover:border-primary-200 reveal">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center mb-5 shadow-lg shadow-emerald-200/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Track Point Records</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Keep detailed logs of all point transactions per student, with full history and timestamps.</p>
                </div>

                <!-- Card 5: View Reports -->
                <div class="feature-card rounded-2xl p-7 border border-slate-100 hover:border-primary-200 sm:col-span-2 lg:col-span-1 reveal" style="transition-delay: 0.08s;">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-400 to-violet-500 flex items-center justify-center mb-5 shadow-lg shadow-violet-200/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">View Reports</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Generate and view performance reports to evaluate student progress across all tasks and time periods.</p>
                </div>

            </div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════
         HOW IT WORKS SECTION — Vertical timeline
         ════════════════════════════════════════════ -->
    <section id="how-it-works" class="py-24 md:py-28 bg-gradient-to-b from-primary-50/60 via-white to-white relative overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-20 right-0 w-[300px] h-[300px] bg-primary-100 rounded-full blur-[100px] opacity-40 pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row gap-16 items-start">

                <!-- Section text (sticky on desktop) -->
                <div class="flex-1 text-center lg:text-left lg:sticky lg:top-28 reveal">
                    <span class="inline-flex items-center gap-2 bg-white text-primary-500 text-xs font-bold px-4 py-2 rounded-full mb-5 border border-primary-200 uppercase tracking-widest shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                        How It Works
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-4 tracking-tight">Simple Steps to<br/>Get Started</h2>
                    <p class="text-slate-400 text-base max-w-md mx-auto lg:mx-0 leading-relaxed">Follow these easy steps to start managing your students' points today.</p>
                    <!-- Desktop-only CTA -->
                    <a href="{{ url('/login') }}" class="hidden lg:inline-flex items-center gap-2 mt-8 bg-primary-500 hover:bg-primary-600 active:scale-[0.97] text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-primary-200/50 transition-all duration-200 text-sm">
                        Start Now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>

                <!-- Steps list -->
                <div class="flex-1 w-full max-w-lg mx-auto lg:mx-0">
                    <ol class="relative">
                        <!-- Vertical connector line -->
                        <div class="timeline-line"></div>

                        <!-- Step 1 -->
                        <li class="relative flex items-start gap-5 mb-8 reveal">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 text-white font-extrabold text-sm flex items-center justify-center shadow-lg shadow-primary-200/40 z-10 shrink-0">1</div>
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 flex-1 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300">
                                <h3 class="font-bold text-slate-800 text-[15px] mb-1">Login as Admin</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Access the system using your admin credentials via the login page.</p>
                            </div>
                        </li>
                        <!-- Step 2 -->
                        <li class="relative flex items-start gap-5 mb-8 reveal" style="transition-delay: 0.06s;">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-500 text-white font-extrabold text-sm flex items-center justify-center shadow-lg shadow-primary-200/40 z-10 shrink-0">2</div>
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 flex-1 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300">
                                <h3 class="font-bold text-slate-800 text-[15px] mb-1">Access the Dashboard</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Get an overview of all students, tasks, and point statistics at a glance.</p>
                            </div>
                        </li>
                        <!-- Step 3 -->
                        <li class="relative flex items-start gap-5 mb-8 reveal" style="transition-delay: 0.12s;">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-400 to-blue-400 text-white font-extrabold text-sm flex items-center justify-center shadow-lg shadow-blue-200/40 z-10 shrink-0">3</div>
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 flex-1 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300">
                                <h3 class="font-bold text-slate-800 text-[15px] mb-1">Manage Students</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Add or update student profiles to keep your records up to date.</p>
                            </div>
                        </li>
                        <!-- Step 4 -->
                        <li class="relative flex items-start gap-5 mb-8 reveal" style="transition-delay: 0.18s;">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-500 text-white font-extrabold text-sm flex items-center justify-center shadow-lg shadow-blue-200/40 z-10 shrink-0">4</div>
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 flex-1 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300">
                                <h3 class="font-bold text-slate-800 text-[15px] mb-1">Create Tasks</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Define tasks with clear instructions and assign a point value to each one.</p>
                            </div>
                        </li>
                        <!-- Step 5 -->
                        <li class="relative flex items-start gap-5 mb-8 reveal" style="transition-delay: 0.24s;">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-300 to-primary-400 text-white font-extrabold text-sm flex items-center justify-center shadow-lg shadow-primary-200/40 z-10 shrink-0">5</div>
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 flex-1 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300">
                                <h3 class="font-bold text-slate-800 text-[15px] mb-1">Assign Points</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Mark task completion for each student and award the corresponding points.</p>
                            </div>
                        </li>
                        <!-- Step 6 -->
                        <li class="relative flex items-start gap-5 reveal" style="transition-delay: 0.3s;">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-200 to-primary-300 text-primary-700 font-extrabold text-sm flex items-center justify-center shadow-lg shadow-primary-100/40 z-10 shrink-0">6</div>
                            <div class="bg-white rounded-2xl border border-slate-100 p-5 flex-1 shadow-sm hover:shadow-lg hover:border-primary-200 transition-all duration-300">
                                <h3 class="font-bold text-slate-800 text-[15px] mb-1">View Reports</h3>
                                <p class="text-slate-400 text-sm leading-relaxed">Analyze student performance through detailed reports and point summaries.</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════
         TUTORIAL SECTION
         ════════════════════════════════════════════ -->
    <section id="tutorial" class="py-24 md:py-28 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6">
            <!-- Section header -->
            <div class="text-center mb-14 reveal">
                <span class="inline-flex items-center gap-2 bg-primary-50 text-primary-500 text-xs font-bold px-4 py-2 rounded-full mb-5 uppercase tracking-widest border border-primary-100">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Tutorial
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-4 tracking-tight">How to Use the System</h2>
                <p class="text-slate-400 text-base max-w-xl mx-auto leading-relaxed">Watch the tutorial video below to get a complete walkthrough of the Student Point Management System.</p>
            </div>

            <!-- Video placeholder - 16:9 -->
            <div class="max-w-3xl mx-auto reveal" style="transition-delay: 0.1s;">
                <div class="relative w-full rounded-3xl overflow-hidden shadow-2xl shadow-primary-100/40 border border-primary-100 bg-gradient-to-br from-primary-50 to-blue-50" style="padding-top: 56.25%;">
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-4">
                        <!-- Play button -->
                        <div class="w-20 h-20 rounded-full bg-white/90 backdrop-blur flex items-center justify-center shadow-lg shadow-primary-200/30 border border-primary-100 hover:scale-110 transition-transform cursor-pointer">
                            <svg class="w-8 h-8 text-primary-500 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                        <p class="text-primary-500 font-bold text-base">YouTube Video Placeholder</p>
                        <p class="text-primary-300 text-sm">Tutorial · 16:9</p>
                    </div>
                </div>

                <!-- Description below video -->
                <div class="mt-10 text-center bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-8 border border-primary-100">
                    <p class="text-slate-500 text-base leading-relaxed mb-6">
                        This tutorial covers everything from initial setup to advanced reporting.
                        Learn how to manage students, create tasks, assign points, and generate insightful performance reports — all in one place.
                    </p>
                    <a href="{{ url('/login') }}"
                       class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 active:scale-[0.97] text-white font-bold px-7 py-3.5 rounded-xl shadow-lg shadow-primary-200/50 transition-all duration-200 text-sm">
                        Start Using the System
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- ════════════════════════════════════════════
         CTA BANNER
         ════════════════════════════════════════════ -->
    <section class="py-20 relative overflow-hidden">
        <!-- Gradient background -->
        <div class="absolute inset-0 bg-gradient-to-r from-primary-600 via-primary-500 to-blue-500"></div>
        <!-- Decorative pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-400/20 rounded-full blur-3xl"></div>

        <div class="container mx-auto px-6 text-center relative z-10 reveal">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-5 tracking-tight">Ready to Get Started?</h2>
            <p class="text-primary-100 text-base mb-10 max-w-md mx-auto leading-relaxed">Log in now and start managing student performance the smart way.</p>
            <a href="{{ url('/login') }}"
               class="inline-flex items-center gap-2 bg-white hover:bg-primary-50 active:scale-[0.97] text-primary-600 font-extrabold px-10 py-4 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-200 text-base">
                Login to Dashboard
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </section>


    <!-- ════════════════════════════════════════════
         FOOTER
         ════════════════════════════════════════════ -->
    <footer class="bg-slate-900 text-slate-400 py-12">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <!-- Logo + name -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('storage_backup/Icon.png') }}" alt="PointMS" class="h-10 w-auto object-contain rounded-lg">
                    <span class="font-display font-bold text-white text-base">Point<span class="text-primary-400">MS</span></span>
                </div>
                <!-- Center text -->
                <p class="text-slate-500 text-sm text-center">
                    Student Point Management System &mdash; School Project
                </p>
                <!-- Links -->
                <div class="flex gap-6 text-sm font-medium">
                    <a href="#features" class="hover:text-primary-400 transition-colors">Features</a>
                    <a href="#how-it-works" class="hover:text-primary-400 transition-colors">How It Works</a>
                    <a href="{{ url('/login') }}" class="hover:text-primary-400 transition-colors">Login</a>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-8 pt-6 text-center text-xs text-slate-600">
                &copy; {{ date('Y') }} Student Point Management System. All rights reserved.
            </div>
        </div>
    </footer>


    <!-- ════════════════════════════════════════════
         SCRIPTS — Smooth scroll, navbar, reveal, etc.
         ════════════════════════════════════════════ -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        /* ── Smooth scroll for anchor links ── */
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Close mobile menu if open
                    document.getElementById('mobileMenu')?.classList.remove('open');
                }
            });
        });

        /* ── Navbar background on scroll ── */
        const navbar = document.getElementById('navbar');
        const updateNavbar = () => {
            if (window.scrollY > 20) {
                navbar.classList.add('bg-white/85', 'backdrop-blur-xl', 'shadow-sm', 'border-b', 'border-primary-100/50');
            } else {
                navbar.classList.remove('bg-white/85', 'backdrop-blur-xl', 'shadow-sm', 'border-b', 'border-primary-100/50');
            }
        };
        updateNavbar();
        window.addEventListener('scroll', updateNavbar, { passive: true });

        /* ── Mobile menu toggle ── */
        document.getElementById('menuToggle')?.addEventListener('click', () => {
            document.getElementById('mobileMenu')?.classList.toggle('open');
        });

        /* ── Scroll-triggered reveal animations ── */
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        /* ── Prevent accidental back navigation on mobile ── */
        history.pushState(null, null, location.href);
        window.onpopstate = () => history.go(1);
    });
    </script>

    {{-- Accessibility Text Size Widget --}}
    @include('partials.text-size-widget')

</body>
</html>