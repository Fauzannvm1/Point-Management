<html>
<div class="min-h-screen bg-slate-50 font-[Inter]">

    {{-- Top Header --}}
    <div class="bg-white border-b border-slate-200 px-8 py-5 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-slate-400 uppercase">Point Management System</p>
            <h1 class="text-2xl font-bold text-slate-800 mt-0.5">Admin Dashboard</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-500">{{ now()->format('l, d F Y') }}</span>
            <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>

    <div class="px-8 py-8 max-w-7xl mx-auto">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Students</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ \App\Models\Student::count() }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Teachers</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ \App\Models\Teacher::count() }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Classes</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ \App\Models\Classes::count() }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-lg bg-rose-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Subjects</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ \App\Models\Subject::count() }}</p>
                </div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Recent Users --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-700">Recent Users</h2>
                    {{-- <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">View all</a> --}}
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach(\App\Models\User::latest()->take(6)->get() as $user)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span @class([
                            'text-xs font-semibold px-2.5 py-1 rounded-full',
                            'bg-indigo-100 text-indigo-700' => $user->role === 'admin',
                            'bg-emerald-100 text-emerald-700' => $user->role === 'teacher',
                            'bg-amber-100 text-amber-700' => $user->role === 'student',
                            'bg-rose-100 text-rose-700' => $user->role === 'parent',
                        ])>{{ ucfirst($user->role) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-700">Quick Actions</h2>
                </div>
                <div class="p-5 flex flex-col gap-3">
                    {{-- <a href="{{ route('admin.users.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 transition group"> --}}
                        <div class="w-8 h-8 bg-indigo-600 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-sm font-medium text-indigo-700">Add New User</span>
                    </a>
                    {{-- <a href="{{ route('admin.classes.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 hover:bg-amber-100 transition"> --}}
                        <div class="w-8 h-8 bg-amber-500 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-sm font-medium text-amber-700">Add New Class</span>
                    </a>
                    {{-- <a href="{{ route('admin.subjects.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-rose-50 hover:bg-rose-100 transition"> --}}
                        <div class="w-8 h-8 bg-rose-500 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-sm font-medium text-rose-700">Add New Subject</span>
                    </a>
                    {{-- <a href="{{ route('admin.majors.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition"> --}}
                        <div class="w-8 h-8 bg-emerald-500 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-sm font-medium text-emerald-700">Add New Major</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
</html>