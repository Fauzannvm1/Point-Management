<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - PointMS</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root { --sidebar-bg:#0f1117; --accent:#6366f1; --accent-glow:rgba(99,102,241,0.15); }
  * { box-sizing:border-box; }
  body { font-family:'Syne',sans-serif; background:#f4f5f9; color:#1e2130; }
  .mono { font-family:'JetBrains Mono',monospace; }
  .sidebar { background:var(--sidebar-bg); width:240px; min-height:100vh; position:fixed; top:0; left:0; z-index:40; display:flex; flex-direction:column; transition:transform 0.25s ease; }
  .nav-item { display:flex; align-items:center; gap:10px; padding:9px 14px; border-radius:10px; font-size:13.5px; font-weight:500; color:#7c8099; cursor:pointer; transition:all 0.15s; text-decoration:none; }
  .nav-item:hover { background:rgba(255,255,255,0.06); color:#e2e4f0; transform:translateX(3px); }
  .nav-item.active { background:var(--accent-glow); color:#a5b4fc; border:1px solid rgba(99,102,241,0.25); }
  .nav-item svg { width:16px; height:16px; flex-shrink:0; }
  .card { background:white; border-radius:16px; border:1px solid #e8eaef; box-shadow:0 1px 4px rgba(0,0,0,0.04); }
  .badge { font-family:'JetBrains Mono',monospace; font-size:10px; padding:2px 8px; border-radius:6px; font-weight:600; }
  .stat-num { font-family:'JetBrains Mono',monospace; font-size:30px; font-weight:600; }
  .page { display:none; }
  .page.active { display:block; }
  .main-content { margin-left:240px; min-height:100vh; }
  .btn-primary { background:#6366f1; color:white; padding:9px 18px; border-radius:10px; font-size:13px; font-weight:600; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:all 0.15s; font-family:'Syne',sans-serif; }
  .btn-primary:hover { background:#4f51de; transform:translateY(-1px); box-shadow:0 4px 12px rgba(99,102,241,0.3); }
  .btn-secondary { background:#f1f2f6; color:#4a5070; padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600; border:1px solid #e0e2ea; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:all 0.15s; font-family:'Syne',sans-serif; }
  .btn-secondary:hover { background:#e8e9f0; }
  .btn-danger { background:#fef2f2; color:#ef4444; padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:600; border:1px solid #fee2e2; cursor:pointer; transition:all 0.15s; font-family:'Syne',sans-serif; }
  .btn-danger:hover { background:#ef4444; color:white; }
  .btn-edit { background:#f0f1ff; color:#6366f1; padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:600; border:1px solid #e0e2ff; cursor:pointer; transition:all 0.15s; font-family:'Syne',sans-serif; }
  .btn-edit:hover { background:#6366f1; color:white; }
  input,select,textarea { font-family:'Syne',sans-serif; outline:none; transition:border 0.15s,box-shadow 0.15s; }
  input:focus,select:focus,textarea:focus { border-color:#6366f1 !important; box-shadow:0 0 0 3px rgba(99,102,241,0.12); }
  .table-row:hover { background:#f9fafb; }
  .modal-overlay { position:fixed; inset:0; background:rgba(15,17,23,0.6); backdrop-filter:blur(4px); z-index:100; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:opacity 0.2s; }
  .modal-overlay.open { opacity:1; pointer-events:all; }
  .modal-box { background:white; border-radius:20px; padding:28px; width:100%; max-width:480px; transform:translateY(16px); transition:transform 0.2s; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
  .modal-overlay.open .modal-box { transform:translateY(0); }
  .tag { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:7px; font-size:12px; font-weight:600; }
  @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
  .page.active { animation:fadeIn 0.2s ease; }
  .notif-dot { width:7px; height:7px; background:#ef4444; border-radius:50%; position:absolute; top:6px; right:6px; }
  ::-webkit-scrollbar{width:5px} ::-webkit-scrollbar-track{background:transparent} ::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:4px}
</style>
</head>
<body>

{{-- MODAL: Add/Edit User --}}
<div class="modal-overlay" id="userModal">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-bold text-slate-900" id="userModalTitle">Add New User</h3>
      <button onclick="closeModal('userModal')" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="space-y-4">
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name</label><input type="text" id="uName" placeholder="e.g. Ahmad Riski" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label><input type="email" id="uEmail" placeholder="user@school.edu" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Role</label><select id="uRole" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"><option value="">-- Select Role --</option><option value="admin">Admin</option><option value="teacher">Teacher</option><option value="student">Student</option><option value="parent">Parent</option></select></div>
      <div id="passField"><label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label><input type="password" id="uPass" placeholder="Min. 6 characters" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('userModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="saveUser()" class="btn-primary flex-1 justify-center" id="userSaveBtn">Add User</button>
    </div>
  </div>
</div>

{{-- MODAL: Add/Edit Task --}}
<div class="modal-overlay" id="taskModal">
  <div class="modal-box" style="max-width:540px">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-bold text-slate-900" id="taskModalTitle">Add New Task</h3>
      <button onclick="closeModal('taskModal')" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="space-y-4">
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Title</label><input type="text" id="tTitle" placeholder="e.g. Algebra Homework" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label><textarea id="tDesc" rows="3" placeholder="Task description..." class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800 resize-none"></textarea></div>
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Teacher Subject</label><select id="tTeacherSubject" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"><option value="">-- Select --</option></select></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Due Date</label><input type="date" id="tDueDate" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
        <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Max Grade</label><input type="number" id="tMaxGrade" value="100" min="1" max="100" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('taskModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="saveTask()" class="btn-primary flex-1 justify-center" id="taskSaveBtn">Add Task</button>
    </div>
  </div>
</div>

{{-- MODAL: Give Points --}}
<div class="modal-overlay" id="pointModal">
  <div class="modal-box" style="max-width:500px">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-bold text-slate-900">Give Points to Student</h3>
      <button onclick="closeModal('pointModal')" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="space-y-4">
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Student</label><select id="pStudent" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"><option value="">-- Select Student --</option></select></div>
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Teacher Subject</label><select id="pTeacherSubject" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"><option value="">-- Select --</option></select></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Points</label><input type="number" id="pPoints" placeholder="e.g. 50 or -20" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"><p class="text-xs text-slate-400 mt-1">Positive = reward · Negative = penalty</p></div>
        <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Date</label><input type="date" id="pDate" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
      </div>
      <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label><input type="text" id="pDesc" placeholder="e.g. Won science competition" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('pointModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="savePoint()" class="btn-primary flex-1 justify-center">Give Points</button>
    </div>
  </div>
</div>

{{-- MODAL: Add/Edit Subject --}}
<div class="modal-overlay" id="subjectModal">
  <div class="modal-box" style="max-width:400px">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-bold text-slate-900" id="subjectModalTitle">Add Subject</h3>
      <button onclick="closeModal('subjectModal')" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div><label class="block text-sm font-semibold text-slate-700 mb-1.5">Subject Name</label><input type="text" id="sName" placeholder="e.g. Mathematics" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('subjectModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="saveSubject()" class="btn-primary flex-1 justify-center" id="subjectSaveBtn">Add Subject</button>
    </div>
  </div>
</div>

{{-- MODAL: Confirm Delete --}}
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box" style="max-width:380px">
    <div class="text-center mb-6">
      <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
      <h3 class="text-lg font-bold text-slate-900">Confirm Delete</h3>
      <p class="text-sm text-slate-500 mt-1" id="deleteMsg">Are you sure?</p>
    </div>
    <div class="flex gap-3">
      <button onclick="closeModal('deleteModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="confirmDelete()" class="flex-1 bg-red-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors flex items-center justify-center">Delete</button>
    </div>
  </div>
</div>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
  <div class="px-5 py-4 border-b" style="border-color:rgba(255,255,255,0.08)">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-indigo-500 flex items-center justify-center flex-shrink-0"><svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
      <div><p class="text-white text-sm font-bold leading-tight">PointMS</p><p class="text-xs" style="color:#5c6080">School Point System</p></div>
    </div>
  </div>
  <div class="px-5 py-4 border-b" style="border-color:rgba(255,255,255,0.08)">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-300 font-bold text-xs flex-shrink-0">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div class="min-w-0"><p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p><span class="badge bg-indigo-500/20 text-indigo-300">{{ auth()->user()->role }}</span></div>
    </div>
  </div>
  <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
    <p class="text-xs uppercase tracking-widest font-semibold px-3 mb-2" style="color:#3d4060">Main</p>
    <a class="nav-item active" onclick="showPage('dashboard',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>Dashboard</a>
    <a class="nav-item" onclick="showPage('users',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Manage Users</a>
    <a class="nav-item" onclick="showPage('tasks',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Tasks</a>
    <a class="nav-item" onclick="showPage('points',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>Give Points</a>
    <a class="nav-item" onclick="showPage('subjects',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/></svg>Subjects</a>
    <a class="nav-item" onclick="showPage('reports',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>Reports</a>
  </nav>
  <div class="px-3 pb-5">
    <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logoutForm').submit();" class="nav-item hover:!bg-red-500/10 hover:!text-red-400"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Logout</a>
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
  </div>
</aside>

{{-- MAIN --}}
<div class="main-content">
  <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-20">
    <div class="flex items-center gap-4">
      <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 lg:hidden"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <div><h1 class="text-base font-bold text-slate-900" id="pageTitle">Dashboard</h1><p class="text-xs text-slate-400">Welcome back, {{ auth()->user()->name }}</p></div>
    </div>
    <div class="flex items-center gap-2">
      <button class="relative p-2 rounded-lg hover:bg-slate-100 text-slate-500"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg><span class="notif-dot"></span></button>
      <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
    </div>
  </header>
  <div class="p-6">

    {{-- DASHBOARD PAGE --}}
    <div id="page-dashboard" class="page active">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="card p-5"><div class="flex items-start justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg></div><span class="badge bg-green-50 text-green-600">+{{ $newStudentsMonth }} this month</span></div><p class="stat-num text-slate-900">{{ $totalStudents }}</p><p class="text-sm text-slate-500 mt-0.5">Total Students</p></div>
        <div class="card p-5"><div class="flex items-start justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center"><svg class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div><span class="badge bg-green-50 text-green-600">+{{ $newTeachersMonth }} this month</span></div><p class="stat-num text-slate-900">{{ $totalTeachers }}</p><p class="text-sm text-slate-500 mt-0.5">Total Teachers</p></div>
        <div class="card p-5"><div class="flex items-start justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center"><svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div><span class="badge bg-green-50 text-green-600">+{{ $pointsToday }} today</span></div><p class="stat-num text-slate-900">{{ number_format($totalPoints) }}</p><p class="text-sm text-slate-500 mt-0.5">Total Points Given</p></div>
        <div class="card p-5"><div class="flex items-start justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div><span class="badge bg-slate-100 text-slate-500">Active</span></div><p class="stat-num text-slate-900">{{ $totalTasks }}</p><p class="text-sm text-slate-500 mt-0.5">Total Tasks</p></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="card lg:col-span-2 overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between"><h2 class="font-semibold text-slate-800 text-sm">Recent Point Activity</h2><a onclick="showPage('reports',document.querySelectorAll('.nav-item')[5])" class="text-xs text-indigo-600 hover:underline cursor-pointer font-semibold">View all</a></div>
          <div id="activityList"></div>
        </div>
        <div class="card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100"><h2 class="font-semibold text-slate-800 text-sm">Quick Actions</h2></div>
          <div class="p-4 space-y-2.5">
            <button onclick="openAddUser()" class="w-full flex items-center gap-3 p-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors text-sm font-semibold"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add New User</button>
            <button onclick="openPointModal()" class="w-full flex items-center gap-3 p-3 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition-colors text-sm font-semibold"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>Give Points</button>
            <button onclick="showPage('tasks',document.querySelectorAll('.nav-item')[2])" class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors text-sm font-semibold"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Manage Tasks</button>
            <button onclick="showPage('reports',document.querySelectorAll('.nav-item')[5])" class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors text-sm font-semibold"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>View Reports</button>
          </div>
        </div>
      </div>
    </div>

    {{-- USERS PAGE --}}
    <div id="page-users" class="page">
      <div class="flex items-center justify-between mb-5"><div><h2 class="text-lg font-bold text-slate-900">Manage Users</h2><p class="text-sm text-slate-500 mt-0.5">Add, edit, or remove system users</p></div><button onclick="openAddUser()" class="btn-primary"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add User</button></div>
      <div class="card p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1"><svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" id="userSearch" placeholder="Search by name or email" class="w-full pl-9 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
        <select id="roleFilter" class="px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"><option value="">All Roles</option><option value="admin">Admin</option><option value="teacher">Teacher</option><option value="student">Student</option><option value="parent">Parent</option></select>
      </div>
      <div class="card overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100 bg-slate-50/60"><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th><th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th></tr></thead><tbody id="usersTableBody"></tbody></table><div id="usersEmpty" class="hidden text-center py-14 text-slate-400"><p class="text-sm font-medium">No users found</p></div></div></div>
    </div>

    {{-- TASKS PAGE --}}
    <div id="page-tasks" class="page">
      <div class="flex items-center justify-between mb-5"><div><h2 class="text-lg font-bold text-slate-900">Manage Tasks</h2><p class="text-sm text-slate-500 mt-0.5">View and manage all tasks across teachers</p></div><button onclick="openAddTask()" class="btn-primary"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add Task</button></div>
      <div class="card overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100 bg-slate-50/60"><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Title</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Teacher</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Due Date</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Submissions</th><th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th></tr></thead><tbody id="tasksTableBody"></tbody></table><div id="tasksEmpty" class="hidden text-center py-14 text-slate-400"><p class="text-sm font-medium">No tasks yet</p></div></div></div>
    </div>

    {{-- GIVE POINTS PAGE --}}
    <div id="page-points" class="page">
      <div class="flex items-center justify-between mb-5"><div><h2 class="text-lg font-bold text-slate-900">Give Points</h2><p class="text-sm text-slate-500 mt-0.5">Manually award or deduct points from students</p></div><button onclick="openPointModal()" class="btn-primary"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Give Points</button></div>
      <div class="card overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100 bg-slate-50/60"><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Teacher / Subject</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Points</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th></tr></thead><tbody id="pointsTableBody"></tbody></table><div id="pointsEmpty" class="hidden text-center py-14 text-slate-400"><p class="text-sm font-medium">No points given yet</p></div></div></div>
    </div>

    {{-- SUBJECTS PAGE --}}
    <div id="page-subjects" class="page">
      <div class="flex items-center justify-between mb-5"><div><h2 class="text-lg font-bold text-slate-900">Subjects</h2><p class="text-sm text-slate-500 mt-0.5">Manage school subjects</p></div><button onclick="openAddSubject()" class="btn-primary"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add Subject</button></div>
      <div class="card overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100 bg-slate-50/60"><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject Name</th><th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th></tr></thead><tbody id="subjectsTableBody"></tbody></table><div id="subjectsEmpty" class="hidden text-center py-14 text-slate-400"><p class="text-sm font-medium">No subjects yet</p></div></div></div>
    </div>

    {{-- REPORTS PAGE --}}
    <div id="page-reports" class="page">
      <div class="flex items-center justify-between mb-5"><div><h2 class="text-lg font-bold text-slate-900">Reports</h2><p class="text-sm text-slate-500 mt-0.5">Full point transaction history</p></div><button class="btn-secondary"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Export CSV</button></div>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="card p-4 text-center"><p class="stat-num text-indigo-600" id="rTotal">--</p><p class="text-xs text-slate-500 mt-0.5">Total Points</p></div>
        <div class="card p-4 text-center"><p class="stat-num text-emerald-600" id="rAwarded">--</p><p class="text-xs text-slate-500 mt-0.5">Points Awarded</p></div>
        <div class="card p-4 text-center"><p class="stat-num text-red-500" id="rDeducted">--</p><p class="text-xs text-slate-500 mt-0.5">Points Deducted</p></div>
        <div class="card p-4 text-center"><p class="stat-num text-slate-900" id="rTransactions">--</p><p class="text-xs text-slate-500 mt-0.5">Transactions</p></div>
      </div>
      <div class="card p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1"><svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" id="reportSearch" placeholder="Search student or teacher" class="w-full pl-9 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"></div>
        <select id="reportTypeFilter" class="px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800"><option value="">All Types</option><option value="positive">Positive</option><option value="negative">Negative</option></select>
      </div>
      <div class="card overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100 bg-slate-50/60"><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Given By</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Points</th><th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th></tr></thead><tbody id="reportTableBody"></tbody></table><div id="reportsEmpty" class="hidden text-center py-14 text-slate-400"><p class="text-sm font-medium">No transactions found</p></div></div></div>
    </div>

  </div>
</div>

<script>
const recentActivity = @json($recentActivity);
const CSRF = '{{ csrf_token() }}';
const roleColors = { admin:'bg-indigo-50 text-indigo-600', teacher:'bg-violet-50 text-violet-600', student:'bg-blue-50 text-blue-600', parent:'bg-amber-50 text-amber-600' };

function initials(name) { return (name||'?').split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2); }
function fmtDate(d) { if (!d) return '--'; return new Date(d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}); }
async function apiFetch(url, opts={}) {
  const res = await fetch(url, { ...opts, headers:{ 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF, ...(opts.headers||{}) } });
  const text = await res.text();
  try { return { ok:res.ok, status:res.status, data:JSON.parse(text) }; }
  catch(e) { console.error('Non-JSON:',url,text.slice(0,200)); return { ok:false, data:{} }; }
}

const pageTitles = { dashboard:'Dashboard', users:'Manage Users', tasks:'Manage Tasks', points:'Give Points', subjects:'Subjects', reports:'Reports' };
function showPage(name, el) {
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('page-'+name).classList.add('active');
  if (el) el.classList.add('active');
  document.getElementById('pageTitle').textContent = pageTitles[name]||name;
  if (name==='dashboard') renderActivity();
  if (name==='users')     loadUsers();
  if (name==='tasks')     loadTasks();
  if (name==='points')    loadPoints();
  if (name==='subjects')  loadSubjects();
  if (name==='reports')   loadReports();
}

function renderActivity() {
  const list = document.getElementById('activityList');
  if (!recentActivity.length) { list.innerHTML='<div class="px-5 py-10 text-center text-slate-400 text-sm">No point activity yet.</div>'; return; }
  list.innerHTML = recentActivity.map(r=>`
    <div class="px-5 py-3.5 border-b border-slate-50 last:border-0 table-row" style="display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:12px">
        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs flex-shrink-0">${initials(r.student)}</div>
        <div><p class="text-sm font-semibold text-slate-800">${r.student}</p><p class="text-xs text-slate-400">by ${r.teacher}${r.category ? ' · ' + r.category : ''}</p></div>
      </div>
      <span class="mono text-sm font-bold flex-shrink-0" style="color:${r.type==='positive'?'#059669':'#ef4444'}">${r.points>0?'+':''}${r.points} pts</span>
    </div>`).join('');
}

// USERS
let users=[]; let editUserId=null;
async function loadUsers() {
  const q=(document.getElementById('userSearch').value||'').trim();
  const rf=document.getElementById('roleFilter').value;
  const p=new URLSearchParams(); if(q) p.append('search',q); if(rf) p.append('role',rf);
  const {ok,data}=await apiFetch(`/users?${p}`); if(ok){users=data;renderUsers();}
}
function renderUsers() {
  const tbody=document.getElementById('usersTableBody'); const empty=document.getElementById('usersEmpty');
  if (!users.length){tbody.innerHTML='';empty.classList.remove('hidden');return;} empty.classList.add('hidden');
  tbody.innerHTML=users.map(u=>{
    const safe=(u.name||'').replace(/'/g,"\\'"); const rc=roleColors[u.role]||roleColors.student;
    return `<tr class="table-row border-b border-slate-50 last:border-0">
      <td class="px-5 py-3.5"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold flex-shrink-0">${initials(u.name)}</div><span class="text-sm font-semibold text-slate-800">${u.name}</span></div></td>
      <td class="px-5 py-3.5 text-sm text-slate-500">${u.email}</td>
      <td class="px-5 py-3.5"><span class="tag ${rc}">${u.role}</span></td>
      <td class="px-5 py-3.5 text-sm text-slate-400 mono">${fmtDate(u.created_at)}</td>
      <td class="px-5 py-3.5 text-right"><div class="flex gap-2 justify-end"><button onclick="openEditUser(${u.id})" class="btn-edit">Edit</button><button onclick="askDelete('user',${u.id},'${safe}')" class="btn-danger">Delete</button></div></td>
    </tr>`;
  }).join('');
}
function openAddUser(){editUserId=null;document.getElementById('userModalTitle').textContent='Add New User';document.getElementById('userSaveBtn').textContent='Add User';['uName','uEmail','uPass'].forEach(id=>document.getElementById(id).value='');document.getElementById('uRole').value='';document.getElementById('passField').style.display='block';openModal('userModal');}
function openEditUser(id){editUserId=id;const u=users.find(x=>x.id===id);document.getElementById('userModalTitle').textContent='Edit User';document.getElementById('userSaveBtn').textContent='Save Changes';document.getElementById('uName').value=u.name;document.getElementById('uEmail').value=u.email;document.getElementById('uRole').value=u.role;document.getElementById('uPass').value='';document.getElementById('passField').style.display='none';openModal('userModal');}
async function saveUser(){
  const name=document.getElementById('uName').value.trim(); const email=document.getElementById('uEmail').value.trim(); const role=document.getElementById('uRole').value; const pass=document.getElementById('uPass').value;
  if(!name||!email||!role){alert('Please fill all fields.');return;} if(!editUserId&&!pass){alert('Password is required.');return;}
  const body={name,email,role}; if(!editUserId) body.password=pass;
  const {ok,data}=await apiFetch(editUserId?`/users/${editUserId}`:'/users',{method:editUserId?'PUT':'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  if(ok){closeModal('userModal');loadUsers();}else{alert(data.message||Object.values(data.errors||{})[0]?.[0]||'Something went wrong.');}
}
document.getElementById('userSearch').addEventListener('input',loadUsers);
document.getElementById('roleFilter').addEventListener('change',loadUsers);

// TASKS
let tasks=[]; let editTaskId=null;
async function loadTasks(){const {ok,data}=await apiFetch('/admin/tasks');if(ok){tasks=data;renderTasks();}}
function renderTasks(){
  const tbody=document.getElementById('tasksTableBody'); const empty=document.getElementById('tasksEmpty');
  if(!tasks.length){tbody.innerHTML='';empty.classList.remove('hidden');return;} empty.classList.add('hidden');
  tbody.innerHTML=tasks.map(t=>{
    const safe=(t.title||'').replace(/'/g,"\\'"); const expired=t.due_date&&new Date(t.due_date)<new Date();
    return `<tr class="table-row border-b border-slate-50 last:border-0">
      <td class="px-5 py-3.5"><p class="text-sm font-semibold text-slate-800">${t.title}</p><p class="text-xs text-slate-400 mt-0.5 max-w-xs truncate">${t.description||'--'}</p></td>
      <td class="px-5 py-3.5 text-sm text-slate-600">${t.teacher_subject?.teacher?.user?.name||'--'}</td>
      <td class="px-5 py-3.5 text-sm text-slate-600">${t.teacher_subject?.subject?.name||'--'}</td>
      <td class="px-5 py-3.5 text-sm mono ${expired?'text-red-500':'text-slate-400'}">${fmtDate(t.due_date)}</td>
      <td class="px-5 py-3.5"><span class="tag bg-slate-100 text-slate-600">${t.submissions_count||0} students</span></td>
      <td class="px-5 py-3.5 text-right"><div class="flex gap-2 justify-end">
        <button onclick="openDrawer(${t.id})" class="btn-secondary" style="padding:7px 14px;font-size:12.5px">View</button>
        <button onclick="openEditTask(${t.id})" class="btn-edit">Edit</button>
        <button onclick="askDelete('task',${t.id},'${safe}')" class="btn-danger">Delete</button>
      </div></td>
    </tr>`;
  }).join('');
}
async function fillTSSelect(id){const {ok,data}=await apiFetch('/admin/teacher-subjects');if(!ok)return;document.getElementById(id).innerHTML='<option value="">-- Select --</option>'+data.map(ts=>`<option value="${ts.id}">${ts.teacher||'?'} -- ${ts.subject||'?'} (${ts.class||'?'})</option>`).join('');}
async function openAddTask(){editTaskId=null;document.getElementById('taskModalTitle').textContent='Add New Task';document.getElementById('taskSaveBtn').textContent='Add Task';['tTitle','tDesc','tDueDate'].forEach(id=>document.getElementById(id).value='');document.getElementById('tMaxGrade').value='100';await fillTSSelect('tTeacherSubject');openModal('taskModal');}
async function openEditTask(id){editTaskId=id;const t=tasks.find(x=>x.id===id);document.getElementById('taskModalTitle').textContent='Edit Task';document.getElementById('taskSaveBtn').textContent='Save Changes';document.getElementById('tTitle').value=t.title;document.getElementById('tDesc').value=t.description||'';document.getElementById('tDueDate').value=t.due_date||'';document.getElementById('tMaxGrade').value=t.max_grade;await fillTSSelect('tTeacherSubject');document.getElementById('tTeacherSubject').value=t.teacher_subject_id;openModal('taskModal');}
async function saveTask(){
  const title=document.getElementById('tTitle').value.trim(); const desc=document.getElementById('tDesc').value.trim(); const ts=document.getElementById('tTeacherSubject').value; const due=document.getElementById('tDueDate').value; const mg=document.getElementById('tMaxGrade').value;
  if(!title||!ts){alert('Title and Teacher Subject are required.');return;}
  const {ok,data}=await apiFetch(editTaskId?`/admin/tasks/${editTaskId}`:'/admin/tasks',{method:editTaskId?'PUT':'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({title,description:desc,teacher_subject_id:ts,due_date:due||null,max_grade:mg})});
  if(ok){closeModal('taskModal');loadTasks();}else{alert(data.message||'Something went wrong.');}
}

// GIVE POINTS
async function openPointModal(){
  const {ok:sOk,data:students}=await apiFetch('/admin/students-for-points');
  if(sOk){document.getElementById('pStudent').innerHTML='<option value="">-- Select Student --</option>'+students.map(s=>`<option value="${s.id}">${s.name} (${s.class})</option>`).join('');}
  await fillTSSelect('pTeacherSubject');
  document.getElementById('pDate').value=new Date().toISOString().split('T')[0];
  document.getElementById('pPoints').value=''; document.getElementById('pDesc').value='';
  openModal('pointModal');
}
async function savePoint(){
  const student_id=document.getElementById('pStudent').value; const teacher_subject_id=document.getElementById('pTeacherSubject').value; const point=document.getElementById('pPoints').value; const description=document.getElementById('pDesc').value.trim(); const point_date=document.getElementById('pDate').value;
  if(!student_id||!teacher_subject_id||!point){alert('Please fill Student, Teacher Subject, and Points.');return;} if(point==0){alert('Points cannot be zero.');return;}
  const payload={student_id,teacher_subject_id,point,description,point_date};
  if(_pointFromSubmissionId) payload.task_submission_id=_pointFromSubmissionId;
  const {ok,data}=await apiFetch('/admin/points',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
  if(ok){
    closeModal('pointModal');
    loadPoints();
    // If came from drawer, refresh the submissions list to show updated status
    if(_pointFromSubmissionId && drawerTaskId) openDrawer(drawerTaskId);
    _pointFromSubmissionId=null;
  }else{alert(data.message||'Something went wrong.');}
}
async function loadPoints(){
  const {ok,data}=await apiFetch('/reports'); if(!ok) return;
  const tbody=document.getElementById('pointsTableBody'); const empty=document.getElementById('pointsEmpty');
  if(!data.length){tbody.innerHTML='';empty.classList.remove('hidden');return;} empty.classList.add('hidden');
  tbody.innerHTML=data.map((r,i)=>`
    <tr class="table-row border-b border-slate-50 last:border-0">
      <td class="px-5 py-3.5 mono text-xs text-slate-400">${String(i+1).padStart(2,'0')}</td>
      <td class="px-5 py-3.5"><div class="flex items-center gap-2.5"><div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">${initials(r.student)}</div><span class="text-sm font-semibold text-slate-800">${r.student}</span></div></td>
      <td class="px-5 py-3.5 text-sm text-slate-500">${r.teacher} · ${r.subject||'--'}</td>
      <td class="px-5 py-3.5 text-sm text-slate-400 max-w-xs truncate">${r.description||'--'}</td>
      <td class="px-5 py-3.5 mono font-bold text-sm ${r.type==='positive'?'text-emerald-600':'text-red-500'}">${r.points>0?'+':''} ${r.points}</td>
      <td class="px-5 py-3.5 text-sm text-slate-400 mono">${fmtDate(r.date)}</td>
    </tr>`).join('');
}

// SUBJECTS
let subjects=[]; let editSubjectId=null;
async function loadSubjects(){const {ok,data}=await apiFetch('/subjects');if(ok){subjects=data;renderSubjects();}}
function renderSubjects(){
  const tbody=document.getElementById('subjectsTableBody'); const empty=document.getElementById('subjectsEmpty');
  if(!subjects.length){tbody.innerHTML='';empty.classList.remove('hidden');return;} empty.classList.add('hidden');
  tbody.innerHTML=subjects.map(s=>{
    const safe=(s.name||'').replace(/'/g,"\\'");
    return `<tr class="table-row border-b border-slate-50 last:border-0">
      <td class="px-5 py-3.5"><div class="flex items-center gap-3"><div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0"><svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/></svg></div><span class="text-sm font-semibold text-slate-800">${s.name}</span></div></td>
      <td class="px-5 py-3.5 text-right"><div class="flex gap-2 justify-end"><button onclick="openEditSubject(${s.id})" class="btn-edit">Edit</button><button onclick="askDelete('subject',${s.id},'${safe}')" class="btn-danger">Delete</button></div></td>
    </tr>`;
  }).join('');
}
function openAddSubject(){editSubjectId=null;document.getElementById('subjectModalTitle').textContent='Add Subject';document.getElementById('subjectSaveBtn').textContent='Add Subject';document.getElementById('sName').value='';openModal('subjectModal');}
function openEditSubject(id){editSubjectId=id;const s=subjects.find(x=>x.id===id);document.getElementById('subjectModalTitle').textContent='Edit Subject';document.getElementById('subjectSaveBtn').textContent='Save Changes';document.getElementById('sName').value=s.name;openModal('subjectModal');}
async function saveSubject(){
  const name=document.getElementById('sName').value.trim(); if(!name){alert('Please enter a subject name.');return;}
  const {ok,data}=await apiFetch(editSubjectId?`/subjects/${editSubjectId}`:'/subjects',{method:editSubjectId?'PUT':'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name})});
  if(ok){closeModal('subjectModal');loadSubjects();}else{alert(data.message||'Something went wrong.');}
}

// REPORTS
async function loadReports(){
  const {ok:sOk,data:stats}=await apiFetch('/reports/stats');
  if(sOk){document.getElementById('rTotal').textContent=Number(stats.total||0).toLocaleString();document.getElementById('rAwarded').textContent=Number(stats.awarded||0).toLocaleString();document.getElementById('rDeducted').textContent=Number(stats.deducted||0).toLocaleString();document.getElementById('rTransactions').textContent=stats.transactions||0;}
  const q=(document.getElementById('reportSearch').value||'').trim(); const type=document.getElementById('reportTypeFilter').value;
  const p=new URLSearchParams(); if(q) p.append('search',q); if(type) p.append('type',type);
  const {ok,data}=await apiFetch(`/reports?${p}`); if(ok) renderReports(data);
}
function renderReports(data){
  const empty=document.getElementById('reportsEmpty'); const tbody=document.getElementById('reportTableBody');
  if(!data.length){tbody.innerHTML='';empty.classList.remove('hidden');return;} empty.classList.add('hidden');
  tbody.innerHTML=data.map((r,i)=>`
    <tr class="table-row border-b border-slate-50 last:border-0">
      <td class="px-5 py-3.5 mono text-xs text-slate-400">${String(i+1).padStart(2,'0')}</td>
      <td class="px-5 py-3.5"><div class="flex items-center gap-2.5"><div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">${initials(r.student)}</div><span class="text-sm font-semibold text-slate-800">${r.student}</span></div></td>
      <td class="px-5 py-3.5 text-sm text-slate-500">${r.teacher}</td>
      <td class="px-5 py-3.5 text-sm text-slate-500">${r.subject||'--'}</td>
      <td class="px-5 py-3.5 text-sm text-slate-400 max-w-xs truncate">${r.description||'--'}</td>
      <td class="px-5 py-3.5 mono font-bold text-sm ${r.type==='positive'?'text-emerald-600':'text-red-500'}">${r.points>0?'+':''} ${r.points}</td>
      <td class="px-5 py-3.5 text-sm text-slate-400 mono">${fmtDate(r.date)}</td>
    </tr>`).join('');
}
document.getElementById('reportSearch').addEventListener('input',loadReports);
document.getElementById('reportTypeFilter').addEventListener('change',loadReports);

// DELETE
let deleteTarget=null;
function askDelete(type,id,name){deleteTarget={type,id};document.getElementById('deleteMsg').textContent=`Delete "${name}"? This action cannot be undone.`;openModal('deleteModal');}
async function confirmDelete(){
  if(!deleteTarget) return;
  const urls={user:`/users/${deleteTarget.id}`,task:`/admin/tasks/${deleteTarget.id}`,subject:`/subjects/${deleteTarget.id}`};
  const {ok,data}=await apiFetch(urls[deleteTarget.type],{method:'DELETE'});
  closeModal('deleteModal');
  if(ok){if(deleteTarget.type==='user')loadUsers();if(deleteTarget.type==='task')loadTasks();if(deleteTarget.type==='subject')loadSubjects();}
  else{alert(data.message||'Could not delete.');}
}

function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',function(e){if(e.target===this)this.classList.remove('open');}));
function toggleSidebar(){const s=document.getElementById('sidebar');s.style.transform=(s.style.transform==='translateX(0px)'||!s.style.transform)?'translateX(-240px)':'translateX(0px)';}

// ===================== TASK DRAWER =====================
// Built via createElement — zero HTML in the page, zero Tailwind interference
let drawerTaskId = null;

const _bd = document.createElement('div');
_bd.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:199;cursor:pointer';
_bd.addEventListener('click', closeDrawer);
document.body.appendChild(_bd);

const _dw = document.createElement('div');
_dw.style.cssText = 'display:none;position:fixed;top:0;right:0;bottom:0;width:560px;max-width:100vw;background:#fff;box-shadow:-8px 0 40px rgba(0,0,0,0.14);z-index:200;flex-direction:column;font-family:Syne,sans-serif;transition:transform .3s ease;transform:translateX(100%)';
_dw.innerHTML = [
  '<div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;background:#fff">',
    '<div style="min-width:0"><h2 id="dTitle" style="margin:0;font-size:15px;font-weight:700;color:#1e2130;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Task</h2><p id="dSub" style="margin:2px 0 0;font-size:11px;color:#94a3b8"></p></div>',
    '<div style="display:flex;gap:8px;margin-left:12px;flex-shrink:0">',
      '<button id="dEditBtn" onclick="openEditTaskFromDrawer()" style="background:#f0f1ff;color:#6366f1;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;border:1px solid #e0e2ff;cursor:pointer;font-family:Syne,sans-serif">Edit</button>',
      '<button onclick="closeDrawer()" style="width:32px;height:32px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;font-size:20px;color:#64748b;line-height:32px;text-align:center">&times;</button>',
    '</div>',
  '</div>',
  '<div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;background:#f8fafc;flex-shrink:0">',
    '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">',
      '<div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid #e8eaef"><p style="margin:0 0 3px;font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em">Teacher</p><p id="dTeacher" style="margin:0;font-size:13px;font-weight:600;color:#1e2130">--</p></div>',
      '<div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid #e8eaef"><p style="margin:0 0 3px;font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em">Subject</p><p id="dSubject" style="margin:0;font-size:13px;font-weight:600;color:#1e2130">--</p></div>',
      '<div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid #e8eaef"><p style="margin:0 0 3px;font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em">Due Date</p><p id="dDue" style="margin:0;font-size:13px;font-weight:600;color:#1e2130">--</p></div>',
      '<div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid #e8eaef"><p style="margin:0 0 3px;font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em">Max Grade</p><p id="dGrade" style="margin:0;font-size:13px;font-weight:600;color:#1e2130">--</p></div>',
    '</div>',
    '<div style="background:#fff;border-radius:10px;padding:10px 14px;border:1px solid #e8eaef;margin-top:8px"><p style="margin:0 0 3px;font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em">Description</p><p id="dDesc" style="margin:0;font-size:13px;color:#475569">--</p></div>',
  '</div>',
  '<div style="padding:10px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0">',
    '<span style="font-size:13px;font-weight:700;color:#374151">Submissions</span>',
    '<span id="dCount" style="font-size:11px;font-weight:600;padding:2px 10px;border-radius:6px;background:#f1f5f9;color:#64748b">--</span>',
  '</div>',
  '<div id="dList" style="flex:1;overflow-y:auto"><div style="padding:48px 24px;text-align:center;color:#94a3b8;font-size:13px">Select a task to view.</div></div>'
].join('');
document.body.appendChild(_dw);

async function openDrawer(taskId) {
  drawerTaskId = taskId;
  const t = tasks.find(x => x.id === taskId);
  if (!t) return;
  document.getElementById('dTitle').textContent   = t.title;
  document.getElementById('dSub').textContent     = 'Task #' + t.id;
  document.getElementById('dTeacher').textContent = t.teacher_subject?.teacher?.user?.name || '--';
  document.getElementById('dSubject').textContent = t.teacher_subject?.subject?.name || '--';
  document.getElementById('dDue').textContent     = fmtDate(t.due_date);
  document.getElementById('dGrade').textContent   = t.max_grade + ' pts';
  document.getElementById('dDesc').textContent    = t.description || 'No description.';
  document.getElementById('dList').innerHTML      = '<div style="padding:48px 24px;text-align:center;color:#94a3b8;font-size:13px">Loading...</div>';
  document.getElementById('dCount').textContent   = '...';
  _bd.style.display = 'block';
  _dw.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  requestAnimationFrame(() => { _dw.style.transform = 'translateX(0)'; });
  const {ok, data} = await apiFetch('/admin/tasks/' + taskId + '/submissions');
  if (!ok) { document.getElementById('dList').innerHTML = '<div style="padding:48px 24px;text-align:center;color:#ef4444;font-size:13px">Failed to load.</div>'; return; }
  document.getElementById('dCount').textContent = data.length + ' student' + (data.length !== 1 ? 's' : '');
  if (!data.length) { document.getElementById('dList').innerHTML = '<div style="padding:48px 24px;text-align:center;color:#94a3b8;font-size:13px">No students have taken this task yet.</div>'; return; }
  const sc = {taken:'background:#f1f5f9;color:#64748b',submitted:'background:#eff6ff;color:#2563eb',correcting:'background:#fffbeb;color:#d97706',graded:'background:#ecfdf5;color:#059669'};
  document.getElementById('dList').innerHTML = data.map(s => {
    const st = sc[s.status] || sc.taken;
    const gr = s.grade !== null ? `<span style="font-family:monospace;font-weight:700;color:#059669">${s.grade}/${t.max_grade}</span>` : '<span style="font-size:12px;color:#94a3b8">Not graded</span>';
    const fb = s.feedback ? `<p style="margin:4px 0 0 42px;font-size:11px;color:#94a3b8;font-style:italic">"${s.feedback}"</p>` : '';
    const su = s.submitted_at ? `<p style="margin:2px 0 0 42px;font-size:11px;color:#cbd5e1">Submitted ${fmtDate(s.submitted_at)}</p>` : '';
    const awardBtn = `<button onclick="openPointModalFromDrawer(${s.student_id},${t.teacher_subject_id},${s.id})" style="background:#f0f1ff;color:#6366f1;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;border:1px solid #e0e2ff;cursor:pointer;font-family:Syne,sans-serif">+ Points</button>`;
    return `<div style="padding:12px 24px;border-bottom:1px solid #f8fafc"><div style="display:flex;align-items:center;justify-content:space-between"><div style="display:flex;align-items:center;gap:10px"><div style="width:32px;height:32px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#64748b;flex-shrink:0">${initials(s.student_name)}</div><div><p style="margin:0;font-size:13px;font-weight:600;color:#1e2130">${s.student_name}</p><p style="margin:1px 0 0;font-size:11px;color:#94a3b8">${s.class||'--'}</p></div></div><div style="display:flex;align-items:center;gap:8px">${gr}<span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:6px;${st}">${s.status}</span>${awardBtn}</div></div>${fb}${su}</div>`;
  }).join('');
}

let _pointFromSubmissionId = null;
async function openPointModalFromDrawer(studentId, teacherSubjectId, submissionId) {
  _pointFromSubmissionId = submissionId || null;
  // Load students dropdown
  const {ok:sOk,data:students} = await apiFetch('/admin/students-for-points');
  if (sOk) {
    document.getElementById('pStudent').innerHTML =
      '<option value="">-- Select Student --</option>' +
      students.map(s => `<option value="${s.id}">${s.name} (${s.class})</option>`).join('');
    document.getElementById('pStudent').value = studentId;
  }
  // Load teacher subjects dropdown
  await fillTSSelect('pTeacherSubject');
  document.getElementById('pTeacherSubject').value = teacherSubjectId;
  // Set today's date
  document.getElementById('pDate').value = new Date().toISOString().split('T')[0];
  document.getElementById('pPoints').value = '';
  document.getElementById('pDesc').value = '';
  openModal('pointModal');
}

function closeDrawer() {
  _dw.style.transform = 'translateX(100%)';
  _bd.style.display = 'none';
  document.body.style.overflow = '';
  drawerTaskId = null;
  setTimeout(() => { _dw.style.display = 'none'; }, 300);
}

function openEditTaskFromDrawer() {
  if (drawerTaskId) { const id = drawerTaskId; closeDrawer(); setTimeout(() => openEditTask(id), 310); }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

renderActivity();
</script>
</body>
</html>