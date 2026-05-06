<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — My Registered Households</title>
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
            font-family: 'Open Sans', sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            font-size: 14px;
            overflow-x: clip;
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
            height: 100vh; overflow: hidden; }

        /* ─── TOP UTILITY BAR ─── */
        .topbar {
            grid-area: topbar;
            background: var(--blue-dark);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 100;
        }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline {
            font-size: 12px; font-weight: 600;
            color: var(--yellow); letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
        }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.45);
        }
        .status-indicator::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: #4CAF50; box-shadow: 0 0 5px #4CAF50;
            animation: blink 2s infinite;
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
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            margin-left: -4px;
            border-radius: 4px;
            color: var(--blue-dark);
            flex-shrink: 0;
            transition: background 0.15s;
        }
        .hamburger:hover { background: var(--blue-pale); }
        .hamburger svg { width: 22px; height: 22px; display: block; }

        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400); margin-bottom: 2px;
        }
        .header-title {
            font-family: 'PT Serif', serif;
            font-size: 18px; font-weight: 700; color: var(--blue-dark);
        }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }

                /* ─── PROFILE BADGE ─── */
        .header-user-badge {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px;
            background: #FFF7ED;
            border: 1px solid #D97706; border-radius: 4px;
            flex-shrink: 1; min-width: 0; overflow: hidden;
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #D97706;
            display: flex; align-items: center; justify-content: center;
            color: #FFFFFF; font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .user-name {
            font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-role {
            font-size: 10px; color: #D97706; text-transform: uppercase; letter-spacing: 0.5px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: block;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 200;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s, visibility 0.25s;
            pointer-events: none;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* ─── SIDEBAR ─── */
        

        .sidebar-close {
            display: none;
            position: absolute;
            top: 12px; right: 12px;
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: 4px;
            width: 32px; height: 32px;
            align-items: center; justify-content: center;
            cursor: pointer; z-index: 10;
            color: var(--gray-600);
            transition: background 0.15s;
        }
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
        .sidebar-close svg { width: 16px; height: 16px; }

        .nav-section-label {
            padding: 18px 20px 8px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: var(--gray-400);
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 20px;
            font-size: 13.5px; font-weight: 500;
            color: var(--gray-600); text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
        }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; color: inherit; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }

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

        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }

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
            margin-top: auto; padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
        }
        .logout-btn {
            width: 100%;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            background: var(--blue); color: var(--white);
            border: none; padding: 10px 16px; border-radius: 4px;
            cursor: pointer; display: flex; align-items: center;
            justify-content: center; gap: 8px; transition: background 0.15s;
        }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content {
            grid-area: main;
            background: var(--gray-50);
            padding: 28px 32px;
            overflow-y: auto;
        }

        .page-titlebar {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
            gap: 12px;
        }
        .page-breadcrumb {
            font-size: 11px; color: var(--gray-400);
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;
        }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 {
            font-family: 'PT Serif', serif;
            font-size: 22px; font-weight: 700; color: var(--blue-dark);
        }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .titlebar-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }

        .alert-success {
            background: var(--green-pale);
            border: 1px solid #BBF7D0; border-left: 4px solid var(--green);
            padding: 12px 16px; margin-bottom: 16px;
            font-size: 13px; color: var(--green-dark);
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success svg { width: 16px; height: 16px; flex-shrink: 0; }

        .stats-row {
            display: grid; grid-template-columns: 1fr 1fr 1fr;
            gap: 16px; margin-bottom: 20px;
        }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            padding: 20px 24px;
            display: flex; align-items: center; gap: 16px;
        }
        .stat-card.total   { border-top: 3px solid var(--blue); }
        .stat-card.pending { border-top: 3px solid var(--orange); }
        .stat-card.approved{ border-top: 3px solid var(--green); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 4px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-card.total   .stat-icon { background: var(--blue-pale); }
        .stat-card.pending .stat-icon { background: var(--orange-pale); }
        .stat-card.approved .stat-icon { background: var(--green-pale); }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-card.total   .stat-icon svg { color: var(--blue); }
        .stat-card.pending .stat-icon svg { color: var(--orange); }
        .stat-card.approved .stat-icon svg { color: var(--green); }
        .stat-number {
            font-family: 'PT Serif', serif;
            font-size: 34px; font-weight: 700; line-height: 1; margin-bottom: 3px;
        }
        .stat-card.total   .stat-number { color: var(--blue); }
        .stat-card.pending .stat-number { color: var(--orange); }
        .stat-card.approved .stat-number { color: var(--green); }
        .stat-label {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400);
        }

        .table-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .table-card-header {
            padding: 13px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; justify-content: space-between;
        }
        .table-card-header-left { display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); }
        .table-section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }

        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; max-width: 100%; }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 11px 14px;
            background: var(--blue); color: var(--white);
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            text-align: left; white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 12px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        tbody tr:nth-child(even) td { background: var(--gray-50); }
        tbody tr:nth-child(even):hover td { background: var(--blue-pale); }

        .td-name strong { display: block; font-size: 13px; color: var(--blue-dark); }
        .td-name small { font-size: 11px; color: var(--gray-400); margin-top: 2px; display: block; }
        .td-address small { font-size: 11px; color: var(--gray-400); display: block; margin-top: 1px; }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 10px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .badge-approved { background: var(--green-pale); color: var(--green-dark); }
        .badge-pending  { background: var(--orange-pale); color: #92400E; }
        .badge svg { width: 10px; height: 10px; }

        .serial-code { font-size: 12px; font-weight: 700; color: var(--blue); font-family: monospace; letter-spacing: 0.5px; }
        .serial-none { font-size: 11px; color: var(--gray-400); font-style: italic; }

        .action-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
        }
        .btn-view {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px;
            background: var(--blue); color: var(--white);
            font-size: 11px; font-weight: 600;
            text-decoration: none; border-radius: 3px;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-view:hover { background: var(--blue-dark); }
        .btn-view svg { width: 12px; height: 12px; }

        .btn-edit {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px;
            background: var(--orange); color: var(--white);
            font-size: 11px; font-weight: 600;
            text-decoration: none; border-radius: 3px;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-edit:hover { background: #B45309; }
        .btn-edit svg { width: 12px; height: 12px; }

        .btn-register {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px;
            background: var(--green); color: var(--white);
            font-size: 12px; font-weight: 700;
            text-decoration: none; border-radius: 4px;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-register:hover { background: var(--green-dark); }
        .btn-register svg { width: 14px; height: 14px; }

        .back-btn {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 600;
            color: var(--blue); text-decoration: none;
            padding: 8px 16px;
            border: 1px solid var(--gray-200);
            background: var(--white); border-radius: 4px;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        .encoder-note {
            background: var(--blue-pale);
            border: 1px solid #C7D9F3;
            border-left: 4px solid var(--blue-light);
            padding: 12px 16px; margin-bottom: 20px;
            font-size: 12px; color: var(--blue);
            display: flex; align-items: flex-start; gap: 10px;
        }
        .encoder-note svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }

        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--gray-100);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .empty-icon svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); margin-bottom: 16px; }

        /* ── Search bar wrap ── */
        .search-bar-wrap {
            padding: 12px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--white);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ── Search scope combo ── */
        .search-combo {
            display: flex; align-items: stretch;
            border: 1px solid var(--gray-200); border-radius: 3px;
            overflow: hidden; background: var(--gray-50);
            transition: border-color 0.15s, box-shadow 0.15s;
            flex: 1; min-width: 260px;
        }
        .search-combo:focus-within {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(36,89,168,0.08);
            background: var(--white);
        }
        .search-combo .scope-sel {
            border: none !important; border-right: 1px solid var(--gray-200) !important;
            background: var(--gray-100) !important;
            padding: 8px 22px 8px 10px !important;
            font-size: 11px !important; font-weight: 700 !important;
            color: var(--gray-600) !important;
            font-family: 'Open Sans', sans-serif !important;
            outline: none !important; appearance: none !important; -webkit-appearance: none !important;
            cursor: pointer; flex-shrink: 0; min-width: 100px; width: auto !important;
            box-shadow: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important; background-position: right 6px center !important;
        }
        .search-combo .scope-sel:hover { background-color: var(--blue-pale) !important; color: var(--blue) !important; }
        .search-combo .srch-input {
            border: none !important; background: transparent !important;
            padding: 8px 10px !important; font-size: 13px !important;
            color: var(--gray-800) !important;
            font-family: 'Open Sans', sans-serif !important;
            outline: none !important; flex: 1; min-width: 0; width: auto !important;
            box-shadow: none !important;
        }
        .search-combo .srch-input::placeholder { color: var(--gray-400); }
        .search-combo .srch-clear {
            display: none; align-items: center; justify-content: center;
            padding: 0 10px; border: none !important; background: transparent !important;
            color: var(--gray-400); cursor: pointer; font-size: 18px; line-height: 1;
            width: auto !important; transition: color 0.12s;
        }
        .search-combo .srch-clear:hover { color: var(--red) !important; background: transparent !important; }
        .search-combo.has-value .srch-clear { display: flex; }

        /* Highlight */
        mark.hl { background: #FFF176; color: inherit; border-radius: 2px; padding: 0 1px; font-style: normal; }

        /* Active filter tags */
        .active-filters-row {
            display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
            padding: 6px 20px 10px;
            background: var(--white);
            border-bottom: 1px solid var(--gray-100);
        }
        .active-filters-row.hidden { display: none; }
        .filter-tag {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 8px; background: var(--blue-pale);
            border: 1px solid #C7D9F5; border-radius: 10px;
            font-size: 11px; color: var(--blue); font-weight: 600;
        }
        .filter-tag a { color: var(--blue); text-decoration: none; margin-left: 2px; opacity: 0.6; font-weight: 700; }
        .filter-tag a:hover { opacity: 1; }
        .clear-all-link { font-size: 11px; color: var(--red); text-decoration: none; font-weight: 600; margin-left: 4px; }
        .clear-all-link:hover { text-decoration: underline; }

        .btn-filter-toggle {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 7px 14px;
            background: var(--white); color: var(--blue);
            font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            border: 1px solid var(--blue-light); border-radius: 3px;
            cursor: pointer; transition: background 0.15s, color 0.15s;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle.active { background: var(--blue); color: var(--white); }
        .btn-filter-toggle svg { width: 13px; height: 13px; }
        .filter-active-pills { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; flex: 1; }
        .filter-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; background: var(--blue-pale); color: var(--blue);
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;
            border: 1px solid #C7D9F3; border-radius: 10px;
        }
        .filter-pill svg { width: 10px; height: 10px; opacity: 0.7; }

        .filter-panel {
            display: none;
            padding: 18px 20px;
            border-bottom: 2px solid var(--blue-pale);
            background: var(--gray-50);
        }
        .filter-panel.open { display: block; }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px;
            align-items: end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--gray-400);
        }
        .filter-select, .filter-date {
            padding: 7px 10px;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; color: var(--gray-800);
            border: 1px solid var(--gray-200); border-radius: 3px;
            background: var(--white); outline: none;
            transition: border-color 0.15s;
            width: 100%;
        }
        .filter-select:focus, .filter-date:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(36,89,168,0.08);
        }

        .filter-checkboxes {
            display: flex; flex-direction: column; gap: 6px;
        }
        .filter-checkbox-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: var(--gray-800);
            cursor: pointer; user-select: none;
        }
        .filter-checkbox-label input[type="checkbox"] {
            width: 14px; height: 14px;
            accent-color: var(--blue);
            cursor: pointer;
            flex-shrink: 0;
        }
        .filter-checkbox-label:hover { color: var(--blue); }

        .filter-actions {
            display: flex; align-items: flex-end; gap: 8px; justify-content: flex-end;
        }
        .btn-apply-filter {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 16px;
            background: var(--blue); color: var(--white);
            font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            border: none; border-radius: 3px;
            cursor: pointer; transition: background 0.15s;
        }
        .btn-apply-filter:hover { background: var(--blue-dark); }
        .btn-apply-filter svg { width: 12px; height: 12px; }
        .btn-reset-filter {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 12px;
            background: var(--white); color: var(--gray-600);
            font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            border: 1px solid var(--gray-200); border-radius: 3px;
            text-decoration: none; transition: background 0.15s, color 0.15s;
        }
        .btn-reset-filter:hover { background: #FEF2F2; color: var(--red); border-color: var(--red); }
        .btn-reset-filter svg { width: 11px; height: 11px; }

        /* ── Pagination ── */
        .pagination-row { padding: 14px 20px; border-top: 1px solid var(--gray-200); background: var(--white); }
        .pagination-row nav { display: none; }
        .pg-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .pg-info { font-size: 12px; color: var(--gray-400); }
        .pg-info strong { color: var(--blue-dark); font-weight: 700; }
        .pg-controls { display: flex; align-items: center; gap: 4px; }
        .pg-btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 34px; min-width: 34px; padding: 0 10px;
            font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600;
            border-radius: 6px; border: 1.5px solid var(--gray-200);
            background: var(--white); color: var(--gray-600);
            text-decoration: none; cursor: pointer; line-height: 1; gap: 5px;
            transition: background 0.13s, color 0.13s, border-color 0.13s, box-shadow 0.13s;
            white-space: nowrap;
        }
        .pg-btn:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); box-shadow: 0 2px 5px rgba(27,63,122,0.10); }
        .pg-btn.active { background: var(--blue); color: var(--white); border-color: var(--blue); box-shadow: 0 2px 8px rgba(27,63,122,0.20); font-weight: 700; pointer-events: none; }
        .pg-btn.nav-btn { padding: 0 14px; color: var(--blue); background: var(--blue-pale); border-color: var(--blue-light); font-weight: 700; }
        .pg-btn.nav-btn:hover { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .pg-btn.disabled { color: var(--gray-400); background: var(--gray-100); border-color: var(--gray-200); cursor: not-allowed; pointer-events: none; box-shadow: none; }
        .pg-dots { display: inline-flex; align-items: center; justify-content: center; height: 34px; min-width: 24px; font-size: 13px; color: var(--gray-400); letter-spacing: 1px; padding: 0 2px; }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 24px; gap: 8px; position: relative; z-index: 400;
            height: 48px;
        }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.4);
            text-decoration: none; transition: color 0.15s;
            white-space: nowrap;
        }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        @media (max-width: 900px) {
            .shell {
                grid-template-rows: 36px auto 1fr 48px;
                grid-template-columns: 1fr;
                grid-template-areas:
                    "topbar"
                    "header"
                    "main"
                    "footer";
                height: 100vh;
                overflow: hidden;
            }
            /* sidebar-overlay shown only via .active class */
            .hamburger { display: flex; }
            header { padding: 0 16px; gap: 10px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .header-user-badge { padding: 6px 10px; gap: 8px; }
            .user-name { font-size: 12px; }
            .user-role { font-size: 9px; letter-spacing: 0.3px; }
            .user-avatar { width: 28px; height: 28px; font-size: 11px; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .main-content { padding: 20px 16px; overflow-y: auto; }
            .stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-card { padding: 14px 16px; gap: 12px; }
            .stat-number { font-size: 26px; }
            .stat-icon { width: 36px; height: 36px; }
            .stat-icon svg { width: 18px; height: 18px; }
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
            .main-content { padding: 16px 12px; overflow-y: auto; }
            .page-titlebar { flex-direction: column; align-items: flex-start; gap: 10px; }
            .page-h1 { font-size: 18px; }
            .titlebar-actions { width: 100%; }
            .back-btn, .btn-register { flex: 1; justify-content: center; }
            .stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-card { padding: 14px 16px; gap: 12px; }
            .stat-number { font-size: 26px; }
            .stat-icon { width: 36px; height: 36px; }
            .stat-icon svg { width: 18px; height: 18px; }
            .encoder-note { font-size: 11px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            /* ── Mobile: hide table, show cards ── */
            .table-wrapper { display: none !important; }
            .hh-cards { display: flex !important; }
        }

        @media (max-width: 480px) {
            .shell { grid-template-rows: 28px 52px 1fr 40px; }
            .main-content { padding: 10px 8px; overflow-y: auto; }
            .topbar { padding: 0 10px; }
            header { padding: 0 8px; }
            .header-title { font-size: 12px; }
            .stats-row { grid-template-columns: 1fr 1fr; gap: 8px; }
        }
        @media (max-width: 380px) {
            .main-content { padding: 12px 10px; overflow-y: auto; }
        }
    
        /* ── Household mobile cards (hidden on desktop) ── */
        .hh-cards {
            display: none;
            flex-direction: column;
            gap: 10px;
        }
        .hh-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-left: 3px solid var(--blue);
            border-radius: 6px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .hh-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }
        .hh-card-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--blue-dark);
            line-height: 1.3;
        }
        .hh-card-meta {
            font-size: 11px;
            color: var(--gray-400);
            margin-top: 2px;
        }
        .hh-card-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--gray-600);
        }
        .hh-card-row svg {
            width: 12px; height: 12px;
            flex-shrink: 0;
            color: var(--gray-400);
        }
        .hh-card-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--gray-400);
            margin-right: 4px;
        }
        .hh-card-actions {
            display: flex;
            gap: 8px;
            margin-top: 4px;
            padding-top: 10px;
            border-top: 1px solid var(--gray-100);
        }
        .hh-card-actions .btn-view,
        .hh-card-actions .btn-edit {
            flex: 1;
            justify-content: center;
        }
        .hh-card-empty {
            text-align: center;
            padding: 32px 16px;
            color: var(--gray-400);
            font-size: 13px;
        }


        /* ════════════════════════════════════════
           SIDEBAR — ALWAYS VISIBLE ON DESKTOP
           ════════════════════════════════════════ */
                .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
        }
        /* Mobile: sidebar becomes a slide-in drawer */
        @media (max-width: 900px) {
            .sidebar {
                grid-area: unset;
                position: fixed;
                top: 0; left: 0; bottom: 0;
                height: 100vh;
                width: var(--sidebar-w);
                z-index: 1200;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 4px 0 20px rgba(0,0,0,0.15);
                overflow-y: auto;
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block !important; z-index: 1100; }
            .sidebar-close { display: flex !important; }
            .sidebar .nav-section-label { padding-top: 56px; }
        }
        

        /* ════════════════════════════════════════
           TOPBAR RESPONSIVE
           ════════════════════════════════════════ */
        @media (max-width: 480px) {
            .status-indicator { display: none !important; }
        }

        /* ════════════════════════════════════════
           GLOBAL NO HORIZONTAL SCROLL
           ════════════════════════════════════════ */
        html, body { overflow-x: clip; max-width: 100vw; }
        @media (max-width: 900px) {
            .form-card, .nf-card, .nf-body, .nf-body-inner,
            .form-card-body, .check-group, .form-actions,
            .page-titlebar, .welcome-card, .access-notice,
            .stats-grid, .quick-nav, .charts-row, .bottom-row {
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            .member-table-wrap {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                max-width: 100% !important;
            }
            .member-table { min-width: 700px; }
            .form-input, .form-select, .form-textarea, .form-control {
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            #location-map { max-width: 100% !important; width: 100% !important; }
            .coord-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; max-width: 100% !important; }
        }
        @media (max-width: 640px) {
            .coord-inputs { grid-template-columns: 1fr !important; }
            .nf-pills { display: none !important; }
            .nf-card-header { flex-wrap: wrap !important; gap: 6px !important; }
            .head-panel-grid { grid-template-columns: 1fr !important; }
        }

        /* ════════════════════════════════════════
           FOOTER RESPONSIVE
           ════════════════════════════════════════ */
        @media (max-width: 640px) {
            footer {
                flex-direction: column;
                height: auto;
                min-height: 48px;
                padding: 10px 12px;
                gap: 4px;
                align-items: flex-start;
            }
            .footer-left { font-size: 10px; white-space: normal; line-height: 1.5; width: 100%; }
            .footer-center { display: none; }
        }

    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="topbar">
        <div class="topbar-left">Republic of the Philippines &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic</div>
        <div class="topbar-right">
            <span class="clock-date-inline" id="top-date">—</span>
            <span class="clock-inline" id="top-time">00:00:00</span>
            <span class="status-indicator">System Online</span>
        </div>
    </div>

    <header>
        <button class="hamburger" onclick="openSidebar()" aria-label="Open navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div class="header-logos">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo" onerror="this.style.display='none'">
            <div class="logo-divider"></div>
            <img src="{{ asset('images/naic-seal.png') }}" alt="Bayan ng Naic Seal" onerror="this.style.display='none'">
        </div>
        <div class="header-text">
            <div class="header-org">Office of the Municipal DRRMO</div>
            <div class="header-title">MDRRMO &mdash; Naic, Cavite</div>
            <div class="header-sub">Municipal Disaster Risk Reduction and Management Office</div>
        </div>
        <div class="header-spacer"></div>
        <div class="header-user-badge">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'Encoder' }}</div>
                <div class="user-role">Data Entry Access</div>
            </div>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="nav-section-label">Encoder Menu</div>

        <a href="{{ route('encoder.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
            <span class="nav-badge">Live</span>
        </a>

        <a href="{{ route('encoder.households.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            List of Households
        </a>

        <hr class="sidebar-sep">

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

    <main class="main-content">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">Encoder / <span>List of Registered Households</span></div>
                <div class="page-h1">List of Registered Households</div>
                <div class="page-sub">RBI-aligned household profiles submitted for admin approval — Barangay Family Track</div>
            </div>
            <div class="titlebar-actions">
                <a href="{{ route('encoder.dashboard') }}" class="back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('encoder.households.create') }}" class="btn-register">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Register New Household
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="encoder-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <strong>Encoder Access:</strong> You can create and update household profiles following the RBI form fields, and cross-reference entries with DSWD Listahanan records. Pending records can be edited before admin approval. Once approved, the Administrator will generate the household QR code and serial code. You cannot access distribution logs or generate QR codes.
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card total">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $households->total() }}</div>
                    <div class="stat-label">Total Submitted</div>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $households->getCollection()->where('approved_by', null)->count() }}</div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-number">{{ $households->getCollection()->filter(fn($h) => $h->isApproved())->count() }}</div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="ca-dot"></div>
                    <div class="table-section-title">Household Records — RBI Framework</div>
                </div>
            </div>

            @php
                $hasFilters = request()->hasAny(['status', 'sex', 'date_from', 'date_to', 'is_4ps', 'is_pwd', 'is_senior', 'is_solo_parent']);
                $activeFilters = array_filter([
                    'search'        => request('search'),
                    'status'        => request('status'),
                    'sex'           => request('sex'),
                    'date_from'     => request('date_from'),
                    'date_to'       => request('date_to'),
                    'is_4ps'        => request('is_4ps') ? '4Ps' : null,
                    'is_pwd'        => request('is_pwd') ? 'PWD' : null,
                    'is_senior'     => request('is_senior') ? 'Senior' : null,
                    'is_solo_parent'=> request('is_solo_parent') ? 'Solo Parent' : null,
                ]);
            @endphp

            <form method="GET" action="{{ route('encoder.households.index') }}" id="searchForm">
                <input type="hidden" name="scope" id="scopeHidden" value="{{ request('scope','all') }}">
                {{-- carry filter params through search --}}
                @foreach(['status','sex','date_from','date_to','is_4ps','is_pwd','is_senior','is_solo_parent'] as $fp)
                    @if(request($fp))<input type="hidden" name="{{ $fp }}" value="{{ request($fp) }}">@endif
                @endforeach

                <div class="search-bar-wrap">
                    {{-- ── Scope combo ── --}}
                    <div class="search-combo" id="searchCombo">
                        <select class="scope-sel" id="scopeSelect" onchange="updateScope(this.value)">
                            <option value="all"      {{ request('scope','all')==='all'      ? 'selected':'' }}>All Fields</option>
                            <option value="name"     {{ request('scope')==='name'     ? 'selected':'' }}>Head Name</option>
                            <option value="barangay" {{ request('scope')==='barangay' ? 'selected':'' }}>Barangay</option>
                            <option value="street"   {{ request('scope')==='street'   ? 'selected':'' }}>Street / Purok</option>
                            <option value="serial"   {{ request('scope')==='serial'   ? 'selected':'' }}>Serial Code</option>
                            <option value="status"   {{ request('scope')==='status'   ? 'selected':'' }}>Status</option>
                        </select>
                        <input type="text" name="search" class="srch-input" id="searchInput"
                            placeholder="Search households..."
                            value="{{ request('search') }}"
                            oninput="onSearchType(this)"
                            onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('searchForm').submit();}"
                            autocomplete="off">
                        <button type="button" class="srch-clear" onclick="clearSearch()" title="Clear">×</button>
                    </div>

                    {{-- ── Filter toggle ── --}}
                    <button class="btn-filter-toggle {{ $hasFilters ? 'active' : '' }}" id="filterToggleBtn" onclick="toggleFilter()" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        Filters
                        @if($hasFilters)<span style="background:#fff;color:var(--blue);border-radius:10px;font-size:10px;padding:1px 6px;margin-left:2px;">{{ count(array_filter([$hasFilters])) }}</span>@endif
                    </button>
                </div>
            </form>

            {{-- ── Active filter tags ── --}}
            @if(count($activeFilters))
            <div class="active-filters-row">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2.5" style="flex-shrink:0"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                @foreach($activeFilters as $key => $val)
                    <span class="filter-tag">
                        {{ ucfirst(str_replace('_',' ',$key)) }}: {{ $val }}
                        <a href="{{ request()->fullUrlWithQuery([$key => null]) }}">×</a>
                    </span>
                @endforeach
                <a href="{{ route('encoder.households.index') }}" class="clear-all-link">Clear all</a>
            </div>
            @endif

            <div class="filter-panel {{ $hasFilters ? 'open' : '' }}" id="filterPanel">
                <form method="GET" action="{{ route('encoder.households.index') }}" id="filterForm">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label class="filter-label">Approval Status</label>
                            <select name="status" class="filter-select">
                                <option value="">All Statuses</option>
                                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Sex</label>
                            <select name="sex" class="filter-select">
                                <option value="">All</option>
                                <option value="Male"   {{ request('sex') === 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ request('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Registered From</label>
                            <input type="date" name="date_from" class="filter-date" value="{{ request('date_from') }}">
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Registered To</label>
                            <input type="date" name="date_to" class="filter-date" value="{{ request('date_to') }}">
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Sector / Classification</label>
                            <div class="filter-checkboxes">
                                <label class="filter-checkbox-label">
                                    <input type="checkbox" name="is_4ps" value="1" {{ request('is_4ps') ? 'checked' : '' }}>
                                    4Ps Beneficiary
                                </label>
                                <label class="filter-checkbox-label">
                                    <input type="checkbox" name="is_pwd" value="1" {{ request('is_pwd') ? 'checked' : '' }}>
                                    PWD (Person w/ Disability)
                                </label>
                                <label class="filter-checkbox-label">
                                    <input type="checkbox" name="is_senior" value="1" {{ request('is_senior') ? 'checked' : '' }}>
                                    Senior Citizen
                                </label>
                                <label class="filter-checkbox-label">
                                    <input type="checkbox" name="is_solo_parent" value="1" {{ request('is_solo_parent') ? 'checked' : '' }}>
                                    Solo Parent
                                </label>
                            </div>
                        </div>
                        <div class="filter-group filter-actions">
                            <button type="submit" class="btn-apply-filter">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Household Head</th>
                            <th>Address</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Serial Code</th>
                            <th>Date Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($households as $household)
                            <tr>
                                <td class="td-name">
                                    <strong class="hl-name">{{ $household->household_head_name }}</strong>
                                    <small>
                                        @php $headMember = $household->primaryFamily?->headMember; @endphp
                                        @if($headMember)
                                            {{ $headMember->sex }}, {{ $headMember->age }} years old
                                        @else
                                            —
                                        @endif
                                    </small>
                                </td>
                                <td class="td-address">
                                    <span class="hl-street">{{ $household->street_purok }}</span>, <span class="hl-barangay">{{ $household->barangay }}</span>
                                    <small>{{ $household->municipality }}, {{ $household->province }}</small>
                                </td>
                                <td>{{ $household->total_members }} person(s)</td>
                                <td>
                                    @if($household->isApproved())
                                        <span class="badge badge-approved hl-status">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            Approved
                                        </span>
                                    @else
                                        <span class="badge badge-pending hl-status">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($household->serial_code)
                                        <span class="serial-code hl-serial">{{ $household->serial_code }}</span>
                                    @else
                                        <span class="serial-none">Not assigned yet</span>
                                    @endif
                                </td>
                                <td>{{ $household->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="action-group">
                                        <a href="{{ route('encoder.households.show', $household) }}" class="btn-view">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            View
                                        </a>
                                        @if(!$household->isApproved())
                                            <a href="{{ route('encoder.households.edit', $household) }}" class="btn-edit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                                <path d="M9 22V12h6v10"/>
                                            </svg>
                                        </div>
                                        <div class="empty-title">No households registered yet</div>
                                        <div class="empty-sub">Start building the barangay's RBI household database by registering the first family profile.</div>
                                        <a href="{{ route('encoder.households.create') }}" class="btn-register">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <line x1="12" y1="5" x2="12" y2="19"/>
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                            </svg>
                                            Register First Household
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile card list (shown on small screens instead of table) --}}
            <div class="hh-cards">
                @forelse($households as $household)
                    @php $headMember = $household->primaryFamily?->headMember; @endphp
                    <div class="hh-card">
                        <div class="hh-card-top">
                            <div>
                                <div class="hh-card-name">{{ $household->household_head_name }}</div>
                                <div class="hh-card-meta">
                                    @if($headMember) {{ $headMember->sex }}, {{ $headMember->age }} yrs old &nbsp;·&nbsp; @endif
                                    {{ $household->total_members }} member(s)
                                </div>
                            </div>
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
                        </div>
                        <div class="hh-card-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $household->street_purok }}, {{ $household->barangay }}, {{ $household->municipality }}
                        </div>
                        <div class="hh-card-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $household->created_at->format('M d, Y') }}
                            @if($household->serial_code)
                                &nbsp;·&nbsp; <span style="font-family:monospace;font-weight:700;color:var(--blue);font-size:12px;">{{ $household->serial_code }}</span>
                            @endif
                        </div>
                        <div class="hh-card-actions">
                            <a href="{{ route('encoder.households.show', $household) }}" class="btn-view">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                            @if(!$household->isApproved())
                                <a href="{{ route('encoder.households.edit', $household) }}" class="btn-edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="hh-card-empty">No households registered yet.</div>
                @endforelse
            </div>

            <div class="pagination-row" id="paginationRow">
                {{ $households->links() }}
            </div>
        </div>

    </main>

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
    /* ── Search scope ── */
    const SEARCH_PH = {
        all:      'Search by name, barangay, street, or serial code…',
        name:     'Search by household head name…',
        barangay: 'Search by barangay…',
        street:   'Search by street or purok…',
        serial:   'Search by serial code…',
        status:   'e.g. Approved, Pending…',
    };
    function updateScope(val) {
        document.getElementById('scopeHidden').value = val;
        const inp = document.getElementById('searchInput');
        if (inp) inp.placeholder = SEARCH_PH[val] || 'Search…';
    }

    /* typing: only update highlight + clear btn — no auto-submit */
    function onSearchType(input) {
        const combo = document.getElementById('searchCombo');
        if (combo) combo.classList.toggle('has-value', input.value.length > 0);
        applyHighlight(input.value, document.getElementById('scopeSelect').value);
    }
    function clearSearch() {
        const inp = document.getElementById('searchInput');
        if (!inp) return;
        inp.value = '';
        const combo = document.getElementById('searchCombo');
        if (combo) combo.classList.remove('has-value');
        removeHighlight();
        document.getElementById('searchForm').submit();
    }

    const HL_SCOPE = {
        all:      ['hl-name','hl-barangay','hl-street','hl-serial','hl-status'],
        name:     ['hl-name'],
        barangay: ['hl-barangay'],
        street:   ['hl-street'],
        serial:   ['hl-serial'],
        status:   ['hl-status'],
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
            const combo = document.getElementById('searchCombo');
            if (combo) combo.classList.add('has-value');
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

        function openSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    window.addEventListener('resize', function() {
        if (window.innerWidth > 900) closeSidebar();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebar();
    });

    function toggleFilter() {
        const panel = document.getElementById('filterPanel');
        const btn   = document.getElementById('filterToggleBtn');
        const isOpen = panel.classList.contains('open');
        panel.classList.toggle('open', !isOpen);
        btn.classList.toggle('active', !isOpen);
    }

    // ── Rebuild pagination cleanly ──
    (function normalizePagination() {
        const container = document.getElementById('paginationRow');
        if (!container) return;
        const nav = container.querySelector('nav');
        if (!nav) return;

        const CHEVRON_L = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0"><polyline points="15 18 9 12 15 6"/></svg>`;
        const CHEVRON_R = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>`;

        const infoEl = nav.querySelector('p');
        const infoHTML = infoEl ? infoEl.innerHTML : '';

        let prevHTML = '', nextHTML = '', pageButtons = '';

        const btnGroup = nav.querySelector('div') || nav;
        Array.from(btnGroup.children).forEach(el => {
            const inner = el.children.length === 1 && el.children[0].tagName === 'SPAN' ? el.children[0] : el;
            const text = inner.textContent.trim().replace(/[\s\u00a0]+/g, ' ');
            const isLink = el.tagName === 'A';
            const isCurrent = el.getAttribute('aria-current') === 'page'
                           || inner.getAttribute('aria-current') === 'page';
            const href = el.getAttribute('href') || '#';

            if (/previous/i.test(text) || text === '\u00ab' || text === '\u2039') {
                prevHTML = isLink
                    ? `<a href="${href}" class="pg-btn nav-btn">${CHEVRON_L} Previous</a>`
                    : `<span class="pg-btn nav-btn disabled">${CHEVRON_L} Previous</span>`;
                return;
            }
            if (/next/i.test(text) || text === '\u00bb' || text === '\u203a') {
                nextHTML = isLink
                    ? `<a href="${href}" class="pg-btn nav-btn">Next ${CHEVRON_R}</a>`
                    : `<span class="pg-btn nav-btn disabled">Next ${CHEVRON_R}</span>`;
                return;
            }
            if (text === '...' || text === '\u2026') {
                pageButtons += `<span class="pg-dots">···</span>`;
                return;
            }
            if (!/^\d+$/.test(text)) return;
            pageButtons += isCurrent
                ? `<span class="pg-btn active">${text}</span>`
                : `<a href="${href}" class="pg-btn">${text}</a>`;
        });

        container.innerHTML = `
            <div class="pg-bar">
                <div class="pg-controls">
                    ${prevHTML}
                    <div style="display:flex;align-items:center;gap:4px;">${pageButtons}</div>
                    ${nextHTML}
                </div>
                ${infoHTML ? `<div class="pg-info">${infoHTML}</div>` : ''}
            </div>`;
    })();
</script>
</body>
</html>