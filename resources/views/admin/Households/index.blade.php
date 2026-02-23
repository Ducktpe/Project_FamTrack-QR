<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Household Management</title>
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
            --orange:     #D97706;
            --orange-pale:#FFFBEB;
            --red:        #C0392B;
            --white:      #FFFFFF;
            --gray-50:    #F7F8FA;
            --gray-100:   #F0F2F5;
            --gray-200:   #DEE2E8;
            --gray-400:   #9AA3B0;
            --gray-600:   #5A6372;
            --gray-800:   #2C3340;
            --sidebar-w:  260px;
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
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline {
            font-size: 12px; font-weight: 600;
            color: var(--yellow); letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
        }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.45);
        }
        .status-indicator::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: #4CAF50; box-shadow: 0 0 5px #4CAF50;
            animation: blink 2s infinite;
        }
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

        /* Hamburger — hidden on desktop */
        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            margin-left: -4px;
            border-radius: 4px;
            color: var(--blue-dark);
            flex-shrink: 0;
            transition: background 0.15s;
        }
        .hamburger:hover { background: var(--blue-pale); }
        .hamburger svg { width: 22px; height: 22px; display: block; }

        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400); margin-bottom: 2px;
        }
        .header-title {
            font-family: 'PT Serif', serif;
            font-size: 18px; font-weight: 700; color: var(--blue-dark);
        }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-user-badge {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px;
            background: var(--blue-pale);
            border: 1px solid var(--gray-200); border-radius: 4px;
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--blue);
            display: flex; align-items: center; justify-content: center;
            color: var(--white); font-weight: 700; font-size: 13px;
            flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .user-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 200;
            opacity: 0;
            transition: opacity 0.25s;
        }
        .sidebar-overlay.active { opacity: 1; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex; flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-close {
            display: none;
            position: absolute;
            top: 12px; right: 12px;
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: 4px;
            width: 32px; height: 32px;
            align-items: center; justify-content: center;
            cursor: pointer; z-index: 10;
            color: var(--gray-600);
            transition: background 0.15s;
        }
        .sidebar-close:hover { background: #FEF2F2; color: var(--red); }
        .sidebar-close svg { width: 16px; height: 16px; }

        .nav-section-label {
            padding: 18px 20px 8px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: var(--gray-400);
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px;
            font-size: 13.5px; font-weight: 500;
            color: var(--gray-600); text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
        }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; color: inherit; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .nav-badge {
            margin-left: auto;
            background: var(--red); color: var(--white);
            font-size: 9px; font-weight: 700;
            padding: 2px 7px; border-radius: 10px;
        }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .sidebar-bottom {
            margin-top: auto; padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
        }
        .logout-btn {
            width: 100%;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            background: var(--blue); color: var(--white);
            border: none; padding: 10px 16px; border-radius: 4px;
            cursor: pointer; display: flex; align-items: center;
            justify-content: center; gap: 8px; transition: background 0.15s;
        }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content {
            grid-area: main;
            background: var(--gray-50);
            overflow-y: auto;
            padding: 28px 32px;
        }

        /* Page title bar */
        .page-titlebar {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
            gap: 12px;
        }
        .page-breadcrumb {
            font-size: 11px; color: var(--gray-400);
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;
        }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 {
            font-family: 'PT Serif', serif;
            font-size: 22px; font-weight: 700; color: var(--blue-dark);
        }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .back-btn {
            display: flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 600;
            color: var(--blue); text-decoration: none;
            padding: 8px 16px;
            border: 1px solid var(--gray-200);
            background: var(--white); border-radius: 4px;
            transition: background 0.15s;
            white-space: nowrap; flex-shrink: 0;
        }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        /* Alerts */
        .alert-success {
            background: var(--green-pale);
            border: 1px solid #BBF7D0; border-left: 4px solid var(--green);
            padding: 12px 16px; margin-bottom: 16px;
            font-size: 13px; color: var(--green-dark);
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success svg { width: 16px; height: 16px; flex-shrink: 0; }
        .alert-danger {
            background: #FEF2F2;
            border: 1px solid #FECACA; border-left: 4px solid var(--red);
            padding: 12px 16px; margin-bottom: 16px;
            font-size: 13px; color: var(--red);
        }

        /* Stats row */
        .stats-row {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 16px; margin-bottom: 20px;
        }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            padding: 20px 24px;
            display: flex; align-items: center; gap: 16px;
        }
        .stat-card.pending { border-top: 3px solid var(--orange); }
        .stat-card.approved { border-top: 3px solid var(--green); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-card.pending .stat-icon { background: var(--orange-pale); }
        .stat-card.approved .stat-icon { background: var(--green-pale); }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-card.pending .stat-icon svg { color: var(--orange); }
        .stat-card.approved .stat-icon svg { color: var(--green); }
        .stat-number {
            font-family: 'PT Serif', serif;
            font-size: 34px; font-weight: 700; line-height: 1;
            margin-bottom: 3px;
        }
        .stat-card.pending .stat-number { color: var(--orange); }
        .stat-card.approved .stat-number { color: var(--green); }
        .stat-label {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400);
        }

        /* Tabs */
        .tabs-row {
            display: flex; gap: 0;
            border-bottom: 2px solid var(--gray-200);
            margin-bottom: 20px;
            overflow-x: auto;
        }
        .tab-link {
            padding: 10px 20px;
            font-size: 13px; font-weight: 500;
            color: var(--gray-600); text-decoration: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            display: flex; align-items: center; gap: 7px;
            transition: color 0.12s;
            white-space: nowrap;
        }
        .tab-link:hover { color: var(--blue); }
        .tab-link.active {
            color: var(--blue); font-weight: 700;
            border-bottom-color: var(--blue);
        }
        .tab-count {
            background: var(--red); color: var(--white);
            font-size: 9px; font-weight: 700;
            padding: 2px 7px; border-radius: 10px;
        }

        /* Table container */
        .table-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .table-card-header {
            padding: 13px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; gap: 10px;
        }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .table-section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }

        /* Scrollable table wrapper on mobile */
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        thead th {
            padding: 11px 14px;
            background: var(--blue); color: var(--white);
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            text-align: left;
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:hover { background: var(--gray-50); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 12px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        tbody tr:nth-child(even) td { background: var(--gray-50); }
        tbody tr:nth-child(even):hover td { background: var(--blue-pale); }

        .td-name strong { display: block; font-size: 13px; color: var(--blue-dark); }
        .td-name small { font-size: 11px; color: var(--gray-400); margin-top: 2px; display: block; }
        .td-address small { font-size: 11px; color: var(--gray-400); display: block; margin-top: 1px; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 10px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .badge-approved { background: var(--green-pale); color: var(--green-dark); }
        .badge-pending  { background: var(--orange-pale); color: #92400E; }
        .badge svg { width: 10px; height: 10px; }

        .serial-code { font-size: 12px; font-weight: 700; color: var(--blue); font-family: monospace; letter-spacing: 0.5px; }
        .serial-none { font-size: 11px; color: var(--gray-400); font-style: italic; }

        /* Action buttons */
        .btn-view {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px;
            background: var(--blue); color: var(--white);
            font-size: 11px; font-weight: 600;
            text-decoration: none; border-radius: 3px;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-view:hover { background: var(--blue-dark); }
        .btn-view svg { width: 12px; height: 12px; }

        .btn-approve {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px;
            background: var(--green); color: var(--white);
            font-size: 11px; font-weight: 600;
            border: none; border-radius: 3px; cursor: pointer;
            font-family: 'Open Sans', sans-serif;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-approve:hover { background: var(--green-dark); }
        .btn-approve svg { width: 12px; height: 12px; }

        td:last-child { white-space: nowrap; }
        .actions-cell { display: flex; align-items: center; gap: 6px; }
        .actions-cell form { margin: 0; padding: 0; }

        /* Empty state */
        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--gray-100);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .empty-icon svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* Pagination */
        .pagination-row { padding: 14px 20px; border-top: 1px solid var(--gray-100); background: var(--gray-50); }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 24px; z-index: 100;
            gap: 8px;
        }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.4);
            text-decoration: none; transition: color 0.15s;
            white-space: nowrap;
        }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        /* ════════════════════════════════════════
           RESPONSIVE
           ════════════════════════════════════════ */
        @media (max-width: 900px) {
            .shell {
                grid-template-rows: 36px auto 1fr 48px;
                grid-template-columns: 1fr;
                grid-template-areas:
                    "topbar"
                    "header"
                    "main"
                    "footer";
                height: 100vh;
                overflow: hidden;
            }
            .sidebar {
                grid-area: unset;
                position: fixed;
                top: 0; left: 0; bottom: 0;
                width: var(--sidebar-w);
                z-index: 300;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }

            .hamburger { display: flex; }

            header { padding: 0 16px; gap: 10px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .header-user-badge { padding: 6px 10px; gap: 8px; }
            .user-name { font-size: 12px; }
            .user-role { display: none; }

            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }

            .main-content { padding: 20px 16px; }

            .stats-row { gap: 10px; }
            .stat-card { padding: 14px 16px; gap: 12px; }
            .stat-number { font-size: 26px; }
        }

        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline { display: none; }
            .status-indicator { display: none; }

            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider { display: none; }
            .header-logos img:last-child { display: none; }
            .header-org { display: none; }
            .header-title { font-size: 13px; line-height: 1.3; }
            .header-user-badge { padding: 5px 8px; }
            .user-avatar { width: 28px; height: 28px; font-size: 11px; }
            .user-name { font-size: 11px; }

            .main-content { padding: 16px 12px; }

            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .back-btn { align-self: flex-start; }

            .stats-row { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card { padding: 12px 14px; gap: 10px; }
            .stat-icon { width: 36px; height: 36px; }
            .stat-icon svg { width: 18px; height: 18px; }
            .stat-number { font-size: 22px; }
            .stat-label { font-size: 10px; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }
    </style>
</head>
<body>
<div class="shell">

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- TOP UTILITY BAR -->
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
        <div class="header-user-badge">
            <div class="user-avatar">A</div>
            <div>
                <div class="user-name">Administrator</div>
                <div class="user-role">Full Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="nav-section-label">Admin Menu</div>

        <!-- Dashboard Overview -->
        <a href="{{ route('admin.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard Overview
        </a>

        <!-- Distribution Events -->
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Distribution Events
        </a>

        <!-- Distribution Logs -->
        <a href="#" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
        </a>

        <!-- List of Residents -->
        <a href="#" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            List of Residents
        </a>

        <!-- List of Households (active) -->
        <a href="{{ route('admin.households.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Households
            @if($pendingCount > 0)
                <span class="nav-badge">{{ $pendingCount }}</span>
            @endif
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

        <!-- Page title -->
        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">Admin / <span>Household Management</span></div>
                <div class="page-h1">Household Management</div>
                <div class="page-sub">Review, approve, and manage registered household profiles — RBI Framework</div>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to Dashboard
            </a>
        </div>

        {{-- Session alerts --}}
        @if(session('success'))
            <div class="alert-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-card pending">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $pendingCount }}</div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $approvedCount }}</div>
                    <div class="stat-label">Approved Households</div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="tabs-row">
            <a href="{{ route('admin.households.index', ['filter' => 'all']) }}"
               class="tab-link {{ $filter === 'all' ? 'active' : '' }}">
                All Households
            </a>
            <a href="{{ route('admin.households.index', ['filter' => 'pending']) }}"
               class="tab-link {{ $filter === 'pending' ? 'active' : '' }}">
                Pending
                @if($pendingCount > 0)
                    <span class="tab-count">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.households.index', ['filter' => 'approved']) }}"
               class="tab-link {{ $filter === 'approved' ? 'active' : '' }}">
                Approved
            </a>
        </div>

        {{-- Table --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="ca-dot"></div>
                <div class="table-section-title">
                    {{ $filter === 'pending' ? 'Pending Approval' : ($filter === 'approved' ? 'Approved Households' : 'All Households') }}
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Household Head</th>
                            <th>Address</th>
                            <th>Members</th>
                            <th>Encoded By</th>
                            <th>Status</th>
                            <th>Serial Code</th>
                            <th>Date Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($households as $household)
                            <tr>
                                <td class="td-name">
                                    <strong>{{ $household->household_head_name }}</strong>
                                    <small>{{ $household->sex }}, {{ $household->age }} years old</small>
                                </td>
                                <td class="td-address">
                                    {{ $household->street_purok }}, {{ $household->barangay }}
                                    <small>{{ $household->municipality }}, {{ $household->province }}</small>
                                </td>
                                <td>{{ $household->total_members }} person(s)</td>
                                <td>{{ $household->encoder->name }}</td>
                                <td>
                                    @if($household->isApproved())
                                        <span class="badge badge-approved">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            Approved
                                        </span>
                                    @else
                                        <span class="badge badge-pending">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($household->serial_code)
                                        <span class="serial-code">{{ $household->serial_code }}</span>
                                    @else
                                        <span class="serial-none">Not assigned</span>
                                    @endif
                                </td>
                                <td>{{ $household->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="{{ route('admin.households.show', $household) }}" class="btn-view">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            View
                                        </a>
                                        @if(!$household->isApproved())
                                            <form method="POST" action="{{ route('admin.households.approve', $household) }}">
                                                @csrf
                                                <button type="submit" class="btn-approve"
                                                    onclick="return confirm('Approve this household and generate QR serial code?')">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                                <path d="M9 22V12h6v10"/>
                                            </svg>
                                        </div>
                                        <div class="empty-title">No households found</div>
                                        <div class="empty-sub">No records match the selected filter.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-row">
                {{ $households->appends(['filter' => $filter])->links() }}
            </div>
        </div>

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
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent =
            pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent =
            days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    // ─── Sidebar ───
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebar();
    });
</script>
</body>
</html>