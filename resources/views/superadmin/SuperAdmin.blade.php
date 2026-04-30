<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') – MDRRMO Naic, Cavite</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=PT+Serif:wght@700&display=swap" rel="stylesheet"/>

    <style>
    /* =========================================================
       MDRRMO Super Admin – Full Redesign
       ========================================================= */

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        /* Core palette */
        --blue:         #1B3F7A;
        --blue-dark:    #122D5A;
        --blue-mid:     #1E4D96;
        --blue-light:   #2459A8;
        --blue-pale:    #EAF0FA;
        --blue-pale2:   #D6E4F7;
        --yellow:       #F5C518;
        --yellow-dark:  #D4A800;
        --yellow-pale:  #FFFAE6;
        --green:        #16A34A;
        --green-pale:   #DCFCE7;
        --green-dark:   #15803D;
        --red:          #C0392B;
        --red-pale:     #FEF2F2;
        --orange:       #D97706;
        --orange-pale:  #FFF7ED;
        --white:        #FFFFFF;
        --gray-50:      #F8F9FB;
        --gray-100:     #F0F3F7;
        --gray-200:     #E2E7EF;
        --gray-300:     #C8D0DC;
        --gray-400:     #96A0B0;
        --gray-500:     #6B7687;
        --gray-600:     #505A6A;
        --gray-700:     #3A4252;
        --gray-800:     #252E3E;
        --gray-900:     #161D2B;

        /* Super Admin gold */
        --super:        #C89B2A;
        --super-mid:    #B8891A;
        --super-dark:   #9A7010;
        --super-pale:   #FFFBEB;
        --super-pale2:  #FEF3C7;
        --super-border: #FCD34D;

        /* Sidebar (dark) */
        --sb-bg:        #0F1B30;
        --sb-hover:     #182644;
        --sb-active:    #1E3060;
        --sb-border:    rgba(255,255,255,.06);
        --sb-text:      #8FA3C0;
        --sb-text-hi:   #C8D8EE;
        --sb-super:     rgba(200,155,42,.12);
        --sb-super-hi:  rgba(200,155,42,.22);

        /* Layout */
        --sidebar-w:    260px;
        --topbar-h:     32px;
        --header-h:     72px;
    }

    html, body {
        height: 100%;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        background: var(--gray-100);
        color: var(--gray-800);
        font-size: 14px;
        -webkit-font-smoothing: antialiased;
    }

    /* ══ SHELL GRID ══ */
    .shell {
        display: grid;
        grid-template-rows: var(--topbar-h) var(--header-h) 1fr;
        grid-template-columns: var(--sidebar-w) 1fr;
        grid-template-areas:
            "topbar  topbar"
            "header  header"
            "sidebar main";
        height: 100vh;
        overflow: hidden;
        position: relative;
    }

    /* ══ TOP BAR ══ */
    .topbar {
        grid-area: topbar;
        background: var(--gray-900);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 20px; z-index: 110;
        border-bottom: 1px solid rgba(255,255,255,.04);
    }
    .topbar-left {
        font-size: 11px; color: rgba(255,255,255,.35);
        letter-spacing: .3px; font-weight: 500;
    }
    .topbar-left strong { color: rgba(255,255,255,.6); font-weight: 600; }
    .topbar-right { display: flex; align-items: center; gap: 14px; }

    .super-topbar-badge {
        display: flex; align-items: center; gap: 5px;
        font-size: 10px; font-weight: 700; letter-spacing: .9px;
        color: #FDE68A; background: rgba(200,155,42,.2);
        border: 1px solid rgba(200,155,42,.5);
        padding: 2px 8px 2px 7px; border-radius: 3px;
        text-transform: uppercase; white-space: nowrap;
    }
    .super-topbar-badge::before {
        content: ''; width: 5px; height: 5px; border-radius: 50%;
        background: #FBBF24; box-shadow: 0 0 6px #FBBF24;
        animation: blink 2.5s infinite; flex-shrink: 0;
    }
    .topbar-divider { width: 1px; height: 14px; background: rgba(255,255,255,.1); }
    .clock-date-inline { font-size: 11px; color: rgba(255,255,255,.35); font-weight: 500; }
    .clock-inline {
        font-size: 12px; font-weight: 700; color: var(--yellow);
        letter-spacing: 1.5px; font-variant-numeric: tabular-nums;
    }
    .status-indicator {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; color: rgba(255,255,255,.35); font-weight: 500;
    }
    .status-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #4ADE80; box-shadow: 0 0 6px #4ADE80;
        animation: blink 2s infinite; flex-shrink: 0;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    /* ══ HEADER ══ */
    header {
        grid-area: header;
        background: var(--blue-dark);
        border-bottom: 3px solid var(--yellow);
        box-shadow: 0 2px 12px rgba(0,0,0,.2);
        display: flex; align-items: stretch;
        padding: 0; z-index: 100;
    }
    .hamburger {
        display: none; background: none; border: none; cursor: pointer;
        padding: 0 16px; color: rgba(255,255,255,.6); flex-shrink: 0;
        transition: color .15s; border-right: 1px solid rgba(255,255,255,.1);
        align-self: stretch;
        align-items: center; justify-content: center;
        min-width: 48px;
    }
    .hamburger:hover { color: var(--white); background: rgba(255,255,255,.06); }
    .hamburger svg { width: 20px; height: 20px; display: block; }

    /* Logo strip — slightly darker band to visually anchor the logos */
    .header-logo-strip {
        background: var(--blue-dark);
        display: flex; align-items: center; gap: 12px;
        padding: 0 20px; flex-shrink: 0;
        align-self: stretch;
    }
    .header-logo-strip img {
        height: 48px; width: 48px; object-fit: contain;
        border-radius: 50%;
        filter: drop-shadow(0 1px 3px rgba(0,0,0,.4));
    }
    .logo-fallback {
        width: 48px; height: 48px; border-radius: 50%;
        background: rgba(255,255,255,.1); display: none;
        align-items: center; justify-content: center;
        font-size: 7px; font-weight: 700; color: var(--yellow);
        text-align: center; line-height: 1.4; padding: 5px;
        border: 1.5px solid rgba(245,197,24,.4);
        text-transform: uppercase; letter-spacing: .3px; flex-shrink: 0;
    }
    .logo-strip-divider { display: none; }

    /* Header text — white on dark blue */
    .header-text {
        padding: 0 20px; display: flex; flex-direction: column;
        justify-content: center; align-self: stretch;
        min-width: 0; overflow: hidden;
    }
    .header-org {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.8px; color: rgba(255,255,255,.45);
        margin-bottom: 3px; line-height: 1;
    }
    .header-title {
        font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700;
        color: var(--white); line-height: 1.1; letter-spacing: -.3px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 100%;
    }
    .header-sub {
        font-size: 11px; color: rgba(255,255,255,.4); margin-top: 3px;
        line-height: 1.3; font-weight: 400;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .header-spacer { flex: 1; }

    /* User badge — seamless against the dark header */
    .header-user-badge {
        display: flex; align-items: center; gap: 11px;
        padding: 0 22px;
        background: rgba(0,0,0,.2);
        flex-shrink: 0; align-self: stretch;
        min-width: 0;
    }
    .user-avatar-ring {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, #C89B2A 0%, #F5C518 100%);
        display: flex; align-items: center; justify-content: center;
        color: var(--blue-dark); font-weight: 800; font-size: 14px; flex-shrink: 0;
        box-shadow: 0 0 0 2px rgba(245,197,24,.3);
    }
    .user-badge-name {
        font-size: 13px; font-weight: 700; color: var(--white);
        line-height: 1.2; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis; max-width: 140px;
    }
    .user-badge-role { display: flex; align-items: center; gap: 5px; margin-top: 3px; }
    /* Text block beside avatar — hidden on mobile via media query */
    .user-badge-text { display: flex; flex-direction: column; min-width: 0; }
    .super-chip {
        background: linear-gradient(90deg, #C89B2A 0%, #F5C518 100%);
        color: var(--blue-dark); font-size: 9px; font-weight: 800;
        letter-spacing: .8px; padding: 2px 7px; border-radius: 3px;
        text-transform: uppercase; white-space: nowrap;
    }
    .full-access-text {
        font-size: 10px; color: rgba(255,255,255,.45);
        text-transform: uppercase; letter-spacing: .6px; font-weight: 600;
    }

    /* ══ SIDEBAR ══ */
    .sidebar-overlay {
        display: none !important; position: fixed; inset: 0;
        background: rgba(0,0,0,.6); z-index: 290;
        opacity: 0; transition: opacity .25s; pointer-events: none;
        backdrop-filter: blur(2px);
    }
    .sidebar-overlay.active { display: block !important; pointer-events: auto; opacity: 1; }

    .sidebar {
        grid-area: sidebar;
        background: var(--sb-bg);
        display: flex; flex-direction: column;
        overflow-y: auto; overflow-x: hidden;
        position: relative;
        border-right: 1px solid rgba(255,255,255,.06);
    }
    .sidebar::-webkit-scrollbar { width: 3px; }
    .sidebar::-webkit-scrollbar-track { background: transparent; }
    .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }

    .sidebar-close {
        display: none; position: absolute; top: 10px; right: 10px;
        background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
        border-radius: 4px; width: 28px; height: 28px;
        align-items: center; justify-content: center;
        cursor: pointer; z-index: 10; color: var(--sb-text); transition: all .15s;
    }
    .sidebar-close:hover { background: rgba(192,57,43,.2); color: #F87171; border-color: rgba(192,57,43,.35); }
    .sidebar-close svg { width: 14px; height: 14px; }

    /* Section labels — clean, compact */
    .nav-section-label {
        padding: 20px 16px 6px;
        font-size: 9px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 2px;
        color: rgba(255,255,255,.22);
        display: flex; align-items: center; gap: 8px;
        white-space: nowrap;
    }
    .nav-section-label.super-section {
        color: rgba(245,197,24,.55);
    }
    .section-line {
        flex: 1; height: 1px;
        background: currentColor; opacity: .25;
        min-width: 0;
    }

    /* Nav items */
    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 14px 9px 12px;
        font-size: 13px; font-weight: 500;
        color: var(--sb-text); text-decoration: none;
        transition: background .12s, color .12s;
        line-height: 1.3; position: relative;
        margin: 1px 8px; border-radius: 6px;
    }
    .nav-item:hover {
        background: rgba(255,255,255,.06);
        color: #D0DFEE;
    }
    /* Active: solid left accent bar + tinted background */
    .nav-item.active {
        background: rgba(36,89,168,.35);
        color: #E8F0FA; font-weight: 600;
        border-left: 3px solid var(--blue-light);
        padding-left: 9px; /* compensate for 3px border */
    }

    /* Super-only items */
    .nav-item.super-only { color: #C9A84C; }
    .nav-item.super-only:hover {
        background: rgba(200,155,42,.1);
        color: #F0D070;
    }
    .nav-item.super-only.active {
        background: rgba(200,155,42,.18);
        color: #FDE68A; font-weight: 600;
        border-left: 3px solid #F5C518;
        padding-left: 9px;
    }

    .nav-icon {
        width: 16px; height: 16px; flex-shrink: 0;
        opacity: .65; display: flex; align-items: center;
        justify-content: center; color: inherit;
    }
    .nav-item:hover .nav-icon,
    .nav-item.active .nav-icon { opacity: 1; }

    .nav-badge {
        margin-left: auto; font-size: 9px; font-weight: 800;
        padding: 2px 7px; border-radius: 10px;
        background: var(--super); color: #fff; flex-shrink: 0;
    }
    .nav-badge.green { background: var(--green); }
    .nav-badge.live {
        background: transparent; color: #4ADE80;
        border: 1px solid rgba(74,222,128,.45);
        animation: blink 2s infinite;
    }

    .sidebar-sep {
        border: none; border-top: 1px solid rgba(255,255,255,.06);
        margin: 8px 16px;
    }

    /* Logout area */
    .sidebar-bottom {
        margin-top: auto; padding: 12px 14px 16px;
        border-top: 1px solid rgba(255,255,255,.06);
    }
    .logout-btn {
        width: 100%; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 11.5px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .8px;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.1);
        color: rgba(255,255,255,.5);
        padding: 10px 16px; border-radius: 6px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all .18s;
    }
    .logout-btn:hover {
        background: rgba(192,57,43,.2);
        border-color: rgba(192,57,43,.4);
        color: #FCA5A5;
    }
    .logout-btn svg { width: 14px; height: 14px; opacity: .8; }

    /* ══ MAIN CONTENT ══ */
    .main-content {
        grid-area: main;
        background: var(--gray-100);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .main-inner {
        flex: 1;
        padding: 26px 28px;
    }

    /* ── Page title bar ── */
    .page-titlebar {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 22px; padding-bottom: 18px;
        border-bottom: 1px solid var(--gray-200); gap: 12px;
    }
    .page-breadcrumb {
        font-size: 11px; color: var(--gray-400); font-weight: 600;
        text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px;
        display: flex; align-items: center; gap: 5px;
    }
    .page-breadcrumb .bc-link { color: var(--blue-light); }
    .page-breadcrumb .bc-sep { color: var(--gray-300); font-weight: 400; font-size: 13px; }
    .page-h1 {
        font-family: 'PT Serif', serif; font-size: 23px;
        font-weight: 700; color: var(--blue-dark); line-height: 1.2;
        letter-spacing: -.3px;
    }
    .page-sub { font-size: 12px; color: var(--gray-500); margin-top: 4px; line-height: 1.5; }
    .page-date-badge { display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; gap: 2px; }
    .page-date-badge .day {
        font-size: 11px; color: var(--gray-400); font-weight: 600;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .page-date-badge .full-date {
        font-size: 13px; font-weight: 700; color: var(--gray-700); white-space: nowrap;
    }

    /* ── Alerts ── */
    .alert {
        padding: 11px 16px; margin-bottom: 18px;
        display: flex; align-items: center; gap: 10px;
        font-size: 13px; border-radius: 6px; font-weight: 500;
    }
    .alert-success {
        background: var(--green-pale); border: 1px solid #BBF7D0;
        border-left: 4px solid var(--green); color: #065f46;
    }
    .alert-error {
        background: var(--red-pale); border: 1px solid #FECACA;
        border-left: 4px solid var(--red); color: #991b1b;
    }
    .alert-dismiss {
        margin-left: auto; background: none; border: none;
        cursor: pointer; color: inherit; opacity: .4;
        font-size: 18px; line-height: 1; padding: 0 2px; transition: opacity .15s;
    }
    .alert-dismiss:hover { opacity: .9; }

    /* ── Stat cards ── */
    .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 22px; }
    .stat-card {
        background: var(--white); border: 1px solid var(--gray-200);
        border-radius: 8px; padding: 16px 18px;
        display: flex; align-items: center; gap: 14px;
        transition: box-shadow .2s, transform .2s;
        position: relative; overflow: hidden;
    }
    .stat-card::after {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; border-radius: 8px 8px 0 0; background: var(--blue);
    }
    .stat-card.c-green::after  { background: var(--green); }
    .stat-card.c-purple::after { background: var(--super); }
    .stat-card.c-orange::after { background: var(--orange); }
    .stat-card.c-yellow::after { background: var(--yellow-dark); }
    .stat-card:hover { box-shadow: 0 6px 20px rgba(27,63,122,.1); transform: translateY(-2px); }
    .ds-icon {
        width: 40px; height: 40px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ds-icon svg { width: 18px; height: 18px; }
    .ds-icon.blue   { background: var(--blue-pale);   color: var(--blue); }
    .ds-icon.green  { background: var(--green-pale);  color: var(--green); }
    .ds-icon.purple { background: var(--super-pale2); color: var(--super); }
    .ds-icon.orange { background: var(--orange-pale); color: var(--orange); }
    .ds-icon.yellow { background: var(--yellow-pale); color: var(--yellow-dark); }
    .ds-label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: var(--gray-400); margin-bottom: 4px; line-height: 1;
    }
    .ds-value {
        font-size: 26px; font-weight: 800; color: var(--gray-800);
        line-height: 1; font-variant-numeric: tabular-nums;
    }
    .ds-value.v-purple { color: var(--super); }
    .ds-value.v-orange { color: var(--orange); }
    .ds-sub { font-size: 11px; color: var(--gray-400); margin-top: 3px; font-weight: 500; }

    /* ── Panels ── */
    .panel {
        background: var(--white); border: 1px solid var(--gray-200);
        border-radius: 8px; margin-bottom: 20px; overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .panel-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 13px 18px; border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50); flex-wrap: wrap; gap: 8px;
    }
    .panel-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 700; color: var(--blue-dark);
    }
    .panel-title small { font-weight: 500; color: var(--gray-400); font-size: 12px; }
    .panel-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .panel-dot.purple { background: var(--super); }
    .panel-dot.blue   { background: var(--blue); }
    .panel-dot.yellow { background: var(--yellow-dark); }
    .panel-dot.green  { background: var(--green); }
    .panel-dot.orange { background: var(--orange); }
    .panel-count { font-size: 12px; color: var(--gray-400); font-weight: 500; }
    .panel-header-actions { display: flex; gap: 8px; align-items: center; }
    .super-only-badge {
        font-size: 9.5px; background: var(--super-pale2);
        color: var(--super-mid); border: 1px solid var(--super-border);
        padding: 2px 8px; border-radius: 20px; font-weight: 700;
        letter-spacing: .5px; text-transform: uppercase;
    }

    /* ── Buttons ── */
    .btn {
        padding: 7px 14px; border-radius: 5px; border: none;
        font-size: 12px; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all .15s; font-family: 'Plus Jakarta Sans', sans-serif;
        text-decoration: none; letter-spacing: .2px;
    }
    .btn-primary { background: var(--blue); color: #fff; }
    .btn-primary:hover { background: var(--blue-dark); box-shadow: 0 4px 12px rgba(27,63,122,.25); }
    .btn-super {
        background: linear-gradient(135deg, #D4A800 0%, #F5C518 100%);
        color: #fff; box-shadow: 0 2px 8px rgba(200,155,42,.25);
    }
    .btn-super:hover { box-shadow: 0 4px 16px rgba(200,155,42,.4); filter: brightness(1.08); }
    .btn-yellow { background: var(--yellow); color: var(--blue-dark); font-weight: 700; }
    .btn-yellow:hover { background: var(--yellow-dark); box-shadow: 0 4px 12px rgba(212,168,0,.2); }
    .btn-sm { padding: 5px 11px; font-size: 11px; }
    .btn-ghost { background: transparent; border: 1px solid var(--gray-200); color: var(--gray-600); }
    .btn-ghost:hover { border-color: var(--blue-light); color: var(--blue); background: var(--blue-pale); }
    .btn-cancel { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
    .btn-cancel:hover { background: var(--gray-200); }

    /* ── Filter bar ── */
    .filter-bar {
        padding: 13px 18px; background: var(--white);
        border-bottom: 1px solid var(--gray-200);
        display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .9px; color: var(--gray-400);
    }
    .filter-input, .filter-select {
        height: 34px; border: 1.5px solid var(--gray-200); border-radius: 5px;
        padding: 0 11px; font-size: 12.5px; color: var(--gray-800);
        background: var(--gray-50); font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 500; transition: border-color .15s, box-shadow .15s;
    }
    .filter-input:focus, .filter-select:focus {
        outline: none; border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-pale); background: var(--white);
    }
    .filter-input.wide { width: 230px; }
    .filter-btn { align-self: flex-end; }

    /* ── Table ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    thead th {
        background: var(--gray-50); padding: 10px 14px; text-align: left;
        font-size: 9.5px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; color: var(--gray-400);
        border-bottom: 2px solid var(--gray-200); white-space: nowrap;
    }
    tbody tr { border-bottom: 1px solid var(--gray-100); transition: background .1s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: var(--blue-pale); }
    tbody td { padding: 11px 14px; color: var(--gray-700); vertical-align: middle; }
    .empty-state {
        text-align: center; padding: 44px 20px !important;
        color: var(--gray-400); font-style: italic; font-size: 13px;
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 8px; border-radius: 20px;
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; white-space: nowrap;
    }
    .badge-admin    { background: var(--blue-pale);   color: var(--blue); }
    .badge-encoder  { background: var(--green-pale);  color: var(--green-dark); }
    .badge-auditor  { background: var(--yellow-pale); color: #92400e; }
    .badge-staff    { background: var(--super-pale2); color: var(--super-mid); }
    .badge-super    { background: var(--super-pale2); color: var(--super); border: 1px solid var(--super-border); }
    .badge-active   { background: var(--green-pale);  color: var(--green-dark); }
    .badge-inactive { background: var(--red-pale);    color: var(--red); }
    .badge-pending  { background: var(--yellow-pale); color: #92400e; }
    .badge-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

    /* ── Action buttons ── */
    .action-btns { display: flex; gap: 4px; }
    .d-inline { display: inline; }
    .icon-btn {
        width: 28px; height: 28px; border-radius: 5px;
        border: 1.5px solid var(--gray-200); background: var(--white);
        cursor: pointer; display: inline-flex; align-items: center;
        justify-content: center; font-size: 12px; transition: all .15s;
        color: var(--gray-400); text-decoration: none; padding: 0;
    }
    .icon-btn.view:hover   { border-color: var(--green);      color: var(--green);      background: var(--green-pale); }
    .icon-btn.edit:hover   { border-color: var(--blue-light); color: var(--blue);       background: var(--blue-pale); }
    .icon-btn.lock:hover   { border-color: var(--orange);     color: var(--orange);     background: var(--orange-pale); }
    .icon-btn.delete:hover { border-color: var(--red);        color: var(--red);        background: var(--red-pale); }

    /* ── Trail log types ── */
    .trail-type { font-size: 9.5px; font-weight: 700; text-transform: uppercase; padding: 2px 7px; border-radius: 4px; letter-spacing: .3px; }
    .trail-create { background: var(--green-pale); color: var(--green-dark); }
    .trail-update { background: var(--blue-pale);  color: var(--blue); }
    .trail-delete { background: var(--red-pale);   color: var(--red); }
    .trail-login  { background: var(--super-pale2);color: var(--super); }
    .trail-system { background: var(--gray-100);   color: var(--gray-600); }
    .ip-code {
        font-family: 'Courier New', monospace; font-size: 11px;
        color: var(--gray-600); background: var(--gray-100);
        padding: 2px 7px; border-radius: 4px;
    }

    /* ── Quick actions ── */
    .quick-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 14px 18px; }
    .quick-card {
        border: 1.5px solid var(--gray-200); border-radius: 7px;
        padding: 13px 15px; cursor: pointer; transition: all .15s;
        display: flex; align-items: center; gap: 12px;
        background: var(--white); text-decoration: none;
        font-family: 'Plus Jakarta Sans', sans-serif; text-align: left;
    }
    .quick-card:hover { border-color: var(--blue-pale2); box-shadow: 0 4px 14px rgba(27,63,122,.1); transform: translateY(-1px); }
    .quick-card.super-action:hover { border-color: var(--super-border); box-shadow: 0 4px 14px rgba(200,155,42,.15); }
    .quick-icon {
        width: 34px; height: 34px; border-radius: 7px;
        background: var(--blue-pale); display: flex; align-items: center;
        justify-content: center; flex-shrink: 0; color: var(--blue);
    }
    .quick-card.super-action .quick-icon { background: var(--super-pale2); color: var(--super); }
    .quick-label { font-size: 12px; font-weight: 700; color: var(--blue-dark); }
    .quick-desc  { font-size: 11px; color: var(--gray-500); margin-top: 2px; }

    /* ── Pagination ── */
    .pagination-bar {
        padding: 11px 18px; display: flex;
        justify-content: space-between; align-items: center;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50); flex-wrap: wrap; gap: 8px;
        border-radius: 0 0 8px 8px;
    }
    .pagination-info { font-size: 12px; color: var(--gray-400); font-weight: 500; }
    .pagination-links { display: flex; gap: 3px; flex-wrap: wrap; }
    .pagination-links a,
    .pagination-links span {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 30px; height: 30px; padding: 0 7px;
        border: 1.5px solid var(--gray-200); border-radius: 5px;
        font-size: 11px; font-weight: 700; color: var(--gray-400);
        text-decoration: none; transition: all .15s; background: var(--white);
    }
    .pagination-links a:hover { border-color: var(--blue-light); color: var(--blue); background: var(--blue-pale); }
    .pagination-links span[aria-current="page"] span { background: var(--blue); border-color: var(--blue); color: var(--white); }

    /* ── Modal ── */
    .modal-bg {
        position: fixed; inset: 0;
        background: rgba(9,18,40,.65);
        backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center;
        z-index: 999; padding: 20px;
    }
    .modal-bg.open { display: flex; }
    .modal {
        background: var(--white); border-radius: 10px; overflow: hidden;
        width: 100%; max-width: 480px;
        box-shadow: 0 24px 64px rgba(0,0,0,.22);
        max-height: 92vh; overflow-y: auto;
    }
    .modal-topbar {
        background: var(--blue-dark); padding: 16px 22px;
        display: flex; align-items: flex-start; justify-content: space-between;
        border-bottom: 3px solid var(--yellow);
    }
    .modal h3 {
        font-family: 'PT Serif', serif; font-size: 18px;
        font-weight: 700; color: var(--white); line-height: 1.25;
    }
    .modal-subtitle { font-size: 11px; color: rgba(255,255,255,.45); margin-top: 3px; }
    .modal-close-btn {
        background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15);
        border-radius: 4px; width: 28px; height: 28px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: rgba(255,255,255,.6); transition: all .15s;
        margin-left: 12px; margin-top: 1px;
    }
    .modal-close-btn:hover { background: rgba(255,255,255,.2); color: #fff; }
    .modal-close-btn svg { width: 14px; height: 14px; }
    .modal-body { padding: 22px 22px; }

    /* ── Form ── */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group.full { grid-column: 1/-1; }
    .form-group label {
        font-size: 10.5px; font-weight: 700; color: var(--gray-500);
        text-transform: uppercase; letter-spacing: .9px;
    }
    .form-group .req { color: var(--red); }
    .form-group input,
    .form-group select {
        height: 37px; border: 1.5px solid var(--gray-200); border-radius: 5px;
        padding: 0 12px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--gray-800); background: var(--gray-50); font-weight: 500;
        transition: border-color .15s, box-shadow .15s;
    }
    .form-group input:focus, .form-group select:focus {
        outline: none; border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-pale); background: var(--white);
    }
    .field-error { font-size: 11px; color: var(--red); margin-top: 1px; font-weight: 500; }
    .modal-actions {
        display: flex; gap: 8px; justify-content: flex-end;
        margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--gray-200);
    }

    /* ── Footer — naturally at bottom of scroll ── */
    footer {
        background: var(--gray-900);
        border-top: 1px solid rgba(255,255,255,.06);
        color: rgba(255,255,255,.28);
        font-size: 11px; padding: 0 24px; height: 44px;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 6px; flex-shrink: 0;
    }
    footer strong { color: rgba(255,255,255,.5); }
    footer a { color: var(--yellow); text-decoration: none; opacity: .75; }
    footer a:hover { opacity: 1; text-decoration: underline; }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--gray-300); }

    /* ═══════════════════════════════════════════
       RESPONSIVE — unified mobile system
       Breakpoints: 1100 → 900 → 640 → 420
    ═══════════════════════════════════════════ */

    /* ── 1100px: tablet landscape ── */
    @media (max-width: 1100px) {
        .stat-row   { grid-template-columns: repeat(2, 1fr); }
        .quick-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* ── 900px: sidebar goes off-canvas ── */
    @media (max-width: 900px) {
        .shell {
            grid-template-rows: var(--topbar-h) var(--header-h) 1fr;
            grid-template-columns: 1fr;
            grid-template-areas:
                "topbar"
                "header"
                "main";
            overflow: visible;
            height: 100vh;
        }
        /* Sidebar: full-height overlay, removed from grid flow */
        .sidebar {
            grid-area: unset;
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            z-index: 300;
            transform: translateX(-100%);
            transition: transform .25s cubic-bezier(.4,0,.2,1);
            box-shadow: 6px 0 24px rgba(0,0,0,.35);
        }
        .sidebar.open { transform: translateX(0); }
        .sidebar-close { display: flex; }
        .hamburger { display: flex; }
        /* Main must scroll independently */
        .main-content { overflow-y: auto; height: calc(100vh - var(--topbar-h) - var(--header-h)); }
        .main-inner { padding: 18px 16px; }

        /* ── Table → Card layout ── */
        .table-wrap { overflow: visible; }
        table { display: block; }
        table thead { display: none; }
        table tbody {
            display: flex; flex-direction: column; gap: 10px; padding: 12px;
        }
        table tbody tr {
            display: flex;
            flex-direction: column;
            background: var(--white);
            border: 1px solid var(--gray-200) !important;
            border-radius: 10px;
            padding: 12px 14px;
            gap: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }
        table tbody tr:hover { background: var(--blue-pale); }

        /* Each cell = label left, value right */
        table tbody td {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 7px 0 !important;
            border: none !important;
            border-bottom: 1px solid var(--gray-100) !important;
            font-size: 12.5px;
            color: var(--gray-700);
        }
        table tbody td:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }

        /* data-label becomes left-side label — scoped only to table td */
        table tbody td[data-label]::before {
            content: attr(data-label);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .9px;
            color: var(--gray-400);
            flex-shrink: 0;
            min-width: 95px;
            line-height: 1.4;
            align-self: flex-start;
            padding-top: 1px;
        }

        /* Account cell: avatar+name block should be right-aligned value */
        table tbody td[data-label="Account"] { align-items: flex-start; }
        table tbody td[data-label="Account"] > div { text-align: right; }

        /* Row counter: compact header line, no label */
        table tbody td[data-label="#"] {
            justify-content: flex-end;
            font-size: 10px;
            color: var(--gray-400);
            font-weight: 700;
            padding: 2px 0 6px !important;
            border-bottom: 1px solid var(--gray-200) !important;
        }
        table tbody td[data-label="#"]::before { display: none; }

        /* Actions cell: right-aligned buttons, no label */
        table tbody td[data-label="Actions"] {
            padding-top: 10px !important;
            justify-content: flex-end;
        }
        table tbody td[data-label="Actions"]::before { display: none; }

        /* Empty state row stays clean */
        table tbody tr:has(td[colspan]) {
            display: block;
            border: none !important;
            box-shadow: none;
            background: transparent;
            padding: 0;
        }

        /* Filter bar: stack inputs */
        .filter-bar { flex-direction: column; align-items: stretch; gap: 10px; }
        .filter-group { width: 100%; }
        .filter-input, .filter-select { width: 100%; }
        .filter-input.wide { width: 100%; }

        /* Log summary strip: 2-col grid */
        .log-summary { grid-template-columns: repeat(2, 1fr); }
        .log-sum-item { border-right: none !important; border-bottom: 1px solid var(--gray-200); }
        .log-sum-item:nth-child(odd) { border-right: 1px solid var(--gray-200) !important; }
        .log-sum-item:nth-last-child(-n+2) { border-bottom: none; }
    }

    /* ── 640px: phones ── */
    @media (max-width: 640px) {
        /* Topbar */
        .topbar-left { display: none; }
        .clock-date-inline { display: none; }

        /* Header: compact without overflowing */
        .header-org   { display: none; }
        .header-title { font-size: 14px; }
        .header-sub   { display: none; }
        .header-text  { padding: 0 10px; }
        .header-logo-strip img { height: 34px; width: 34px; }
        .header-logo-strip { padding: 0 8px; gap: 6px; }
        /* Drop second logo on small screens to give title breathing room */
        .header-logo-strip img:nth-child(n+3),
        .header-logo-strip .logo-fallback:nth-child(n+3) { display: none !important; }
        .header-user-badge { padding: 0 12px; gap: 8px; }
        /* On mobile: hide the name, keep the Super Admin chip */
        .user-badge-name { display: none; }
        .full-access-text { display: none; }
        .super-chip { font-size: 8px; padding: 2px 6px; }

        /* Main padding */
        .main-inner { padding: 14px 12px; }

        /* Page titlebar */
        .page-titlebar {
            flex-direction: column; align-items: flex-start;
            gap: 8px; padding-bottom: 14px; margin-bottom: 16px;
        }
        .page-date-badge { flex-direction: row; align-items: center; gap: 6px; }
        .page-date-badge .day::after { content: ','; }
        .page-h1 { font-size: 19px; }

        /* Stat cards: 2-col */
        .stat-row { grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px; }
        .stat-card { padding: 12px 14px; gap: 10px; }
        .ds-value  { font-size: 22px; }
        .ds-icon   { width: 34px; height: 34px; }

        /* Quick grid: 2-col */
        .quick-grid { grid-template-columns: 1fr 1fr; gap: 8px; padding: 10px 12px; }
        .quick-card { padding: 10px 12px; gap: 8px; flex-direction: column; align-items: flex-start; }
        .quick-label { font-size: 11.5px; }
        .quick-desc  { font-size: 10.5px; }

        /* Forms */
        .form-grid { grid-template-columns: 1fr; }
        .form-group.full { grid-column: 1; }
        .modal-body { padding: 16px 14px; }

        /* Panels */
        .panel-header { padding: 10px 14px; flex-wrap: wrap; }
        .panel-title  { font-size: 12px; }

        /* Pagination */
        .pagination-bar { flex-direction: column; align-items: flex-start; gap: 8px; padding: 10px 14px; }
        .pagination-info { font-size: 11px; }

        /* Footer */
        footer { flex-direction: column; text-align: center; height: auto; padding: 12px 14px; gap: 4px; }

        /* Alerts */
        .alert { font-size: 12px; padding: 10px 12px; }

        /* Log summary */
        .log-summary { grid-template-columns: repeat(2, 1fr); }
        .log-sum-item { padding: 10px 12px; gap: 8px; }
        .log-sum-val  { font-size: 16px; }

        /* Table card label min-width on phones */
        table tbody td::before { min-width: 80px; }
    }

    /* ── 420px: very small phones ── */
    @media (max-width: 420px) {
        .stat-row   { grid-template-columns: 1fr 1fr; }
        .quick-grid { grid-template-columns: 1fr; }
        .topbar-right { gap: 8px; }
        .main-inner { padding: 12px 10px; }
        .user-badge-name { max-width: 70px; }
        .header-title { font-size: 12px; }
        table tbody td::before { min-width: 70px; }
    }
    </style>

    @stack('styles')
