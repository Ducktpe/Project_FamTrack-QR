<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Active Distribution Event</title>
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
            --orange:     #EA580C;
            --orange-pale:#FFF7ED;
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
            display: none !important;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); z-index: 200;
            opacity: 0; transition: opacity 0.25s;
            pointer-events: none;
        }
        .sidebar-overlay.active {
            display: block !important;
            pointer-events: auto;
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
        .page-breadcrumb a { color: var(--gray-400); text-decoration: none; }
        .page-breadcrumb a:hover { color: var(--blue-light); }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── ACTIVE EVENT CARD ─── */
        .event-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 4px solid var(--green);
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .event-card-header {
            background: linear-gradient(135deg, #0F3D2A 0%, #166534 100%);
            padding: 22px 28px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            flex-wrap: wrap;
        }
        .event-card-header-left { display: flex; align-items: center; gap: 16px; }
        .event-pulse-dot {
            width: 14px; height: 14px; border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 0 0 rgba(74,222,128,0.4);
            animation: pulse-ring 1.8s ease-out infinite;
            flex-shrink: 0;
        }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(74,222,128,0.5); }
            70%  { box-shadow: 0 0 0 10px rgba(74,222,128,0); }
            100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
        }
        .event-live-tag {
            background: rgba(74,222,128,0.15);
            border: 1px solid rgba(74,222,128,0.4);
            border-radius: 3px;
            padding: 3px 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; color: #4ade80;
        }
        .event-header-name { font-family: 'PT Serif', serif; font-size: 21px; font-weight: 700; color: var(--white); margin-bottom: 3px; }
        .event-header-sub { font-size: 12px; color: rgba(255,255,255,0.55); }
        .event-header-sub strong { color: rgba(255,255,255,0.85); }
        .event-elapsed-badge {
            text-align: right; flex-shrink: 0;
        }
        .elapsed-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); margin-bottom: 4px; }
        .elapsed-time { font-size: 26px; font-weight: 700; color: var(--yellow); font-variant-numeric: tabular-nums; letter-spacing: 1px; line-height: 1; }
        .elapsed-unit { font-size: 10px; color: rgba(255,255,255,0.4); margin-top: 3px; letter-spacing: 0.5px; }

        .event-card-body {
            padding: 22px 28px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .event-stat {
            padding: 14px 20px;
            border-right: 1px solid var(--gray-100);
        }
        .event-stat:first-child { padding-left: 0; }
        .event-stat:last-child { border-right: none; }
        .event-stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .event-stat-label svg { width: 13px; height: 13px; color: var(--gray-400); flex-shrink: 0; }
        .event-stat-value { font-size: 17px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .event-stat-value.scan-count { font-size: 28px; color: var(--green); }
        .event-stat-meta { font-size: 11px; color: var(--gray-400); margin-top: 3px; }

        .event-card-footer {
            padding: 16px 28px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
            background: var(--gray-50);
        }
        .event-start-info { font-size: 12px; color: var(--gray-600); }
        .event-start-info strong { color: var(--gray-800); }
        .event-scan-btn {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--green); color: var(--white);
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            text-decoration: none; padding: 12px 24px; border-radius: 4px;
            transition: background 0.15s, transform 0.1s; white-space: nowrap;
            -webkit-tap-highlight-color: transparent;
        }
        .event-scan-btn:hover { background: var(--green-dark); transform: translateY(-1px); }
        .event-scan-btn svg { width: 16px; height: 16px; }

        /* ─── NO EVENT STATE ─── */
        .no-event-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 4px solid var(--gray-400);
            padding: 52px 40px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .no-event-icon {
            width: 72px; height: 72px;
            background: var(--gray-100); border: 2px solid var(--gray-200);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .no-event-icon svg { width: 32px; height: 32px; color: var(--gray-400); }
        .no-event-title { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--gray-600); margin-bottom: 8px; }
        .no-event-desc { font-size: 13px; color: var(--gray-400); line-height: 1.6; max-width: 440px; }

        /* ─── RECENT SCANS TABLE ─── */
        .section-title {
            font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-600); margin-bottom: 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .section-title::after { content: ''; flex: 1; height: 1px; background: var(--gray-200); }

        .scans-table-wrap {
            background: var(--white); border: 1px solid var(--gray-200);
            box-shadow: 0 1px 4px rgba(0,0,0,0.04); margin-bottom: 24px;
            overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 11px 16px; text-align: left;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400); background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200); white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--gray-50); }
        tbody td { padding: 11px 16px; font-size: 12.5px; color: var(--gray-800); vertical-align: middle; }
        .td-num { font-variant-numeric: tabular-nums; }
        .td-household { font-weight: 600; color: var(--blue-dark); }
        .td-time { color: var(--gray-600); font-variant-numeric: tabular-nums; }
        .badge-released {
            display: inline-block; padding: 2px 10px; border-radius: 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0;
        }

        .empty-table-row td {
            text-align: center; padding: 40px 16px;
            color: var(--gray-400); font-size: 12px; font-style: italic;
        }

        /* ─── INFO NOTICE ─── */
        .info-notice {
            background: var(--white); border: 1px solid var(--gray-200); border-left: 4px solid var(--blue);
            padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px;
        }
        .info-notice svg { width: 18px; height: 18px; color: var(--blue); flex-shrink: 0; margin-top: 1px; }
        .info-notice-text { font-size: 12px; color: var(--gray-600); line-height: 1.6; }
        .info-notice-text strong { color: var(--blue-dark); }

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

        /* ─── RESPONSIVE ─── */
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
            .event-card-body { grid-template-columns: 1fr 1fr; }
            .event-stat:nth-child(2) { border-right: none; }
            .event-stat:nth-child(3) { padding-left: 0; border-top: 1px solid var(--gray-100); }
            .event-stat:nth-child(4) { border-top: 1px solid var(--gray-100); }
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
            .event-card-header { padding: 18px 20px; }
            .event-header-name { font-size: 17px; }
            .elapsed-time { font-size: 20px; }
            .event-card-body { padding: 16px 20px; grid-template-columns: 1fr 1fr; }
            .event-stat { padding: 10px 12px; }
            .event-stat:first-child { padding-left: 12px; }
            .event-card-footer { padding: 14px 20px; }
            .event-scan-btn { width: 100%; justify-content: center; min-height: 52px; font-size: 14px; }
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
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
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

        <a href="{{ route('staff.dashboard') }}" class="nav-item" onclick="closeSidebar()">
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
            My Scan History
        </a>

        <a href="#" class="nav-item active" onclick="closeSidebar()">
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
                <div class="page-breadcrumb">
                    <a href="{{ route('staff.dashboard') }}">Home</a> /
                    <span>Active Distribution Event</span>
                </div>
                <div class="page-h1">Active Distribution Event</div>
                <div class="page-sub">Live status and scan summary for the currently ongoing relief distribution.</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             STATE A: ACTIVE EVENTS EXIST
             ══════════════════════════════════════════ --}}
        @if(isset($activeEvents) && $activeEvents->isNotEmpty())

        @foreach($activeEvents as $event)

        <div class="event-card">

            {{-- Header strip --}}
            <div class="event-card-header">
                <div class="event-card-header-left">
                    <div class="event-pulse-dot"></div>
                    <div>
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">
                            <span class="event-live-tag">&#9679; Live</span>
                            <span style="font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:0.5px;">Event #{{ $event->id }}</span>
                        </div>
                        <div class="event-header-name">{{ $event->event_name }}</div>
                        <div class="event-header-sub">
                            Relief Type: <strong>{{ $event->relief_type }}</strong>
                            &nbsp;&mdash;&nbsp;
                            Barangay: <strong>{{ $event->target_barangay ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
                <div class="event-elapsed-badge">
                    <div class="elapsed-label">Elapsed Time</div>
                    <div class="elapsed-time" id="elapsed-{{ $event->id }}">00:00:00</div>
                    <div class="elapsed-unit">HH : MM : SS</div>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="event-card-body">
                <div class="event-stat">
                    <div class="event-stat-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                            <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                            <rect x="8" y="8" width="8" height="8" rx="1"/>
                        </svg>
                        Households Served
                    </div>
                    <div class="event-stat-value scan-count">{{ $event->scan_count }}</div>
                    <div class="event-stat-meta">confirmed releases</div>
                </div>
                <div class="event-stat">
                    <div class="event-stat-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Started At
                    </div>
                    <div class="event-stat-value" style="font-size:15px;">
                        {{ $event->started_at ? $event->started_at->format('h:i A') : '—' }}
                    </div>
                    <div class="event-stat-meta">
                        {{ $event->started_at ? $event->started_at->format('M d, Y') : '' }}
                    </div>
                </div>
                <div class="event-stat">
                    <div class="event-stat-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Target Barangay
                    </div>
                    <div class="event-stat-value" style="font-size:15px;">{{ $event->target_barangay ?? '—' }}</div>
                    <div class="event-stat-meta">service area</div>
                </div>
                <div class="event-stat">
                    <div class="event-stat-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                        </svg>
                        Relief Type
                    </div>
                    <div class="event-stat-value" style="font-size:15px;">{{ $event->relief_type }}</div>
                    <div class="event-stat-meta">goods category</div>
                </div>
            </div>

            {{-- Footer action --}}
            <div class="event-card-footer">
                <div class="event-start-info">
                    Event started by: <strong>{{ $event->creator->name ?? 'Administrator' }}</strong>
                    &nbsp;&bull;&nbsp;
                    Started <strong>{{ $event->started_at ? $event->started_at->diffForHumans() : '—' }}</strong>
                </div>
                <a href="{{ route('staff.scan') }}" class="event-scan-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                        <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                        <rect x="8" y="8" width="8" height="8" rx="1"/>
                    </svg>
                    Open QR Scanner
                </a>
            </div>
        </div>

        {{-- Recent scans table for this event --}}
        <div class="section-title">My Recent Scans — {{ $event->event_name }}</div>
        <div class="scans-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Barangay</th>
                        <th>Household Head</th>
                        <th>Members</th>
                        <th>Released At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($event->recent_scans as $i => $log)
                    <tr>
                        <td class="td-num">{{ $i + 1 }}</td>
                        <td class="td-household">{{ $log->household->barangay ?? '—' }}</td>
                        <td>{{ $log->household->household_head_name ?? '—' }}</td>
                        <td class="td-num">{{ $log->household->total_members ?? '—' }}</td>
                        <td class="td-time">{{ $log->distributed_at ? $log->distributed_at->format('h:i:s A') : $log->created_at->format('h:i:s A') }}</td>
                        <td><span class="badge-released">Released</span></td>
                    </tr>
                    @empty
                    <tr class="empty-table-row">
                        <td colspan="6">No scans recorded yet for this event. Open the scanner to begin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @endforeach

        <div class="info-notice">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="info-notice-text">
                <strong>Showing your scans only.</strong> These tables reflect releases confirmed by your account per event session. For the full distribution log, please contact your administrator.
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             STATE B: NO ACTIVE EVENT
             ══════════════════════════════════════════ --}}
        @else

        <div class="no-event-card">
            <div class="no-event-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            <div class="no-event-title">No Active Distribution Event</div>
            <div class="no-event-desc">There is currently no ongoing relief distribution event. Please wait for an administrator to start an event, or check back later.</div>
        </div>

        <div class="info-notice">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="info-notice-text">
                <strong>Waiting for event.</strong> Distribution events are started by the MDRRMO administrator. Once an event goes live, this page will show the event details and you will be able to begin scanning household QR codes. You may also return to the <a href="{{ route('staff.dashboard') }}" style="color:var(--blue-light);">Dashboard</a> in the meantime.
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

    /* ─── Elapsed time counters (one per active event) ─── */
    @if(isset($activeEvents) && $activeEvents->isNotEmpty())
    const eventTimers = {
        @foreach($activeEvents as $event)
        @if($event->started_at)
        {{ $event->id }}: {{ $event->started_at->timestamp }} * 1000,
        @endif
        @endforeach
    };
    function updateAllElapsed() {
        Object.entries(eventTimers).forEach(([id, startTs]) => {
            const el = document.getElementById('elapsed-' + id);
            if (!el) return;
            const diff = Math.max(0, Date.now() - startTs);
            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
        });
    }
    updateAllElapsed();
    setInterval(updateAllElapsed, 1000);
    @endif

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