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
            --purple:     #5B3FA6;
            --purple-dark:#3D1F8A;
            --purple-pale:#F5F0FF;
            --purple-border:#D8CBF5;
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
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
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
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--purple-pale); border: 1px solid var(--purple-border); border-radius: 4px; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--purple); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--purple-dark); }
        .user-role { font-size: 10px; color: #7C5CBF; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── READ-ONLY BADGE ─── */
        .readonly-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; background: #FFFBEB;
            border: 1px solid #FDE68A; border-radius: 3px;
            font-size: 11px; font-weight: 700; color: #92400E;
            text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0;
        }
        .readonly-badge svg { width: 12px; height: 12px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: #FEF2F2; color: var(--red); }
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

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .back-btn { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; padding: 8px 16px; border: 1px solid var(--gray-200); background: var(--white); border-radius: 4px; transition: background 0.15s; white-space: nowrap; flex-shrink: 0; }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        /* Stats row */
        .stats-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .stat-card { background: var(--white); border: 1px solid var(--gray-200); padding: 20px 24px; display: flex; align-items: center; gap: 16px; }
        .stat-card.pending { border-top: 3px solid var(--orange); }
        .stat-card.approved { border-top: 3px solid var(--green); }
        .stat-icon { width: 44px; height: 44px; border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-card.pending .stat-icon { background: var(--orange-pale); }
        .stat-card.approved .stat-icon { background: var(--green-pale); }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-card.pending .stat-icon svg { color: var(--orange); }
        .stat-card.approved .stat-icon svg { color: var(--green); }
        .stat-number { font-family: 'PT Serif', serif; font-size: 34px; font-weight: 700; line-height: 1; margin-bottom: 3px; }
        .stat-card.pending .stat-number { color: var(--orange); }
        .stat-card.approved .stat-number { color: var(--green); }
        .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }

        /* Tabs */
        .tabs-row { display: flex; gap: 0; border-bottom: 2px solid var(--gray-200); margin-bottom: 0; overflow-x: auto; }
        .tab-link { padding: 10px 20px; font-size: 13px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-bottom: 3px solid transparent; margin-bottom: -2px; display: flex; align-items: center; gap: 7px; transition: color 0.12s; white-space: nowrap; }
        .tab-link:hover { color: var(--blue); }
        .tab-link.active { color: var(--blue); font-weight: 700; border-bottom-color: var(--blue); }

        /* ─── SEARCH BAR ─── */
        .search-bar { background: var(--white); border: 1px solid var(--gray-200); border-top: none; padding: 12px 16px; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .search-input-wrap { position: relative; flex: 1; max-width: 420px; }
        .search-input-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--gray-400); pointer-events: none; }
        .search-input { width: 100%; padding: 8px 10px 8px 34px; border: 1px solid var(--gray-200); background: var(--gray-50); font-size: 13px; font-family: 'Open Sans', sans-serif; color: var(--gray-800); border-radius: 3px; outline: none; transition: border-color 0.15s, background 0.15s; }
        .search-input:focus { border-color: var(--blue); background: var(--white); }
        .search-input::placeholder { color: var(--gray-400); }
        .search-btn { padding: 8px 16px; background: var(--blue); color: var(--white); border: none; border-radius: 3px; font-size: 12px; font-weight: 600; font-family: 'Open Sans', sans-serif; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.15s; white-space: nowrap; }
        .search-btn:hover { background: var(--blue-dark); }
        .search-btn svg { width: 13px; height: 13px; }
        .search-clear { padding: 8px 12px; background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 3px; font-size: 12px; font-weight: 600; font-family: 'Open Sans', sans-serif; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 5px; transition: background 0.15s; white-space: nowrap; }
        .search-clear:hover { background: var(--gray-200); }
        .search-clear svg { width: 12px; height: 12px; }
        .search-results-label { margin-left: auto; font-size: 12px; color: var(--gray-600); white-space: nowrap; }
        .search-results-label strong { color: var(--blue-dark); }

        /* ─── QR SCAN COUNT ─── */
        .scan-count-wrap { display: inline-flex; flex-direction: column; align-items: center; gap: 2px; }
        .scan-count-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 10px; font-size: 12px; font-weight: 700; line-height: 1; }
        .scan-count-badge svg { width: 11px; height: 11px; flex-shrink: 0; }
        .scan-count-badge.has-scans { background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; }
        .scan-count-badge.no-scans { background: var(--gray-100); color: var(--gray-400); border: 1px solid var(--gray-200); font-weight: 600; }
        .scan-count-sub { font-size: 10px; color: var(--gray-400); text-align: center; }

        /* Table */
        .table-card { background: var(--white); border: 1px solid var(--gray-200); }
        .table-card-header { padding: 13px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .table-section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        thead th { padding: 11px 14px; background: var(--blue); color: var(--white); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:hover { background: var(--gray-50); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 12px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        tbody tr:nth-child(even) td { background: var(--gray-50); }
        tbody tr:nth-child(even):hover td { background: var(--blue-pale); }
        .td-name strong { display: block; font-size: 13px; color: var(--blue-dark); }
        .td-name small { font-size: 11px; color: var(--gray-400); margin-top: 2px; display: block; }
        .td-address small { font-size: 11px; color: var(--gray-400); display: block; margin-top: 1px; }

        mark { background: #FEF08A; color: var(--gray-800); border-radius: 2px; padding: 0 1px; font-style: normal; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .badge-approved { background: var(--green-pale); color: var(--green-dark); }
        .badge-pending  { background: var(--orange-pale); color: #92400E; }
        .badge svg { width: 10px; height: 10px; }
        .serial-code { font-size: 12px; font-weight: 700; color: var(--blue); font-family: monospace; letter-spacing: 0.5px; }
        .serial-none { font-size: 11px; color: var(--gray-400); font-style: italic; }

        .btn-view { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--blue); color: var(--white); font-size: 11px; font-weight: 600; text-decoration: none; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.15s; white-space: nowrap; }
        .btn-view:hover { background: var(--blue-dark); }
        .btn-view svg { width: 12px; height: 12px; }

        /* Empty state */
        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon { width: 48px; height: 48px; border-radius: 50%; background: var(--gray-100); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── PAGINATION ─── */
        .pagination-row { padding: 14px 20px; border-top: 1px solid var(--gray-100); background: var(--gray-50); }
        .pagination-row svg { width: 14px !important; height: 14px !important; flex-shrink: 0; display: block; }
        .pagination-row nav { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .pagination-row p, .pagination-row nav > p { font-size: 12px; color: var(--gray-400); line-height: 1.5; }
        .pagination-row p strong { color: var(--gray-600); font-weight: 600; }
        .pagination-row nav > div, .pagination-row .flex, .pagination-row span[role="group"] { display: flex; align-items: center; gap: 3px; flex-wrap: wrap; }
        .pagination-row nav a, .pagination-row nav span > span, .pagination-row nav > span { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; color: var(--gray-600); background: var(--white); border: 1px solid var(--gray-200); border-radius: 3px; text-decoration: none; line-height: 1; transition: background 0.12s, color 0.12s, border-color 0.12s; }
        .pagination-row nav a:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }
        .pagination-row nav span[aria-current="page"] > span, .pagination-row nav .active > span { background: var(--blue); color: var(--white); border-color: var(--blue); font-weight: 700; }
        .pagination-row nav span[aria-disabled="true"], .pagination-row nav span[aria-disabled="true"] > span { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

        /* ─── FOOTER ─── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; gap: 8px; }
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
            .header-title { font-size: 13px; }
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .stats-row { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card { padding: 12px 14px; gap: 10px; }
            .stat-icon { width: 36px; height: 36px; }
            .stat-icon svg { width: 18px; height: 18px; }
            .stat-number { font-size: 22px; }
            .search-bar { flex-wrap: wrap; }
            .search-input-wrap { max-width: 100%; width: 100%; }
            .search-results-label { display: none; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .readonly-badge { display: none; }
        }
        .nav-badge-view { margin-left: auto; background: var(--gray-400); color: var(--white); font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 10px; letter-spacing: 0.5px; }
        .role-notice { margin: 12px 14px; background: var(--purple-pale); border: 1px solid var(--purple-border); border-left: 3px solid var(--purple); padding: 10px 12px; border-radius: 2px; }
        .role-notice-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--purple-dark); margin-bottom: 3px; }
        .role-notice-text { font-size: 11px; color: #4B3080; line-height: 1.5; }
    </style>
</head>
<body>
<div class="shell">

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
        <span class="readonly-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Read-Only Access
        </span>
        <div class="header-user-badge">
            <div class="user-avatar">A</div>
            <div>
                <div class="user-name">Auditor</div>
                <div class="user-role">View-Only Access</div>
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

        <a href="{{ route('auditor.distribution.logs') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
            <span class="nav-badge-view">View</span>
        </a>

        <a href="{{ route('auditor.households.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Households
            <span class="nav-badge-view">View</span>
        </a>

        <a href="#" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Reports &amp; Exports
            <span class="nav-badge-view">View</span>
        </a>

        <a href="#" class="nav-item" onclick="closeSidebar()">
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
                <div class="page-breadcrumb">Auditor / <span>Households</span></div>
                <div class="page-h1">Household Management</div>
                <div class="page-sub">Read-only view of registered household profiles — RBI Framework</div>
            </div>
            <a href="{{ route('auditor.dashboard') }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card pending">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div class="stat-number">{{ $pendingCount }}</div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div class="stat-number">{{ $approvedCount }}</div>
                    <div class="stat-label">Approved Households</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-row">
            <a href="{{ route('auditor.households.index', ['filter' => 'all', 'search' => request('search')]) }}"
               class="tab-link {{ $filter === 'all' ? 'active' : '' }}">All Households</a>
            <a href="{{ route('auditor.households.index', ['filter' => 'pending', 'search' => request('search')]) }}"
               class="tab-link {{ $filter === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('auditor.households.index', ['filter' => 'approved', 'search' => request('search')]) }}"
               class="tab-link {{ $filter === 'approved' ? 'active' : '' }}">Approved</a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('auditor.households.index') }}" id="searchForm">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="search-bar">
                <div class="search-input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="text" name="search" id="searchInput" class="search-input"
                        placeholder="Search by name, barangay, serial code..."
                        value="{{ request('search') }}" autocomplete="off"
                        oninput="debounceSearch()"
                    >
                </div>
                <button type="submit" class="search-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('auditor.households.index', ['filter' => $filter]) }}" class="search-clear">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Clear
                    </a>
                @endif
                <span class="search-results-label">
                    Showing <strong>{{ $households->count() }}</strong> of <strong>{{ $households->total() }}</strong>
                    @if(request('search'))
                        &nbsp;for &ldquo;<strong>{{ request('search') }}</strong>&rdquo;
                    @endif
                </span>
            </div>
        </form>

        <!-- Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="ca-dot"></div>
                <div class="table-section-title">
                    {{ $filter === 'pending' ? 'Pending Approval' : ($filter === 'approved' ? 'Approved Households' : 'All Households') }}
                    @if(request('search'))
                        &mdash; Results for &ldquo;{{ request('search') }}&rdquo;
                    @endif
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
                            <th>QR Scans</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($households as $household)
                            @php $scanCount = $household->distributionLogs->count(); @endphp
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
                                <td>
                                    <div class="scan-count-wrap">
                                        <span class="scan-count-badge {{ $scanCount > 0 ? 'has-scans' : 'no-scans' }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                                                <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                                                <rect x="8" y="8" width="8" height="8" rx="1"/>
                                            </svg>
                                            {{ $scanCount }}
                                        </span>
                                        <span class="scan-count-sub">{{ $scanCount > 0 ? 'time'.($scanCount !== 1 ? 's' : '').' scanned' : 'not scanned' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('auditor.households.show', $household) }}" class="btn-view">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
                                            </svg>
                                        </div>
                                        <div class="empty-title">
                                            @if(request('search'))
                                                No results for &ldquo;{{ request('search') }}&rdquo;
                                            @else
                                                No households found
                                            @endif
                                        </div>
                                        <div class="empty-sub">
                                            @if(request('search'))
                                                Try a different search term or <a href="{{ route('auditor.households.index', ['filter' => $filter]) }}" style="color:var(--blue-light);">clear the search</a>.
                                            @else
                                                No records match the selected filter.
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-row">
                {{ $households->appends(['filter' => $filter, 'search' => request('search')])->links() }}
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
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
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
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    let searchTimer;
    function debounceSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('searchForm').submit(), 450);
    }
</script>
</body>
</html>