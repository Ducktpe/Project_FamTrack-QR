{{-- resources/views/auditor/distribution/logs.blade.php --}}
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
            --green:         #1A7A4A;
            --green-pale:    #EAF5EF;
            --green-border:  #A8D8BE;
            --orange:        #D97706;
            --orange-pale:   #FFFBEB;
            --red-pale:      #FEF2F2;
            --purple:        #5B3FA6;
            --purple-dark:   #3D1F8A;
            --purple-pale:   #F5F0FF;
            --purple-border: #D8CBF5;
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
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
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

        /* ── Purple auditor badge (matches dashboard & family profile) ── */
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--purple-pale); border: 1px solid var(--purple-border); border-radius: 4px; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--purple); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--purple-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: #7C5CBF; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

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

        /* Read-only role notice — identical to dashboard */
        .role-notice { margin: 12px 14px; background: var(--purple-pale); border: 1px solid var(--purple-border); border-left: 3px solid var(--purple); padding: 10px 12px; border-radius: 2px; }
        .role-notice-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--purple-dark); margin-bottom: 3px; }
        .role-notice-text { font-size: 11px; color: #4B3080; line-height: 1.5; }

        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn { width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white); border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        /* ─── PAGE TITLEBAR ─── */
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; flex-wrap: wrap; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── SUMMARY CARDS ─── */
        .summary-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .summary-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 16px 18px; }
        .summary-card.green  { border-top-color: var(--green); }
        .summary-card.orange { border-top-color: var(--orange); }
        .summary-card.yellow { border-top-color: var(--yellow-dark); }
        .summary-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 6px; }
        .summary-value { font-family: 'PT Serif', serif; font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .summary-card.green  .summary-value { color: var(--green); }
        .summary-card.orange .summary-value { color: var(--orange); }
        .summary-card.yellow .summary-value { color: var(--yellow-dark); }
        .summary-sub { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        /* ─── FILTER BOX ─── */
        .filter-box { background: var(--white); border: 1px solid var(--gray-200); padding: 16px 20px; margin-bottom: 16px; }
        .filter-box-header { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 12px; }
        .filters { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap: 10px; align-items: end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }
        input[type="text"], input[type="date"], select {
            width: 100%; padding: 8px 10px; border: 1px solid var(--gray-200); border-radius: 3px;
            font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); background: var(--white); outline: none;
        }
        input:focus, select:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.1); }
        input::placeholder { color: var(--gray-400); }
        .btn-filter { padding: 8px 16px; background: var(--blue); color: var(--white); border: none; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background 0.15s; white-space: nowrap; align-self: end; }
        .btn-filter:hover { background: var(--blue-dark); }
        .btn-clear { padding: 8px 14px; background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-decoration: none; white-space: nowrap; align-self: end; display: inline-block; text-align: center; transition: background 0.15s; }
        .btn-clear:hover { background: var(--gray-100); }

        /* ─── TABLE ─── */
        .table-wrap { background: var(--white); border: 1px solid var(--gray-200); overflow: hidden; }
        .table-header { padding: 13px 20px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; gap: 10px; justify-content: space-between; }
        .table-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); display: flex; align-items: center; gap: 8px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .table-count { font-size: 11px; color: var(--gray-400); }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        thead tr { background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
        thead th { padding: 11px 16px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 12px 16px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }

        /* Status badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .badge-upcoming  { background: var(--blue-pale);  color: var(--blue); }
        .badge-ongoing   { background: var(--green-pale); color: var(--green); }
        .badge-completed { background: var(--gray-100);   color: var(--gray-600); }
        .badge-cancelled { background: var(--red-pale);   color: var(--red); }

        /* Action button — matches view-btn style from family-profiles */
        .btn-view { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--blue-pale); color: var(--blue); border: 1px solid #C5D9F5; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 11px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.12s, color 0.12s; white-space: nowrap; }
        .btn-view:hover { background: var(--blue); color: var(--white); }
        .btn-view svg { width: 12px; height: 12px; }

        /* Empty state */
        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon-wrap { width: 48px; height: 48px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon-wrap svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* Pagination */
        .pagination-wrap { padding: 12px 20px; border-top: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--gray-600); flex-wrap: wrap; gap: 8px; }
        .pagination-wrap .links a, .pagination-wrap .links span { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-600); font-size: 12px; text-decoration: none; border-radius: 3px; margin: 0 2px; transition: all 0.15s; padding: 0 6px; }
        .pagination-wrap .links span[aria-current] { background: var(--blue); color: var(--white); border-color: var(--blue); font-weight: 700; }
        .pagination-wrap .links a:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }

        /* ─── MODAL ─── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 500; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: var(--white); width: 100%; max-width: 960px; max-height: 88vh; display: flex; flex-direction: column; border-top: 4px solid var(--yellow); box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); flex-shrink: 0; }
        .modal-head-left { display: flex; align-items: center; gap: 10px; }
        .modal-head-icon { width: 32px; height: 32px; background: var(--blue-pale); border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .modal-head-icon svg { width: 16px; height: 16px; color: var(--blue); }
        .modal-head h2 { font-family: 'PT Serif', serif; font-size: 16px; font-weight: 700; color: var(--blue-dark); }
        .modal-head-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .modal-close-btn { background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--gray-600); transition: background 0.15s; flex-shrink: 0; }
        .modal-close-btn:hover { background: var(--red-pale); color: var(--red); }
        .modal-close-btn svg { width: 15px; height: 15px; }
        .modal-body { padding: 20px 22px; overflow-y: auto; flex: 1; }
        .modal-loading { display: flex; align-items: center; justify-content: center; height: 120px; color: var(--gray-400); font-size: 13px; gap: 10px; }
        .spinner { width: 18px; height: 18px; border: 2px solid var(--gray-200); border-top-color: var(--blue); border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

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
            .summary-row { grid-template-columns: repeat(2, 1fr); }
            .filters { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline, .status-indicator { display: none; }
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider, .header-logos img:last-child, .header-org { display: none; }
            .header-title { font-size: 13px; line-height: 1.3; }
            .header-user-badge { padding: 5px 8px; }
            .user-avatar { width: 28px; height: 28px; font-size: 11px; }
            .user-name { font-size: 11px; }
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }
            .summary-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            .filters { grid-template-columns: 1fr; }
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
        <!-- Purple auditor badge — matches dashboard & family profile -->
        <div class="header-user-badge">
            <div class="user-avatar">A</div>
            <div>
                <div class="user-name">Auditor</div>
                <div class="user-role">View-Only Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR — identical structure to dashboard & family profile -->
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

        <!-- Active: Distribution Logs -->
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

        <a href="#" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Household
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
                <div class="page-breadcrumb">
                    <a href="{{ route('auditor.dashboard') }}">Home</a> / <span>Distribution Logs</span>
                </div>
                <div class="page-h1">Distribution Events</div>
                <div class="page-sub">View all relief distribution events — Barangay Family Track QR System</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-label">Total Events</div>
                <div class="summary-value">{{ $events->total() }}</div>
                <div class="summary-sub">All recorded events</div>
            </div>
            <div class="summary-card green">
                <div class="summary-label">Ongoing</div>
                <div class="summary-value">{{ $events->where('status','ongoing')->count() }}</div>
                <div class="summary-sub">Currently active</div>
            </div>
            <div class="summary-card orange">
                <div class="summary-label">Upcoming</div>
                <div class="summary-value">{{ $events->where('status','upcoming')->count() }}</div>
                <div class="summary-sub">Scheduled events</div>
            </div>
            <div class="summary-card yellow">
                <div class="summary-label">Completed</div>
                <div class="summary-value">{{ $events->where('status','completed')->count() }}</div>
                <div class="summary-sub">Finished events</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filter-box">
            <div class="filter-box-header">Filter Events</div>
            <form method="GET">
                <div class="filters">
                    <div class="filter-group">
                        <div class="filter-label">Search</div>
                        <input type="text" name="search" placeholder="Event name, relief type…" value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">From Date</div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">To Date</div>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">Status</div>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="upcoming"  {{ request('status')=='upcoming'  ? 'selected':'' }}>Upcoming</option>
                            <option value="ongoing"   {{ request('status')=='ongoing'   ? 'selected':'' }}>Ongoing</option>
                            <option value="completed" {{ request('status')=='completed' ? 'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-filter">Filter</button>
                    <a href="{{ route('auditor.distribution.logs') }}" class="btn-clear">Clear</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            <div class="table-header">
                <div class="table-title">
                    <div class="ca-dot"></div>
                    Distribution Events Log
                </div>
                <div class="table-count">{{ $events->total() }} event(s) found</div>
            </div>

            @if($events->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="empty-title">No distribution events found</div>
                    <div class="empty-sub">Try adjusting your filters or check back later.</div>
                </div>
            @else
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Event Name</th>
                                <th>Relief Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th style="text-align:center">Distributed</th>
                                <th style="text-align:center">Households</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $i => $event)
                                <tr>
                                    <td style="color:var(--gray-400); font-size:12px;">{{ $events->firstItem() + $i }}</td>
                                    <td>
                                        <div style="font-weight:600; color:var(--blue-dark);">{{ $event->event_name }}</div>
                                        @if($event->description)
                                            <div style="font-size:11px; color:var(--gray-400); margin-top:2px;">{{ Str::limit($event->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td style="font-size:12px; color:var(--gray-600);">{{ $event->relief_type ?? '—' }}</td>
                                    <td style="font-size:12px; white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                    </td>
                                    <td>
                                        @php $s = strtolower($event->status); @endphp
                                        <span class="badge badge-{{ $s }}">{{ ucfirst($s) }}</span>
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:var(--blue-dark);">
                                        {{ $event->total_distributed ?? 0 }}
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:var(--gray-600);">
                                        {{ $event->unique_households ?? 0 }}
                                    </td>
                                    <td>
                                        <button class="btn-view" onclick="openModal(
                                            '{{ route('auditor.distribution.events.households', $event) }}',
                                            '{{ htmlspecialchars(addslashes($event->event_name), ENT_QUOTES) }}',
                                            '{{ ucfirst(strtolower($event->status)) }}'
                                        )">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($events->hasPages())
                    <div class="pagination-wrap">
                        <div>Showing {{ $events->firstItem() }}–{{ $events->lastItem() }} of {{ $events->total() }}</div>
                        <div class="links">{{ $events->withQueryString()->links() }}</div>
                    </div>
                @endif
            @endif
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

<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div class="modal-head-left">
                <div class="modal-head-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <div>
                    <h2 id="modalTitle">Event Households</h2>
                    <div class="modal-head-sub" id="modalSub">Loading details…</div>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <div class="modal-loading">
                <div class="spinner"></div> Loading households…
            </div>
        </div>
    </div>
</div>

<script>
    /* ─── Clock ─── */
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

    /* ─── Sidebar ─── */
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

    /* ─── Modal ─── */
    let currentModalUrl = '';

    function openModal(url, eventName, status) {
        currentModalUrl = url;
        document.getElementById('modalTitle').textContent = eventName;
        document.getElementById('modalSub').textContent   = 'Status: ' + status + ' — Household recipients';
        document.getElementById('modalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
        loadModalContent(url);
    }

    function loadModalContent(url) {
        document.getElementById('modalBody').innerHTML = '<div class="modal-loading"><div class="spinner"></div> Loading households…</div>';
        fetch(url)
            .then(r => r.text())
            .then(html => {
                const doc     = new DOMParser().parseFromString(html, 'text/html');
                const content = doc.querySelector('.modal-body');
                document.getElementById('modalBody').innerHTML = content
                    ? content.innerHTML
                    : '<p style="color:var(--red);padding:20px;">Could not load content.</p>';

                const btn   = document.getElementById('modalSearchBtn');
                const input = document.getElementById('modalSearchInput');
                btn?.addEventListener('click',  () => modalSearch());
                input?.addEventListener('keydown', e => { if(e.key==='Enter') modalSearch(); });
            })
            .catch(() => {
                document.getElementById('modalBody').innerHTML = '<p style="color:var(--red);padding:20px;">Error loading households. Please try again.</p>';
            });
    }

    function modalSearch() {
        const search = document.getElementById('modalSearchInput')?.value ?? '';
        const url    = new URL(currentModalUrl, window.location.origin);
        url.searchParams.set('search', search);
        loadModalContent(url.toString());
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        document.body.style.overflow = '';
        currentModalUrl = '';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeSidebar(); closeModal(); }
    });
</script>
</body>
</html>