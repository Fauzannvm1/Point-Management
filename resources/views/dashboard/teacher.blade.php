<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher - PointMS</title>
  <link rel="icon" type="image/png" href="{{ asset('storage_backup/Icon.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --sidebar-bg: #0a1628;
      --accent:     #0d9488;
      --accent-ghost: rgba(13,148,136,0.15);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; }

    body {
      font-family: 'Syne', sans-serif;
      background: #f0faf8;
      color: #1e2130;
    }

    .mono { font-family: 'JetBrains Mono', monospace; }

    .shell { display: flex; height: 100vh; overflow: hidden; }

    .sidebar {
      background: var(--sidebar-bg);
      width: 220px;
      min-width: 220px;
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
      margin-left: 220px;
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      min-width: 0;
      height: 100vh;
    }

    .topbar {
      background: #fff;
      border-bottom: 1px solid #d1faf4;
      padding: 0 20px;
      height: 54px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .scroll { flex: 1; overflow-y: auto; padding: 18px 20px; }

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
    .nav-item.on    { background: var(--accent-ghost); color: #5eead4; border: 1px solid rgba(13,148,136,0.3); }
    .nav-item svg   { width: 15px; height: 15px; flex-shrink: 0; }

    .nav-section {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-weight: 700;
      color: #2a4060;
      padding: 10px 10px 4px;
    }

    .card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #d1faf4;
      box-shadow: 0 1px 4px rgba(13,148,136,0.06);
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

    .btn-primary {
      background: #0d9488;
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
    .btn-primary:hover { background: #0f766e; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(13,148,136,0.35); }

    .btn-secondary {
      background: #f0fdf9;
      color: #0f766e;
      padding: 8px 12px;
      border-radius: 9px;
      font-size: 12.5px;
      font-weight: 600;
      border: 1px solid #99f6e4;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-secondary:hover { background: #ccfbf1; }

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

    .btn-edit {
      background: #f0fdf9;
      color: #0d9488;
      padding: 5px 9px;
      border-radius: 7px;
      font-size: 11px;
      font-weight: 600;
      border: 1px solid #99f6e4;
      cursor: pointer;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-edit:hover { background: #0d9488; color: #fff; }

    .btn-amber {
      background: #fffbeb;
      color: #d97706;
      padding: 5px 9px;
      border-radius: 7px;
      font-size: 11px;
      font-weight: 600;
      border: 1px solid #fde68a;
      cursor: pointer;
      transition: all 0.15s;
      font-family: 'Syne', sans-serif;
    }
    .btn-amber:hover { background: #f59e0b; color: #fff; }

    input, select, textarea {
      font-family: 'Syne', sans-serif;
      outline: none;
      transition: border 0.15s, box-shadow 0.15s;
    }
    input:focus, select:focus, textarea:focus {
      border-color: #0d9488 !important;
      box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
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
    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 4px;
    }

    .table-head th {
      text-align: left;
      padding: 10px 13px;
      font-size: 10px;
      font-weight: 700;
      color: #0f766e;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      background: rgba(240,253,249,0.6);
      border-bottom: 1px solid #d1faf4;
    }
    .table-row td { padding: 10px 13px; border-bottom: 1px solid #f0fdf9; font-size: 13px; }
    .table-row:hover td { background: #f0fdf9; }
    .table-row:last-child td { border-bottom: none; }

    .modal {
      position: fixed;
      inset: 0;
      background: rgba(10,22,40,0.6);
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
      box-shadow: 0 20px 60px rgba(13,148,136,0.15);
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

    .s-taken      { background: #f1f5f9; color: #64748b; }
    .s-submitted  { background: #eff6ff; color: #2563eb; }
    .s-correcting { background: #fffbeb; color: #d97706; }
    .s-graded     { background: #ecfdf5; color: #059669; }
    .s-active     { background: #ecfdf5; color: #059669; }

    .upload-zone {
      border: 2px dashed #99f6e4;
      border-radius: 12px;
      padding: 18px;
      text-align: center;
      cursor: pointer;
      transition: all 0.15s;
      background: #f0fdf9;
    }
    .upload-zone:hover, .upload-zone.drag-over { border-color: #0d9488; background: #ccfbf1; }

    .tab-bar { display: flex; gap: 4px; background: #f0fdf9; border-radius: 10px; padding: 4px; }
    .tab-btn {
      flex: 1; padding: 6px 12px; border-radius: 7px; border: none; cursor: pointer;
      font-size: 12px; font-weight: 600; color: #64748b; background: transparent;
      font-family: 'Syne', sans-serif; transition: all 0.15s;
    }
    .tab-btn.on { background: #fff; color: #0d9488; box-shadow: 0 1px 3px rgba(13,148,136,0.15); }

    .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }
    .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #99f6e4; border-radius: 4px; }

    .notif-dot { width:6px;height:6px;background:#ef4444;border-radius:50%;position:absolute;top:5px;right:5px; }

    .hamburger {
      display: none; padding: 6px; border-radius: 8px; background: transparent;
      border: none; cursor: pointer; color: #64748b; align-items: center; justify-content: center;
    }
    .hamburger:hover { background: #f0fdf9; }

    .page-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 14px; gap: 10px; flex-wrap: wrap;
    }

    .empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; }
    .empty-state p { font-size: 13px; font-weight: 500; }

    .class-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 14px;
    }

    .class-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #d1faf4;
      box-shadow: 0 1px 4px rgba(13,148,136,0.06);
      overflow: hidden;
      transition: box-shadow 0.15s, transform 0.15s;
    }
    .class-card:hover { box-shadow: 0 4px 18px rgba(13,148,136,0.13); transform: translateY(-2px); }

    .class-card-bar { height: 5px; background: linear-gradient(90deg, #0d9488, #14b8a6); }

    .join-code-box {
      display: flex; align-items: center; gap: 8px;
      background: #f0fdf9; border: 1px solid #d1faf4;
      border-radius: 8px; padding: 7px 10px;
    }
    .join-code-box .mono { font-size: 13px; font-weight: 700; color: #0d9488; letter-spacing: 0.06em; flex: 1; }
    .copy-btn {
      background: none; border: none; cursor: pointer; color: #64748b;
      padding: 2px; border-radius: 5px; transition: color 0.15s;
    }
    .copy-btn:hover { color: #0d9488; }

    /* ===== DASHBOARD CLASS MINI-CARDS ===== */
    .dash-cls-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
      gap: 10px;
      padding: 14px;
    }

    .dash-cls-card {
      background: #f9fffe;
      border: 1px solid #d1faf4;
      border-radius: 11px;
      padding: 13px;
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
      background: linear-gradient(90deg, #0d9488, #14b8a6);
    }
    .dash-cls-card:hover {
      border-color: #5eead4;
      box-shadow: 0 6px 20px rgba(13,148,136,0.14);
      transform: translateY(-2px);
    }
    .dash-cls-card:active { transform: translateY(0); }

    /* ===== CLASS DETAIL PANEL ===== */
    .class-panel {
      position: fixed; inset: 0; z-index: 300;
      display: flex; align-items: stretch;
      pointer-events: none; opacity: 0;
      transition: opacity 0.25s;
    }
    .class-panel.on { pointer-events: all; opacity: 1; }

    .class-panel-backdrop {
      flex: 1;
      background: rgba(10,22,40,0.5);
      backdrop-filter: blur(3px);
      cursor: pointer;
    }

    .class-panel-body {
      width: 75vw; max-width: 100vw;
      background: #fff;
      display: flex; flex-direction: column;
      overflow: hidden;
      transform: translateX(100%);
      transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
      box-shadow: -12px 0 50px rgba(13,148,136,0.12);
    }
    .class-panel.on .class-panel-body { transform: translateX(0); }

    /* Mobile nav */
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
      flex: 1; display: flex; flex-direction: column; align-items: center; gap: 2px;
      padding: 5px 2px; color: #7c8099; cursor: pointer; font-size: 9px; font-weight: 600;
      font-family: 'Syne', sans-serif; border: none; background: transparent; transition: color 0.15s;
    }
    .mobile-nav-item svg { width: 18px; height: 18px; }
    .mobile-nav-item.on { color: #5eead4; }

    @media (max-width: 1024px) {
      .sidebar { padding-bottom: 70px; transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .main { margin-left: 0; }
      .hamburger { display: flex !important; }
      .grid-4 { grid-template-columns: 1fr 1fr !important; }
      .dash-cols { grid-template-columns: 1fr !important; }
      .class-grid { grid-template-columns: 1fr 1fr !important; }
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

<!-- Modal: Create / Edit Task -->
<div class="modal" id="mTask">
  <div class="modal-body" style="max-width:580px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h3 style="font-size:15px;font-weight:700;color:#1e293b" id="mTaskTitle">New Task</h3>
      <button onclick="closeModal('mTask')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div><label class="form-label">Title <span style="color:#ef4444">*</span></label><input type="text" id="tTitle" class="form-input" placeholder="e.g. Chapter 5 Essay"></div>
      <div><label class="form-label">Description</label><textarea id="tDesc" rows="3" class="form-input" style="resize:none" placeholder="Instructions..."></textarea></div>
      <div class="grid-2">
        <div><label class="form-label">Subject / Class <span style="color:#ef4444">*</span></label><select id="tTS" class="form-input"><option value="">-- Select --</option></select></div>
        <div style="display:none"><label class="form-label">Max Grade</label><input type="number" id="tGrade" value="100" class="form-input"></div>
      </div>
      <div><label class="form-label">Due Date</label><input type="date" id="tDue" class="form-input"></div>
      <div>
        <label class="form-label">Images / Attachments</label>
        <div class="upload-zone" id="tDZ" onclick="document.getElementById('tFI').click()" ondragover="onDragOver(event,'tDZ')" ondragleave="onDragLeave(event,'tDZ')" ondrop="onDrop(event,'tDZ','tFI')">
          <svg style="width:22px;height:22px;color:#0d9488;display:block;margin:0 auto 6px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <p style="font-size:12px;font-weight:600;color:#0d9488">Add images or files</p>
        </div>
        <input type="file" id="tFI" multiple accept="image/*,.pdf,.doc,.docx" style="display:none" onchange="previewTaskFiles(this)">
        <div id="tFP" style="display:none;grid-template-columns:repeat(auto-fill,minmax(64px,1fr));gap:7px;margin-top:8px"></div>
      </div>
    </div>
    <div style="display:flex;gap:10px;margin-top:16px">
      <button onclick="closeModal('mTask')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveTask()" class="btn-primary" style="flex:1;justify-content:center" id="tSaveBtn">Create Task</button>
    </div>
  </div>
</div>

<!-- Modal: Task Detail -->
<div class="modal" id="mDetail">
  <div class="modal-body" style="max-width:620px">
    <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:14px">
      <div><h3 style="font-size:15px;font-weight:700;color:#1e293b" id="dtTitle">Task Detail</h3><p style="font-size:11px;color:#94a3b8;margin-top:2px" id="dtMeta">--</p></div>
      <button onclick="closeModal('mDetail')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1;flex-shrink:0">×</button>
    </div>
    <div id="dtImgs" style="display:none;margin-bottom:12px"></div>
    <div style="background:#f8fafc;border-radius:11px;padding:13px;margin-bottom:12px"><p style="font-size:13px;color:#475569;line-height:1.6" id="dtDesc">--</p></div>
    <div class="grid-3" style="margin-bottom:12px">
      <div style="background:#f0fdf9;border-radius:10px;padding:10px 12px;border:1px solid #d1faf4"><p style="font-size:9px;font-weight:600;color:#0d9488;text-transform:uppercase;margin-bottom:2px">Subject</p><p style="font-size:13px;font-weight:700;color:#1e293b" id="dtSubj">--</p></div>
      <div style="background:#fffbeb;border-radius:10px;padding:10px 12px;border:1px solid #fde68a"><p style="font-size:9px;font-weight:600;color:#d97706;text-transform:uppercase;margin-bottom:2px">Due Date</p><p style="font-size:13px;font-weight:700;color:#1e293b" id="dtDue">--</p></div>
      <div style="background:#f5f3ff;border-radius:10px;padding:10px 12px;border:1px solid #e9d5ff"><p style="font-size:9px;font-weight:600;color:#7c3aed;text-transform:uppercase;margin-bottom:2px">Max Grade</p><p style="font-size:13px;font-weight:700;color:#1e293b" id="dtGrade">--</p></div>
    </div>
    <div id="dtAttach" style="display:none;margin-bottom:12px"><p style="font-size:10px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:7px">Attachments</p><div id="dtAttachList"></div></div>
    <div style="display:flex;gap:10px">
      <button onclick="closeModal('mDetail')" class="btn-secondary" style="flex:1;justify-content:center">Close</button>
      <button onclick="openEditFromDetail()" class="btn-primary" style="flex:1;justify-content:center">Edit Task</button>
    </div>
  </div>
</div>

<!-- Modal: Grade Submission -->
<div class="modal" id="mGrade">
  <div class="modal-body" style="max-width:420px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h3 style="font-size:15px;font-weight:700;color:#1e293b">Grade Submission</h3>
      <button onclick="closeModal('mGrade')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div style="background:#f0fdf9;border-radius:10px;padding:10px 12px;border:1px solid #d1faf4"><p style="font-size:9px;font-weight:600;color:#0d9488;text-transform:uppercase;margin-bottom:2px">Student</p><p style="font-size:13px;font-weight:700;color:#1e293b" id="gStudent">--</p></div>
      <div id="gSubDetails" style="margin-bottom:4px"></div>
      <div>
        <label class="form-label">Set Status</label>
        <div class="tab-bar">
          <button class="tab-btn" onclick="setGradeStatus('correcting',this)">Correcting</button>
          <button class="tab-btn on" onclick="setGradeStatus('graded',this)">Graded</button>
        </div>
      </div>
      <input type="hidden" id="gStatus" value="graded">
      <div><label class="form-label">Grade (out of <span id="gMax">100</span>)</label><input type="number" id="gVal" class="form-input" placeholder="e.g. 85" min="0"></div>
      <div><label class="form-label">Feedback (optional)</label><textarea id="gFeedback" rows="3" class="form-input" style="resize:none" placeholder="e.g. Good work!"></textarea></div>
    </div>
    <input type="hidden" id="gSubID">
    <div style="display:flex;gap:10px;margin-top:16px">
      <button onclick="closeModal('mGrade')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="submitGrade()" class="btn-primary" style="flex:1;justify-content:center">Save</button>
    </div>
  </div>
</div>

<!-- Modal: Add Subject Assignment -->
<div class="modal" id="mSubj">
  <div class="modal-body" style="max-width:420px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h3 style="font-size:15px;font-weight:700;color:#1e293b">Add Subject Assignment</h3>
      <button onclick="closeModal('mSubj')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:11px">
      <div><label class="form-label">Subject <span style="color:#ef4444">*</span></label><select id="smSubj" class="form-input"><option value="">-- Select --</option></select></div>
      <div><label class="form-label">Class <span style="color:#ef4444">*</span></label><select id="smClass" class="form-input"><option value="">-- Select --</option></select></div>
    </div>
    <div style="display:flex;gap:10px;margin-top:16px">
      <button onclick="closeModal('mSubj')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveSubject()" class="btn-primary" style="flex:1;justify-content:center">Assign</button>
    </div>
  </div>
</div>

<!-- Modal: Add / Edit Class -->
<div class="modal" id="mClass">
  <div class="modal-body" style="max-width:440px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <div>
        <h3 style="font-size:15px;font-weight:700;color:#1e293b" id="mClassTitle">Add Class</h3>
        <p style="font-size:11px;color:#94a3b8;margin-top:2px" id="mClassSub">Create a new class</p>
      </div>
      <button onclick="closeModal('mClass')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:13px">
      <div>
        <label class="form-label">Class Name <span style="color:#ef4444">*</span></label>
        <input type="text" id="clName" class="form-input" placeholder="e.g. Class 10A">
      </div>
      <div>
        <label class="form-label">Join Code <span style="font-weight:400;color:#94a3b8;margin-left:4px">(auto-generated if empty)</span></label>
        <div style="display:flex;gap:8px">
          <input type="text" id="clCode" class="form-input" placeholder="E.G. MTH-2024" style="font-family:'JetBrains Mono',monospace;text-transform:uppercase;letter-spacing:0.06em">
          <button type="button" onclick="genJoinCode()" class="btn-secondary" style="white-space:nowrap;flex-shrink:0">
            <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Generate
          </button>
        </div>
      </div>
      <div>
        <label class="form-label">Description <span style="font-weight:400;color:#94a3b8">(optional)</span></label>
        <textarea id="clDesc" rows="3" class="form-input" style="resize:none" placeholder="Short description..."></textarea>
      </div>
      <div>
        <label class="form-label">Subject <span style="color:#ef4444">*</span></label>
        <select id="clSubj" class="form-input"><option value="">Loading...</option></select>
      </div>
      <div style="background:#f0fdf9;border:1px solid #d1faf4;border-radius:10px;padding:10px 12px;display:flex;gap:8px;align-items:flex-start">
        <svg style="width:14px;height:14px;color:#0d9488;flex-shrink:0;margin-top:1px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p style="font-size:11px;color:#0f766e;line-height:1.5">Students will join using the <strong>Join Code</strong>. You can rename this class anytime.</p>
      </div>
    </div>
    <input type="hidden" id="clId">
    <div style="display:flex;gap:10px;margin-top:18px">
      <button onclick="closeModal('mClass')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="saveClass()" class="btn-primary" style="flex:1;justify-content:center" id="clSaveBtn">Add Class</button>
    </div>
  </div>
</div>

<!-- Modal: Rename Class -->
<div class="modal" id="mRename">
  <div class="modal-body" style="max-width:360px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h3 style="font-size:15px;font-weight:700;color:#1e293b">Rename Class</h3>
      <button onclick="closeModal('mRename')" style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1">×</button>
    </div>
    <div><label class="form-label">New Name <span style="color:#ef4444">*</span></label><input type="text" id="renName" class="form-input" placeholder="e.g. Class 10B"></div>
    <input type="hidden" id="renId">
    <div style="display:flex;gap:10px;margin-top:16px">
      <button onclick="closeModal('mRename')" class="btn-secondary" style="flex:1;justify-content:center">Cancel</button>
      <button onclick="confirmRename()" class="btn-primary" style="flex:1;justify-content:center">Rename</button>
    </div>
  </div>
</div>

<!-- Modal: Delete Confirmation -->
<div class="modal" id="mDel">
  <div class="modal-body" style="max-width:320px;text-align:center">
    <div style="width:44px;height:44px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
      <svg style="width:22px;height:22px;color:#ef4444" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </div>
    <h3 style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:6px">Confirm Delete</h3>
    <p style="font-size:13px;color:#64748b;margin-bottom:16px" id="delMsg">Are you sure?</p>
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

    <!-- Banner -->
    <div style="background:linear-gradient(135deg,#0d9488,#14b8a6);padding:18px 20px;flex-shrink:0;position:relative">
      <button onclick="closeClassPanel()" style="position:absolute;top:12px;right:14px;width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,0.2);border:none;cursor:pointer;color:#fff;font-size:18px;line-height:1;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)">×</button>
      <div style="display:flex;align-items:center;gap:12px">
        <div id="cpAvatar" style="width:50px;height:50px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#0d9488;flex-shrink:0;box-shadow:0 4px 14px rgba(0,0,0,0.15)">A</div>
        <div>
          <h2 id="cpName" style="font-size:17px;font-weight:800;color:#fff">Class Name</h2>
          <div style="display:flex;align-items:center;gap:7px;margin-top:3px">
            <span id="cpCode" style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:rgba(255,255,255,0.9);background:rgba(255,255,255,0.18);padding:2px 8px;border-radius:5px;border:1px solid rgba(255,255,255,0.25)">CODE</span>
            <button onclick="copyCode(document.getElementById('cpCodeRaw').value)" style="background:rgba(255,255,255,0.15);border:none;cursor:pointer;color:#fff;padding:2px 6px;border-radius:5px;font-size:10px;font-weight:600;font-family:'Syne',sans-serif">Copy</button>
          </div>
        </div>
      </div>
      <input type="hidden" id="cpCodeRaw">
    </div>

    <!-- Info strip -->
    <div style="padding:12px 20px;border-bottom:1px solid #d1faf4;display:flex;gap:10px;flex-shrink:0">
      <div style="flex:1;background:#f0fdf9;border-radius:10px;padding:10px 12px;border:1px solid #d1faf4">
        <p style="font-size:9px;font-weight:700;color:#0d9488;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:4px">Homeroom Teacher</p>
        <div style="display:flex;align-items:center;gap:7px">
          <div id="cpTeacherAvatar" style="width:24px;height:24px;border-radius:50%;background:#0d9488;color:#fff;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0">T</div>
          <p id="cpTeacher" style="font-size:13px;font-weight:700;color:#1e293b">--</p>
        </div>
      </div>
      <div style="background:#f0fdf9;border-radius:10px;padding:10px 12px;border:1px solid #d1faf4;text-align:center;min-width:64px">
        <p style="font-size:9px;font-weight:700;color:#0d9488;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:2px">Students</p>
        <p id="cpStudentCount" style="font-family:'JetBrains Mono',monospace;font-size:20px;font-weight:700;color:#1e293b">--</p>
      </div>
      <div style="background:#f0fdf9;border-radius:10px;padding:10px 12px;border:1px solid #d1faf4;text-align:center;min-width:64px">
        <p style="font-size:9px;font-weight:700;color:#0d9488;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:2px">Tasks</p>
        <p id="cpTaskCount" style="font-family:'JetBrains Mono',monospace;font-size:20px;font-weight:700;color:#1e293b">--</p>
      </div>
    </div>

    <!-- Description -->
    <div id="cpDescWrap" style="padding:10px 20px;border-bottom:1px solid #d1faf4;flex-shrink:0;display:none">
      <p id="cpDesc" style="font-size:12px;color:#64748b;line-height:1.5"></p>
    </div>

    <!-- Action buttons -->
    <div style="padding:10px 20px;border-bottom:1px solid #d1faf4;display:flex;gap:8px;flex-shrink:0">
      <button onclick="editClassFromPanel()" class="btn-edit" style="flex:1;justify-content:center;font-size:12px">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Class
      </button>
      <button onclick="openAddTask()" class="btn-primary" style="flex:1;justify-content:center;font-size:12px">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Task
      </button>
      <button onclick="deleteClassFromPanel()" class="btn-danger" style="padding:8px 12px">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      </button>
    </div>

    <!-- Tabs -->
    <div style="padding:0 20px;border-bottom:1px solid #d1faf4;display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <div>
        <button onclick="switchCPTab('students')" id="cpTabStudents" style="padding:10px 16px;font-size:12px;font-weight:700;border:none;background:transparent;cursor:pointer;border-bottom:2px solid #0d9488;color:#0d9488;font-family:'Syne',sans-serif">Students</button>
        <button onclick="switchCPTab('tasks')" id="cpTabTasks" style="padding:10px 16px;font-size:12px;font-weight:600;border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;color:#94a3b8;font-family:'Syne',sans-serif">Tasks</button>
      </div>
      <button onclick="exportClassData()" id="cpExportBtn" style="display:flex;align-items:center;gap:5px;background:#f0fdf9;color:#0d9488;border:1px solid #99f6e4;padding:4px 10px;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;font-family:'Syne',sans-serif;transition:all 0.15s">
        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
      </button>
    </div>

    <!-- Tab content -->
    <div style="flex:1;overflow-y:auto">
      <div id="cpStudentsTab"><div id="cpStudentList" style="min-height:80px"></div></div>
      <div id="cpTasksTab" style="display:none"><div id="cpTaskList" style="min-height:80px"></div></div>
    </div>

    <input type="hidden" id="cpClassId">
  </div>
</div>


<!-- APP SHELL -->
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

    <div style="padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08)">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:26px;height:26px;border-radius:50%;background:rgba(13,148,136,.2);border:1px solid rgba(13,148,136,.3);display:flex;align-items:center;justify-content:center;color:#5eead4;font-weight:700;font-size:10px;flex-shrink:0">T</div>
        <div style="min-width:0">
          <p style="color:#fff;font-size:11px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Teacher Name</p>
          <span class="badge" style="background:rgba(13,148,136,.2);color:#5eead4">teacher</span>
        </div>
      </div>
    </div>

    <nav style="flex:1;padding:8px;overflow-y:auto">
      <p class="nav-section">Main</p>
      <a class="nav-item on" id="n-dash" onclick="goToPage('dash',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        Dashboard
      </a>
      <a class="nav-item" id="n-tasks" onclick="goToPage('tasks',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        My Tasks
      </a>
      <a class="nav-item" id="n-subs" onclick="goToPage('subs',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Submissions
      </a>
      <a class="nav-item" id="n-subj" onclick="goToPage('subj',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        My Subjects
      </a>
      <a class="nav-item" id="n-cls" onclick="goToPage('cls',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        Classes
      </a>
      <p class="nav-section">Analytics</p>
      <a class="nav-item" id="n-pts" onclick="goToPage('pts',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        Points Given
      </a>
      <a class="nav-item" id="n-rep" onclick="goToPage('rep',this)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
          <h1 style="font-size:14px;font-weight:700;color:#1e293b" id="pageTitle">Dashboard</h1>
          <p style="font-size:11px;color:#94a3b8" id="pageDate">Loading...</p>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <button style="position:relative;padding:6px;border-radius:8px;background:transparent;border:none;cursor:pointer;color:#64748b">
          <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span class="notif-dot"></span>
        </button>
        <div style="width:28px;height:28px;border-radius:50%;background:#0d9488;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px">T</div>
      </div>
    </header>

    <div class="scroll" id="scr">

      <!-- ===== DASHBOARD ===== -->
      <div id="p-dash" class="page on">
        <div class="grid-4" style="margin-bottom:14px">
          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#f0fdf9;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#0d9488" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
              <span class="badge" style="background:#f0fdf9;color:#0d9488">Active</span>
            </div>
            <p class="stat-number" style="color:#1e293b" id="dTotalTasks">{{ $totalTasks }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">Total Tasks</p>
          </div>
          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <span class="badge" style="background:#fffbeb;color:#d97706">Review</span>
            </div>
            <p class="stat-number" style="color:#1e293b" id="dPendingGrading">{{ $pendingGrading }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">Pending Grading</p>
          </div>
          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#ecfdf5;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#059669" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <span class="badge" style="background:#ecfdf5;color:#059669">Done</span>
            </div>
            <p class="stat-number" style="color:#1e293b" id="dTotalGraded">{{ $totalGraded }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">Graded</p>
          </div>
          <div class="card" style="padding:14px">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
              <div style="width:34px;height:34px;border-radius:10px;background:#f5f3ff;display:flex;align-items:center;justify-content:center"><svg style="width:16px;height:16px;color:#7c3aed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
              <span class="badge" style="background:#f5f3ff;color:#7c3aed">Today</span>
            </div>
            <p class="stat-number" style="color:#1e293b" id="dPointsGiven">{{ $pointsGiven }}</p>
            <p style="font-size:11px;color:#64748b;margin-top:2px">Points Today</p>
          </div>
        </div>

        <div class="dash-cols" style="display:grid;grid-template-columns:1fr 280px;gap:14px">

          <!-- Left: Classes + Activity -->
          <div style="display:flex;flex-direction:column;gap:14px;min-width:0">

            <!-- Assigned Classes Section -->
            <div class="card" style="overflow:hidden">
              <div style="padding:12px 16px;border-bottom:1px solid #d1faf4;display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:8px">
                  <h2 style="font-size:13px;font-weight:600;color:#1e293b">Assigned Classes</h2>
                  <span id="dashClsCount" class="badge" style="background:#f0fdf9;color:#0d9488">--</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                  <a onclick="goToPage('subj',document.getElementById('n-subj'))" style="font-size:12px;color:#0d9488;cursor:pointer;font-weight:600">View all →</a>
                </div>
              </div>

              <div id="dashClsGrid" class="dash-cls-grid">
                <div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading assigned classes...</div>
              </div>
            </div>

            <!-- Dashboard Tasks Section -->
            <div class="card" style="overflow:hidden">
              <div style="padding:12px 16px;border-bottom:1px solid #d1faf4;display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:8px">
                  <h2 style="font-size:13px;font-weight:600;color:#1e293b">My Tasks</h2>
                  <span id="dashTasksCount" class="badge" style="background:#f0fdf9;color:#0d9488">--</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                  <button onclick="openAddTask()" style="display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:7px;background:#0d9488;color:#fff;border:none;cursor:pointer;font-size:11px;font-weight:600;font-family:'Syne',sans-serif">
                    <svg style="width:10px;height:10px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New
                  </button>
                  <a onclick="goToPage('tasks',document.getElementById('n-tasks'))" style="font-size:12px;color:#0d9488;cursor:pointer;font-weight:600">View all →</a>
                </div>
              </div>

              <div id="dashTasksGrid" class="dash-cls-grid">
                <div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading tasks...</div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="card" style="overflow:hidden">
              <div style="padding:12px 16px;border-bottom:1px solid #d1faf4;display:flex;align-items:center;justify-content:space-between">
                <h2 style="font-size:13px;font-weight:600;color:#1e293b">Recent Submission Activity</h2>
                <a onclick="goToPage('subs',document.getElementById('n-subs'))" style="font-size:12px;color:#0d9488;cursor:pointer;font-weight:600">View all →</a>
              </div>
              <div id="actList">
                @forelse($recentActivity as $act)
                  <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
                    <div>
                      <p style="font-size:13px;font-weight:600;color:#1e293b">{{ $act['student'] }}</p>
                      <p style="font-size:11px;color:#64748b">{{ $act['task'] }} - {{ $act['subject'] }}</p>
                    </div>
                    <div style="text-align:right">
                      <span class="badge" style="background:#f0fdf9;color:#0d9488;text-transform:capitalize">{{ $act['status'] }}</span>
                      @if($act['grade'] !== null)
                        <p style="font-size:11px;font-weight:700;color:#0d9488;margin-top:4px">Grade: {{ $act['grade'] }}</p>
                      @endif
                    </div>
                  </div>
                @empty
                  <div style="padding:36px;text-align:center;color:#94a3b8;font-size:13px">No recent activity</div>
                @endforelse
              </div>
            </div>
          </div>

          <!-- Right: Quick Actions -->
          <div class="card" style="overflow:hidden;align-self:start">
            <div style="padding:12px 14px;border-bottom:1px solid #d1faf4"><h2 style="font-size:13px;font-weight:600;color:#1e293b">Quick Actions</h2></div>
            <div style="padding:10px;display:flex;flex-direction:column;gap:7px">
              <button onclick="openAddTask()" class="btn-primary" style="width:100%;justify-content:center">
                <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Create New Task
              </button>
              <button onclick="goToPage('subs',document.getElementById('n-subs'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f59e0b;color:#fff;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">
                Review Submissions
              </button>
              <button onclick="openAddClass()" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f0fdf9;color:#0d9488;border:1px solid #d1faf4;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">Add Class</button>
              <button onclick="goToPage('cls',document.getElementById('n-cls'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f0fdf9;color:#0d9488;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">Manage Classes</button>
              <button onclick="goToPage('subj',document.getElementById('n-subj'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f0fdf9;color:#0d9488;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">Manage Subjects</button>
              <button onclick="goToPage('rep',document.getElementById('n-rep'))" style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;border-radius:9px;background:#f0fdf9;color:#0d9488;border:none;cursor:pointer;font-size:12.5px;font-weight:600;font-family:'Syne',sans-serif">View Reports</button>
            </div>
          </div>
        </div>
      </div>

      <!-- ===== MY TASKS ===== -->
      <div id="p-tasks" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e293b">My Tasks</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Tasks you've created</p></div>
          <button onclick="openAddTask()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>New Task</button>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>Title</th><th class="hide-mobile">Subject</th><th class="hide-mobile">Class</th><th>Due</th><th class="hide-mobile">Submissions</th><th style="text-align:right">Actions</th></tr></thead>
              <tbody id="tBody"></tbody>
            </table>
            <div id="tEmpty" class="empty-state" style="display:none"><p>No tasks yet — create your first one!</p></div>
          </div>
        </div>
      </div>

      <!-- ===== SUBMISSIONS ===== -->
      <div id="p-subs" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e293b">Submissions</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Review and grade student work</p></div>
          <div style="display:flex;gap:8px;flex-wrap:wrap" class="filter-row">
            <select id="sfTask" onchange="loadSubmissions()" class="form-input" style="width:auto;min-width:110px;padding:7px 10px;font-size:12px"><option value="">All Tasks</option></select>
            <select id="sfStatus" onchange="loadSubmissions()" class="form-input" style="width:auto;min-width:100px;padding:7px 10px;font-size:12px">
              <option value="">All Status</option><option value="submitted">Submitted</option><option value="correcting">Correcting</option><option value="graded">Graded</option><option value="taken">Taken</option>
            </select>
          </div>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>Student</th><th class="hide-mobile">Task</th><th class="hide-mobile">Subject</th><th>Status</th><th class="hide-mobile">Grade</th><th style="text-align:right">Actions</th></tr></thead>
              <tbody id="subBody"></tbody>
            </table>
            <div id="subEmpty" class="empty-state" style="display:none"><p>No submissions found</p></div>
          </div>
        </div>
      </div>

      <!-- ===== MY SUBJECTS ===== -->
      <div id="p-subj" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e293b">My Subjects</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Subjects and classes assigned to you</p></div>
          <button onclick="openAddSubject()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add Subject</button>
        </div>
        <div class="class-grid" id="subjGrid"></div>
        <div id="subjEmpty" class="empty-state" style="display:none">
          <svg style="width:40px;height:40px;margin:0 auto 10px;color:#cbd5e1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          <p>No subjects assigned yet</p>
        </div>
      </div>

      <!-- ===== CLASSES ===== -->
      <div id="p-cls" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e293b">Classes</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Create and manage classes</p></div>
          <button onclick="openAddClass()" class="btn-primary"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>Add Class</button>
        </div>
        <div id="clsStats" style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap">
          <div class="card" style="padding:12px 16px;display:flex;align-items:center;gap:10px;flex:1;min-width:120px">
            <div style="width:32px;height:32px;border-radius:9px;background:#f0fdf9;display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg style="width:15px;height:15px;color:#0d9488" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
            <div><p class="stat-number" style="font-size:20px;color:#1e293b" id="clsTotalNum">--</p><p style="font-size:11px;color:#64748b">Total Classes</p></div>
          </div>
          <div class="card" style="padding:12px 16px;display:flex;align-items:center;gap:10px;flex:1;min-width:120px">
            <div style="width:32px;height:32px;border-radius:9px;background:#f0fdf9;display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg style="width:15px;height:15px;color:#0d9488" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div><p class="stat-number" style="font-size:20px;color:#1e293b" id="clsTotalStudents">--</p><p style="font-size:11px;color:#64748b">Total Students</p></div>
          </div>
        </div>
        <div class="class-grid" id="clsGrid"></div>
        <div id="clsEmpty" class="empty-state" style="display:none">
          <svg style="width:40px;height:40px;margin:0 auto 10px;color:#cbd5e1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          <p>No classes yet — create your first one!</p>
        </div>
      </div>

      <!-- ===== POINTS GIVEN ===== -->
      <div id="p-pts" class="page">
        <div class="page-header"><div><h2 style="font-size:15px;font-weight:700;color:#1e293b">Points Given</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Points awarded via grading</p></div></div>
        <div class="grid-3" style="margin-bottom:14px">
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#0d9488" id="ptTotal">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Total Given</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#059669" id="ptAwarded">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Positive</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#ef4444" id="ptDeducted">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Deductions</p></div>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>#</th><th>Student</th><th class="hide-mobile">Subject</th><th class="hide-mobile">Description</th><th>Points</th><th class="hide-mobile">Date</th></tr></thead>
              <tbody id="ptBody"></tbody>
            </table>
            <div id="ptEmpty" class="empty-state" style="display:none"><p>No points given yet</p></div>
          </div>
        </div>
      </div>

      <!-- ===== REPORTS ===== -->
      <div id="p-rep" class="page">
        <div class="page-header">
          <div><h2 style="font-size:15px;font-weight:700;color:#1e293b">Reports</h2><p style="font-size:12px;color:#64748b;margin-top:2px">Analytics and performance overview</p></div>
          <div style="display:flex;gap:8px;flex-wrap:wrap" class="filter-row">
            <select id="repSubj" onchange="loadReports()" class="form-input" style="width:auto;min-width:120px;padding:7px 10px;font-size:12px"><option value="">All Subjects</option></select>
            <select id="repPer" onchange="loadReports()" class="form-input" style="width:auto;min-width:100px;padding:7px 10px;font-size:12px">
              <option value="week">This Week</option><option value="month" selected>This Month</option><option value="all">All Time</option>
            </select>
          </div>
        </div>
        <div class="grid-4" style="margin-bottom:14px">
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#0d9488" id="rTasks">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Tasks</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#2563eb" id="rSubs">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Submissions</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#059669" id="rGraded">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Graded</p></div>
          <div class="card" style="padding:14px;text-align:center"><p class="stat-number" style="color:#7c3aed" id="rAvg">--</p><p style="font-size:11px;color:#64748b;margin-top:3px">Avg Grade</p></div>
        </div>
        <div class="card" style="overflow:hidden;margin-bottom:14px">
          <div style="padding:12px 16px;border-bottom:1px solid #d1faf4"><h3 style="font-size:13px;font-weight:600;color:#1e293b">Class Performance</h3></div>
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>Class</th><th>Subject</th><th class="hide-mobile">Students</th><th class="hide-mobile">Submitted</th><th>Graded</th><th class="hide-mobile">Avg</th><th>Completion</th></tr></thead>
              <tbody id="repBody"></tbody>
            </table>
            <div id="repEmpty" class="empty-state" style="display:none"><p>No data for selected period</p></div>
          </div>
        </div>
        <div class="card" style="overflow:hidden">
          <div style="padding:12px 16px;border-bottom:1px solid #d1faf4"><h3 style="font-size:13px;font-weight:600;color:#1e293b">Point History</h3></div>
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
              <thead><tr class="table-head"><th>Student</th><th class="hide-mobile">Subject</th><th class="hide-mobile">Description</th><th>Points</th><th class="hide-mobile">Date</th></tr></thead>
              <tbody id="repPtsBody"></tbody>
            </table>
            <div id="repPtsEmpty" class="empty-state" style="display:none"><p>No point records</p></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Mobile bottom nav -->
<nav class="mobile-nav" id="mnav">
  <button class="mobile-nav-item on" onclick="mobileGoToPage('dash',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>Home</button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('tasks',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Tasks</button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('subs',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Subs</button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('cls',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>Classes</button>
  <button class="mobile-nav-item" onclick="mobileGoToPage('rep',this)"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Reports</button>
</nav>

<script>
const CSRF = '{{ csrf_token() }}';

// Set date
document.getElementById('pageDate').textContent = new Date().toLocaleDateString('en-US', { weekday:'long', day:'numeric', month:'long', year:'numeric' });

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

const PAGE_TITLES = { dash:'Dashboard', tasks:'My Tasks', subs:'Submissions', subj:'My Subjects', cls:'Classes', pts:'Points Given', rep:'Reports' };

function goToPage(name, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('on'));
  document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('on'));
  document.getElementById('p-'+name)?.classList.add('on');
  if (el) el.classList.add('on');
  document.getElementById('pageTitle').textContent = PAGE_TITLES[name] || name;
  document.getElementById('scr').scrollTop = 0;
  if (name==='dash')  loadDashData();
  if (name==='tasks') loadTasks();
  if (name==='subs')  loadSubmissions();
  if (name==='subj')  loadSubjects();
  if (name==='cls')   loadClasses();
  if (name==='pts')   loadPoints();
  if (name==='rep')   loadReports();
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


// ===== FILE DRAG-DROP =====
function onDragOver(e,z)  { e.preventDefault(); document.getElementById(z).classList.add('drag-over'); }
function onDragLeave(e,z) { document.getElementById(z).classList.remove('drag-over'); }
function onDrop(e,z,inp) {
  e.preventDefault(); document.getElementById(z).classList.remove('drag-over');
  const input=document.getElementById(inp), dt=new DataTransfer();
  Array.from(input.files||[]).forEach(f=>dt.items.add(f));
  Array.from(e.dataTransfer.files).forEach(f=>dt.items.add(f));
  input.files=dt.files; previewTaskFiles(input);
}
let pendingFiles=[];
function previewTaskFiles(input) {
  pendingFiles=Array.from(input.files);
  const grid=document.getElementById('tFP');
  if(!pendingFiles.length){ grid.style.display='none'; return; }
  grid.style.display='grid';
  grid.innerHTML=pendingFiles.map((f,i)=>{
    if(f.type.startsWith('image/')){ const url=URL.createObjectURL(f); return `<div style="position:relative"><img src="${url}" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:1px solid #d1faf4"><button onclick="removeFile(${i})" style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,.5);border:none;border-radius:50%;width:15px;height:15px;cursor:pointer;color:#fff;font-size:9px;line-height:1">✕</button></div>`; }
    return `<div style="background:#f0fdf9;border-radius:8px;padding:6px;border:1px solid #d1faf4;font-size:9px;text-align:center;overflow:hidden">📄 ${f.name.slice(0,10)}</div>`;
  }).join('');
}
function removeFile(i) {
  pendingFiles.splice(i,1); const dt=new DataTransfer();
  pendingFiles.forEach(f=>dt.items.add(f));
  document.getElementById('tFI').files=dt.files; previewTaskFiles(document.getElementById('tFI'));
}


// ===== DASHBOARD DATA =====
let dashClasses = [];

async function loadDashData() {
  // Load classes for dashboard
  const {ok, data} = await apiFetch('/teacher/subjects');
  if (ok) {
    dashClasses = Array.isArray(data) ? data : (data.subjects || []);
    document.getElementById('dashClsCount').textContent = dashClasses.length;
    renderDashClasses();
  }
  // Load stats
  const {ok:sOk, data:stats} = await apiFetch('/teacher/tasks');
  if (sOk) {
    document.getElementById('dTotalTasks').textContent = Array.isArray(stats) ? stats.length : '--';
    document.getElementById('dashTasksCount').textContent = Array.isArray(stats) ? stats.length : '--';
    renderDashTasks(Array.isArray(stats) ? stats.slice(0, 4) : []);
  }
}

function renderDashTasks(tasks) {
  const grid = document.getElementById('dashTasksGrid');
  if (!tasks.length) {
    grid.innerHTML = `<div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">
      No tasks yet. <button onclick="openAddTask()" style="color:#0d9488;font-weight:600;background:none;border:none;cursor:pointer;font-family:'Syne',sans-serif">Create one →</button>
    </div>`;
    return;
  }
  
  grid.innerHTML = tasks.map((t) => {
    const subj = t.teacher_subject?.subject?.name || '--';
    const cls = t.teacher_subject?.class?.name || '--';
    const dueStr = t.due_date ? new Date(t.due_date).toLocaleDateString() : 'No due date';
    return `
      <div class="dash-cls-card" onclick="openDetail(${t.id})" style="border-top:3px solid #0d9488;cursor:pointer;display:flex;flex-direction:column;justify-content:space-between">
        <div style="padding:14px">
          <h3 style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:6px">${t.title}</h3>
          <p style="font-size:11px;color:#64748b;margin-bottom:6px">${subj} - ${cls}</p>
          <p style="font-size:10px;font-weight:600;color:#0d9488;background:#f0fdf9;padding:3px 6px;border-radius:4px;display:inline-block;border:1px solid #ccfbf1">Due: ${dueStr}</p>
        </div>
        <div style="padding:10px 14px;background:#f8fafc;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:10px;font-weight:600;color:#64748b">Submissions</span>
          <span style="font-size:12px;font-weight:700;color:#1e293b">${t.submissions_count || t.pending_count || 0}</span>
        </div>
      </div>
    `;
  }).join('');
}

function renderDashClasses() {
  const grid = document.getElementById('dashClsGrid');
  if (!dashClasses.length) {
    grid.innerHTML = `<div style="grid-column:1/-1;padding:30px;text-align:center;color:#94a3b8;font-size:13px">
      No assigned classes yet. <button onclick="goToPage('subj',document.getElementById('n-subj'))" style="color:#0d9488;font-weight:600;background:none;border:none;cursor:pointer;font-family:'Syne',sans-serif">Add one →</button>
    </div>`;
    return;
  }

  const COLORS = [
    ['#0d9488','#14b8a6'], ['#3b82f6','#06b6d4'], ['#8b5cf6','#0d9488'],
    ['#f59e0b','#0d9488'], ['#10b981','#3b82f6'], ['#0ea5e9','#14b8a6'],
  ];

  grid.innerHTML = dashClasses.map((c, i) => {
    const [from, to] = COLORS[i % COLORS.length];
    const subject = c.subject_name || '--';
    const classn = c.class_name || '--';
    const students = c.students_count || 0;
    const tasks    = c.tasks_count || 0;
    return `
      <div class="dash-cls-card" onclick="goToPage('subj',document.getElementById('n-subj'))" style="border-top:3px solid ${from};cursor:pointer">
        <div style="padding:16px;display:flex;gap:12px;align-items:center">
          <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,${from},${to});display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:800;flex-shrink:0">
            ${subject[0].toUpperCase()}
          </div>
          <div style="min-width:0">
            <h3 style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${subject}</h3>
            <p style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:4px">
              ${classn}
            </p>
          </div>
        </div>
        <div style="padding:10px 16px;background:#f8fafc;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
          <div style="display:flex;align-items:center;gap:4px;color:#64748b;font-size:11px">
            <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            ${students} Students
          </div>
          <div style="display:flex;align-items:center;gap:4px;color:#64748b;font-size:11px">
            <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            ${tasks} Tasks
          </div>
        </div>
      </div>
    `;
  }).join('');
}


// ===== CLASS DETAIL PANEL =====
let currentPanelClassId = null;

async function openClassPanel(classId) {
  let c = allClasses.find(x=>x.id===classId) || dashClasses.find(x=>x.id===classId);
  // dashClasses from /teacher/subjects uses class_id, not id
  if (!c) c = dashClasses.find(x=>x.class_id===classId);
  // If still not found locally, fetch from API
  if (!c) {
    const {ok, data} = await apiFetch(`/teacher/classes/${classId}`);
    if (!ok) { showToast('Could not load class details.', 'error'); return; }
    c = data;
  }
  currentPanelClassId = classId;

  document.getElementById('cpClassId').value = classId;
  const displayName = c.name || c.class_name || '?';
  document.getElementById('cpAvatar').textContent = displayName[0].toUpperCase();
  document.getElementById('cpName').textContent = displayName;
  document.getElementById('cpCode').textContent = c.join_code || c.code || '--';
  document.getElementById('cpCodeRaw').value    = c.join_code || c.code || '';
  document.getElementById('cpTeacher').textContent = c.teacher_name || 'Not assigned';
  document.getElementById('cpTeacherAvatar').textContent = getInitials(c.teacher_name||'?');
  document.getElementById('cpStudentCount').textContent  = c.student_count ?? c.students_count ?? '--';
  document.getElementById('cpTaskCount').textContent     = c.task_count    ?? c.tasks_count    ?? '--';

  const descWrap = document.getElementById('cpDescWrap');
  if (c.description) { descWrap.style.display='block'; document.getElementById('cpDesc').textContent=c.description; }
  else               { descWrap.style.display='none'; }

  switchCPTab('students');
  document.getElementById('classPanel').classList.add('on');
  document.body.style.overflow='hidden';
  loadPanelStudents(classId);
}

function closeClassPanel() {
  document.getElementById('classPanel').classList.remove('on');
  document.body.style.overflow='';
  currentPanelClassId=null;
}

function switchCPTab(tab) {
  document.getElementById('cpStudentsTab').style.display = tab==='students'?'block':'none';
  document.getElementById('cpTasksTab').style.display    = tab==='tasks'   ?'block':'none';
  const btnS=document.getElementById('cpTabStudents'), btnT=document.getElementById('cpTabTasks');
  if (tab==='students') {
    btnS.style.borderBottomColor='#0d9488'; btnS.style.color='#0d9488'; btnS.style.fontWeight='700';
    btnT.style.borderBottomColor='transparent'; btnT.style.color='#94a3b8'; btnT.style.fontWeight='600';
    if (currentPanelClassId) loadPanelStudents(currentPanelClassId);
  } else {
    btnT.style.borderBottomColor='#0d9488'; btnT.style.color='#0d9488'; btnT.style.fontWeight='700';
    btnS.style.borderBottomColor='transparent'; btnS.style.color='#94a3b8'; btnS.style.fontWeight='600';
    if (currentPanelClassId) loadPanelTasks(currentPanelClassId);
  }
}

async function loadPanelStudents(classId) {
  const list = document.getElementById('cpStudentList');
  list.innerHTML='<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading student report...</div>';
  const {ok,data} = await apiFetch(`/teacher/classes/${classId}/report`);
  if (!ok) { list.innerHTML='<div style="padding:30px;text-align:center;color:#ef4444;font-size:13px">Failed to load students.</div>'; return; }
  const students=data.students||[];
  const tasks=data.tasks||[];
  document.getElementById('cpStudentCount').textContent=students.length;
  if (!students.length) {
    list.innerHTML=`<div style="padding:40px 20px;text-align:center;color:#94a3b8">
      <div style="width:44px;height:44px;border-radius:12px;background:#f0fdf9;border:1px solid #d1faf4;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px">
        <svg style="width:18px;height:18px;color:#0d9488" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No students enrolled</p>
      <p style="font-size:12px">Students join with the class code.</p>
    </div>`;
    return;
  }

  let thHTML = `<th>Rank</th><th>Student</th><th>Total Points</th><th>Avg Grade</th>`;
  tasks.forEach(t => {
      thHTML += `<th class="hide-mobile" style="min-width:100px">Task: ${t.title}</th>`;
  });
  
  let trHTML = students.map(s => {
      let tds = `
        <td class="mono" style="font-size:12px;font-weight:700;color:#0d9488">#${s.rank}</td>
        <td>
          <div style="display:flex;align-items:center;gap:8px">
            <div style="width:28px;height:28px;border-radius:50%;background:#f0fdf9;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;border:1px solid #d1faf4">${getInitials(s.name)}</div>
            <div>
              <p style="font-size:13px;font-weight:600;color:#1e293b">${s.name}</p>
              <p style="font-size:10px;color:#94a3b8">${s.email}</p>
            </div>
          </div>
        </td>
        <td class="mono" style="font-size:13px;font-weight:700;color:#1e293b">${s.total_points}</td>
        <td class="mono" style="font-size:13px;color:#64748b">${s.average_grade}</td>
      `;
      tasks.forEach(t => {
          let g = s.grades[t.id];
          if (g === null) {
              tds += `<td class="hide-mobile mono" style="font-size:12px"><span style="color:#94a3b8">—</span> <span style="font-size:9px;color:#cbd5e1">/ ${t.max_grade}</span></td>`;
          } else if (typeof g === 'number' || !isNaN(g)) {
              tds += `<td class="hide-mobile mono" style="font-size:12px"><span style="color:#059669">${g}</span> <span style="font-size:9px;color:#cbd5e1">/ ${t.max_grade}</span></td>`;
          } else {
              tds += `<td class="hide-mobile mono" style="font-size:12px"><span class="tag s-${g}" style="font-size:9px;padding:2px 4px;margin-bottom:0;text-transform:capitalize">${g}</span></td>`;
          }
      });
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

async function loadPanelTasks(classId) {
  const list=document.getElementById('cpTaskList');
  list.innerHTML='<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">Loading tasks...</div>';
  const {ok,data}=await apiFetch('/teacher/tasks');
  if(!ok){ list.innerHTML='<div style="padding:30px;text-align:center;color:#ef4444;font-size:13px">Failed to load tasks.</div>'; return; }
  const tasks=Array.isArray(data)?data.filter(t=>t.teacher_subject?.class_id===classId||t.teacher_subject?.class?.id===classId):[];
  document.getElementById('cpTaskCount').textContent=tasks.length;
  if(!tasks.length){
    list.innerHTML=`<div style="padding:40px 20px;text-align:center;color:#94a3b8">
      <div style="width:44px;height:44px;border-radius:12px;background:#f0fdf9;border:1px solid #d1faf4;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px">
        <svg style="width:18px;height:18px;color:#0d9488" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
      <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No tasks for this class</p>
      <button onclick="closeClassPanel();openAddTask()" class="btn-primary" style="margin:8px auto 0;font-size:12px">Create Task</button>
    </div>`;
    return;
  }
  list.innerHTML=tasks.map(t=>{
    const expired=t.due_date&&new Date(t.due_date)<new Date();
    return `<div style="padding:12px 20px;border-bottom:1px solid #f0fdf9;display:flex;align-items:center;justify-content:space-between">
      <div style="min-width:0;flex:1;margin-right:10px">
        <p style="font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${t.title}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:2px">${t.teacher_subject?.subject?.name||'--'}</p>
      </div>
      <div style="text-align:right;flex-shrink:0">
        <p style="font-size:11px;font-family:'JetBrains Mono',monospace;color:${expired?'#ef4444':'#94a3b8'}">${formatDateShort(t.due_date)}</p>
        <p style="font-size:10px;color:#64748b;margin-top:2px">${t.submissions_count||0} subs</p>
      </div>
    </div>`;
  }).join('');
}

function editClassFromPanel() {
  if (!currentPanelClassId) return;
  const id=currentPanelClassId; closeClassPanel();
  setTimeout(()=>openEditClassFull(id), 300);
}

function exportClassData() {
  if (!currentPanelClassId) return;
  window.location.href = `/teacher/classes/${currentPanelClassId}/export`;
}

function deleteClassFromPanel() {
  if (!currentPanelClassId) return;
  const c=dashClasses.find(x=>x.id===currentPanelClassId)||allClasses.find(x=>x.id===currentPanelClassId);
  const id=currentPanelClassId; closeClassPanel();
  setTimeout(()=>askDelete('class',id,c?.name||'this class'), 300);
}

document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeClassPanel(); });

function copyCode(code) {
  if(!code) return;
  navigator.clipboard.writeText(code).then(()=>showToast('Join code copied!','success'));
}

function showToast(message, type='success') {
  const toast=document.createElement('div');
  toast.style.cssText=`position:fixed;bottom:80px;right:20px;z-index:9999;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;font-family:'Syne',sans-serif;box-shadow:0 8px 24px rgba(0,0,0,0.15);transition:all 0.3s;opacity:0;transform:translateY(10px);display:flex;align-items:center;gap:8px;`;
  if(type==='success'){ toast.style.background='#f0fdf9'; toast.style.color='#0d9488'; toast.style.border='1px solid #99f6e4'; toast.innerHTML=`<svg style="width:14px;height:14px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>${message}`; }
  else { toast.style.background='#fef2f2'; toast.style.color='#ef4444'; toast.innerHTML=message; }
  document.body.appendChild(toast);
  requestAnimationFrame(()=>{ toast.style.opacity='1'; toast.style.transform='translateY(0)'; });
  setTimeout(()=>{ toast.style.opacity='0'; toast.style.transform='translateY(10px)'; setTimeout(()=>toast.remove(),300); },2500);
}


// ===== TASKS =====
let allTasks=[], editTaskId=null, currentDetailTaskId=null;

async function loadTasks() {
  const {ok,data}=await apiFetch('/teacher/tasks');
  if(ok){ allTasks=data; renderTasks(); }
}

function renderTasks() {
  const tbody=document.getElementById('tBody'), empty=document.getElementById('tEmpty');
  if(!allTasks.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=allTasks.map(t=>{
    const safe=(t.title||'').replace(/'/g,"\\'"), expired=t.due_date&&new Date(t.due_date)<new Date(), pending=t.pending_count||0, imgs=t.images_count||0;
    const imgThumb=imgs>0?`<div style="width:28px;height:28px;border-radius:7px;background:#f0fdf9;border:1px solid #d1faf4;display:flex;align-items:center;justify-content:center;font-size:8px;color:#0d9488;font-weight:700;flex-shrink:0">${imgs}🖼</div>`:'';
    return `<tr class="table-row">
      <td><div style="display:flex;align-items:center;gap:8px">${imgThumb}<div><p style="font-weight:600;color:#1e293b;font-size:13px">${t.title}</p><p class="hide-mobile" style="font-size:11px;color:#94a3b8;margin-top:1px">${t.description||'--'}</p></div></div></td>
      <td class="hide-mobile" style="color:#64748b;font-size:13px">${t.teacher_subject?.subject?.name||'--'}</td>
      <td class="hide-mobile"><span class="tag s-active">${t.teacher_subject?.class?.name||'--'}</span></td>
      <td class="mono" style="font-size:11px;color:${expired?'#ef4444':'#94a3b8'}">${formatDateShort(t.due_date)}</td>
      <td class="hide-mobile"><div style="display:flex;gap:4px"><span class="tag" style="background:#f1f5f9;color:#64748b">${t.submissions_count||0}</span>${pending>0?`<span class="tag s-correcting">${pending}</span>`:''}</div></td>
      <td style="text-align:right"><div style="display:flex;gap:4px;justify-content:flex-end">
        <button onclick="viewTaskDetail(${t.id})" class="btn-secondary" style="padding:4px 8px;font-size:11px">View</button>
        <button onclick="openEditTask(${t.id})" class="btn-edit">Edit</button>
        <button onclick="askDelete('task',${t.id},'${safe}')" class="btn-danger">Del</button>
      </div></td>
    </tr>`;
  }).join('');
}

async function openAddTask() {
  editTaskId=null;
  document.getElementById('mTaskTitle').textContent='New Task';
  document.getElementById('tSaveBtn').textContent='Create Task';
  ['tTitle','tDesc','tDue'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('tGrade').value='100';
  document.getElementById('tFP').style.display='none'; document.getElementById('tFP').innerHTML='';
  pendingFiles=[]; document.getElementById('tFI').value='';
  await fillTeacherSubjectSelect('tTS');
  openModal('mTask');
}

async function openEditTask(id) {
  editTaskId=id; const t=allTasks.find(x=>x.id===id);
  document.getElementById('mTaskTitle').textContent='Edit Task'; document.getElementById('tSaveBtn').textContent='Save Changes';
  document.getElementById('tTitle').value=t.title; document.getElementById('tDesc').value=t.description||'';
  document.getElementById('tDue').value=t.due_date||''; document.getElementById('tGrade').value=100;
  document.getElementById('tFP').style.display='none'; pendingFiles=[]; document.getElementById('tFI').value='';
  await fillTeacherSubjectSelect('tTS'); document.getElementById('tTS').value=t.teacher_subject_id;
  openModal('mTask');
}

function openEditFromDetail() { closeModal('mDetail'); openEditTask(currentDetailTaskId); }

async function fillTeacherSubjectSelect(selectId) {
  const {ok,data}=await apiFetch('/teacher/tasks/create'); if(!ok) return;
  document.getElementById(selectId).innerHTML='<option value="">-- Select --</option>'+data.teacherSubjects.map(ts=>`<option value="${ts.id}">${ts.subject} (${ts.class})</option>`).join('');
}

async function saveTask() {
  const title=document.getElementById('tTitle').value.trim(), ts=document.getElementById('tTS').value;
  if(!title||!ts){ alert('Title and Class/Subject are required.'); return; }
  const fd=new FormData();
  fd.append('title',title); fd.append('description',document.getElementById('tDesc').value.trim());
  fd.append('teacher_subject_id',ts);
  if(document.getElementById('tDue').value) fd.append('due_date',document.getElementById('tDue').value);
  const maxGrade = document.getElementById('tGrade').value;
  if (maxGrade) fd.append('max_grade', maxGrade);
  pendingFiles.forEach((f,i)=>fd.append(`files[${i}]`,f));
  if(editTaskId) fd.append('_method','PUT');
  const {ok,data}=await apiFetch(editTaskId?`/teacher/tasks/${editTaskId}`:'/teacher/tasks',{method:'POST',body:fd});
  if(ok){ closeModal('mTask'); loadTasks(); showToast(editTaskId?'Task updated!':'Task created!'); }
  else { alert(data.message||'Something went wrong.'); }
}

async function viewTaskDetail(id) {
  currentDetailTaskId=id; const {ok,data}=await apiFetch(`/task/${id}/detail`);
  if(!ok){ alert('Could not load task.'); return; }
  document.getElementById('dtTitle').textContent=data.title||'--'; document.getElementById('dtMeta').textContent=`${data.subject||'--'} · ${data.class||'--'}`;
  document.getElementById('dtDesc').textContent=data.description||'No description.'; document.getElementById('dtSubj').textContent=data.subject||'--';
  document.getElementById('dtDue').textContent=formatDate(data.due_date); document.getElementById('dtGrade').textContent=data.max_grade||'100';
  const imgEl=document.getElementById('dtImgs');
  if(data.images&&data.images.length){ imgEl.style.display='grid'; imgEl.style.gridTemplateColumns='repeat(auto-fill,minmax(64px,1fr))'; imgEl.style.gap='7px'; imgEl.innerHTML=data.images.map(img=>`<img src="${img.url}" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:1px solid #d1faf4;cursor:pointer" onclick="window.open('${img.url}','_blank')">`).join(''); }
  else { imgEl.style.display='none'; }
  const attachWrap=document.getElementById('dtAttach');
  if(data.attachments&&data.attachments.length){ attachWrap.style.display='block'; document.getElementById('dtAttachList').innerHTML=data.attachments.map(a=>`<a href="${a.url}" target="_blank" style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;text-decoration:none;color:#374151;font-size:12px;margin-bottom:6px"><span>📄</span><span style="flex:1">${a.name}</span></a>`).join('');}
  else { attachWrap.style.display='none'; }
  openModal('mDetail');
}


// ===== SUBMISSIONS =====
let cachedTasks=[];

async function loadSubmissions() {
  if(!allTasks.length) await loadTasks(); cachedTasks=allTasks;
  const taskSel=document.getElementById('sfTask'), cur=taskSel.value;
  taskSel.innerHTML='<option value="">All Tasks</option>'+cachedTasks.map(t=>`<option value="${t.id}"${t.id==cur?' selected':''}>${t.title}</option>`).join('');
  const params=new URLSearchParams();
  if(taskSel.value) params.append('task_id',taskSel.value);
  const status=document.getElementById('sfStatus').value; if(status) params.append('status',status);
  const {ok,data}=await apiFetch(`/teacher/submissions?${params}`); if(!ok) return;
  window.currentSubmissions = data;
  const tbody=document.getElementById('subBody'), empty=document.getElementById('subEmpty');
  if(!data.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=data.map(s=>{
    const gradeHtml=s.grade!==null?`<span class="mono" style="font-weight:700;color:#059669">${s.grade}/${s.max_grade}</span>`:'<span style="color:#94a3b8;font-size:11px">—</span>';
    const actions=[];
    if(s.status==='submitted') actions.push(`<button onclick="setSubmissionStatus(${s.id},'correcting')" class="btn-amber" style="font-size:10px">Review</button>`);
    if(s.status==='submitted'||s.status==='correcting') actions.push(`<button onclick="openGradeModal(${s.id})" class="btn-primary" style="font-size:10px;padding:4px 8px">Grade</button>`);
    return `<tr class="table-row">
      <td><div style="display:flex;align-items:center;gap:8px"><div style="width:27px;height:27px;border-radius:50%;background:#f0fdf9;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0">${getInitials(s.student_name)}</div><div><p style="font-weight:600;font-size:13px;color:#1e293b">${s.student_name}</p><p class="hide-mobile" style="font-size:11px;color:#94a3b8">${s.class||'--'}</p></div></div></td>
      <td class="hide-mobile" style="font-weight:500;color:#374151;font-size:13px">${s.task_title||'--'}</td>
      <td class="hide-mobile" style="color:#64748b;font-size:13px">${s.subject||'--'}</td>
      <td><span class="tag s-${s.status||'taken'}">${s.status}</span></td>
      <td class="hide-mobile">${gradeHtml}</td>
      <td style="text-align:right"><div style="display:flex;gap:4px;justify-content:flex-end">${actions.join('')}</div></td>
    </tr>`;
  }).join('');
}

async function setSubmissionStatus(id,status) {
  const {ok}=await apiFetch(`/teacher/submissions/${id}/status`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({status})});
  if(ok) loadSubmissions(); else alert('Could not update status.');
}
function setGradeStatus(status,btn) {
  document.getElementById('gStatus').value=status;
  document.querySelectorAll('#mGrade .tab-btn').forEach(b=>b.classList.remove('on')); btn.classList.add('on');
}
function openGradeModal(submissionId) {
  const s = window.currentSubmissions.find(x => x.id === submissionId);
  if (!s) return;
  document.getElementById('gSubID').value=submissionId; document.getElementById('gStudent').textContent=s.student_name;
  document.getElementById('gMax').textContent=s.max_grade; document.getElementById('gVal').value=''; document.getElementById('gVal').max=s.max_grade;
  document.getElementById('gFeedback').value=''; document.getElementById('gStatus').value='graded';
  
  const detailsEl = document.getElementById('gSubDetails');
  if (detailsEl) {
    let html = '';
    if (s.note) html += `<div style="margin-bottom:8px"><p style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:2px">Student's Note</p><p style="font-size:13px;color:#1e293b;background:#f8fafc;padding:8px 10px;border-radius:8px;border:1px solid #e2e8f0">${s.note}</p></div>`;
    if (s.files && s.files.length) {
      html += `<p style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px">Attached Files</p><div style="display:flex;flex-wrap:wrap;gap:6px">`;
      html += s.files.map(f => {
        if (f.name.match(/\\.(jpeg|jpg|png|webp|gif)$/i)) {
          return `<a href="${f.url}" target="_blank"><img src="${f.url}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #cbd5e1"></a>`;
        }
        return `<a href="${f.url}" target="_blank" style="font-size:11px;background:#e2e8f0;padding:4px 8px;border-radius:6px;color:#334155;text-decoration:none">${f.name}</a>`;
      }).join('');
      html += `</div>`;
    }
    if (!html) html = `<p style="font-size:12px;color:#94a3b8;font-style:italic">No notes or files submitted.</p>`;
    detailsEl.innerHTML = html;
  }
  
  document.querySelectorAll('#mGrade .tab-btn').forEach((b,i)=>b.classList.toggle('on',i===1)); openModal('mGrade');
}
async function submitGrade() {
  const id=document.getElementById('gSubID').value, grade=document.getElementById('gVal').value, feedback=document.getElementById('gFeedback').value.trim(), status=document.getElementById('gStatus').value;
  if(!grade){ alert('Please enter a grade.'); return; }
  const {ok,data}=await apiFetch(`/teacher/submissions/${id}/grade`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({grade,feedback,status})});
  if(ok){ closeModal('mGrade'); loadSubmissions(); showToast('Grade saved!'); }
  else { alert(data.message||'Something went wrong.'); }
}


// ===== SUBJECTS =====
async function loadSubjects() {
  const {ok,data}=await apiFetch('/teacher/subjects'); if(!ok) return;
  const grid=document.getElementById('subjGrid'), empty=document.getElementById('subjEmpty');
  if(!data.length){ grid.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  grid.innerHTML=data.map((s,i)=>{
    const safeName=(s.subject_name||'').replace(/'/g,"\\'");
    return `
    <div class="class-card" style="cursor:pointer" onclick="openClassPanel(${s.class_id})">
      <div class="class-card-bar"></div>
      <div style="padding:14px 16px">
        <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:10px">
          <div style="min-width:0;flex:1">
            <h3 style="font-size:14px;font-weight:700;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${s.subject_name||'--'}</h3>
            <p style="font-size:11px;color:#94a3b8;margin-top:2px">${s.class_name||'--'}</p>
          </div>
          <div style="display:flex;gap:4px;flex-shrink:0;margin-left:8px" onclick="event.stopPropagation()">
            <a href="/teacher/classes/${s.class_id}/export" class="btn-primary" style="padding:4px 8px;font-size:10px;text-decoration:none">Export Excel</a>
            <button onclick="askDelete('subj',${s.id},'${safeName}')" class="btn-danger" style="padding:4px 8px;font-size:10px">Del</button>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-bottom:12px">
          <div style="flex:1;background:#f8fafc;border-radius:8px;padding:7px 10px;text-align:center"><p class="mono" style="font-size:16px;font-weight:700;color:#1e293b">${s.students_count||0}</p><p style="font-size:10px;color:#64748b">Students</p></div>
          <div style="flex:1;background:#f8fafc;border-radius:8px;padding:7px 10px;text-align:center"><p class="mono" style="font-size:16px;font-weight:700;color:#1e293b">${s.tasks_count||0}</p><p style="font-size:10px;color:#64748b">Tasks</p></div>
        </div>
        <div style="margin-top:9px;display:flex;align-items:center;gap:4px;color:#0d9488">
          <p style="font-size:10px;font-weight:600">Click to view details</p>
          <svg style="width:10px;height:10px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
      </div>
    </div>`;
  }).join('');
}
async function openAddSubject() {
  const {ok,data}=await apiFetch('/teacher/subjects/create'); if(!ok){ alert('Could not load data.'); return; }
  document.getElementById('smSubj').innerHTML='<option value="">-- Select --</option>'+data.subjects.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
  document.getElementById('smClass').innerHTML='<option value="">-- Select --</option>'+data.classes.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
  openModal('mSubj');
}
async function saveSubject() {
  const sid=document.getElementById('smSubj').value, cid=document.getElementById('smClass').value;
  if(!sid||!cid){ alert('Select both subject and class.'); return; }
  const {ok,data}=await apiFetch('/teacher/subjects',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({subject_id:sid,class_id:cid})});
  if(ok){ closeModal('mSubj'); loadSubjects(); } else { alert(data.message||'Something went wrong.'); }
}


// ===== CLASSES =====
let allClasses=[], editClassId=null;

async function loadClasses() {
  const {ok,data}=await apiFetch('/teacher/classes'); if(!ok) return;
  allClasses=Array.isArray(data)?data:(data.classes||[]);
  dashClasses=allClasses; // keep in sync
  renderClasses();
}

function renderClasses() {
  const grid=document.getElementById('clsGrid'), empty=document.getElementById('clsEmpty');
  const total=allClasses.length, totalStudents=allClasses.reduce((s,c)=>s+(c.student_count||c.students_count||0),0);
  document.getElementById('clsTotalNum').textContent=total; document.getElementById('clsTotalStudents').textContent=totalStudents;
  if(!total){ grid.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  grid.innerHTML=allClasses.map(c=>{
    const safeName=(c.name||'').replace(/'/g,"\\'"), created=c.created_at?new Date(c.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}):'--';
    const code=c.join_code||c.code||'--', students=c.student_count??c.students_count??0, tasks=c.task_count??c.tasks_count??0;
    return `
    <div class="class-card" style="cursor:pointer" onclick="openClassPanel(${c.id})">
      <div class="class-card-bar"></div>
      <div style="padding:14px 16px">
        <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:10px">
          <div style="min-width:0;flex:1">
            <h3 style="font-size:14px;font-weight:700;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${c.name}</h3>
            <p style="font-size:11px;color:#94a3b8;margin-top:2px">Created ${created}</p>
          </div>
          <div style="display:flex;gap:4px;flex-shrink:0;margin-left:8px" onclick="event.stopPropagation()">
            <button onclick="openRenameClass(${c.id},'${safeName}')" class="btn-edit" style="padding:4px 8px;font-size:10px">Rename</button>
            <button onclick="openEditClassFull(${c.id})" class="btn-amber" style="padding:4px 8px;font-size:10px">Edit</button>
            <button onclick="askDelete('class',${c.id},'${safeName}')" class="btn-danger" style="padding:4px 8px;font-size:10px">Del</button>
          </div>
        </div>
        ${c.description?`<p style="font-size:12px;color:#64748b;line-height:1.5;margin-bottom:10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">${c.description}</p>`:''}
        <div style="display:flex;gap:8px;margin-bottom:12px">
          <div style="flex:1;background:#f8fafc;border-radius:8px;padding:7px 10px;text-align:center"><p class="mono" style="font-size:16px;font-weight:700;color:#1e293b">${students}</p><p style="font-size:10px;color:#64748b">Students</p></div>
          <div style="flex:1;background:#f8fafc;border-radius:8px;padding:7px 10px;text-align:center"><p class="mono" style="font-size:16px;font-weight:700;color:#1e293b">${tasks}</p><p style="font-size:10px;color:#64748b">Tasks</p></div>
        </div>
        <div class="join-code-box">
          <svg style="width:13px;height:13px;color:#0d9488;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
          <span class="mono">${code}</span>
          <button class="copy-btn" onclick="event.stopPropagation();copyCode('${code}')" title="Copy join code"><svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>
        </div>
        <div style="margin-top:9px;display:flex;align-items:center;gap:4px;color:#0d9488">
          <p style="font-size:10px;font-weight:600">Click to view details</p>
          <svg style="width:10px;height:10px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
      </div>
    </div>`;
  }).join('');
}

function genJoinCode() {
  const chars='ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  const code=Array.from({length:3},()=>chars[Math.floor(Math.random()*chars.length)]).join('')+'-'+Array.from({length:4},()=>'0123456789'[Math.floor(Math.random()*10)]).join('');
  document.getElementById('clCode').value=code;
}

function openAddClass() {
  editClassId=null;
  document.getElementById('mClassTitle').textContent='Add Class'; document.getElementById('mClassSub').textContent='Create a new class';
  document.getElementById('clSaveBtn').textContent='Add Class';
  document.getElementById('clName').value=''; document.getElementById('clCode').value=''; document.getElementById('clDesc').value=''; document.getElementById('clId').value='';
  document.getElementById('clSubj').innerHTML='<option value="">Loading...</option>';
  openModal('mClass');
  apiFetch('/teacher/subjects/create').then(({ok,data}) => {
    if(ok) document.getElementById('clSubj').innerHTML='<option value="">-- Select --</option>'+data.subjects.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
  });
}

function openEditClassFull(id) {
  const c=allClasses.find(x=>x.id===id)||dashClasses.find(x=>x.id===id); if(!c) return;
  editClassId=id;
  document.getElementById('mClassTitle').textContent='Edit Class'; document.getElementById('mClassSub').textContent='Update class details';
  document.getElementById('clSaveBtn').textContent='Save Changes';
  document.getElementById('clName').value=c.name||''; document.getElementById('clCode').value=c.join_code||c.code||''; document.getElementById('clDesc').value=c.description||''; document.getElementById('clId').value=id;
  document.getElementById('clSubj').innerHTML='<option value="">Loading...</option>';
  openModal('mClass');
  apiFetch('/teacher/subjects/create').then(({ok,data}) => {
    if(ok) document.getElementById('clSubj').innerHTML='<option value="">-- Select --</option>'+data.subjects.map(s=>`<option value="${s.id}"${c.subject_name==s.name?' selected':''}>${s.name}</option>`).join('');
  });
}

async function saveClass() {
  const name=document.getElementById('clName').value.trim();
  let code=document.getElementById('clCode').value.trim().toUpperCase();
  const desc=document.getElementById('clDesc').value.trim();
  const subj=document.getElementById('clSubj').value;
  if(!name){ alert('Class name is required.'); return; }

  // Auto-generate join code if blank
  if(!code) {
    const chars='ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    code=Array.from({length:3},()=>chars[Math.floor(Math.random()*chars.length)]).join('')+'-'+Array.from({length:4},()=>'0123456789'[Math.floor(Math.random()*10)]).join('');
    document.getElementById('clCode').value=code;
  }

  const payload={name, join_code:code, description:desc, subject_id:subj};
  const id=document.getElementById('clId').value;

  const btn=document.getElementById('clSaveBtn');
  btn.disabled=true; btn.textContent='Saving...';

  const {ok,data}=await apiFetch(id?`/teacher/classes/${id}`:'/teacher/classes',{
    method:id?'PUT':'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)
  });

  btn.disabled=false; btn.textContent=id?'Save Changes':'Add Class';

  if(ok){
    closeModal('mClass');
    showToast(id?'Class updated!':'Class created!','success');
    // Refresh both the classes page and dashboard grid
    await loadClasses();
    renderDashClasses();
    document.getElementById('dashClsCount').textContent=allClasses.length;
  } else {
    alert(data.message||Object.values(data.errors||{})[0]?.[0]||'Something went wrong.');
  }
}

// Quick Rename
function openRenameClass(id,currentName) { document.getElementById('renId').value=id; document.getElementById('renName').value=currentName; openModal('mRename'); }
async function confirmRename() {
  const id=document.getElementById('renId').value, name=document.getElementById('renName').value.trim();
  if(!name){ alert('Name cannot be empty.'); return; }
  const c=allClasses.find(x=>x.id==id);
  const {ok,data}=await apiFetch(`/teacher/classes/${id}`,{method:'PUT',headers:{'Content-Type':'application/json'},body:JSON.stringify({name,join_code:c?.join_code||c?.code||'',description:c?.description||''})});
  if(ok){ closeModal('mRename'); await loadClasses(); renderDashClasses(); showToast('Class renamed!'); }
  else { alert(data.message||'Could not rename.'); }
}


// ===== POINTS =====
async function loadPoints() {
  const {ok:sOk,data:stats}=await apiFetch('/teacher/points/stats');
  if(sOk){ document.getElementById('ptTotal').textContent=Number(stats.total||0).toLocaleString(); document.getElementById('ptAwarded').textContent=Number(stats.awarded||0).toLocaleString(); document.getElementById('ptDeducted').textContent=Number(stats.deducted||0).toLocaleString(); }
  const {ok,data}=await apiFetch('/teacher/points'); if(!ok) return;
  const tbody=document.getElementById('ptBody'), empty=document.getElementById('ptEmpty');
  if(!data.length){ tbody.innerHTML=''; empty.style.display='block'; return; }
  empty.style.display='none';
  tbody.innerHTML=data.map((r,i)=>`
    <tr class="table-row">
      <td class="mono" style="font-size:11px;color:#94a3b8">${String(i+1).padStart(2,'0')}</td>
      <td><div style="display:flex;align-items:center;gap:7px"><div style="width:25px;height:25px;border-radius:50%;background:#f0fdf9;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700">${getInitials(r.student)}</div><span style="font-weight:600;font-size:13px;color:#1e293b">${r.student}</span></div></td>
      <td class="hide-mobile" style="color:#64748b;font-size:13px">${r.subject||'--'}</td>
      <td class="hide-mobile" style="color:#94a3b8;font-size:12px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.description||'--'}</td>
      <td class="mono" style="font-weight:700;font-size:13px;color:${r.type==='positive'?'#059669':'#ef4444'}">${r.points>0?'+':''}${r.points}</td>
      <td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(r.date)}</td>
    </tr>`).join('');
}


// ===== REPORTS =====
async function loadReports() {
  const period=document.getElementById('repPer').value, sid=document.getElementById('repSubj').value;
  const {ok:subjOk,data:subjData}=await apiFetch('/teacher/subjects');
  if(subjOk){ const sel=document.getElementById('repSubj'), cur=sel.value; sel.innerHTML='<option value="">All Subjects</option>'+subjData.map(s=>`<option value="${s.id}"${s.id==cur?' selected':''}>${s.subject_name} (${s.class_name})</option>`).join(''); }
  const params=new URLSearchParams(); params.append('period',period); if(sid) params.append('ts_id',sid);
  const {ok,data}=await apiFetch(`/teacher/report?${params}`); if(!ok) return;
  document.getElementById('rTasks').textContent=data.summary?.tasks??'--'; document.getElementById('rSubs').textContent=data.summary?.submissions??'--'; document.getElementById('rGraded').textContent=data.summary?.graded??'--';
  document.getElementById('rAvg').textContent=data.summary?.avg_grade!=null?Number(data.summary.avg_grade).toFixed(1)+'%':'--';
  const repTbody=document.getElementById('repBody'), repEmpty=document.getElementById('repEmpty');
  if(!data.breakdown||!data.breakdown.length){ repTbody.innerHTML=''; repEmpty.style.display='block'; }
  else { repEmpty.style.display='none'; repTbody.innerHTML=data.breakdown.map(r=>{ const pct=r.total_students>0?Math.round((r.submitted_count/r.total_students)*100):0; return `<tr class="table-row"><td><span class="tag s-active">${r.class_name}</span></td><td style="font-weight:600;color:#1e293b;font-size:13px">${r.subject_name}</td><td class="hide-mobile" style="color:#64748b;font-size:13px">${r.total_students}</td><td class="hide-mobile" style="color:#2563eb;font-size:13px">${r.submitted_count}</td><td style="color:#059669;font-size:13px">${r.graded_count}</td><td class="hide-mobile mono" style="font-weight:600;font-size:13px">${r.avg_grade!=null?Number(r.avg_grade).toFixed(1):'--'}</td><td><div style="display:flex;align-items:center;gap:5px"><div style="flex:1;background:#e2e8f0;border-radius:4px;height:5px;overflow:hidden"><div style="width:${pct}%;background:#0d9488;height:100%;border-radius:4px"></div></div><span class="mono" style="font-size:10px;color:#64748b;min-width:26px">${pct}%</span></div></td></tr>`; }).join(''); }
  const ptsTbody=document.getElementById('repPtsBody'), ptsEmpty=document.getElementById('repPtsEmpty');
  if(!data.points||!data.points.length){ ptsTbody.innerHTML=''; ptsEmpty.style.display='block'; }
  else { ptsEmpty.style.display='none'; ptsTbody.innerHTML=data.points.map(r=>`<tr class="table-row"><td><div style="display:flex;align-items:center;gap:7px"><div style="width:25px;height:25px;border-radius:50%;background:#f0fdf9;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700">${getInitials(r.student)}</div><span style="font-weight:600;font-size:13px;color:#1e293b">${r.student}</span></div></td><td class="hide-mobile" style="color:#64748b;font-size:13px">${r.subject||'--'}</td><td class="hide-mobile" style="color:#94a3b8;font-size:12px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.description||'--'}</td><td class="mono" style="font-weight:700;font-size:13px;color:${r.type==='positive'?'#059669':'#ef4444'}">${r.points>0?'+':''}${r.points}</td><td class="hide-mobile mono" style="font-size:11px;color:#94a3b8">${formatDateShort(r.date)}</td></tr>`).join(''); }
}


// ===== DELETE =====
let pendingDelete=null;
function askDelete(type,id,name) { pendingDelete={type,id}; document.getElementById('delMsg').textContent=`Delete "${name}"? This cannot be undone.`; openModal('mDel'); }
async function confirmDelete() {
  if(!pendingDelete) return;
  const urls={task:`/teacher/tasks/${pendingDelete.id}`,subj:`/teacher/subjects/${pendingDelete.id}`,class:`/teacher/classes/${pendingDelete.id}`};
  const {ok,data}=await apiFetch(urls[pendingDelete.type],{method:'DELETE'});
  closeModal('mDel');
  if(ok){
    showToast('Deleted successfully.');
    if(pendingDelete.type==='task')  loadTasks();
    if(pendingDelete.type==='subj')  loadSubjects();
    if(pendingDelete.type==='class'){ await loadClasses(); renderDashClasses(); document.getElementById('dashClsCount').textContent=allClasses.length; }
  } else { alert(data.message||'Could not delete.'); }
}

// Init
loadDashData();
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