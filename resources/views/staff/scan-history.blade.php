<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — My Scan History</title>
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
            --red-pale:   #FEF2F2;
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
            width: 32px; height: 32px; border-radius: 50%; background: var(--green);
            display: flex; align-items: center; justify-content: center;
            color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: var(--green-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--green); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none !important;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); z-index: 200;
            opacity: 0; transition: opacity 0.25s; pointer-events: none;
        }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar; background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex; flex-direction: column; overflow-y: auto; position: relative;
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
            display: flex; align-items: center; gap: 12px; padding: 11px 20px;
            font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none;
            border-left: 3px solid transparent; transition: background 0.12s, color 0.12s, border-color 0.12s;
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
            width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white);
            border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s;
        }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        /* ─── PAGE TITLEBAR ─── */
        .page-titlebar {
            display: flex; align-items: flex-end; justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200); gap: 12px;
        }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .titlebar-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }

        .back-btn {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none;
            padding: 8px 16px; border: 1px solid var(--gray-200);
            background: var(--white); border-radius: 4px; transition: background 0.15s; white-space: nowrap;
        }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        .btn-scanner {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 16px; background: var(--green); color: var(--white);
            font-size: 12px; font-weight: 700; text-decoration: none; border-radius: 4px;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s; white-space: nowrap;
        }
        .btn-scanner:hover { background: var(--green-dark); }
        .btn-scanner svg { width: 14px; height: 14px; }

        /* ─── STATS ROW ─── */
        .stats-row { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .stat-card {
            background: var(--white); border: 1px solid var(--gray-200);
            padding: 18px 20px; display: flex; align-items: center; gap: 14px;
        }
        .stat-card.total    { border-top: 3px solid var(--blue); }
        .stat-card.today    { border-top: 3px solid var(--green); }
        .stat-card.events   { border-top: 3px solid var(--yellow-dark); }
        .stat-card.latest   { border-top: 3px solid var(--orange); }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 4px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-card.total  .stat-icon { background: var(--blue-pale); }
        .stat-card.today  .stat-icon { background: var(--green-pale); }
        .stat-card.events .stat-icon { background: #FFFBEB; }
        .stat-card.latest .stat-icon { background: var(--orange-pale); }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-card.total  .stat-icon svg { color: var(--blue); }
        .stat-card.today  .stat-icon svg { color: var(--green); }
        .stat-card.events .stat-icon svg { color: var(--yellow-dark); }
        .stat-card.latest .stat-icon svg { color: var(--orange); }
        .stat-number { font-family: 'PT Serif', serif; font-size: 28px; font-weight: 700; line-height: 1; margin-bottom: 2px; }
        .stat-card.total  .stat-number { color: var(--blue); }
        .stat-card.today  .stat-number { color: var(--green); }
        .stat-card.events .stat-number { color: var(--yellow-dark); }
        .stat-card.latest .stat-number { color: var(--orange); }
        .stat-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }

        /* ─── TABLE CARD ─── */
        .table-card { background: var(--white); border: 1px solid var(--gray-200); }
        .table-card-header {
            padding: 13px 20px; border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50); display: flex; align-items: center; justify-content: space-between;
        }
        .table-card-header-left { display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); }
        .table-section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }

        /* ─── SEARCH + FILTER BAR ─── */
        .search-bar-wrap {
            padding: 12px 20px; border-bottom: 1px solid var(--gray-100);
            background: var(--white); display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        }
        .search-form { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; }
        .search-input-wrap { position: relative; flex: 1; }
        .search-input-wrap svg {
            position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
            width: 14px; height: 14px; color: var(--gray-400); pointer-events: none;
        }
        .search-input {
            width: 100%; padding: 8px 12px 8px 32px;
            font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800);
            border: 1px solid var(--gray-200); border-radius: 3px;
            background: var(--gray-50); outline: none;
            transition: border-color 0.15s, background 0.15s;
        }
        .search-input:focus { border-color: var(--blue-light); background: var(--white); box-shadow: 0 0 0 3px rgba(36,89,168,0.08); }
        .search-input::placeholder { color: var(--gray-400); }
        .btn-search {
            display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px;
            background: var(--blue); color: var(--white); font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            border: none; border-radius: 3px; cursor: pointer; transition: background 0.15s; white-space: nowrap;
        }
        .btn-search:hover { background: var(--blue-dark); }
        .btn-search svg { width: 12px; height: 12px; }
        .btn-clear-search {
            display: inline-flex; align-items: center; gap: 5px; padding: 8px 12px;
            background: var(--white); color: var(--gray-600); font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
            border: 1px solid var(--gray-200); border-radius: 3px; text-decoration: none;
            transition: background 0.15s, color 0.15s; white-space: nowrap;
        }
        .btn-clear-search:hover { background: var(--gray-100); color: var(--red); border-color: var(--red); }
        .btn-clear-search svg { width: 11px; height: 11px; }

        /* ─── FILTER PANEL TOGGLE ─── */
        .btn-filter-toggle {
            display: inline-flex; align-items: center; gap: 7px; padding: 7px 14px;
            background: var(--white); color: var(--blue); font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            border: 1px solid var(--blue-light); border-radius: 3px;
            cursor: pointer; transition: background 0.15s, color 0.15s;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle.active { background: var(--blue); color: var(--white); }
        .btn-filter-toggle svg { width: 13px; height: 13px; }
        .filter-active-pills { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .filter-pill {
            display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px;
            background: var(--blue-pale); color: var(--blue);
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;
            border: 1px solid #C7D9F3; border-radius: 10px;
        }
        .btn-reset-filter {
            display: inline-flex; align-items: center; gap: 5px; padding: 7px 12px;
            background: var(--white); color: var(--gray-600); font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
            border: 1px solid var(--gray-200); border-radius: 3px; text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .btn-reset-filter:hover { background: #FEF2F2; color: var(--red); border-color: var(--red); }
        .btn-reset-filter svg { width: 11px; height: 11px; }
        .search-result-info { font-size: 11px; color: var(--gray-400); margin-top: 6px; width: 100%; }
        .search-result-info strong { color: var(--blue); }

        /* ─── FILTER PANEL ─── */
        .filter-panel {
            display: none; padding: 16px 20px;
            border-bottom: 2px solid var(--blue-pale); background: var(--gray-50);
        }
        .filter-panel.open { display: block; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; align-items: end; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); }
        .filter-select, .filter-date {
            padding: 7px 10px; font-family: 'Open Sans', sans-serif; font-size: 12px; color: var(--gray-800);
            border: 1px solid var(--gray-200); border-radius: 3px; background: var(--white);
            outline: none; transition: border-color 0.15s; width: 100%;
        }
        .filter-select:focus, .filter-date:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.08); }
        .btn-apply-filter {
            display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px;
            background: var(--blue); color: var(--white); font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            border: none; border-radius: 3px; cursor: pointer; transition: background 0.15s;
        }
        .btn-apply-filter:hover { background: var(--blue-dark); }
        .filter-actions { display: flex; align-items: flex-end; gap: 8px; justify-content: flex-end; }

        /* ─── TABLE ─── */
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        thead th {
            padding: 11px 14px; background: var(--blue); color: var(--white);
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px; text-align: left; white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 12px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        tbody tr:nth-child(even) td { background: var(--gray-50); }
        tbody tr:nth-child(even):hover td { background: var(--blue-pale); }

        .td-household strong { display: block; font-size: 13px; color: var(--blue-dark); font-weight: 600; }
        .td-household small { font-size: 11px; color: var(--gray-400); margin-top: 2px; display: block; }
        .serial-code { font-family: monospace; font-size: 12px; font-weight: 700; color: var(--blue); letter-spacing: 0.5px; }
        .event-name { font-size: 12px; color: var(--gray-800); }
        .event-name small { display: block; font-size: 11px; color: var(--gray-400); margin-top: 1px; }
        .goods-text { font-size: 12px; color: var(--gray-600); max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .goods-none { font-size: 11px; color: var(--gray-400); font-style: italic; }
        .timestamp { font-size: 12px; color: var(--gray-800); }
        .timestamp small { display: block; font-size: 11px; color: var(--gray-400); margin-top: 1px; }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;
        }
        .badge-success { background: var(--green-pale); color: var(--green-dark); }
        .badge svg { width: 10px; height: 10px; }

        /* ─── EMPTY STATE ─── */
        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon {
            width: 52px; height: 52px; border-radius: 50%; background: var(--gray-100);
            display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
        }
        .empty-icon svg { width: 24px; height: 24px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); margin-bottom: 18px; }

        /* ─── PAGINATION ─── */
        .pagination-row { padding: 14px 20px; border-top: 1px solid var(--gray-100); background: var(--gray-50); }

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
                grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0;
                width: var(--sidebar-w); z-index: 300; transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
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
            .stats-row { grid-template-columns: 1fr 1fr; }
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
            .page-titlebar { flex-direction: column; align-items: flex-start; gap: 10px; }
            .page-h1 { font-size: 18px; }
            .titlebar-actions { width: 100%; }
            .back-btn, .btn-scanner { flex: 1; justify-content: center; }
            .stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        @media (max-width: 380px) {
            .main-content { padding: 12px 10px; }
            .stats-row { grid-template-columns: 1fr; }
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
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="nav-section-label">Staff Menu</div>

        <a href="{{ route('staff.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('staff.scan') }}" class="nav-item scanner-primary" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                <rect x="8" y="8" width="8" height="8" rx="1"/>
            </svg>
            Open QR Scanner
            <span class="nav-badge-scan">Active</span>
        </a>

        <hr class="sidebar-sep">
        <div class="nav-section-label">My Activity</div>

        <a href="{{ route('staff.scan.history') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            My Scan History
        </a>

        <a href="{{ route('staff.active-event') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
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

        <!-- Page title -->
        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">Staff / <span>My Scan History</span></div>
                <div class="page-h1">My Scan History</div>
                <div class="page-sub">A log of every QR scan and release confirmation you have processed</div>
            </div>
            <div class="titlebar-actions">
                <a href="{{ route('staff.dashboard') }}" class="back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('staff.scan') }}" class="btn-scanner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                        <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                        <rect x="8" y="8" width="8" height="8" rx="1"/>
                    </svg>
                    Open Scanner
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-card total">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $totalScans }}</div>
                    <div class="stat-label">Total Scans</div>
                </div>
            </div>
            <div class="stat-card today">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                        <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                        <rect x="8" y="8" width="8" height="8" rx="1"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $todayScans }}</div>
                    <div class="stat-label">Today's Scans</div>
                </div>
            </div>
            <div class="stat-card events">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $totalEvents }}</div>
                    <div class="stat-label">Events Worked</div>
                </div>
            </div>
            <div class="stat-card latest">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number" style="font-size:16px; padding-top:4px;">
                        {{ $lastScanAt ? $lastScanAt->format('M d') : '—' }}
                    </div>
                    <div class="stat-label">Last Scan Date</div>
                </div>
            </div>
        </div>

        {{-- Table card --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="ca-dot"></div>
                    <div class="table-section-title">Distribution Log — My Records</div>
                </div>
            </div>

            {{-- Search + Filter bar --}}
            @php
                $hasFilters = request()->hasAny(['event_id', 'date_from', 'date_to']);
            @endphp
            <div class="search-bar-wrap">
                <form method="GET" action="{{ route('staff.scan.history') }}" class="search-form">
                    <div class="search-input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input
                            type="text" name="search" class="search-input"
                            placeholder="Search by household name or serial code…"
                            value="{{ request('search') }}" autocomplete="off"
                        >
                    </div>
                    <button type="submit" class="btn-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('staff.scan.history') }}" class="btn-clear-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Clear
                        </a>
                    @endif
                </form>

                <button class="btn-filter-toggle {{ $hasFilters ? 'active' : '' }}" id="filterToggleBtn" onclick="toggleFilter()" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filters{{ $hasFilters ? ' (Active)' : '' }}
                </button>

                @if($hasFilters)
                    <div class="filter-active-pills">
                        @if(request('event_id'))
                            <span class="filter-pill">Event filtered</span>
                        @endif
                        @if(request('date_from') || request('date_to'))
                            <span class="filter-pill">
                                {{ request('date_from') ?: '…' }} → {{ request('date_to') ?: '…' }}
                            </span>
                        @endif
                        <a href="{{ route('staff.scan.history', array_filter(['search' => request('search')])) }}" class="btn-reset-filter">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Reset
                        </a>
                    </div>
                @endif

                @if(request('search'))
                    <div class="search-result-info">
                        Showing results for <strong>"{{ request('search') }}"</strong> — {{ $logs->total() }} record(s) found
                    </div>
                @endif
            </div>

            {{-- Filter panel --}}
            <div class="filter-panel {{ $hasFilters ? 'open' : '' }}" id="filterPanel">
                <form method="GET" action="{{ route('staff.scan.history') }}" id="filterForm">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <div class="filter-grid">

                        {{-- Event --}}
                        <div class="filter-group">
                            <label class="filter-label">Distribution Event</label>
                            <select name="event_id" class="filter-select">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->event_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date from --}}
                        <div class="filter-group">
                            <label class="filter-label">Distributed From</label>
                            <input type="date" name="date_from" class="filter-date" value="{{ request('date_from') }}">
                        </div>

                        {{-- Date to --}}
                        <div class="filter-group">
                            <label class="filter-label">Distributed To</label>
                            <input type="date" name="date_to" class="filter-date" value="{{ request('date_to') }}">
                        </div>

                        <div class="filter-group filter-actions">
                            <button type="submit" class="btn-apply-filter">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Household</th>
                            <th>Serial Code</th>
                            <th>Distribution Event</th>
                            <th>Goods / Relief</th>
                            <th>Distributed At</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td style="color:var(--gray-400);font-size:12px;">
                                    {{ $logs->firstItem() + $loop->index }}
                                </td>
                                <td class="td-household">
                                    <strong>{{ $log->household->household_head_name ?? '—' }}</strong>
                                    <small>
                                        {{ $log->household->barangay ?? '' }}
                                        @if($log->household)
                                            &mdash; {{ $log->household->total_members }} member(s)
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="serial-code">{{ $log->serial_code }}</span>
                                </td>
                                <td>
                                    <div class="event-name">
                                        {{ $log->event->event_name ?? '—' }}
                                        @if($log->event && $log->event->event_date ?? null)
                                            <small>{{ \Carbon\Carbon::parse($log->event->event_date)->format('M d, Y') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($log->goods_detail)
                                        <span class="goods-text" title="{{ $log->goods_detail }}">{{ $log->goods_detail }}</span>
                                    @else
                                        <span class="goods-none">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="timestamp">
                                        {{ $log->distributed_at->format('M d, Y') }}
                                        <small>{{ $log->distributed_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($log->remarks)
                                        <span style="font-size:12px;color:var(--gray-600);">{{ $log->remarks }}</span>
                                    @else
                                        <span style="font-size:11px;color:var(--gray-400);font-style:italic;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                                            </svg>
                                        </div>
                                        <div class="empty-title">No scan records found</div>
                                        <div class="empty-sub">
                                            @if(request('search') || $hasFilters)
                                                No records match your current search or filters.
                                            @else
                                                You haven't confirmed any distributions yet. Open the scanner to get started.
                                            @endif
                                        </div>
                                        <a href="{{ route('staff.scan') }}" class="btn-scanner">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                                                <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                                                <rect x="8" y="8" width="8" height="8" rx="1"/>
                                            </svg>
                                            Open Scanner
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-row">
                {{ $logs->withQueryString()->links() }}
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

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    function toggleFilter() {
        const panel = document.getElementById('filterPanel');
        const btn   = document.getElementById('filterToggleBtn');
        const isOpen = panel.classList.contains('open');
        panel.classList.toggle('open', !isOpen);
        btn.classList.toggle('active', !isOpen);
    }
</script>
</body>
</html>