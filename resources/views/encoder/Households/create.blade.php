<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Register New Household</title>
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

        /* Hamburger — hidden on desktop */
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
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-user-badge {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px;
            background: var(--blue-pale);
            border: 1px solid var(--gray-200); border-radius: 4px;
            flex-shrink: 0;
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--green);
            display: flex; align-items: center; justify-content: center;
            color: var(--white); font-weight: 700; font-size: 13px;
            flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 200;
            opacity: 0;
            transition: opacity 0.25s;
        }
        .sidebar-overlay.active { opacity: 1; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex; flex-direction: column;
            overflow-y: auto;
            position: relative;
        }

        /* Close button — only shown on mobile */
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
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn {
            width: 100%; font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
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
            overflow-y: auto;
            padding: 28px 32px;
        }

        /* Page title bar */
        .page-titlebar {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
            gap: 12px;
        }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .back-btn {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 600;
            color: var(--blue); text-decoration: none;
            padding: 8px 16px;
            border: 1px solid var(--gray-200);
            background: var(--white); border-radius: 4px;
            transition: background 0.15s;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        /* Alert */
        .alert-danger {
            background: var(--red-pale);
            border: 1px solid #FECACA; border-left: 4px solid var(--red);
            padding: 14px 16px; margin-bottom: 20px;
            font-size: 13px; color: var(--red);
        }
        .alert-danger strong { display: block; margin-bottom: 6px; }
        .alert-danger ul { margin-left: 18px; }
        .alert-danger li { margin-bottom: 3px; }

        /* ─── FORM SECTION CARD ─── */
        .form-section {
            background: var(--white);
            border: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }
        .form-section-header {
            padding: 13px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; gap: 10px;
            flex-wrap: wrap;
        }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .form-section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .form-section-sub { font-size: 11px; color: var(--gray-400); margin-left: auto; }
        .form-section-body { padding: 22px 24px; }

        /* Form layout */
        .form-row { display: grid; gap: 16px; margin-bottom: 16px; }
        .form-row.cols-1 { grid-template-columns: 1fr; }
        .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
        .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .form-row.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
        .form-row:last-child { margin-bottom: 0; }

        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-label {
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--gray-600);
        }
        .form-label .req { color: var(--red); margin-left: 2px; }
        .form-label .opt { color: var(--gray-400); font-weight: 400; text-transform: none; letter-spacing: 0; font-size: 10px; margin-left: 4px; }

        input[type="text"],
        input[type="date"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            font-family: 'Open Sans', sans-serif;
            font-size: 13px;
            color: var(--gray-800);
            background: var(--white);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="tel"]:focus,
        select:focus,
        textarea:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(36, 89, 168, 0.1);
        }
        input::placeholder { color: var(--gray-400); }
        select { cursor: pointer; }
        textarea { resize: vertical; min-height: 72px; }

        /* Checkbox group */
        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .checkbox-item {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 14px;
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            cursor: pointer;
            transition: background 0.12s, border-color 0.12s;
        }
        .checkbox-item:hover { background: var(--blue-pale); border-color: var(--blue-light); }
        .checkbox-item input[type="checkbox"] {
            width: 16px; height: 16px; flex-shrink: 0;
            accent-color: var(--blue); cursor: pointer;
        }
        .checkbox-item-label { font-size: 13px; color: var(--gray-800); cursor: pointer; line-height: 1.3; }
        .checkbox-item-label small { display: block; font-size: 11px; color: var(--gray-400); margin-top: 2px; }

        /* ─── FAMILY MEMBERS ─── */
        #members-container { margin-bottom: 16px; }

        .member-card {
            border: 1px solid var(--gray-200);
            border-left: 4px solid var(--blue-light);
            background: var(--gray-50);
            margin-bottom: 14px;
        }
        .member-card-header {
            padding: 11px 16px;
            background: var(--blue-pale);
            border-bottom: 1px solid var(--gray-200);
            display: flex; align-items: center; justify-content: space-between;
            gap: 8px;
        }
        .member-card-title {
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--blue);
            display: flex; align-items: center; gap: 8px;
        }
        .member-card-title svg { width: 13px; height: 13px; flex-shrink: 0; }
        .member-card-body { padding: 18px 16px; }
        .member-card-body .form-row { margin-bottom: 14px; }
        .member-card-body .form-row:last-child { margin-bottom: 0; }

        .btn-remove-member {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 11px;
            background: var(--red); color: var(--white);
            border: none; border-radius: 3px; cursor: pointer;
            font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .btn-remove-member:hover { background: #9B1C1C; }
        .btn-remove-member svg { width: 11px; height: 11px; }

        .btn-add-member {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px;
            background: var(--white); color: var(--blue);
            border: 1px dashed var(--blue-light); border-radius: 3px; cursor: pointer;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s, border-color 0.15s;
            width: 100%;
            justify-content: center;
        }
        .btn-add-member:hover { background: var(--blue-pale); border-color: var(--blue); }
        .btn-add-member svg { width: 14px; height: 14px; }

        /* Member checkbox inline */
        .member-checkbox-row {
            display: flex; gap: 12px; flex-wrap: wrap;
        }
        .member-checkbox-item {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 14px;
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            cursor: pointer;
            background: var(--white);
            font-size: 13px; color: var(--gray-800);
            transition: background 0.12s;
        }
        .member-checkbox-item:hover { background: var(--blue-pale); }
        .member-checkbox-item input[type="checkbox"] {
            width: 14px; height: 14px; accent-color: var(--blue); cursor: pointer;
        }

        /* ─── SUBMIT BAR ─── */
        .submit-bar {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--green);
            padding: 18px 24px;
            display: flex; align-items: center; gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .btn-submit {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 11px 24px;
            background: var(--green); color: var(--white);
            border: none; border-radius: 4px; cursor: pointer;
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: background 0.15s;
        }
        .btn-submit:hover { background: var(--green-dark); }
        .btn-submit svg { width: 15px; height: 15px; }
        .btn-cancel {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px;
            background: var(--white); color: var(--gray-600);
            border: 1px solid var(--gray-200); border-radius: 4px;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn-cancel:hover { background: var(--gray-100); }
        .submit-note {
            margin-left: auto;
            font-size: 11px; color: var(--gray-400);
            display: flex; align-items: center; gap: 6px;
        }
        .submit-note svg { width: 13px; height: 13px; color: var(--orange); flex-shrink: 0; }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 24px; gap: 8px; z-index: 100;
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

        /* ════════════════════════════════════════
           RESPONSIVE
           ════════════════════════════════════════ */
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

            .sidebar {
                grid-area: unset;
                position: fixed;
                top: 0; left: 0; bottom: 0;
                width: var(--sidebar-w);
                z-index: 300;
                transform: translateX(-100%);
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

            /* Form grid collapses */
            .form-row.cols-3,
            .form-row.cols-4 { grid-template-columns: 1fr 1fr; }

            .checkbox-grid { grid-template-columns: 1fr; }

            /* Member card 4-col → 2-col */
            .member-card-body .form-row.cols-4 { grid-template-columns: 1fr 1fr; }

            .submit-note { margin-left: 0; width: 100%; }
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
            .back-btn { align-self: flex-start; }

            .form-section-body { padding: 16px 14px; }
            .form-section-sub { display: none; }

            /* All multi-col → single col on small screens */
            .form-row.cols-2,
            .form-row.cols-3,
            .form-row.cols-4 { grid-template-columns: 1fr; }

            .member-card-body .form-row.cols-4,
            .member-card-body .form-row.cols-2 { grid-template-columns: 1fr; }

            .member-card-body { padding: 14px 12px; }
            .member-card-header { padding: 10px 12px; }

            .member-checkbox-row { flex-direction: column; gap: 8px; }
            .member-checkbox-item { width: 100%; }

            .submit-bar { padding: 14px 16px; }
            .btn-submit { width: 100%; justify-content: center; }
            .btn-cancel { width: 100%; justify-content: center; }
            .submit-note { font-size: 10px; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        @media (max-width: 380px) {
            .main-content { padding: 12px 10px; }
            .form-section-body { padding: 14px 12px; }
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
            <div class="user-avatar">E</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'Encoder' }}</div>
                <div class="user-role">Data Encoder</div>
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

        <div class="nav-section-label">Encoder Menu</div>

        <a href="{{ route('encoder.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('encoder.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            My Households
        </a>

        <a href="{{ route('encoder.households.create') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Register New Household
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

        <!-- Page title -->
        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">Encoder / My Households / <span>Register New Household</span></div>
                <div class="page-h1">Register New Household</div>
                <div class="page-sub">RBI-compliant family profile — Record of Barangay Inhabitants (RBI) Framework</div>
            </div>
            <a href="{{ route('encoder.households.index') }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to List
            </a>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="alert-danger">
                <strong>Please fix the following errors before submitting:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('encoder.households.store') }}">
        @csrf

            {{-- ── SECTION 1: Household Head (RBI) ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="ca-dot"></div>
                    <div class="form-section-title">Section 1 — Household Head Information (RBI)</div>
                    <div class="form-section-sub">Fields marked <span style="color:var(--red);">*</span> are required</div>
                </div>
                <div class="form-section-body">

                    <div class="form-row cols-1">
                        <div class="form-group">
                            <label class="form-label">Full Name (Last, First, MI) <span class="req">*</span></label>
                            <input type="text" name="household_head_name"
                                value="{{ old('household_head_name') }}"
                                required placeholder="e.g. Dela Cruz, Juan A.">
                        </div>
                    </div>

                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label class="form-label">Sex <span class="req">*</span></label>
                            <select name="sex" required>
                                <option value="">— Select —</option>
                                <option value="Male"   {{ old('sex') == 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Birthday <span class="req">*</span></label>
                            <input type="date" name="birthday" value="{{ old('birthday') }}" required max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Civil Status <span class="req">*</span></label>
                            <select name="civil_status" required>
                                <option value="">— Select —</option>
                                <option value="Single"    {{ old('civil_status') == 'Single'    ? 'selected' : '' }}>Single</option>
                                <option value="Married"   {{ old('civil_status') == 'Married'   ? 'selected' : '' }}>Married</option>
                                <option value="Widowed"   {{ old('civil_status') == 'Widowed'   ? 'selected' : '' }}>Widowed</option>
                                <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">Contact Number <span class="opt">(optional)</span></label>
                            <input type="tel" name="contact_number" value="{{ old('contact_number') }}" placeholder="09171234567">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Occupation <span class="opt">(optional)</span></label>
                            <input type="text" name="occupation" value="{{ old('occupation') }}" placeholder="e.g. Farmer, Vendor, Unemployed">
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── SECTION 2: Address ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="ca-dot"></div>
                    <div class="form-section-title">Section 2 — Address</div>
                </div>
                <div class="form-section-body">

                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">House / Lot Number <span class="opt">(optional)</span></label>
                            <input type="text" name="house_number" value="{{ old('house_number') }}" placeholder="e.g. Blk 1 Lot 5">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Street / Purok <span class="opt">(optional)</span></label>
                            <input type="text" name="street_purok" value="{{ old('street_purok') }}" placeholder="e.g. Purok 3, Sampaguita St.">
                        </div>
                    </div>

                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label class="form-label">Barangay <span class="req">*</span></label>
                            <input type="text" name="barangay" value="{{ old('barangay', 'Barangay Poblacion') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Municipality / City <span class="req">*</span></label>
                            <input type="text" name="municipality" value="{{ old('municipality', 'Naic') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Province <span class="req">*</span></label>
                            <input type="text" name="province" value="{{ old('province', 'Cavite') }}" required>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── SECTION 3: DSWD / Listahanan ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="ca-dot"></div>
                    <div class="form-section-title">Section 3 — DSWD / Listahanan Information</div>
                    <div class="form-section-sub">Cross-reference with DSWD Listahanan for eligibility verification</div>
                </div>
                <div class="form-section-body">

                    <div class="form-row cols-1" style="margin-bottom: 20px;">
                        <div class="form-group">
                            <label class="form-label">Listahanan Household ID <span class="opt">(if enrolled in National Household Targeting System)</span></label>
                            <input type="text" name="listahanan_id" value="{{ old('listahanan_id') }}" placeholder="e.g. 1234-5678-9012" style="max-width: 340px;">
                        </div>
                    </div>

                    <div class="form-label" style="margin-bottom: 10px;">Beneficiary Flags — Check all that apply to this household</div>
                    <div class="checkbox-grid">
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_4ps_beneficiary" value="1" {{ old('is_4ps_beneficiary') ? 'checked' : '' }}>
                            <div class="checkbox-item-label">
                                4Ps / Pantawid Pamilyang Pilipino Program Beneficiary
                                <small>Household enrolled in DSWD's conditional cash transfer program</small>
                            </div>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_pwd" value="1" {{ old('is_pwd') ? 'checked' : '' }}>
                            <div class="checkbox-item-label">
                                Has Person with Disability (PWD) Member
                                <small>At least one household member is a registered PWD</small>
                            </div>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_senior" value="1" {{ old('is_senior') ? 'checked' : '' }}>
                            <div class="checkbox-item-label">
                                Has Senior Citizen Member (60 years and above)
                                <small>At least one household member is a senior citizen</small>
                            </div>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_solo_parent" value="1" {{ old('is_solo_parent') ? 'checked' : '' }}>
                            <div class="checkbox-item-label">
                                Has Solo Parent Member
                                <small>Household has a solo parent covered under RA 8972</small>
                            </div>
                        </label>
                    </div>

                </div>
            </div>

            {{-- ── SECTION 4: Family Members ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <div class="ca-dot"></div>
                    <div class="form-section-title">Section 4 — Family Members</div>
                    <div class="form-section-sub">Excluding household head — can be added or updated later</div>
                </div>
                <div class="form-section-body">

                    <div id="members-container"></div>

                    <button type="button" onclick="addMember()" class="btn-add-member">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Family Member
                    </button>

                </div>
            </div>

            {{-- ── SUBMIT BAR ── --}}
            <div class="submit-bar">
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Register Household
                </button>
                <a href="{{ route('encoder.households.index') }}" class="btn-cancel">Cancel</a>
                <div class="submit-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Record will be submitted for Barangay Admin approval. QR code is generated after approval.
                </div>
            </div>

        </form>

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
    /* ─── Clock ─── */
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

    /* ─── Family Members ─── */
    let memberIndex = 0;
    const today = new Date().toISOString().split('T')[0];

    function addMember() {
        const container = document.getElementById('members-container');
        const idx = memberIndex;
        const card = document.createElement('div');
        card.className = 'member-card';
        card.id = `member-${idx}`;

        card.innerHTML = `
            <div class="member-card-header">
                <div class="member-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                    </svg>
                    Family Member ${idx + 1}
                </div>
                <button type="button" class="btn-remove-member" onclick="removeMember(${idx})">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Remove
                </button>
            </div>
            <div class="member-card-body">

                <div class="form-row cols-1">
                    <div class="form-group">
                        <label class="form-label">Full Name (Last, First, MI) <span class="req">*</span></label>
                        <input type="text" name="members[${idx}][full_name]" required placeholder="e.g. Dela Cruz, Maria A.">
                    </div>
                </div>

                <div class="form-row cols-4">
                    <div class="form-group">
                        <label class="form-label">Relationship to Head <span class="req">*</span></label>
                        <select name="members[${idx}][relationship]" required>
                            <option value="">— Select —</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Son">Son</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Parent">Parent</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Grandchild">Grandchild</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sex <span class="req">*</span></label>
                        <select name="members[${idx}][sex]" required>
                            <option value="">— Select —</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Birthday <span class="req">*</span></label>
                        <input type="date" name="members[${idx}][birthday]" required max="${today}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Educational Attainment <span class="opt">(optional)</span></label>
                        <select name="members[${idx}][educational_attainment]">
                            <option value="">— Select —</option>
                            <option value="No Schooling">No Schooling</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                            <option value="Vocational">Vocational</option>
                            <option value="College">College</option>
                            <option value="Postgraduate">Postgraduate</option>
                        </select>
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Occupation <span class="opt">(optional)</span></label>
                        <input type="text" name="members[${idx}][occupation]" placeholder="e.g. Student, Employed, Unemployed">
                    </div>
                    <div class="form-group">
                        <label class="form-label">PhilHealth Number <span class="opt">(optional)</span></label>
                        <input type="text" name="members[${idx}][philhealth_no]" placeholder="e.g. 12-345678901-2">
                    </div>
                </div>

                <div class="form-row cols-1">
                    <div class="form-group">
                        <label class="form-label">Flags</label>
                        <div class="member-checkbox-row">
                            <label class="member-checkbox-item">
                                <input type="checkbox" name="members[${idx}][is_pwd]" value="1">
                                Person with Disability (PWD)
                            </label>
                            <label class="member-checkbox-item">
                                <input type="checkbox" name="members[${idx}][is_student]" value="1">
                                Currently a Student
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        `;

        container.appendChild(card);
        memberIndex++;
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function removeMember(index) {
        const card = document.getElementById(`member-${index}`);
        if (card) card.remove();
    }
</script>
</body>
</html>