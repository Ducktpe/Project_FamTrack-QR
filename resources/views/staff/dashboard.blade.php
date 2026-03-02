<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Distribution Staff Dashboard</title>
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
        }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite;
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
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-user-badge {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px; background: var(--green-pale);
            border: 1px solid #BBF7D0; border-radius: 4px; flex-shrink: 0;
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--green); display: flex; align-items: center; justify-content: center;
            color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: var(--green-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--green); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none !important; /* Force hide until activated */
            position: fixed; 
            inset: 0;
            background: rgba(0,0,0,0.45); 
            z-index: 200;
            opacity: 0; 
            transition: opacity 0.25s;
            pointer-events: none; /* Don't block clicks when hidden */
        }
        .sidebar-overlay.active {
            display: block !important;
            pointer-events: auto; /* Allow clicks when active */
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar; background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex; flex-direction: column;
            overflow-y: auto; position: relative;
        }
        .sidebar-close {
            display: none; position: absolute; top: 12px; right: 12px;
            background: var(--gray-100); border: 1px solid var(--gray-200);
            border-radius: 4px; width: 32px; height: 32px;
            align-items: center; justify-content: center;
            cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s;
        }
        .sidebar-close:hover { background: #FEF2F2; color: var(--red); }
        .sidebar-close svg { width: 16px; height: 16px; }

        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px; font-size: 13.5px; font-weight: 500;
            color: var(--gray-600); text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s; cursor: pointer;
        }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-item.scanner-primary { background: var(--green-pale); color: var(--green-dark); border-left-color: var(--green); font-weight: 600; }
        .nav-item.scanner-primary:hover { background: #BBF7D0; color: var(--green-dark); border-left-color: var(--green-dark); }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; color: inherit; opacity: 0.8; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon, .nav-item.scanner-primary .nav-icon { opacity: 1; }
        .nav-badge-scan { margin-left: auto; background: var(--green); color: var(--white); font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 10px; letter-spacing: 0.5px; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }

        .role-notice { margin: 12px 14px; background: var(--green-pale); border: 1px solid #BBF7D0; border-left: 3px solid var(--green); padding: 10px 12px; border-radius: 2px; }
        .role-notice-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--green-dark); margin-bottom: 3px; }
        .role-notice-text { font-size: 11px; color: #166534; line-height: 1.5; }

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
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* Welcome card */
        .welcome-card { background: var(--blue); border-left: 5px solid var(--yellow); padding: 22px 28px; display: flex; align-items: center; gap: 20px; margin-bottom: 24px; }
        .welcome-card img { width: 50px; height: 50px; object-fit: contain; flex-shrink: 0; }
        .welcome-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.55); margin-bottom: 4px; }
        .welcome-heading { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--white); }
        .welcome-heading em { color: var(--yellow); font-style: normal; }
        .welcome-desc { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; }

        /* PRIMARY SCANNER CTA */
        .scanner-cta {
            background: var(--white); border: 1px solid var(--gray-200);
            border-top: 4px solid var(--green); padding: 32px 28px;
            display: flex; align-items: center; gap: 28px;
            margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); flex-wrap: wrap;
        }
        .scanner-cta-icon { width: 64px; height: 64px; background: var(--green-pale); border: 2px solid #BBF7D0; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .scanner-cta-icon svg { width: 32px; height: 32px; color: var(--green); }
        .scanner-cta-content { flex: 1; min-width: 0; }
        .scanner-cta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--green); margin-bottom: 4px; }
        .scanner-cta-title { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--blue-dark); margin-bottom: 6px; }
        .scanner-cta-desc { font-size: 12px; color: var(--gray-600); line-height: 1.6; }
        .scanner-cta-btn {
            flex-shrink: 0; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px;
            background: var(--green); 
            color: var(--white); 
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            text-decoration: none; 
            padding: 14px 28px; 
            border-radius: 4px; 
            border: none;
            transition: background 0.15s, transform 0.1s; 
            cursor: pointer; 
            white-space: nowrap;
            /* ADD THESE LINES FOR MOBILE: */
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
            user-select: none;
            position: relative;
            z-index: 1;
        }
        .scanner-cta-btn:hover { background: var(--green-dark); transform: translateY(-1px); }
        .scanner-cta-btn svg { width: 18px; height: 18px; }

        /* Access notice */
        .access-notice { background: var(--white); border: 1px solid var(--gray-200); border-left: 4px solid var(--blue); padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px; }
        .access-notice svg { width: 18px; height: 18px; color: var(--blue); flex-shrink: 0; margin-top: 1px; }
        .access-notice-text { font-size: 12px; color: var(--gray-600); line-height: 1.6; }
        .access-notice-text strong { color: var(--blue-dark); }

        /* Workflow */
        .workflow-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-600); margin-bottom: 14px; }
        .workflow-steps { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-bottom: 28px; background: var(--white); border: 1px solid var(--gray-200); }
        .step { padding: 18px 16px; border-right: 1px solid var(--gray-100); }
        .step:last-child { border-right: none; }
        .step-num { width: 24px; height: 24px; border-radius: 50%; background: var(--blue); color: var(--white); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; flex-shrink: 0; }
        .step-title { font-size: 12px; font-weight: 600; color: var(--blue-dark); margin-bottom: 4px; }
        .step-desc { font-size: 11px; color: var(--gray-600); line-height: 1.5; }

        /* Alert boxes */
        .alert-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }
        .alert-box { padding: 16px 18px; display: flex; align-items: flex-start; gap: 12px; border: 1px solid; }
        .alert-box.cleared  { background: var(--green-pale); border-color: #BBF7D0; border-left: 4px solid var(--green); }
        .alert-box.duplicate{ background: #FEF2F2; border-color: #FECACA; border-left: 4px solid var(--red); }
        .alert-box svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert-box.cleared svg  { color: var(--green); }
        .alert-box.duplicate svg{ color: var(--red); }
        .alert-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .alert-box.cleared .alert-title  { color: var(--green-dark); }
        .alert-box.duplicate .alert-title{ color: var(--red); }
        .alert-desc { font-size: 11px; line-height: 1.5; }
        .alert-box.cleared .alert-desc  { color: #166534; }
        .alert-box.duplicate .alert-desc{ color: #7F1D1D; }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; gap: 8px; z-index: 100;
        }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
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
                grid-template-areas: "topbar" "header" "main" "footer";
                height: 100vh; overflow: hidden;
            }
            .sidebar {
                grid-area: unset; position: fixed;
                top: 0; left: 0; bottom: 0; width: var(--sidebar-w);
                z-index: 300; transform: translateX(-100%);
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

            /* Workflow 5-col → 2-col */
            .workflow-steps { grid-template-columns: 1fr 1fr; }
            .step { border-right: none; border-bottom: 1px solid var(--gray-100); }
            .step:last-child { border-bottom: none; }

            /* CTA: button stretches full-width */
            .scanner-cta { padding: 22px 20px; gap: 18px; }
            .scanner-cta-btn { width: 100%; justify-content: center; }
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

            .welcome-card { padding: 16px 18px; gap: 14px; }
            .welcome-card img { width: 38px; height: 38px; }
            .welcome-heading { font-size: 16px; }
            .welcome-desc { display: none; }

            .scanner-cta { padding: 18px 16px; gap: 14px; }
            .scanner-cta-icon { width: 48px; height: 48px; }
            .scanner-cta-icon svg { width: 24px; height: 24px; }
            .scanner-cta-title { font-size: 16px; }
            .scanner-cta-desc { display: none; }
            
            /* CRITICAL: Large touch target for mobile */
            .scanner-cta-btn { 
                width: 100%; 
                justify-content: center;
                min-height: 56px !important;
                font-size: 15px;
                padding: 18px 24px;
            }

            .access-notice { padding: 12px 14px; }
            .access-notice-text { font-size: 11px; }

            .workflow-steps { grid-template-columns: 1fr; }
            .alert-row { grid-template-columns: 1fr; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        @media (max-width: 380px) {
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
            <div class="user-avatar">S</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Distribution Staff</div>
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

        <div class="nav-section-label">Staff Menu</div>

        <a href="#" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('staff.scan') }}" class="nav-item scanner-primary" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 7 23 1 17 1"/>
                <polyline points="1 17 1 23 7 23"/>
                <polyline points="23 17 23 23 17 23"/>
                <polyline points="1 7 1 1 7 1"/>
                <rect x="8" y="8" width="8" height="8" rx="1"/>
            </svg>
            Open QR Scanner
            <span class="nav-badge-scan">Active</span>
        </a>

        <hr class="sidebar-sep">
        <div class="nav-section-label">My Activity</div>

        <a href="{{ route('staff.scan.history') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Scan History
        </a>

        <a href="{{ route('staff.active-event') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
            Active Distribution Event
        </a>

        <div class="role-notice">
            <div class="role-notice-title">&#9432; Field Staff Access</div>
            <div class="role-notice-text">You can scan QR codes and confirm releases for the active event. You cannot edit family data, view other staff sessions, or export records.</div>
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
                <div class="page-breadcrumb">Home / <span>Staff Dashboard</span></div>
                <div class="page-h1">Distribution Staff Portal</div>
                <div class="page-sub">Barangay Family Track QR System — Ayuda Distribution &amp; QR Scanning</div>
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
                <div class="welcome-heading">Good day, <em>{{ auth()->user()->name }}!</em></div>
                <div class="welcome-desc">You are logged in as Relief Distribution Staff. Select an active distribution event and begin scanning household QR codes.</div>
            </div>
        </div>

        <div class="scanner-cta">
            <div class="scanner-cta-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 7 23 1 17 1"/>
                    <polyline points="1 17 1 23 7 23"/>
                    <polyline points="23 17 23 23 17 23"/>
                    <polyline points="1 7 1 1 7 1"/>
                    <rect x="8" y="8" width="8" height="8" rx="1"/>
                </svg>
            </div>
            <div class="scanner-cta-content">
                <div class="scanner-cta-label">Primary Action</div>
                <div class="scanner-cta-title">QR Code Scanner</div>
                <div class="scanner-cta-desc">Activate your device camera to scan household QR cards. The system will instantly validate the family, check for duplicate releases, and record the distribution upon confirmation.</div>
            </div>
            <a href="{{ route('staff.scan') }}" class="scanner-cta-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 7 23 1 17 1"/>
                    <polyline points="1 17 1 23 7 23"/>
                    <polyline points="23 17 23 23 17 23"/>
                    <polyline points="1 7 1 1 7 1"/>
                    <rect x="8" y="8" width="8" height="8" rx="1"/>
                </svg>
                Open Scanner
            </a>
        </div>

        <div class="access-notice">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="access-notice-text">
                <strong>Relief Distribution Staff — Scanner / Field Access.</strong> You may scan household QR codes using your device camera, view the family summary on scan (household head, members, last release date), and confirm receipt to log the distribution. You cannot edit family data, view other staff sessions, or export any records. Duplicate releases are automatically blocked by the system.
            </div>
        </div>

        <div class="workflow-title">Distribution Workflow — Step by Step</div>
        <div class="workflow-steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-title">Select Event</div>
                <div class="step-desc">Log in and select the active distribution event assigned to you.</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-title">Open Scanner</div>
                <div class="step-desc">Tap "Open Scanner" to activate your device camera via the browser.</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-title">Scan QR Card</div>
                <div class="step-desc">Hold the household QR card under the camera. System reads the serial code.</div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-title">Validate Family</div>
                <div class="step-desc">System shows family name, member count, and last release. Verify details.</div>
            </div>
            <div class="step">
                <div class="step-num">5</div>
                <div class="step-title">Confirm Release</div>
                <div class="step-desc">Tap Confirm. System logs your staff ID, timestamp, and goods automatically.</div>
            </div>
        </div>

        <div class="workflow-title">Possible Scan Results</div>
        <div class="alert-row">
            <div class="alert-box cleared">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div>
                    <div class="alert-title">&#10003; Cleared — Confirm Release</div>
                    <div class="alert-desc">Screen displays the family name, household head, member count, and a green CONFIRM RELEASE button. Tap confirm to record the distribution with your staff ID and timestamp.</div>
                </div>
            </div>
            <div class="alert-box duplicate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <div>
                    <div class="alert-title">&#9888; Duplicate Alert — Already Received</div>
                    <div class="alert-desc">Red warning screen shows ALREADY RECEIVED with the date, time, and name of the staff who confirmed the previous release. Re-release is automatically blocked.</div>
                </div>
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

    /* ─── Sidebar ─── */
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