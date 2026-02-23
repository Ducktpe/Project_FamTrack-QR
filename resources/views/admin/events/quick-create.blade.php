<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Create Distribution Event</title>
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
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--blue);
            display: flex; align-items: center; justify-content: center;
            color: var(--white); font-weight: 700; font-size: 13px;
        }
        .user-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .user-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex; flex-direction: column;
            overflow-y: auto;
        }
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
        }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .back-btn {
            display: flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 600;
            color: var(--blue); text-decoration: none;
            padding: 8px 16px;
            border: 1px solid var(--gray-200);
            background: var(--white); border-radius: 4px;
            transition: background 0.15s;
        }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        /* Layout: form left, info panel right */
        .create-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }

        /* ─── FORM SECTION CARD ─── */
        .form-section {
            background: var(--white);
            border: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }
        .form-section:last-child { margin-bottom: 0; }
        .form-section-header {
            padding: 13px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; gap: 10px;
        }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .form-section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .form-section-body { padding: 22px 24px; }

        /* Form controls */
        .form-row { display: grid; gap: 16px; margin-bottom: 16px; }
        .form-row.cols-1 { grid-template-columns: 1fr; }
        .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
        .form-row:last-child { margin-bottom: 0; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-label {
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--gray-600);
        }
        .form-label .req { color: var(--red); margin-left: 2px; }
        .form-label .opt { color: var(--gray-400); font-weight: 400; text-transform: none; letter-spacing: 0; font-size: 10px; margin-left: 4px; }
        .form-hint { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        input[type="text"],
        input[type="date"],
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
        select:focus,
        textarea:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(36,89,168,0.1);
        }
        input::placeholder { color: var(--gray-400); }
        textarea::placeholder { color: var(--gray-400); }
        select { cursor: pointer; }
        textarea { resize: vertical; min-height: 80px; }

        /* Status badge options */
        .status-options { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        .status-option { position: relative; }
        .status-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        .status-option label {
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            padding: 14px 10px;
            border: 2px solid var(--gray-200);
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px; font-weight: 600;
            color: var(--gray-600);
            text-align: center;
            transition: border-color 0.15s, background 0.15s;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .status-option label svg { width: 18px; height: 18px; }
        .status-option input[type="radio"]:checked + label.upcoming  { border-color: var(--blue);   background: var(--blue-pale);   color: var(--blue); }
        .status-option input[type="radio"]:checked + label.ongoing   { border-color: var(--green);  background: var(--green-pale);  color: var(--green-dark); }
        .status-option input[type="radio"]:checked + label.completed { border-color: var(--gray-400); background: var(--gray-100); color: var(--gray-600); }
        .status-option label:hover { border-color: var(--blue-light); background: var(--blue-pale); }

        /* Submit bar */
        .submit-bar {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--green);
            padding: 16px 24px;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 0;
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

        /* ─── INFO SIDEBAR PANEL ─── */
        .info-panel { display: flex; flex-direction: column; gap: 16px; }

        .info-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .info-card-header {
            padding: 11px 16px;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-100);
            display: flex; align-items: center; gap: 8px;
        }
        .info-card-title { font-size: 12px; font-weight: 600; color: var(--blue-dark); }
        .info-card-body { padding: 16px; }

        .workflow-steps { display: flex; flex-direction: column; gap: 0; }
        .workflow-step {
            display: flex; gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .workflow-step:last-child { border-bottom: none; padding-bottom: 0; }
        .workflow-step:first-child { padding-top: 0; }
        .step-num {
            width: 22px; height: 22px; border-radius: 50%;
            background: var(--blue); color: var(--white);
            font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        .step-text { font-size: 12px; color: var(--gray-600); line-height: 1.5; }
        .step-text strong { color: var(--gray-800); display: block; margin-bottom: 2px; }

        .status-legend { display: flex; flex-direction: column; gap: 8px; }
        .status-legend-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-legend-item.upcoming  { background: var(--blue-pale);   color: var(--blue); }
        .status-legend-item.ongoing   { background: var(--green-pale);  color: var(--green-dark); }
        .status-legend-item.completed { background: var(--gray-100);    color: var(--gray-600); }
        .status-legend-item svg { width: 14px; height: 14px; flex-shrink: 0; }
        .status-legend-item div strong { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-legend-item div span { font-size: 11px; opacity: 0.8; }

        .relief-types { display: flex; flex-wrap: wrap; gap: 6px; }
        .relief-chip {
            padding: 4px 10px;
            background: var(--blue-pale);
            border: 1px solid #C7D9F3;
            border-radius: 10px;
            font-size: 11px; color: var(--blue); font-weight: 600;
            cursor: pointer; transition: background 0.12s;
        }
        .relief-chip:hover { background: #C7D9F3; }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 24px; z-index: 100;
        }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.4);
            text-decoration: none; transition: color 0.15s;
        }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }
    </style>
</head>
<body>
<div class="shell">

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
                <div class="user-name">Administrator</div>
                <div class="user-role">Full Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="nav-section-label">Admin Menu</div>

        <a href="{{ route('admin.dashboard') }}" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard Overview
        </a>

        <a href="#" class="nav-item active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Distribution Events
        </a>

        <a href="#" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
        </a>

        <a href="#" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            List of Residents
        </a>

        <a href="{{ route('admin.households.index') }}" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            Household Management
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
                <div class="page-breadcrumb">Admin / Distribution Events / <span>Create New Event</span></div>
                <div class="page-h1">Create Distribution Event</div>
                <div class="page-sub">Configure a new ayuda / relief distribution event for QR scan tracking</div>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <form method="POST" action="{{ route('admin.events.quick-store') }}">
        @csrf

        <div class="create-layout">

            <!-- LEFT: Form -->
            <div>

                {{-- Event Details --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Event Details</div>
                    </div>
                    <div class="form-section-body">

                        <div class="form-row cols-1">
                            <div class="form-group">
                                <label class="form-label">Event Name <span class="req">*</span></label>
                                <input type="text" name="event_name"
                                    value="{{ old('event_name') }}"
                                    required
                                    placeholder="e.g. Typhoon Carina Relief Round 1">
                                <div class="form-hint">Use a clear, descriptive name that identifies the calamity or program and the round number.</div>
                            </div>
                        </div>

                        <div class="form-row cols-2">
                            <div class="form-group">
                                <label class="form-label">Relief Type <span class="req">*</span></label>
                                <input type="text" name="relief_type"
                                    id="relief_type_input"
                                    value="{{ old('relief_type') }}"
                                    required
                                    placeholder="e.g. Food Pack">
                                <div class="form-hint">Type of goods being distributed during this event.</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Event Date <span class="opt">(optional)</span></label>
                                <input type="date" name="event_date" value="{{ old('event_date', date('Y-m-d')) }}">
                                <div class="form-hint">Scheduled date of the distribution activity.</div>
                            </div>
                        </div>

                        <div class="form-row cols-1">
                            <div class="form-group">
                                <label class="form-label">Goods Detail / Notes <span class="opt">(optional)</span></label>
                                <textarea name="goods_detail" placeholder="e.g. 5kg rice, 2 canned goods, cooking oil — per household">{{ old('goods_detail') }}</textarea>
                                <div class="form-hint">Specify contents per household pack. This appears in distribution logs for each scan.</div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Relief type quick-select chips --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Quick-Select Relief Type</div>
                    </div>
                    <div class="form-section-body">
                        <div class="form-hint" style="margin-bottom: 12px;">Click a type to auto-fill the Relief Type field above.</div>
                        <div class="relief-types">
                            <span class="relief-chip" onclick="setRelief('Food Pack')">Food Pack</span>
                            <span class="relief-chip" onclick="setRelief('Cash Aid')">Cash Aid</span>
                            <span class="relief-chip" onclick="setRelief('Medical Kit')">Medical Kit</span>
                            <span class="relief-chip" onclick="setRelief('Rice Subsidy')">Rice Subsidy</span>
                            <span class="relief-chip" onclick="setRelief('Hygiene Kit')">Hygiene Kit</span>
                            <span class="relief-chip" onclick="setRelief('Clothing Pack')">Clothing Pack</span>
                            <span class="relief-chip" onclick="setRelief('School Supplies')">School Supplies</span>
                            <span class="relief-chip" onclick="setRelief('Other')">Other</span>
                        </div>
                    </div>
                </div>

                {{-- Event Status --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Initial Event Status</div>
                    </div>
                    <div class="form-section-body">
                        <div class="form-hint" style="margin-bottom: 14px;">Select the starting status for this event. Distribution Staff can only scan QR codes during <strong>Ongoing</strong> events.</div>
                        <div class="status-options">
                            <div class="status-option">
                                <input type="radio" name="status" id="status_upcoming" value="upcoming" {{ old('status', 'ongoing') == 'upcoming' ? 'checked' : '' }}>
                                <label for="status_upcoming" class="upcoming">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Upcoming
                                </label>
                            </div>
                            <div class="status-option">
                                <input type="radio" name="status" id="status_ongoing" value="ongoing" {{ old('status', 'ongoing') == 'ongoing' ? 'checked' : '' }}>
                                <label for="status_ongoing" class="ongoing">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                    </svg>
                                    Ongoing
                                </label>
                            </div>
                            <div class="status-option">
                                <input type="radio" name="status" id="status_completed" value="completed" {{ old('status') == 'completed' ? 'checked' : '' }}>
                                <label for="status_completed" class="completed">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    Completed
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="submit-bar">
                    <button type="submit" class="btn-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Create Distribution Event
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn-cancel">Cancel</a>
                </div>

            </div>

            <!-- RIGHT: Info Panel -->
            <div class="info-panel">

                {{-- Distribution workflow --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="ca-dot"></div>
                        <div class="info-card-title">Distribution Workflow</div>
                    </div>
                    <div class="info-card-body">
                        <div class="workflow-steps">
                            <div class="workflow-step">
                                <div class="step-num">1</div>
                                <div class="step-text"><strong>Create Event</strong>Set event name, relief type, date, and initial status.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">2</div>
                                <div class="step-text"><strong>Set to Ongoing</strong>Distribution Staff can only scan QR codes on active ongoing events.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">3</div>
                                <div class="step-text"><strong>Staff Scan QR Cards</strong>System validates serial code, checks for duplicates, and logs receipt with timestamp and staff ID.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">4</div>
                                <div class="step-text"><strong>Duplicate Alert</strong>If a household already received goods, a red alert blocks re-release immediately.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">5</div>
                                <div class="step-text"><strong>Complete &amp; Export</strong>Mark event as completed. Export distribution logs to PDF or Excel for DSWD submission.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status legend --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="ca-dot"></div>
                        <div class="info-card-title">Event Status Reference</div>
                    </div>
                    <div class="info-card-body">
                        <div class="status-legend">
                            <div class="status-legend-item upcoming">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div>
                                    <strong>Upcoming</strong>
                                    <span>Event is scheduled. No scanning yet.</span>
                                </div>
                            </div>
                            <div class="status-legend-item ongoing">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                <div>
                                    <strong>Ongoing</strong>
                                    <span>Active — staff can scan and log distribution.</span>
                                </div>
                            </div>
                            <div class="status-legend-item completed">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <div>
                                    <strong>Completed</strong>
                                    <span>Closed. Logs locked, export available.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DB note --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="ca-dot"></div>
                        <div class="info-card-title">What Gets Logged Per Scan</div>
                    </div>
                    <div class="info-card-body">
                        <div class="workflow-steps">
                            <div class="workflow-step">
                                <div class="step-num" style="background:var(--gray-400);">→</div>
                                <div class="step-text"><strong>Event ID</strong>Links the log to this distribution event.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num" style="background:var(--gray-400);">→</div>
                                <div class="step-text"><strong>Household ID + Serial Code</strong>Identifies which family received goods.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num" style="background:var(--gray-400);">→</div>
                                <div class="step-text"><strong>Staff ID + Timestamp</strong>Records who scanned and the exact date and time.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num" style="background:var(--gray-400);">→</div>
                                <div class="step-text"><strong>Goods Detail</strong>Copied from this event's goods detail field per log entry.</div>
                            </div>
                        </div>
                    </div>
                </div>

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

    function setRelief(type) {
        document.getElementById('relief_type_input').value = type;
        document.getElementById('relief_type_input').focus();
    }
</script>
</body>
</html>