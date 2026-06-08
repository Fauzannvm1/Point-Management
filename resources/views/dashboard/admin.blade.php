<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - PointMS</title>
  <link rel="icon" type="image/png" href="{{ asset('storage_backup/Icon.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --sidebar-bg:   #0f1117;
      --accent:       #6366f1;
      --accent-ghost: rgba(99,102,241,0.15);
      --sidebar-w:    240px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; }

    body {
      font-family: 'Syne', sans-serif;
      background: #f4f5f9;
      color: #1e2130;
    }

    .mono { font-family: 'JetBrains Mono', monospace; }

    .shell { display: flex; height: 100vh; overflow: hidden; }

    .sidebar {
      background: var(--sidebar-bg);
      width: var(--sidebar-w);
      min-width: var(--sidebar-w);
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      flex-shrink: 0;
      position: fixed;
      left: 0; top: 0;
      z-index: 60;
      transition: transform 0.25s ease;
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 14px;
      border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .sidebar-brand-icon {
      width: 44px;
      height: 44px;
      flex-shrink: 0;
      overflow: hidden;
      border-radius: 8px;
      background: #fff;
      position: relative;
    }
    .sidebar-brand-icon img {
      width: 115px;
      height: auto;
      max-width: none;
      position: absolute;
      left: 50%;
      top: -14px;
      transform: translateX(-50%);
      display: block;
    }
    .sidebar-brand-name {
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.2;
    }
    .sidebar-brand-tag {
      font-size: 10px;
      color: #94a3b8;
      margin-top: 2px;
    }

    .main {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      min-width: 0;
      height: 100vh;
    }

    .topbar {
      background: #fff;
      border-bottom: 1px solid #e8eaef;
      padding: 0 20px;
      height: 54px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .scroll { flex: 1; overflow-y: auto; padding: 20px; }

    .overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 59;
      backdrop-filter: blur(2px);
    }
    .overlay.on { display: block; }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 12px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 500;
      color: #7c8099;
      cursor: pointer;
      transition: all 0.15s;
      text-decoration: none;
    }
    .nav-item:hover { background: rgba(255,255,255,0.06); color: #e2e4f0; }
    .nav-item.on    { background: var(--accent-ghost); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.25); }
    .nav-item svg   { width: 16px; height: 16px; flex-shrink: 0; }

    .nav-section {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-weight: 700;
      color: #3d4060;
      padding: 10px 12px 4px;
    }

    .card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #e8eaef;
      box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .badge {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      padding: 2px 7px;
      border-radius: 5px;
      font-weight: 600;
    }

    .stat-number {
      font-family: 'JetBrains Mono', monospace;
      font-size: 28px;
      font-weight: 600;
    }

    .tag {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 600;
    }

    .btn-primary {
      background: #6366f1;
      color: #fff;
      padding: 8px 16px;
      border-radius: 9px;
      font-size: 12.5px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-primary:hover { background: #4f51de; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.3); }

    .btn-secondary {
      background: #f1f2f6;
      color: #4a5070;
      padding: 8px 14px;
      border-radius: 9px;
      font-size: 12.5px;
      font-weight: 600;
      border: 1px solid #e0e2ea;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-secondary:hover { background: #e8e9f0; }

    .btn-danger {
      background: #fef2f2;
      color: #ef4444;
      padding: 5px 10px;
      border-radius: 7px;
      font-size: 11.5px;
      font-weight: 600;
      border: 1px solid #fee2e2;
      cursor: pointer;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-danger:hover { background: #ef4444; color: #fff; }

    .btn-edit {
      background: #f0f1ff;
      color: #6366f1;
      padding: 5px 10px;
      border-radius: 7px;
      font-size: 11.5px;
      font-weight: 600;
      border: 1px solid #e0e2ff;
      cursor: pointer;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-edit:hover { background: #6366f1; color: #fff; }

    input, select, textarea {
      font-family: 'Syne', sans-serif;
      outline: none;
      transition: border 0.15s, box-shadow 0.15s;
    }
    input:focus, select:focus, textarea:focus {
      border-color: #6366f1 !important;
      box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    }
    .form-input {
      width: 100%;
      padding: 9px 12px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      font-size: 13px;
      background: #f9fafb;
      color: #1f2937;
    }

    .table-head th {
      text-align: left;
      padding: 10px 14px;
      font-size: 10px;
      font-weight: 700;
      color: #6366f1;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      background: rgba(99,102,241,0.04);
      border-bottom: 1px solid #e8eaef;
    }
    .table-row td { padding: 10px 14px; border-bottom: 1px solid #f4f5f9; font-size: 13px; }
    .table-row:hover td { background: #f9fafb; }
    .table-row:last-child td { border-bottom: none; }

    .modal {
      position: fixed;
      inset: 0;
      background: rgba(15,17,23,0.6);
      z-index: 200;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
      padding: 16px;
    }
    .modal.on { opacity: 1; pointer-events: all; backdrop-filter: blur(4px); }

    .modal-body {
      background: #fff;
      border-radius: 18px;
      padding: 22px;
      width: 100%;
      transform: translateY(14px);
      transition: transform 0.2s;
      box-shadow: 0 20px 60px rgba(0,0,0,0.2);
      overflow-y: auto;
      max-height: 90vh;
    }
    .modal.on .modal-body { transform: translateY(0); }

    .page { display: none; }
    .page.on { display: block; animation: fadeUp 0.18s ease; }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(6px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; }
    .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

    .notif-dot { width:6px;height:6px;background:#ef4444;border-radius:50%;position:absolute;top:5px;right:5px; }

    .hamburger {
      display: none;
      padding: 6px;
      border-radius: 8px;
      background: transparent;
      border: none;
      cursor: pointer;
      color: #64748b;
      align-items: center;
      justify-content: center;
    }
    .hamburger:hover { background: #f1f5f9; }

    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
      gap: 10px;
      flex-wrap: wrap;
    }

    /* Class card */
    .cls-card {
      background: #fff;
      border: 1px solid #e8eaef;
      border-radius: 14px;
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 11px;
      position: relative;
      overflow: hidden;
      transition: all 0.15s;
      box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .cls-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, #6366f1, #a855f7);
    }
    .cls-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 16px rgba(99,102,241,0.1); transform: translateY(-1px); }

    /* Clickable class card for dashboard */
    .dash-cls-card {
      background: #fff;
      border: 1px solid #e8eaef;
      border-radius: 12px;
      padding: 14px;
      cursor: pointer;
      transition: all 0.18s;
      position: relative;
      overflow: hidden;
    }
    .dash-cls-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
    }
    .dash-cls-card:hover {
      border-color: #c7d2fe;
      box-shadow: 0 6px 20px rgba(99,102,241,0.12);
      transform: translateY(-2px);
    }
    .dash-cls-card:active { transform: translateY(0); }

    .cls-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 14px;
    }

    /* Dashboard classes mini grid */
    .dash-cls-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 10px;
      padding: 14px;
    }

    /* Mobile bottom nav */
    .mobile-nav {
      display: none;
      position: fixed;
      bottom: 0; left: 0; right: 0;
      background: var(--sidebar-bg);
      border-top: 1px solid rgba(255,255,255,0.08);
      z-index: 50;
      padding: 4px 10px calc(4px + env(safe-area-inset-bottom));
      justify-content: space-around;
      gap: 10px;
    }
    .mobile-nav-item {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2px;
      padding: 5px 2px;
      color: #7c8099;
      cursor: pointer;
      font-size: 9px;
      font-weight: 600;
      font-family: 'Syne', sans-serif;
      border: none;
      background: transparent;
      transition: color 0.15s;
    }
    .mobile-nav-item svg { width: 19px; height: 19px; }
    .mobile-nav-item.on { color: #a5b4fc; }

    /* Class detail panel (slide-in) */
    .class-panel {
      position: fixed;
      inset: 0;
      z-index: 300;
      display: flex;
      align-items: stretch;
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.25s;
    }
    .class-panel.on {
      pointer-events: all;
      opacity: 1;
    }
    .class-panel-backdrop {
      flex: 1;
      background: rgba(15,17,23,0.55);
      backdrop-filter: blur(3px);
      cursor: pointer;
    }
    .class-panel-body {
      width: 75vw;
      max-width: 100vw;
      background: #fff;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transform: translateX(100%);
      transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
      box-shadow: -12px 0 50px rgba(0,0,0,0.15);
    }
    .class-panel.on .class-panel-body {
      transform: translateX(0);
    }

    @media (max-width: 1024px) {
      .sidebar { padding-bottom: 70px; transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .main { margin-left: 0; }
      .hamburger { display: flex !important; }
      .grid-4 { grid-template-columns: 1fr 1fr !important; }
      .dash-cols { grid-template-columns: 1fr !important; }
    }

    @media (max-width: 640px) {
      .scroll  { padding: 12px !important; padding-bottom: 72px !important; }
      .topbar  { padding: 0 12px !important; }
      .grid-4  { grid-template-columns: 1fr 1fr !important; gap: 8px !important; }
      .grid-3  { grid-template-columns: 1fr 1fr !important; gap: 8px !important; }
      .grid-2  { grid-template-columns: 1fr !important; }
      .stat-number { font-size: 20px !important; }
      .modal   { padding: 0 !important; align-items: flex-end !important; }
      .modal-body { border-radius: 18px 18px 0 0 !important; max-height: 92vh !important; max-width: 100% !important; }
      .page-header { flex-direction: column; align-items: flex-start; }
      .hide-mobile { display: none !important; }
      .mobile-nav  { display: flex !important; }
      .cls-grid { grid-template-columns: 1fr !important; }
      .dash-cls-grid { grid-template-columns: 1fr 1fr !important; }
      .class-panel-body { width: 100vw; }
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
<body>

<div class="overlay" id="ov" onclick="closeSidebar()"></div>


<!-- Modal: Add/Edit User -->
<div class="modal" id="mUser">
  <div class="modal-body" style="max-width:480px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
      <h3 style="font-size:15px;font-weight:700;color:#1e2130" id="mUserTitle">Add New User</h3>
      <button onclick="closeModal('mUser')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Full Name</label><input type="text" id="uName" placeholder="e.g. Ahmad Riski" class="form-input"></div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Username</label><input type="text" id="uUsername" placeholder="username" class="form-input"></div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Email</label><input type="email" id="uEmail" placeholder="user@school.edu" class="form-input"></div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Role</label>
        <select id="uRole" class="form-input">
          <option value="">-- Select Role --</option>
          <option value="admin">Admin</option>
          <option value="teacher">Teacher</option>
          <option value="student">Student</option>
          <option value="parent">Parent</option>
        </select>
      </div>
      <div id="uPassWrap"><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Password</label><input type="password" id="uPass" placeholder="Min. 6 characters" class="form-input"></div>
    </div>
    <div style="display:flex;gap:10px;margin-top:18px">
      <button onclick="closeModal('mUser')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveUser()" class="btn-primary" style="flex:1;justify-content:center" id="uSaveBtn">Add User</button>
    </div>
  </div>
</div>


<!-- Modal: Add/Edit Task -->
<div class="modal" id="mTask">
  <div class="modal-body" style="max-width:520px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
      <h3 style="font-size:15px;font-weight:700;color:#1e2130" id="mTaskTitle">Add Task</h3>
      <button onclick="closeModal('mTask')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Title</label><input type="text" id="tTitle" placeholder="e.g. Algebra Homework" class="form-input"></div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Description</label><textarea id="tDesc" rows="3" class="form-input" style="resize:none"></textarea></div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Teacher Subject</label><select id="tTS" class="form-input"><option value="">-- Select --</option></select></div>
      <div class="grid-2">
        <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Due Date</label><input type="date" id="tDue" class="form-input"></div>
        <div style="display:none"><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Max Grade</label><input type="number" id="tGrade" value="100" class="form-input"></div>
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Images / Attachments</label>
        <div class="upload-zone" id="tDZ" onclick="document.getElementById('tFI').click()" style="border:2px dashed #cbd5e1;border-radius:10px;padding:16px;text-align:center;cursor:pointer">
          <p style="font-size:12px;font-weight:600;color:#6366f1">Click to add images or files</p>
        </div>
        <input type="file" id="tFI" multiple accept="image/*,.pdf,.doc,.docx" style="display:none" onchange="previewTaskFiles(this)">
        <div id="tFP" style="display:none;grid-template-columns:repeat(auto-fill,minmax(64px,1fr));gap:7px;margin-top:8px"></div>
      </div>
    </div>
    <div style="display:flex;gap:10px;margin-top:18px">
      <button onclick="closeModal('mTask')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveTask()" class="btn-primary" style="flex:1;justify-content:center" id="tSaveBtn">Add Task</button>
    </div>
  </div>
</div>


<!-- Modal: Give Points -->
<div class="modal" id="mPoint">
  <div class="modal-body" style="max-width:480px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
      <h3 style="font-size:15px;font-weight:700;color:#1e2130">Give Points</h3>
      <button onclick="closeModal('mPoint')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Student</label><select id="pStudent" class="form-input"><option value="">-- Select --</option></select></div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Teacher Subject</label><select id="pTS" class="form-input"><option value="">-- Select --</option></select></div>
      <div class="grid-2">
        <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Points</label><input type="number" id="pPts" class="form-input"><p style="font-size:10px;color:#94a3b8;margin-top:3px">Positive = reward, Negative = penalty</p></div>
        <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Date</label><input type="date" id="pDate" class="form-input"></div>
      </div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Description</label><input type="text" id="pDesc" class="form-input"></div>
    </div>
    <div style="display:flex;gap:10px;margin-top:18px">
      <button onclick="closeModal('mPoint')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="savePoint()" class="btn-primary" style="flex:1;justify-content:center">Give Points</button>
    </div>
  </div>
</div>


<!-- Modal: Add/Edit Subject -->
<div class="modal" id="mSubj">
  <div class="modal-body" style="max-width:380px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
      <h3 style="font-size:15px;font-weight:700;color:#1e2130" id="mSubjTitle">Add Subject</h3>
      <button onclick="closeModal('mSubj')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Subject Name</label><input type="text" id="sName" class="form-input" placeholder="e.g. Mathematics"></div>
    <div style="display:flex;gap:10px;margin-top:18px">
      <button onclick="closeModal('mSubj')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveSubject()" class="btn-primary" style="flex:1;justify-content:center" id="sSaveBtn">Add Subject</button>
    </div>
  </div>
</div>


<!-- Modal: Assign Teacher -->
<div class="modal" id="mTS">
  <div class="modal-body" style="max-width:480px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
      <h3 style="font-size:15px;font-weight:700;color:#1e2130" id="mTSTitle">Assign Teacher</h3>
      <button onclick="closeModal('mTS')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Teacher</label>
        <select id="tsTeacher" onchange="onTeacherSelectChange()" class="form-input"><option value="">-- Select --</option></select>
        <div id="tsNewWrap" style="display:none;margin-top:8px">
          <input type="text" id="tsNewName" placeholder="New teacher name..." class="form-input" style="border-color:#6366f1">
          <p style="font-size:10px;color:#6366f1;margin-top:3px">A new teacher account will be created.</p>
        </div>
      </div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Subject</label><select id="tsSubj" class="form-input"><option value="">-- Select --</option></select></div>
      <div><label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Class</label><select id="tsClass" class="form-input"><option value="">-- Select --</option></select></div>
    </div>
    <div style="display:flex;gap:10px;margin-top:18px">
      <button onclick="closeModal('mTS')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveTeacherSubject()" class="btn-primary" style="flex:1;justify-content:center" id="tsSaveBtn">Assign</button>
    </div>
  </div>
</div>


<!-- Modal: Add/Edit Class -->
<div class="modal" id="mClass">
  <div class="modal-body" style="max-width:480px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
      <h3 style="font-size:15px;font-weight:700;color:#1e2130" id="mClassTitle">Add Class</h3>
      <button onclick="closeModal('mClass')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Class Name</label>
        <input type="text" id="clsName" placeholder="e.g. Class 10A" class="form-input">
      </div>
      <div class="grid-2">
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Main Subject</label>
          <select id="clsSubject" class="form-input">
            <option value="">-- Select --</option>
          </select>
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Teacher</label>
          <!-- Kept the onchange function from earlier if you are still using the "Add New" feature -->
          <select id="clsTeacher" onchange="onClassTeacherSelectChange()" class="form-input">
            <option value="">-- Select --</option>
          </select>
        </div>
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">
          Join Code
          <span style="font-weight:400;color:#94a3b8;margin-left:4px">(auto-generated if empty)</span>
        </label>
        <input type="text" id="clsCode" placeholder="e.g. MTH-2024" class="form-input" style="font-family:'JetBrains Mono',monospace;letter-spacing:0.1em;text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Description <span style="font-weight:400;color:#94a3b8">(optional)</span></label>
        <textarea id="clsDesc" rows="2" class="form-input" style="resize:none" placeholder="Short description..."></textarea>
      </div>
    </div>
    <div style="display:flex;gap:10px;margin-top:18px">
      <button onclick="closeModal('mClass')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveClass()" class="btn-primary" style="flex:1;justify-content:center" id="clsSaveBtn">Add Class</button>
    </div>
  </div>
</div>


<!-- Modal: Class Students -->
<div class="modal" id="mClassStudents">
  <div class="modal-body" style="max-width:520px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <div>
        <h3 style="font-size:15px;font-weight:700;color:#1e2130" id="mCSTitle">Students</h3>
        <p style="font-size:11px;color:#94a3b8;margin-top:2px" id="mCSMeta">--</p>
      </div>
      <button onclick="closeModal('mClassStudents')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;gap:8px;margin-bottom:14px">
      <select id="csAddSelect" class="form-input" style="flex:1"><option value="">-- Add a student --</option></select>
      <button onclick="addStudentToClass()" class="btn-primary" style="flex-shrink:0;padding:8px 14px">Add</button>
    </div>
    <div id="csStuList" style="max-height:340px;overflow-y:auto">
      <div style="text-align:center;padding:30px;color:#94a3b8;font-size:13px">Loading...</div>
    </div>
    <input type="hidden" id="csClassId">
    <div style="margin-top:14px">
      <button onclick="closeModal('mClassStudents')" class="btn-secondary" style="width:100%;justify-content:center">Close</button>
    </div>
  </div>
</div>


<!-- Modal: Delete Confirmation -->
<div class="modal" id="mDel">
  <div class="modal-body" style="max-width:340px;text-align:center">
    <div style="width:44px;height:44px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
      <svg style="width:22px;height:22px;color:#ef4444" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </div>
    <h3 style="font-size:14px;font-weight:700;color:#1e2130;margin-bottom:6px">Confirm Delete</h3>
    <p style="font-size:13px;color:#64748b;margin-bottom:18px" id="delMsg">Are you sure?</p>
    <div style="display:flex;gap:10px">
      <button onclick="closeModal('mDel')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="confirmDelete()" style="flex:1;background:#ef4444;color:#fff;padding:9px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:'Syne',sans-serif">Delete</button>
    </div>
  </div>
</div>


<!-- ===== CLASS DETAIL PANEL ===== -->
<div class="class-panel" id="classPanel">
  <div class="class-panel-backdrop" onclick="closeClassPanel()"></div>
  <div class="class-panel-body">
    <!-- Header -->
    <div id="cpHeader" style="padding:0;flex-shrink:0">
      <div id="cpBanner" style="height:90px;position:relative;display:flex;align-items:flex-end;padding:16px 20px;gap:12px">
        <div id="cpInitial" style="width:52px;height:52px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#6366f1;flex-shrink:0;box-shadow:0 4px 14px rgba(0,0,0,0.15)">A</div>
        <div style="min-width:0">
          <h2 id="cpName" style="font-size:17px;font-weight:800;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Class Name</h2>
          <p id="cpSubject" style="font-size:12px;color:rgba(255,255,255,0.75);margin-top:1px">Main Subject</p>
        </div>
        <button onclick="closeClassPanel()" style="position:absolute;top:12px;right:14px;width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,0.2);border:none;cursor:pointer;color:#fff;font-size:18px;line-height:1;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)">×</button>
      </div>
    </div>

    <!-- Info row -->
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:stretch;gap:10px;flex-shrink:0;background:#fff">
      <div style="flex:1;background:#f9fafb;border-radius:10px;padding:10px 12px;border:1px solid #e8eaef">
        <p style="font-size:9px;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:4px">Homeroom Teacher</p>
        <div style="display:flex;align-items:center;gap:7px">
          <div id="cpTeacherAvatar" style="width:26px;height:26px;border-radius:50%;background:#f0f1ff;color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0">T</div>
          <p id="cpTeacher" style="font-size:13px;font-weight:700;color:#1e2130">--</p>
        </div>
      </div>
      <div style="background:#f9fafb;border-radius:10px;padding:10px 12px;border:1px solid #e8eaef;text-align:center;min-width:70px">
        <p style="font-size:9px;font-weight:700;color:#3b82f6;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:4px">Students</p>
        <p id="cpStudentCount" style="font-family:'JetBrains Mono',monospace;font-size:20px;font-weight:700;color:#1e2130">--</p>
      </div>
      <div style="background:#f9fafb;border-radius:10px;padding:10px 12px;border:1px solid #e8eaef;text-align:center;min-width:60px">
        <p style="font-size:9px;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:4px">Tasks</p>
        <p id="cpTaskCount" style="font-family:'JetBrains Mono',monospace;font-size:20px;font-weight:700;color:#1e2130">--</p>
      </div>
    </div>

    <!-- Code + description -->
    <div style="padding:12px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;flex-shrink:0">
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase">Join Code</span>
        <span id="cpCode" style="font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:700;color:#6366f1;background:#f0f1ff;padding:3px 10px;border-radius:6px;border:1px solid #e0e2ff">--</span>
      </div>
      <p id="cpDesc" style="font-size:12px;color:#94a3b8;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></p>
    </div>

    <!-- Action buttons -->
    <div style="padding:10px 20px;border-bottom:1px solid #f1f5f9;display:flex;gap:8px;flex-shrink:0">
      <button id="cpEditBtn" onclick="editClassFromPanel()" class="btn-edit" style="flex:1;justify-content:center">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Class
      </button>
      <button id="cpManageBtn" onclick="manageStudentsFromPanel()" class="btn-primary" style="flex:1;justify-content:center">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Students
      </button>
      <!-- <button id="cpDelBtn" onclick="deleteClassFromPanel()" class="btn-danger" style="padding:8px 12px">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      </button> -->
    </div>

    <!-- Tabs -->
    <div style="padding:0 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <div>
        <button onclick="switchCPTab('students')" id="cpTabStudents" style="padding:10px 16px;font-size:12px;font-weight:700;border:none;background:transparent;cursor:pointer;border-bottom:2px solid #6366f1;color:#6366f1;font-family:'Syne',sans-serif">Students</button>
        <button onclick="switchCPTab('tasks')" id="cpTabTasks" style="padding:10px 16px;font-size:12px;font-weight:600;border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;color:#94a3b8;font-family:'Syne',sans-serif">Tasks</button>
      </div>
      <button onclick="exportClassData()" id="cpExportBtn" style="display:flex;align-items:center;gap:5px;background:#f0f1ff;color:#6366f1;border:1px solid #e0e2ff;padding:4px 10px;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;font-family:'Syne',sans-serif;transition:all 0.15s">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
      </button>
    </div>

    <!-- Tab content -->
    <div style="flex:1;overflow-y:auto">
      <div id="cpStudentsTab">
        <div id="cpStudentList" style="min-height:100px"></div>
      </div>
      <div id="cpTasksTab" style="display:none">
        <div id="cpTaskList" style="min-height:100px"></div>
      </div>
    </div>
  </div>
</div>


<!-- App shell -->
<div class="shell">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-icon">
        <img src="{{ asset('storage_backup/Icon.png') }}" alt="">
      </div>
      <div>
        <p class="sidebar-brand-name">PointMS</p>
        <p class="sidebar-brand-tag">School Point System</p>
      </div>
    </div>

    <div style="padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.08)">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:28px;height:28px;border-radius:50%;background:rgba(99,102,241,.2);border:1px solid rgba(99,102,241,.3);display:flex;align-items:center;justify-content:center;color:#a5b4fc;font-weight:700;font-size:11px;flex-shrink:0">A</div>
        <div style="min-width:0">
          <p style="color:#fff;font-size:12px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Admin User</p>
          <span class="badge" style="background:rgba(99,102,241,.2);color:#a5b4fc">admin</span>
        </div>
      </div>
    </div>

    <nav style="flex:1;padding:8px;overflow-y:auto">
      <p class="nav-section">Main</p>
      <a class="nav-item on" id="n-dash"    onclick="goToPage('dash',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        Dashboard
      </a>
      <a class="nav-item" id="n-users"   onclick="goToPage('users',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Manage Users
      </a>
      <a class="nav-item" id="n-classes" onclick="goToPage('classes',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/></svg>
        Classes
      </a>
      <a class="nav-item" id="n-tasks"   onclick="goToPage('tasks',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Tasks
      </a>
      <a class="nav-item" id="n-points"  onclick="goToPage('points',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        Give Points
      </a>
      <a class="nav-item" id="n-subj"    onclick="goToPage('subj',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.747 0-3.332.477-4.5 1.253"/></svg>
        Subjects
      </a>
      <a class="nav-item" id="n-rep"     onclick="goToPage('rep',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Reports
      </a>
    </nav>

    <div style="padding:6px;padding-bottom:env(safe-area-inset-bottom,14px)">
      <a href="{{ route('logout') }}"
         onclick="event.preventDefault();document.getElementById('logout-form').submit();"
         class="nav-item"
         onmouseover="this.style.background='rgba(239,68,68,.1)';this.style.color='#f87171'"
         onmouseout="this.style.background='';this.style.color='#7c8099'">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Logout
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
  </aside>


  <div class="main">
    <header class="topbar">
      <div style="display:flex;align-items:center;gap:10px">
        <button class="hamburger" onclick="toggleSidebar()">
          <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div>
          <h1 style="font-size:14px;font-weight:700;color:#1e2130" id="pageTitle">Dashboard</h1>
          <p style="font-size:11px;color:#94a3b8">Welcome back, Admin</p>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <button style="position:relative;padding:6px;border-radius:8px;background:transparent;border:none;cursor:pointer;color:#64748b">
          <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span class="notif-dot"></span>
        </button>
        <div style="width:30px;height:30px;border-radius:50%;background:#6366f1;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px">A</div>
      </div>
    </header>


    <div class="scroll" id="scr">

      <!-- = Page: Dashboard = -->
      <div id="p-dash" class="page on">
        <!-- Stats -->
        <div class="grid-4" style="margin-bottom:16px">
          <div class="card" style="padding:16px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:10px">
              <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center"><svg style="width:17px;height:17px;color:#3b82f6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg></div>
              <span class="badge" style="background:#f0fdf4;color:#16a34a" id="dNewStudents">+{{ $newStudentsMonth }} this month</span>
            </div>
            <p class="stat-number" style="color:#1e2130" id="dTotalStudents">{{ $totalStudents }}</p>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Total Students</p>
          </div>
          <div class="card" style="padding:16px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:10px">
              <div style="width:36px;height:36px;border-radius:10px;background:#f5f3ff;display:flex;align-items:center;justify-content:center"><svg style="width:17px;height:17px;color:#8b5cf6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
              <span class="badge" style="background:#f0fdf4;color:#16a34a" id="dNewTeachers">+{{ $newTeachersMonth }} this month</span>
            </div>
            <p class="stat-number" style="color:#1e2130" id="dTotalTeachers">{{ $totalTeachers }}</p>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Total Teachers</p>
          </div>
          <div class="card" style="padding:16px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:10px">
              <div style="width:36px;height:36px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center"><svg style="width:17px;height:17px;color:#f59e0b" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
              <span class="badge" style="background:#eff6ff;color:#2563eb" id="dPointsToday">+{{ $pointsToday }} today</span>
            </div>
            <p class="stat-number" style="color:#1e2130" id="dTotalPoints">{{ $totalPoints }}</p>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Total Points Given</p>
          </div>
          <div class="card" style="padding:16px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:10px">
              <div style="width:36px;height:36px;border-radius:10px;background:#ecfdf5;display:flex;align-items:center;justify-content:center"><svg style="width:17px;height:17px;color:#10b981" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
              <span class="badge" style="background:#f1f2f6;color:#64748b">Active</span>
            </div>
            <p class="stat-number" style="color:#1e2130" id="dTotalTasks">{{ $totalTasks }}</p>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Total Tasks</p>
          </div>
        </div>

        <!-- Main content columns -->
        <div class="dash-cols" style="display:grid;grid-template-columns:1fr 290px;gap:14px">

          <!-- Left column: Activity + Classes -->
          <div style="display:flex;flex-direction:column;gap:14px;min-width:0">

            <!-- Classes section -->
            <div class="card" style="overflow:hidden">
              <div style="padding:13px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:8px">
                  <h2 style="font-size:13px;font-weight:600;color:#1e2130">Classes</h2>
                  <span id="dashClsCount" class="badge" style="background:#f0f1ff;color:#6366f1">{{ $totalTasks }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                  <button onclick="openAddClass()" style="display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:7px;background:#6366f1;color:#fff;border:none;cursor:pointer;font-size:11px;font-weight:600;font-family:'Syne',sans-serif">
                    <svg style="width:10px;height:10px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add
                  </button>
                  <a onclick="goToPage('classes',document.getElementById('n-classes'))" style="font-size:12px;color:#6366f1;cursor:pointer;font-weight:600">View all</a>
                </div>
              </div>
              <div id="dashClsGrid" class="dash-cls-grid">
                <div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading classes...</div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="card" style="overflow:hidden">
              <div style="padding:13px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
                <h2 style="font-size:13px;font-weight:600;color:#1e2130">Recent Point Activity</h2>
                <a onclick="goToPage('rep',document.getElementById('n-rep'))" style="font-size:12px;color:#6366f1;cursor:pointer;font-weight:600">View all</a>
              </div>
              <div id="actList"></div>
            </div>
          </div>

          <!-- Right column: Quick Actions -->
          <div class="card" style="overflow:hidden;align-self:start">
            <div style="padding:13px 16px;border-bottom:1px solid #f1f5f9"><h2 style="font-size:13px;font-weight:600;color:#1e2130">Quick Actions</h2></div>
            <div style="padding:10px;display:flex;flex-direction:column;gap:7px">
              <button onclick="openAddUser()" class="btn-primary" style="width:100%;justify-content:center"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add New User</button>
              <button onclick="openAddClass()" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#6366f1;color:#fff;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">Add Class</button>
              <button onclick="goToPage('points',document.getElementById('n-points'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f59e0b;color:#fff;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">Give Points</button>
              <button onclick="goToPage('tasks',document.getElementById('n-tasks'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f1f2f6;color:#374151;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">Manage Tasks</button>
              <button onclick="goToPage('rep',document.getElementById('n-rep'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f1f2f6;color:#374151;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">View Reports</button>
            </div>
          </div>
        </div>
      </div>


      <!-- = Page: Manage Users = -->
      <div id="p-users" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e2130">Manage Users</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Add, edit, or remove system users</p></div>
          <button onclick="openAddUser()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add User</button>
        </div>
        <div class="card" style="padding:12px;margin-bottom:12px;display:flex;flex-wrap:wrap;gap:8px">
          <div style="position:relative;flex:1;min-width:160px">
            <svg style="width:13px;height:13px;color:#94a3b8;position:absolute;left:10px;top:50%;transform:translateY(-50%)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="uSearch" placeholder="Search by name or email" class="form-input" style="padding-left:30px">
          </div>
          <select id="uRoleF" class="form-input" style="width:auto;min-width:110px">
            <option value="">All Roles</option><option value="admin">Admin</option><option value="teacher">Teacher</option><option value="student">Student</option><option value="parent">Parent</option>
          </select>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>User</th><th class="hide-mobile">Email</th><th>Role</th><th class="hide-mobile">Joined</th><th style="text-align:right">Actions</th></tr></thead>
              <tbody id="uBody"></tbody>
            </table>
            <div id="uEmpty" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:13px">No users found</div>
          </div>
        </div>
      </div>


      <!-- = Page: Classes = -->
      <div id="p-classes" class="page">
        <div class="page-header">
          <div>
            <h2 style="font-size:15px;font-weight:700;color:#1e2130">Manage Classes</h2>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Create classes and manage student enrollment</p>
          </div>
          <button onclick="openAddClass()" class="btn-primary">
            <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Class
          </button>
        </div>
        <div class="grid-3" style="margin-bottom:16px">
          <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;border-radius:11px;background:#f0f1ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <svg style="width:17px;height:17px;color:#6366f1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
            </div>
            <div><p class="stat-number" style="font-size:22px;color:#1e2130" id="clStatTotal">{{ $totalClasses }}</p><p style="font-size:11px;color:#64748b">Total Classes</p></div>
          </div>
          <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;border-radius:11px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <svg style="width:17px;height:17px;color:#3b82f6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div><p class="stat-number" style="font-size:22px;color:#1e2130" id="clStatStudents">--</p><p style="font-size:11px;color:#64748b">Total Enrollments</p></div>
          </div>
          <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;border-radius:11px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <svg style="width:17px;height:17px;color:#10b981" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div><p class="stat-number" style="font-size:22px;color:#1e2130" id="clStatTasks">{{ $totalTasks }}</p><p style="font-size:11px;color:#64748b">Total Tasks</p></div>
          </div>
        </div>
        <div class="card" style="padding:10px;margin-bottom:12px;display:flex;flex-wrap:wrap;gap:8px">
          <div style="position:relative;flex:1;min-width:160px">
            <svg style="width:13px;height:13px;color:#94a3b8;position:absolute;left:10px;top:50%;transform:translateY(-50%)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="clSearch" oninput="filterClasses()" placeholder="Search name or code..." class="form-input" style="padding-left:30px">
          </div>
          <select id="clSubjF" onchange="filterClasses()" class="form-input" style="width:auto;min-width:120px"><option value="">All Subjects</option></select>
          <select id="clTeachF" onchange="filterClasses()" class="form-input" style="width:auto;min-width:120px"><option value="">All Teachers</option></select>
        </div>
        <div id="clGrid" class="cls-grid"></div>
        <div id="clEmpty" style="display:none;text-align:center;padding:50px 20px;color:#94a3b8">
          <div style="width:52px;height:52px;border-radius:16px;background:#f0f1ff;border:1px solid #e0e2ff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
            <svg style="width:22px;height:22px;color:#6366f1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
          </div>
          <p style="font-weight:700;color:#1e2130;font-size:14px;margin-bottom:4px">No classes yet</p>
          <p style="font-size:12px;margin-bottom:14px">Create your first class to get started.</p>
          <button onclick="openAddClass()" class="btn-primary" style="margin:0 auto">Add Class</button>
        </div>
      </div>


      <!-- = Page: Tasks = -->
      <div id="p-tasks" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e2130">Manage Tasks</h2><p style="font-size:12px;color:#64748b;margin-top:2px">View and manage all tasks</p></div>
          <button onclick="openAddTask()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add Task</button>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>Title</th><th class="hide-mobile">Teacher</th><th class="hide-mobile">Subject</th><th>Due</th><th class="hide-mobile">Submissions</th><th style="text-align:right">Actions</th></tr></thead>
              <tbody id="tBody"></tbody>
            </table>
            <div id="tEmpty" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:13px">No tasks yet</div>
          </div>
        </div>
      </div>


      <!-- = Page: Give Points = -->
      <div id="p-points" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e2130">Give Points</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Manually award or deduct points</p></div>
          <button onclick="openPointModal()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Give Points</button>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>#</th><th>Student</th><th class="hide-mobile">Teacher/Subject</th><th class="hide-mobile">Description</th><th>Points</th><th class="hide-mobile">Date</th></tr></thead>
              <tbody id="ptBody"></tbody>
            </table>
            <div id="ptEmpty" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:13px">No points given yet</div>
          </div>
        </div>
      </div>


      <!-- = Page: Subjects = -->
      <div id="p-subj" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e2130">Subjects</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Manage subjects and teacher assignments</p></div>
          <button onclick="openAddSubject()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add Subject</button>
        </div>
        <div class="card" style="overflow:hidden;margin-bottom:18px">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>Subject Name</th><th style="text-align:right">Actions</th></tr></thead>
              <tbody id="sBody"></tbody>
            </table>
            <div id="sEmpty" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:13px">No subjects yet</div>
          </div>
        </div>
        <div class="page-header">
          <div><h3 style="font-size:14px;font-weight:700;color:#1e2130">Teacher Assignments</h3><p style="font-size:12px;color:#64748b;margin-top:2px">Assign teachers to subjects and classes</p></div>
          <button onclick="openAddTeacherSubject()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Assign Teacher</button>
        </div>
        <div class="card" style="padding:10px;margin-bottom:10px">
          <div style="position:relative">
            <svg style="width:13px;height:13px;color:#94a3b8;position:absolute;left:10px;top:50%;transform:translateY(-50%)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="tsSearch" oninput="filterTeacherSubjects()" placeholder="Filter by teacher..." class="form-input" style="padding-left:30px">
          </div>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>Teacher</th><th>Subject</th><th class="hide-mobile">Class</th><th style="text-align:right">Actions</th></tr></thead>
              <tbody id="tsBody"></tbody>
            </table>
            <div id="tsEmpty" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:13px">No assignments yet</div>
          </div>
        </div>
      </div>


      <!-- = Page: Reports = -->
      <div id="p-rep" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e2130">Reports</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Full point transaction history</p></div>
        </div>
        <div class="grid-4" style="margin-bottom:14px">
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#6366f1" id="rTotal">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Total Points</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#10b981" id="rAwarded">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Awarded</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#ef4444" id="rDeducted">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Deducted</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#1e2130" id="rTx">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Transactions</p></div>
        </div>
        <div class="card" style="padding:10px;margin-bottom:10px;display:flex;flex-wrap:wrap;gap:8px">
          <div style="position:relative;flex:1;min-width:160px">
            <svg style="width:13px;height:13px;color:#94a3b8;position:absolute;left:10px;top:50%;transform:translateY(-50%)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="rSearch" placeholder="Search student or teacher" class="form-input" style="padding-left:30px">
          </div>
          <select id="rTypeF" class="form-input" style="width:auto;min-width:110px">
            <option value="">All Types</option><option value="positive">Positive</option><option value="negative">Negative</option>
          </select>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>#</th><th>Student</th><th class="hide-mobile">Given By</th><th class="hide-mobile">Subject</th><th class="hide-mobile">Description</th><th>Points</th><th class="hide-mobile">Date</th></tr></thead>
              <tbody id="rBody"></tbody>
            </table>
            <div id="rEmpty" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:13px">No transactions found</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>


<!-- Mobile bottom nav -->
<nav class="mobile-nav" id="mnav">
  <button class="mobile-nav-item on" onclick="mobileGoToPage('dash',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
    Home
  </button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('users',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    Users
  </button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('classes',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
    Classes
  </button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('tasks',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Tasks
  </button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('rep',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/></svg>
    Reports
  </button>
</nav>


<script>
// ---- Replace with Laravel blade values in production ----
const CSRF           = '{{ csrf_token() }}';
const recentActivity = typeof _recentActivity !== 'undefined' ? _recentActivity : [];

const PAGE_TITLES = { dash:'Dashboard', users:'Manage Users', classes:'Manage Classes', tasks:'Manage Tasks', points:'Give Points', subj:'Subjects', rep:'Reports' };
const ROLE_COLORS = { admin:'background:#eff6ff;color:#2563eb', teacher:'background:#f5f3ff;color:#7c3aed', student:'background:#eff6ff;color:#0284c7', parent:'background:#fffbeb;color:#d97706' };
const CLS_PALETTES = [
  ['#6366f1','#a855f7'],['#3b82f6','#06b6d4'],['#10b981','#3b82f6'],
  ['#f59e0b','#ef4444'],['#8b5cf6','#ec4899'],['#0ea5e9','#6366f1'],
  ['#14b8a6','#3b82f6'],['#f97316','#f59e0b'],
];

function getInitials(name) { return (name||'?').split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2); }
function formatDate(d)      { return d ? new Date(d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '--'; }
function formatDateShort(d) { return d ? new Date(d).toLocaleDateString('en-US',{month:'short',day:'numeric'}) : '--'; }

async function apiFetch(url, opts={}) {
  const res  = await fetch(url, {...opts, headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':CSRF,...(opts.headers||{})}});
  const text = await res.text();
  try {
    const data = JSON.parse(text);
    if (!res.ok && !data.message && data.errors) {
      const first = Object.values(data.errors)[0];
      data.message = Array.isArray(first) ? first[0] : first;
    }
    return {ok:res.ok, status:res.status, data};
  } catch {
    const message = res.status === 419 ? 'Session expired. Please refresh the page.' : `Request failed (${res.status}).`;
    return {ok:false, status:res.status, data:{message}};
  }
}

function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('ov').classList.toggle('on'); }
function closeSidebar()  { document.getElementById('sidebar').classList.remove('open'); document.getElementById('ov').classList.remove('on'); }

function goToPage(name, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('on'));
  document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('on'));
  document.getElementById('p-'+name)?.classList.add('on');
  if (el) el.classList.add('on');
  document.getElementById('pageTitle').textContent = PAGE_TITLES[name] || name;
  document.getElementById('scr').scrollTop = 0;
  if (name==='dash')    { renderActivityFeed(); loadDashClasses(); }
  if (name==='users')   loadUsers();
  if (name==='classes') loadClasses();
  if (name==='tasks')   loadTasks();
  if (name==='points')  loadPointHistory();
  if (name==='subj')    { loadSubjects(); loadTeacherSubjects(); }
  if (name==='rep')     loadReports();
  closeSidebar();
}

function mobileGoToPage(name, btn) {
  document.querySelectorAll('.mobile-nav-item').forEach(b => b.classList.remove('on'));
  btn.classList.add('on');
  goToPage(name, document.getElementById('n-'+name));
}

function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal').forEach(m => m.addEventListener('click', function(e){ if(e.target===this) this.classList.remove('on'); }));


// ===== DASHBOARD CLASSES =====

let dashClasses = [];

async function loadDashClasses() {
  const grid = document.getElementById('dashClsGrid');
  grid.innerHTML = '<div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading classes...</div>';

  const {ok, data} = await apiFetch('/admin/classes');
  if (!ok) {
    grid.innerHTML = '<div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">Could not load classes.</div>';
    return;
  }

  dashClasses = data.classes || data;
  document.getElementById('dashClsCount').textContent = dashClasses.length;
  renderDashClasses();
}

function renderDashClasses() {
  const grid = document.getElementById('dashClsGrid');
  if (!dashClasses.length) {
    grid.innerHTML = `<div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">
      No classes yet. <button onclick="openAddClass()" style="color:#6366f1;font-weight:600;background:none;border:none;cursor:pointer;font-family:'Syne',sans-serif">Create one →</button>
    </div>`;
    return;
  }

  grid.innerHTML = dashClasses.map((c, i) => {
    const [from, to] = CLS_PALETTES[i % CLS_PALETTES.length];
    const initial = (c.name||'?')[0].toUpperCase();
    const teacher = c.teacher_name || '--';
    const subject = c.subject_name || '--';
    return `
      <div class="dash-cls-card" onclick="openClassPanel(${c.id})" style="border-top:3px solid ${from}">
        <div style="display:flex;align-items:center;gap:9px;margin-bottom:8px">
          <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,${from},${to});display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-weight:800;font-size:15px">${initial}</div>
          <div style="min-width:0;flex:1">
            <p style="font-size:13px;font-weight:700;color:#1e2130;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${c.name}</p>
            <p style="font-size:10px;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${subject}</p>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:5px;margin-bottom:8px">
          <div style="width:18px;height:18px;border-radius:50%;background:linear-gradient(135deg,${from},${to});display:flex;align-items:center;justify-content:center;color:#fff;font-size:7px;font-weight:700;flex-shrink:0">${(teacher[0]||'?').toUpperCase()}</div>
          <p style="font-size:11px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1">${teacher}</p>
        </div>
        <div style="display:flex;gap:6px">
          <div style="flex:1;background:#f0f1ff;border-radius:6px;padding:5px;text-align:center;border:1px solid #e0e2ff">
            <p style="font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:700;color:#6366f1">${c.student_count ?? 0}</p>
            <p style="font-size:8px;color:#94a3b8;font-weight:600">Students</p>
          </div>
          <div style="flex:1;background:#ecfdf5;border-radius:6px;padding:5px;text-align:center;border:1px solid #bbf7d0">
            <p style="font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:700;color:#10b981">${c.task_count ?? 0}</p>
            <p style="font-size:8px;color:#94a3b8;font-weight:600">Tasks</p>
          </div>
          <div style="flex:1;background:#f9fafb;border-radius:6px;padding:5px;text-align:center;border:1px solid #e8eaef">
            <p style="font-family:'JetBrains Mono',monospace;font-size:10px;font-weight:700;color:#6366f1;letter-spacing:0.05em">${c.code || '--'}</p>
            <p style="font-size:8px;color:#94a3b8;font-weight:600">Code</p>
          </div>
        </div>
        <div style="margin-top:8px;display:flex;align-items:center;gap:4px;color:#6366f1">
          <p style="font-size:10px;font-weight:600">View details</p>
          <svg style="width:10px;height:10px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
      </div>`;
  }).join('');
}


// ===== CLASS DETAIL PANEL =====

let currentPanelClassId = null;
let currentPanelTab = 'students';

async function openClassPanel(classId) {
  const c = dashClasses.find(x => x.id === classId) || allClasses.find(x => x.id === classId);
  if (!c) return;

  currentPanelClassId = classId;
  currentPanelTab = 'students';

  // Set palette
  const idx = (dashClasses.findIndex(x => x.id === classId) + allClasses.findIndex(x => x.id === classId) + 1) % CLS_PALETTES.length;
  const [from, to] = CLS_PALETTES[Math.max(0,idx) % CLS_PALETTES.length];

  // Header banner gradient
  document.getElementById('cpBanner').style.background = `linear-gradient(135deg, ${from}, ${to})`;
  document.getElementById('cpInitial').textContent = (c.name||'?')[0].toUpperCase();
  document.getElementById('cpInitial').style.color = from;
  document.getElementById('cpName').textContent = c.name || '--';
  document.getElementById('cpSubject').textContent = c.subject_name || 'No subject assigned';
  document.getElementById('cpTeacher').textContent = c.teacher_name || 'Not assigned';
  document.getElementById('cpTeacherAvatar').textContent = getInitials(c.teacher_name || '?');
  document.getElementById('cpStudentCount').textContent = c.student_count ?? '--';
  document.getElementById('cpTaskCount').textContent = c.task_count ?? '--';
  document.getElementById('cpCode').textContent = c.code || '--';
  document.getElementById('cpDesc').textContent = c.description || '';

  // Reset tabs
  switchCPTab('students');

  // Show panel
  document.getElementById('classPanel').classList.add('on');
  document.body.style.overflow = 'hidden';

  // Load students
  loadPanelStudents(classId);
}

function closeClassPanel() {
  document.getElementById('classPanel').classList.remove('on');
  document.body.style.overflow = '';
  currentPanelClassId = null;
}

function switchCPTab(tab) {
  currentPanelTab = tab;
  document.getElementById('cpStudentsTab').style.display = tab === 'students' ? 'block' : 'none';
  document.getElementById('cpTasksTab').style.display    = tab === 'tasks'    ? 'block' : 'none';

  const btnS = document.getElementById('cpTabStudents');
  const btnT = document.getElementById('cpTabTasks');
  if (tab === 'students') {
    btnS.style.borderBottomColor = '#6366f1'; btnS.style.color = '#6366f1'; btnS.style.fontWeight = '700';
    btnT.style.borderBottomColor = 'transparent'; btnT.style.color = '#94a3b8'; btnT.style.fontWeight = '600';
    if (currentPanelClassId) loadPanelStudents(currentPanelClassId);
  } else {
    btnT.style.borderBottomColor = '#6366f1'; btnT.style.color = '#6366f1'; btnT.style.fontWeight = '700';
    btnS.style.borderBottomColor = 'transparent'; btnS.style.color = '#94a3b8'; btnS.style.fontWeight = '600';
    if (currentPanelClassId) loadPanelTasks(currentPanelClassId);
  }
}

async function loadPanelStudents(classId) {
  const list = document.getElementById('cpStudentList');
  list.innerHTML = '<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading student report...</div>';

  const {ok, data} = await apiFetch(`/admin/classes/${classId}/report`);
  if (!ok) { list.innerHTML = '<div style="padding:30px;text-align:center;color:#ef4444;font-size:13px">Failed to load students.</div>'; return; }

  const students = data.students || [];
  const tasks = data.tasks || [];

  // Update count badge
  document.getElementById('cpStudentCount').textContent = students.length;

  if (!students.length) {
    list.innerHTML = `<div style="padding:40px 20px;text-align:center;color:#94a3b8">
      <div style="width:44px;height:44px;border-radius:12px;background:#f0f1ff;border:1px solid #e0e2ff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px">
        <svg style="width:18px;height:18px;color:#6366f1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No students enrolled</p>
      <p style="font-size:12px;margin-bottom:12px">Add students to this class.</p>
      <button onclick="manageStudentsFromPanel()" class="btn-primary" style="margin:0 auto;font-size:12px">Add Students</button>
    </div>`;
    return;
  }

  let thHTML = `<th>Rank</th><th>Student</th><th>Total Points</th><th>Avg Grade</th>`;
  tasks.forEach(t => {
      thHTML += `<th class="hide-mobile" style="min-width:100px">Task: ${t.title}</th>`;
  });
  thHTML += `<th>Actions</th>`;
  
  let trHTML = students.map(s => {
      let tds = `
        <td class="mono" style="font-size:12px;font-weight:700;color:#6366f1">#${s.rank}</td>
        <td>
          <div style="display:flex;align-items:center;gap:8px">
            <div style="width:28px;height:28px;border-radius:50%;background:#f0f1ff;color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;border:1px solid #e0e2ff">${getInitials(s.name)}</div>
            <div>
              <p style="font-size:13px;font-weight:600;color:#1e2130">${s.name}</p>
              <p style="font-size:10px;color:#94a3b8">${s.email}</p>
            </div>
          </div>
        </td>
        <td class="mono" style="font-size:13px;font-weight:700;color:#1e2130">${s.total_points}</td>
        <td class="mono" style="font-size:13px;color:#64748b">${s.average_grade}</td>
      `;
      tasks.forEach(t => {
          let g = s.grades[t.id];
          let formattedGrade = g !== null ? `<span style="color:#6366f1">${g}</span>` : `<span style="color:#94a3b8">—</span>`;
          tds += `<td class="hide-mobile mono" style="font-size:12px">${formattedGrade} <span style="font-size:9px;color:#cbd5e1">/ ${t.max_grade}</span></td>`;
      });
      tds += `
        <td>
            <div style="display:flex;align-items:center;gap:8px">
                <button onclick="removePanelStudent(${s.id})" class="btn-danger" style="padding:3px 7px;font-size:10px">✕</button>
            </div>
        </td>
      `;
      return `<tr class="table-row">${tds}</tr>`;
  }).join('');
  
  list.innerHTML = `
    <div style="overflow-x:auto;width:100%">
      <table style="width:100%;border-collapse:collapse;white-space:nowrap">
        <thead><tr class="table-head">${thHTML}</tr></thead>
        <tbody>${trHTML}</tbody>
      </table>
    </div>
  `;
}

async function removePanelStudent(studentId) {
  if (!currentPanelClassId) return;
  const {ok, data} = await apiFetch(`/admin/classes/${currentPanelClassId}/students/${studentId}`, {method:'DELETE'});
  if (ok) {
    loadPanelStudents(currentPanelClassId);
    loadDashClasses();
  } else { alert(data.message || 'Could not remove student.'); }
}

async function loadPanelTasks(classId) {
  const list = document.getElementById('cpTaskList');
  list.innerHTML = '<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading tasks...</div>';

  // Get tasks via admin/tasks filtered by class context
  const {ok, data} = await apiFetch('/admin/tasks');
  if (!ok) { list.innerHTML = '<div style="padding:30px;text-align:center;color:#ef4444;font-size:13px">Failed to load.</div>'; return; }

  // Filter tasks relevant to this class via teacher_subject class_id
  const c = dashClasses.find(x => x.id === classId) || allClasses.find(x => x.id === classId);
  // Show all tasks from the class's teacher_subject if possible, otherwise show all
  const tasks = data.filter ? data.filter(t => {
    return t.teacher_subject?.class_id === classId || t.class_id === classId;
  }) : data;

  document.getElementById('cpTaskCount').textContent = tasks.length;

  if (!tasks.length) {
    list.innerHTML = `<div style="padding:40px 20px;text-align:center;color:#94a3b8">
      <div style="width:44px;height:44px;border-radius:12px;background:#ecfdf5;border:1px solid #bbf7d0;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px">
        <svg style="width:18px;height:18px;color:#10b981" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No tasks for this class</p>
      <p style="font-size:12px">Tasks are assigned through teacher subjects.</p>
    </div>`;
    return;
  }

  list.innerHTML = tasks.map(t => {
    const expired = t.due_date && new Date(t.due_date) < new Date();
    return `<div style="padding:12px 20px;border-bottom:1px solid #f4f5f9;display:flex;align-items:center;justify-content:space-between">
      <div style="min-width:0;flex:1;margin-right:10px">
        <p style="font-size:13px;font-weight:600;color:#1e2130;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${t.title}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:2px">${t.teacher_subject?.subject?.name||'--'} · ${t.teacher_subject?.teacher?.user?.name||'--'}</p>
      </div>
      <div style="text-align:right;flex-shrink:0">
        <p style="font-size:11px;font-family:'JetBrains Mono',monospace;color:${expired?'#ef4444':'#94a3b8'}">${formatDateShort(t.due_date)}</p>
        <p style="font-size:10px;color:#64748b;margin-top:2px">${t.submissions_count||0} submissions</p>
      </div>
    </div>`;
  }).join('');
}

function exportClassData() {
  if (!currentPanelClassId) return;
  window.location.href = `/admin/classes/${currentPanelClassId}/export`;
}

function editClassFromPanel() {
  if (!currentPanelClassId) return;
  const id = currentPanelClassId;
  closeClassPanel();
  setTimeout(() => {
    // If on classes page, use allClasses; otherwise use dashClasses
    if (!allClasses.length) allClasses = dashClasses;
    openEditClass(id);
  }, 300);
}

function manageStudentsFromPanel() {
  if (!currentPanelClassId) return;
  const c = dashClasses.find(x => x.id === currentPanelClassId) || allClasses.find(x => x.id === currentPanelClassId);
  closeClassPanel();
  setTimeout(() => openClassStudents(currentPanelClassId, c?.name || 'Class'), 300);
}

function deleteClassFromPanel() {
  if (!currentPanelClassId) return;
  const c = dashClasses.find(x => x.id === currentPanelClassId) || allClasses.find(x => x.id === currentPanelClassId);
  closeClassPanel();
  setTimeout(() => askDelete('class', currentPanelClassId, c?.name || 'this class'), 300);
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeClassPanel(); });


//  DASHBOARD

function renderActivityFeed() {
  const el = document.getElementById('actList');
  if (!recentActivity.length) { el.innerHTML='<div style="padding:36px;text-align:center;color:#94a3b8;font-size:13px">No point activity yet.</div>'; return; }
  el.innerHTML = recentActivity.map(r=>`
    <div style="padding:11px 18px;border-bottom:1px solid #f4f5f9;display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:30px;height:30px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#64748b;font-weight:700;font-size:10px;flex-shrink:0">${getInitials(r.student)}</div>
        <div><p style="font-size:13px;font-weight:600;color:#1e2130">${r.student}</p><p style="font-size:11px;color:#94a3b8">by ${r.teacher}</p></div>
      </div>
      <span class="mono" style="font-size:13px;font-weight:700;color:${r.type==='positive'?'#059669':'#ef4444'};flex-shrink:0">${r.points>0?'+':''}${r.points} pts</span>
    </div>`).join('');
}


//  USERS

let allUsers=[], editUserId=null;

async function loadUsers() {
  const q=document.getElementById('uSearch').value.trim(), rf=document.getElementById('uRoleF').value;
  const p=new URLSearchParams(); if(q) p.append('search',q); if(rf) p.append('role',rf);
  const {ok,data} = await apiFetch(`/users?${p}`);
  if(ok){ allUsers=data; renderUsers(); }
}

function renderUsers() {
  const tbody=document.getElementById('uBody'), empty=document.getElementById('uEmpty');
  if(!allUsers.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=allUsers.map(u=>{
    const safe=(u.name||'').replace(/'/g,"\\'"), rc=ROLE_COLORS[u.role]||ROLE_COLORS.student;
    return `<tr class="table-row">
      <td><div style="display:flex;align-items:center;gap:8px"><div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0">${getInitials(u.name)}</div><span style="font-size:13px;font-weight:600;color:#1e2130">${u.name}</span></div></td>
      <td class="hide-mobile" style="font-size:12px;color:#64748b">${u.email}</td>
      <td><span class="tag" style="${rc}">${u.role}</span></td>
      <td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(u.created_at)}</td>
      <td style="text-align:right"><div style="display:flex;gap:4px;justify-content:flex-end"><button onclick="openEditUser(${u.id})" class="btn-edit">Edit</button><button onclick="askDelete('user',${u.id},'${safe}')" class="btn-danger">Del</button></div></td>
    </tr>`;
  }).join('');
}

function openAddUser() {
  editUserId=null;
  document.getElementById('mUserTitle').textContent='Add New User';
  document.getElementById('uSaveBtn').textContent='Add User';
  ['uName','uUsername','uEmail','uPass'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('uRole').value='';
  document.getElementById('uPassWrap').style.display='block';
  openModal('mUser');
}

function openEditUser(id) {
  editUserId=id;
  const u=allUsers.find(x=>x.id===id);
  document.getElementById('mUserTitle').textContent='Edit User';
  document.getElementById('uSaveBtn').textContent='Save Changes';
  document.getElementById('uName').value=u.name;
  document.getElementById('uUsername').value=u.username||'';
  document.getElementById('uEmail').value=u.email;
  document.getElementById('uRole').value=u.role;
  document.getElementById('uPass').value='';
  document.getElementById('uPassWrap').style.display='none';
  openModal('mUser');
}

async function saveUser() {
  const name=document.getElementById('uName').value.trim(), email=document.getElementById('uEmail').value.trim(),
        role=document.getElementById('uRole').value, password=document.getElementById('uPass').value,
        username=document.getElementById('uUsername').value.trim();
  if(!name||!email||!role){ alert('Please fill all fields.'); return; }
  if(!editUserId&&!password){ alert('Password required.'); return; }
  const body={name,email,role,username}; if(!editUserId) body.password=password;
  const {ok,data} = await apiFetch(editUserId?`/users/${editUserId}`:'/users',{method:editUserId?'PUT':'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  if(ok){ closeModal('mUser'); loadUsers(); } else { alert(data.message||Object.values(data.errors||{})[0]?.[0]||'Something went wrong.'); }
}

document.getElementById('uSearch').addEventListener('input', loadUsers);
document.getElementById('uRoleF').addEventListener('change', loadUsers);


//  CLASSES

let allClasses=[], editClassId=null;

async function loadClasses() {
  const grid=document.getElementById('clGrid'), empty=document.getElementById('clEmpty');
  grid.innerHTML='<div style="grid-column:1/-1;padding:40px;text-align:center;color:#94a3b8;font-size:13px">Loading...</div>';

  const {ok,data} = await apiFetch('/admin/classes');
  if(!ok){ grid.innerHTML=''; empty.style.display='block'; return; }

  allClasses = data.classes || data;

  if(data.subjects){
    const sel=document.getElementById('clSubjF'), cur=sel.value;
    sel.innerHTML='<option value="">All Subjects</option>'+data.subjects.map(s=>`<option value="${s.id}"${s.id==cur?' selected':''}>${s.name}</option>`).join('');
  }
  if(data.teachers){
    const sel=document.getElementById('clTeachF'), cur=sel.value;
    sel.innerHTML='<option value="">All Teachers</option>'+data.teachers.map(t=>`<option value="${t.id}"${t.id==cur?' selected':''}>${t.name}</option>`).join('');
  }

  const totalStudents = allClasses.reduce((s,c)=>s+(c.student_count||0),0);
  const totalTasks    = allClasses.reduce((s,c)=>s+(c.task_count||0),0);
  document.getElementById('clStatTotal').textContent    = allClasses.length;
  document.getElementById('clStatStudents').textContent = totalStudents;
  document.getElementById('clStatTasks').textContent    = totalTasks;

  filterClasses();
}

function filterClasses() {
  const q      = (document.getElementById('clSearch')?.value||'').toLowerCase();
  const subjId = document.getElementById('clSubjF')?.value;
  const teachId= document.getElementById('clTeachF')?.value;

  const filtered = allClasses.filter(c => {
    const matchQ     = !q     || (c.name||'').toLowerCase().includes(q) || (c.code||'').toLowerCase().includes(q);
    const matchSubj  = !subjId  || String(c.subject_id)===String(subjId);
    const matchTeach = !teachId || String(c.teacher_id)===String(teachId);
    return matchQ && matchSubj && matchTeach;
  });

  renderClasses(filtered);
}

function renderClasses(list) {
  const grid=document.getElementById('clGrid'), empty=document.getElementById('clEmpty');
  if(!list.length){ grid.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';

  grid.innerHTML = list.map((c,i)=>{
    const [from,to] = CLS_PALETTES[i % CLS_PALETTES.length];
    const initial   = (c.name||'?')[0].toUpperCase();
    const teacher   = c.teacher_name||'--';
    const subject   = c.subject_name||'--';
    const safe      = (c.name||'').replace(/'/g,"\\'");
    return `
      <div class="cls-card" style="cursor:pointer" onclick="openClassPanel(${c.id})">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
          <div style="display:flex;align-items:center;gap:10px;min-width:0">
            <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,${from},${to});display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-weight:800;font-size:17px">${initial}</div>
            <div style="min-width:0">
              <p style="font-size:14px;font-weight:700;color:#1e2130;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${c.name||'--'}</p>
              <p style="font-size:11px;color:#64748b;margin-top:1px">${subject}</p>
            </div>
          </div>
          <div style="display:flex;gap:4px;flex-shrink:0" onclick="event.stopPropagation()">
            <button onclick="openEditClass(${c.id})" class="btn-edit" style="padding:4px 8px;font-size:11px">Edit</button>
            <button onclick="askDelete('class',${c.id},'${safe}')" class="btn-danger" style="padding:4px 8px;font-size:11px">Del</button>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f9fafb;border-radius:9px;border:1px solid #e8eaef">
          <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,${from},${to});display:flex;align-items:center;justify-content:center;color:#fff;font-size:9px;font-weight:700;flex-shrink:0">${(teacher[0]||'?').toUpperCase()}</div>
          <div>
            <p style="font-size:9px;font-weight:600;color:#6366f1;text-transform:uppercase;letter-spacing:0.04em">Teacher</p>
            <p style="font-size:12px;font-weight:600;color:#1e2130">${teacher}</p>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px">
          <div style="background:#f0f1ff;border-radius:8px;padding:7px;text-align:center;border:1px solid #e0e2ff">
            <p style="font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:700;color:#6366f1">${c.task_count??'--'}</p>
            <p style="font-size:9px;color:#64748b;font-weight:600;margin-top:1px">Tasks</p>
          </div>
          <div style="background:#eff6ff;border-radius:8px;padding:7px;text-align:center;border:1px solid #dbeafe">
            <p style="font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:700;color:#3b82f6">${c.student_count??'--'}</p>
            <p style="font-size:9px;color:#64748b;font-weight:600;margin-top:1px">Students</p>
          </div>
          <div style="background:#f0fdf4;border-radius:8px;padding:7px;text-align:center;border:1px solid #bbf7d0;cursor:pointer" onclick="event.stopPropagation();openClassStudents(${c.id},'${safe}')">
            <p style="font-size:9px;color:#059669;font-weight:700;text-transform:uppercase;letter-spacing:0.03em">Manage</p>
            <p style="font-size:9px;color:#64748b;font-weight:600;margin-top:1px">Students</p>
          </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid #f4f5f9">
          <div style="display:flex;align-items:center;gap:5px">
            <span style="font-size:9px;font-weight:600;color:#94a3b8;text-transform:uppercase">Code</span>
            <span style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:#6366f1;background:#f0f1ff;padding:2px 7px;border-radius:5px;border:1px solid #e0e2ff">${c.code||'--'}</span>
          </div>
          <span style="font-size:10px;color:#94a3b8">${formatDateShort(c.created_at)}</span>
        </div>
      </div>`;
  }).join('');
}

async function openAddClass() {
  editClassId=null;
  document.getElementById('mClassTitle').textContent='Add Class';
  document.getElementById('clsSaveBtn').textContent='Add Class';
  ['clsName','clsCode','clsDesc'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('clsSubject').innerHTML='<option value="">Loading...</option>';
  document.getElementById('clsTeacher').innerHTML='<option value="">Loading...</option>';
  openModal('mClass');
  await populateClassForm();
}

async function openEditClass(id) {
  editClassId=id;
  // Try both caches
  const c = allClasses.find(x=>x.id===id) || dashClasses.find(x=>x.id===id);
  document.getElementById('mClassTitle').textContent='Edit Class';
  document.getElementById('clsSaveBtn').textContent='Save Changes';
  document.getElementById('clsName').value=c?.name||'';
  document.getElementById('clsCode').value=c?.code||'';
  document.getElementById('clsDesc').value=c?.description||'';
  document.getElementById('clsSubject').innerHTML='<option value="">Loading...</option>';
  document.getElementById('clsTeacher').innerHTML='<option value="">Loading...</option>';
  openModal('mClass');
  await populateClassForm();
  if(c) {
    document.getElementById('clsSubject').value=c.subject_id||'';
    document.getElementById('clsTeacher').value=c.teacher_id||'';
  }
}

async function populateClassForm() {
  const {ok,data} = await apiFetch('/admin/classes');
  if(!ok) return;
  const subjects = data.subjects || [];
  const teachers = data.teachers || [];
  document.getElementById('clsSubject').innerHTML='<option value="">-- Select --</option>'+subjects.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
  document.getElementById('clsTeacher').innerHTML='<option value="">-- Select --</option>'+teachers.map(t=>`<option value="${t.id}">${t.name}</option>`).join('');
}

async function saveClass() {
  const name        = document.getElementById('clsName').value.trim();
  const subjectId   = document.getElementById('clsSubject').value;
  const teacherId   = document.getElementById('clsTeacher').value;
  const code        = document.getElementById('clsCode').value.trim().toUpperCase() || null;
  const description = document.getElementById('clsDesc').value.trim() || null;

  if (!name) { alert('Class name is required.'); return; }

  const btn = document.getElementById('clsSaveBtn');
  btn.disabled = true;
  btn.textContent = 'Saving...';

  const {ok, data} = await apiFetch(
    editClassId ? `/admin/classes/${editClassId}` : '/admin/classes',
    {
      method: editClassId ? 'PUT' : 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        name,
        subject_id: subjectId || null,
        teacher_id: teacherId || null,
        code,
        description
      })
    }
  );

  btn.disabled = false;
  btn.textContent = editClassId ? 'Save Changes' : 'Add Class';

  if (ok) {
    closeModal('mClass');
    // Refresh both dashboard classes and classes page if open
    loadDashClasses();
    if (document.getElementById('p-classes').classList.contains('on')) {
      loadClasses();
    }
    // Show success feedback
    showToast(editClassId ? 'Class updated!' : 'Class created!', 'success');
  } else {
    const msg = data.message || Object.values(data.errors||{})[0]?.[0] || 'Something went wrong.';
    alert(msg);
  }
}

// Toast notification
function showToast(message, type='success') {
  const toast = document.createElement('div');
  toast.style.cssText = `position:fixed;bottom:80px;right:20px;z-index:9999;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;font-family:'Syne',sans-serif;box-shadow:0 8px 24px rgba(0,0,0,0.15);transition:all 0.3s;opacity:0;transform:translateY(10px);display:flex;align-items:center;gap:8px;`;
  if (type === 'success') {
    toast.style.background = '#ecfdf5';
    toast.style.color = '#059669';
    toast.style.border = '1px solid #6ee7b7';
    toast.innerHTML = `<svg style="width:14px;height:14px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>${message}`;
  } else {
    toast.style.background = '#fef2f2';
    toast.style.color = '#ef4444';
    toast.innerHTML = message;
  }
  document.body.appendChild(toast);
  requestAnimationFrame(() => { toast.style.opacity='1'; toast.style.transform='translateY(0)'; });
  setTimeout(() => { toast.style.opacity='0'; toast.style.transform='translateY(10px)'; setTimeout(()=>toast.remove(), 300); }, 3000);
}

// Manage students modal
async function openClassStudents(classId, className) {
  document.getElementById('csClassId').value=classId;
  document.getElementById('mCSTitle').textContent=`Students — ${className}`;
  document.getElementById('mCSMeta').textContent='Loading...';
  document.getElementById('csStuList').innerHTML='<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading...</div>';

  const {ok:uOk, data:uData} = await apiFetch('/users?role=student');
  if(uOk) document.getElementById('csAddSelect').innerHTML='<option value="">-- Add a student --</option>'+uData.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');

  await refreshClassStudentList(classId);
  openModal('mClassStudents');
}

async function refreshClassStudentList(classId) {
  const id = classId || document.getElementById('csClassId').value;
  const {ok,data} = await apiFetch(`/admin/classes/${id}/students`);
  const list = document.getElementById('csStuList');
  const meta = document.getElementById('mCSMeta');
  if(!ok){ list.innerHTML='<div style="padding:30px;text-align:center;color:#ef4444;font-size:13px">Could not load students.</div>'; return; }
  const students = data.students||[];
  meta.textContent=`${students.length} enrolled student${students.length!==1?'s':''}`;
  if(!students.length){ list.innerHTML='<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">No students enrolled yet.</div>'; return; }
  list.innerHTML=students.map(s=>`
    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #f4f5f9">
      <div style="display:flex;align-items:center;gap:9px">
        <div style="width:28px;height:28px;border-radius:50%;background:#f0f1ff;color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0">${getInitials(s.name)}</div>
        <div>
          <p style="font-size:13px;font-weight:600;color:#1e2130">${s.name}</p>
          <p style="font-size:11px;color:#94a3b8">${s.email} · Joined ${formatDateShort(s.joined_at)}</p>
        </div>
      </div>
      <button onclick="removeStudentFromClass(${s.id})" class="btn-danger" style="padding:4px 8px;font-size:11px;flex-shrink:0">Remove</button>
    </div>`).join('');
}

async function addStudentToClass() {
  const classId=document.getElementById('csClassId').value;
  const studentId=document.getElementById('csAddSelect').value;
  if(!studentId){ alert('Select a student first.'); return; }
  const {ok,data} = await apiFetch(`/admin/classes/${classId}/students`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({student_id:studentId})});
  if(ok){
    document.getElementById('csAddSelect').value='';
    refreshClassStudentList(classId);
    loadDashClasses();
    if (currentPanelClassId == classId) loadPanelStudents(classId);
  }
  else { alert(data.message||'Could not add student.'); }
}

async function removeStudentFromClass(studentId) {
  const classId=document.getElementById('csClassId').value;
  const {ok,data} = await apiFetch(`/admin/classes/${classId}/students/${studentId}`,{method:'DELETE'});
  if(ok){ refreshClassStudentList(classId); loadDashClasses(); }
  else   { alert(data.message||'Could not remove student.'); }
}


//  TASKS

let allTasks=[], editTaskId=null, pendingFiles=[];

function previewTaskFiles(input) {
  pendingFiles = Array.from(input.files);
  const grid = document.getElementById('tFP');
  if (!pendingFiles.length) { grid.style.display = 'none'; return; }
  grid.style.display = 'grid';
  grid.innerHTML = pendingFiles.map((f, i) =>
    `<div style="position:relative;background:#f1f5f9;border-radius:8px;padding:6px;font-size:10px;text-align:center">${f.name.slice(0,12)}<button type="button" onclick="removeTaskFile(${i})" style="position:absolute;top:-4px;right:-4px;width:16px;height:16px;border-radius:50%;background:#ef4444;color:#fff;border:none;font-size:10px;cursor:pointer">×</button></div>`
  ).join('');
}
function removeTaskFile(i) {
  pendingFiles.splice(i, 1);
  const dt = new DataTransfer();
  pendingFiles.forEach(f => dt.items.add(f));
  document.getElementById('tFI').files = dt.files;
  previewTaskFiles(document.getElementById('tFI'));
}

async function loadTasks() {
  const {ok,data} = await apiFetch('/admin/tasks');
  if(ok){ allTasks=data; renderTasks(); }
}

function renderTasks() {
  const tbody=document.getElementById('tBody'), empty=document.getElementById('tEmpty');
  if(!allTasks.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=allTasks.map(t=>{
    const safe=(t.title||'').replace(/'/g,"\\'"), expired=t.due_date&&new Date(t.due_date)<new Date();
    return `<tr class="table-row">
      <td><p style="font-size:13px;font-weight:600;color:#1e2130">${t.title}</p><p class="hide-mobile" style="font-size:11px;color:#94a3b8;margin-top:1px">${t.description||'--'}</p></td>
      <td class="hide-mobile" style="font-size:13px;color:#64748b">${t.teacher_subject?.teacher?.user?.name||'--'}</td>
      <td class="hide-mobile" style="font-size:13px;color:#64748b">${t.teacher_subject?.subject?.name||'--'}</td>
      <td style="font-size:12px;color:${expired?'#ef4444':'#94a3b8'};font-family:'JetBrains Mono',monospace">${formatDateShort(t.due_date)}</td>
      <td class="hide-mobile"><span class="tag" style="background:#f1f5f9;color:#64748b">${t.submissions_count||0}</span></td>
      <td style="text-align:right"><div style="display:flex;gap:4px;justify-content:flex-end"><button onclick="openDrawer(${t.id})" class="btn-secondary" style="padding:4px 8px;font-size:11px">View</button><button onclick="openEditTask(${t.id})" class="btn-edit">Edit</button><button onclick="askDelete('task',${t.id},'${safe}')" class="btn-danger">Del</button></div></td>
    </tr>`;
  }).join('');
}

async function populateTeacherSubjectSelect(selectId) {
  const {ok,data} = await apiFetch('/admin/teacher-subjects');
  if(!ok) return;
  document.getElementById(selectId).innerHTML='<option value="">-- Select --</option>'+data.map(ts=>`<option value="${ts.id}">${ts.teacher||'?'} — ${ts.subject||'?'} (${ts.class||'?'})</option>`).join('');
}

function openAddTask() {
  editTaskId=null;
  document.getElementById('mTaskTitle').textContent='Add Task';
  document.getElementById('tSaveBtn').textContent='Add Task';
  ['tTitle','tDesc','tDue'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('tGrade').value='100';
  pendingFiles=[]; document.getElementById('tFI').value=''; document.getElementById('tFP').style.display='none';
  populateTeacherSubjectSelect('tTS');
  openModal('mTask');
}

async function openEditTask(id) {
  editTaskId=id;
  const t=allTasks.find(x=>x.id===id);
  document.getElementById('mTaskTitle').textContent='Edit Task';
  document.getElementById('tSaveBtn').textContent='Save Changes';
  document.getElementById('tTitle').value=t.title;
  document.getElementById('tDesc').value=t.description||'';
  document.getElementById('tDue').value=t.due_date||'';
  document.getElementById('tGrade').value=t.max_grade||'100';
  pendingFiles=[]; document.getElementById('tFI').value=''; document.getElementById('tFP').style.display='none';
  await populateTeacherSubjectSelect('tTS');
  document.getElementById('tTS').value=t.teacher_subject_id;
  openModal('mTask');
}

async function saveTask() {
  const title=document.getElementById('tTitle').value.trim(), ts=document.getElementById('tTS').value;
  if(!title||!ts){ alert('Title and Teacher Subject required.'); return; }
  const fd=new FormData();
  fd.append('title', title);
  fd.append('description', document.getElementById('tDesc').value.trim());
  fd.append('teacher_subject_id', ts);
  if (document.getElementById('tDue').value) fd.append('due_date', document.getElementById('tDue').value);
  const maxGrade = document.getElementById('tGrade').value;
  if (maxGrade) fd.append('max_grade', maxGrade);
  pendingFiles.forEach((f, i) => fd.append(`files[${i}]`, f));
  if (editTaskId) fd.append('_method', 'PUT');
  const {ok,data} = await apiFetch(editTaskId?`/admin/tasks/${editTaskId}`:'/admin/tasks',{method:'POST',body:fd});
  if(ok){ closeModal('mTask'); loadTasks(); } else { alert(data.message||Object.values(data.errors||{})[0]?.[0]||'Something went wrong.'); }
}


//  POINTS

let pendingSubmissionId=null, drawerTaskId=null;

async function openPointModal() {
  pendingSubmissionId=null;
  const {ok,data} = await apiFetch('/admin/students-for-points');
  if(ok) document.getElementById('pStudent').innerHTML='<option value="">-- Select --</option>'+data.map(s=>`<option value="${s.id}">${s.name} (${s.class})</option>`).join('');
  await populateTeacherSubjectSelect('pTS');
  document.getElementById('pDate').value=new Date().toISOString().split('T')[0];
  document.getElementById('pPts').value='';
  document.getElementById('pDesc').value='';
  openModal('mPoint');
}

async function openPointModalFromDrawer(studentId, teacherSubjectId, submissionId) {
  pendingSubmissionId=submissionId||null;
  const {ok,data} = await apiFetch('/admin/students-for-points');
  if(ok){ document.getElementById('pStudent').innerHTML='<option value="">-- Select --</option>'+data.map(s=>`<option value="${s.id}">${s.name} (${s.class})</option>`).join(''); document.getElementById('pStudent').value=studentId; }
  await populateTeacherSubjectSelect('pTS');
  document.getElementById('pTS').value=teacherSubjectId;
  document.getElementById('pDate').value=new Date().toISOString().split('T')[0];
  document.getElementById('pPts').value='';
  document.getElementById('pDesc').value='';
  openModal('mPoint');
}

async function savePoint() {
  const sid=document.getElementById('pStudent').value, tsid=document.getElementById('pTS').value, pts=document.getElementById('pPts').value;
  if(!sid||!tsid||!pts){ alert('Fill Student, Subject, and Points.'); return; }
  if(pts==0){ alert('Points cannot be zero.'); return; }
  const payload={student_id:sid,teacher_subject_id:tsid,point:pts,description:document.getElementById('pDesc').value.trim(),point_date:document.getElementById('pDate').value};
  if(pendingSubmissionId) payload.task_submission_id=pendingSubmissionId;
  const {ok,data} = await apiFetch('/admin/points',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
  if(ok){ closeModal('mPoint'); loadPointHistory(); if(pendingSubmissionId&&drawerTaskId) openDrawer(drawerTaskId); pendingSubmissionId=null; }
  else   { alert(data.message||'Something went wrong.'); }
}

async function loadPointHistory() {
  const {ok,data} = await apiFetch('/reports');
  if(!ok) return;
  const tbody=document.getElementById('ptBody'), empty=document.getElementById('ptEmpty');
  if(!data.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=data.map((r,i)=>`
    <tr class="table-row">
      <td class="mono" style="font-size:11px;color:#94a3b8">${String(i+1).padStart(2,'0')}</td>
      <td><div style="display:flex;align-items:center;gap:7px"><div style="width:26px;height:26px;border-radius:50%;background:#f1f5f9;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700">${getInitials(r.student)}</div><span style="font-size:13px;font-weight:600;color:#1e2130">${r.student}</span></div></td>
      <td class="hide-mobile" style="font-size:12px;color:#64748b">${r.teacher}·${r.subject||'--'}</td>
      <td class="hide-mobile" style="font-size:12px;color:#94a3b8;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.description||'--'}</td>
      <td class="mono" style="font-weight:700;font-size:13px;color:${r.type==='positive'?'#059669':'#ef4444'}">${r.points>0?'+':''}${r.points}</td>
      <td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(r.date)}</td>
    </tr>`).join('');
}


//  SUBJECTS

let allSubjects=[], editSubjectId=null;

async function loadSubjects() {
  const {ok,data} = await apiFetch('/subjects');
  if(ok){ allSubjects=data; renderSubjects(); }
}

function renderSubjects() {
  const tbody=document.getElementById('sBody'), empty=document.getElementById('sEmpty');
  if(!allSubjects.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=allSubjects.map(s=>{
    const safe=(s.name||'').replace(/'/g,"\\'");
    return `<tr class="table-row"><td style="font-size:13px;font-weight:600;color:#1e2130">${s.name}</td><td style="text-align:right"><div style="display:flex;gap:4px;justify-content:flex-end"><button onclick="openEditSubject(${s.id})" class="btn-edit">Edit</button><button onclick="askDelete('subj',${s.id},'${safe}')" class="btn-danger">Del</button></div></td></tr>`;
  }).join('');
}

function openAddSubject() {
  editSubjectId=null;
  document.getElementById('mSubjTitle').textContent='Add Subject';
  document.getElementById('sSaveBtn').textContent='Add Subject';
  document.getElementById('sName').value='';
  openModal('mSubj');
}

function openEditSubject(id) {
  editSubjectId=id;
  const s=allSubjects.find(x=>x.id===id);
  document.getElementById('mSubjTitle').textContent='Edit Subject';
  document.getElementById('sSaveBtn').textContent='Save Changes';
  document.getElementById('sName').value=s.name;
  openModal('mSubj');
}

async function saveSubject() {
  const name=document.getElementById('sName').value.trim();
  if(!name){ alert('Enter a subject name.'); return; }
  const {ok,data} = await apiFetch(editSubjectId?`/subjects/${editSubjectId}`:'/subjects',{method:editSubjectId?'PUT':'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name})});
  if(ok){ closeModal('mSubj'); loadSubjects(); } else { alert(data.message||'Something went wrong.'); }
}


//  TEACHER-SUBJECT

let allTeacherSubjects=[], editTeacherSubjectId=null;

async function loadTeacherSubjects() {
  const {ok,data} = await apiFetch('/admin/teacher-subjects');
  if(ok){ allTeacherSubjects=data; filterTeacherSubjects(); }
}

function filterTeacherSubjects() {
  const q=(document.getElementById('tsSearch')?.value||'').toLowerCase();
  renderTeacherSubjects(q?allTeacherSubjects.filter(ts=>ts.teacher.toLowerCase().includes(q)):allTeacherSubjects);
}

function renderTeacherSubjects(data) {
  const tbody=document.getElementById('tsBody'), empty=document.getElementById('tsEmpty');
  if(!data.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=data.map(ts=>{
    const safe=(ts.teacher||'').replace(/'/g,"\\'");
    return `<tr class="table-row">
      <td><div style="display:flex;align-items:center;gap:7px"><div style="width:26px;height:26px;border-radius:50%;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0">${getInitials(ts.teacher)}</div><span style="font-size:13px;font-weight:600;color:#1e2130">${ts.teacher}</span></div></td>
      <td style="font-size:13px;color:#64748b">${ts.subject}</td>
      <td class="hide-mobile"><span class="tag" style="background:#f1f5f9;color:#64748b">${ts.class}</span></td>
      <td style="text-align:right"><div style="display:flex;gap:4px;justify-content:flex-end"><button onclick="openEditTeacherSubject(${ts.id})" class="btn-edit">Edit</button><button onclick="askDelete('ts',${ts.id},'${safe}')" class="btn-danger">Del</button></div></td>
    </tr>`;
  }).join('');
}

async function openAddTeacherSubject() {
  editTeacherSubjectId=null;
  document.getElementById('mTSTitle').textContent='Assign Teacher';
  document.getElementById('tsSaveBtn').textContent='Assign';
  await loadTeacherSubjectFormData();
  ['tsTeacher','tsSubj','tsClass', 'clsTeacher'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('tsNewWrap').style.display='none';
  document.getElementById('tsNewName').value='';
  openModal('mTS');
}

async function openEditTeacherSubject(id) {
  editTeacherSubjectId=id;
  const ts=allTeacherSubjects.find(x=>x.id===id);
  document.getElementById('mTSTitle').textContent='Edit Assignment';
  document.getElementById('tsSaveBtn').textContent='Save Changes';
  await loadTeacherSubjectFormData();
  document.getElementById('tsTeacher').value=ts.teacher_id;
  document.getElementById('tsSubj').value=ts.subject_id;
  document.getElementById('tsClass').value=ts.class_id;
  document.getElementById('tsNewWrap').style.display='none';
  openModal('mTS');
}

async function loadTeacherSubjectFormData() {
  const {ok,data} = await apiFetch('/admin/teacher-subjects/form-data');
  if(!ok) return;
  document.getElementById('tsTeacher').innerHTML='<option value="">-- Select --</option>'+data.teachers.map(t=>`<option value="${t.id}">${t.name}</option>`).join('')+'<option value="__new__">+ Add new teacher...</option>';
  document.getElementById('tsSubj').innerHTML='<option value="">-- Select --</option>'+data.subjects.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
  document.getElementById('tsClass').innerHTML='<option value="">-- Select --</option>'+data.classes.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
}

function onTeacherSelectChange() {
  const isNew=document.getElementById('tsTeacher').value==='__new__';
  document.getElementById('tsNewWrap').style.display=isNew?'block':'none';
  if(isNew) document.getElementById('tsNewName').focus();
  else      document.getElementById('tsNewName').value='';
}

async function saveTeacherSubject() {
  const tv=document.getElementById('tsTeacher').value, sid=document.getElementById('tsSubj').value, cid=document.getElementById('tsClass').value;
  if(!tv||!sid||!cid){ alert('Please fill all fields.'); return; }
  let teacherId=tv;
  if(tv==='__new__'){
    const nm=document.getElementById('tsNewName').value.trim(); if(!nm){ alert('Enter teacher name.'); return; }
    const {ok,data}=await apiFetch('/users',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name:nm,email:nm.toLowerCase().replace(/\s+/g,'')+'@school.edu',role:'teacher',password:'password'})});
    if(!ok){ alert(data.message||'Could not create teacher.'); return; }
    const {ok:rOk,data:ref}=await apiFetch('/admin/teacher-subjects/form-data');
    if(rOk){ const f=ref.teachers.find(t=>t.name===nm); if(!f){ alert('Teacher created but not found. Refresh.'); return; } teacherId=f.id; }
  }
  const {ok,data}=await apiFetch(editTeacherSubjectId?`/admin/teacher-subjects/${editTeacherSubjectId}`:'/admin/teacher-subjects',{method:editTeacherSubjectId?'PUT':'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({teacher_id:teacherId,subject_id:sid,class_id:cid})});
  if(ok){ closeModal('mTS'); loadTeacherSubjects(); } else { alert(data.message||'Something went wrong.'); }
}


//  REPORTS

async function loadReports() {
  const {ok:sOk,data:stats}=await apiFetch('/reports/stats');
  if(sOk){ document.getElementById('rTotal').textContent=Number(stats.total||0).toLocaleString(); document.getElementById('rAwarded').textContent=Number(stats.awarded||0).toLocaleString(); document.getElementById('rDeducted').textContent=Number(stats.deducted||0).toLocaleString(); document.getElementById('rTx').textContent=stats.transactions||0; }
  const q=document.getElementById('rSearch').value.trim(), tp=document.getElementById('rTypeF').value;
  const p=new URLSearchParams(); if(q) p.append('search',q); if(tp) p.append('type',tp);
  const {ok,data}=await apiFetch(`/reports?${p}`); if(ok) renderReports(data);
}

function renderReports(data) {
  const tbody=document.getElementById('rBody'), empty=document.getElementById('rEmpty');
  if(!data.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=data.map((r,i)=>`
    <tr class="table-row">
      <td class="mono" style="font-size:11px;color:#94a3b8">${String(i+1).padStart(2,'0')}</td>
      <td><div style="display:flex;align-items:center;gap:7px"><div style="width:26px;height:26px;border-radius:50%;background:#f1f5f9;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700">${getInitials(r.student)}</div><span style="font-size:13px;font-weight:600;color:#1e2130">${r.student}</span></div></td>
      <td class="hide-mobile" style="font-size:12px;color:#64748b">${r.teacher}</td>
      <td class="hide-mobile" style="font-size:12px;color:#64748b">${r.subject||'--'}</td>
      <td class="hide-mobile" style="font-size:12px;color:#94a3b8;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.description||'--'}</td>
      <td class="mono" style="font-weight:700;font-size:13px;color:${r.type==='positive'?'#059669':'#ef4444'}">${r.points>0?'+':''}${r.points}</td>
      <td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(r.date)}</td>
    </tr>`).join('');
}

document.getElementById('rSearch').addEventListener('input', loadReports);
document.getElementById('rTypeF').addEventListener('change', loadReports);


//  DELETE

let pendingDelete=null;

function askDelete(type, id, name) {
  pendingDelete={type,id};
  document.getElementById('delMsg').textContent=`Delete "${name}"? This cannot be undone.`;
  openModal('mDel');
}

async function confirmDelete() {
  if(!pendingDelete) return;
  const urls={user:`/users/${pendingDelete.id}`,task:`/admin/tasks/${pendingDelete.id}`,subj:`/subjects/${pendingDelete.id}`,ts:`/admin/teacher-subjects/${pendingDelete.id}`,class:`/admin/classes/${pendingDelete.id}`};
  const {ok,data}=await apiFetch(urls[pendingDelete.type],{method:'DELETE'});
  closeModal('mDel');
  if(ok){
    if(pendingDelete.type==='user')  loadUsers();
    if(pendingDelete.type==='task')  loadTasks();
    if(pendingDelete.type==='subj')  loadSubjects();
    if(pendingDelete.type==='ts')    loadTeacherSubjects();
    if(pendingDelete.type==='class') { loadDashClasses(); if(document.getElementById('p-classes').classList.contains('on')) loadClasses(); }
    showToast('Deleted successfully.');
  } else { alert(data.message||'Could not delete.'); }
}


//  TASK DRAWER

const drawerBackdrop=document.createElement('div');
drawerBackdrop.style.cssText='display:none;position:fixed;inset:0;background:rgba(10,22,40,.4);z-index:199;cursor:pointer;backdrop-filter:blur(2px)';
drawerBackdrop.onclick=closeDrawer;
document.body.appendChild(drawerBackdrop);

const drawerPanel=document.createElement('div');
drawerPanel.style.cssText='display:none;position:fixed;top:0;right:0;bottom:0;width:460px;max-width:100vw;background:#fff;box-shadow:-8px 0 40px rgba(13,148,136,.12);z-index:200;flex-direction:column;font-family:Syne,sans-serif;transition:transform .3s ease;transform:translateX(100%)';
drawerPanel.innerHTML=`
  <div style="padding:14px 18px;border-bottom:1px solid #d1faf4;display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
    <div style="min-width:0"><h2 id="dw-title" style="font-size:14px;font-weight:700;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Task</h2><p id="dw-sub" style="font-size:11px;color:#94a3b8;margin-top:1px"></p></div>
    <div style="display:flex;gap:7px;margin-left:10px;flex-shrink:0">
      <button id="dw-edit" onclick="openEditTaskFromDrawer()" style="background:#f0fdf9;color:#0d9488;padding:5px 10px;border-radius:7px;font-size:12px;font-weight:600;border:1px solid #99f6e4;cursor:pointer;font-family:Syne,sans-serif">Edit</button>
      <button onclick="closeDrawer()" style="width:28px;height:28px;border-radius:8px;background:#f0fdf9;border:none;cursor:pointer;font-size:16px;color:#64748b;line-height:1">×</button>
    </div>
  </div>
  <div style="padding:12px 18px;border-bottom:1px solid #d1faf4;background:#f0fdf9;flex-shrink:0">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px">
      <div style="background:#fff;border-radius:9px;padding:9px 11px;border:1px solid #d1faf4"><p style="font-size:9px;font-weight:600;color:#0d9488;text-transform:uppercase;margin-bottom:2px">Teacher</p><p id="dw-teacher" style="font-size:13px;font-weight:600;color:#1e293b">--</p></div>
      <div style="background:#fff;border-radius:9px;padding:9px 11px;border:1px solid #d1faf4"><p style="font-size:9px;font-weight:600;color:#0d9488;text-transform:uppercase;margin-bottom:2px">Subject</p><p id="dw-subject" style="font-size:13px;font-weight:600;color:#1e293b">--</p></div>
      <div style="background:#fffbeb;border-radius:9px;padding:9px 11px;border:1px solid #fde68a"><p style="font-size:9px;font-weight:600;color:#d97706;text-transform:uppercase;margin-bottom:2px">Due Date</p><p id="dw-due" style="font-size:13px;font-weight:600;color:#1e293b">--</p></div>
      <div style="background:#f5f3ff;border-radius:9px;padding:9px 11px;border:1px solid #e9d5ff"><p style="font-size:9px;font-weight:600;color:#7c3aed;text-transform:uppercase;margin-bottom:2px">Max Grade</p><p id="dw-grade" style="font-size:13px;font-weight:600;color:#1e293b">--</p></div>
    </div>
    <div style="background:#fff;border-radius:9px;padding:9px 11px;border:1px solid #d1faf4;margin-top:7px"><p style="font-size:9px;font-weight:600;color:#0d9488;text-transform:uppercase;margin-bottom:2px">Description</p><p id="dw-desc" style="font-size:13px;color:#475569">--</p></div>
  </div>
  <div style="padding:9px 18px;border-bottom:1px solid #d1faf4;display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
    <span style="font-size:13px;font-weight:700;color:#1e293b">Submissions</span>
    <span id="dw-count" style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:6px;background:#f0fdf9;color:#0d9488">--</span>
  </div>
  <div id="dw-list" style="flex:1;overflow-y:auto"><div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px">Select a task to view.</div></div>`;
document.body.appendChild(drawerPanel);

async function openDrawer(taskId) {
  drawerTaskId=taskId;
  const task=allTasks.find(t=>t.id===taskId); if(!task) return;
  document.getElementById('dw-title').textContent=task.title;
  document.getElementById('dw-sub').textContent='Task #'+task.id;
  document.getElementById('dw-teacher').textContent=task.teacher_subject?.teacher?.user?.name||'--';
  document.getElementById('dw-subject').textContent=task.teacher_subject?.subject?.name||'--';
  document.getElementById('dw-due').textContent=formatDate(task.due_date);
  document.getElementById('dw-grade').textContent=task.max_grade+' pts';
  document.getElementById('dw-desc').textContent=task.description||'No description.';
  document.getElementById('dw-list').innerHTML='<div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px">Loading...</div>';
  document.getElementById('dw-count').textContent='...';
  drawerBackdrop.style.display='block';
  drawerPanel.style.display='flex';
  document.body.style.overflow='hidden';
  requestAnimationFrame(()=>{ drawerPanel.style.transform='translateX(0)'; });
  const {ok,data}=await apiFetch('/admin/tasks/'+taskId+'/submissions');
  if(!ok){ document.getElementById('dw-list').innerHTML='<div style="padding:40px;text-align:center;color:#ef4444;font-size:13px">Failed to load.</div>'; return; }
  document.getElementById('dw-count').textContent=data.length+' student'+(data.length!==1?'s':'');
  if(!data.length){ document.getElementById('dw-list').innerHTML='<div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px">No students have taken this task yet.</div>'; return; }
  const sc={taken:'background:#f1f5f9;color:#64748b',submitted:'background:#eff6ff;color:#2563eb',correcting:'background:#fffbeb;color:#d97706',graded:'background:#ecfdf5;color:#059669'};
  document.getElementById('dw-list').innerHTML=data.map(s=>{
    const gradeHtml=s.grade!==null?`<span style="font-family:monospace;font-weight:700;color:#059669">${s.grade}/${task.max_grade}</span>`:'<span style="font-size:12px;color:#94a3b8">Not graded</span>';
    const feedbackHtml=s.feedback?`<p style="margin:4px 0 0 42px;font-size:11px;color:#94a3b8;font-style:italic">"${s.feedback}"</p>`:'';
    const ptsBtn=`<button onclick="openPointModalFromDrawer(${s.student_id},${task.teacher_subject_id},${s.id})" style="background:#f0fdf9;color:#0d9488;padding:3px 7px;border-radius:5px;font-size:10px;font-weight:600;border:1px solid #99f6e4;cursor:pointer;font-family:Syne,sans-serif">+Pts</button>`;
    return `<div style="padding:11px 18px;border-bottom:1px solid #f0fdf9">
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:9px">
          <div style="width:30px;height:30px;border-radius:50%;background:#f0fdf9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#0d9488;flex-shrink:0">${getInitials(s.student_name)}</div>
          <div><p style="font-size:13px;font-weight:600;color:#1e293b">${s.student_name}</p><p style="font-size:11px;color:#94a3b8">${s.class||'--'}</p></div>
        </div>
        <div style="display:flex;align-items:center;gap:5px">${gradeHtml}<span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:5px;${sc[s.status]||sc.taken}">${s.status}</span>${ptsBtn}</div>
      </div>${feedbackHtml}
    </div>`;
  }).join('');
}

function closeDrawer() {
  drawerPanel.style.transform='translateX(100%)';
  drawerBackdrop.style.display='none';
  document.body.style.overflow='';
  drawerTaskId=null;
  setTimeout(()=>{ drawerPanel.style.display='none'; },300);
}

function openEditTaskFromDrawer() {
  if(!drawerTaskId) return;
  const id=drawerTaskId; closeDrawer();
  setTimeout(()=>openEditTask(id),310);
}

// Init
renderActivityFeed();
loadDashClasses();
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