<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic, Cavite — Admin Dashboard</title>
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
            --yellow-pale:#FFFAE6;
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

        /* ─── Layout ─── */
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
        }
        .topbar-left {
            font-size: 11px;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.3px;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .clock-inline {
            font-size: 12px;
            font-weight: 600;
            color: var(--yellow);
            letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
        }
        .clock-date-inline {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }
        .status-indicator::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #4CAF50;
            box-shadow: 0 0 5px #4CAF50;
            animation: blink 2s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ─── HEADER ─── */
        header {
            grid-area: header;
            background: var(--white);
            border-bottom: 3px solid var(--yellow);
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 14px;
            z-index: 90;
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

        .header-logos {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .header-logos img {
            height: 54px;
            width: 54px;
            object-fit: contain;
        }
        .logo-divider {
            width: 1px;
            height: 44px;
            background: var(--gray-200);
        }
        .header-text { margin-left: 4px; }
        .header-org {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray-400);
            margin-bottom: 2px;
        }
        .header-title {
            font-family: 'PT Serif', serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-dark);
            line-height: 1.2;
        }
        .header-sub {
            font-size: 11px;
            color: var(--gray-600);
            margin-top: 2px;
        }
        .header-spacer { flex: 1; }
        .header-admin-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background: var(--blue-pale);
            border: 1px solid var(--gray-200);
            border-radius: 4px;
        }
        .admin-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--blue);
            display: flex; align-items: center; justify-content: center;
            color: var(--white);
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }
        .admin-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-dark);
            line-height: 1.2;
        }
        .admin-role {
            font-size: 10px;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

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
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Close button — only shown on mobile */
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
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
        .sidebar-close svg { width: 16px; height: 16px; }

        .nav-section-label {
            padding: 18px 20px 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gray-400);
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--gray-600);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
        }
        .nav-item:hover {
            background: var(--gray-50);
            color: var(--blue);
            border-left-color: var(--blue-light);
        }
        .nav-item.active {
            background: var(--blue-pale);
            color: var(--blue);
            border-left-color: var(--blue);
            font-weight: 600;
        }
        .nav-icon {
            width: 17px; height: 17px;
            flex-shrink: 0;
            color: inherit;
            opacity: 0.7;
        }
        .nav-item.active .nav-icon,
        .nav-item:hover .nav-icon { opacity: 1; }
        .nav-badge {
            margin-left: auto;
            background: var(--blue);
            color: var(--white);
            font-size: 9px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            letter-spacing: 0.5px;
        }
        .sidebar-sep {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 8px 0;
        }
        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
        }
        .logout-btn {
            width: 100%;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: var(--blue);
            color: var(--white);
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s;
        }
        .logout-btn:hover { background: #C0392B; }

        /* ─── MAIN ─── */
        .main-content {
            grid-area: main;
            background: var(--gray-50);
            overflow-y: auto;
            padding: 28px 32px;
        }

        .page-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
            gap: 12px;
        }
        .page-breadcrumb {
            font-size: 11px;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 {
            font-family: 'PT Serif', serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--blue-dark);
        }
        .page-sub {
            font-size: 12px;
            color: var(--gray-600);
            margin-top: 3px;
        }
        .page-date {
            font-size: 12px;
            color: var(--gray-600);
            text-align: right;
            flex-shrink: 0;
        }
        .page-date strong {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-800);
            white-space: nowrap;
        }

        /* Welcome card */
        .welcome-card {
            background: var(--blue);
            border-left: 5px solid var(--yellow);
            padding: 22px 28px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }
        .welcome-card img {
            width: 50px; height: 50px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .welcome-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.55);
            margin-bottom: 4px;
        }
        .welcome-heading {
            font-family: 'PT Serif', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--white);
        }
        .welcome-heading em {
            color: var(--yellow);
            font-style: normal;
        }
        .welcome-desc {
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            margin-top: 4px;
        }

        /* Quick nav cards */
        .quick-nav {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .qnav-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            padding: 18px 20px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: box-shadow 0.15s, border-top-color 0.15s;
        }
        .qnav-card:hover {
            box-shadow: 0 3px 12px rgba(27,63,122,0.12);
            border-top-color: var(--yellow);
        }
        .qnav-icon {
            width: 32px; height: 32px;
            background: var(--blue-pale);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .qnav-icon svg { width: 17px; height: 17px; color: var(--blue); }
        .qnav-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-dark);
        }
        .qnav-desc {
            font-size: 11px;
            color: var(--gray-600);
        }

        /* Content area */
        .content-area {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .content-area-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ca-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-dark);
        }
        .ca-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--yellow);
            border: 2px solid var(--yellow-dark);
            flex-shrink: 0;
        }
        .content-placeholder-body {
            padding: 56px 40px;
            text-align: center;
        }
        .ph-icon-wrap {
            width: 48px; height: 48px;
            background: var(--gray-100);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .ph-icon-wrap svg { width: 22px; height: 22px; color: var(--gray-400); }
        .ph-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 5px;
        }
        .ph-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            gap: 8px;
            z-index: 100;
        }
        .footer-left {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }
        .footer-left strong { color: rgba(255,255,255,0.75); }
        .footer-center {
            font-size: 10px;
            color: rgba(255,255,255,0.25);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .fb-link {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            transition: color 0.15s;
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
            .header-admin-badge { padding: 6px 10px; gap: 8px; }
            .admin-name { font-size: 12px; }
            .admin-role { display: none; }

            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }

            .main-content { padding: 20px 16px; }
            .quick-nav { grid-template-columns: repeat(2, 1fr); }
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
            .header-admin-badge { padding: 5px 8px; }
            .admin-avatar { width: 28px; height: 28px; font-size: 11px; }
            .admin-name { font-size: 11px; }

            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }

            .welcome-card { padding: 16px 18px; gap: 14px; }
            .welcome-card img { width: 38px; height: 38px; }
            .welcome-heading { font-size: 16px; }
            .welcome-desc { display: none; }

            .quick-nav { grid-template-columns: 1fr 1fr; gap: 10px; }
            .qnav-card { padding: 14px; gap: 8px; }
            .qnav-title { font-size: 12px; }
            .qnav-desc { display: none; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        @media (max-width: 380px) {
            .quick-nav { grid-template-columns: 1fr; }
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
            <img src="{{ asset('/images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
            <div class="logo-divider"></div>
            <img src="{{ asset('/images/naic-seal.png') }}" alt="Bayan ng Naic Seal">
        </div>
        <div class="header-text">
            <div class="header-org">Office of the Municipal DRRMO</div>
            <div class="header-title">MDRRMO — Naic, Cavite</div>
            <div class="header-sub">Municipal Disaster Risk Reduction and Management Office</div>
        </div>
        <div class="header-spacer"></div>
        <div class="header-admin-badge">
            <div class="admin-avatar">A</div>
            <div class="admin-info">
                <div class="admin-name">Administrator</div>
                <div class="admin-role">Full Access</div>
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
        <a href="{{ route('admin.dashboard') }}" class="nav-item active" onclick="closeSidebar()">
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

        <!-- List of Households -->
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
                <div class="page-breadcrumb">Home / <span>Dashboard</span></div>
                <div class="page-h1">Dashboard Overview</div>
                <div class="page-sub">Barangay Family Track QR Relief Distribution System — MDRRMO Naic, Cavite</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <div class="welcome-card">
            <img src="{{ asset('/images/mdrrmo-logo.png') }}" alt="MDRRMO">
            <div>
                <div class="welcome-label">Welcome Back</div>
                <div class="welcome-heading">Good day, <em>Admin!</em></div>
                <div class="welcome-desc">Office of the Municipal Disaster Risk Reduction and Management Officer — Naic, Cavite</div>
            </div>
        </div>

        <div class="quick-nav">
            <!-- Distribution Events -->
            <a href="{{ route('admin.events.quick-create') }}" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="qnav-title">Distribution Events</div>
                <div class="qnav-desc">Create &amp; manage relief events</div>
            </a>
            <!-- Distribution Logs -->
            <a href="#" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <div class="qnav-title">Distribution Logs</div>
                <div class="qnav-desc">Track all relief distributions</div>
            </a>
            <!-- List of Residents -->
            <a href="#" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                    </svg>
                </div>
                <div class="qnav-title">List of Residents</div>
                <div class="qnav-desc">Manage resident profiles</div>
            </a>
            <!-- List of Households -->
            <a href="{{ route('admin.households.index') }}" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
                </div>
                <div class="qnav-title">List of Households</div>
                <div class="qnav-desc">View &amp; manage households</div>
            </a>
        </div>

        <div class="content-area">
            <div class="content-area-header">
                <div class="ca-dot"></div>
                <div class="ca-title">Dashboard Content Area</div>
            </div>
            <div class="content-placeholder-body">
                <div class="ph-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </div>
                <div class="ph-title">No content to display</div>
                <div class="ph-sub">Select a module from the sidebar or the quick links above to load content here.</div>
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
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        document.getElementById('top-time').textContent =
            pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent =
            days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        document.getElementById('main-date').textContent =
            days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
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