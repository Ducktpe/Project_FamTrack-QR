<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Household Profile</title>
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
            --red-pale:   #FEF2F2;
            --green:      #1A7A4A;
            --green-pale: #EAF5EF;
            --green-border:#A8D8BE;
            --orange:     #BF6000;
            --orange-pale:#FFF3E0;
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
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
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
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid #C5D9F5; border-radius: 4px; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--blue-light); text-transform: uppercase; letter-spacing: 0.5px; }

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

        .title-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 6px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; padding: 9px 16px; border-radius: 3px; border: none; cursor: pointer; text-decoration: none; transition: background 0.15s, color 0.15s; white-space: nowrap; }
        .btn-outline { background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); }
        .btn-outline:hover { background: var(--gray-50); color: var(--blue); border-color: var(--blue-light); }
        .btn-primary { background: var(--blue); color: var(--white); }
        .btn-primary:hover { background: var(--blue-dark); }
        .btn svg { width: 13px; height: 13px; }

        /* ─── APPROVAL BANNER ─── */
        .approval-banner { padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; border: 1px solid; border-radius: 2px; }
        .approval-banner.pending { background: var(--orange-pale); border-color: #FFD08A; border-left: 4px solid var(--orange); }
        .approval-banner.approved { background: var(--green-pale); border-color: var(--green-border); border-left: 4px solid var(--green); }
        .approval-banner svg { width: 16px; height: 16px; flex-shrink: 0; }
        .approval-banner.pending svg { color: var(--orange); }
        .approval-banner.approved svg { color: var(--green); }
        .approval-banner-text { font-size: 12px; line-height: 1.5; }
        .approval-banner.pending .approval-banner-text { color: #7A3D00; }
        .approval-banner.approved .approval-banner-text { color: #0D4D2A; }
        .approval-banner-text strong { font-weight: 700; }

        /* ─── CARDS ─── */
        .card { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 16px; }
        .card-header { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .card-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .card-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .card-body { padding: 20px; }

        /* ─── FIELD GRID ─── */
        .field-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .field-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .field { display: flex; flex-direction: column; gap: 4px; }
        .field label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); }
        .field-val { font-size: 13px; color: var(--gray-800); font-weight: 500; }
        .field-val.mono { font-family: monospace; font-size: 12px; }
        .field-val.empty { color: var(--gray-400); font-style: italic; }

        /* Serial code chip */
        .serial-chip { display: inline-block; font-family: monospace; font-size: 13px; font-weight: 700; color: var(--blue); background: var(--blue-pale); padding: 4px 12px; border-radius: 3px; border: 1px solid #C5D9F5; }

        /* ─── BADGES ─── */
        .badge { display: inline-block; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 3px 8px; border-radius: 10px; margin: 2px; }
        .badge-4ps    { background: var(--green-pale); color: var(--green); border: 1px solid var(--green-border); }
        .badge-pwd    { background: var(--orange-pale); color: var(--orange); border: 1px solid #FFD08A; }
        .badge-senior { background: var(--blue-pale); color: var(--blue); border: 1px solid #C5D9F5; }
        .badge-solo   { background: #F5F0FF; color: #3D1F8A; border: 1px solid #D8CBF5; }
        .badge-none   { background: var(--gray-100); color: var(--gray-400); border: 1px solid var(--gray-200); font-style: italic; }

        /* Status */
        .status-active   { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; color: var(--green); }
        .status-active::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--green); }
        .status-inactive { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; color: var(--gray-400); }
        .status-inactive::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--gray-400); }

        /* ─── MEMBERS TABLE ─── */
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        thead tr { background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
        thead th { padding: 10px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 11px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        .member-name { font-weight: 600; color: var(--blue-dark); }

        /* Empty members */
        .empty-members { padding: 32px; text-align: center; color: var(--gray-400); font-size: 12px; }

        /* ─── META INFO ─── */
        .meta-row { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; padding: 12px 20px; background: var(--gray-50); border-top: 1px solid var(--gray-100); font-size: 11px; color: var(--gray-400); }
        .meta-row strong { color: var(--gray-600); }

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
            .field-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider { display: none; }
            .header-logos img:last-child { display: none; }
            .header-org { display: none; }
            .header-title { font-size: 13px; }
            .main-content { padding: 16px 12px; }
            .field-grid { grid-template-columns: 1fr 1fr; }
            .field-grid-2 { grid-template-columns: 1fr; }
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
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'Encoder' }}</div>
                <div class="user-role">Encoder</div>
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
        <hr class="sidebar-sep">
        <a href="{{ route('encoder.households.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            My Households
        </a>
        <a href="{{ route('encoder.households.create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Register Household
        </a>
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
                    <span>View Profile</span>
                </div>
                <div class="page-h1">{{ $household->household_head_name }}</div>
                <div class="page-sub">Household Profile — Encoded by you</div>
            </div>
            <div class="title-actions">
                @if(!$household->isApproved())
                    <a href="{{ route('encoder.households.edit', $household) }}" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('encoder.households.index') }}" class="btn btn-outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
        <div style="background:var(--green-pale);border:1px solid var(--green-border);border-left:4px solid var(--green);padding:12px 18px;margin-bottom:16px;font-size:12px;color:#0D4D2A;">
            {{ session('success') }}
        </div>
        @endif

        {{-- Approval Status Banner --}}
        @if($household->approved_by)
        <div class="approval-banner approved">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <div class="approval-banner-text">
                <strong>Approved.</strong> This household has been approved by
                <strong>{{ $household->approver->name ?? 'Admin' }}</strong>.
                Serial code and QR have been assigned.
            </div>
        </div>
        @else
        <div class="approval-banner pending">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="approval-banner-text">
                <strong>Pending Approval.</strong> This household is awaiting Admin review.
                You may still edit this record until it is approved.
            </div>
        </div>
        @endif

        {{-- Household Head Info --}}
        <div class="card">
            <div class="card-header">
                <div class="card-dot"></div>
                <div class="card-title">Household Head Information</div>
            </div>
            <div class="card-body">
                <div class="field-grid" style="margin-bottom:16px;">
                    <div class="field">
                        <label>Serial Code</label>
                        <div class="field-val">
                            @if($household->serial_code)
                                <span class="serial-chip">{{ $household->serial_code }}</span>
                            @else
                                <span class="empty">Not yet assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="field">
                        <label>Full Name</label>
                        <div class="field-val">{{ $household->household_head_name }}</div>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <div class="field-val">
                            @if($household->status === 'active')
                                <span class="status-active">Active</span>
                            @else
                                <span class="status-inactive">{{ ucfirst($household->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="field-grid">
                    <div class="field">
                        <label>Sex</label>
                        <div class="field-val">{{ $household->sex }}</div>
                    </div>
                    <div class="field">
                        <label>Birthday</label>
                        <div class="field-val">
                            {{ $household->birthday ? \Carbon\Carbon::parse($household->birthday)->format('F d, Y') : '—' }}
                        </div>
                    </div>
                    <div class="field">
                        <label>Civil Status</label>
                        <div class="field-val">{{ ucfirst($household->civil_status) }}</div>
                    </div>
                    <div class="field">
                        <label>Contact Number</label>
                        <div class="field-val">{{ $household->contact_number ?? '—' }}</div>
                    </div>
                    <div class="field">
                        <label>Listahanan ID</label>
                        <div class="field-val mono">{{ $household->listahanan_id ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="card">
            <div class="card-header">
                <div class="card-dot"></div>
                <div class="card-title">Address</div>
            </div>
            <div class="card-body">
                <div class="field-grid">
                    <div class="field">
                        <label>House No.</label>
                        <div class="field-val">{{ $household->house_number ?? '—' }}</div>
                    </div>
                    <div class="field">
                        <label>Street / Purok</label>
                        <div class="field-val">{{ $household->street_purok ?? '—' }}</div>
                    </div>
                    <div class="field">
                        <label>Barangay</label>
                        <div class="field-val">{{ $household->barangay }}</div>
                    </div>
                    <div class="field">
                        <label>Municipality</label>
                        <div class="field-val">{{ $household->municipality }}</div>
                    </div>
                    <div class="field">
                        <label>Province</label>
                        <div class="field-val">{{ $household->province }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Beneficiary Classification --}}
        <div class="card">
            <div class="card-header">
                <div class="card-dot"></div>
                <div class="card-title">Beneficiary Classification</div>
            </div>
            <div class="card-body">
                @if($household->is_4ps_beneficiary || $household->is_pwd || $household->is_senior || $household->is_solo_parent)
                    @if($household->is_4ps_beneficiary) <span class="badge badge-4ps">4Ps Beneficiary</span> @endif
                    @if($household->is_pwd)             <span class="badge badge-pwd">Person with Disability (PWD)</span> @endif
                    @if($household->is_senior)          <span class="badge badge-senior">Senior Citizen</span> @endif
                    @if($household->is_solo_parent)     <span class="badge badge-solo">Solo Parent</span> @endif
                @else
                    <span class="badge badge-none">No beneficiary tags</span>
                @endif
            </div>
        </div>

        {{-- Family Members --}}
        <div class="card">
            <div class="card-header">
                <div class="card-dot"></div>
                <div class="card-title">Family Members</div>
                <span style="margin-left:auto;background:var(--blue-pale);color:var(--blue);font-size:10px;font-weight:700;padding:3px 10px;border-radius:10px;border:1px solid #C5D9F5;">
                    {{ $household->members->count() }} {{ Str::plural('Member', $household->members->count()) }}
                </span>
            </div>
            @if($household->members->count() > 0)
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Relationship</th>
                            <th>Sex</th>
                            <th>Birthday</th>
                            <th>Tags</th>
                            <th>Occupation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($household->members as $i => $member)
                        <tr>
                            <td style="color:var(--gray-400);font-size:12px;">{{ $i + 1 }}</td>
                            <td><span class="member-name">{{ $member->full_name }}</span></td>
                            <td style="font-size:12px;">{{ $member->relationship }}</td>
                            <td style="font-size:12px;">{{ $member->sex }}</td>
                            <td style="font-size:12px;">
                                {{ $member->birthday ? \Carbon\Carbon::parse($member->birthday)->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                @if($member->is_pwd)    <span class="badge badge-pwd">PWD</span> @endif
                                @if($member->is_student)<span class="badge badge-senior">Student</span> @endif
                                @if(!$member->is_pwd && !$member->is_student) <span style="font-size:11px;color:var(--gray-400);">—</span> @endif
                            </td>
                            <td style="font-size:12px;color:var(--gray-600);">{{ $member->occupation ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-members">No family members recorded for this household.</div>
            @endif

            {{-- Meta info --}}
            <div class="meta-row">
                <span>Encoded by: <strong>{{ $household->encoder->name ?? 'Unknown' }}</strong></span>
                <span>Date encoded: <strong>{{ $household->created_at ? $household->created_at->format('F d, Y') : '—' }}</strong></span>
                <span>Last updated: <strong>{{ $household->updated_at ? $household->updated_at->format('F d, Y') : '—' }}</strong></span>
                @if($household->approved_by)
                    <span>Approved by: <strong>{{ $household->approver->name ?? 'Admin' }}</strong></span>
                @endif
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
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

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
</script>
</body>
</html>