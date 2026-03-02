<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — List of Residents</title>
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
            --white:      #FFFFFF;
            --gray-50:    #F7F8FA;
            --gray-100:   #F0F2F5;
            --gray-200:   #DEE2E8;
            --gray-400:   #9AA3B0;
            --gray-600:   #5A6372;
            --gray-800:   #2C3340;
            --red:        #C0392B;
            --red-pale:   #FEF2F2;
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

        /* ─── TOP UTILITY BAR ─── */
        .topbar {
            grid-area: topbar;
            background: var(--blue-dark);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 100;
        }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ─── HEADER ─── */
        header {
            grid-area: header;
            background: var(--white);
            border-bottom: 3px solid var(--yellow);
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            display: flex; align-items: center;
            padding: 0 28px; gap: 14px; z-index: 90;
        }
        .hamburger {
            display: none; background: none; border: none; cursor: pointer;
            padding: 6px; margin-left: -4px; border-radius: 4px;
            color: var(--blue-dark); flex-shrink: 0; transition: background 0.15s;
        }
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
        .header-admin-badge {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px; background: var(--blue-pale);
            border: 1px solid var(--gray-200); border-radius: 4px;
        }
        .admin-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--blue); display: flex; align-items: center; justify-content: center;
            color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .admin-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .admin-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none !important; position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); z-index: 200;
            opacity: 0; transition: opacity 0.25s; pointer-events: none;
        }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar; background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex; flex-direction: column; overflow-y: auto;
        }
        .sidebar-close {
            display: none; position: absolute; top: 12px; right: 12px;
            background: var(--gray-100); border: 1px solid var(--gray-200);
            border-radius: 4px; width: 32px; height: 32px;
            align-items: center; justify-content: center;
            cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s;
        }
        .sidebar-close:hover { background: var(--red-pale); color: var(--red); }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px; font-size: 13.5px; font-weight: 500;
            color: var(--gray-600); text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
        }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; color: inherit; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn {
            width: 100%; font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
            background: var(--blue); color: var(--white); border: none;
            padding: 10px 16px; border-radius: 4px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s;
        }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        .page-titlebar {
            display: flex; align-items: flex-end; justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200); gap: 12px;
        }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--gray-400); text-decoration: none; }
        .page-breadcrumb a:hover { color: var(--blue-light); }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── SUMMARY STATS ─── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            padding: 16px 18px;
        }
        .stat-card.yellow { border-top-color: var(--yellow); }
        .stat-card.green  { border-top-color: var(--green); }
        .stat-card.red    { border-top-color: var(--red); }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 6px; }
        .stat-value { font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .stat-card.yellow .stat-value { color: var(--yellow-dark); }
        .stat-card.green  .stat-value { color: var(--green); }
        .stat-card.red    .stat-value { color: var(--red); }
        .stat-meta { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        /* ─── FILTERS ─── */
        .filters-bar {
            background: var(--white);
            border: 1px solid var(--gray-200);
            padding: 14px 18px;
            display: flex; align-items: center; gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .filter-group { display: flex; align-items: center; gap: 6px; }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); white-space: nowrap; }
        .filter-input, .filter-select {
            border: 1px solid var(--gray-200);
            background: var(--gray-50);
            padding: 7px 10px;
            font-size: 12px; color: var(--gray-800);
            font-family: 'Open Sans', sans-serif;
            outline: none; border-radius: 3px;
            transition: border-color 0.15s;
        }
        .filter-input { width: 220px; }
        .filter-select { padding-right: 28px; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center;
        }
        .filter-input:focus, .filter-select:focus { border-color: var(--blue); background: var(--white); }
        .filter-spacer { flex: 1; }
        .filter-count { font-size: 12px; color: var(--gray-600); white-space: nowrap; }
        .filter-count strong { color: var(--blue-dark); }

        /* ─── TABLE ─── */
        .table-wrap {
            background: var(--white);
            border: 1px solid var(--gray-200);
            overflow-x: auto;
            margin-bottom: 16px;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 11px 14px; text-align: left;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400); background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200); white-space: nowrap;
            cursor: pointer; user-select: none;
        }
        thead th:hover { color: var(--blue); }
        thead th.sorted { color: var(--blue); }
        thead th .sort-icon { display: inline-block; margin-left: 4px; opacity: 0.4; font-size: 9px; }
        thead th.sorted .sort-icon { opacity: 1; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 10px 14px; font-size: 12.5px; color: var(--gray-800); vertical-align: middle; }
        .td-name { font-weight: 600; color: var(--blue-dark); }
        .td-sub  { font-size: 11px; color: var(--gray-400); margin-top: 1px; }
        .td-num  { font-variant-numeric: tabular-nums; }

        /* Badges */
        .badge {
            display: inline-block; padding: 2px 8px; border-radius: 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .badge-head    { background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F5; }
        .badge-member  { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
        .badge-male    { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
        .badge-female  { background: #FDF2F8; color: #9D174D; border: 1px solid #FBCFE8; }
        .badge-pwd     { background: #FFF7ED; color: var(--orange, #C2410C); border: 1px solid #FED7AA; }
        .badge-senior  { background: var(--yellow-pale, #FFFAE6); color: var(--yellow-dark); border: 1px solid #FDE68A; }
        .badge-4ps     { background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; }

        /* ─── EMPTY STATE ─── */
        .empty-state { padding: 56px 24px; text-align: center; }
        .empty-icon {
            width: 52px; height: 52px; border-radius: 50%;
            background: var(--gray-100); border: 2px solid var(--gray-200);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .empty-icon svg { width: 24px; height: 24px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── PAGINATION ─── */
        .pagination {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 18px;
            background: var(--white); border: 1px solid var(--gray-200);
            border-top: none; gap: 12px; flex-wrap: wrap;
        }
        .pagination-info { font-size: 12px; color: var(--gray-600); }
        .pagination-info strong { color: var(--gray-800); }
        .pagination-btns { display: flex; align-items: center; gap: 4px; }
        .pg-btn {
            min-width: 32px; height: 32px; border-radius: 3px;
            border: 1px solid var(--gray-200); background: var(--white);
            font-size: 12px; font-weight: 600; color: var(--gray-600);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            padding: 0 8px; transition: background 0.12s, color 0.12s, border-color 0.12s;
            font-family: 'Open Sans', sans-serif;
        }
        .pg-btn:hover:not(:disabled) { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }
        .pg-btn.active { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .pg-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; gap: 8px; z-index: 100;
        }
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
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; height: 100vh; overflow: hidden; }
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
            .header-admin-badge { padding: 6px 10px; gap: 8px; }
            .admin-name { font-size: 12px; }
            .admin-role { display: none; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .main-content { padding: 20px 16px; }
            .stats-row { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline { display: none; }
            .status-indicator { display: none; }
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider, .header-logos img:last-child, .header-org { display: none; }
            .header-title { font-size: 13px; }
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .filter-input { width: 100%; }
            .filters-bar { gap: 8px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
        }
        @media (max-width: 380px) {
            .main-content { padding: 12px 10px; }
            .stats-row { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- TOP BAR -->
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
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
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

        <!-- 1. Dashboard Overview -->
        <a href="{{ route('admin.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard Overview
        </a>

        <!-- 2. Distribution Events -->
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Distribution Events
        </a>

        <!-- 3. Distribution Logs -->
        <a href="{{ route('admin.distribution.logs') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
        </a>

        <!-- 4. List of Residents (active) -->
        <a href="{{ route('admin.residents.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            List of Residents
        </a>

        <!-- 5. List of Households -->
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

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Home</a> / <span>List of Residents</span>
                </div>
                <div class="page-h1">List of Residents</div>
                <div class="page-sub">All registered household heads and family members across all barangays.</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        {{-- SUMMARY STATS --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Residents</div>
                <div class="stat-value">{{ $totalResidents }}</div>
                <div class="stat-meta">heads + members</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Household Heads</div>
                <div class="stat-value">{{ $totalHeads }}</div>
                <div class="stat-meta">registered households</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-label">4Ps Beneficiaries</div>
                <div class="stat-value">{{ $total4Ps }}</div>
                <div class="stat-meta">households</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Senior Citizens</div>
                <div class="stat-value">{{ $totalSeniors }}</div>
                <div class="stat-meta">aged 60 and above</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">PWD</div>
                <div class="stat-value">{{ $totalPwd }}</div>
                <div class="stat-meta">persons w/ disability</div>
            </div>
        </div>

        {{-- FILTERS --}}
        <form method="GET" action="{{ route('admin.residents.index') }}" id="filterForm">
            <div class="filters-bar">
                <div class="filter-group">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" class="filter-input" placeholder="Search by name, barangay..." value="{{ request('search') }}" oninput="debounceSubmit()">
                </div>
                <div class="filter-group">
                    <span class="filter-label">Type</span>
                    <select name="type" class="filter-select" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="head"   {{ request('type') === 'head'   ? 'selected' : '' }}>Household Heads</option>
                        <option value="member" {{ request('type') === 'member' ? 'selected' : '' }}>Members Only</option>
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Sex</span>
                    <select name="sex" class="filter-select" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="Male"   {{ request('sex') === 'Male'   ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ request('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Barangay</span>
                    <select name="barangay" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy }}" {{ request('barangay') === $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Tag</span>
                    <select name="tag" class="filter-select" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="4ps"    {{ request('tag') === '4ps'    ? 'selected' : '' }}>4Ps</option>
                        <option value="pwd"    {{ request('tag') === 'pwd'    ? 'selected' : '' }}>PWD</option>
                        <option value="senior" {{ request('tag') === 'senior' ? 'selected' : '' }}>Senior Citizen</option>
                        <option value="solo"   {{ request('tag') === 'solo'   ? 'selected' : '' }}>Solo Parent</option>
                    </select>
                </div>
                <div class="filter-spacer"></div>
                <div class="filter-count">Showing <strong>{{ $residents->count() }}</strong> of <strong>{{ $residents->total() }}</strong></div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Type</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Barangay</th>
                        <th>Household Head</th>
                        <th>Relationship</th>
                        <th>Tags</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($residents as $i => $person)
                    <tr>
                        <td class="td-num">{{ $residents->firstItem() + $i }}</td>
                        <td>
                            <div class="td-name">{{ $person['name'] }}</div>
                            @if($person['type'] === 'head' && $person['contact_number'])
                                <div class="td-sub">{{ $person['contact_number'] }}</div>
                            @endif
                        </td>
                        <td>
                            @if($person['type'] === 'head')
                                <span class="badge badge-head">Head</span>
                            @else
                                <span class="badge badge-member">Member</span>
                            @endif
                        </td>
                        <td>
                            @if($person['sex'] === 'Male')
                                <span class="badge badge-male">M</span>
                            @elseif($person['sex'] === 'Female')
                                <span class="badge badge-female">F</span>
                            @else
                                <span style="color:var(--gray-400)">—</span>
                            @endif
                        </td>
                        <td class="td-num">{{ $person['age'] ?? '—' }}</td>
                        <td>{{ $person['barangay'] ?? '—' }}</td>
                        <td>
                            @if($person['type'] === 'head')
                                <span style="color:var(--gray-400);font-style:italic;font-size:11px;">—</span>
                            @else
                                {{ $person['household_head'] ?? '—' }}
                            @endif
                        </td>
                        <td>{{ $person['relationship'] ?? ($person['type'] === 'head' ? 'Head' : '—') }}</td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                @if($person['is_4ps'])    <span class="badge badge-4ps">4Ps</span> @endif
                                @if($person['is_pwd'])    <span class="badge badge-pwd">PWD</span> @endif
                                @if($person['is_senior']) <span class="badge badge-senior">Senior</span> @endif
                                @if($person['is_solo'] ?? false) <span class="badge badge-senior">Solo Parent</span> @endif
                                @if(!$person['is_4ps'] && !$person['is_pwd'] && !$person['is_senior'] && !($person['is_solo'] ?? false))
                                    <span style="color:var(--gray-400);font-size:11px;">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="8" r="4"/>
                                        <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                                    </svg>
                                </div>
                                <div class="empty-title">No residents found</div>
                                <div class="empty-sub">Try adjusting your search or filter criteria.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($residents->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing <strong>{{ $residents->firstItem() }}–{{ $residents->lastItem() }}</strong> of <strong>{{ $residents->total() }}</strong> residents
            </div>
            <div class="pagination-btns">
                <button class="pg-btn" {{ $residents->onFirstPage() ? 'disabled' : '' }}
                    onclick="goToPage({{ $residents->currentPage() - 1 }})">
                    &lsaquo;
                </button>
                @foreach($residents->getUrlRange(max(1, $residents->currentPage()-2), min($residents->lastPage(), $residents->currentPage()+2)) as $page => $url)
                    <button class="pg-btn {{ $page == $residents->currentPage() ? 'active' : '' }}"
                        onclick="goToPage({{ $page }})">{{ $page }}</button>
                @endforeach
                <button class="pg-btn" {{ !$residents->hasMorePages() ? 'disabled' : '' }}
                    onclick="goToPage({{ $residents->currentPage() + 1 }})">
                    &rsaquo;
                </button>
            </div>
        </div>
        @endif

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">
            &copy; <span id="footer-year"></span> <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office
        </div>
        <div class="footer-center">Republic of the Philippines</div>
        <a class="fb-link" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
            </svg>
            facebook.com/naicmdrrmo
        </a>
    </footer>

</div>

<script>
    function pad(n){ return String(n).padStart(2,'0'); }
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

    /* ─── Sidebar ─── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    /* ─── Search debounce ─── */
    let searchTimer;
    function debounceSubmit() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 450);
    }

    /* ─── Pagination ─── */
    function goToPage(page) {
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location = url.toString();
    }
</script>
</body>
</html>