</head>
<body>

<div class="shell">

    {{-- ══ TOP BAR ══ --}}
    <div class="topbar">
        <div class="topbar-left">
            <strong>Republic of the Philippines</strong>
            &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic
        </div>
        <div class="topbar-right">
            <span class="super-topbar-badge">Super Admin</span>
            <div class="topbar-divider"></div>
            <span class="clock-date-inline" id="clockDate"></span>
            <span class="clock-inline" id="clock">--:--:--</span>
            <div class="topbar-divider"></div>
            <span class="status-indicator">
                <span class="status-dot"></span>
                System Online
            </span>
        </div>
    </div>

    {{-- ══ HEADER ══ --}}
    <header>
        <button class="hamburger" id="hamburger" aria-label="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="header-logo-strip">
            <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
            <div class="logo-fallback">NAIC<br>SEAL</div>
            <div class="logo-strip-divider"></div>
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
            <div class="logo-fallback">MDRRMO<br>NAIC</div>
        </div>

        <div class="header-text">
            <div class="header-org">Office of the Municipal DRRMO</div>
            <div class="header-title">MDRRMO – Naic, Cavite</div>
            <div class="header-sub">Municipal Disaster Risk Reduction and Management Office</div>
        </div>

        <div class="header-spacer"></div>

        <div class="header-user-badge">
            <div class="user-avatar-ring">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="user-badge-text">
                <div class="user-badge-name">{{ Auth::user()->name }}</div>
                <div class="user-badge-role">
                    <span class="super-chip">Super Admin</span>
                    <span class="full-access-text">Full Access</span>
                </div>
            </div>
        </div>
    </header>

    {{-- ══ SIDEBAR ══ --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <nav class="sidebar" id="sidebar">
        <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="nav-section-label super-section">
            Super Admin<span class="section-line"></span>
        </div>

        <a class="nav-item super-only {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"
           href="{{ route('superadmin.dashboard') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </span>
            Dashboard
        </a>

        {{-- Active only on the index itself, not on create/show/etc --}}
        <a class="nav-item super-only {{ request()->routeIs('superadmin.accounts.index') || request()->routeIs('superadmin.accounts.show') || request()->routeIs('superadmin.accounts.archived') ? 'active' : '' }}"
           href="{{ route('superadmin.accounts.index') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </span>
            Account Management
            @if(isset($pendingUsers) && $pendingUsers > 0)
                <span class="nav-badge">{{ $pendingUsers }}</span>
            @endif
        </a>

        <a class="nav-item super-only {{ request()->routeIs('superadmin.accounts.create') || request()->routeIs('superadmin.accounts.store') ? 'active' : '' }}"
           href="{{ route('superadmin.accounts.create') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </span>
            Create Account
        </a>

        <a class="nav-item super-only {{ request()->routeIs('superadmin.roles.*') ? 'active' : '' }}"
           href="{{ route('superadmin.roles.index') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </span>
            Role Permissions
        </a>

        <hr class="sidebar-sep"/>

        <div class="nav-section-label">
            Logs<span class="section-line"></span>
        </div>

        <a class="nav-item super-only {{ request()->routeIs('superadmin.trails.advanced') ? 'active' : '' }}"
           href="{{ route('superadmin.trails.advanced') }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </span>
            Advanced Trail Logs
            <span class="nav-badge live">LIVE</span>
        </a>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- ══ MAIN CONTENT ══ --}}
    <main class="main-content">
        <div class="main-inner">

        @if(session('success'))
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <strong>{{ session('success') }}</strong>
                <button class="alert-dismiss" onclick="this.closest('.alert').remove()" aria-label="Dismiss">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <strong>{{ session('error') }}</strong>
                <button class="alert-dismiss" onclick="this.closest('.alert').remove()" aria-label="Dismiss">&times;</button>
            </div>
        @endif

        @yield('content')

        </div>{{-- .main-inner --}}

        {{-- ══ FOOTER — always at bottom of scroll ══ --}}
        <footer>
            <span>© {{ date('Y') }} <strong>MDRRMO Naic, Cavite</strong> — Municipal Disaster Risk Reduction and Management Office</span>
            <span>Republic of the Philippines &nbsp;|&nbsp; <a href="https://facebook.com/naicmdrrmo" target="_blank">facebook.com/naicmdrrmo</a></span>
        </footer>

    </main>

