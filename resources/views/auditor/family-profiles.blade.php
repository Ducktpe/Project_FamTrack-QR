<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Family Profiles</title>
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
            --white:      #FFFFFF;
            --gray-50:    #F7F8FA;
            --gray-100:   #F0F2F5;
            --gray-200:   #DEE2E8;
            --gray-400:   #9AA3B0;
            --gray-600:   #5A6372;
            --gray-800:   #2C3340;
            --red:        #C0392B;
            --green:      #1A7A4A;
            --green-pale: #EAF5EF;
            --green-border:#A8D8BE;
            --purple:     #5B3FA6;
            --purple-dark:#3D1F8A;
            --purple-pale:#F5F0FF;
            --purple-border:#D8CBF5;
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
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── STATS ROW ─── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            padding: 16px 20px;
        }
        .stat-card.yellow { border-top-color: var(--yellow); }
        .stat-card.green  { border-top-color: var(--green); }
        .stat-card.purple { border-top-color: var(--purple); }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 6px; }
        .stat-value { font-family: 'PT Serif', serif; font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .stat-card.yellow .stat-value { color: var(--yellow-dark); }
        .stat-card.green  .stat-value { color: var(--green); }
        .stat-card.purple .stat-value { color: var(--purple); }
        .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        /* ─── FILTER / SEARCH BAR ─── */
        .filter-bar {
            background: var(--white);
            border: 1px solid var(--gray-200);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search {
            flex: 1;
            min-width: 200px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            padding: 0 12px;
            background: var(--gray-50);
        }
        .filter-search svg { width: 14px; height: 14px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search input { border: none; background: none; font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); padding: 9px 0; width: 100%; outline: none; }
        .filter-search input::placeholder { color: var(--gray-400); }
        .filter-select {
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            background: var(--gray-50);
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
            color: var(--gray-600);
            padding: 8px 10px;
            outline: none;
            cursor: pointer;
        }
        .filter-count {
            font-size: 12px;
            color: var(--gray-400);
            margin-left: auto;
            white-space: nowrap;
        }
        .filter-count strong { color: var(--blue); }

        /* ─── TABLE ─── */
        .table-wrap {
            background: var(--white);
            border: 1px solid var(--gray-200);
            overflow: hidden;
        }
        .table-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .th-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); }
        .th-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .th-badge { margin-left: auto; background: var(--blue-pale); color: var(--blue); font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 10px; border: 1px solid #C5D9F5; }

        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        thead tr { background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
        thead th { padding: 11px 16px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 12px 16px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }

        .serial-code {
            font-family: monospace;
            font-size: 11px;
            font-weight: 700;
            color: var(--blue);
            background: var(--blue-pale);
            padding: 3px 8px;
            border-radius: 3px;
            border: 1px solid #C5D9F5;
            white-space: nowrap;
        }

        .household-name { font-weight: 600; color: var(--blue-dark); }
        .household-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }

        .address-line { font-size: 12px; color: var(--gray-600); }

        .badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 10px;
            margin: 1px 2px 1px 0;
        }
        .badge-4ps    { background: #EAF5EF; color: #1A7A4A; border: 1px solid #A8D8BE; }
        .badge-pwd    { background: #FFF3E0; color: #BF6000; border: 1px solid #FFD08A; }
        .badge-senior { background: #EAF0FA; color: var(--blue); border: 1px solid #C5D9F5; }
        .badge-solo   { background: var(--purple-pale); color: var(--purple-dark); border: 1px solid var(--purple-border); }
        .badge-none   { background: var(--gray-100); color: var(--gray-400); border: 1px solid var(--gray-200); font-style: italic; }

        .status-active   { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: var(--green); }
        .status-active::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--green); }
        .status-inactive { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: var(--gray-400); }
        .status-inactive::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--gray-400); }

        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: 'Open Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: var(--blue);
            background: var(--blue-pale);
            border: 1px solid #C5D9F5;
            border-radius: 3px;
            padding: 6px 12px;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.12s, color 0.12s;
        }
        .view-btn:hover { background: var(--blue); color: var(--white); }
        .view-btn svg { width: 12px; height: 12px; }

        /* ─── EMPTY STATE ─── */
        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon-wrap { width: 48px; height: 48px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon-wrap svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── PAGINATION ─── */
        .pagination-row {
            padding: 12px 20px;
            border-top: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .pag-info { font-size: 12px; color: var(--gray-400); }
        .pag-links { display: flex; gap: 4px; }
        .pag-btn { font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; padding: 6px 12px; border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-600); border-radius: 3px; cursor: pointer; transition: background 0.12s; }
        .pag-btn:hover { background: var(--blue-pale); color: var(--blue); border-color: #C5D9F5; }
        .pag-btn.active { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .pag-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* ─── MODAL ─── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 500;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--white);
            width: 100%;
            max-width: 640px;
            max-height: 90vh;
            overflow-y: auto;
            border-top: 4px solid var(--yellow);
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .modal-head {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }
        .modal-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); }
        .modal-serial { font-size: 11px; color: var(--gray-400); margin-top: 4px; font-family: monospace; }
        .modal-close { background: none; border: none; cursor: pointer; padding: 4px; color: var(--gray-400); border-radius: 3px; transition: background 0.12s; flex-shrink: 0; }
        .modal-close:hover { background: var(--gray-100); color: var(--red); }
        .modal-close svg { width: 20px; height: 20px; }
        .modal-body { padding: 20px 24px; }
        .modal-section { margin-bottom: 20px; }
        .modal-section-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--blue-light); margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--blue-pale); }
        .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-field label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); display: block; margin-bottom: 4px; }
        .modal-field .field-val { font-size: 13px; color: var(--gray-800); font-weight: 500; }
        .modal-field .field-val.mono { font-family: monospace; font-size: 12px; }
        .modal-badges-wrap { display: flex; flex-wrap: wrap; gap: 6px; }
        .modal-foot { padding: 16px 24px; border-top: 1px solid var(--gray-100); background: var(--gray-50); display: flex; justify-content: flex-end; }
        .modal-close-btn { font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; background: var(--blue); color: var(--white); border: none; padding: 10px 24px; border-radius: 3px; cursor: pointer; transition: background 0.15s; }
        .modal-close-btn:hover { background: var(--blue-dark); }

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
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; }
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
            .stats-row { grid-template-columns: repeat(2, 1fr); }
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
            .stats-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            .modal-grid { grid-template-columns: 1fr; }
        }
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
        <a href="#" class="nav-item active" onclick="closeSidebar()">
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
            <div class="role-notice-text">You have view-only access. No records can be added, edited, or deleted.</div>
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
                    <a href="{{ route('auditor.dashboard') }}">Home</a> /
                    <span>Family Profiles</span>
                </div>
                <div class="page-h1">Family Profiles</div>
                <div class="page-sub">Registered household heads — Barangay Family Track QR System</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Households</div>
                <div class="stat-value">{{ $households->total() }}</div>
                <div class="stat-sub">Registered records</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-label">4Ps Beneficiaries</div>
                <div class="stat-value">{{ $households->getCollection()->where('is_4ps_beneficiary', 1)->count() }}</div>
                <div class="stat-sub">This page</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Active Status</div>
                <div class="stat-value">{{ $households->getCollection()->where('status', 'active')->count() }}</div>
                <div class="stat-sub">This page</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">With Beneficiary Tags</div>
                <div class="stat-value">{{ $households->getCollection()->filter(fn($h) => $h->is_pwd || $h->is_senior || $h->is_solo_parent)->count() }}</div>
                <div class="stat-sub">PWD / Senior / Solo</div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <span class="filter-label">Filter</span>
            <div class="filter-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Search household head, barangay, serial…" oninput="filterTable()">
            </div>
            <select class="filter-select" id="barangayFilter" onchange="filterTable()">
                <option value="">All Barangays</option>
                @foreach($households->getCollection()->pluck('barangay')->unique()->sort() as $brgy)
                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="statusFilter" onchange="filterTable()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <span class="filter-count">Showing <strong id="visibleCount">{{ $households->count() }}</strong> of {{ $households->total() }}</span>
        </div>

        <!-- TABLE -->
        <div class="table-wrap">
            <div class="table-header">
                <div class="th-dot"></div>
                <div class="th-title">Household Profiles</div>
                <span class="th-badge">{{ $households->total() }} Total Records</span>
            </div>
            <div class="table-scroll">
                <table id="profileTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial Code</th>
                            <th>Household Head</th>
                            <th>Sex / Birthday</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Beneficiary Tags</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($households as $index => $hh)
                        <tr class="profile-row"
                            data-name="{{ strtolower($hh->household_head_name) }}"
                            data-serial="{{ strtolower($hh->serial_code) }}"
                            data-barangay="{{ strtolower($hh->barangay) }}"
                            data-status="{{ $hh->status }}">
                            <td style="color:var(--gray-400);font-size:12px;">{{ $households->firstItem() + $index }}</td>
                            <td>
                                <span class="serial-code">{{ $hh->serial_code ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="household-name">{{ $hh->household_head_name }}</div>
                                <div class="household-sub">{{ ucfirst($hh->civil_status) }}</div>
                            </td>
                            <td>
                                <div style="font-size:13px;">{{ $hh->sex }}</div>
                                <div style="font-size:11px;color:var(--gray-400);">
                                    {{ $hh->birthday ? \Carbon\Carbon::parse($hh->birthday)->format('M d, Y') : '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="address-line">
                                    {{ collect([$hh->house_number, $hh->street_purok])->filter()->implode(', ') }}
                                </div>
                                <div class="address-line" style="color:var(--gray-400);">
                                    {{ collect([$hh->barangay, $hh->municipality])->filter()->implode(', ') }}
                                </div>
                            </td>
                            <td style="font-size:12px;color:var(--gray-600);">
                                {{ $hh->contact_number ?? '—' }}
                            </td>
                            <td>
                                @if($hh->is_4ps_beneficiary)
                                    <span class="badge badge-4ps">4Ps</span>
                                @endif
                                @if($hh->is_pwd)
                                    <span class="badge badge-pwd">PWD</span>
                                @endif
                                @if($hh->is_senior)
                                    <span class="badge badge-senior">Senior</span>
                                @endif
                                @if($hh->is_solo_parent)
                                    <span class="badge badge-solo">Solo Parent</span>
                                @endif
                                @if(!$hh->is_4ps_beneficiary && !$hh->is_pwd && !$hh->is_senior && !$hh->is_solo_parent)
                                    <span class="badge badge-none">None</span>
                                @endif
                            </td>
                            <td>
                                @if($hh->status === 'active')
                                    <span class="status-active">Active</span>
                                @else
                                    <span class="status-inactive">{{ ucfirst($hh->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <button class="view-btn" onclick="openModal({{ $hh->id }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon-wrap">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                    <div class="empty-title">No household records found</div>
                                    <div class="empty-sub">There are no registered family profiles in the system yet.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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

<!-- ─── PROFILE MODAL ─── -->
<div class="modal-overlay" id="modalOverlay" onclick="handleOverlayClick(event)">
    <div class="modal" id="profileModal">
        <div class="modal-head">
            <div>
                <div class="modal-title" id="modal-name">—</div>
                <div class="modal-serial" id="modal-serial">—</div>
            </div>
            <button class="modal-close" onclick="closeModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-section">
                <div class="modal-section-label">Personal Information</div>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>Sex</label>
                        <div class="field-val" id="modal-sex">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Birthday</label>
                        <div class="field-val" id="modal-birthday">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Civil Status</label>
                        <div class="field-val" id="modal-civil">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Contact Number</label>
                        <div class="field-val" id="modal-contact">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-label">Address</div>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>House No. / Purok</label>
                        <div class="field-val" id="modal-address">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Barangay</label>
                        <div class="field-val" id="modal-barangay">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Municipality</label>
                        <div class="field-val" id="modal-municipality">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Province</label>
                        <div class="field-val" id="modal-province">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-label">Beneficiary Classification</div>
                <div class="modal-badges-wrap" id="modal-badges">—</div>
            </div>
            <div class="modal-section">
                <div class="modal-section-label">System Information</div>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>Listahanan ID</label>
                        <div class="field-val mono" id="modal-listahanan">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Status</label>
                        <div class="field-val" id="modal-status">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Date Encoded</label>
                        <div class="field-val" id="modal-created">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Last Updated</label>
                        <div class="field-val" id="modal-updated">—</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="modal-close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

{{-- Pass household data as JSON for modal --}}
<script>
const householdsData = @json($households->getCollection()->keyBy('id'));
</script>

<script>
    // ─── Clock ───
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
    document.getElementById('hamburgerBtn').addEventListener('click', () => {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // ─── Filter ───
    function filterTable() {
        const search  = document.getElementById('searchInput').value.toLowerCase();
        const brgy    = document.getElementById('barangayFilter').value.toLowerCase();
        const status  = document.getElementById('statusFilter').value.toLowerCase();
        const rows    = document.querySelectorAll('.profile-row');
        let visible   = 0;
        rows.forEach(row => {
            const matchSearch = !search || row.dataset.name.includes(search) || row.dataset.serial.includes(search) || row.dataset.barangay.includes(search);
            const matchBrgy   = !brgy   || row.dataset.barangay === brgy;
            const matchStatus = !status || row.dataset.status === status;
            const show = matchSearch && matchBrgy && matchStatus;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        document.getElementById('visibleCount').textContent = visible;
    }

    // ─── Modal ───
    function openModal(id) {
        const hh = householdsData[id];
        if (!hh) return;

        document.getElementById('modal-name').textContent    = hh.household_head_name || '—';
        document.getElementById('modal-serial').textContent  = hh.serial_code ? 'Serial: ' + hh.serial_code : 'No serial code';
        document.getElementById('modal-sex').textContent     = hh.sex || '—';
        document.getElementById('modal-birthday').textContent= hh.birthday ? formatDate(hh.birthday) : '—';
        document.getElementById('modal-civil').textContent   = hh.civil_status ? capitalize(hh.civil_status) : '—';
        document.getElementById('modal-contact').textContent = hh.contact_number || '—';
        document.getElementById('modal-address').textContent = [hh.house_number, hh.street_purok].filter(Boolean).join(', ') || '—';
        document.getElementById('modal-barangay').textContent    = hh.barangay || '—';
        document.getElementById('modal-municipality').textContent= hh.municipality || '—';
        document.getElementById('modal-province').textContent    = hh.province || '—';
        document.getElementById('modal-listahanan').textContent  = hh.listahanan_id || '—';
        document.getElementById('modal-created').textContent = hh.created_at ? formatDate(hh.created_at) : '—';
        document.getElementById('modal-updated').textContent = hh.updated_at ? formatDate(hh.updated_at) : '—';

        // Status
        const statusEl = document.getElementById('modal-status');
        if (hh.status === 'active') {
            statusEl.innerHTML = '<span class="status-active">Active</span>';
        } else {
            statusEl.innerHTML = '<span class="status-inactive">' + capitalize(hh.status || 'Unknown') + '</span>';
        }

        // Badges
        const badgesEl = document.getElementById('modal-badges');
        let badges = '';
        if (hh.is_4ps_beneficiary) badges += '<span class="badge badge-4ps">4Ps Beneficiary</span>';
        if (hh.is_pwd)             badges += '<span class="badge badge-pwd">Person with Disability (PWD)</span>';
        if (hh.is_senior)          badges += '<span class="badge badge-senior">Senior Citizen</span>';
        if (hh.is_solo_parent)     badges += '<span class="badge badge-solo">Solo Parent</span>';
        badgesEl.innerHTML = badges || '<span class="badge badge-none">No beneficiary tags</span>';

        document.getElementById('modalOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    function handleOverlayClick(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function formatDate(str) {
        const d = new Date(str);
        if (isNaN(d)) return str;
        return d.toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
</body>
</html>