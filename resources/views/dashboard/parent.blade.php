<html>
<div class="min-h-screen bg-slate-50">

    {{-- Top Header --}}
    <div class="bg-white border-b border-slate-200 px-8 py-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-slate-400 uppercase">Point Management System</p>
            <h1 class="text-2xl font-bold text-slate-800 mt-0.5">Parent Dashboard</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-500">{{ now()->format('l, d F Y') }}</span>
            <div class="w-9 h-9 rounded-full bg-rose-500 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>

    @php
        $parent = auth()->user()->parent;
        $children = $parent->students()->with(['user', 'class.major'])->get();
    @endphp

    <div class="px-8 py-8 max-w-7xl mx-auto">

        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl p-6 mb-8 text-white">
            <p class="text-rose-100 text-sm">Welcome,</p>
            <h2 class="text-xl font-bold mt-0.5">{{ auth()->user()->name }}</h2>
            <p class="text-rose-200 text-sm mt-1">
                You are monitoring <span class="text-white font-semibold">{{ $children->count() }}</span> {{ Str::plural('child', $children->count()) }}
            </p>
        </div>

        {{-- Children Cards --}}
        @forelse($children as $child)
        @php
            $totalPoints = $child->points()->sum('point');
            $pendingTasks = \App\Models\Task::whereHas('teacherSubject', fn($q) => $q->where('class_id', $child->class_id))
                ->whereDoesntHave('submissions', fn($q) => $q->where('student_id', $child->id))
                ->count();
            $recentPoints = $child->points()->with('teacherSubject.subject')->latest('point_date')->take(4)->get();
            $recentSubmissions = $child->taskSubmissions()->with('task')->latest()->take(4)->get();
        @endphp

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

            {{-- Child Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 font-bold text-sm">
                        {{ strtoupper(substr($child->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $child->user->name }}</h3>
                        <p class="text-xs text-slate-400">{{ $child->class->name ?? 'No class' }} — {{ $child->class->major->name ?? 'No major' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Points</p>
                        <p @class([
                            'text-lg font-bold',
                            'text-emerald-600' => $totalPoints >= 0,
                            'text-rose-600' => $totalPoints < 0,
                        ])>{{ $totalPoints }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Pending</p>
                        <p class="text-lg font-bold text-amber-500">{{ $pendingTasks }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                {{-- Recent Points --}}
                <div>
                    <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Recent Points</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($recentPoints as $point)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ $point->teacherSubject->subject->name }}</p>
                                <p class="text-xs text-slate-400">{{ $point->description ?? 'No description' }}</p>
                                <p class="text-xs text-slate-300">{{ \Carbon\Carbon::parse($point->point_date)->format('d M Y') }}</p>
                            </div>
                            <span @class([
                                'text-sm font-bold px-3 py-1 rounded-full',
                                'bg-emerald-100 text-emerald-700' => $point->point > 0,
                                'bg-rose-100 text-rose-700' => $point->point < 0,
                            ])>{{ $point->point > 0 ? '+' : '' }}{{ $point->point }}</span>
                        </div>
                        @empty
                        <div class="px-6 py-6 text-center text-slate-400 text-sm">No points yet.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Submissions --}}
                <div>
                    <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Task Submissions</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($recentSubmissions as $submission)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ $submission->task->title }}</p>
                                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y') }}</p>
                            </div>
                            @if($submission->grade !== null)
                                <span class="text-sm font-bold px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
                                    {{ $submission->grade }}
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-600">
                                    Pending
                                </span>
                            @endif
                        </div>
                        @empty
                        <div class="px-6 py-6 text-center text-slate-400 text-sm">No submissions yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-8 py-16 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-slate-500 font-medium">No children linked to your account yet.</p>
            <p class="text-slate-400 text-sm mt-1">Please contact your administrator to link your children.</p>
        </div>
        @endforelse

    </div>
</div>
</html>