</div>{{-- .shell --}}

{{-- ══ MODAL ══ --}}
<div class="modal-bg" id="createModal">
    <div class="modal">
        <div class="modal-topbar">
            <div>
                <h3>@yield('modal-title', 'Create Account')</h3>
                <div class="modal-subtitle">@yield('modal-subtitle', 'Fill in the details below.')</div>
            </div>
            <button class="modal-close-btn" id="closeModal" aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            @yield('modal-body')
        </div>
    </div>
</div>

@stack('scripts')

<script>
/* ── Live clock ── */
(function () {
    const clockEl = document.getElementById('clock');
    const dateEl  = document.getElementById('clockDate');
    const days    = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const months  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    function tick() {
        const n = new Date(), p = v => String(v).padStart(2,'0');
        if (clockEl) clockEl.textContent = p(n.getHours())+':'+p(n.getMinutes())+':'+p(n.getSeconds());
        if (dateEl)  dateEl.textContent  = days[n.getDay()]+', '+months[n.getMonth()]+' '+n.getDate();
    }
    tick(); setInterval(tick, 1000);
})();

document.addEventListener('DOMContentLoaded', function () {

    /* Sidebar */
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('hamburger');
    const closeBtn  = document.getElementById('sidebarClose');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow='hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow=''; }
    if (hamburger) hamburger.addEventListener('click', openSidebar);
    if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
    if (overlay)   overlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => { if (e.key==='Escape') closeSidebar(); });

    /* Modal */
    const modalBg = document.getElementById('createModal');
    window.openModal = function () {
        if (!modalBg) return;
        modalBg.classList.add('open'); document.body.style.overflow='hidden';
        const first = modalBg.querySelector('input, select');
        if (first) setTimeout(() => first.focus(), 80);
    };
    window.closeModal = function () {
        if (!modalBg) return;
        modalBg.classList.remove('open'); document.body.style.overflow='';
    };
    document.querySelectorAll('[data-open-modal]').forEach(b => b.addEventListener('click', openModal));
    const closeModalBtn = document.getElementById('closeModal');
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (modalBg) modalBg.addEventListener('click', e => { if (e.target===modalBg) closeModal(); });
    document.addEventListener('keydown', e => {
        if (e.key==='Escape' && modalBg && modalBg.classList.contains('open')) closeModal();
    });

    /* Form validation */
    const form = document.getElementById('createAccountForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const pwd=form.querySelector('#password'), conf=form.querySelector('#password_confirmation');
            let valid=true;
            form.querySelectorAll('.field-error[data-client]').forEach(el => el.remove());
            function appendErr(input, msg) {
                const s=document.createElement('span'); s.className='field-error'; s.dataset.client='1'; s.textContent=msg;
                input.parentNode.appendChild(s); input.focus();
            }
            if (pwd && pwd.value.length<8)          { valid=false; appendErr(pwd,'Password must be at least 8 characters.'); }
            if (pwd && conf && pwd.value!==conf.value) { valid=false; appendErr(conf,'Passwords do not match.'); }
            if (!valid) e.preventDefault();
        });
    }

    /* Auto-dismiss alerts */
    document.querySelectorAll('.alert').forEach(a => {
        setTimeout(() => {
            a.style.transition='opacity .5s ease'; a.style.opacity='0';
            setTimeout(() => a.remove(), 500);
        }, 5000);
    });
});
</script>

</body>
</html>