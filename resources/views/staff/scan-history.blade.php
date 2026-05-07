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
        /* ─── NAMECARD BADGE — green for Distribution Staff role ─── */
        .header-admin-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--green-pale); border: 1px solid #BBF7D0; border-radius: 4px; flex-shrink: 0; }
        .admin-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--green); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .admin-name { font-size: 13px; font-weight: 600; color: var(--green-dark); line-height: 1.2; }
        .admin-role { font-size: 10px; color: var(--green); text-transform: uppercase; letter-spacing: 0.5px; }

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
        .sidebar-close:hover { background: var(--red-pale); color: var(--red); }
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

        /* Scope combo */
        .search-combo { display: flex; align-items: stretch; border: 1px solid var(--gray-200); border-radius: 3px; overflow: hidden; background: var(--gray-50); transition: border-color 0.15s, box-shadow 0.15s; flex: 1; min-width: 260px; }
        .search-combo:focus-within { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.08); background: var(--white); }
        .search-combo .scope-sel { border: none !important; border-right: 1px solid var(--gray-200) !important; background: var(--gray-100) !important; padding: 8px 22px 8px 10px !important; font-size: 11px !important; font-weight: 700 !important; color: var(--gray-600) !important; font-family: 'Open Sans', sans-serif !important; outline: none !important; appearance: none !important; -webkit-appearance: none !important; cursor: pointer; flex-shrink: 0; min-width: 100px; width: auto !important; box-shadow: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important; background-position: right 6px center !important; }
        .search-combo .scope-sel:hover { background-color: var(--blue-pale) !important; color: var(--blue) !important; }
        .search-combo .srch-input { border: none !important; background: transparent !important; padding: 8px 10px !important; font-size: 13px !important; color: var(--gray-800) !important; font-family: 'Open Sans', sans-serif !important; outline: none !important; flex: 1; min-width: 0; width: auto !important; box-shadow: none !important; }
        .search-combo .srch-input::placeholder { color: var(--gray-400); }
        .search-combo .srch-clear { display: none; align-items: center; justify-content: center; padding: 0 10px; border: none !important; background: transparent !important; color: var(--gray-400); cursor: pointer; font-size: 18px; line-height: 1; width: auto !important; transition: color 0.12s; }
        .search-combo .srch-clear:hover { color: var(--red) !important; }
        .search-combo.has-value .srch-clear { display: flex; }

        /* Highlight */
        mark.hl { background: #FFF176; color: inherit; border-radius: 2px; padding: 0 1px; font-style: normal; }

        /* Active filter tags */
        .active-filters-row { padding: 7px 20px 10px; background: var(--white); border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .active-filters-row.hidden { display: none; }
        .filter-tag { display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; background: var(--blue-pale); border: 1px solid #C7D9F5; border-radius: 10px; font-size: 11px; color: var(--blue); font-weight: 600; }
        .filter-tag a { color: var(--blue); text-decoration: none; margin-left: 2px; opacity: 0.6; font-weight: 700; }
        .filter-tag a:hover { opacity: 1; }
        .clear-all-link { font-size: 11px; color: var(--red); text-decoration: none; font-weight: 600; margin-left: 4px; }
        .clear-all-link:hover { text-decoration: underline; }

        /* Filter toggle button */
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
        .filter-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; background: var(--blue-pale); color: var(--blue); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; border: 1px solid #C7D9F3; border-radius: 10px; }

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
        .table-wrapper { overflow-x: hidden; }
        table { width: 100%; border-collapse: collapse; }
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

        /* ─── MOBILE CARD ROW (hidden by default, shown on small screens) ─── */
        .mobile-card-list { display: none; }
        .mobile-card {
            padding: 14px 16px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--white);
        }
        .mobile-card:last-child { border-bottom: none; }
        .mobile-card:nth-child(even) { background: var(--gray-50); }
        .mobile-card-top {
            display: flex; align-items: flex-start;
            justify-content: space-between; gap: 10px; margin-bottom: 8px;
        }
        .mobile-card-name { font-size: 13px; font-weight: 700; color: var(--blue-dark); }
        .mobile-card-barangay { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .mobile-card-meta {
            display: grid; grid-template-columns: 1fr 1fr; gap: 6px 12px;
            margin-bottom: 10px;
        }
        .mobile-card-meta-item { display: flex; flex-direction: column; gap: 2px; }
        .mobile-card-meta-label {
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: var(--gray-400);
        }
        .mobile-card-meta-value { font-size: 12px; color: var(--gray-800); }
        .mobile-card-footer {
            display: flex; align-items: center;
            justify-content: space-between; gap: 8px;
        }
        .mobile-card-time { font-size: 11px; color: var(--gray-400); }
        .mobile-card-num {
            font-size: 10px; font-weight: 700; color: var(--gray-400);
            background: var(--gray-100); border-radius: 10px; padding: 2px 8px;
        }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;
        }
        .badge-success { background: var(--green-pale); color: var(--green-dark); }
        .badge svg { width: 10px; height: 10px; }

        /* ─── DETAILS BUTTON ─── */
        .btn-details {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--blue); background: var(--blue-pale);
            border: 1px solid #C7D9F5; border-radius: 3px;
            cursor: pointer; transition: background 0.15s, color 0.15s;
        }
        .btn-details:hover { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .btn-details svg { width: 12px; height: 12px; }

        /* ─── MODAL ─── */
        .modal-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 1000;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: var(--white); width: 100%; max-width: 520px;
            border-top: 4px solid var(--blue); box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            animation: modal-in 0.18s ease; display: flex; flex-direction: column;
            max-height: 90vh;
        }
        @keyframes modal-in { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px; border-bottom: 1px solid var(--gray-100);
        }
        .modal-title { font-family: 'PT Serif', serif; font-size: 16px; font-weight: 700; color: var(--blue-dark); }
        .modal-close {
            background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 3px;
            width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--gray-600); transition: background 0.15s;
        }
        .modal-close:hover { background: #FEF2F2; color: var(--red); border-color: #FECACA; }
        .modal-close svg { width: 14px; height: 14px; }
        .modal-body { padding: 20px; display: flex; flex-direction: column; gap: 0; overflow-y: auto; }
        .modal-row {
            display: grid; grid-template-columns: 140px 1fr; gap: 8px;
            padding: 10px 0; border-bottom: 1px solid var(--gray-100);
        }
        .modal-row:last-child { border-bottom: none; }
        .modal-row-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); padding-top: 2px; }
        .modal-row-value { font-size: 13px; color: var(--gray-800); line-height: 1.5; }
        .modal-row-value strong { color: var(--blue-dark); }
        .modal-tag {
            display: inline-block; padding: 2px 9px; border-radius: 10px; margin: 2px 3px 2px 0;
            font-size: 11px; font-weight: 600; background: var(--blue-pale); color: var(--blue);
            border: 1px solid #C7D9F5;
        }
        .modal-tag-green { background: var(--green-pale); color: var(--green-dark); border-color: #BBF7D0; }
        .relief-items-list { display: flex; flex-direction: column; gap: 5px; padding-top: 2px; }
        .relief-item-row {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 10px; background: var(--green-pale);
            border: 1px solid #BBF7D0; border-radius: 4px;
        }
        .relief-item-num {
            width: 18px; height: 18px; border-radius: 50%;
            background: var(--green); color: var(--white);
            font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .relief-item-name { font-size: 12px; font-weight: 600; color: var(--green-dark); flex: 1; }
        .relief-item-qty {
            font-size: 11px; color: var(--green-dark); opacity: 0.75;
            background: #BBF7D0; padding: 1px 7px; border-radius: 10px;
            font-weight: 600; white-space: nowrap;
        }

        /* ─── RELEASE PHOTO ─── */
        .modal-photo-block {
            margin-top: 4px; padding: 14px 0 2px;
            border-top: 2px dashed var(--gray-200);
        }
        .modal-photo-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; color: var(--gray-400); margin-bottom: 10px;
            display: flex; align-items: center; gap: 6px;
        }
        .modal-photo-label svg { width: 12px; height: 12px; color: var(--green); }
        .modal-photo-img {
            width: 100%; border-radius: 6px; border: 2px solid #BBF7D0;
            object-fit: cover; max-height: 240px; display: block;
        }
        .modal-photo-none {
            padding: 18px; text-align: center; background: var(--gray-50);
            border: 1px dashed var(--gray-200); border-radius: 4px;
            font-size: 12px; color: var(--gray-400); font-style: italic;
        }

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
        .pagination-row {
            padding: 14px 20px;
            border-top: 1px solid var(--gray-100);
            background: var(--gray-50);
        }

        /* ── Reset all SVGs inside pagination to a fixed small size ── */
        .pagination-row svg {
            width: 14px !important;
            height: 14px !important;
            flex-shrink: 0;
            display: block;
        }

        /* Laravel Tailwind pagination nav wrapper */
        .pagination-row nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Result count text */
        .pagination-row p,
        .pagination-row nav > p {
            font-size: 12px;
            color: var(--gray-400);
            line-height: 1.5;
        }
        .pagination-row p strong {
            color: var(--gray-600);
            font-weight: 600;
        }

        /* The flex row of page buttons */
        .pagination-row nav > div,
        .pagination-row .flex,
        .pagination-row span[role="group"] {
            display: flex;
            align-items: center;
            gap: 3px;
            flex-wrap: wrap;
        }

        /* Every anchor and disabled span in pagination */
        .pagination-row nav a,
        .pagination-row nav span > span,
        .pagination-row nav > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-600);
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            text-decoration: none;
            line-height: 1;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
            cursor: pointer;
        }

        /* Hover state */
        .pagination-row nav a:hover {
            background: var(--blue-pale);
            color: var(--blue);
            border-color: var(--blue-light);
        }

        /* Active / current page */
        .pagination-row nav span[aria-current="page"] > span,
        .pagination-row nav .active > span {
            background: var(--blue);
            color: var(--white);
            border-color: var(--blue);
            font-weight: 700;
        }

        /* Disabled prev / next */
        .pagination-row nav span[aria-disabled="true"],
        .pagination-row nav span[aria-disabled="true"] > span {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Ellipsis */
        .pagination-row nav span.dots,
        .pagination-row nav span[aria-hidden] {
            border: none;
            background: transparent;
            color: var(--gray-400);
            cursor: default;
            min-width: unset;
            padding: 0 4px;
        }

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
            .header-admin-badge { padding: 6px 10px; gap: 8px; }
            .admin-name { font-size: 12px; }
            .admin-role { display: none; }
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
            .header-admin-badge { padding: 5px 8px; }
            .admin-avatar { width: 28px; height: 28px; font-size: 11px; }
            .admin-name { font-size: 11px; }
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; gap: 10px; }
            .page-h1 { font-size: 18px; }
            .titlebar-actions { width: 100%; }
            .back-btn, .btn-scanner { flex: 1; justify-content: center; }
            .stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            /* Switch table → cards on mobile */
            .table-wrapper table { display: none; }
            .mobile-card-list { display: block; }
            /* Search bar stacks */
            .search-bar-wrap { flex-direction: column; align-items: stretch; }
            .search-combo { min-width: 0; }
            .btn-filter-toggle { width: 100%; justify-content: center; }
            /* Modal full-screen on mobile */
            .modal { max-width: 100%; margin: 0; border-radius: 0; max-height: 100vh; }
            .modal-backdrop { padding: 0; align-items: flex-end; }
            .modal-row { grid-template-columns: 110px 1fr; }
        }

        @media (max-width: 480px) {
            .shell { grid-template-rows: 28px 52px 1fr 40px; }
            .main-content { padding: 10px 8px; }
            .topbar { padding: 0 10px; }
            header { padding: 0 8px; }
            .header-title { font-size: 13px; }
        }

        @media (max-width: 380px) {
            .main-content { padding: 12px 10px; }
            .stats-row { grid-template-columns: 1fr; }
            .mobile-card-meta { grid-template-columns: 1fr; }
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
        <div class="header-admin-badge">
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="admin-name">{{ auth()->user()->name }}</div>
                <div class="admin-role">Distribution Staff</div>
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
            Scan History
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
                $activeFilters = array_filter([
                    'search'   => request('search'),
                    'event'    => request('event_id') ? 'Event #'.request('event_id') : null,
                    'date_from'=> request('date_from'),
                    'date_to'  => request('date_to'),
                ]);
            @endphp

            <form method="GET" action="{{ route('staff.scan.history') }}" id="searchForm">
                <input type="hidden" name="scope" id="scopeHidden" value="{{ request('scope','all') }}">
                @foreach(['event_id','date_from','date_to'] as $fp)
                    @if(request($fp))<input type="hidden" name="{{ $fp }}" value="{{ request($fp) }}">@endif
                @endforeach

                <div class="search-bar-wrap">
                    {{-- Scope combo --}}
                    <div class="search-combo" id="searchCombo">
                        <select class="scope-sel" id="scopeSelect" onchange="updateScope(this.value)">
                            <option value="all"    {{ request('scope','all')==='all'    ? 'selected':'' }}>All Fields</option>
                            <option value="name"   {{ request('scope')==='name'   ? 'selected':'' }}>Household Name</option>
                            <option value="serial" {{ request('scope')==='serial' ? 'selected':'' }}>Serial Code</option>
                            <option value="event"  {{ request('scope')==='event'  ? 'selected':'' }}>Event Name</option>
                        </select>
                        <input type="text" name="search" class="srch-input" id="searchInput"
                            placeholder="Search scan history..."
                            value="{{ request('search') }}" autocomplete="off"
                            oninput="onSearchType(this)"
                            onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('searchForm').submit();}">
                        <button type="button" class="srch-clear" onclick="clearSearch()" title="Clear">×</button>
                    </div>

                    {{-- Filter toggle --}}
                    <button class="btn-filter-toggle {{ $hasFilters ? 'active' : '' }}" id="filterToggleBtn" onclick="toggleFilter()" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        Filters
                        @if($hasFilters)<span style="background:#fff;color:var(--blue);border-radius:10px;font-size:10px;padding:1px 6px;margin-left:2px;">{{ count(array_filter([request('event_id'), request('date_from'), request('date_to')])) }}</span>@endif
                    </button>
                </div>
            </form>

            {{-- Active filter tags --}}
            @if(count($activeFilters))
            <div class="active-filters-row">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2.5" style="flex-shrink:0"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                @foreach($activeFilters as $key => $val)
                    <span class="filter-tag">
                        {{ ucfirst(str_replace('_',' ',$key)) }}: {{ $val }}
                        <a href="{{ request()->fullUrlWithQuery([$key === 'event' ? 'event_id' : $key => null]) }}">×</a>
                    </span>
                @endforeach
                <a href="{{ route('staff.scan.history') }}" class="clear-all-link">Clear all</a>
            </div>
            @endif

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
                            <th>Scanned As</th>
                            <th>Serial Code</th>
                            <th>Distribution Event</th>
                            <th>Distributed At</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                @php
                                        $toStr = fn($v) => is_null($v) ? '' : (is_array($v) ? implode(', ', array_map(fn($i) => is_array($i) ? json_encode($i) : (string)$i, $v)) : (string)$v);
                                        $reliefTypeStr  = $toStr($log->event->relief_type  ?? null);
                                        $goodsDetailStr = $toStr($log->goods_detail        ?? null);
                                        $remarksStr     = $toStr($log->remarks             ?? null);
                                        $barangayStr    = $toStr($log->household->barangay ?? null);

                                        // Use items_received from the log (object-keyed JSON: {canned_goods:{name,qty,unit},...})
                                        // Fallback to event->relief_items if items_received is empty
                                        $rawItems = $log->items_received ?? $log->event->relief_items ?? null;
                                        if (is_string($rawItems)) {
                                            $decoded = json_decode($rawItems, true);
                                            $rawItems = is_array($decoded) ? $decoded : null;
                                        }
                                        if (is_array($rawItems)) {
                                            $formattedItems = [];
                                            foreach ($rawItems as $key => $item) {
                                                if (is_array($item)) {
                                                    $name = $item['name'] ?? ucwords(str_replace('_', ' ', $key));
                                                    $qty  = $item['qty']  ?? '';
                                                    $unit = $item['unit'] ?? '';
                                                    $formattedItems[] = trim("{$name}" . ($qty ? " — {$qty} {$unit}" : ''));
                                                } else {
                                                    $formattedItems[] = (string) $item;
                                                }
                                            }
                                            $reliefItemsStr = implode('||', $formattedItems);
                                        } else {
                                            $reliefItemsStr = '';
                                        }
                                @endphp
                                <td style="color:var(--gray-400);font-size:12px;">
                                    {{ $logs->firstItem() + $loop->index }}
                                </td>
                                <td class="td-household">
                                    <strong class="hl-name">{{ $log->household->household_head_name ?? '—' }}</strong>
                                    <small>
                                        {{ $barangayStr }}
                                        @if($log->household)
                                            &mdash; {{ $log->household->total_members }} member(s)
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if($log->familyMember)
                                        <span style="display:inline-block;margin-bottom:3px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:1px 7px;border-radius:3px;background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;">
                                            Family Head
                                        </span>
                                        <div style="font-size:12px;font-weight:600;color:var(--blue-dark);">{{ $log->familyMember->full_name }}</div>
                                        <div style="font-size:10px;color:var(--gray-400);">{{ $log->familyMember->sex }}@if($log->familyMember->age), {{ $log->familyMember->age }} yrs @endif</div>
                                    @else
                                        <span style="display:inline-block;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:1px 7px;border-radius:3px;background:var(--blue-pale);color:var(--blue);border:1px solid #C7D9F5;">
                                            Household QR
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="serial-code hl-serial">{{ $log->serial_code }}</span>
                                </td>
                                <td>
                                    <div class="event-name">
                                        <span class="hl-event">{{ $log->event->event_name ?? '—' }}</span>
                                        @if($log->event && ($log->event->event_date ?? null))
                                            <small>{{ \Carbon\Carbon::parse($log->event->event_date)->format('M d, Y') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="timestamp">
                                        {{ $log->distributed_at->format('M d, Y') }}
                                        <small>{{ $log->distributed_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <button
                                        class="btn-details"
                                        onclick="openDetails({
                                            household:   {{ json_encode($log->household->household_head_name ?? '—') }},
                                            barangay:    {{ json_encode($barangayStr) }},
                                            members:     {{ json_encode($log->household->total_members ?? '—') }},
                                            serial:      {{ json_encode($log->serial_code) }},
                                            event:       {{ json_encode($log->event->event_name ?? '—') }},
                                            eventDate:   {{ json_encode($log->event && $log->event->event_date ? \Carbon\Carbon::parse($log->event->event_date)->format('M d, Y') : '—') }},
                                            reliefType:  {{ json_encode($reliefTypeStr) }},
                                            reliefItems: {{ json_encode($reliefItemsStr) }},
                                            goods:       {{ json_encode($goodsDetailStr) }},
                                            remarks:     {{ json_encode($remarksStr) }},
                                            distributedAt: {{ json_encode($log->distributed_at->format('M d, Y h:i A')) }},
                                            photoUrl: {{ json_encode($log->releasePhoto ? asset('storage/' . $log->releasePhoto->photo_path) : null) }},
                                            familyHeadName: {{ json_encode($log->familyMember->full_name ?? null) }},
                                            familyHeadSex:  {{ json_encode($log->familyMember->sex ?? null) }},
                                            familyHeadAge:  {{ json_encode($log->familyMember->age ?? null) }},
                                            scanMode: {{ json_encode($log->familyMember ? 'family_head' : 'household') }}
                                        })"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
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

            {{-- Mobile card list (shown on ≤640px instead of the table) --}}
            <div class="mobile-card-list">
                @forelse($logs as $log)
                    @php
                        $toStr = fn($v) => is_null($v) ? '' : (is_array($v) ? implode(', ', array_map(fn($i) => is_array($i) ? json_encode($i) : (string)$i, $v)) : (string)$v);
                        $reliefTypeStr  = $toStr($log->event->relief_type  ?? null);
                        $goodsDetailStr = $toStr($log->goods_detail        ?? null);
                        $remarksStr     = $toStr($log->remarks             ?? null);
                        $barangayStr    = $toStr($log->household->barangay ?? null);
                        // Use items_received from the log (object-keyed JSON: {canned_goods:{name,qty,unit},...})
                        // Fallback to event->relief_items if items_received is empty
                        $rawItems = $log->items_received ?? $log->event->relief_items ?? null;
                        if (is_string($rawItems)) {
                            $decoded = json_decode($rawItems, true);
                            $rawItems = is_array($decoded) ? $decoded : null;
                        }
                        if (is_array($rawItems)) {
                            $formattedItems = [];
                            foreach ($rawItems as $key => $item) {
                                if (is_array($item)) {
                                    $name = $item['name'] ?? ucwords(str_replace('_', ' ', $key));
                                    $qty  = $item['qty']  ?? '';
                                    $unit = $item['unit'] ?? '';
                                    $formattedItems[] = trim("{$name}" . ($qty ? " — {$qty} {$unit}" : ''));
                                } else {
                                    $formattedItems[] = (string) $item;
                                }
                            }
                            $reliefItemsStr = implode('||', $formattedItems);
                        } else {
                            $reliefItemsStr = '';
                        }
                    @endphp
                    <div class="mobile-card">
                        <div class="mobile-card-top">
                            <div>
                                <div class="mobile-card-name">{{ $log->household->household_head_name ?? '—' }}</div>
                                <div class="mobile-card-barangay">{{ $barangayStr }}@if($log->household) &mdash; {{ $log->household->total_members }} member(s)@endif</div>
                            </div>
                            <span class="mobile-card-num">#{{ $logs->firstItem() + $loop->index }}</span>
                        </div>
                        {{-- Scanned As badge --}}
                        <div style="margin-bottom:8px;">
                            @if($log->familyMember)
                                <span style="display:inline-block;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:1px 7px;border-radius:3px;background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;">Family Head QR</span>
                                <span style="margin-left:6px;font-size:12px;font-weight:600;color:var(--blue-dark);">{{ $log->familyMember->full_name }}</span>
                                <span style="margin-left:4px;font-size:10px;color:var(--gray-400);">{{ $log->familyMember->sex }}@if($log->familyMember->age), {{ $log->familyMember->age }} yrs @endif</span>
                            @else
                                <span style="display:inline-block;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:1px 7px;border-radius:3px;background:var(--blue-pale);color:var(--blue);border:1px solid #C7D9F5;">Household QR</span>
                            @endif
                        </div>
                        <div class="mobile-card-meta">
                            <div class="mobile-card-meta-item">
                                <span class="mobile-card-meta-label">Serial Code</span>
                                <span class="mobile-card-meta-value serial-code">{{ $log->serial_code }}</span>
                            </div>
                            <div class="mobile-card-meta-item">
                                <span class="mobile-card-meta-label">Date</span>
                                <span class="mobile-card-meta-value">{{ $log->distributed_at->format('M d, Y') }}<br><small style="color:var(--gray-400);">{{ $log->distributed_at->format('h:i A') }}</small></span>
                            </div>
                            <div class="mobile-card-meta-item" style="grid-column: 1 / -1;">
                                <span class="mobile-card-meta-label">Event</span>
                                <span class="mobile-card-meta-value">{{ $log->event->event_name ?? '—' }}@if($log->event && $log->event->event_date) <small style="color:var(--gray-400);"> · {{ \Carbon\Carbon::parse($log->event->event_date)->format('M d, Y') }}</small>@endif</span>
                            </div>
                        </div>
                        <div class="mobile-card-footer">
                            <span class="mobile-card-time">{{ $log->distributed_at->diffForHumans() }}</span>
                            <button
                                class="btn-details"
                                onclick="openDetails({
                                    household:   {{ json_encode($log->household->household_head_name ?? '—') }},
                                    barangay:    {{ json_encode($barangayStr) }},
                                    members:     {{ json_encode($log->household->total_members ?? '—') }},
                                    serial:      {{ json_encode($log->serial_code) }},
                                    event:       {{ json_encode($log->event->event_name ?? '—') }},
                                    eventDate:   {{ json_encode($log->event && $log->event->event_date ? \Carbon\Carbon::parse($log->event->event_date)->format('M d, Y') : '—') }},
                                    reliefType:  {{ json_encode($reliefTypeStr) }},
                                    reliefItems: {{ json_encode($reliefItemsStr) }},
                                    goods:       {{ json_encode($goodsDetailStr) }},
                                    remarks:     {{ json_encode($remarksStr) }},
                                    distributedAt: {{ json_encode($log->distributed_at->format('M d, Y h:i A')) }},
                                    photoUrl: {{ json_encode($log->releasePhoto ? asset('storage/' . $log->releasePhoto->photo_path) : null) }},
                                    familyHeadName: {{ json_encode($log->familyMember->full_name ?? null) }},
                                    familyHeadSex:  {{ json_encode($log->familyMember->sex ?? null) }},
                                    familyHeadAge:  {{ json_encode($log->familyMember->age ?? null) }},
                                    scanMode: {{ json_encode($log->familyMember ? 'family_head' : 'household') }}
                                })"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Details
                            </button>
                        </div>
                    </div>
                @empty
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
                                You haven't confirmed any distributions yet.
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
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

    <!-- DETAILS MODAL -->
    <div class="modal-backdrop" id="detailsModal" onclick="closeDetails(event)">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="modal-header">
                <div class="modal-title" id="modalTitle">Distribution Record Details</div>
                <button class="modal-close" onclick="closeDetailsBtn()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

