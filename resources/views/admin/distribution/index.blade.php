<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Distribution Logs</title>
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
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ─── HEADER ─── */
        header { grid-area: header; background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; align-items: center; padding: 0 28px; gap: 14px; z-index: 90; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; margin-left: -4px; border-radius: 4px; color: var(--blue-dark); flex-shrink: 0; }
        .hamburger:hover { background: var(--gray-50); }
        .hamburger svg { width: 20px; height: 20px; }
        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-admin-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid var(--gray-200); border-radius: 4px; }
        .admin-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .admin-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .admin-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR ─── */
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
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

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── MAIN CONTENT ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        /* ─── FILTERS BAR ─── */
        .filters-bar { background: var(--white); border: 1px solid var(--gray-200); padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 0; border-bottom: none; border-radius: 4px 4px 0 0; }
        .filter-group { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); white-space: nowrap; }
        .filter-select { border: 1px solid var(--gray-200); background: var(--gray-50); padding: 7px 10px; font-size: 12px; color: var(--gray-800); font-family: 'Open Sans', sans-serif; outline: none; border-radius: 3px; transition: border-color 0.15s; padding-right: 28px; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center; }
        .filter-select:focus { border-color: var(--blue); background-color: var(--white); }
        .filter-input-date { border: 1px solid var(--gray-200); background: var(--gray-50); padding: 7px 10px; font-size: 12px; color: var(--gray-800); font-family: 'Open Sans', sans-serif; outline: none; border-radius: 3px; transition: border-color 0.15s; }
        .filter-input-date:focus { border-color: var(--blue); background: var(--white); box-shadow: 0 0 0 3px rgba(27,63,122,0.08); }
        .filter-spacer { flex: 1; min-width: 8px; }
        .filter-count { font-size: 12px; color: var(--gray-600); white-space: nowrap; }
        .filter-count strong { color: var(--blue-dark); }

        /* Search scope combo */
        .search-combo { display: flex; align-items: stretch; border: 1px solid var(--gray-200); border-radius: 3px; overflow: hidden; background: var(--gray-50); transition: border-color 0.15s, box-shadow 0.15s; }
        .search-combo:focus-within { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(27,63,122,0.1); background: var(--white); }
        .scope-select { border: none; border-right: 1px solid var(--gray-200); background: var(--gray-100); padding: 7px 22px 7px 8px; font-size: 11px; font-weight: 700; color: var(--gray-600); font-family: 'Open Sans', sans-serif; outline: none; appearance: none; cursor: pointer; flex-shrink: 0; min-width: 96px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 5px center; transition: background-color 0.12s; }
        .scope-select:hover { background-color: var(--blue-pale); color: var(--blue); }
        .search-text-input { border: none; background: transparent; padding: 7px 10px; font-size: 12px; color: var(--gray-800); font-family: 'Open Sans', sans-serif; outline: none; width: 200px; }
        .search-clear-btn { display: none; align-items: center; justify-content: center; padding: 0 9px; border: none; background: transparent; color: var(--gray-400); cursor: pointer; font-size: 17px; line-height: 1; transition: color 0.12s; }
        .search-clear-btn:hover { color: var(--red); }
        .search-combo.has-value .search-clear-btn { display: flex; }

        /* Highlight */
        mark.hl { background: #FFF176; color: inherit; border-radius: 2px; padding: 0 1px; font-style: normal; }

        /* Active filter tags */
        .active-filters { background: var(--white); border: 1px solid var(--gray-200); border-top: none; padding: 8px 16px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .active-filters.hidden { display: none; }
        .filter-tag { display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; background: var(--blue-pale); border: 1px solid #C7D9F5; border-radius: 10px; font-size: 11px; color: var(--blue); font-weight: 600; }
        .filter-tag a { color: var(--blue); text-decoration: none; margin-left: 2px; opacity: 0.6; font-weight: 700; }
        .filter-tag a:hover { opacity: 1; }
        .clear-all { font-size: 11px; color: var(--red); text-decoration: none; font-weight: 600; margin-left: 4px; }
        .clear-all:hover { text-decoration: underline; }

        /* Mobile filter toggle */
        .filter-toggle-btn { display: none; align-items: center; gap: 6px; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 3px; padding: 7px 12px; font-size: 12px; font-weight: 600; color: var(--gray-600); cursor: pointer; font-family: 'Open Sans', sans-serif; white-space: nowrap; transition: background 0.15s; flex-shrink: 0; }
        .filter-toggle-btn:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }
        .filter-toggle-btn .ftb-count { background: var(--blue); color: #fff; border-radius: 10px; font-size: 10px; padding: 1px 6px; margin-left: 2px; display: none; }
        .filter-toggle-btn .ftb-count.show { display: inline; }
        .filters-collapsible { display: contents; }

        /* ─── TABLE ─── */
        .table-wrap { background: var(--white); border: 1px solid var(--gray-200); border-top: none; overflow-x: auto; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); background: var(--gray-50); border-bottom: 2px solid var(--gray-200); white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 10px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        .td-event { font-weight: 600; color: var(--blue-dark); font-size: 13px; }
        .td-date { font-size: 12px; color: var(--gray-600); white-space: nowrap; }
        .td-num { font-variant-numeric: tabular-nums; font-weight: 600; }

        /* Status badges */
        .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .status-upcoming  { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
        .status-upcoming::before  { background: #3B82F6; }
        .status-ongoing   { background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; }
        .status-ongoing::before   { background: var(--green); box-shadow: 0 0 4px var(--green); animation: blink 1.5s infinite; }
        .status-completed { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
        .status-completed::before { background: var(--gray-400); }
        .status-cancelled { background: #FEF2F2; color: var(--red); border: 1px solid #FECACA; }
        .status-cancelled::before { background: var(--red); }

        /* View btn */
        .btn-view { padding: 5px 12px; font-size: 12px; background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F5; border-radius: 4px; cursor: pointer; font-weight: 600; font-family: 'Open Sans', sans-serif; transition: background 0.12s; white-space: nowrap; }
        .btn-view:hover { background: #d4e4f7; }

        /* Empty state */
        .empty-state { padding: 56px 24px; text-align: center; background: var(--white); border: 1px solid var(--gray-200); border-top: none; margin-bottom: 24px; }
        .empty-icon { width: 52px; height: 52px; border-radius: 50%; background: var(--gray-100); border: 2px solid var(--gray-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon svg { width: 24px; height: 24px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-content { background: #fff; border-radius: 6px; max-width: 900px; width: 90%; max-height: 85vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--gray-200); }
        .modal-header h2 { font-size: 18px; font-weight: 700; color: var(--blue); }
        .modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray-600); transition: color 0.12s; }
        .modal-close:hover { color: var(--red); }
        .modal-body { padding: 20px; }

        /* ─── FOOTER ─── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; gap: 8px; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px 76px 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; }
            .sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 300; transform: translateX(-100%); transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block !important; pointer-events: none; }
            .sidebar-overlay.active { pointer-events: auto; }
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

            /* Collapsible filters on tablet */
            .filter-toggle-btn { display: flex; }
            .filters-collapsible { display: none; width: 100%; }
            .filters-collapsible.open { display: flex; flex-direction: column; align-items: flex-start; gap: 8px; }
            .filters-bar { flex-wrap: wrap; gap: 8px; }
            .filter-group { width: 100%; }
            .filter-group .filter-select,
            .filter-group .filter-input-date { flex: 1; width: 100%; }
            .search-combo { width: 100%; }
            .search-text-input { flex: 1; width: auto; min-width: 0; }

            /* Card-style table rows */
            .table-wrap table thead { display: none; }
            .table-wrap table tbody tr { display: grid; grid-template-columns: 1fr 1fr; gap: 2px 12px; padding: 10px 14px; border-bottom: 2px solid var(--gray-200); }
            .table-wrap table tbody td { padding: 3px 0; border: none; font-size: 12px; }
            .table-wrap table tbody td:before { content: attr(data-label); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-400); display: block; margin-bottom: 1px; }
            .table-wrap table tbody td:first-child { grid-column: 1 / -1; }
        }

        @media (max-width: 640px) {
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider { display: none; }
            .header-logos img:last-child { display: none; }
            .header-org { display: none; }
            .header-title { font-size: 13px; }
            .header-admin-badge { display: none; }
            .topbar { justify-content: flex-end; }
            .status-indicator { display: none; }
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            footer { padding: 0 12px; }
            .footer-left { font-size: 10px; }
            .table-wrap table tbody tr { grid-template-columns: 1fr; }
            .table-wrap table tbody td:first-child { grid-column: 1; }
        }
    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- TOP UTILITY BAR -->
    <div class="topbar">
        <div class="topbar-left">Republic of the Philippines | Province of Cavite | Municipality of Naic</div>
        <div class="topbar-right">
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
            <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal">
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
                <div class="admin-role">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close navigation">
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
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
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
                <div class="page-breadcrumb">Admin / Distribution / <span>Logs &amp; Events</span></div>
                <div class="page-h1">Distribution Events</div>
                <div class="page-sub">View and manage distribution events with household details</div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             FILTERS
        ═══════════════════════════════════════════════ --}}
        @php
            $activeFilters = array_filter([
                'search'    => request('search'),
                'date_from' => request('date_from'),
                'date_to'   => request('date_to'),
                'status'    => request('status'),
                'scan_mode' => request('scan_mode'),
            ]);
        @endphp

        <form method="GET" action="{{ route('admin.distribution.logs') }}" id="filterForm">
            <input type="hidden" name="scope" id="scopeHidden" value="{{ request('scope', 'all') }}">

            <div class="filters-bar">

                {{-- Mobile toggle --}}
                <button type="button" class="filter-toggle-btn" onclick="toggleFilters()" id="filterToggleBtn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filters
                    <span class="ftb-count {{ count($activeFilters) ? 'show' : '' }}" id="filterBadge">{{ count($activeFilters) ?: '' }}</span>
                </button>

                {{-- Search scope combo --}}
                <div class="filter-group">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2" style="flex-shrink:0">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <div class="search-combo" id="searchCombo">
                        <select class="scope-select" id="scopeSelect" onchange="updateScope(this.value)">
                            <option value="all"        {{ request('scope','all') === 'all'        ? 'selected' : '' }}>All Fields</option>
                            <option value="event_name" {{ request('scope') === 'event_name' ? 'selected' : '' }}>Event Name</option>
                            <option value="status"     {{ request('scope') === 'status'     ? 'selected' : '' }}>Status</option>
                            <option value="scan_mode"  {{ request('scope') === 'scan_mode'  ? 'selected' : '' }}>Scan Mode</option>
                            <option value="date"       {{ request('scope') === 'date'       ? 'selected' : '' }}>Date</option>
                        </select>
                        <input type="text" name="search" class="search-text-input" id="searchInput"
                            placeholder="Search distribution events..."
                            value="{{ request('search') }}"
                            oninput="onSearchInput(this)"
                            autocomplete="off">
                        <button type="button" class="search-clear-btn" onclick="clearSearch()" title="Clear">×</button>
                    </div>
                </div>

                {{-- Collapsible secondary filters --}}
                <div class="filters-collapsible" id="filtersCollapsible">
                    <div class="filter-group">
                        <span class="filter-label">From</span>
                        <input type="date" name="date_from" class="filter-input-date"
                            value="{{ request('date_from') }}" onchange="this.form.submit()">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">To</span>
                        <input type="date" name="date_to" class="filter-input-date"
                            value="{{ request('date_to') }}" onchange="this.form.submit()">
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Status</span>
                        <select name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="upcoming"  {{ request('status') == 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                            <option value="ongoing"   {{ request('status') == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Scan Mode</span>
                        <select name="scan_mode" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Modes</option>
                            <option value="household"   {{ request('scan_mode') == 'household'   ? 'selected' : '' }}>🏠 Per Household</option>
                            <option value="family_head" {{ request('scan_mode') == 'family_head' ? 'selected' : '' }}>👤 Per Family Head</option>
                        </select>
                    </div>
                </div>

                <div class="filter-spacer"></div>
                <div class="filter-count">
                    <strong>{{ $events->total() }}</strong> events
                </div>
            </div>

            {{-- Active filter tags --}}
            @if(count($activeFilters))
            <div class="active-filters">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2.5" style="flex-shrink:0">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                @foreach($activeFilters as $key => $val)
                    <span class="filter-tag">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $val }}
                        <a href="{{ request()->fullUrlWithQuery([$key => null]) }}">×</a>
                    </span>
                @endforeach
                <a href="{{ route('admin.distribution.logs') }}" class="clear-all">Clear all</a>
            </div>
            @endif
        </form>

        {{-- ═══════════════════════════════════════════════
             TABLE
        ═══════════════════════════════════════════════ --}}
        @if($events->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <div class="empty-title">No distribution events found</div>
                <div class="empty-sub">Try adjusting your search or filter criteria.</div>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Scan Mode</th>
                            <th>Total Distributed</th>
                            <th>Unique Households</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        @php $statusSlug = strtolower($event->status); @endphp
                        <tr>
                            <td class="td-event hl-event" data-label="Event Name">{{ $event->event_name }}</td>
                            <td class="td-date hl-date" data-label="Date">{{ $event->event_date }}</td>
                            <td data-label="Status">
                                <span class="status-badge status-{{ $statusSlug }} hl-status">{{ $event->status }}</span>
                            </td>
                            <td class="hl-scan" data-label="Scan Mode">
                                @if($event->scan_mode === 'household') 🏠 Per Household
                                @elseif($event->scan_mode === 'family_head') 👤 Per Family Head
                                @else {{ $event->scan_mode ?? '—' }}
                                @endif
                            </td>
                            <td class="td-num" data-label="Total Distributed">{{ number_format($event->total_distributed) }}</td>
                            <td class="td-num" data-label="Unique Households">{{ number_format($event->unique_households) }}</td>
                            <td data-label="Action">
                                <button class="btn-view"
                                    onclick="openModal('{{ route('admin.distribution.events.households', $event) }}', '{{ addslashes($event->event_name) }}')">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Modal -->
        <div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 id="modalTitle">Event Details</h2>
                    <button class="modal-close" onclick="closeModal()">✕</button>
                </div>
                <div class="modal-body" id="modalBody">
                    <p style="color:var(--gray-400);text-align:center;padding:20px 0">Loading…</p>
                </div>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">
            <span>© <span id="footer-year"></span> <strong>MDRRMO Naic, Cavite</strong> — All Rights Reserved</span>
        </div>
    </footer>

</div>

<script>
    /* ─── Sidebar ─── */
    function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('active'); document.body.style.overflow = ''; }

    /* ─── Modal ─── */
    function openModal(url, eventName) {
        document.getElementById('modalTitle').textContent = eventName + ' — Households';
        document.getElementById('modalBody').innerHTML = '<p style="color:var(--gray-400);text-align:center;padding:20px 0">Loading…</p>';
        document.getElementById('modalOverlay').classList.add('active');
        fetch(url)
            .then(r => r.text())
            .then(html => {
                const body = new DOMParser().parseFromString(html, 'text/html').querySelector('.modal-body');
                document.getElementById('modalBody').innerHTML = body ? body.innerHTML : html;
            })
            .catch(() => {
                document.getElementById('modalBody').innerHTML = '<p style="color:var(--red);padding:20px 0">Error loading data. Please try again.</p>';
            });
    }
    function closeModal() { document.getElementById('modalOverlay').classList.remove('active'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    /* ─── Clock ─── */
    function updateClock() {
        const now = new Date(), pad = n => String(n).padStart(2,'0');
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
    }
    updateClock(); setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    /* ─── Search scope ─── */
    const PLACEHOLDERS = {
        all:        'Search event name, status, date, scan mode…',
        event_name: 'Search by event name…',
        status:     'e.g. Upcoming, Ongoing, Completed…',
        scan_mode:  'e.g. household, family_head…',
        date:       'e.g. 2025-06-15…',
    };
    function updateScope(val) {
        document.getElementById('scopeHidden').value = val;
        document.getElementById('searchInput').placeholder = PLACEHOLDERS[val] || 'Search…';
    }

    /* ─── Search input: debounce + live highlight + clear btn ─── */
    let searchTimer;
    function onSearchInput(input) {
        document.getElementById('searchCombo').classList.toggle('has-value', input.value.length > 0);
        applyHighlight(input.value, document.getElementById('scopeSelect').value);
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 600);
    }
    function clearSearch() {
        const input = document.getElementById('searchInput');
        input.value = '';
        document.getElementById('searchCombo').classList.remove('has-value');
        removeHighlight();
        document.getElementById('filterForm').submit();
    }

    /* ─── Live highlight ─── */
    const SCOPE_CLASSES = {
        all:        ['hl-event','hl-date','hl-status','hl-scan'],
        event_name: ['hl-event'],
        status:     ['hl-status'],
        scan_mode:  ['hl-scan'],
        date:       ['hl-date'],
    };
    function escRe(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
    function applyHighlight(term, scope) {
        removeHighlight();
        if (!term) return;
        const re = new RegExp('(' + escRe(term) + ')', 'gi');
        (SCOPE_CLASSES[scope] || SCOPE_CLASSES.all).forEach(cls => {
            document.querySelectorAll('.' + cls).forEach(el => {
                el.innerHTML = el.textContent.replace(re, '<mark class="hl">$1</mark>');
            });
        });
    }
    function removeHighlight() {
        document.querySelectorAll('mark.hl').forEach(m => {
            m.parentNode.replaceChild(document.createTextNode(m.textContent), m);
            m.parentNode.normalize();
        });
    }

    /* ─── Mobile filter toggle ─── */
    function toggleFilters() {
        const col = document.getElementById('filtersCollapsible');
        const btn = document.getElementById('filterToggleBtn');
        col.classList.toggle('open');
        btn.style.color = col.classList.contains('open') ? 'var(--blue)' : '';
    }

    /* ─── Init on page load ─── */
    (function() {
        const input = document.getElementById('searchInput');
        const scope = document.getElementById('scopeSelect');
        if (input && input.value) {
            document.getElementById('searchCombo').classList.add('has-value');
            applyHighlight(input.value, scope ? scope.value : 'all');
        }
        if (scope) updateScope(scope.value);
    })();
</script>
</body>
</html>