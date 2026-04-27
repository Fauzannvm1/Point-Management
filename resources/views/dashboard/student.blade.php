<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student - PointMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --sidebar-bg:   #0c1a2e;
      --accent:       #2563eb;
      --accent-ghost: rgba(37,99,235,0.12);
      --sidebar-w:    220px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; }

    body {
      font-family: 'Syne', sans-serif;
      background: #f0f4ff;
      color: #1e2130;
    }

    .mono { font-family: 'JetBrains Mono', monospace; }

    /* Layout */
    .shell {
      display: flex;
      height: 100vh;
      overflow: hidden;
    }

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
      z-index: 40;
      transition: transform 0.25s ease;
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
      border-bottom: 1px solid #dbeafe;
      padding: 0 20px;
      height: 54px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .scroll {
      flex: 1;
      overflow-y: auto;
      padding: 18px 20px;
    }

    /* Mobile overlay */
    .overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 39;
      backdrop-filter: blur(2px);
    }
    .overlay.on { display: block; }

    /* Sidebar nav */
    .nav-item {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 8px 10px;
      border-radius: 9px;
      font-size: 13px;
      font-weight: 500;
      color: #7c8099;
      cursor: pointer;
      transition: all 0.15s;
      text-decoration: none;
    }
    .nav-item:hover { background: rgba(255,255,255,0.06); color: #e2e4f0; }
    .nav-item.on    { background: var(--accent-ghost); color: #93c5fd; border: 1px solid rgba(37,99,235,0.25); }
    .nav-item svg   { width: 15px; height: 15px; flex-shrink: 0; }

    .nav-section {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-weight: 700;
      color: #1e3a5f;
      padding: 10px 10px 4px;
    }

    /* Cards */
    .card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #dbeafe;
      box-shadow: 0 1px 4px rgba(37,99,235,0.06);
    }

    .badge {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      padding: 2px 6px;
      border-radius: 5px;
      font-weight: 600;
    }

    .stat-number {
      font-family: 'JetBrains Mono', monospace;
      font-size: 26px;
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

    /* Buttons */
    .btn-primary {
      background: #2563eb;
      color: #fff;
      padding: 8px 14px;
      border-radius: 9px;
      font-size: 12.5px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,99,235,0.35); }

    .btn-secondary {
      background: #eff6ff;
      color: #1d4ed8;
      padding: 8px 12px;
      border-radius: 9px;
      font-size: 12.5px;
      font-weight: 600;
      border: 1px solid #bfdbfe;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-secondary:hover { background: #dbeafe; }

    .btn-edit {
      background: #eff6ff;
      color: #2563eb;
      padding: 5px 9px;
      border-radius: 7px;
      font-size: 11px;
      font-weight: 600;
      border: 1px solid #bfdbfe;
      cursor: pointer;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-edit:hover { background: #2563eb; color: #fff; }

    .btn-danger {
      background: #fef2f2;
      color: #ef4444;
      padding: 5px 9px;
      border-radius: 7px;
      font-size: 11px;
      font-weight: 600;
      border: 1px solid #fee2e2;
      cursor: pointer;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-danger:hover { background: #ef4444; color: #fff; }

    /* Form inputs */
    input, select, textarea {
      font-family: 'Syne', sans-serif;
      outline: none;
      transition: border 0.15s, box-shadow 0.15s;
    }
    input:focus, select:focus, textarea:focus {
      border-color: #2563eb !important;
      box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
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

    /* Tables */
    .table-head th {
      text-align: left;
      padding: 10px 13px;
      font-size: 10px;
      font-weight: 700;
      color: #1d4ed8;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      background: rgba(239,246,255,0.7);
      border-bottom: 1px solid #dbeafe;
    }
    .table-row td { padding: 10px 13px; border-bottom: 1px solid #eff6ff; font-size: 13px; }
    .table-row:hover td { background: #eff6ff; }
    .table-row:last-child td { border-bottom: none; }

    /* Modals */
    .modal {
      position: fixed;
      inset: 0;
      background: rgba(12,26,46,0.6);
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
      box-shadow: 0 20px 60px rgba(37,99,235,0.15);
      overflow-y: auto;
      max-height: 90vh;
    }
    .modal.on .modal-body { transform: translateY(0); }

    /* Pages */
    .page { display: none; }
    .page.on { display: block; animation: fadeUp 0.18s ease; }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(6px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Status tags */
    .s-taken      { background: #f1f5f9; color: #64748b; }
    .s-submitted  { background: #eff6ff; color: #2563eb; }
    .s-correcting { background: #fffbeb; color: #d97706; }
    .s-graded     { background: #ecfdf5; color: #059669; }

    /* File upload zone */
    .upload-zone {
      border: 2px dashed #bfdbfe;
      border-radius: 12px;
      padding: 18px;
      text-align: center;
      cursor: pointer;
      transition: all 0.15s;
      background: #eff6ff;
    }
    .upload-zone:hover,
    .upload-zone.drag-over { border-color: #2563eb; background: #dbeafe; }

    /* Grids */
    .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }
    .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #bfdbfe; border-radius: 4px; }

    .notif-dot {
      width: 6px; height: 6px;
      background: #ef4444;
      border-radius: 50%;
      position: absolute;
      top: 5px; right: 5px;
    }

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
    .hamburger:hover { background: #eff6ff; }

    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
      gap: 10px;
      flex-wrap: wrap;
    }

    .empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; }
    .empty-state p { font-size: 13px; font-weight: 500; }

    /* Class card styles */
    .class-card {
      background: #fff;
      border: 1px solid #dbeafe;
      border-radius: 14px;
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      transition: all 0.15s;
      box-shadow: 0 1px 4px rgba(37,99,235,0.06);
      position: relative;
      overflow: hidden;
    }
    .class-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, #2563eb, #7c3aed);
      border-radius: 14px 14px 0 0;
    }
    .class-card:hover {
      border-color: #93c5fd;
      box-shadow: 0 4px 14px rgba(37,99,235,0.12);
      transform: translateY(-1px);
    }

    /* Class grid */
    .class-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 12px;
    }

    /* Join code input */
    .join-code-input {
      font-family: 'JetBrains Mono', monospace;
      font-size: 18px;
      font-weight: 600;
      letter-spacing: 0.15em;
      text-align: center;
      text-transform: uppercase;
      padding: 12px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      width: 100%;
      background: #f9fafb;
      color: #1e293b;
      transition: all 0.15s;
    }
    .join-code-input:focus {
      border-color: #2563eb !important;
      box-shadow: 0 0 0 4px rgba(37,99,235,0.1) !important;
      background: #fff;
    }

    /* Step indicator */
    .step-dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: #e2e8f0;
      transition: background 0.2s;
    }
    .step-dot.active { background: #2563eb; }

    /* Mobile bottom nav */
    .mobile-nav {
      display: none;
      position: fixed;
      bottom: 0; left: 0; right: 0;
      background: var(--sidebar-bg);
      border-top: 1px solid rgba(255,255,255,0.08);
      z-index: 50;
      padding: 4px 0 env(safe-area-inset-bottom, 4px);
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
    .mobile-nav-item svg { width: 18px; height: 18px; }
    .mobile-nav-item.on { color: #93c5fd; }

    /* Responsive */
    @media (max-width: 1024px) {
      .sidebar { padding-bottom: 70px; }
      .sidebar { transform: translateX(-100%); }
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
      .filter-row  { flex-direction: column !important; gap: 8px !important; }
      .filter-row select { width: 100% !important; }
      .class-grid { grid-template-columns: 1fr !important; }
    }
  </style>
</head>
<body>

<div class="overlay" id="ov" onclick="closeSidebar()"></div>


<!-- Modal: Submit Task -->
<div class="modal" id="mSubmit">
  <div class="modal-body" style="max-width:520px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h3 style="font-size:15px;font-weight:700;color:#1e293b" id="mSubmitTitle">Submit Task</h3>
      <button onclick="closeModal('mSubmit')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="background:#eff6ff;border-radius:11px;padding:12px 14px;margin-bottom:14px;border:1px solid #dbeafe">
      <p style="font-size:13px;font-weight:700;color:#1e293b" id="mSubmitTaskTitle">--</p>
      <p style="font-size:11px;color:#64748b;margin-top:3px" id="mSubmitTaskMeta">--</p>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Notes (optional)</label>
        <textarea id="subNotes" rows="3" class="form-input" style="resize:none" placeholder="Any notes for your teacher..."></textarea>
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Attachments</label>
        <div class="upload-zone" id="subDZ"
             onclick="document.getElementById('subFI').click()"
             ondragover="onDragOver(event,'subDZ')"
             ondragleave="onDragLeave(event,'subDZ')"
             ondrop="onDrop(event,'subDZ','subFI')">
          <svg style="width:22px;height:22px;color:#2563eb;display:block;margin:0 auto 6px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <p style="font-size:12px;font-weight:600;color:#2563eb">Add files or images</p>
        </div>
        <input type="file" id="subFI" multiple accept="image/*,.pdf,.doc,.docx" style="display:none" onchange="previewSubmitFiles(this)">
        <div id="subFP" style="display:none;grid-template-columns:repeat(auto-fill,minmax(64px,1fr));gap:7px;margin-top:8px"></div>
      </div>
    </div>
    <input type="hidden" id="subTaskId">
    <div style="display:flex;gap:10px;margin-top:16px">
      <button onclick="closeModal('mSubmit')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="submitTask()" class="btn-primary" style="flex:1;justify-content:center">Submit</button>
    </div>
  </div>
</div>


<!-- Modal: Task Detail -->
<div class="modal" id="mDetail">
  <div class="modal-body" style="max-width:580px">
    <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:14px">
      <div>
        <h3 style="font-size:15px;font-weight:700;color:#1e293b" id="dtTitle">Task Detail</h3>
        <p style="font-size:11px;color:#94a3b8;margin-top:2px" id="dtMeta">--</p>
      </div>
      <button onclick="closeModal('mDetail')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1;flex-shrink:0">×</button>
    </div>
    <div id="dtImgs" style="display:none;margin-bottom:12px"></div>
    <div style="background:#f8fafc;border-radius:11px;padding:13px;margin-bottom:12px">
      <p style="font-size:13px;color:#475569;line-height:1.6" id="dtDesc">--</p>
    </div>
    <div class="grid-3" style="margin-bottom:12px">
      <div style="background:#eff6ff;border-radius:10px;padding:10px 12px;border:1px solid #dbeafe">
        <p style="font-size:9px;font-weight:600;color:#2563eb;text-transform:uppercase;margin-bottom:2px">Subject</p>
        <p style="font-size:13px;font-weight:700;color:#1e293b" id="dtSubj">--</p>
      </div>
      <div style="background:#fffbeb;border-radius:10px;padding:10px 12px;border:1px solid #fde68a">
        <p style="font-size:9px;font-weight:600;color:#d97706;text-transform:uppercase;margin-bottom:2px">Due Date</p>
        <p style="font-size:13px;font-weight:700;color:#1e293b" id="dtDue">--</p>
      </div>
      <div style="background:#f5f3ff;border-radius:10px;padding:10px 12px;border:1px solid #e9d5ff">
        <p style="font-size:9px;font-weight:600;color:#7c3aed;text-transform:uppercase;margin-bottom:2px">Max Grade</p>
        <p style="font-size:13px;font-weight:700;color:#1e293b" id="dtGrade">--</p>
      </div>
    </div>
    <div id="dtMySubmission" style="display:none;margin-bottom:12px">
      <p style="font-size:10px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:7px">My Submission</p>
      <div id="dtMySubmissionContent"></div>
    </div>
    <div style="display:flex;gap:10px">
      <button onclick="closeModal('mDetail')" class="btn-secondary" style="flex:1;justify-content:center">Close</button>
      <button id="dtSubmitBtn" onclick="openSubmitFromDetail()" class="btn-primary" style="flex:1;justify-content:center">Submit Task</button>
    </div>
  </div>
</div>


<!-- Modal: Join / Add Class -->
<div class="modal" id="mJoinClass">
  <div class="modal-body" style="max-width:460px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
      <div>
        <h3 style="font-size:15px;font-weight:700;color:#1e293b">Join a Class</h3>
        <p style="font-size:11px;color:#94a3b8;margin-top:2px">Enter the class code given by your teacher</p>
      </div>
      <button onclick="closeModal('mJoinClass')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>

    <!-- Icon -->
    <div style="text-align:center;margin-bottom:18px">
      <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px">
        <svg style="width:24px;height:24px;color:#2563eb" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
      </div>
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:8px;text-align:center">Class Code</label>
      <input type="text" id="joinCodeInput" class="join-code-input" maxlength="10" placeholder="e.g. MTH-2024"
             oninput="this.value=this.value.toUpperCase()" onkeydown="if(event.key==='Enter')joinClass()">
    </div>

    <!-- Error / success message -->
    <div id="joinMsg" style="display:none;padding:9px 12px;border-radius:9px;font-size:12px;font-weight:600;margin-bottom:14px;text-align:center"></div>

    <!-- Preview of class found -->
    <div id="joinPreview" style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:11px;padding:13px;margin-bottom:14px">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:36px;height:36px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg style="width:16px;height:16px;color:#059669" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
          <p style="font-size:13px;font-weight:700;color:#1e293b" id="jpClassName">--</p>
          <p style="font-size:11px;color:#64748b" id="jpClassMeta">--</p>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button onclick="closeModal('mJoinClass')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="joinClass()" id="joinBtn" class="btn-primary" style="flex:1;justify-content:center">
        <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Join Class
      </button>
    </div>
  </div>
</div>


<!-- Modal: Leave Class Confirm -->
<div class="modal" id="mLeaveClass">
  <div class="modal-body" style="max-width:380px">
    <div style="text-align:center;padding:8px 0 16px">
      <div style="width:48px;height:48px;border-radius:14px;background:#fef2f2;border:1px solid #fee2e2;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
        <svg style="width:22px;height:22px;color:#ef4444" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </div>
      <h3 style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:6px">Leave Class?</h3>
      <p style="font-size:13px;color:#64748b;line-height:1.5">You are about to leave <strong id="leaveClassName" style="color:#1e293b">--</strong>. You'll lose access to all tasks in this class.</p>
    </div>
    <input type="hidden" id="leaveClassId">
    <div style="display:flex;gap:10px;margin-top:4px">
      <button onclick="closeModal('mLeaveClass')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="leaveClass()" style="flex:1;justify-content:center;background:#ef4444;color:#fff;padding:9px 14px;border-radius:9px;font-size:12.5px;font-weight:600;border:none;cursor:pointer;font-family:'Syne',sans-serif">Leave</button>
    </div>
  </div>
</div>


<!-- App shell -->
<div class="shell">

  <aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div style="padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.08)">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:26px;height:26px;border-radius:8px;background:#2563eb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
          </svg>
        </div>
        <div>
          <p style="color:#fff;font-size:13px;font-weight:700">PointMS</p>
          <p style="font-size:10px;color:#3a5070">School Point System</p>
        </div>
      </div>
    </div>

    <!-- Logged-in user -->
    <div style="padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08)">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:26px;height:26px;border-radius:50%;background:rgba(37,99,235,.2);border:1px solid rgba(37,99,235,.3);display:flex;align-items:center;justify-content:center;color:#93c5fd;font-weight:700;font-size:10px;flex-shrink:0">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div style="min-width:0">
          <p style="color:#fff;font-size:11px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->name }}</p>
          <span class="badge" style="background:rgba(37,99,235,.2);color:#93c5fd">student</span>
        </div>
      </div>
    </div>

    <!-- Nav -->
    <nav style="flex:1;padding:8px;overflow-y:auto">
      <p class="nav-section">Main</p>

      <a class="nav-item on" id="n-dash" onclick="goToPage('dash',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        Dashboard
      </a>

      <a class="nav-item" id="n-classes" onclick="goToPage('classes',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        My Classes
      </a>

      <a class="nav-item" id="n-tasks" onclick="goToPage('tasks',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        My Tasks
      </a>

      <a class="nav-item" id="n-subs" onclick="goToPage('subs',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        My Submissions
      </a>

      <p class="nav-section">Analytics</p>

      <a class="nav-item" id="n-pts" onclick="goToPage('pts',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        My Points
      </a>

      <a class="nav-item" id="n-rep" onclick="goToPage('rep',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        My Report
      </a>
    </nav>

    <!-- Logout -->
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
          <h1 style="font-size:14px;font-weight:700;color:#1e293b" id="pageTitle">Dashboard</h1>
          <p style="font-size:11px;color:#94a3b8">{{ now()->format('l, d M Y') }}</p>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <button style="position:relative;padding:6px;border-radius:8px;background:transparent;border:none;cursor:pointer;color:#64748b">
          <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span class="notif-dot"></span>
        </button>
        <div style="width:28px;height:28px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
      </div>
    </header>


    <div class="scroll" id="scr">

      <!-- Page: Dashboard -->
      <div id="p-dash" class="page on">
        <div class="grid-4" style="margin-bottom:14px">

          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#2563eb" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
              <span class="badge" style="background:#eff6ff;color:#2563eb">Active</span>
            </div>
            <p class="stat-number" style="color:#1e293b">{{ $totalTasks }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">Total Tasks</p>
          </div>

          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <span class="badge" style="background:#fffbeb;color:#d97706">Pending</span>
            </div>
            <p class="stat-number" style="color:#1e293b">{{ $pendingTasks }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">Not Submitted</p>
          </div>

          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#ecfdf5;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#059669" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <span class="badge" style="background:#ecfdf5;color:#059669">Done</span>
            </div>
            <p class="stat-number" style="color:#1e293b">{{ $submittedTasks }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">Submitted</p>
          </div>

          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#f5f3ff;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#7c3aed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
              <span class="badge" style="background:#f5f3ff;color:#7c3aed">Total</span>
            </div>
            <p class="stat-number" style="color:#1e293b">{{ $totalPoints }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">My Points</p>
          </div>

        </div>

        <div class="dash-cols" style="display:grid;grid-template-columns:1fr 280px;gap:14px">
          <div class="card" style="overflow:hidden">
            <div style="padding:12px 16px;border-bottom:1px solid #dbeafe;display:flex;align-items:center;justify-content:space-between">
              <h2 style="font-size:13px;font-weight:600;color:#1e293b">Recent Tasks</h2>
              <a onclick="goToPage('tasks',document.getElementById('n-tasks'))" style="font-size:12px;color:#2563eb;cursor:pointer;font-weight:600">View all →</a>
            </div>
            <div>
              @forelse($recentTasks as $t)
              <div style="padding:10px 16px;border-bottom:1px solid #eff6ff;display:flex;align-items:center;justify-content:space-between">
                <div>
                  <p style="font-size:13px;font-weight:600;color:#1e293b">{{ $t['title'] }}</p>
                  <p style="font-size:11px;color:#94a3b8">{{ $t['subject'] }} · Due {{ $t['due'] }}</p>
                </div>
                <span class="tag s-{{ $t['status'] }}">{{ $t['status'] }}</span>
              </div>
              @empty
              <div class="empty-state"><p>No tasks yet</p></div>
              @endforelse
            </div>
          </div>

          <div class="card" style="overflow:hidden">
            <div style="padding:12px 14px;border-bottom:1px solid #dbeafe">
              <h2 style="font-size:13px;font-weight:600;color:#1e293b">Quick Actions</h2>
            </div>
            <div style="padding:10px;display:flex;flex-direction:column;gap:7px">
              <button onclick="openJoinClassModal()" class="btn-primary" style="width:100%;justify-content:center">
                <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Join a Class
              </button>
              <button onclick="goToPage('tasks',document.getElementById('n-tasks'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#eff6ff;color:#2563eb;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">View My Tasks</button>
              <button onclick="goToPage('subs',document.getElementById('n-subs'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f59e0b;color:#fff;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">My Submissions</button>
              <button onclick="goToPage('pts',document.getElementById('n-pts'))"   style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#eff6ff;color:#2563eb;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">My Points</button>
              <button onclick="goToPage('rep',document.getElementById('n-rep'))"   style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#eff6ff;color:#2563eb;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">My Report</button>
            </div>
          </div>
        </div>
      </div>


      <!--   Page: My Classes   -->
      <div id="p-classes" class="page">
        <div class="page-header">
          <div>
            <h2 style="font-size:15px;font-weight:700;color:#1e293b">My Classes</h2>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Classes you're currently enrolled in</p>
          </div>
          <button onclick="openJoinClassModal()" class="btn-primary">
            <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Join Class
          </button>
        </div>

        <!-- Stats row -->
        <div class="grid-3" style="margin-bottom:14px">
          <div class="card" style="padding:13px;display:flex;align-items:center;gap:11px">
            <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <svg style="width:16px;height:16px;color:#2563eb" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
              <p class="stat-number" style="font-size:20px;color:#1e293b" id="clsTotal">--</p>
              <p style="font-size:11px;color:#64748b">Enrolled Classes</p>
            </div>
          </div>
          <div class="card" style="padding:13px;display:flex;align-items:center;gap:11px">
            <div style="width:36px;height:36px;border-radius:10px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <svg style="width:16px;height:16px;color:#059669" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
              <p class="stat-number" style="font-size:20px;color:#1e293b" id="clsTeachers">--</p>
              <p style="font-size:11px;color:#64748b">Teachers</p>
            </div>
          </div>
          <div class="card" style="padding:13px;display:flex;align-items:center;gap:11px">
            <div style="width:36px;height:36px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <svg style="width:16px;height:16px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
              <p class="stat-number" style="font-size:20px;color:#1e293b" id="clsActiveTasks">--</p>
              <p style="font-size:11px;color:#64748b">Active Tasks</p>
            </div>
          </div>
        </div>

        <!-- Classes grid -->
        <div id="classGrid" class="class-grid"></div>
        <div id="classEmpty" class="empty-state card" style="display:none">
          <div style="width:52px;height:52px;border-radius:16px;background:#eff6ff;border:1px solid #bfdbfe;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
            <svg style="width:22px;height:22px;color:#2563eb" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          </div>
          <p style="font-weight:700;color:#1e293b;font-size:14px;margin-bottom:4px">No classes yet</p>
          <p style="color:#94a3b8;font-size:12px;margin-bottom:14px">Ask your teacher for a class code to get started.</p>
          <button onclick="openJoinClassModal()" class="btn-primary" style="margin:0 auto">
            <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Join Your First Class
          </button>
        </div>
      </div>


      <!-- Page: My Tasks -->
      <div id="p-tasks" class="page">
        <div class="page-header">
          <div>
            <h2 style="font-size:15px;font-weight:700;color:#1e293b">My Tasks</h2>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Tasks assigned to your class</p>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap" class="filter-row">
            <select id="tfSubj" onchange="loadTasks()" class="form-input" style="width:auto;min-width:110px;padding:7px 10px;font-size:12px">
              <option value="">All Subjects</option>
            </select>
            <select id="tfStatus" onchange="loadTasks()" class="form-input" style="width:auto;min-width:100px;padding:7px 10px;font-size:12px">
              <option value="">All Status</option>
              <option value="taken">Not Submitted</option>
              <option value="submitted">Submitted</option>
              <option value="graded">Graded</option>
            </select>
          </div>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr class="table-head">
                  <th>Task</th>
                  <th class="hide-mobile">Subject</th>
                  <th class="hide-mobile">Teacher</th>
                  <th>Due</th>
                  <th>Status</th>
                  <th style="text-align:right">Actions</th>
                </tr>
              </thead>
              <tbody id="tBody"></tbody>
            </table>
            <div id="tEmpty" class="empty-state" style="display:none"><p>No tasks assigned yet</p></div>
          </div>
        </div>
      </div>


      <!-- Page: My Submissions -->
      <div id="p-subs" class="page">
        <div class="page-header">
          <div>
            <h2 style="font-size:15px;font-weight:700;color:#1e293b">My Submissions</h2>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Work you've submitted</p>
          </div>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr class="table-head">
                  <th>Task</th>
                  <th class="hide-mobile">Subject</th>
                  <th>Status</th>
                  <th class="hide-mobile">Grade</th>
                  <th class="hide-mobile">Feedback</th>
                  <th class="hide-mobile">Submitted</th>
                </tr>
              </thead>
              <tbody id="subBody"></tbody>
            </table>
            <div id="subEmpty" class="empty-state" style="display:none"><p>No submissions yet</p></div>
          </div>
        </div>
      </div>


      <!-- Page: My Points -->
      <div id="p-pts" class="page">
        <div class="page-header">
          <div>
            <h2 style="font-size:15px;font-weight:700;color:#1e293b">My Points</h2>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Points awarded and deducted</p>
          </div>
        </div>
        <div class="grid-3" style="margin-bottom:14px">
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#2563eb" id="ptTotal">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Total Points</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#059669" id="ptAwarded">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Awarded</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#ef4444" id="ptDeducted">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Deducted</p></div>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr class="table-head">
                  <th>#</th>
                  <th>Subject</th>
                  <th class="hide-mobile">Given By</th>
                  <th class="hide-mobile">Description</th>
                  <th>Points</th>
                  <th class="hide-mobile">Date</th>
                </tr>
              </thead>
              <tbody id="ptBody"></tbody>
            </table>
            <div id="ptEmpty" class="empty-state" style="display:none"><p>No points yet</p></div>
          </div>
        </div>
      </div>


      <!-- Page: My Report -->
      <div id="p-rep" class="page">
        <div class="page-header">
          <div>
            <h2 style="font-size:15px;font-weight:700;color:#1e293b">My Report</h2>
            <p style="font-size:12px;color:#64748b;margin-top:2px">Your performance overview</p>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap" class="filter-row">
            <select id="repPer" onchange="loadReport()" class="form-input" style="width:auto;min-width:100px;padding:7px 10px;font-size:12px">
              <option value="month" selected>This Month</option>
              <option value="all">All Time</option>
            </select>
          </div>
        </div>

        <div class="grid-4" style="margin-bottom:14px">
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#2563eb" id="rTasks">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Tasks</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#059669" id="rSubmitted">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Submitted</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#7c3aed" id="rAvg">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Avg Grade</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#d97706" id="rPoints">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Points</p></div>
        </div>

        <div class="card" style="overflow:hidden;margin-bottom:14px">
          <div style="padding:12px 16px;border-bottom:1px solid #dbeafe">
            <h3 style="font-size:13px;font-weight:600;color:#1e293b">Performance by Subject</h3>
          </div>
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr class="table-head">
                  <th>Subject</th>
                  <th class="hide-mobile">Teacher</th>
                  <th>Tasks</th>
                  <th>Submitted</th>
                  <th>Avg Grade</th>
                  <th>Completion</th>
                </tr>
              </thead>
              <tbody id="repBody"></tbody>
            </table>
            <div id="repEmpty" class="empty-state" style="display:none"><p>No data yet</p></div>
          </div>
        </div>

        <div class="card" style="overflow:hidden">
          <div style="padding:12px 16px;border-bottom:1px solid #dbeafe">
            <h3 style="font-size:13px;font-weight:600;color:#1e293b">Graded Work</h3>
          </div>
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr class="table-head">
                  <th>Task</th>
                  <th class="hide-mobile">Subject</th>
                  <th>Grade</th>
                  <th class="hide-mobile">Feedback</th>
                  <th class="hide-mobile">Date</th>
                </tr>
              </thead>
              <tbody id="repGradedBody"></tbody>
            </table>
            <div id="repGradedEmpty" class="empty-state" style="display:none"><p>No graded work yet</p></div>
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
  <button class="mobile-nav-item" onclick="mobileGoToPage('classes',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Classes
  </button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('tasks',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Tasks
  </button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('pts',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
    Points
  </button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('rep',this)">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Report
  </button>
</nav>


<script>
const CSRF = '{{ csrf_token() }}';

function formatDate(d)      { return d ? new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '--'; }
function formatDateShort(d) { return d ? new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric' }) : '--'; }

async function apiFetch(url, opts = {}) {
  const res  = await fetch(url, { ...opts, headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF, ...(opts.headers||{}) } });
  const text = await res.text();
  try { return { ok: res.ok, data: JSON.parse(text) }; } catch { return { ok: false, data: {} }; }
}

// Sidebar
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('ov').classList.toggle('on'); }
function closeSidebar()  { document.getElementById('sidebar').classList.remove('open'); document.getElementById('ov').classList.remove('on'); }

// Page navigation
const PAGE_TITLES = { dash:'Dashboard', classes:'My Classes', tasks:'My Tasks', subs:'My Submissions', pts:'My Points', rep:'My Report' };

function goToPage(name, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('on'));
  document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('on'));
  document.getElementById('p-' + name)?.classList.add('on');
  if (el) el.classList.add('on');
  document.getElementById('pageTitle').textContent = PAGE_TITLES[name] || name;
  document.getElementById('scr').scrollTop = 0;
  if (name === 'classes') loadClasses();
  if (name === 'tasks')   loadTasks();
  if (name === 'subs')    loadSubmissions();
  if (name === 'pts')     loadPoints();
  if (name === 'rep')     loadReport();
  closeSidebar();
}

function mobileGoToPage(name, btn) {
  document.querySelectorAll('.mobile-nav-item').forEach(b => b.classList.remove('on'));
  btn.classList.add('on');
  goToPage(name, document.getElementById('n-' + name));
}

// Modals
function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal').forEach(m => m.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('on'); }));


//  CLASSES 

// Palette for class cards (cycles through)
const CLASS_PALETTES = [
  { from:'#2563eb', to:'#7c3aed' },
  { from:'#059669', to:'#0891b2' },
  { from:'#d97706', to:'#dc2626' },
  { from:'#7c3aed', to:'#db2777' },
  { from:'#0891b2', to:'#059669' },
];

async function loadClasses() {
  const grid  = document.getElementById('classGrid');
  const empty = document.getElementById('classEmpty');
  grid.innerHTML = '<div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading classes…</div>';

  const { ok, data } = await apiFetch('/student/classes');
  if (!ok) { grid.innerHTML = ''; empty.style.display = 'flex'; empty.style.flexDirection = 'column'; empty.style.alignItems = 'center'; return; }

  const classes = data.classes || data || [];

  // Update stats
  const teachers   = new Set(classes.map(c => c.teacher_name || c.teacher)).size;
  const activeTasks = classes.reduce((s, c) => s + (c.active_tasks || 0), 0);
  document.getElementById('clsTotal').textContent       = classes.length;
  document.getElementById('clsTeachers').textContent    = teachers;
  document.getElementById('clsActiveTasks').textContent = activeTasks;

  if (!classes.length) {
    grid.innerHTML = '';
    empty.style.display = 'flex';
    empty.style.flexDirection = 'column';
    empty.style.alignItems = 'center';
    return;
  }
  empty.style.display = 'none';

  grid.innerHTML = classes.map((cls, i) => {
    const pal       = CLASS_PALETTES[i % CLASS_PALETTES.length];
    const initial   = (cls.name || '?')[0].toUpperCase();
    const teacher   = cls.teacher_name || cls.teacher || '--';
    const subject   = cls.subject_name || cls.subject || '--';
    const students  = cls.student_count != null ? cls.student_count : '--';
    const tasks     = cls.total_tasks    != null ? cls.total_tasks : '--';
    const active    = cls.active_tasks   != null ? cls.active_tasks : '--';
    const joinedAt  = cls.joined_at ? formatDateShort(cls.joined_at) : '--';
    const code      = cls.code || '--';

    return `
      <div class="class-card" style="--cls-from:${pal.from};--cls-to:${pal.to}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
          <div style="display:flex;align-items:center;gap:10px;min-width:0">
            <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,${pal.from},${pal.to});display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-weight:800;font-size:16px">${initial}</div>
            <div style="min-width:0">
              <p style="font-size:14px;font-weight:700;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${cls.name || '--'}</p>
              <p style="font-size:11px;color:#64748b;margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${subject}</p>
            </div>
          </div>
          <button onclick="confirmLeaveClass(${cls.id},'${(cls.name||'').replace(/'/g,"\\'")}'" style="flex-shrink:0;background:#fef2f2;border:1px solid #fee2e2;border-radius:7px;padding:4px 7px;cursor:pointer;color:#ef4444;font-size:10px;font-weight:600;font-family:'Syne',sans-serif;transition:all 0.15s" onmouseover="this.style.background='#ef4444';this.style.color='#fff'" onmouseout="this.style.background='#fef2f2';this.style.color='#ef4444'">Leave</button>
        </div>

        <!-- Teacher -->
        <div style="display:flex;align-items:center;gap:7px;padding:8px 10px;background:#f8fafc;border-radius:9px;border:1px solid #e2e8f0">
          <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,${pal.from},${pal.to});display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:9px;flex-shrink:0">${(teacher[0]||'?').toUpperCase()}</div>
          <div>
            <p style="font-size:10px;font-weight:600;color:#2563eb;text-transform:uppercase;letter-spacing:0.04em">Teacher</p>
            <p style="font-size:12px;font-weight:600;color:#1e293b">${teacher}</p>
          </div>
        </div>

        <!-- Stats row -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px">
          <div style="background:#eff6ff;border-radius:8px;padding:7px;text-align:center;border:1px solid #dbeafe">
            <p style="font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:700;color:#2563eb">${tasks}</p>
            <p style="font-size:9px;color:#64748b;font-weight:600;margin-top:1px">Tasks</p>
          </div>
          <div style="background:#fffbeb;border-radius:8px;padding:7px;text-align:center;border:1px solid #fde68a">
            <p style="font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:700;color:#d97706">${active}</p>
            <p style="font-size:9px;color:#64748b;font-weight:600;margin-top:1px">Active</p>
          </div>
          <div style="background:#f0fdf4;border-radius:8px;padding:7px;text-align:center;border:1px solid #bbf7d0">
            <p style="font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:700;color:#059669">${students}</p>
            <p style="font-size:9px;color:#64748b;font-weight:600;margin-top:1px">Students</p>
          </div>
        </div>

        <!-- Footer -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:6px;border-top:1px solid #f1f5f9">
          <div style="display:flex;align-items:center;gap:5px">
            <span style="font-size:9px;font-weight:600;color:#94a3b8;text-transform:uppercase">Code</span>
            <span style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 6px;border-radius:5px;border:1px solid #bfdbfe">${code}</span>
          </div>
          <span style="font-size:10px;color:#94a3b8">Joined ${joinedAt}</span>
        </div>
      </div>`;
  }).join('');
}

function openJoinClassModal() {
  document.getElementById('joinCodeInput').value = '';
  document.getElementById('joinMsg').style.display     = 'none';
  document.getElementById('joinPreview').style.display = 'none';
  openModal('mJoinClass');
  setTimeout(() => document.getElementById('joinCodeInput').focus(), 220);
}

async function joinClass() {
  const code = document.getElementById('joinCodeInput').value.trim().toUpperCase();
  if (!code) { showJoinMsg('Please enter a class code.', 'error'); return; }

  const btn = document.getElementById('joinBtn');
  btn.disabled = true;
  btn.textContent = 'Joining…';

  const { ok, data } = await apiFetch('/student/classes/join', {
    method: 'POST',
    headers: { 'Content-Type':'application/json' },
    body: JSON.stringify({ code })
  });

  btn.disabled = false;
  btn.innerHTML = '<svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Join Class';

  if (ok) {
    // Show preview of joined class
    const cls = data.class || data;
    document.getElementById('jpClassName').textContent = cls.name || 'Class joined!';
    document.getElementById('jpClassMeta').textContent = `${cls.subject_name || cls.subject || ''} · ${cls.teacher_name || cls.teacher || ''}`.replace(/^·\s*|·\s*$/, '');
    document.getElementById('joinPreview').style.display = 'block';
    showJoinMsg('Successfully joined the class!', 'success');
    setTimeout(() => {
      closeModal('mJoinClass');
      // If on classes page, reload it
      if (document.getElementById('p-classes').classList.contains('on')) loadClasses();
    }, 1400);
  } else {
    showJoinMsg(data.message || 'Invalid code or class not found.', 'error');
    document.getElementById('joinPreview').style.display = 'none';
  }
}

function showJoinMsg(msg, type) {
  const el = document.getElementById('joinMsg');
  el.style.display    = 'block';
  el.textContent      = msg;
  el.style.background = type === 'success' ? '#f0fdf4' : '#fef2f2';
  el.style.color      = type === 'success' ? '#059669' : '#ef4444';
  el.style.border     = `1px solid ${type === 'success' ? '#bbf7d0' : '#fecaca'}`;
}

function confirmLeaveClass(id, name) {
  document.getElementById('leaveClassId').value        = id;
  document.getElementById('leaveClassName').textContent = name;
  openModal('mLeaveClass');
}

async function leaveClass() {
  const id = document.getElementById('leaveClassId').value;
  const { ok, data } = await apiFetch(`/student/classes/${id}/leave`, { method: 'DELETE' });
  if (ok) {
    closeModal('mLeaveClass');
    loadClasses();
  } else {
    alert(data.message || 'Could not leave class.');
  }
}


//  FILE UPLOAD 

let pendingFiles = [];

function onDragOver(e, zoneId)  { e.preventDefault(); document.getElementById(zoneId).classList.add('drag-over'); }
function onDragLeave(e, zoneId) { document.getElementById(zoneId).classList.remove('drag-over'); }

function onDrop(e, zoneId, inputId) {
  e.preventDefault();
  document.getElementById(zoneId).classList.remove('drag-over');
  const input = document.getElementById(inputId);
  const dt    = new DataTransfer();
  Array.from(input.files || []).forEach(f => dt.items.add(f));
  Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
  input.files = dt.files;
  previewSubmitFiles(input);
}

function previewSubmitFiles(input) {
  pendingFiles = Array.from(input.files);
  const grid   = document.getElementById('subFP');
  if (!pendingFiles.length) { grid.style.display = 'none'; return; }
  grid.style.display = 'grid';
  grid.innerHTML = pendingFiles.map((file, i) => {
    if (file.type.startsWith('image/')) {
      const url = URL.createObjectURL(file);
      return `<div style="position:relative"><img src="${url}" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:1px solid #dbeafe"><button onclick="removeFile(${i})" style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,.5);border:none;border-radius:50%;width:15px;height:15px;cursor:pointer;color:#fff;font-size:9px;line-height:1">✕</button></div>`;
    }
    return `<div style="background:#eff6ff;border-radius:8px;padding:6px;border:1px solid #dbeafe;font-size:9px;text-align:center;overflow:hidden">📄 ${file.name.slice(0, 10)}</div>`;
  }).join('');
}

function removeFile(i) {
  pendingFiles.splice(i, 1);
  const dt = new DataTransfer();
  pendingFiles.forEach(f => dt.items.add(f));
  document.getElementById('subFI').files = dt.files;
  previewSubmitFiles(document.getElementById('subFI'));
}


//  TASKS 

let allTasks            = [];
let currentDetailTaskId = null;

async function loadTasks() {
  const subj   = document.getElementById('tfSubj').value;
  const status = document.getElementById('tfStatus').value;
  const params = new URLSearchParams();
  if (subj)   params.append('subject_id', subj);
  if (status) params.append('status', status);

  const { ok, data } = await apiFetch(`/student/tasks?${params}`);
  if (!ok) return;

  allTasks = data.tasks || data;

  if (data.subjects) {
    const sel = document.getElementById('tfSubj');
    const cur = sel.value;
    sel.innerHTML = '<option value="">All Subjects</option>' +
      data.subjects.map(s => `<option value="${s.id}"${s.id == cur ? ' selected' : ''}>${s.name}</option>`).join('');
  }

  renderTasks();
}

function renderTasks() {
  const tbody = document.getElementById('tBody');
  const empty = document.getElementById('tEmpty');
  if (!allTasks.length) { tbody.innerHTML = ''; empty.style.display = 'block'; return; }
  empty.style.display = 'none';

  tbody.innerHTML = allTasks.map(t => {
    const expired   = t.due_date && new Date(t.due_date) < new Date();
    const canSubmit = t.my_status === 'taken';
    const actionBtn = canSubmit
      ? `<button onclick="openSubmitModal(${t.id},'${(t.title||'').replace(/'/g,"\\'")}','${t.subject||''}','${formatDateShort(t.due_date)}')" class="btn-primary" style="padding:4px 10px;font-size:11px">Submit</button>`
      : `<button onclick="viewTaskDetail(${t.id})" class="btn-edit" style="font-size:11px">View</button>`;
    return `
      <tr class="table-row">
        <td>
          <p style="font-weight:600;color:#1e293b;font-size:13px">${t.title}</p>
          <p class="hide-mobile" style="font-size:11px;color:#94a3b8;margin-top:1px">${t.description || '--'}</p>
        </td>
        <td class="hide-mobile" style="color:#64748b;font-size:13px">${t.subject || '--'}</td>
        <td class="hide-mobile" style="color:#64748b;font-size:13px">${t.teacher || '--'}</td>
        <td class="mono" style="font-size:11px;color:${expired ? '#ef4444' : '#94a3b8'}">${formatDateShort(t.due_date)}</td>
        <td><span class="tag s-${t.my_status || 'taken'}">${t.my_status || 'not submitted'}</span></td>
        <td style="text-align:right">
          <div style="display:flex;gap:4px;justify-content:flex-end">
            <button onclick="viewTaskDetail(${t.id})" class="btn-secondary" style="padding:4px 8px;font-size:11px">Detail</button>
            ${actionBtn}
          </div>
        </td>
      </tr>`;
  }).join('');
}

async function viewTaskDetail(id) {
  currentDetailTaskId = id;
  const { ok, data } = await apiFetch(`/student/tasks/${id}`);
  if (!ok) { alert('Could not load task.'); return; }

  document.getElementById('dtTitle').textContent = data.title || '--';
  document.getElementById('dtMeta').textContent  = `${data.subject || '--'} · ${data.teacher || '--'}`;
  document.getElementById('dtDesc').textContent  = data.description || 'No description.';
  document.getElementById('dtSubj').textContent  = data.subject || '--';
  document.getElementById('dtDue').textContent   = formatDate(data.due_date);
  document.getElementById('dtGrade').textContent = data.max_grade || '100';

  const imgEl = document.getElementById('dtImgs');
  if (data.images && data.images.length) {
    imgEl.style.display             = 'grid';
    imgEl.style.gridTemplateColumns = 'repeat(auto-fill,minmax(64px,1fr))';
    imgEl.style.gap                 = '7px';
    imgEl.innerHTML = data.images.map(img =>
      `<img src="${img.url}" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:1px solid #dbeafe;cursor:pointer" onclick="window.open('${img.url}','_blank')">`
    ).join('');
  } else {
    imgEl.style.display = 'none';
  }

  const mySubWrap = document.getElementById('dtMySubmission');
  if (data.my_submission) {
    mySubWrap.style.display = 'block';
    const s = data.my_submission;
    const gradeHtml = s.grade !== null
      ? `<span class="mono" style="font-weight:700;color:#059669;font-size:14px">${s.grade} / ${data.max_grade}</span>`
      : '<span style="color:#94a3b8;font-size:12px">Not graded yet</span>';
    document.getElementById('dtMySubmissionContent').innerHTML = `
      <div style="background:#f8fafc;border-radius:10px;padding:11px 13px;border:1px solid #e2e8f0">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
          <span class="tag s-${s.status}">${s.status}</span>
          ${gradeHtml}
        </div>
        ${s.feedback ? `<p style="font-size:12px;color:#64748b;font-style:italic">"${s.feedback}"</p>` : ''}
        ${s.notes    ? `<p style="font-size:12px;color:#475569;margin-top:4px">Your notes: ${s.notes}</p>` : ''}
      </div>`;
  } else {
    mySubWrap.style.display = 'none';
  }

  const canSubmit = !data.my_submission || data.my_submission.status === 'taken';
  document.getElementById('dtSubmitBtn').style.display = canSubmit ? 'flex' : 'none';
  openModal('mDetail');
}

function openSubmitFromDetail() {
  const task = allTasks.find(t => t.id === currentDetailTaskId);
  if (!task) return;
  closeModal('mDetail');
  setTimeout(() => openSubmitModal(task.id, task.title, task.subject, formatDateShort(task.due_date)), 220);
}

function openSubmitModal(taskId, title, subject, due) {
  document.getElementById('subTaskId').value              = taskId;
  document.getElementById('mSubmitTitle').textContent     = 'Submit Task';
  document.getElementById('mSubmitTaskTitle').textContent = title;
  document.getElementById('mSubmitTaskMeta').textContent  = `${subject} · Due ${due}`;
  document.getElementById('subNotes').value               = '';
  document.getElementById('subFP').style.display          = 'none';
  document.getElementById('subFP').innerHTML              = '';
  pendingFiles = [];
  document.getElementById('subFI').value = '';
  openModal('mSubmit');
}

async function submitTask() {
  const taskId = document.getElementById('subTaskId').value;
  const notes  = document.getElementById('subNotes').value.trim();
  if (!taskId) return;

  const formData = new FormData();
  formData.append('task_id', taskId);
  if (notes) formData.append('notes', notes);
  pendingFiles.forEach((f, i) => formData.append(`files[${i}]`, f));

  const { ok, data } = await apiFetch('/student/submissions', { method: 'POST', body: formData });
  if (ok) { closeModal('mSubmit'); loadTasks(); }
  else    { alert(data.message || 'Something went wrong.'); }
}


//  SUBMISSIONS 

async function loadSubmissions() {
  const { ok, data } = await apiFetch('/student/submissions');
  if (!ok) return;

  const tbody = document.getElementById('subBody');
  const empty = document.getElementById('subEmpty');
  if (!data.length) { tbody.innerHTML = ''; empty.style.display = 'block'; return; }
  empty.style.display = 'none';

  tbody.innerHTML = data.map(s => {
    const gradeHtml = s.grade !== null
      ? `<span class="mono" style="font-weight:700;color:#059669">${s.grade}/${s.max_grade}</span>`
      : '<span style="color:#94a3b8;font-size:11px">—</span>';
    return `
      <tr class="table-row">
        <td>
          <p style="font-weight:600;font-size:13px;color:#1e293b">${s.task_title || '--'}</p>
          <p class="hide-mobile" style="font-size:11px;color:#94a3b8;margin-top:1px">${s.class || '--'}</p>
        </td>
        <td class="hide-mobile" style="color:#64748b;font-size:13px">${s.subject || '--'}</td>
        <td><span class="tag s-${s.status || 'submitted'}">${s.status}</span></td>
        <td class="hide-mobile">${gradeHtml}</td>
        <td class="hide-mobile" style="font-size:12px;color:#64748b;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${s.feedback || '—'}</td>
        <td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(s.submitted_at)}</td>
      </tr>`;
  }).join('');
}


//  POINTS 

async function loadPoints() {
  const { ok: sOk, data: stats } = await apiFetch('/student/points/stats');
  if (sOk) {
    document.getElementById('ptTotal').textContent    = Number(stats.total    || 0).toLocaleString();
    document.getElementById('ptAwarded').textContent  = Number(stats.awarded  || 0).toLocaleString();
    document.getElementById('ptDeducted').textContent = Number(stats.deducted || 0).toLocaleString();
  }

  const { ok, data } = await apiFetch('/student/points');
  if (!ok) return;

  const tbody = document.getElementById('ptBody');
  const empty = document.getElementById('ptEmpty');
  if (!data.length) { tbody.innerHTML = ''; empty.style.display = 'block'; return; }
  empty.style.display = 'none';

  tbody.innerHTML = data.map((r, i) => `
    <tr class="table-row">
      <td class="mono" style="font-size:11px;color:#94a3b8">${String(i + 1).padStart(2, '0')}</td>
      <td style="font-weight:600;font-size:13px;color:#1e293b">${r.subject || '--'}</td>
      <td class="hide-mobile" style="color:#64748b;font-size:13px">${r.teacher || '--'}</td>
      <td class="hide-mobile" style="color:#94a3b8;font-size:12px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.description || '--'}</td>
      <td class="mono" style="font-weight:700;font-size:13px;color:${r.type === 'positive' ? '#059669' : '#ef4444'}">${r.points > 0 ? '+' : ''}${r.points}</td>
      <td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(r.date)}</td>
    </tr>`).join('');
}


//  REPORT 

async function loadReport() {
  const period = document.getElementById('repPer').value;
  const params = new URLSearchParams();
  params.append('period', period);

  const { ok, data } = await apiFetch(`/student/report?${params}`);
  if (!ok) return;

  document.getElementById('rTasks').textContent     = data.summary?.tasks     ?? '--';
  document.getElementById('rSubmitted').textContent = data.summary?.submitted ?? '--';
  document.getElementById('rPoints').textContent    = data.summary?.points    ?? '--';
  document.getElementById('rAvg').textContent       = data.summary?.avg_grade != null
    ? Number(data.summary.avg_grade).toFixed(1) + '%'
    : '--';

  const repTbody = document.getElementById('repBody');
  const repEmpty = document.getElementById('repEmpty');
  if (!data.breakdown || !data.breakdown.length) {
    repTbody.innerHTML = '';
    repEmpty.style.display = 'block';
  } else {
    repEmpty.style.display = 'none';
    repTbody.innerHTML = data.breakdown.map(r => {
      const pct = r.total_tasks > 0 ? Math.round((r.submitted / r.total_tasks) * 100) : 0;
      return `
        <tr class="table-row">
          <td style="font-weight:600;color:#1e293b;font-size:13px">${r.subject_name}</td>
          <td class="hide-mobile" style="color:#64748b;font-size:13px">${r.teacher_name || '--'}</td>
          <td style="color:#64748b;font-size:13px">${r.total_tasks}</td>
          <td style="color:#2563eb;font-size:13px">${r.submitted}</td>
          <td class="mono" style="font-weight:600;font-size:13px;color:${r.avg_grade >= 70 ? '#059669' : '#ef4444'}">${r.avg_grade != null ? Number(r.avg_grade).toFixed(1) : '--'}</td>
          <td>
            <div style="display:flex;align-items:center;gap:5px">
              <div style="flex:1;background:#e2e8f0;border-radius:4px;height:5px;overflow:hidden">
                <div style="width:${pct}%;background:#2563eb;height:100%;border-radius:4px"></div>
              </div>
              <span class="mono" style="font-size:10px;color:#64748b;min-width:26px">${pct}%</span>
            </div>
          </td>
        </tr>`;
    }).join('');
  }

  const gradedTbody = document.getElementById('repGradedBody');
  const gradedEmpty = document.getElementById('repGradedEmpty');
  if (!data.graded || !data.graded.length) {
    gradedTbody.innerHTML = '';
    gradedEmpty.style.display = 'block';
  } else {
    gradedEmpty.style.display = 'none';
    gradedTbody.innerHTML = data.graded.map(g => `
      <tr class="table-row">
        <td style="font-weight:600;font-size:13px;color:#1e293b">${g.task_title}</td>
        <td class="hide-mobile" style="color:#64748b;font-size:13px">${g.subject || '--'}</td>
        <td class="mono" style="font-weight:700;font-size:13px;color:${g.grade >= 70 ? '#059669' : '#ef4444'}">${g.grade} / ${g.max_grade}</td>
        <td class="hide-mobile" style="font-size:12px;color:#64748b;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${g.feedback || '—'}</td>
        <td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(g.graded_at)}</td>
      </tr>`).join('');
  }
}
</script>
</body>
</html>