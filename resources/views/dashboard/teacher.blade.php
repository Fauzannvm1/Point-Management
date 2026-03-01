<html>
<div class="min-h-screen bg-slate-50">

    {{-- Top Header --}}
    <div class="bg-white border-b border-slate-200 px-8 py-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-slate-400 uppercase">Point Management System</p>
            <h1 class="text-2xl font-bold text-slate-800 mt-0.5">Teacher Dashboard</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-500">{{ now()->format('l, d F Y') }}</span>
            <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>

    @php
        $teacher = auth()->user()->teacher;
        $teacherSubjects = $teacher->teacherSubjects()->with(['subject', 'class'])->get();
        $totalStudents = $teacherSubjects->flatMap(fn($ts) => $ts->class->students ?? collect())->unique('id')->count();
        $totalTasks = $teacher->teacherSubjects()->withCount('tasks')->get()->sum('tasks_count');
        $pendingSubmissions = \App\Models\TaskSubmission::whereHas('task.teacherSubject', fn($q) => $q->where('teacher_id', $teacher->id))->whereNull('grade')->count();
    @endphp

    <div class="px-8 py-8 max-w-7xl mx-auto">

        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-6 mb-8 text-white flex items-center justify-between">
            <div>
                <p class="text-emerald-100 text-sm">Welcome back,</p>
                <h2 class="text-xl font-bold mt-0.5">{{ auth()->user()->name }}</h2>
                <p class="text-emerald-200 text-sm mt-1">You have <span class="text-white font-semibold">{{ $pendingSubmissions }}</span> ungraded submissions</p>
            </div>
            <svg class="w-20 h-20 text-emerald-500 opacity-30" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">My Subjects</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $teacherSubjects->count() }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">My Students</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalStudents }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Tasks</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalTasks }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- My Classes & Subjects --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-700">My Classes & Subjects</h2>
                    <a href="{{ route('teacher.subjects.index') }}" class="text-xs text-emerald-600 hover:underline font-medium">View all</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($teacherSubjects as $ts)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ $ts->subject->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Class: {{ $ts->class->name }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('teacher.tasks.index', $ts->id) }}" class="text-xs bg-indigo-50 text-indigo-600 font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition">Tasks</a>
                            <a href="{{ route('teacher.points.index', $ts->id) }}" class="text-xs bg-emerald-50 text-emerald-600 font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition">Points</a>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-slate-400 text-sm">No subjects assigned yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Pending Submissions --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-700">Pending Grading</h2>
                    <span class="text-xs bg-rose-100 text-rose-600 font-semibold px-2 py-0.5 rounded-full">{{ $pendingSubmissions }}</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse(\App\Models\TaskSubmission::whereHas('task.teacherSubject', fn($q) => $q->where('teacher_id', $teacher->id))->whereNull('grade')->with(['task', 'student.user'])->latest()->take(5)->get() as $submission)
                    <div class="px-6 py-3">
                        <p class="text-sm font-medium text-slate-700">{{ $submission->student->user->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $submission->task->title }}</p>
                        <a href="{{ route('teacher.submissions.show', $submission->id) }}" class="text-xs text-indigo-600 hover:underline mt-1 inline-block">Grade now →</a>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-slate-400 text-sm">All caught up! 🎉</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
</html>