</div>

<script>
    /* ── Search scope ── */
    const SEARCH_PH = {
        all:    'Search by household name or serial code…',
        name:   'Search by household name…',
        serial: 'Search by serial code…',
        event:  'Search by event name…',
    };
    function updateScope(val) {
        document.getElementById('scopeHidden').value = val;
        const inp = document.getElementById('searchInput');
        if (inp) inp.placeholder = SEARCH_PH[val] || 'Search…';
    }

    /* typing: highlight + clear btn only — submit on Enter */
    function onSearchType(input) {
        const combo = document.getElementById('searchCombo');
        if (combo) combo.classList.toggle('has-value', input.value.length > 0);
        applyHighlight(input.value, document.getElementById('scopeSelect').value);
    }
    function clearSearch() {
        const inp = document.getElementById('searchInput');
        if (!inp) return;
        inp.value = '';
        document.getElementById('searchCombo').classList.remove('has-value');
        removeHighlight();
        document.getElementById('searchForm').submit();
    }

    /* ── Live highlight ── */
    const HL_SCOPE = {
        all:    ['hl-name','hl-serial','hl-event'],
        name:   ['hl-name'],
        serial: ['hl-serial'],
        event:  ['hl-event'],
    };
    function _re(s) { return s.replace(/[-[\]{}()*+?.,\^$|#\s]/g, '\$&'); }
    function applyHighlight(term, scope) {
        removeHighlight();
        if (!term) return;
        const re = new RegExp('(' + _re(term) + ')', 'gi');
        (HL_SCOPE[scope] || HL_SCOPE.all).forEach(cls => {
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

    /* Init on load */
    (function() {
        const inp   = document.getElementById('searchInput');
        const scope = document.getElementById('scopeSelect');
        if (inp && inp.value) {
            document.getElementById('searchCombo').classList.add('has-value');
            applyHighlight(inp.value, scope ? scope.value : 'all');
        }
        if (scope) updateScope(scope.value);
    })();

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
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeSidebar(); closeDetailsBtn(); }
    });

    function toggleFilter() {
        const panel = document.getElementById('filterPanel');
        const btn   = document.getElementById('filterToggleBtn');
        const isOpen = panel.classList.contains('open');
        panel.classList.toggle('open', !isOpen);
        btn.classList.toggle('active', !isOpen);
    }

    /* ─── Details Modal ─── */
    function openDetails(d) {
        function row(label, value) {
            if (!value) return '';
            return `<div class="modal-row">
                <div class="modal-row-label">${label}</div>
                <div class="modal-row-value">${value}</div>
            </div>`;
        }
        function tags(str, cls) {
            if (!str) return '<span style="color:var(--gray-400);font-style:italic;">Not specified</span>';
            return str.split(',').map(s => `<span class="modal-tag ${cls || ''}">${s.trim()}</span>`).join('');
        }

        function reliefItemsList(str) {
            if (!str) return '<span style="color:var(--gray-400);font-style:italic;">Not specified</span>';
            const items = str.split('||').map(s => s.trim()).filter(Boolean);
            return items.map((item, i) => {
                const parts = item.split(' — ');
                const name  = parts[0] || item;
                const qty   = parts[1] || '';
                return `<div class="relief-item-row">
                    <span class="relief-item-num">${i + 1}</span>
                    <span class="relief-item-name">${name}</span>
                    ${qty ? `<span class="relief-item-qty">${qty}</span>` : ''}
                </div>`;
            }).join('');
        }

        let html = '';
        // QR / Scan mode badge
        if (d.scanMode === 'family_head') {
            html += `<div style="margin-bottom:12px;padding:8px 12px;background:#F0FDF4;border:1px solid #BBF7D0;border-left:3px solid #16A34A;border-radius:4px;display:flex;align-items:center;gap:10px;">
                <span style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:1px 7px;border-radius:3px;background:#16A34A;color:#fff;">Family Head QR</span>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#15803D;">${d.familyHeadName || '—'}</div>
                    ${d.familyHeadSex || d.familyHeadAge ? `<div style="font-size:10px;color:#15803D;opacity:0.75;">${[d.familyHeadSex, d.familyHeadAge ? d.familyHeadAge + ' yrs old' : null].filter(Boolean).join(', ')}</div>` : ''}
                </div>
            </div>`;
        } else {
            html += `<div style="margin-bottom:12px;padding:6px 12px;background:var(--blue-pale);border:1px solid #C7D9F5;border-left:3px solid var(--blue);border-radius:4px;">
                <span style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:1px 7px;border-radius:3px;background:var(--blue);color:#fff;">Household QR</span>
                <span style="margin-left:8px;font-size:11px;color:var(--blue-dark);">Scanned via household card</span>
            </div>`;
        }
        html += row('Household',     `<strong>${d.household}</strong>`);
        html += row('Barangay',      d.barangay);
        html += row('Members',       d.members ? `${d.members} member(s)` : null);
        html += row('Serial Code',   `<span style="font-family:monospace;font-weight:700;color:var(--blue);">${d.serial}</span>`);
        html += row('Event',         `<strong>${d.event}</strong>${d.eventDate ? `<br><span style="font-size:11px;color:var(--gray-400);">${d.eventDate}</span>` : ''}`);
        html += `<div class="modal-row">
            <div class="modal-row-label">Relief Type</div>
            <div class="modal-row-value">${tags(d.reliefType, '')}</div>
        </div>`;
        html += `<div class="modal-row">
            <div class="modal-row-label">Relief Items</div>
            <div class="modal-row-value relief-items-list">${reliefItemsList(d.reliefItems)}</div>
        </div>`;
        if (d.goods) html += row('Goods Detail', d.goods);
        if (d.remarks) html += row('Remarks', `<span style="color:var(--gray-600);">${d.remarks}</span>`);
        html += row('Distributed At', `<strong>${d.distributedAt}</strong>`);

        // Photo proof at the bottom
        html += `<div class="modal-photo-block">
            <div class="modal-photo-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
                Recipient Photo Proof
            </div>
            ${d.photoUrl
                ? `<img class="modal-photo-img" src="${d.photoUrl}" alt="Recipient photo proof">`
                : `<div class="modal-photo-none">No photo recorded for this release.</div>`
            }
        </div>`;

        document.getElementById('modalBody').innerHTML = html;
        document.getElementById('detailsModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailsBtn() {
        document.getElementById('detailsModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeDetails(e) {
        if (e.target === document.getElementById('detailsModal')) closeDetailsBtn();
    }
</script>
</body>
</html>