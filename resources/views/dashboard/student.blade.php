<html>
<div class="min-h-screen bg-slate-50">

    {{-- Top Header --}}
    <div class="bg-white border-b border-slate-200 px-8 py-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-slate-400 uppercase">Point Management System</p>
            <h1 class="text-2xl font-bold text-slate-800 mt-0.5">Student Dashboard</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-500">{{ now()->format('l, d F Y') }}</span>
            <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>

    @php
        $student = auth()->user()->student;
        $totalPoints = $student->points()->sum('point');
        $pendingTasks = \App\Models\Task::whereHas('teacherSubject', fn($q) => $q->where('class_id', $student->class_id))
            ->whereDoesntHave('submissions', fn($q) => $q->where('student_id', $student->id))
            ->count();
        $submittedTasks = $student->taskSubmissions()->count();
        $recentPoints = $student->points()->with('teacherSubject.subject')->latest('point_date')->take(5)->get();
        $upcomingTasks = \App\Models\Task::whereHas('teacherSubject', fn($q) => $q->where('class_id', $student->class_id))
            ->whereDoesntHave('submissions', fn($q) => $q->where('student_id', $student->id))
            ->orderBy('due_date')
            ->take(5)
            ->get();
    @endphp

    <div class="px-8 py-8 max-w-7xl mx-auto">

        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-6 mb-8 text-white flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-sm">Hello,</p>
                <h2 class="text-xl font-bold mt-0.5">{{ auth()->user()->name }}</h2>
                <p class="text-amber-200 text-sm mt-1">
                    Class: <span class="text-white font-semibold">{{ $student->class->name ?? 'Not assigned' }}</span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-amber-100 text-xs uppercase font-semibold tracking-wide">Total Points</p>
                <p class="text-4xl font-extrabold">{{ $totalPoints }}</p>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Points</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $totalPoints }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-rose-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Pending Tasks</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $pendingTasks }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Submitted Tasks</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $submittedTasks }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Recent Points --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-700">Recent Points</h2>
                    <a href="{{ route('student.points.index') }}" class="text-xs text-amber-600 hover:underline font-medium">View all</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($recentPoints as $point)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ $point->teacherSubject->subject->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $point->description ?? 'No description' }}</p>
                            <p class="text-xs text-slate-300 mt-0.5">{{ \Carbon\Carbon::parse($point->point_date)->format('d M Y') }}</p>
                        </div>
                        <span @class([
                            'text-sm font-bold px-3 py-1 rounded-full',
                            'bg-emerald-100 text-emerald-700' => $point->point > 0,
                            'bg-rose-100 text-rose-700' => $point->point < 0,
                        ])>{{ $point->point > 0 ? '+' : '' }}{{ $point->point }}</span>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-slate-400 text-sm">No points recorded yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Upcoming Tasks --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-700">Upcoming Tasks</h2>
                    <a href="{{ route('student.tasks.index') }}" class="text-xs text-amber-600 hover:underline font-medium">View all</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($upcomingTasks as $task)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ $task->title }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $task->teacherSubject->subject->name ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            @if($task->due_date)
                                <p @class([
                                    'text-xs font-semibold',
                                    'text-rose-500' => \Carbon\Carbon::parse($task->due_date)->isPast(),
                                    'text-amber-500' => \Carbon\Carbon::parse($task->due_date)->isToday(),
                                    'text-slate-500' => \Carbon\Carbon::parse($task->due_date)->isFuture(),
                                ])>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</p>
                            @else
                                <p class="text-xs text-slate-400">No due date</p>
                            @endif
                            <a href="{{ route('student.tasks.show', $task->id) }}" class="text-xs text-indigo-600 hover:underline mt-0.5 inline-block">Submit →</a>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-slate-400 text-sm">No pending tasks. Great job! 🎉</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
</html>