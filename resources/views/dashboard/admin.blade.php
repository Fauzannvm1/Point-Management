<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard – PointMS</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --sidebar-bg: #0f1117;
    --accent: #6366f1;
    --accent-glow: rgba(99,102,241,0.15);
  }
  * { box-sizing: border-box; }
  body { font-family: 'Syne', sans-serif; background: #f4f5f9; color: #1e2130; }
  .mono { font-family: 'JetBrains Mono', monospace; }
  .sidebar { background: var(--sidebar-bg); width: 240px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 40; display: flex; flex-direction: column; transition: transform 0.25s ease; }
  .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 14px; border-radius: 10px; font-size: 13.5px; font-weight: 500; color: #7c8099; cursor: pointer; transition: all 0.15s; text-decoration: none; }
  .nav-item:hover { background: rgba(255,255,255,0.06); color: #e2e4f0; transform: translateX(3px); }
  .nav-item.active { background: var(--accent-glow); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.25); }
  .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }
  .card { background: white; border-radius: 16px; border: 1px solid #e8eaef; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
  .badge { font-family: 'JetBrains Mono', monospace; font-size: 10px; padding: 2px 8px; border-radius: 6px; font-weight: 600; }
  .stat-num { font-family: 'JetBrains Mono', monospace; font-size: 30px; font-weight: 600; }
  .page { display: none; }
  .page.active { display: block; }
  .main-content { margin-left: 240px; min-height: 100vh; }
  .btn-primary { background: #6366f1; color: white; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.15s; font-family: 'Syne', sans-serif; }
  .btn-primary:hover { background: #4f51de; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
  .btn-secondary { background: #f1f2f6; color: #4a5070; padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; border: 1px solid #e0e2ea; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.15s; font-family: 'Syne', sans-serif; }
  .btn-secondary:hover { background: #e8e9f0; }
  .btn-danger { background: #fef2f2; color: #ef4444; padding: 7px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 600; border: 1px solid #fee2e2; cursor: pointer; transition: all 0.15s; font-family: 'Syne', sans-serif; }
  .btn-danger:hover { background: #ef4444; color: white; }
  .btn-edit { background: #f0f1ff; color: #6366f1; padding: 7px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 600; border: 1px solid #e0e2ff; cursor: pointer; transition: all 0.15s; font-family: 'Syne', sans-serif; }
  .btn-edit:hover { background: #6366f1; color: white; }
  input, select, textarea { font-family: 'Syne', sans-serif; outline: none; transition: border 0.15s, box-shadow 0.15s; }
  input:focus, select:focus, textarea:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
  .table-row:hover { background: #f9fafb; }
  .modal-overlay { position: fixed; inset: 0; background: rgba(15,17,23,0.6); backdrop-filter: blur(4px); z-index: 100; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.2s; }
  .modal-overlay.open { opacity: 1; pointer-events: all; }
  .modal-box { background: white; border-radius: 20px; padding: 28px; width: 100%; max-width: 480px; transform: translateY(16px); transition: transform 0.2s; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
  .modal-overlay.open .modal-box { transform: translateY(0); }
  .tag { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 7px; font-size: 12px; font-weight: 600; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
  .page.active { animation: fadeIn 0.2s ease; }
  .notif-dot { width: 7px; height: 7px; background: #ef4444; border-radius: 50%; position: absolute; top: 6px; right: 6px; }
  ::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: transparent; } ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
</style>
</head>
<body>

<!-- MODAL: Add/Edit User -->
<div class="modal-overlay" id="userModal">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-bold text-slate-900" id="userModalTitle">Add New User</h3>
      <button onclick="closeModal('userModal')" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-600 font-semibold text-slate-700 mb-1.5">Full Name</label>
        <input type="text" id="uName" placeholder="e.g. Ahmad Riski" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
        <input type="email" id="uEmail" placeholder="user@school.edu" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role</label>
        <select id="uRole" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
          <option value="">-- Select Role --</option>
          <option value="admin">Admin</option>
          <option value="teacher">Teacher</option>
          <option value="student">Student</option>
          <option value="parent">Parent</option>
        </select>
      </div>
      <div id="passField">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
        <input type="password" id="uPass" placeholder="Min. 8 characters" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('userModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="saveUser()" class="btn-primary flex-1 justify-center" id="userSaveBtn">Add User</button>
    </div>
  </div>
</div>

<!-- MODAL: Add/Edit Category -->
<div class="modal-overlay" id="catModal">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-bold text-slate-900" id="catModalTitle">Add Category</h3>
      <button onclick="closeModal('catModal')" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Category Name</label>
        <input type="text" id="cName" placeholder="e.g. Academic Excellence" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Type</label>
          <select id="cType" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
            <option value="positive">Positive</option>
            <option value="negative">Negative</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Default Points</label>
          <input type="number" id="cPoints" placeholder="e.g. 50" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
        <textarea id="cDesc" rows="2" placeholder="Brief description..." class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800 resize-none"></textarea>
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Color</label>
        <div class="flex gap-2" id="colorPicker">
          <span class="color-opt w-7 h-7 rounded-lg cursor-pointer border-2 border-transparent transition-all" style="background:#6366f1" data-color="indigo" onclick="pickColor(this)"></span>
          <span class="color-opt w-7 h-7 rounded-lg cursor-pointer border-2 border-transparent transition-all" style="background:#f59e0b" data-color="amber" onclick="pickColor(this)"></span>
          <span class="color-opt w-7 h-7 rounded-lg cursor-pointer border-2 border-transparent transition-all" style="background:#10b981" data-color="emerald" onclick="pickColor(this)"></span>
          <span class="color-opt w-7 h-7 rounded-lg cursor-pointer border-2 border-transparent transition-all" style="background:#ef4444" data-color="red" onclick="pickColor(this)"></span>
          <span class="color-opt w-7 h-7 rounded-lg cursor-pointer border-2 border-transparent transition-all" style="background:#3b82f6" data-color="blue" onclick="pickColor(this)"></span>
          <span class="color-opt w-7 h-7 rounded-lg cursor-pointer border-2 border-transparent transition-all" style="background:#8b5cf6" data-color="violet" onclick="pickColor(this)"></span>
        </div>
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('catModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="saveCat()" class="btn-primary flex-1 justify-center" id="catSaveBtn">Add Category</button>
    </div>
  </div>
</div>

<!-- MODAL: Confirm Delete -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box" style="max-width:380px">
    <div class="text-center mb-6">
      <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      </div>
      <h3 class="text-lg font-bold text-slate-900">Confirm Delete</h3>
      <p class="text-sm text-slate-500 mt-1" id="deleteMsg">Are you sure you want to delete this item?</p>
    </div>
    <div class="flex gap-3">
      <button onclick="closeModal('deleteModal')" class="btn-secondary flex-1 justify-center">Cancel</button>
      <button onclick="confirmDelete()" class="flex-1 justify-center bg-red-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors flex items-center justify-center gap-2">Delete</button>
    </div>
  </div>
</div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="px-5 py-4 border-b border-white/8" style="border-color:rgba(255,255,255,0.08)">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-indigo-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
      </div>
      <div>
        <p class="text-white text-sm font-bold leading-tight">PointMS</p>
        <p class="text-xs" style="color:#5c6080">School Point System</p>
      </div>
    </div>
  </div>

  <div class="px-5 py-4 border-b" style="border-color:rgba(255,255,255,0.08)">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-300 font-bold text-xs flex-shrink-0">A</div>
      <div class="min-w-0">
        <p class="text-white text-sm font-semibold truncate">Admin User</p>
        <span class="badge bg-indigo-500/20 text-indigo-300">admin</span>
      </div>
    </div>
  </div>

  <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
    <p class="text-xs uppercase tracking-widest font-semibold px-3 mb-2" style="color:#3d4060">Main</p>
    <a class="nav-item active" onclick="showPage('dashboard', this)">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
      Dashboard
    </a>
    <a class="nav-item" onclick="showPage('users', this)">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Manage Users
    </a>
    <a class="nav-item" onclick="showPage('categories', this)">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
      Point Categories
    </a>
    <a class="nav-item" onclick="showPage('reports', this)">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Reports
    </a>
  </nav>

  <div class="px-3 pb-5">
    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();" class="nav-item hover:!bg-red-500/10 hover:!text-red-400">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Logout
    </a>
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
  </div>
</aside>

<!-- MAIN -->
<div class="main-content">

  <!-- Header -->
  <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-20">
    <div class="flex items-center gap-4">
      <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 lg:hidden">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <div>
        <h1 class="text-base font-bold text-slate-900" id="pageTitle">Dashboard</h1>
        <p class="text-xs text-slate-400">Welcome back, Admin User</p>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <button class="relative p-2 rounded-lg hover:bg-slate-100 text-slate-500">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span class="notif-dot"></span>
      </button>
      <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xs">A</div>
    </div>
  </header>

  <div class="p-6">

    <!-- =================== DASHBOARD PAGE =================== -->
    <div id="page-dashboard" class="page active">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="card p-5">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <span class="badge bg-green-50 text-green-600">+{{ $newStudentsMonth }} this month</span>
          </div>
          <p class="stat-num text-slate-900">{{ $totalStudents }}</p>
          <p class="text-sm text-slate-500 mt-0.5">Total Students</p>
        </div>
        <div class="card p-5">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="badge bg-green-50 text-green-600">+{{ $newTeachersMonth }} this month</span>
          </div>
          <p class="stat-num text-slate-900">{{ $totalTeachers }}</p>
          <p class="text-sm text-slate-500 mt-0.5">Total Teachers</p>
        </div>
        <div class="card p-5">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <span class="badge bg-green-50 text-green-600">+231 today</span>
          </div>
          <p class="stat-num text-slate-900">{{ number_format($totalPoints) }}</p>
          <p class="text-sm text-slate-500 mt-0.5">Total Points Given</p>
        </div>
        <div class="card p-5">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <span class="badge bg-slate-100 text-slate-500">Active</span>
          </div>
          <p class="stat-num text-slate-900">{{ $totalCategories }}</p>
          <p class="text-sm text-slate-500 mt-0.5">Point Categories</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="card lg:col-span-2 overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800 text-sm">Recent Point Activity</h2>
            <a onclick="showPage('reports', document.querySelector('.nav-item:nth-child(4)'))" class="text-xs text-indigo-600 hover:underline cursor-pointer font-semibold">View all →</a>
          </div>
          <div id="activityList"></div>
        </div>
        <div class="card overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800 text-sm">Quick Actions</h2>
          </div>
          <div class="p-4 space-y-2.5">
            <button onclick="openModal('userModal')" class="w-full flex items-center gap-3 p-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors text-sm font-semibold">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add New User
            </button>
            <button onclick="showPage('categories', document.querySelectorAll('.nav-item')[2])" class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors text-sm font-semibold">
              <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>Manage Categories
            </button>
            <button onclick="showPage('reports', document.querySelectorAll('.nav-item')[3])" class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors text-sm font-semibold">
              <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>View Reports
            </button>
            <button onclick="showPage('users', document.querySelectorAll('.nav-item')[1])" class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors text-sm font-semibold">
              <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Manage Users
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- =================== USERS PAGE =================== -->
    <div id="page-users" class="page">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-lg font-bold text-slate-900">Manage Users</h2>
          <p class="text-sm text-slate-500 mt-0.5">Add, edit, or remove system users</p>
        </div>
        <button onclick="openAddUser()" class="btn-primary">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Add User
        </button>
      </div>

      <!-- Filters -->
      <div class="card p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input type="text" id="userSearch" placeholder="Search by name or email…" oninput="renderUsers()" class="w-full pl-9 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
        </div>
        <select id="roleFilter" onchange="renderUsers()" class="px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="teacher">Teacher</option>
          <option value="student">Student</option>
          <option value="parent">Parent</option>
        </select>
      </div>

      <div class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50/60">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody id="usersTableBody"></tbody>
          </table>
          <div id="usersEmpty" class="hidden text-center py-14 text-slate-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-sm font-medium">No users found</p>
          </div>
        </div>
      </div>
    </div>

    <!-- =================== CATEGORIES PAGE =================== -->
    <div id="page-categories" class="page">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-lg font-bold text-slate-900">Point Categories</h2>
          <p class="text-sm text-slate-500 mt-0.5">Manage point types awarded to students</p>
        </div>
        <button onclick="openAddCat()" class="btn-primary">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Add Category
        </button>
      </div>

      <!-- Summary cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        <div class="card p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-bold mono" id="catTotalNum">8</div>
          <div><p class="text-sm font-semibold text-slate-800">Total Categories</p><p class="text-xs text-slate-400">All types combined</p></div>
        </div>
        <div class="card p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold mono" id="catPosNum">6</div>
          <div><p class="text-sm font-semibold text-slate-800">Positive</p><p class="text-xs text-slate-400">Reward categories</p></div>
        </div>
        <div class="card p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 font-bold mono" id="catNegNum">2</div>
          <div><p class="text-sm font-semibold text-slate-800">Negative</p><p class="text-xs text-slate-400">Penalty categories</p></div>
        </div>
      </div>

      <div class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50/60">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Default Pts</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody id="catTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- =================== REPORTS PAGE =================== -->
    <div id="page-reports" class="page">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-lg font-bold text-slate-900">Reports</h2>
          <p class="text-sm text-slate-500 mt-0.5">Full point transaction history</p>
        </div>
        <div class="flex gap-2">
          <button class="btn-secondary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
          </button>
        </div>
      </div>

      <!-- Report Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="card p-4 text-center">
          <p class="stat-num text-indigo-600">4,820</p>
          <p class="text-xs text-slate-500 mt-0.5">Total Points</p>
        </div>
        <div class="card p-4 text-center">
          <p class="stat-num text-emerald-600">4,210</p>
          <p class="text-xs text-slate-500 mt-0.5">Points Awarded</p>
        </div>
        <div class="card p-4 text-center">
          <p class="stat-num text-red-500">610</p>
          <p class="text-xs text-slate-500 mt-0.5">Points Deducted</p>
        </div>
        <div class="card p-4 text-center">
          <p class="stat-num text-slate-900">156</p>
          <p class="text-xs text-slate-500 mt-0.5">Transactions</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="card p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input type="text" id="reportSearch" placeholder="Search student or teacher…" oninput="renderReports()" class="w-full pl-9 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
        </div>
        <select id="reportCatFilter" onchange="renderReports()" class="px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
          <option value="">All Categories</option>
        </select>
        <select id="reportTypeFilter" onchange="renderReports()" class="px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-800">
          <option value="">All Types</option>
          <option value="positive">Positive</option>
          <option value="negative">Negative</option>
        </select>
      </div>

      <div class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50/60">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Given By</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Points</th>
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
              </tr>
            </thead>
            <tbody id="reportTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>

  </div><!-- /p-6 -->
</div><!-- /main-content -->

<script>
const recentActivity = @json($recentActivity);

// ===================== COLOR HELPERS =====================
const colorMap = {
  indigo:  { bg:'bg-indigo-50',  text:'text-indigo-600',  dot:'#6366f1' },
  amber:   { bg:'bg-amber-50',   text:'text-amber-600',   dot:'#f59e0b' },
  emerald: { bg:'bg-emerald-50', text:'text-emerald-600', dot:'#10b981' },
  red:     { bg:'bg-red-50',     text:'text-red-500',     dot:'#ef4444' },
  blue:    { bg:'bg-blue-50',    text:'text-blue-600',    dot:'#3b82f6' },
  violet:  { bg:'bg-violet-50',  text:'text-violet-600',  dot:'#8b5cf6' },
};
const roleColors = {
  admin:   { bg:'bg-indigo-50',  text:'text-indigo-600' },
  teacher: { bg:'bg-violet-50',  text:'text-violet-600' },
  student: { bg:'bg-blue-50',    text:'text-blue-600' },
  parent:  { bg:'bg-amber-50',   text:'text-amber-600' },
};

function initials(name) { return name.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2); }
function fmtDate(d) { return new Date(d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}); }

// ===================== PAGE ROUTING =====================
const titles = { dashboard:'Dashboard', users:'Manage Users', categories:'Point Categories', reports:'Reports' };
function showPage(name, el) {
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('page-'+name).classList.add('active');
  if (el) el.classList.add('active');
  document.getElementById('pageTitle').textContent = titles[name];
  if (name==='users') renderUsers();
  if (name==='categories') renderCats();
  if (name==='reports') renderReports();
  if (name==='dashboard') renderActivity();
}

// ===================== ACTIVITY FEED =====================
function renderActivity() {
    const list = document.getElementById('activityList');

    if (!recentActivity.length) {
        list.innerHTML = `
            <div class="px-5 py-10 text-center text-slate-400 text-sm">
                No point activity yet.
            </div>`;
        return;
    }

    list.innerHTML = recentActivity.map(r => `
        <div class="px-5 py-3.5 flex items-center justify-between table-row border-b border-slate-50 last:border-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs flex-shrink-0">
                    ${r.student.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2)}
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">${r.student}</p>
                    <p class="text-xs text-slate-400">by ${r.teacher} · ${r.category}</p>
                </div>
            </div>
            <span class="mono text-sm font-bold ${r.type === 'positive' ? 'text-emerald-600' : 'text-red-500'}">
                ${r.points > 0 ? '+' : ''}${r.points} pts
            </span>
        </div>
    `).join('');
}

// ===================== USERS (AJAX) =====================

let users = [];

async function loadUsers() {
    const q  = (document.getElementById('userSearch').value || '').trim();
    const rf = document.getElementById('roleFilter').value;

    const params = new URLSearchParams();
    if (q)  params.append('search', q);
    if (rf) params.append('role', rf);

    const res  = await fetch(`/users?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
         }
    });

    const text = await res.text();

    try {
        users = JSON.parse(text);
        renderUsers();
    } catch(e) {
        console.error('Not valid JSON:', e);
        console.log('Raw response:', text.slice(0, 300));
    }
}

function renderUsers() {
    const tbody = document.getElementById('usersTableBody');
    const empty = document.getElementById('usersEmpty');

    if (!users.length) {
        tbody.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }

    empty.classList.add('hidden');
    const rc = roleColors;

    tbody.innerHTML = users.map(u => `
        <tr class="table-row border-b border-slate-50 last:border-0">
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                        ${initials(u.name)}
                    </div>
                    <span class="text-sm font-semibold text-slate-800">${u.name}</span>
                </div>
            </td>
            <td class="px-5 py-3.5 text-sm text-slate-500">${u.email}</td>
            <td class="px-5 py-3.5">
                <span class="tag ${(rc[u.role]||rc.student).bg} ${(rc[u.role]||rc.student).text}">${u.role}</span>
            </td>
            <td class="px-5 py-3.5 text-sm text-slate-400 mono">${fmtDate(u.created_at)}</td>
            <td class="px-5 py-3.5 text-right">
                <div class="flex gap-2 justify-end">
                    <button onclick="openEditUser(${u.id})" class="btn-edit">Edit</button>
                    <button onclick="askDelete('user',${u.id},'${u.name}')" class="btn-danger">Delete</button>
                </div>
            </td>
        </tr>`).join('');
}

// Search/filter now hits the server
document.getElementById('userSearch').addEventListener('input', loadUsers);
document.getElementById('roleFilter').addEventListener('change', loadUsers);

// ── Add User ──────────────────────────────────────────────
let editUserId = null;

function openAddUser() {
    editUserId = null;
    document.getElementById('userModalTitle').textContent = 'Add New User';
    document.getElementById('userSaveBtn').textContent    = 'Add User';
    document.getElementById('uName').value  = '';
    document.getElementById('uEmail').value = '';
    document.getElementById('uRole').value  = '';
    document.getElementById('uPass').value  = '';
    document.getElementById('passField').style.display = 'block';
    openModal('userModal');
}

// ── Edit User ─────────────────────────────────────────────
function openEditUser(id) {
    editUserId = id;
    const u = users.find(x => x.id === id);
    document.getElementById('userModalTitle').textContent = 'Edit User';
    document.getElementById('userSaveBtn').textContent    = 'Save Changes';
    document.getElementById('uName').value  = u.name;
    document.getElementById('uEmail').value = u.email;
    document.getElementById('uRole').value  = u.role;
    document.getElementById('uPass').value  = '';
    document.getElementById('passField').style.display = 'none';
    openModal('userModal');
}

// ── Save (Add or Edit) ────────────────────────────────────
async function saveUser() {
    const name  = document.getElementById('uName').value.trim();
    const email = document.getElementById('uEmail').value.trim();
    const role  = document.getElementById('uRole').value;
    const pass  = document.getElementById('uPass').value;

    if (!name || !email || !role) { alert('Please fill all fields.'); return; }

    const body    = { name, email, role, _token: '{{ csrf_token() }}' };
    const isEdit  = !!editUserId;

    if (!isEdit) {
        if (!pass) { alert('Password is required.'); return; }
        body.password = pass;
    }

    const url    = isEdit ? `/users/${editUserId}` : '/users';
    const method = isEdit ? 'PUT' : 'POST';

    const res = await fetch(url, {
        method,
        headers: {
            'Content-Type':     'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN':     '{{ csrf_token() }}',
        },
        body: JSON.stringify(body),
    });

    if (res.ok) {
        closeModal('userModal');
        loadUsers(); // refresh from DB
    } else {
        const err = await res.json();
        alert(err.message || 'Something went wrong.');
    }
}

// ── Delete ────────────────────────────────────────────────
async function confirmDelete() {
    if (!deleteTarget) return;

    if (deleteTarget.type === 'user') {
        const res = await fetch(`/users/${deleteTarget.id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':     '{{ csrf_token() }}',
            },
        });

        if (res.ok) {
            closeModal('deleteModal');
            loadUsers();
        } else {
            const err = await res.json();
            alert(err.message || 'Could not delete user.');
            closeModal('deleteModal');
        }
    }

    if (deleteTarget.type === 'cat') {
        // keep existing category delete logic
        categories = categories.filter(c => c.id !== deleteTarget.id);
        closeModal('deleteModal');
        renderCats();
    }
}

// Load users when page tab is opened
function showPage(name, el) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('page-' + name).classList.add('active');
    if (el) el.classList.add('active');
    document.getElementById('pageTitle').textContent = titles[name];

    if (name === 'dashboard')  renderActivity();
    if (name === 'users')      loadUsers();      // 👈 added
    if (name === 'categories') renderCats();
    if (name === 'reports')    renderReports();
}

// ===================== CATEGORIES =====================
let selectedColor = 'indigo';
let editCatId = null;
function pickColor(el) { selectedColor=el.dataset.color; document.querySelectorAll('.color-opt').forEach(c=>c.style.borderColor='transparent'); el.style.borderColor='white'; el.style.boxShadow='0 0 0 2px #6366f1'; }

function renderCats() {
  const pos = categories.filter(c=>c.type==='positive').length;
  const neg = categories.filter(c=>c.type==='negative').length;
  document.getElementById('catTotalNum').textContent = categories.length;
  document.getElementById('catPosNum').textContent = pos;
  document.getElementById('catNegNum').textContent = neg;
  const tbody = document.getElementById('catTableBody');
  tbody.innerHTML = categories.map(c => {
    const col = colorMap[c.color]||colorMap.indigo;
    return `<tr class="table-row border-b border-slate-50 last:border-0">
      <td class="px-5 py-3.5">
        <div class="flex items-center gap-3">
          <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${col.dot}"></span>
          <span class="text-sm font-semibold text-slate-800">${c.name}</span>
        </div>
      </td>
      <td class="px-5 py-3.5">
        <span class="tag ${c.type==='positive'?'bg-emerald-50 text-emerald-700':'bg-red-50 text-red-600'}">${c.type}</span>
      </td>
      <td class="px-5 py-3.5 mono text-sm font-semibold ${c.points>0?'text-emerald-600':'text-red-500'}">${c.points>0?'+':''}${c.points}</td>
      <td class="px-5 py-3.5 text-sm text-slate-400 max-w-xs truncate">${c.desc}</td>
      <td class="px-5 py-3.5 text-right">
        <div class="flex gap-2 justify-end">
          <button onclick="openEditCat(${c.id})" class="btn-edit">Edit</button>
          <button onclick="askDelete('cat',${c.id},'${c.name}')" class="btn-danger">Delete</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function openAddCat() { editCatId=null; document.getElementById('catModalTitle').textContent='Add Category'; document.getElementById('catSaveBtn').textContent='Add Category'; document.getElementById('cName').value=''; document.getElementById('cType').value='positive'; document.getElementById('cPoints').value=''; document.getElementById('cDesc').value=''; selectedColor='indigo'; document.querySelectorAll('.color-opt').forEach(c=>{c.style.borderColor='transparent';c.style.boxShadow=''}); openModal('catModal'); }
function openEditCat(id) {
  editCatId=id; const c=categories.find(x=>x.id===id);
  document.getElementById('catModalTitle').textContent='Edit Category'; document.getElementById('catSaveBtn').textContent='Save Changes';
  document.getElementById('cName').value=c.name; document.getElementById('cType').value=c.type; document.getElementById('cPoints').value=c.points; document.getElementById('cDesc').value=c.desc;
  selectedColor=c.color; document.querySelectorAll('.color-opt').forEach(el=>{el.style.borderColor='transparent';el.style.boxShadow=''; if(el.dataset.color===c.color){el.style.borderColor='white';el.style.boxShadow='0 0 0 2px #6366f1';}});
  openModal('catModal');
}
function saveCat() {
  const name=document.getElementById('cName').value.trim();
  const type=document.getElementById('cType').value;
  const pts=parseInt(document.getElementById('cPoints').value)||0;
  const desc=document.getElementById('cDesc').value.trim();
  if (!name) { alert('Please enter a category name.'); return; }
  if (editCatId) { const c=categories.find(x=>x.id===editCatId); c.name=name; c.type=type; c.points=type==='negative'?-Math.abs(pts):Math.abs(pts); c.desc=desc; c.color=selectedColor; }
  else { categories.push({ id:nextCatId++, name, type, points:type==='negative'?-Math.abs(pts):Math.abs(pts), desc, color:selectedColor }); }
  closeModal('catModal'); renderCats(); populateReportCatFilter();
}

// ===================== REPORTS =====================
function populateReportCatFilter() {
  const sel = document.getElementById('reportCatFilter');
  const cur = sel.value;
  sel.innerHTML = '<option value="">All Categories</option>' + categories.map(c=>`<option value="${c.name}" ${cur===c.name?'selected':''}>${c.name}</option>`).join('');
}
function renderReports() {
  const q=(document.getElementById('reportSearch').value||'').toLowerCase();
  const cf=document.getElementById('reportCatFilter').value;
  const tf=document.getElementById('reportTypeFilter').value;
  const filtered=reports.filter(r=>
    (!q||r.student.toLowerCase().includes(q)||r.teacher.toLowerCase().includes(q))&&
    (!cf||r.category===cf)&&
    (!tf||r.type===tf)
  );
  document.getElementById('reportTableBody').innerHTML = filtered.map((r,i)=>`
    <tr class="table-row border-b border-slate-50 last:border-0">
      <td class="px-5 py-3.5 mono text-xs text-slate-400">${String(i+1).padStart(2,'0')}</td>
      <td class="px-5 py-3.5">
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">${initials(r.student)}</div>
          <span class="text-sm font-semibold text-slate-800">${r.student}</span>
        </div>
      </td>
      <td class="px-5 py-3.5 text-sm text-slate-500">${r.teacher}</td>
      <td class="px-5 py-3.5 text-sm text-slate-600">${r.category}</td>
      <td class="px-5 py-3.5 mono font-bold text-sm ${r.type==='positive'?'text-emerald-600':'text-red-500'}">${r.points>0?'+':''}${r.points}</td>
      <td class="px-5 py-3.5 text-sm text-slate-400 mono">${fmtDate(r.date)}</td>
    </tr>`).join('');
}

// ===================== DELETE =====================
let deleteTarget = null;
function askDelete(type, id, name) {
  deleteTarget={type,id};
  document.getElementById('deleteMsg').textContent=`Delete "${name}"? This action cannot be undone.`;
  openModal('deleteModal');
}
function confirmDelete() {
  if (!deleteTarget) return;
  if (deleteTarget.type==='user') users=users.filter(u=>u.id!==deleteTarget.id);
  if (deleteTarget.type==='cat') categories=categories.filter(c=>c.id!==deleteTarget.id);
  closeModal('deleteModal');
  renderUsers(); renderCats(); renderActivity();
}

// ===================== MODALS =====================
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',function(e){ if(e.target===this) this.classList.remove('open'); }));

function toggleSidebar() {
  const s=document.getElementById('sidebar');
  s.style.transform = s.style.transform==='translateX(0px)'||!s.style.transform ? 'translateX(-240px)' : 'translateX(0px)';
}

// ===================== INIT =====================
renderActivity();
populateReportCatFilter();
</script>
</body>
</html>