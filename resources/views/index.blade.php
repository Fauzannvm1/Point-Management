<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Point Management System</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Nunito (friendly + clean) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Sora', 'sans-serif'],
                        body: ['Nunito', 'sans-serif'],
                    },
                    colors: {
                        sky: {
                            25: '#f0f9ff',
                            50: '#e0f2fe',
                            100: '#bae6fd',
                            200: '#7dd3fc',
                            300: '#38bdf8',
                            400: '#0ea5e9',
                            500: '#0284c7',
                            600: '#0369a1',
                        },
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-up': 'fadeUp 0.6s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-12px)' },
                        },
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                },
            },
        }
    </script>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8faff;
        }
        h1, h2, h3 {
            font-family: 'Sora', sans-serif;
        }
        /* Subtle dot pattern background */
        .dot-pattern {
            background-image: radial-gradient(circle, #bae6fd 1px, transparent 1px);
            background-size: 28px 28px;
        }
        /* Gradient blob decorations */
        .blob {
            border-radius: 9999px;
            filter: blur(80px);
            opacity: 0.35;
            position: absolute;
            pointer-events: none;
        }
        /* Step number badge */
        .step-badge {
            min-width: 2rem;
            min-height: 2rem;
        }
        body {
            user-select: none;
            -webkit-user-select: none;
        }
        input, textarea, select, .join-code-box .mono {
            user-select: text;
            -webkit-user-select: text;
        }
    </style>
</head>
<body class="text-slate-700">

    <!--   NAVBAR   -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-sky-100 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center shadow">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="font-display font-700 text-sky-600 text-base font-bold tracking-tight hidden sm:block">SPMS</span>
            </div>
            <!-- Nav Links -->
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-500">
                <a href="#features" class="hover:text-sky-500 transition-colors">Features</a>
                <a href="#how-to-use" class="hover:text-sky-500 transition-colors">How to Use</a>
                <a href="#tutorial" class="hover:text-sky-500 transition-colors">Tutorial</a>
            </div>
            <!-- CTA Button -->
            <a href="{{ url('/login') }}"
               class="bg-sky-500 hover:bg-sky-600 active:scale-95 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-md hover:shadow-sky-200 transition-all duration-200">
                Login
            </a>
        </div>
    </nav>

    <!--   HERO SECTION   -->
    <section class="relative overflow-hidden pt-20 pb-28 dot-pattern">
        <!-- Decorative blobs -->
        <div class="blob w-96 h-96 bg-sky-300 top-[-80px] left-[-80px]"></div>
        <div class="blob w-80 h-80 bg-blue-200 bottom-0 right-[-60px]"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col-reverse lg:flex-row items-center gap-14">

                <!-- Hero Text -->
                <div class="flex-1 text-center lg:text-left">
                    <!-- Badge -->
                    <span class="inline-block bg-sky-100 text-sky-600 text-xs font-bold px-4 py-1.5 rounded-full mb-5 tracking-wide uppercase">
                        🎓 School Project
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-[3.4rem] font-extrabold text-slate-800 leading-tight mb-6">
                        Student Point<br/>
                        <span class="text-sky-500">Management</span> System
                    </h1>
                    <p class="text-slate-500 text-lg leading-relaxed max-w-xl mx-auto lg:mx-0 mb-9">
                        A web-based system designed to manage student points based on assigned tasks.
                        It helps teachers monitor student performance and track task completion efficiently.
                    </p>
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ url('/login') }}"
                           class="bg-sky-500 hover:bg-sky-600 active:scale-95 text-white font-bold px-8 py-3.5 rounded-2xl shadow-lg hover:shadow-sky-200 transition-all duration-200 text-base">
                            Get Started →
                        </a>
                        <a href="#how-to-use"
                           class="bg-white hover:bg-sky-50 text-sky-500 font-bold px-8 py-3.5 rounded-2xl border-2 border-sky-200 hover:border-sky-300 transition-all duration-200 text-base">
                            Learn More
                        </a>
                    </div>
                    <!-- Stats row -->
                    <div class="flex gap-8 mt-10 justify-center lg:justify-start text-center">
                        <div>
                            <p class="text-2xl font-extrabold text-sky-500">100%</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Web-based</p>
                        </div>
                        <div class="w-px bg-sky-100"></div>
                        <div>
                            <p class="text-2xl font-extrabold text-sky-500">Easy</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">To Use</p>
                        </div>
                        <div class="w-px bg-sky-100"></div>
                        <div>
                            <p class="text-2xl font-extrabold text-sky-500">Real-time</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Tracking</p>
                        </div>
                    </div>
                </div>

                <!-- Hero Image Placeholder -->
                <div class="flex-1 w-full max-w-lg mx-auto animate-float">
                    <div class="relative">
                        <!-- Glow ring -->
                        <div class="absolute inset-0 rounded-3xl bg-sky-200 blur-2xl opacity-50 scale-95"></div>
                        <!-- Placeholder box -->
                        <div class="relative bg-white border-2 border-dashed border-sky-300 rounded-3xl aspect-video flex flex-col items-center justify-center gap-3 shadow-xl shadow-sky-100">
                            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-sky-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <p class="text-sky-400 font-bold text-sm">Image Placeholder</p>
                            <p class="text-sky-300 text-xs">Dashboard Preview</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!--   FEATURES SECTION   -->
    <section id="features" class="py-24 bg-white">
        <div class="container mx-auto px-6">

            <!-- Section header -->
            <div class="text-center mb-14">
                <span class="inline-block bg-sky-50 text-sky-500 text-xs font-bold px-4 py-1.5 rounded-full mb-4 uppercase tracking-widest">Features</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-4">Everything You Need</h2>
                <p class="text-slate-400 text-base max-w-xl mx-auto">Packed with tools that make managing student points simple, fast, and accurate.</p>
            </div>

            <!-- Feature cards grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Card 1: Manage Students -->
                <div class="group bg-sky-25 hover:bg-sky-50 border border-sky-100 hover:border-sky-300 rounded-2xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100 cursor-default">
                    <!-- Icon placeholder -->
                    <div class="w-12 h-12 rounded-xl bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center mb-5 transition-colors">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-2">Manage Student Data</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Easily add, update, and organize all student profiles in one centralized place.</p>
                </div>

                <!-- Card 2: Create & Manage Tasks -->
                <div class="group bg-sky-25 hover:bg-sky-50 border border-sky-100 hover:border-sky-300 rounded-2xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100 cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center mb-5 transition-colors">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-2">Create & Manage Tasks</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Define tasks with descriptions, deadlines, and point values for structured assignment management.</p>
                </div>

                <!-- Card 3: Assign Points -->
                <div class="group bg-sky-25 hover:bg-sky-50 border border-sky-100 hover:border-sky-300 rounded-2xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100 cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center mb-5 transition-colors">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-2">Assign Points</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Award points to students based on task completion with a simple and intuitive process.</p>
                </div>

                <!-- Card 4: Track Records -->
                <div class="group bg-sky-25 hover:bg-sky-50 border border-sky-100 hover:border-sky-300 rounded-2xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100 cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center mb-5 transition-colors">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-2">Track Point Records</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Keep detailed logs of all point transactions per student, with full history and timestamps.</p>
                </div>

                <!-- Card 5: View Reports -->
                <div class="group bg-sky-25 hover:bg-sky-50 border border-sky-100 hover:border-sky-300 rounded-2xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-sky-100 cursor-default sm:col-span-2 lg:col-span-1">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center mb-5 transition-colors">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-2">View Reports</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Generate and view performance reports to evaluate student progress across all tasks and time periods.</p>
                </div>

            </div>
        </div>
    </section>

    <!--   HOW TO USE SECTION   -->
    <section id="how-to-use" class="py-24 bg-gradient-to-b from-sky-50 to-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-16 items-center">

                <!-- Section text -->
                <div class="flex-1 text-center lg:text-left">
                    <span class="inline-block bg-white text-sky-500 text-xs font-bold px-4 py-1.5 rounded-full mb-4 border border-sky-200 uppercase tracking-widest">How It Works</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-4">Simple Steps to Get Started</h2>
                    <p class="text-slate-400 text-base max-w-md mx-auto lg:mx-0">Follow these easy steps to start managing your students' points today.</p>
                </div>

                <!-- Steps list -->
                <div class="flex-1 w-full max-w-lg mx-auto lg:mx-0">
                    <ol class="relative">
                        <!-- Vertical line -->
                        <div class="absolute left-5 top-5 bottom-5 w-0.5 bg-sky-100 -z-0"></div>

                        <!-- Step 1 -->
                        <li class="relative flex items-start gap-5 mb-7">
                            <div class="step-badge w-10 h-10 rounded-full bg-sky-500 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-sky-200 z-10 shrink-0">1</div>
                            <div class="bg-white rounded-2xl border border-sky-100 p-4 flex-1 shadow-sm hover:shadow-md hover:border-sky-200 transition-all">
                                <h3 class="font-bold text-slate-700 text-base">Login as Admin</h3>
                                <p class="text-slate-400 text-sm mt-1">Access the system using your admin credentials via the login page.</p>
                            </div>
                        </li>
                        <!-- Step 2 -->
                        <li class="relative flex items-start gap-5 mb-7">
                            <div class="step-badge w-10 h-10 rounded-full bg-sky-400 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-sky-200 z-10 shrink-0">2</div>
                            <div class="bg-white rounded-2xl border border-sky-100 p-4 flex-1 shadow-sm hover:shadow-md hover:border-sky-200 transition-all">
                                <h3 class="font-bold text-slate-700 text-base">Access the Dashboard</h3>
                                <p class="text-slate-400 text-sm mt-1">Get an overview of all students, tasks, and point statistics at a glance.</p>
                            </div>
                        </li>
                        <!-- Step 3 -->
                        <li class="relative flex items-start gap-5 mb-7">
                            <div class="step-badge w-10 h-10 rounded-full bg-sky-400 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-sky-200 z-10 shrink-0">3</div>
                            <div class="bg-white rounded-2xl border border-sky-100 p-4 flex-1 shadow-sm hover:shadow-md hover:border-sky-200 transition-all">
                                <h3 class="font-bold text-slate-700 text-base">Manage Students</h3>
                                <p class="text-slate-400 text-sm mt-1">Add or update student profiles to keep your records up to date.</p>
                            </div>
                        </li>
                        <!-- Step 4 -->
                        <li class="relative flex items-start gap-5 mb-7">
                            <div class="step-badge w-10 h-10 rounded-full bg-sky-300 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-sky-200 z-10 shrink-0">4</div>
                            <div class="bg-white rounded-2xl border border-sky-100 p-4 flex-1 shadow-sm hover:shadow-md hover:border-sky-200 transition-all">
                                <h3 class="font-bold text-slate-700 text-base">Create Tasks</h3>
                                <p class="text-slate-400 text-sm mt-1">Define tasks with clear instructions and assign a point value to each one.</p>
                            </div>
                        </li>
                        <!-- Step 5 -->
                        <li class="relative flex items-start gap-5 mb-7">
                            <div class="step-badge w-10 h-10 rounded-full bg-sky-300 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-sky-200 z-10 shrink-0">5</div>
                            <div class="bg-white rounded-2xl border border-sky-100 p-4 flex-1 shadow-sm hover:shadow-md hover:border-sky-200 transition-all">
                                <h3 class="font-bold text-slate-700 text-base">Assign Points</h3>
                                <p class="text-slate-400 text-sm mt-1">Mark task completion for each student and award the corresponding points.</p>
                            </div>
                        </li>
                        <!-- Step 6 -->
                        <li class="relative flex items-start gap-5">
                            <div class="step-badge w-10 h-10 rounded-full bg-sky-200 text-sky-600 font-extrabold text-sm flex items-center justify-center shadow-md shadow-sky-100 z-10 shrink-0">6</div>
                            <div class="bg-white rounded-2xl border border-sky-100 p-4 flex-1 shadow-sm hover:shadow-md hover:border-sky-200 transition-all">
                                <h3 class="font-bold text-slate-700 text-base">View Reports</h3>
                                <p class="text-slate-400 text-sm mt-1">Analyze student performance through detailed reports and point summaries.</p>
                            </div>
                        </li>

                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!--   TUTORIAL SECTION   -->
    <section id="tutorial" class="py-24 bg-white">
        <div class="container mx-auto px-6">

            <!-- Section header -->
            <div class="text-center mb-12">
                <span class="inline-block bg-sky-50 text-sky-500 text-xs font-bold px-4 py-1.5 rounded-full mb-4 uppercase tracking-widest">Tutorial</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-4">How to Use the System</h2>
                <p class="text-slate-400 text-base max-w-xl mx-auto">Watch the tutorial video below to get a complete walkthrough of the Student Point Management System.</p>
            </div>

            <!-- Video placeholder - 16:9 aspect ratio -->
            <div class="max-w-3xl mx-auto">
                <div class="relative w-full rounded-3xl overflow-hidden shadow-2xl shadow-sky-100 border-2 border-dashed border-sky-200 bg-sky-50" style="padding-top: 56.25%;">
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-4">
                        <!-- Play button decoration -->
                        <div class="w-20 h-20 rounded-full bg-sky-100 flex items-center justify-center border-2 border-sky-200">
                            <svg class="w-9 h-9 text-sky-400 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                        <p class="text-sky-400 font-bold text-base">YouTube Video Placeholder</p>
                        <p class="text-sky-300 text-sm">Tutorial · 16:9</p>
                    </div>
                </div>

                <!-- Description below video -->
                <div class="mt-8 text-center bg-sky-50 rounded-2xl p-6 border border-sky-100">
                    <p class="text-slate-500 text-base leading-relaxed">
                        This tutorial covers everything from initial setup to advanced reporting.
                        Learn how to manage students, create tasks, assign points, and generate insightful performance reports — all in one place.
                    </p>
                    <a href="{{ url('/login') }}"
                       class="inline-block mt-5 bg-sky-500 hover:bg-sky-600 active:scale-95 text-white font-bold px-7 py-3 rounded-xl shadow-md hover:shadow-sky-200 transition-all duration-200 text-sm">
                        Start Using the System →
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!--   CTA BANNER  -->
    <section class="py-16 bg-gradient-to-r from-sky-500 to-sky-400">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-extrabold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-sky-100 text-base mb-8 max-w-md mx-auto">Log in now and start managing student performance the smart way.</p>
            <a href="{{ url('/login') }}"
               class="inline-block bg-white hover:bg-sky-50 active:scale-95 text-sky-500 font-extrabold px-10 py-4 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-200 text-base">
                Login to Dashboard →
            </a>
        </div>
    </section>

    <!--   FOOTER   -->
    <footer class="bg-slate-800 text-slate-400 py-10">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Logo + name -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-slate-300 font-bold text-sm">Student Point Management System</span>
                </div>
                <!-- Center text -->
                <p class="text-slate-500 text-sm text-center">
                    Student Point Management System &mdash; School Project
                </p>
                <!-- Links -->
                <div class="flex gap-6 text-sm">
                    <a href="#features" class="hover:text-sky-400 transition-colors">Features</a>
                    <a href="#how-to-use" class="hover:text-sky-400 transition-colors">How to Use</a>
                    <a href="{{ url('/login') }}" class="hover:text-sky-400 transition-colors">Login</a>
                </div>
            </div>
            <div class="border-t border-slate-700 mt-8 pt-6 text-center text-xs text-slate-600">
                &copy; {{ date('Y') }} Student Point Management System. All rights reserved.
            </div>
        </div>
    </footer>

    <!--   SMOOTH SCROLL   -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
    <script>
        // Prevent highlighting text globally except on specific inputs
        document.addEventListener('selectstart', function(e) {
        const tag = e.target.tagName;
        if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
            e.preventDefault();
        }
        });
        // Prevent accidental back navigation on mobile
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
        history.go(1);
        };
    </script>
</body>
</html>