<!DOCTYPE html>
<html lang="en">
<head>
    <title>Distribution Event — {{ $event->event_name }}</title>
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
            --green-dark: #15803D;
            --purple:     #7C3AED;
            --purple-pale:#F5F3FF;
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

        html, body {
            height: 100%;
            font-family: 'Open Sans', sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            font-size: 14px;
        }

        /* ── SHELL ── */
        .shell {
            display: grid;
            grid-template-rows: 36px 76px 1fr 48px;
            grid-template-columns: var(--sidebar-w) 1fr;
            grid-template-areas:
                "topbar  topbar"
                "header  header"
                "sidebar main"
                "footer  footer";
            height: 100vh;
            overflow: hidden;
        }

        /* ── TOPBAR ── */
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ── HEADER ── */
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

        /* ── SIDEBAR ── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 250; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; opacity: 1; }
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: var(--red-pale); color: var(--red); }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-left: 3px solid transparent; transition: background 0.12s, color 0.12s, border-color 0.12s; }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn { width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white); border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .logout-btn:hover { background: var(--red); }

        /* ── MAIN ── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 24px 28px; }

        /* ── PAGE TITLEBAR ── */
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 11px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date span { display: block; font-size: 12px; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ── EVENT HEADER CARD ── */
        .event-header-card { background: var(--white); border: 1px solid var(--gray-200); border-left: 5px solid var(--blue); padding: 18px 24px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
        .event-title { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--blue-dark); margin-bottom: 8px; }
        .event-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .meta-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-badge.status-ongoing   { background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; }
        .meta-badge.status-upcoming  { background: var(--blue-pale);  color: var(--blue);       border: 1px solid #C7D9F5; }
        .meta-badge.status-completed { background: var(--gray-100);   color: var(--gray-600);   border: 1px solid var(--gray-200); }
        .meta-badge.status-cancelled { background: var(--red-pale);   color: var(--red);        border: 1px solid #FECACA; }
        .meta-badge.mode-household   { background: var(--blue-pale);  color: var(--blue);       border: 1px solid #C7D9F5; }
        .meta-badge.mode-head        { background: var(--purple-pale);color: var(--purple);     border: 1px solid #DDD6FE; }
        .event-desc { font-size: 12px; color: var(--gray-600); margin-top: 8px; max-width: 600px; line-height: 1.5; }

        /* ── EXPORT BUTTONS ── */
        .btn-group { display: flex; gap: 8px; flex-wrap: wrap; flex-shrink: 0; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 4px; font-size: 11px; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.15s; white-space: nowrap; border: none; cursor: pointer; }
        .btn-primary   { background: var(--blue); color: var(--white); }
        .btn-primary:hover { background: var(--blue-dark); }
        .btn-secondary { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
        .btn-secondary:hover { background: var(--gray-200); }
        .btn svg { width: 13px; height: 13px; }

        /* ── STATS ROW ── */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 16px 18px; }
        .stat-card.green  { border-top-color: var(--green); }
        .stat-card.purple { border-top-color: var(--purple); }
        .stat-card.orange { border-top-color: var(--orange); }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 6px; }
        .stat-value { font-family: 'PT Serif', serif; font-size: 28px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .stat-card.green  .stat-value { color: var(--green); }
        .stat-card.purple .stat-value { color: var(--purple); font-size: 14px; padding-top: 6px; font-family: 'Open Sans', sans-serif; }
        .stat-card.orange .stat-value { color: var(--orange); }
        .stat-meta { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        /* ── SECTION CARD ── */
        .section-card { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 16px; }
        .section-header { padding: 13px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }

        /* ── TABLE ── */
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); background: var(--gray-50); border-bottom: 2px solid var(--gray-200); white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 10px 14px; font-size: 12.5px; color: var(--gray-800); vertical-align: middle; }
        .td-serial { font-family: 'Courier New', monospace; font-size: 11px; background: var(--blue-pale); color: var(--blue); padding: 2px 6px; border-radius: 3px; white-space: nowrap; }
        .td-muted { color: var(--gray-400); font-style: italic; font-size: 11px; }
        .td-num { color: var(--gray-400); font-size: 12px; }

        /* ── EMPTY STATE ── */
        .empty-state { padding: 48px 24px; text-align: center; color: var(--gray-400); }
        .empty-state svg { width: 40px; height: 40px; margin: 0 auto 12px; display: block; opacity: 0.3; }
        .empty-state p { font-size: 13px; }

        /* ── FOOTER ── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.45); }
        .footer-left strong { color: rgba(255,255,255,0.75); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.25); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); text-decoration: none; white-space: nowrap; transition: color 0.15s; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; height: 100vh; overflow: hidden; }
            .sidebar { grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 1200; transform: translateX(-100%); transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block !important; z-index: 1100; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }
            .hamburger { display: flex; }
            header { padding: 0 16px; gap: 10px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .header-admin-badge { padding: 6px 10px; gap: 8px; }
            .admin-name { font-size: 12px; }
            .admin-role { display: none; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .main-content { padding: 20px 16px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline, .status-indicator { display: none; }
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider, .header-logos img:last-child, .header-org { display: none; }
            .header-title { font-size: 13px; line-height: 1.3; }
            .header-admin-badge { padding: 5px 8px; }
            .admin-avatar { width: 28px; height: 28px; font-size: 11px; }
            .admin-name { font-size: 11px; }
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }
            .event-header-card { flex-direction: column; }
            .stats-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }
        @media (max-width: 480px) {
            .shell { grid-template-rows: 28px 52px 1fr 40px; }
            .main-content { padding: 10px 8px; }
            .topbar { padding: 0 10px; }
            header { padding: 0 8px; }
            .header-title { font-size: 13px; }
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
        <button class="hamburger" onclick="openSidebar()" aria-label="Open navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div class="header-logos">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
            <div class="logo-divider"></div>
            <img src="{{ asset('images/naic-seal.png') }}" alt="Bayan ng Naic Seal">
        </div>
        <div class="header-text">
            <div class="header-org">Office of the Municipal DRRMO</div>
            <div class="header-title">MDRRMO — Naic, Cavite</div>
            <div class="header-sub">Municipal Disaster Risk Reduction and Management Office</div>
        </div>
        <div class="header-spacer"></div>
        <div class="header-admin-badge">
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="admin-name">{{ auth()->user()->name }}</div>
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
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard Overview
        </a>
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Distribution Events
        </a>
        <a href="{{ route('admin.distribution.logs') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
            Distribution Logs
        </a>
        <a href="{{ route('admin.residents.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
            List of Residents
        </a>
        <a href="{{ route('admin.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
            List of Households
        </a>
        <a href="{{ route('admin.traillog.trail') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Trail Logs
        </a>
        <a href="{{ route('admin.distribution.scan-history') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/><polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/><rect x="8" y="8" width="8" height="8" rx="1"/></svg>
            Staff Scan History
        </a>
        <hr class="sidebar-sep">
        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- Page titlebar -->
        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Home</a> /
                    <a href="{{ route('admin.distribution.logs') }}">Distribution Logs</a> /
                    <span>Event Details</span>
                </div>
                <div class="page-h1">{{ $event->event_name }}</div>
                <div class="page-sub">Distribution event details and log records</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <!-- Event header card -->
        <div class="event-header-card">
            <div>
                <div class="event-meta">
                    <span class="meta-badge status-{{ strtolower($event->status) }}">{{ ucfirst($event->status) }}</span>
                    @if($event->scan_mode === 'family_head')
                        <span class="meta-badge mode-head">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
                            Family Head QR Mode
                        </span>
                    @else
                        <span class="meta-badge mode-household">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                            Household QR Mode
                        </span>
                    @endif
                    <span style="font-size:11px;color:var(--gray-400);">
                        {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('F d, Y') : '—' }}
                    </span>
                </div>
                @if($event->description)
                    <div class="event-desc">{{ $event->description }}</div>
                @endif
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.distribution.events.export.csv', $event) }}" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </a>
                <a href="{{ route('admin.distribution.events.export.xlsx', $event) }}" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    XLSX
                </a>
                <a href="{{ route('admin.distribution.events.export.pdf', $event) }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    PDF
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card green">
                <div class="stat-label">Total Released</div>
                <div class="stat-value">{{ $totalReleased }}</div>
                <div class="stat-meta">distribution records</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unique Households</div>
                <div class="stat-value">{{ $uniqueHouseholds }}</div>
                <div class="stat-meta">households served</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Scan Mode</div>
                <div class="stat-value">
                    @if(($event->scan_mode ?? 'household') === 'family_head') 👤 Per Family Head
                    @else 🏠 Per Household
                    @endif
                </div>
                <div class="stat-meta">QR type accepted</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Duplicates Blocked</div>
                <div class="stat-value">{{ $event->scanAttempts?->where('result', 'duplicate')->count() ?? '—' }}</div>
                <div class="stat-meta">blocked re-releases</div>
            </div>
        </div>

        <!-- Distribution Logs Table -->
        <div class="section-card">
            <div class="section-header">
                <div class="ca-dot"></div>
                <div class="section-title">Distribution Logs — {{ $totalReleased }} records</div>
            </div>

            @if($event->logs->isEmpty())
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                    <p>No distribution records yet for this event.</p>
                </div>
            @else
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Serial Code</th>
                                <th>Household Head</th>
                                @if(($event->scan_mode ?? 'household') === 'family_head')
                                    <th>Family Head (Scanned)</th>
                                @endif
                                <th>Barangay</th>
                                <th>Distributed By</th>
                                <th>Distributed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->logs as $i => $log)
                            <tr>
                                <td class="td-num">{{ $i + 1 }}</td>
                                <td><span class="td-serial">{{ $log->serial_code }}</span></td>
                                <td style="font-weight:600;color:var(--blue-dark);">{{ $log->household?->household_head_name ?? '—' }}</td>
                                @if(($event->scan_mode ?? 'household') === 'family_head')
                                    <td>{{ $log->familyMember?->full_name ?? '—' }}</td>
                                @endif
                                <td>{{ $log->household?->barangay ?? '—' }}</td>
                                <td>{{ $log->staff?->name ?? '—' }}</td>
                                <td style="font-size:12px;color:var(--gray-600);">{{ $log->distributed_at?->format('M d, Y g:i A') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">
            &copy; <span id="footer-year"></span> <strong>MDRRMO Naic, Cavite</strong> — All Rights Reserved
        </div>
        <div class="footer-center">Barangay Family Track QR Relief Distribution System</div>
        <a href="https://www.facebook.com/naicmdrrmo" class="fb-link" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            facebook.com/naicmdrrmo
        </a>
    </footer>

</div>

<script>
    /* ── Clock & date ── */
    function pad(n) { return String(n).padStart(2,'0'); }
    function updateClock() {
        const now = new Date();
        const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        document.getElementById('main-date').textContent = days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    /* ── Sidebar ── */
    const sidebarEl = document.getElementById('sidebar');
    const overlayEl = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebarEl.classList.add('open'); overlayEl.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebarEl.classList.remove('open'); overlayEl.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>
</body>
</html>