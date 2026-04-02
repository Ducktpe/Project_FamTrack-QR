{{-- resources/views/auditor/distribution/events-list.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Distribution Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue:          #1B3F7A;
            --blue-dark:     #122D5A;
            --blue-light:    #2459A8;
            --blue-pale:     #EAF0FA;
            --yellow:        #F5C518;
            --yellow-dark:   #D4A800;
            --white:         #FFFFFF;
            --gray-50:       #F7F8FA;
            --gray-100:      #F0F2F5;
            --gray-200:      #DEE2E8;
            --gray-400:      #9AA3B0;
            --gray-600:      #5A6372;
            --gray-800:      #2C3340;
            --red:           #C0392B;
            --red-pale:      #FEF2F2;
            --green:         #16A34A;
            --green-pale:    #DCFCE7;
            --orange:        #D97706;
            --orange-pale:   #FFFBEB;
            --purple:        #5B3FA6;
            --purple-dark:   #3D1F8A;
            --purple-pale:   #F5F0FF;
            --purple-border: #D8CBF5;
            --sky:           #0EA5E9;
            --sky-dark:      #0369A1;
            --sky-pale:      #F0F9FF;
            --sky-border:    #BAE6FD;
            --sidebar-w:     260px;
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
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
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
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; margin-left: -4px; border-radius: 4px; color: var(--blue-dark); flex-shrink: 0; transition: background 0.15s; }
        .hamburger:hover { background: var(--blue-pale); }
        .hamburger svg { width: 22px; height: 22px; display: block; }
        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .readonly-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 3px; font-size: 11px; font-weight: 700; color: #92400E; text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; }
        .readonly-badge svg { width: 12px; height: 12px; }
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--sky-pale); border: 1px solid var(--sky-border); border-radius: 4px; flex-shrink: 0; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--sky); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--sky-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: #0284C7; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; opacity: 1; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-left: 3px solid transparent; transition: background 0.12s, color 0.12s, border-color 0.12s; cursor: pointer; }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; color: inherit; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .nav-badge-view { margin-left: auto; background: var(--gray-400); color: var(--white); font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 10px; letter-spacing: 0.5px; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .role-notice { margin: 12px 14px; background: var(--purple-pale); border: 1px solid var(--purple-border); border-left: 3px solid var(--purple); padding: 10px 12px; border-radius: 2px; }
        .role-notice-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--purple-dark); margin-bottom: 3px; }
        .role-notice-text { font-size: 11px; color: #4B3080; line-height: 1.5; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn { width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white); border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--gray-400); text-decoration: none; }
        .page-breadcrumb a:hover { color: var(--blue-light); }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── EVENTS GRID ─── */
        .events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }

        .event-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            border-radius: 4px;
            padding: 20px;
            cursor: pointer;
            transition: box-shadow 0.2s, border-top-color 0.2s, transform 0.2s;
        }
        .event-card:hover { box-shadow: 0 4px 16px rgba(27,63,122,0.12); border-top-color: var(--yellow); transform: translateY(-2px); }

        .event-name { font-family: 'PT Serif', serif; font-size: 15px; font-weight: 700; color: var(--blue-dark); margin-bottom: 6px; }
        .event-date-line { font-size: 12px; color: var(--gray-600); margin-bottom: 12px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

        .event-status { display: inline-flex; align-items: center; gap: 4px; padding: 2px 9px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .event-status.ongoing   { background: var(--green-pale); color: var(--green); }
        .event-status.upcoming  { background: var(--blue-pale);  color: var(--blue); }
        .event-status.completed { background: var(--gray-100);   color: var(--gray-600); }
        .event-status.cancelled { background: var(--red-pale);   color: var(--red); }

        .event-stats { display: flex; gap: 16px; border-top: 1px solid var(--gray-100); padding-top: 12px; margin-top: 4px; }
        .stat { flex: 1; }
        .stat-number { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--blue); line-height: 1.1; }
        .stat-label { font-size: 10px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

        .event-action { margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--gray-100); }
        .view-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; transition: color 0.15s; }
        .view-btn:hover { color: var(--blue-light); text-decoration: underline; }
        .view-btn svg { width: 13px; height: 13px; }

        /* Empty state */
        .empty-state { text-align: center; padding: 56px 40px; background: var(--white); border: 1px solid var(--gray-200); border-radius: 4px; }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── FOOTER ─── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
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
            .header-user-badge { padding: 6px 10px; gap: 8px; }
            .user-name { font-size: 12px; }
            .user-role { display: none; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .main-content { padding: 20px 16px; }
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
            .page-date { text-align: left; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            .events-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 420px) {
            .events-grid { grid-template-columns: 1fr; gap: 12px; }
            .main-content { padding: 12px 10px; }
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
        <button class="hamburger" id="hamburgerBtn" aria-label="Open navigation">
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
        <span class="readonly-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Read-Only Access
        </span>
        <div class="header-user-badge">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">View-Only Access</div>
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

        <div class="nav-section-label">Auditor Menu</div>

        <a href="{{ route('auditor.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <hr class="sidebar-sep">
        <div class="nav-section-label">View-Only Access</div>

        <a href="{{ route('auditor.family-profiles') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Family Profiles
            <span class="nav-badge-view">View</span>
        </a>

        <a href="{{ route('auditor.distribution.logs') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
            <span class="nav-badge-view">View</span>
        </a>

        <a href="{{ route('auditor.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Households
            <span class="nav-badge-view">View</span>
        </a>

        <a href="{{ route('auditor.audit.trail') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
                <path d="M15.54 8.46a5 5 0 010 7.07M8.46 8.46a5 5 0 000 7.07"/>
            </svg>
            Audit Trail Logs
            <span class="nav-badge-view">View</span>
        </a>

        <div class="role-notice">
            <div class="role-notice-title">&#9432; Read-Only Access</div>
            <div class="role-notice-text">You have view-only access. No records can be added, edited, or deleted. Access may be time-limited by the Administrator.</div>
        </div>

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
                    <a href="{{ route('auditor.dashboard') }}">Home</a> / <span>Distribution Events</span>
                </div>
                <div class="page-h1">Distribution Events</div>
                <div class="page-sub">View all relief distribution events — Read-Only</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        @if($events->isEmpty())
            <div class="empty-state">
                <div class="empty-title">No distribution events found.</div>
                <div class="empty-sub">No events have been recorded yet.</div>
            </div>
        @else
            <div class="events-grid">
                @foreach($events as $event)
                    @php $s = strtolower($event->status); @endphp
                    <div class="event-card">
                        <div class="event-name">{{ $event->event_name }}</div>
                        <div class="event-date-line">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--gray-400);flex-shrink:0;">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
                            &nbsp;
                            <span class="event-status {{ $s }}">{{ ucfirst($s) }}</span>
                        </div>
                        <div class="event-stats">
                            <div class="stat">
                                <div class="stat-number">{{ $event->total_distributed }}</div>
                                <div class="stat-label">Total Distributed</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">{{ $event->unique_households }}</div>
                                <div class="stat-label">Households</div>
                            </div>
                        </div>
                        <div class="event-action">
                            <a href="{{ route('auditor.distribution.events.households', $event) }}" class="view-btn">
                                View Households
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
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
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            facebook.com/naicmdrrmo
        </a>
    </footer>

</div>

<script>
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now    = new Date();
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

    // ─── Sidebar ───
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('hamburgerBtn').addEventListener('click', function() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>
</body>
</html>