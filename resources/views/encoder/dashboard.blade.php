<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Encoder Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue:       #1B3F7A;
            --blue-dark:  #122D5A;
            --blue-light: #2459A8;
            --blue-pale:  #EAF0FA;
            --yellow:     #F5C518;
            --yellow-dark:#D4A800;
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
            color: rgba(255,255,255,0.5);
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
        .header-logos {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .header-logos img {
            height: 54px; width: 54px;
            object-fit: contain;
        }
        .logo-divider {
            width: 1px; height: 44px;
            background: var(--gray-200);
        }
        .header-text { margin-left: 4px; }
        .header-org {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400); margin-bottom: 2px;
        }
        .header-title {
            font-family: 'PT Serif', serif;
            font-size: 18px; font-weight: 700;
            color: var(--blue-dark); line-height: 1.2;
        }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }

        .header-spacer { flex: 1; }

        .header-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background: var(--blue-pale);
            border: 1px solid var(--gray-200);
            border-radius: 4px;
        }
        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--blue);
            display: flex; align-items: center; justify-content: center;
            color: var(--white);
            font-weight: 700; font-size: 13px;
        }
        .user-name {
            font-size: 13px; font-weight: 600;
            color: var(--blue-dark); line-height: 1.2;
        }
        .user-role {
            font-size: 10px; color: var(--gray-600);
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .nav-section-label {
            padding: 18px 20px 8px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: var(--gray-400);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            font-size: 13.5px; font-weight: 500;
            color: var(--gray-600);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
            cursor: pointer;
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
            flex-shrink: 0; color: inherit; opacity: 0.7;
        }
        .nav-item.active .nav-icon,
        .nav-item:hover .nav-icon { opacity: 1; }

        .nav-badge {
            margin-left: auto;
            background: var(--blue);
            color: var(--white);
            font-size: 9px; font-weight: 700;
            padding: 2px 8px; border-radius: 10px;
            letter-spacing: 0.5px;
        }

        .nav-badge-warn {
            margin-left: auto;
            background: #D97706;
            color: var(--white);
            font-size: 9px; font-weight: 700;
            padding: 2px 8px; border-radius: 10px;
            letter-spacing: 0.5px;
        }

        .sidebar-sep {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 8px 0;
        }

        /* Role notice box */
        .role-notice {
            margin: 12px 14px;
            background: #FFFAE6;
            border: 1px solid #F5C518;
            border-left: 3px solid #D4A800;
            padding: 10px 12px;
            border-radius: 2px;
        }
        .role-notice-title {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: #92400E; margin-bottom: 3px;
        }
        .role-notice-text {
            font-size: 11px;
            color: #78350F;
            line-height: 1.5;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
        }
        .logout-btn {
            width: 100%;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            background: var(--blue);
            color: var(--white);
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
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
        }
        .page-breadcrumb {
            font-size: 11px; color: var(--gray-400);
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 {
            font-family: 'PT Serif', serif;
            font-size: 22px; font-weight: 700;
            color: var(--blue-dark);
        }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; }
        .page-date strong {
            display: block; font-size: 13px;
            font-weight: 600; color: var(--gray-800);
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
            object-fit: contain; flex-shrink: 0;
        }
        .welcome-label {
            font-size: 11px; text-transform: uppercase;
            letter-spacing: 1.5px; color: rgba(255,255,255,0.55);
            margin-bottom: 4px;
        }
        .welcome-heading {
            font-family: 'PT Serif', serif;
            font-size: 20px; font-weight: 700; color: var(--white);
        }
        .welcome-heading em { color: var(--yellow); font-style: normal; }
        .welcome-desc { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; }

        /* Quick action cards */
        .quick-nav {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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
        }
        .qnav-icon svg { width: 17px; height: 17px; color: var(--blue); }
        .qnav-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .qnav-desc { font-size: 11px; color: var(--gray-600); }

        /* Info notice */
        .access-notice {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-left: 4px solid var(--blue);
            padding: 14px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .access-notice svg { width: 18px; height: 18px; color: var(--blue); flex-shrink: 0; margin-top: 1px; }
        .access-notice-text { font-size: 12px; color: var(--gray-600); line-height: 1.6; }
        .access-notice-text strong { color: var(--blue-dark); }

        /* Content area */
        .content-area {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .content-area-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; gap: 10px;
        }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); }
        .ca-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .content-placeholder-body { padding: 56px 40px; text-align: center; }
        .ph-icon-wrap {
            width: 48px; height: 48px;
            background: var(--gray-100); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .ph-icon-wrap svg { width: 22px; height: 22px; color: var(--gray-400); }
        .ph-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .ph-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
        }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.4);
            text-decoration: none; transition: color 0.15s;
        }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }
    </style>
</head>
<body>
<div class="shell">

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
            <div class="user-avatar">E</div>
            <div>
                <div class="user-name">Encoder</div>
                <div class="user-role">Data Entry Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="nav-section-label">Encoder Menu</div>

        <a href="#" class="nav-item active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
            <span class="nav-badge">Live</span>
        </a>

        <a href="#" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Register Family Profile
        </a>

        <a href="{{ route('encoder.households.index') }}" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Household Management
        </a>

        <a href="#" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            View Submitted Records
        </a>

        <hr class="sidebar-sep">
        <div class="nav-section-label">DSWD Integration</div>

        <a href="#" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
            Listahanan Cross-Reference
        </a>

        <a href="#" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Flag Duplicate Records
            <span class="nav-badge-warn">Action</span>
        </a>

        <div class="role-notice">
            <div class="role-notice-title">&#9432; Encoder Access</div>
            <div class="role-notice-text">You can create and update family profiles. QR code generation and distribution logs are managed by the Admin.</div>
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
                <div class="page-breadcrumb">Home / <span>Encoder Dashboard</span></div>
                <div class="page-h1">Encoder Dashboard</div>
                <div class="page-sub">Barangay Family Track QR System — Data Entry &amp; Family Profiling</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <div class="welcome-card">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO">
            <div>
                <div class="welcome-label">Welcome Back</div>
                <div class="welcome-heading">Good day, <em>Encoder!</em></div>
                <div class="welcome-desc">You are logged in as a Data Encoder. Register family profiles, manage household members, and cross-reference with DSWD Listahanan.</div>
            </div>
        </div>

        <div class="access-notice">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="access-notice-text">
                <strong>Encoder Role — Data Entry Access.</strong> You may create and update family profiles following the RBI form, add or edit household members, and cross-reference entries with DSWD Listahanan. Submitted records will be reviewed and approved by the Barangay Admin before QR codes are generated. You do not have access to distribution logs or QR code management.
            </div>
        </div>

        <div class="quick-nav">
            <a href="#" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                </div>
                <div class="qnav-title">Register Family Profile</div>
                <div class="qnav-desc">Add a new household following the RBI form fields</div>
            </a>
            <a href="#" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div class="qnav-title">Household Management</div>
                <div class="qnav-desc">Add, edit, or archive members within a household</div>
            </a>
            <a href="#" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <div class="qnav-title">Listahanan Cross-Reference</div>
                <div class="qnav-desc">Tag and verify families against DSWD Listahanan records</div>
            </a>
        </div>

        <div class="content-area">
            <div class="content-area-header">
                <div class="ca-dot"></div>
                <div class="ca-title">Content Area</div>
            </div>
            <div class="content-placeholder-body">
                <div class="ph-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
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
</script>
</body>
</html>