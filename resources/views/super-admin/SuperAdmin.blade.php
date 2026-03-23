<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') – MDRRMO Naic, Cavite</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet"/>

    <style>
    /* =========================================================
       MDRRMO Super Admin – Embedded Stylesheet
       ========================================================= */

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --navy:        #1a2a4a;
        --navy-dark:   #111d35;
        --gold:        #e6a817;
        --gold-light:  #f5c84a;
        --green:       #22a86a;
        --red:         #e03c3c;
        --orange:      #e08a1a;
        --blue-acc:    #2e6ddd;
        --sidebar-w:   252px;
        --header-h:    88px;
        --govbar-h:    28px;
        --text:        #1e2d4d;
        --muted:       #6b7a99;
        --border:      #dde3ef;
        --bg:          #f0f3f9;
        --card:        #ffffff;
        --super-badge: #7c3aed;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg);
        color: var(--text);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ── GOV BAR ── */
    .gov-bar {
        background: var(--navy-dark); color: #b0bdd6;
        font-size: 11px; padding: 0 20px; height: var(--govbar-h);
        display: flex; align-items: center; justify-content: space-between;
        letter-spacing: .3px; flex-shrink: 0;
    }
    .gov-bar strong { color: #e0e7f2; }
    .gov-bar-right { display: flex; gap: 18px; align-items: center; }
    .clock {
        color: var(--gold); font-weight: 700; font-size: 13px;
        font-family: 'Rajdhani', sans-serif; letter-spacing: 1px;
    }
    .online { display: flex; align-items: center; gap: 5px; }
    .dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--green); display: inline-block;
        animation: pulse 2s infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

    /* ── MAIN HEADER ── */
    .main-header {
        background: #fff; border-bottom: 3px solid var(--gold);
        height: var(--header-h); display: flex; align-items: center;
        padding: 0 24px; gap: 16px; flex-shrink: 0;
        position: sticky; top: 0; z-index: 100;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .header-logos { display: flex; gap: 10px; align-items: center; }
    .seal-img { width: 54px; height: 54px; border-radius: 50%; border: 2px solid var(--gold); object-fit: cover; }
    .logo-circle {
        width: 54px; height: 54px; border-radius: 50%; background: var(--navy);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Rajdhani', sans-serif; font-size: 9px;
        color: var(--gold); text-align: center; font-weight: 700;
        line-height: 1.2; padding: 4px; border: 2px solid var(--gold);
    }
    .header-info { flex: 1; }
    .header-info .label { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; }
    .header-info h1 { font-family: 'Rajdhani', sans-serif; font-size: 24px; font-weight: 700; color: var(--navy-dark); line-height: 1; }
    .header-info .sub { font-size: 11px; color: var(--muted); }
    .header-user {
        display: flex; align-items: center; gap: 10px;
        background: var(--navy-dark); border-radius: 8px; padding: 8px 14px;
    }
    .user-avatar {
        width: 36px; height: 36px; border-radius: 50%; background: var(--super-badge);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #fff; font-size: 15px;
    }
    .user-info .name { font-size: 13px; font-weight: 600; color: #fff; }
    .user-info .role {
        font-size: 10px; font-weight: 700; color: var(--gold-light);
        text-transform: uppercase; letter-spacing: .8px;
        display: flex; align-items: center; gap: 4px;
    }
    .super-badge {
        background: var(--super-badge); color: #fff; font-size: 9px;
        padding: 1px 5px; border-radius: 3px; font-weight: 700; letter-spacing: .5px;
    }

    /* ── LAYOUT ── */
    .layout { display: flex; flex: 1; min-height: 0; position: relative; }

    /* ── SIDEBAR ── */
    .sidebar {
        width: var(--sidebar-w); background: var(--navy-dark);
        display: flex; flex-direction: column;
        position: sticky; top: var(--header-h);
        height: calc(100vh - var(--govbar-h) - var(--header-h));
        overflow-y: auto; flex-shrink: 0;
        transition: transform .25s ease; z-index: 90;
    }
    .sidebar-inner { flex: 1; padding-bottom: 8px; }
    .sidebar-section { padding: 14px 0 4px; }
    .sidebar-section-title {
        font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px;
        color: #4a5a7a; padding: 0 20px 8px; font-weight: 700;
    }
    .nav-item {
        display: flex; align-items: center; gap: 11px;
        padding: 10px 20px; color: #8fa0c2; font-size: 13px;
        cursor: pointer; transition: all .18s;
        border-left: 3px solid transparent;
        text-decoration: none; line-height: 1;
    }
    .nav-item:hover { background: rgba(255,255,255,.05); color: #d0dbf0; border-left-color: #3b5080; }
    .nav-item.active { background: rgba(46,109,221,.15); color: #fff; border-left-color: var(--blue-acc); font-weight: 600; }
    .nav-item.active .nav-icon { color: var(--gold); }
    .nav-item.super-only { color: #b89af0; }
    .nav-item.super-only:hover { background: rgba(124,58,237,.12); color: #d4b8ff; border-left-color: var(--super-badge); }
    .nav-item.super-only.active { background: rgba(124,58,237,.18); border-left-color: var(--super-badge); color: #fff; }
    .nav-icon { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }
    .nav-badge {
        margin-left: auto; background: var(--super-badge); color: #fff;
        font-size: 9px; padding: 2px 6px; border-radius: 10px; font-weight: 700;
    }
    .nav-badge.green { background: var(--green); }
    .sidebar-divider { border: none; border-top: 1px solid #243558; margin: 4px 16px; }
    .logout-form { padding: 12px 16px 16px; flex-shrink: 0; }
    .logout-btn {
        width: 100%; background: rgba(224,60,60,.12);
        border: 1px solid rgba(224,60,60,.3); color: #f08080;
        padding: 9px 16px; border-radius: 7px; cursor: pointer;
        font-size: 13px; font-weight: 600;
        display: flex; align-items: center; gap: 8px;
        transition: all .18s; font-family: 'Inter', sans-serif;
    }
    .logout-btn:hover { background: rgba(224,60,60,.22); color: #ffa0a0; }

    /* MOBILE TOGGLE */
    .sidebar-toggle {
        display: none; position: fixed; bottom: 20px; right: 20px;
        z-index: 200; background: var(--navy-dark); color: #fff;
        border: none; width: 44px; height: 44px; border-radius: 50%;
        font-size: 18px; cursor: pointer; box-shadow: 0 4px 16px rgba(0,0,0,.3);
    }

    /* ── MAIN CONTENT ── */
    .main { flex: 1; padding: 28px 32px; overflow-y: auto; min-width: 0; }

    /* ── ALERTS ── */
    .alert {
        border-radius: 8px; padding: 12px 18px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px; font-size: 13px;
    }
    .alert-success { background: #ecfdf5; border: 1px solid #6ee7b7; border-left: 4px solid var(--green); color: #065f46; }
    .alert-error   { background: #fff1f2; border: 1px solid #fca5a5; border-left: 4px solid var(--red);   color: #991b1b; }

    /* ── PAGE HEADER ── */
    .breadcrumb { font-size: 11px; color: var(--muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: .8px; }
    .breadcrumb span { color: var(--blue-acc); }
    .page-title { font-family: 'Rajdhani', sans-serif; font-size: 28px; font-weight: 700; color: var(--navy-dark); }
    .page-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .page-header-row { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 22px; }
    .today-label { text-align: right; font-size: 11px; color: var(--muted); }
    .today-label strong { display: block; font-size: 13px; color: var(--text); font-weight: 600; }

    /* ── STAT CARDS ── */
    .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
    .stat-card {
        background: var(--card); border-radius: 10px; padding: 18px 20px;
        border-top: 4px solid transparent; box-shadow: 0 1px 4px rgba(0,0,0,.06);
        transition: transform .18s, box-shadow .18s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
    .stat-card.c-blue   { border-top-color: var(--blue-acc); }
    .stat-card.c-green  { border-top-color: var(--green); }
    .stat-card.c-purple { border-top-color: var(--super-badge); }
    .stat-card.c-orange { border-top-color: var(--orange); }
    .stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); font-weight: 600; }
    .stat-val { font-family: 'Rajdhani', sans-serif; font-size: 36px; font-weight: 700; line-height: 1.1; }
    .stat-val.blue   { color: var(--blue-acc); }
    .stat-val.green  { color: var(--green); }
    .stat-val.purple { color: var(--super-badge); }
    .stat-val.orange { color: var(--orange); }
    .stat-desc { font-size: 11px; color: var(--muted); margin-top: 2px; }
    .stat-icon { font-size: 22px; margin-bottom: 8px; }

    /* ── PANELS ── */
    .panel { background: var(--card); border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.07); margin-bottom: 22px; overflow: hidden; }
    .panel-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 8px; }
    .panel-title { display: flex; align-items: center; gap: 9px; font-size: 14px; font-weight: 700; color: var(--navy); }
    .panel-title small { font-weight: 400; color: var(--muted); font-size: 12px; }
    .panel-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .panel-dot.purple { background: var(--super-badge); }
    .panel-dot.blue   { background: var(--blue-acc); }
    .panel-dot.orange { background: var(--orange); }
    .panel-count { font-size: 12px; color: var(--muted); }
    .panel-header-actions { display: flex; gap: 8px; align-items: center; }
    .super-only-badge { font-size: 10px; background: #fef3c7; color: #92400e; padding: 2px 7px; border-radius: 10px; font-weight: 700; margin-left: 6px; }

    /* ── BUTTONS ── */
    .btn {
        padding: 7px 14px; border-radius: 6px; border: none;
        font-size: 12px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all .15s; font-family: 'Inter', sans-serif; text-decoration: none;
    }
    .btn-primary { background: var(--blue-acc); color: #fff; }
    .btn-primary:hover { background: #1e5bc4; }
    .btn-super { background: var(--super-badge); color: #fff; }
    .btn-super:hover { background: #6d28d9; }
    .btn-sm { padding: 5px 11px; font-size: 11px; }
    .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
    .btn-ghost:hover { border-color: var(--blue-acc); color: var(--blue-acc); background: #f0f5ff; }
    .btn-cancel { background: #f1f5f9; color: var(--muted); border: none; }
    .btn-cancel:hover { background: #e2e8f0; }

    /* ── FILTER BAR ── */
    .filter-bar { padding: 14px 20px; background: #f8f9fc; border-bottom: 1px solid var(--border); display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: var(--muted); }
    .filter-input, .filter-select {
        height: 34px; border: 1px solid var(--border); border-radius: 6px;
        padding: 0 10px; font-size: 12px; color: var(--text);
        background: #fff; font-family: 'Inter', sans-serif; transition: border-color .15s;
    }
    .filter-input:focus, .filter-select:focus { outline: none; border-color: var(--blue-acc); }
    .filter-input.wide { width: 220px; }
    .filter-btn { align-self: flex-end; }

    /* ── TABLE ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    thead th {
        background: #f4f6fb; padding: 10px 16px; text-align: left;
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .8px; color: var(--muted);
        border-bottom: 2px solid var(--border); white-space: nowrap;
    }
    tbody tr { border-bottom: 1px solid #eef1f8; transition: background .12s; }
    tbody tr:hover { background: #f8f9fd; }
    tbody td { padding: 11px 16px; color: var(--text); vertical-align: middle; }
    .empty-state { text-align: center; color: var(--muted); font-style: italic; padding: 32px !important; }

    /* ── BADGES ── */
    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    .badge-admin   { background: #dbeafe; color: #1e40af; }
    .badge-encoder { background: #dcfce7; color: #166534; }
    .badge-auditor { background: #fef3c7; color: #92400e; }
    .badge-staff   { background: #f3e8ff; color: #5b21b6; }
    .badge-super   { background: #ede9fe; color: #4c1d95; border: 1px solid #c4b5fd; }
    .badge-active  { background: #dcfce7; color: #166534; }
    .badge-inactive{ background: #fee2e2; color: #991b1b; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

    /* ── ACTION BUTTONS ── */
    .action-btns { display: flex; gap: 5px; }
    .d-inline { display: inline; }
    .icon-btn {
        width: 28px; height: 28px; border-radius: 5px; border: 1px solid var(--border);
        background: #fff; cursor: pointer; display: inline-flex; align-items: center;
        justify-content: center; font-size: 13px; transition: all .15s;
        color: var(--muted); text-decoration: none; padding: 0;
    }
    .icon-btn.view:hover   { border-color: var(--green);   color: var(--green);    background: #f0fdf4; }
    .icon-btn.edit:hover   { border-color: var(--blue-acc); color: var(--blue-acc); background: #eff6ff; }
    .icon-btn.lock:hover   { border-color: var(--orange);   color: var(--orange);   background: #fff7ed; }
    .icon-btn.delete:hover { border-color: var(--red);      color: var(--red);      background: #fff1f2; }

    /* ── TRAIL LOG TYPES ── */
    .trail-type { font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 7px; border-radius: 4px; }
    .trail-create { background: #dcfce7; color: #166534; }
    .trail-update { background: #dbeafe; color: #1e40af; }
    .trail-delete { background: #fee2e2; color: #991b1b; }
    .trail-login  { background: #f3e8ff; color: #5b21b6; }
    .trail-system { background: #f1f5f9; color: #475569; }
    .ip-code { font-family: 'Courier New', monospace; font-size: 11px; color: var(--muted); background: #f1f5f9; padding: 2px 6px; border-radius: 3px; }

    /* ── QUICK ACTIONS ── */
    .quick-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding: 16px 20px; }
    .quick-card {
        border: 1px solid var(--border); border-radius: 8px; padding: 14px 16px;
        cursor: pointer; transition: all .18s; display: flex; align-items: center; gap: 12px;
        background: #fff; text-decoration: none; font-family: 'Inter', sans-serif; text-align: left;
    }
    .quick-card:hover { border-color: var(--super-badge); background: #faf8ff; transform: translateY(-1px); box-shadow: 0 3px 10px rgba(124,58,237,.1); }
    .quick-icon { font-size: 22px; flex-shrink: 0; }
    .quick-label { font-size: 12px; font-weight: 600; color: var(--text); }
    .quick-desc  { font-size: 11px; color: var(--muted); margin-top: 1px; }

    /* ── PAGINATION ── */
    .pagination-bar { padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); flex-wrap: wrap; gap: 8px; }
    .pagination-info { font-size: 12px; color: var(--muted); }
    .pagination-links { display: flex; gap: 4px; flex-wrap: wrap; }
    .pagination-links a,
    .pagination-links span {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 28px; height: 28px; padding: 0 6px;
        border: 1px solid var(--border); border-radius: 5px;
        font-size: 11px; font-weight: 600; color: var(--muted);
        text-decoration: none; transition: all .15s;
    }
    .pagination-links a:hover { border-color: var(--blue-acc); color: var(--blue-acc); background: #f0f5ff; }
    .pagination-links span[aria-current="page"] span { background: var(--blue-acc); border-color: var(--blue-acc); color: #fff; }

    /* ── MODAL ── */
    .modal-bg { position: fixed; inset: 0; background: rgba(10,18,40,.55); display: none; align-items: center; justify-content: center; z-index: 999; padding: 20px; }
    .modal-bg.open { display: flex; }
    .modal { background: #fff; border-radius: 12px; padding: 28px 32px; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,.2); max-height: 90vh; overflow-y: auto; }
    .modal-head { margin-bottom: 18px; }
    .modal h3 { font-family: 'Rajdhani', sans-serif; font-size: 20px; font-weight: 700; margin-bottom: 4px; }
    .modal p { font-size: 12px; color: var(--muted); }

    /* ── FORM ── */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { display: flex; flex-direction: column; gap: 4px; }
    .form-group.full { grid-column: 1/-1; }
    .form-group label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .7px; }
    .form-group .req { color: var(--red); }
    .form-group input,
    .form-group select {
        height: 36px; border: 1px solid var(--border); border-radius: 7px;
        padding: 0 12px; font-size: 13px; font-family: 'Inter', sans-serif;
        color: var(--text); transition: border-color .15s;
    }
    .form-group input:focus,
    .form-group select:focus { outline: none; border-color: var(--super-badge); box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
    .field-error { font-size: 11px; color: var(--red); margin-top: 2px; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border); }

    /* ── FOOTER ── */
    footer { background: var(--navy-dark); color: #556080; font-size: 11px; padding: 10px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px; flex-shrink: 0; }
    footer a { color: #3b82f6; text-decoration: none; }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    /* ── RESPONSIVE ── */
    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); }
        .quick-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .main { padding: 18px 16px; }
        .sidebar { position: fixed; left: 0; top: calc(var(--govbar-h) + var(--header-h)); height: calc(100vh - var(--govbar-h) - var(--header-h)); transform: translateX(-100%); z-index: 200; }
        .sidebar.open { transform: translateX(0); }
        .sidebar-toggle { display: flex; align-items: center; justify-content: center; }
        .page-header-row { flex-direction: column; align-items: flex-start; gap: 8px; }
        .today-label { text-align: left; }
    }
    @media (max-width: 640px) {
        .gov-bar { font-size: 10px; }
        .header-info h1 { font-size: 18px; }
        .seal-img, .logo-circle { width: 42px; height: 42px; }
        .stat-row { grid-template-columns: 1fr 1fr; gap: 10px; }
        .quick-grid { grid-template-columns: 1fr 1fr; }
        .form-grid { grid-template-columns: 1fr; }
        .form-group.full { grid-column: 1; }
        .modal { padding: 20px 16px; }
        .filter-bar { gap: 8px; }
        .filter-input.wide { width: 100%; }
        footer { flex-direction: column; text-align: center; }
    }
    @media (max-width: 400px) {
        .stat-row { grid-template-columns: 1fr; }
        .quick-grid { grid-template-columns: 1fr; }
    }
    </style>

    @stack('styles')
</head>
<body>

{{-- GOV BAR --}}
<div class="gov-bar">
    <div>
        <strong>Republic of the Philippines</strong>
        &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic
    </div>
    <div class="gov-bar-right">
        <span class="clock" id="clock">--:--:--</span>
        <span class="online"><span class="dot"></span> System Online</span>
    </div>
</div>

{{-- MAIN HEADER --}}
<header class="main-header">
    <div class="header-logos">
        <img src="{{ asset('images/cavite-seal.png') }}" alt="Cavite Seal" class="seal-img"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
        <div class="logo-circle" style="display:none">CAVITE<br>SEAL</div>

        <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal" class="seal-img"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
        <div class="logo-circle" style="display:none">NAIC<br>SEAL</div>
    </div>
    <div class="header-info">
        <div class="label">Office of the Municipal DRRMO</div>
        <h1>MDRRMO – Naic, Cavite</h1>
        <div class="sub">Municipal Disaster Risk Reduction and Management Office</div>
    </div>
    <div class="header-user">
        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <div class="name">{{ Auth::user()->name }}</div>
            <div class="role">
                <span class="super-badge">SUPER ADMIN</span>
                FULL ACCESS
            </div>
        </div>
    </div>
</header>

{{-- BODY LAYOUT --}}
<div class="layout">

    {{-- SIDEBAR --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-inner">

            <div class="sidebar-section">
                <div class="sidebar-section-title">⚡ Super Admin</div>

                <a class="nav-item super-only {{ request()->routeIs('superadmin.accounts.*') ? 'active' : '' }}"
                   href="{{ route('superadmin.accounts.index') }}">
                    <span class="nav-icon">👤</span>
                    Account Management
                    @if(isset($pendingUsers) && $pendingUsers > 0)
                        <span class="nav-badge">{{ $pendingUsers }}</span>
                    @endif
                </a>

                <a class="nav-item super-only {{ request()->routeIs('superadmin.accounts.create') ? 'active' : '' }}"
                   href="{{ route('superadmin.accounts.create') }}">
                    <span class="nav-icon">➕</span>
                    Create Account
                </a>

                <a class="nav-item super-only {{ request()->routeIs('superadmin.roles.*') ? 'active' : '' }}"
                   href="{{ route('superadmin.roles.index') }}">
                    <span class="nav-icon">🔐</span>
                    Role Permissions
                </a>

                <a class="nav-item super-only {{ request()->routeIs('superadmin.trails.advanced') ? 'active' : '' }}"
                   href="{{ route('superadmin.trails.advanced') }}">
                    <span class="nav-icon">🕵️</span>
                    Advanced Trail Logs
                    <span class="nav-badge green">LIVE</span>
                </a>
            </div>

            <hr class="sidebar-divider"/>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Logs</div>
                <a class="nav-item {{ request()->routeIs('superadmin.trails.index') ? 'active' : '' }}"
                   href="{{ route('superadmin.trails.index') }}">
                    <span class="nav-icon">🔍</span>
                    Trail Logs
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <span>↩</span> LOGOUT
            </button>
        </form>
    </nav>

    {{-- MOBILE SIDEBAR TOGGLE --}}
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>

    {{-- MAIN CONTENT --}}
    <main class="main">
        @if(session('success'))
            <div class="alert alert-success">
                ✅ &nbsp;<strong>{{ session('success') }}</strong>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                ⛔ &nbsp;<strong>{{ session('error') }}</strong>
            </div>
        @endif

        @yield('content')
    </main>

</div>

{{-- FOOTER --}}
<footer>
    <span>© {{ date('Y') }} <strong>MDRRMO Naic, Cavite</strong> — Municipal Disaster Risk Reduction and Management Office</span>
    <span>REPUBLIC OF THE PHILIPPINES &nbsp;|&nbsp; <a href="https://facebook.com/naicmdrrmo" target="_blank">facebook.com/naicmdrrmo</a></span>
</footer>

@stack('scripts')

<script>
/* =========================================================
   MDRRMO Super Admin – Embedded JavaScript
   ========================================================= */

/* ── LIVE CLOCK ── */
(function () {
    const el = document.getElementById('clock');
    if (!el) return;
    function tick() {
        const n = new Date(), p = v => String(v).padStart(2,'0');
        el.textContent = p(n.getHours()) + ':' + p(n.getMinutes()) + ':' + p(n.getSeconds());
    }
    tick();
    setInterval(tick, 1000);
})();

/* ── MODAL ── */
const modalBg = document.getElementById('createModal');

function openModal() {
    if (!modalBg) return;
    modalBg.classList.add('open');
    document.body.style.overflow = 'hidden';
    const first = modalBg.querySelector('input, select');
    if (first) setTimeout(() => first.focus(), 80);
}

function closeModal() {
    if (!modalBg) return;
    modalBg.classList.remove('open');
    document.body.style.overflow = '';
}

function appendFieldError(input, message) {
    const span = document.createElement('span');
    span.className = 'field-error';
    span.dataset.client = '1';
    span.textContent = message;
    input.parentNode.appendChild(span);
    input.focus();
}

document.addEventListener('DOMContentLoaded', function () {

    /* Open modal – any element with data-open-modal attribute */
    document.querySelectorAll('[data-open-modal]').forEach(function (btn) {
        btn.addEventListener('click', openModal);
    });

    /* Close button inside modal */
    var closeBtn = document.getElementById('closeModal');
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    /* Click backdrop to close */
    if (modalBg) {
        modalBg.addEventListener('click', function (e) {
            if (e.target === modalBg) closeModal();
        });
    }

    /* Escape key */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalBg && modalBg.classList.contains('open')) closeModal();
    });

    /* ── SIDEBAR TOGGLE (mobile) ── */
    var sidebar   = document.getElementById('sidebar');
    var toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (
                window.innerWidth <= 900 &&
                sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                e.target !== toggleBtn
            ) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* ── CLIENT-SIDE FORM VALIDATION ── */
    var form = document.getElementById('createAccountForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            var pwd  = form.querySelector('#password');
            var conf = form.querySelector('#password_confirmation');
            var valid = true;

            /* Remove previous client-side errors */
            form.querySelectorAll('.field-error[data-client]').forEach(function (el) { el.remove(); });

            if (pwd && pwd.value.length < 8) {
                valid = false;
                appendFieldError(pwd, 'Password must be at least 8 characters.');
            }
            if (pwd && conf && pwd.value !== conf.value) {
                valid = false;
                appendFieldError(conf, 'Passwords do not match.');
            }

            if (!valid) e.preventDefault();
        });
    }

    /* ── AUTO-DISMISS ALERTS after 5 seconds ── */
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .5s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 5000);
    });
});
</script>

</body>
</html>
