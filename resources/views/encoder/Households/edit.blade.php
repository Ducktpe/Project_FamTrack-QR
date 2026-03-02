<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Edit Household</title>
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
        html, body { height: 100%; font-family: 'Open Sans', sans-serif; background: var(--gray-100); color: var(--gray-800); font-size: 14px; }

        .shell {
            display: grid;
            grid-template-rows: 36px 76px 1fr 48px;
            grid-template-columns: var(--sidebar-w) 1fr;
            grid-template-areas: "topbar topbar" "header header" "sidebar main" "footer footer";
            height: 100vh;
            overflow: hidden;
        }

        /* ─── TOPBAR ─── */
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
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid var(--gray-200); border-radius: 4px; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--green); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: var(--red-pale); color: var(--red); }
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

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; flex-wrap: wrap; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        .back-btn { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; padding: 8px 16px; border: 1px solid var(--gray-200); background: var(--white); border-radius: 4px; transition: background 0.15s; white-space: nowrap; }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        /* ─── ALERT ─── */
        .alert-error { background: var(--red-pale); border: 1px solid #FECACA; border-left: 4px solid var(--red); padding: 12px 16px; margin-bottom: 16px; font-size: 12px; color: #7F1D1D; }
        .alert-error ul { margin-left: 16px; margin-top: 4px; }

        /* ─── FORM CARD ─── */
        .form-card { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 16px; }
        .form-card-header { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .card-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .card-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .form-card-body { padding: 24px; }

        /* ─── FORM FIELDS ─── */
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.span-2 { grid-column: span 2; }
        .form-group.span-3 { grid-column: span 3; }
        .form-group label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-600); }
        .form-group label .required { color: var(--red); margin-left: 2px; }
        .form-control {
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; color: var(--gray-800);
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            padding: 9px 12px;
            background: var(--white);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 100%;
        }
        .form-control:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.1); }
        .form-control.is-error { border-color: var(--red); }
        .field-error { font-size: 11px; color: var(--red); margin-top: 2px; }

        /* Checkboxes */
        .checkbox-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 3px; cursor: pointer; transition: background 0.12s, border-color 0.12s; }
        .checkbox-item:hover { background: var(--blue-pale); border-color: #C5D9F5; }
        .checkbox-item input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--blue); cursor: pointer; flex-shrink: 0; }
        .checkbox-item span { font-size: 12px; font-weight: 600; color: var(--gray-700); }

        /* ─── MEMBERS SECTION ─── */
        .members-list { display: flex; flex-direction: column; gap: 12px; }
        .member-row {
            border: 1px solid var(--gray-200);
            border-left: 3px solid var(--blue-light);
            padding: 16px;
            position: relative;
            background: var(--gray-50);
        }
        .member-row-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .member-row-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--blue); }
        .btn-remove-member { background: none; border: 1px solid var(--gray-200); border-radius: 3px; padding: 4px 10px; font-size: 11px; font-weight: 600; color: var(--red); cursor: pointer; display: flex; align-items: center; gap: 5px; transition: background 0.12s; }
        .btn-remove-member:hover { background: var(--red-pale); border-color: #FECACA; }
        .btn-remove-member svg { width: 12px; height: 12px; }

        .btn-add-member { display: inline-flex; align-items: center; gap: 7px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; color: var(--blue); background: var(--blue-pale); border: 1px dashed var(--blue-light); border-radius: 3px; padding: 10px 18px; cursor: pointer; transition: background 0.12s; margin-top: 8px; }
        .btn-add-member:hover { background: #D6E8FA; }
        .btn-add-member svg { width: 14px; height: 14px; }

        /* ─── FORM ACTIONS ─── */
        .form-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 20px 24px; background: var(--gray-50); border: 1px solid var(--gray-200); border-top: none; }
        .btn-cancel { font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; color: var(--gray-600); background: var(--white); border: 1px solid var(--gray-200); border-radius: 3px; padding: 10px 20px; cursor: pointer; text-decoration: none; transition: background 0.15s; }
        .btn-cancel:hover { background: var(--gray-100); }
        .btn-submit { font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--white); background: var(--blue); border: none; border-radius: 3px; padding: 10px 28px; cursor: pointer; display: flex; align-items: center; gap: 7px; transition: background 0.15s; }
        .btn-submit:hover { background: var(--blue-dark); }
        .btn-submit svg { width: 14px; height: 14px; }

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
            .main-content { padding: 20px 16px; }
            .form-grid { grid-template-columns: repeat(2, 1fr); }
            .form-group.span-3 { grid-column: span 2; }
            .checkbox-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider { display: none; }
            .header-logos img:last-child { display: none; }
            .header-org { display: none; }
            .header-title { font-size: 13px; }
            .main-content { padding: 16px 12px; }
            .form-grid { grid-template-columns: 1fr 1fr; }
            .form-grid-2 { grid-template-columns: 1fr; }
            .form-group.span-2, .form-group.span-3 { grid-column: span 2; }
            .checkbox-grid { grid-template-columns: 1fr 1fr; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
        }
    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- TOPBAR -->
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
                <div class="user-name">{{ auth()->user()->name ?? 'Encoder' }}</div>
                <div class="user-role">Data Entry Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()">
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
        <a href="{{ route('encoder.households.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            My Households
        </a>
        <a href="{{ route('encoder.households.create') }}" class="nav-item" onclick="closeSidebar()">
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

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">
                    <a href="{{ route('encoder.dashboard') }}">Home</a> /
                    <a href="{{ route('encoder.households.index') }}">My Households</a> /
                    <a href="{{ route('encoder.households.show', $household) }}">{{ $household->household_head_name }}</a> /
                    <span>Edit</span>
                </div>
                <div class="page-h1">Edit Household</div>
                <div class="page-sub">Update household information — changes saved until Admin approves</div>
            </div>
            <a href="{{ route('encoder.households.show', $household) }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to Profile
            </a>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="alert-error">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('encoder.households.update', $household) }}">
            @csrf
            @method('PATCH')

            {{-- ── Household Head ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot"></div>
                    <div class="card-title">Household Head Information</div>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group span-2">
                            <label>Full Name <span class="required">*</span></label>
                            <input type="text" name="household_head_name" class="form-control @error('household_head_name') is-error @enderror"
                                value="{{ old('household_head_name', $household->household_head_name) }}" required>
                            @error('household_head_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Sex <span class="required">*</span></label>
                            <select name="sex" class="form-control @error('sex') is-error @enderror" required>
                                <option value="">— Select —</option>
                                <option value="Male"   {{ old('sex', $household->sex) === 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex', $household->sex) === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Birthday <span class="required">*</span></label>
                            <input type="date" name="birthday" class="form-control @error('birthday') is-error @enderror"
                                value="{{ old('birthday', $household->birthday ? \Carbon\Carbon::parse($household->birthday)->format('Y-m-d') : '') }}" required>
                            @error('birthday')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Civil Status <span class="required">*</span></label>
                            <select name="civil_status" class="form-control @error('civil_status') is-error @enderror" required>
                                <option value="">— Select —</option>
                                @foreach(['Single','Married','Widowed','Separated','Divorced'] as $cs)
                                    <option value="{{ $cs }}" {{ old('civil_status', $household->civil_status) === $cs ? 'selected' : '' }}>{{ $cs }}</option>
                                @endforeach
                            </select>
                            @error('civil_status')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-error @enderror"
                                value="{{ old('contact_number', $household->contact_number) }}" placeholder="e.g. 09XX XXX XXXX">
                            @error('contact_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Listahanan ID</label>
                            <input type="text" name="listahanan_id" class="form-control @error('listahanan_id') is-error @enderror"
                                value="{{ old('listahanan_id', $household->listahanan_id) }}" placeholder="DSWD Listahanan reference">
                            @error('listahanan_id')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Address ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot"></div>
                    <div class="card-title">Address</div>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>House Number</label>
                            <input type="text" name="house_number" class="form-control @error('house_number') is-error @enderror"
                                value="{{ old('house_number', $household->house_number) }}" placeholder="e.g. 123">
                            @error('house_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group span-2">
                            <label>Street / Purok</label>
                            <input type="text" name="street_purok" class="form-control @error('street_purok') is-error @enderror"
                                value="{{ old('street_purok', $household->street_purok) }}" placeholder="e.g. Purok 3, Timalan Street">
                            @error('street_purok')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Barangay <span class="required">*</span></label>
                            <input type="text" name="barangay" class="form-control @error('barangay') is-error @enderror"
                                value="{{ old('barangay', $household->barangay) }}" required>
                            @error('barangay')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Municipality <span class="required">*</span></label>
                            <input type="text" name="municipality" class="form-control @error('municipality') is-error @enderror"
                                value="{{ old('municipality', $household->municipality) }}" required>
                            @error('municipality')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Province <span class="required">*</span></label>
                            <input type="text" name="province" class="form-control @error('province') is-error @enderror"
                                value="{{ old('province', $household->province) }}" required>
                            @error('province')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Beneficiary Classification ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot"></div>
                    <div class="card-title">Beneficiary Classification</div>
                </div>
                <div class="form-card-body">
                    <div class="checkbox-grid">
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_4ps_beneficiary" value="1"
                                {{ old('is_4ps_beneficiary', $household->is_4ps_beneficiary) ? 'checked' : '' }}>
                            <span>4Ps Beneficiary</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_pwd" value="1"
                                {{ old('is_pwd', $household->is_pwd) ? 'checked' : '' }}>
                            <span>Person with Disability (PWD)</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_senior" value="1"
                                {{ old('is_senior', $household->is_senior) ? 'checked' : '' }}>
                            <span>Senior Citizen</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="is_solo_parent" value="1"
                                {{ old('is_solo_parent', $household->is_solo_parent) ? 'checked' : '' }}>
                            <span>Solo Parent</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ── Form Actions ── --}}
            <div class="form-actions">
                <a href="{{ route('encoder.households.show', $household) }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save Changes
                </button>
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
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar() { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>
</body>
</html>