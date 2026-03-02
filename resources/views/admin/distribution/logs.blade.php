{{-- resources/views/admin/distribution/logs.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Distribution Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue:       #1B3F7A;
            --blue-dark:  #122D5A;
            --blue-light: #2459A8;
            --blue-pale:  #EAF0FA;
            --yellow:     #F5C518;
            --yellow-dark:#D4A800;
            --green:      #16A34A;
            --green-pale: #DCFCE7;
            --orange:     #D97706;
            --orange-pale:#FFFBEB;
            --red:        #C0392B;
            --red-pale:   #FEF2F2;
            --white:      #FFFFFF;
            --gray-50:    #F7F8FA;
            --gray-100:   #F0F2F5;
            --gray-200:   #DEE2E8;
            --gray-400:   #9AA3B0;
            --gray-600:   #5A6372;
            --gray-800:   #2C3340;
            --sidebar-w:  256px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Open Sans', sans-serif; background: var(--gray-100); color: var(--gray-800); font-size: 14px; }

        /* ─── SHELL ─── */
        .shell {
            display: grid;
            grid-template-rows: 36px 76px 1fr 48px;
            grid-template-columns: var(--sidebar-w) 1fr;
            grid-template-areas: "topbar topbar" "header header" "sidebar main" "footer footer";
            height: 100vh;
            overflow: hidden;
        }

        /* ─── TOPBAR ─── */
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ─── HEADER ─── */
        header { grid-area: header; background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; align-items: center; padding: 0 28px; gap: 14px; z-index: 90; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; margin-left: -4px; border-radius: 4px; color: var(--blue-dark); flex-shrink: 0; transition: background 0.15s; }
        .hamburger:hover { background: var(--blue-pale); }
        .hamburger svg { width: 22px; height: 22px; display: block; }
        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-admin-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid var(--gray-200); border-radius: 4px; flex-shrink: 0; }
        .admin-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .admin-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .admin-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); }
        .sidebar-close:hover { background: var(--red-pale); color: var(--red); }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-left: 3px solid transparent; transition: background 0.12s, color 0.12s, border-color 0.12s; }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; color: inherit; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn { width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white); border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        /* ─── PAGE TITLEBAR ─── */
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; flex-wrap: wrap; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .btn-create { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; background: var(--green); color: var(--white); border: none; border-radius: 4px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: none; cursor: pointer; transition: background 0.15s; flex-shrink: 0; }
        .btn-create:hover { background: #15803D; }
        .btn-create svg { width: 14px; height: 14px; }

        /* ─── SUMMARY CARDS ─── */
        .summary-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .summary-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 16px 18px; }
        .summary-card.green  { border-top-color: var(--green); }
        .summary-card.orange { border-top-color: var(--orange); }
        .summary-card.yellow { border-top-color: var(--yellow-dark); }
        .summary-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 6px; }
        .summary-value { font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .summary-card.green  .summary-value { color: var(--green); }
        .summary-card.orange .summary-value { color: var(--orange); }
        .summary-card.yellow .summary-value { color: var(--yellow-dark); }

        /* ─── FILTER BOX ─── */
        .filter-box { background: var(--white); border: 1px solid var(--gray-200); padding: 16px 20px; margin-bottom: 16px; }
        .filter-box-header { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 12px; }
        .filters { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap: 10px; align-items: end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }
        input[type="text"], input[type="date"], select {
            width: 100%; padding: 8px 10px; border: 1px solid var(--gray-200); border-radius: 3px;
            font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); background: var(--white); outline: none;
        }
        input:focus, select:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.1); }
        input::placeholder { color: var(--gray-400); }
        .btn-filter { padding: 8px 16px; background: var(--blue); color: var(--white); border: none; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background 0.15s; white-space: nowrap; align-self: end; }
        .btn-filter:hover { background: var(--blue-dark); }
        .btn-clear { padding: 8px 14px; background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-decoration: none; white-space: nowrap; align-self: end; display: inline-block; text-align: center; transition: background 0.15s; }
        .btn-clear:hover { background: var(--gray-100); }

        /* ─── TABLE ─── */
        .table-wrap { background: var(--white); border: 1px solid var(--gray-200); overflow: hidden; }
        .table-header { padding: 13px 20px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; gap: 10px; justify-content: space-between; }
        .table-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); display: flex; align-items: center; gap: 8px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .table-count { font-size: 11px; color: var(--gray-400); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 11px 16px; border-bottom: 1px solid var(--gray-100); text-align: left; font-size: 13px; }
        th { background: var(--gray-50); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-600); border-bottom: 1px solid var(--gray-200); }
        tbody tr { transition: background 0.1s; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody tr:last-child td { border-bottom: none; }

        /* Status badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-upcoming  { background: var(--blue-pale);   color: var(--blue); }
        .badge-ongoing   { background: var(--green-pale);  color: var(--green); }
        .badge-completed { background: var(--gray-100);    color: var(--gray-600); }
        .badge-cancelled { background: var(--red-pale);    color: var(--red); }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

        /* Action buttons */
        .btn-view { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F3; border-radius: 3px; font-size: 11px; font-weight: 600; cursor: pointer; font-family: 'Open Sans', sans-serif; text-decoration: none; transition: background 0.15s; }
        .btn-view:hover { background: var(--blue); color: var(--white); }
        .btn-view svg { width: 12px; height: 12px; }

        /* Empty state */
        .empty-state { padding: 60px 40px; text-align: center; }
        .empty-icon { width: 48px; height: 48px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* Pagination */
        .pagination-wrap { padding: 14px 20px; border-top: 1px solid var(--gray-200); background: var(--gray-50); display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--gray-600); }
        .pagination-wrap .links a, .pagination-wrap .links span { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-600); font-size: 12px; text-decoration: none; border-radius: 3px; margin: 0 2px; transition: all 0.15s; }
        .pagination-wrap .links span[aria-current] { background: var(--blue); color: var(--white); border-color: var(--blue); font-weight: 700; }
        .pagination-wrap .links a:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }

        /* ─── MODAL ─── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 500; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: var(--white); width: 90%; max-width: 960px; max-height: 88vh; display: flex; flex-direction: column; border-top: 4px solid var(--blue); box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); flex-shrink: 0; }
        .modal-head-left { display: flex; align-items: center; gap: 10px; }
        .modal-head-icon { width: 32px; height: 32px; background: var(--blue-pale); border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .modal-head-icon svg { width: 16px; height: 16px; color: var(--blue); }
        .modal-head h2 { font-family: 'PT Serif', serif; font-size: 16px; font-weight: 700; color: var(--blue-dark); }
        .modal-head-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .modal-close { background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--gray-600); transition: background 0.15s; flex-shrink: 0; }
        .modal-close:hover { background: var(--red-pale); color: var(--red); }
        .modal-close svg { width: 15px; height: 15px; }
        .modal-body { padding: 20px 22px; overflow-y: auto; flex: 1; }
        .modal-loading { display: flex; align-items: center; justify-content: center; height: 120px; color: var(--gray-400); font-size: 13px; gap: 10px; }
        .spinner { width: 18px; height: 18px; border: 2px solid var(--gray-200); border-top-color: var(--blue); border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ─── FOOTER ─── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.45); }
        .footer-left strong { color: rgba(255,255,255,0.75); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.25); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; }
            .sidebar { grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 300; transform: translateX(-100%); transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }
            .hamburger { display: flex; }
            header { padding: 0 16px; gap: 10px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .main-content { padding: 20px 16px; }
            .summary-row { grid-template-columns: repeat(2, 1fr); }
            .filters { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline, .status-indicator { display: none; }
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider, .header-logos img:last-child, .header-org { display: none; }
            .header-title { font-size: 13px; }
            .main-content { padding: 16px 12px; }
            .summary-row { grid-template-columns: 1fr 1fr; }
            .filters { grid-template-columns: 1fr; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
        }
    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">Republic of the Philippines &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic</div>
        <div class="topbar-right">
            <span class="clock-date-inline" id="top-date">—</span>
            <span class="clock-inline" id="top-time">00:00:00</span>
            <span class="status-indicator">System Online</span>
        </div>
    </div>

    <!-- HEADER -->
    <header>
        <button class="hamburger" onclick="openSidebar()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div class="header-logos">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
            <div class="logo-divider"></div>
            <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal">
        </div>
        <div class="header-text">
            <div class="header-org">Office of the Municipal DRRMO</div>
            <div class="header-title">MDRRMO — Naic, Cavite</div>
            <div class="header-sub">Municipal Disaster Risk Reduction and Management Office</div>
        </div>
        <div class="header-spacer"></div>
        <div class="header-admin-badge">
            <div class="admin-avatar">A</div>
            <div>
                <div class="admin-name">Administrator</div>
                <div class="admin-role">Full Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <div class="nav-section-label">Admin Menu</div>

        <a href="{{ route('admin.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard Overview
        </a>
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Distribution Events
        </a>
        <a href="{{ route('admin.distribution.logs') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
        </a>
        <a href="{{ route('admin.residents.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            List of Residents
        </a>
        <a href="{{ route('admin.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Households
        </a>

        <hr class="sidebar-sep">
        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
        @if(session('success'))
            <div style="background:#DCFCE7;border:1px solid #16A34A;color:#15803D;padding:12px 16px;border-radius:3px;margin-bottom:16px;font-size:13px;font-weight:600;">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#FEF2F2;border:1px solid #C0392B;color:#C0392B;padding:12px 16px;border-radius:3px;margin-bottom:16px;font-size:13px;font-weight:600;">
                ✕ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#FEF2F2;border:1px solid #C0392B;color:#C0392B;padding:12px 16px;border-radius:3px;margin-bottom:16px;font-size:13px;font-weight:600;">
                ✕ {{ $errors->first() }}
            </div>
        @endif

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Home</a> / <span>Distribution Logs</span>
                </div>
                <div class="page-h1">Distribution Events</div>
                <div class="page-sub">View all relief distribution events and household recipient details</div>
            </div>
            <a href="{{ route('admin.events.quick-create') }}" class="btn-create">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                New Event
            </a>
        </div>

        {{-- Summary Cards --}}
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-label">Total Events</div>
                <div class="summary-value">{{ $events->total() }}</div>
            </div>
            <div class="summary-card green">
                <div class="summary-label">Ongoing</div>
                <div class="summary-value">{{ $events->where('status','ongoing')->count() }}</div>
            </div>
            <div class="summary-card orange">
                <div class="summary-label">Upcoming</div>
                <div class="summary-value">{{ $events->where('status','upcoming')->count() }}</div>
            </div>
            <div class="summary-card yellow">
                <div class="summary-label">Completed</div>
                <div class="summary-value">{{ $events->where('status','completed')->count() }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filter-box">
            <div class="filter-box-header">Filter Events</div>
            <form method="GET">
                <div class="filters">
                    <div class="filter-group">
                        <div class="filter-label">Search</div>
                        <input type="text" name="search" placeholder="Event name, relief type…" value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">From Date</div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">To Date</div>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">Status</div>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="upcoming"  {{ request('status')=='upcoming'  ? 'selected':'' }}>Upcoming</option>
                            <option value="ongoing"   {{ request('status')=='ongoing'   ? 'selected':'' }}>Ongoing</option>
                            <option value="completed" {{ request('status')=='completed' ? 'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-filter">Filter</button>
                    <a href="{{ route('admin.distribution.logs') }}" class="btn-clear">Clear</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            <div class="table-header">
                <div class="table-title">
                    <div class="ca-dot"></div>
                    Distribution Events Log
                </div>
                <div class="table-count">{{ $events->total() }} event(s) found</div>
            </div>

            @if($events->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="empty-title">No distribution events found</div>
                    <div class="empty-sub">Try adjusting your filters or create a new event.</div>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Event Name</th>
                            <th>Relief Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th style="text-align:center">Distributed</th>
                            <th style="text-align:center">Households</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $i => $event)
                            <tr>
                                <td style="color:var(--gray-400); font-size:12px;">{{ $events->firstItem() + $i }}</td>
                                <td>
                                    <strong style="color:var(--blue-dark);">{{ $event->event_name }}</strong>
                                    @if($event->description)
                                        <div style="font-size:11px; color:var(--gray-400); margin-top:2px;">{{ Str::limit($event->description, 50) }}</div>
                                    @endif
                                    @if($event->started_at)
                                        <div style="font-size:10px; color:var(--green); margin-top:2px;">▶ Started: {{ $event->started_at->format('M d, Y H:i') }}</div>
                                    @endif
                                    @if($event->ended_at)
                                        <div style="font-size:10px; color:var(--gray-400); margin-top:2px;">■ Ended: {{ $event->ended_at->format('M d, Y H:i') }}</div>
                                    @endif
                                    @if($event->cancelled_at)
                                        <div style="font-size:10px; color:var(--red); margin-top:2px;">✕ Cancelled: {{ $event->cancelled_at->format('M d, Y H:i') }}</div>
                                    @endif
                                </td>
                                <td style="font-size:12px; color:var(--gray-600);">{{ $event->relief_type ?? '—' }}</td>
                                <td style="font-size:12px; white-space:nowrap;">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                </td>
                                <td>
                                    @php $s = strtolower($event->status); @endphp
                                    <span class="badge badge-{{ $s }}">{{ ucfirst($s) }}</span>
                                </td>
                                <td style="text-align:center; font-weight:700; color:var(--blue-dark);">
                                    {{ $event->total_distributed ?? 0 }}
                                </td>
                                <td style="text-align:center; font-weight:700; color:var(--gray-600);">
                                    {{ $event->unique_households ?? 0 }}
                                </td>
                                <td>
                                    <div style="display:flex; gap:5px; flex-wrap:wrap;">
                                        {{-- View Button --}}
                                        <button class="btn-view" onclick="openModal(
                                            '{{ route('admin.distribution.events.households', $event) }}',
                                            '{{ htmlspecialchars(addslashes($event->event_name), ENT_QUOTES) }}',
                                            '{{ ucfirst(strtolower($event->status)) }}'
                                        )">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            View
                                        </button>

                                        {{-- Cancel Button --}}
                                        @if($event->canCancel())
                                            <button type="button"
                                                onclick="openCancelModal({{ $event->id }}, '{{ addslashes($event->event_name) }}')"
                                                style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#FEF2F2;color:#C0392B;border:1px solid #FECACA;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Open Sans',sans-serif;">
                                                ✕ Cancel
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($events->hasPages())
                    <div class="pagination-wrap">
                        <div>Showing {{ $events->firstItem() }}–{{ $events->lastItem() }} of {{ $events->total() }}</div>
                        <div class="links">{{ $events->withQueryString()->links() }}</div>
                    </div>
                @endif
            @endif
        </div>

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">&copy; <span id="footer-year"></span> <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office</div>
        <div class="footer-center">Republic of the Philippines</div>
        <a class="fb-link" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            facebook.com/naicmdrrmo
        </a>
    </footer>

</div>

{{-- MODAL --}}
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div class="modal-head-left">
                <div class="modal-head-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
                    </svg>
                </div>
                <div>
                    <h2 id="modalTitle">Event Households</h2>
                    <div class="modal-head-sub" id="modalSub">Loading details…</div>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <div class="modal-loading">
                <div class="spinner"></div> Loading households…
            </div>
        </div>
    </div>
</div>

{{-- CANCEL MODAL --}}
<div class="modal-overlay" id="cancelModalOverlay" onclick="closeCancelModal()">
    <div class="modal-box" style="max-width:480px;" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div class="modal-head-left">
                <div class="modal-head-icon" style="background:#FEF2F2;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#C0392B" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div>
                    <h2 id="cancelModalTitle" style="color:#C0392B;">Cancel Event</h2>
                    <div class="modal-head-sub">This action cannot be undone</div>
                </div>
            </div>
            <button class="modal-close" onclick="closeCancelModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="cancelForm" method="POST">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--gray-600);display:block;margin-bottom:6px;">
                        Reason for Cancellation <span style="color:var(--red);">*</span>
                    </label>
                    <textarea name="cancellation_reason" id="cancellationReason" rows="4"
                        placeholder="Please provide a reason for cancelling this event..."
                        style="width:100%;padding:10px;border:1px solid var(--gray-200);border-radius:3px;font-family:'Open Sans',sans-serif;font-size:13px;color:var(--gray-800);outline:none;resize:vertical;"
                        onfocus="this.style.borderColor='var(--red)'"
                        onblur="this.style.borderColor='var(--gray-200)'"></textarea>
                    <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">Minimum 5 characters required</div>
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" onclick="closeCancelModal()"
                        style="padding:8px 16px;background:var(--white);color:var(--gray-600);border:1px solid var(--gray-200);border-radius:3px;font-family:'Open Sans',sans-serif;font-size:12px;font-weight:600;cursor:pointer;">
                        Back
                    </button>
                    <button type="submit"
                        style="padding:8px 16px;background:var(--red);color:#fff;border:none;border-radius:3px;font-family:'Open Sans',sans-serif;font-size:12px;font-weight:700;cursor:pointer;">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="cancelRouteBase" data-url="{{ url('admin/distribution/events') }}" style="display:none;"></div>

<script>
    console.log('SCRIPT STARTED');  // add this as very first line
    /* ─── Clock ─── */
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now = new Date();
        const days  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const shortM= ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock(); setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    /* ─── Sidebar ─── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open');    overlay.classList.add('active');    document.body.style.overflow='hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow=''; }
    document.addEventListener('keydown', e => { if(e.key==='Escape') { closeSidebar(); closeModal(); } });

    /* ─── Modal ─── */
    let currentModalUrl = '';

    function openModal(url, eventName, status) {
        currentModalUrl = url;
        document.getElementById('modalTitle').textContent = eventName;
        document.getElementById('modalSub').textContent = 'Status: ' + status + ' — Household recipients';
        document.getElementById('modalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
        window.loadModalContent(url);
    }

    window.loadModalContent = function(url) {
        console.log('loadModalContent called:', url);
        document.getElementById('modalBody').innerHTML = '<div class="modal-loading"><div class="spinner"></div> Loading households…</div>';

        fetch(url)
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const content = doc.querySelector('.modal-body');
                document.getElementById('modalBody').innerHTML = content
                    ? content.innerHTML
                    : '<p style="color:var(--red); padding:20px;">Could not load content.</p>';

                // Bind ONCE after content is injected
                const btn = document.getElementById('modalSearchBtn');
                const input = document.getElementById('modalSearchInput');

                console.log('btn found:', btn);   // <-- add this
                console.log('input found:', input);

                btn?.addEventListener('click', function() {
                    window.modalSearch();
                });

                input?.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') window.modalSearch();
                });
            })
            .catch(() => {
                document.getElementById('modalBody').innerHTML = '<p style="color:var(--red); padding:20px;">Error loading households. Please try again.</p>';
            });
    }

    window.modalSearch = function() {
        const search = document.getElementById('modalSearchInput')?.value ?? '';
        console.log('Search value:', search);
        console.log('Current URL:', currentModalUrl);
        
        const url = new URL(currentModalUrl, window.location.origin);
        url.searchParams.set('search', search);
        
        console.log('Final URL:', url.toString());
        window.loadModalContent(url.toString());
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        document.body.style.overflow = '';
        currentModalUrl = '';
    }

        /* ─── Cancel Modal ─── */
    function openCancelModal(eventId, eventName) {
        const base = document.getElementById('cancelRouteBase').dataset.url;
        document.getElementById('cancelModalTitle').textContent = 'Cancel: ' + eventName;
        document.getElementById('cancelForm').action = base + '/' + eventId + '/cancel';
        document.getElementById('cancellationReason').value = '';
        document.getElementById('cancelModalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCancelModal() {
        document.getElementById('cancelModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    /* ─── Silent Status Refresh ─── */
    function refreshEventStatuses() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');

                // Update each status badge
                document.querySelectorAll('tbody tr').forEach((row, index) => {
                    const newRow = doc.querySelectorAll('tbody tr')[index];
                    if (!newRow) return;

                    // Update status badge
                    const oldBadge = row.querySelector('.badge');
                    const newBadge = newRow.querySelector('.badge');
                    if (oldBadge && newBadge && oldBadge.textContent !== newBadge.textContent) {
                        oldBadge.className = newBadge.className;
                        oldBadge.textContent = newBadge.textContent;
                    }

                    // Update action buttons (start/end/cancel)
                    const oldActions = row.querySelector('td:last-child');
                    const newActions = newRow.querySelector('td:last-child');
                    if (oldActions && newActions) {
                        oldActions.innerHTML = newActions.innerHTML;
                    }

                    // Update timestamps (started_at, ended_at, cancelled_at)
                    const oldTimestamps = row.querySelector('td:nth-child(2)');
                    const newTimestamps = newRow.querySelector('td:nth-child(2)');
                    if (oldTimestamps && newTimestamps) {
                        oldTimestamps.innerHTML = newTimestamps.innerHTML;
                    }
                });

                // Update summary cards
                document.querySelectorAll('.summary-card .summary-value').forEach((card, index) => {
                    const newCard = doc.querySelectorAll('.summary-card .summary-value')[index];
                    if (newCard && card.textContent !== newCard.textContent) {
                        card.textContent = newCard.textContent;

                        // Flash animation to show it updated
                        card.style.transition = 'color 0.3s';
                        card.style.color = 'var(--yellow-dark)';
                        setTimeout(() => card.style.color = '', 1000);
                    }
                });
            })
            .catch(() => {
                // Silently fail — no disruption to user
            });
    }

    // Run every 60 seconds
    setInterval(refreshEventStatuses, 60000);
</script>
</body>
</html>