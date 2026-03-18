<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Auditor Dashboard</title>
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
            --red-border: #FECACA;
            --green:      #16A34A;
            --green-pale: #F0FDF4;
            --green-border:#BBF7D0;
            --orange:     #D97706;
            --orange-pale:#FFFBEB;
            --orange-border:#FDE68A;
            --purple:     #5B3FA6;
            --purple-dark:#3D1F8A;
            --purple-pale:#F5F0FF;
            --purple-border:#D8CBF5;
            --sky:        #0EA5E9;
            --sky-dark:   #0369A1;
            --sky-pale:   #F0F9FF;
            --sky-border: #BAE6FD;
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
        .topbar-left {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 0.3px;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .clock-inline {
            font-size: 12px;
            font-weight: 600;
            color: var(--yellow);
            letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
        }
        .clock-date-inline {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }
        .status-indicator::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #4CAF50;
            box-shadow: 0 0 5px #4CAF50;
            animation: blink 2s infinite;
        }
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

        .header-logos {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .header-logos img {
            height: 54px; width: 54px;
            object-fit: contain;
        }
        .logo-divider {
            width: 1px; height: 44px;
            background: var(--gray-200);
        }
        .header-text { margin-left: 4px; }
        .header-org {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400); margin-bottom: 2px;
        }
        .header-title {
            font-family: 'PT Serif', serif;
            font-size: 18px; font-weight: 700;
            color: var(--blue-dark); line-height: 1.2;
        }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }

        .header-spacer { flex: 1; }

        .header-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background: var(--sky-pale);
            border: 1px solid var(--sky-border);
            border-radius: 4px;
        }
        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--sky);
            display: flex; align-items: center; justify-content: center;
            color: var(--white);
            font-weight: 700; font-size: 13px;
            flex-shrink: 0;
        }
        .user-name {
            font-size: 13px; font-weight: 600;
            color: var(--sky-dark); line-height: 1.2;
        }
        .user-role {
            font-size: 10px; color: #0284C7;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none !important;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 200;
            opacity: 0;
            transition: opacity 0.25s;
            pointer-events: none;
        }
        .sidebar-overlay.active {
            display: block !important;
            opacity: 1;
            pointer-events: auto;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
        }

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
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            font-size: 13.5px; font-weight: 500;
            color: var(--gray-600);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
            cursor: pointer;
        }
        .nav-item:hover {
            background: var(--gray-50);
            color: var(--blue);
            border-left-color: var(--blue-light);
        }
        .nav-item.active {
            background: var(--blue-pale);
            color: var(--blue);
            border-left-color: var(--blue);
            font-weight: 600;
        }
        .nav-icon {
            width: 17px; height: 17px;
            flex-shrink: 0; color: inherit; opacity: 0.7;
        }
        .nav-item.active .nav-icon,
        .nav-item:hover .nav-icon { opacity: 1; }

        .nav-badge-view {
            margin-left: auto;
            background: var(--gray-400);
            color: var(--white);
            font-size: 9px; font-weight: 700;
            padding: 2px 8px; border-radius: 10px;
            letter-spacing: 0.5px;
        }

        .sidebar-sep {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 8px 0;
        }

        .role-notice {
            margin: 12px 14px;
            background: var(--purple-pale);
            border: 1px solid var(--purple-border);
            border-left: 3px solid var(--purple);
            padding: 10px 12px;
            border-radius: 2px;
        }
        .role-notice-title {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--purple-dark); margin-bottom: 3px;
        }
        .role-notice-text {
            font-size: 11px;
            color: #4B3080;
            line-height: 1.5;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
        }
        .logout-btn {
            width: 100%;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            background: var(--blue);
            color: var(--white);
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: flex; align-items: center;
            justify-content: center; gap: 8px;
            transition: background 0.15s;
        }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content {
            grid-area: main;
            background: var(--gray-50);
            overflow-y: auto;
            padding: 28px 32px;
        }

        .page-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
            gap: 12px;
        }
        .page-breadcrumb {
            font-size: 11px; color: var(--gray-400);
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 {
            font-family: 'PT Serif', serif;
            font-size: 22px; font-weight: 700;
            color: var(--blue-dark);
        }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date {
            font-size: 12px; color: var(--gray-600);
            text-align: right; flex-shrink: 0;
        }
        .page-date strong {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--gray-800); white-space: nowrap;
        }

        /* Welcome card */
        .welcome-card {
            background: var(--blue);
            border-left: 5px solid var(--yellow);
            padding: 22px 28px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }
        .welcome-card img {
            width: 50px; height: 50px;
            object-fit: contain; flex-shrink: 0;
        }
        .welcome-label {
            font-size: 11px; text-transform: uppercase;
            letter-spacing: 1.5px; color: rgba(255,255,255,0.55);
            margin-bottom: 4px;
        }
        .welcome-heading {
            font-family: 'PT Serif', serif;
            font-size: 20px; font-weight: 700; color: var(--white);
        }
        .welcome-heading em { color: var(--yellow); font-style: normal; }
        .welcome-desc { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; }

        /* Access notice */
        .access-notice {
            background: var(--white);
            border: 1px solid var(--purple-border);
            border-left: 4px solid var(--purple);
            padding: 14px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .access-notice svg { width: 18px; height: 18px; color: var(--purple); flex-shrink: 0; margin-top: 1px; }
        .access-notice-text { font-size: 12px; color: var(--gray-600); line-height: 1.6; }
        .access-notice-text strong { color: var(--blue-dark); }

        /* Quick view cards */
        .quick-nav {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .qnav-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            padding: 18px 20px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: box-shadow 0.15s, border-top-color 0.15s;
            position: relative;
        }
        .qnav-card:hover {
            box-shadow: 0 3px 12px rgba(27,63,122,0.12);
            border-top-color: var(--yellow);
        }
        .qnav-icon {
            width: 32px; height: 32px;
            background: var(--blue-pale);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
        }
        .qnav-icon svg { width: 17px; height: 17px; color: var(--blue); }
        .qnav-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .qnav-desc { font-size: 11px; color: var(--gray-600); }
        .qnav-readonly {
            position: absolute;
            top: 10px; right: 12px;
            font-size: 8px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            background: var(--gray-100);
            color: var(--gray-400);
            padding: 2px 7px;
            border-radius: 10px;
            border: 1px solid var(--gray-200);
        }

        /* ─── CONTENT AREA ─── */
        .content-area {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .content-area-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
        }
        .ca-header-left { display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); }
        .ca-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .ca-subtitle { font-size: 11px; color: var(--gray-400); }

        /* Stat grid */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .stat-card {
            padding: 20px 22px;
            border-right: 1px solid var(--gray-100);
            position: relative;
        }
        .stat-card:last-child { border-right: none; }
        .stat-card-accent {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--blue);
        }
        .stat-card-accent.yellow  { background: var(--yellow); }
        .stat-card-accent.green   { background: var(--green); }
        .stat-card-accent.red     { background: var(--red); }
        .stat-card-accent.orange  { background: var(--orange); }
        .stat-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--gray-400); margin-bottom: 8px;
        }
        .stat-number {
            font-family: 'PT Serif', serif;
            font-size: 32px; font-weight: 700;
            color: var(--blue-dark); line-height: 1;
            margin-bottom: 6px;
        }
        .stat-sub {
            font-size: 11px; color: var(--gray-600);
            display: flex; flex-wrap: wrap; gap: 8px;
        }
        .stat-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 10px;
            font-size: 10px; font-weight: 600;
        }
        .stat-pill.approved  { background: var(--green-pale);  color: var(--green);  border: 1px solid var(--green-border); }
        .stat-pill.pending   { background: var(--orange-pale); color: var(--orange); border: 1px solid var(--orange-border); }
        .stat-pill.ongoing   { background: var(--sky-pale);    color: var(--sky-dark); border: 1px solid var(--sky-border); }
        .stat-pill.completed { background: var(--green-pale);  color: var(--green);  border: 1px solid var(--green-border); }
        .stat-pill.upcoming  { background: var(--orange-pale); color: var(--orange); border: 1px solid var(--orange-border); }
        .stat-pill.high      { background: var(--red-pale);    color: var(--red);    border: 1px solid var(--red-border); }
        .stat-pill.today     { background: var(--blue-pale);   color: var(--blue);   border: 1px solid #C7D8F5; }

        /* Two-column section */
        .content-body {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 0;
        }
        .content-panel {
            padding: 20px 22px;
        }
        .content-panel + .content-panel {
            border-left: 1px solid var(--gray-100);
        }
        .panel-title {
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--gray-400); margin-bottom: 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .panel-title::after {
            content: '';
            flex: 1; height: 1px;
            background: var(--gray-100);
        }

        /* Recent activity table */
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .activity-table th {
            text-align: left;
            padding: 7px 10px;
            color: var(--gray-400);
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-100);
        }
        .activity-table td {
            padding: 9px 10px;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }
        .activity-table tr:last-child td { border-bottom: none; }
        .activity-table tr:hover td { background: var(--gray-50); }

        .action-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .action-badge.cat-household    { background: var(--blue-pale);    color: var(--blue-dark); }
        .action-badge.cat-distribution { background: var(--green-pale);   color: var(--green); }
        .action-badge.cat-auth         { background: var(--sky-pale);     color: var(--sky-dark); }
        .action-badge.cat-qr_code      { background: var(--orange-pale);  color: var(--orange); }
        .action-badge.cat-general      { background: var(--gray-100);     color: var(--gray-600); }

        .severity-dot {
            display: inline-block;
            width: 7px; height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .severity-dot.high   { background: var(--red); }
        .severity-dot.medium { background: var(--orange); }
        .severity-dot.low    { background: var(--green); }

        .time-ago { color: var(--gray-400); white-space: nowrap; font-size: 11px; }
        .user-cell { font-weight: 600; color: var(--gray-800); }
        .desc-cell { color: var(--gray-600); max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Quick-links panel */
        .quick-link-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12.5px; font-weight: 500;
            color: var(--gray-600);
            transition: background 0.12s, color 0.12s;
            margin-bottom: 4px;
        }
        .quick-link-item:hover {
            background: var(--blue-pale);
            color: var(--blue);
        }
        .quick-link-icon {
            width: 30px; height: 30px;
            border-radius: 4px;
            background: var(--gray-100);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background 0.12s;
        }
        .quick-link-item:hover .quick-link-icon { background: #d0e1f7; }
        .quick-link-icon svg { width: 15px; height: 15px; color: var(--blue); }
        .quick-link-arrow { margin-left: auto; color: var(--gray-200); }
        .quick-link-item:hover .quick-link-arrow { color: var(--blue-light); }

        .ql-divider {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 8px 0;
        }

        .view-all-link {
            display: block;
            text-align: center;
            padding: 10px;
            font-size: 11px; font-weight: 600;
            color: var(--blue-light);
            text-decoration: none;
            border-top: 1px solid var(--gray-100);
            margin-top: 4px;
            transition: background 0.12s, color 0.12s;
        }
        .view-all-link:hover { background: var(--blue-pale); color: var(--blue); }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            gap: 8px;
            z-index: 100;
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

        /* Tablet — sidebar collapses to drawer */
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

            /* Quick nav: 2 columns */
            .quick-nav { grid-template-columns: repeat(2, 1fr); }

            /* Stat cards: 2 columns */
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .stat-card { border-right: none; border-bottom: 1px solid var(--gray-100); }
            .stat-card:last-child { border-bottom: none; }

            /* Content body: stack vertically */
            .content-body { grid-template-columns: 1fr; }
            .content-panel + .content-panel {
                border-left: none;
                border-top: 1px solid var(--gray-100);
            }

            /* Activity table: hide Description column */
            .activity-table .col-desc { display: none; }
            .desc-cell { display: none; }
        }

        /* Large mobile */
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
            .page-date { text-align: left; }
            .welcome-card { padding: 16px 18px; gap: 14px; }
            .welcome-card img { width: 38px; height: 38px; }
            .welcome-heading { font-size: 16px; }
            .welcome-desc { display: none; }
            .access-notice { padding: 12px 14px; }
            .access-notice-text { font-size: 11px; }

            /* Quick nav: 2 columns, compact */
            .quick-nav { grid-template-columns: 1fr 1fr; gap: 10px; }
            .qnav-card { padding: 14px; gap: 8px; }
            .qnav-title { font-size: 12px; }
            .qnav-desc { display: none; }
            .qnav-readonly { display: none; }

            /* Stat cards: 2 columns, compact */
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .stat-number { font-size: 26px; }
            .stat-label { font-size: 9px; }
            .stat-card { padding: 14px 14px 12px; }

            /* Content area header: hide subtitle */
            .ca-subtitle { display: none; }

            /* Activity table: show only User + Action + Time */
            .activity-table .col-desc,
            .activity-table .col-user { display: none; }
            .desc-cell, .user-cell { display: none; }
            .activity-table th, .activity-table td { padding: 8px 8px; }

            /* Quick links: tighten */
            .quick-link-item { padding: 9px 8px; font-size: 12px; }
            .quick-link-icon { width: 26px; height: 26px; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        /* Small mobile */
        @media (max-width: 420px) {
            .quick-nav { grid-template-columns: 1fr 1fr; gap: 8px; }
            .qnav-card { padding: 12px 10px; }

            /* Stat cards: full width stack */
            .stat-grid { grid-template-columns: 1fr; }
            .stat-card { padding: 14px 16px; }
            .stat-number { font-size: 28px; }

            /* Activity table: show only severity + action + time */
            .activity-table .col-user { display: none; }
            .user-cell { display: none; }

            .content-panel { padding: 16px 14px; }
        }

        @media (max-width: 360px) {
            .quick-nav { grid-template-columns: 1fr; }
            .main-content { padding: 12px 10px; }
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
                <div class="user-name">{{ auth()->user()->name }}</div>
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

        <a href="{{ route('auditor.dashboard') }}" class="nav-item active" onclick="closeSidebar()">
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

        <a href="{{ route('auditor.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Households
            <span class="nav-badge-view">View</span>
        </a>

        <a href="{{ route('auditor.audit.trail') }}" class="nav-item" onclick="closeSidebar()">
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
            <div class="role-notice-text">You have view-only access. No records can be added, edited, or deleted. Access may be time-limited by the Administrator.</div>
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
                <div class="page-breadcrumb">Home / <span>Auditor Dashboard</span></div>
                <div class="page-h1">Auditor Dashboard</div>
                <div class="page-sub">Barangay Family Track QR System — Read-Only Viewer &amp; Audit Access</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <div class="welcome-card">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO">
            <div>
                <div class="welcome-label">Welcome</div>
                <div class="welcome-heading">Good day, <em>{{ auth()->user()->name }}!</em></div>
                <div class="welcome-desc">You have read-only access to family profiles, distribution logs, household records, reports, and the system audit trail.</div>
            </div>
        </div>

        <div class="access-notice">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="access-notice-text">
                <strong>Read-Only Viewer / Auditor Role.</strong> You may view all family profiles, distribution logs, household records, and generated reports. You cannot add, edit, delete, or export any records, and you cannot generate QR codes. This account is suitable for DSWD field officers, Sangguniang Barangay members, LGU auditors, and COA inspectors. Access may be time-limited and set by the Administrator.
            </div>
        </div>

        <div class="quick-nav">
            <a href="{{ route('auditor.family-profiles') }}" class="qnav-card">
                <span class="qnav-readonly">Read Only</span>
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                </div>
                <div class="qnav-title">Family Profiles</div>
                <div class="qnav-desc">View all registered household profiles</div>
            </a>

            <a href="{{ route('auditor.distribution.logs') }}" class="qnav-card">
                <span class="qnav-readonly">Read Only</span>
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                    </svg>
                </div>
                <div class="qnav-title">Distribution Logs</div>
                <div class="qnav-desc">View all relief distribution records per event</div>
            </a>

            <a href="{{ route('auditor.households.index') }}" class="qnav-card">
                <span class="qnav-readonly">Read Only</span>
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
                </div>
                <div class="qnav-title">Households</div>
                <div class="qnav-desc">View household records and QR codes</div>
            </a>

            <a href="{{ route('auditor.audit.trail') }}" class="qnav-card">
                <span class="qnav-readonly">Read Only</span>
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
                    </svg>
                </div>
                <div class="qnav-title">Audit Trail Logs</div>
                <div class="qnav-desc">View system-wide action and login audit logs</div>
            </a>
        </div>

        {{-- ═══════════════════════════════════════════
             CONTENT AREA — System Overview
             ═══════════════════════════════════════════ --}}
        <div class="content-area">

            <div class="content-area-header">
                <div class="ca-header-left">
                    <div class="ca-dot"></div>
                    <div class="ca-title">System Overview</div>
                </div>
                <div class="ca-subtitle">Live summary — read-only view</div>
            </div>

            {{-- ── STAT CARDS ── --}}
            <div class="stat-grid">

                {{-- Households --}}
                <div class="stat-card">
                    <div class="stat-card-accent"></div>
                    <div class="stat-label">Total Households</div>
                    <div class="stat-number">{{ number_format($totalHouseholds) }}</div>
                    <div class="stat-sub">
                        <span class="stat-pill approved">
                            ✓ {{ number_format($approvedHouseholds) }} Approved
                        </span>
                        <span class="stat-pill pending">
                            ⏳ {{ number_format($pendingHouseholds) }} Pending
                        </span>
                    </div>
                </div>

                {{-- Distribution Events --}}
                <div class="stat-card">
                    <div class="stat-card-accent yellow"></div>
                    <div class="stat-label">Distribution Events</div>
                    <div class="stat-number">{{ number_format($totalEvents) }}</div>
                    <div class="stat-sub">
                        <span class="stat-pill ongoing">● {{ number_format($ongoingEvents) }} Ongoing</span>
                        <span class="stat-pill upcoming">◌ {{ number_format($upcomingEvents) }} Upcoming</span>
                        <span class="stat-pill completed">✓ {{ number_format($completedEvents) }} Done</span>
                    </div>
                </div>

                {{-- Relief Distributed --}}
                <div class="stat-card">
                    <div class="stat-card-accent green"></div>
                    <div class="stat-label">Relief Distributed</div>
                    <div class="stat-number">{{ number_format($totalDistributed) }}</div>
                    <div class="stat-sub" style="color:var(--gray-400)">
                        Total distribution log entries
                    </div>
                </div>

                {{-- Audit Logs --}}
                <div class="stat-card">
                    <div class="stat-card-accent {{ $highSeverityLogs > 0 ? 'red' : '' }}"></div>
                    <div class="stat-label">Audit Log Entries</div>
                    <div class="stat-number">{{ number_format($totalLogs) }}</div>
                    <div class="stat-sub">
                        @if($highSeverityLogs > 0)
                            <span class="stat-pill high">⚠ {{ number_format($highSeverityLogs) }} High Severity</span>
                        @endif
                        <span class="stat-pill today">{{ number_format($todayLogs) }} Today</span>
                    </div>
                </div>

            </div>
            {{-- ── END STAT CARDS ── --}}

            {{-- ── TWO-COLUMN BODY ── --}}
            <div class="content-body">

                {{-- LEFT: Recent Activity --}}
                <div class="content-panel">
                    <div class="panel-title">Recent Activity</div>

                    @if($recentLogs->isEmpty())
                        <div style="padding: 32px; text-align:center; color:var(--gray-400); font-size:12px;">
                            No activity logs found.
                        </div>
                    @else
                        <table class="activity-table">
                            <thead>
                                <tr>
                                    <th style="width:20px"></th>
                                    <th class="col-user">User</th>
                                    <th>Action</th>
                                    <th class="col-desc">Description</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td style="width:20px; padding-left:10px;">
                                        <span class="severity-dot {{ $log->severity }}"></span>
                                    </td>
                                    <td class="user-cell">{{ $log->user_name ?? '—' }}</td>
                                    <td>
                                        <span class="action-badge cat-{{ $log->category }}">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                    </td>
                                    <td class="desc-cell" title="{{ $log->description }}">
                                        {{ $log->description ?? '—' }}
                                    </td>
                                    <td class="time-ago">
                                        {{ $log->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('auditor.audit.trail') }}" class="view-all-link">
                            View all audit logs →
                        </a>
                    @endif
                </div>

                {{-- RIGHT: Quick Links + Status --}}
                <div class="content-panel">
                    <div class="panel-title">Quick Access</div>

                    <a href="{{ route('auditor.households.index') }}?filter=pending" class="quick-link-item">
                        <div class="quick-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                <path d="M9 22V12h6v10"/>
                            </svg>
                        </div>
                        Pending Households
                        <span style="margin-left:auto; background:var(--orange-pale); color:var(--orange); border:1px solid var(--orange-border); border-radius:10px; font-size:10px; font-weight:700; padding:2px 8px;">
                            {{ $pendingHouseholds }}
                        </span>
                    </a>

                    <a href="{{ route('auditor.households.index') }}?filter=approved" class="quick-link-item">
                        <div class="quick-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        Approved Households
                        <span style="margin-left:auto; background:var(--green-pale); color:var(--green); border:1px solid var(--green-border); border-radius:10px; font-size:10px; font-weight:700; padding:2px 8px;">
                            {{ $approvedHouseholds }}
                        </span>
                    </a>

                    <a href="{{ route('auditor.distribution.logs') }}" class="quick-link-item">
                        <div class="quick-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                                <line x1="9" y1="12" x2="15" y2="12"/>
                            </svg>
                        </div>
                        Distribution Logs
                        <svg class="quick-link-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>

                    <hr class="ql-divider">

                    <a href="{{ route('auditor.audit.trail') }}?severity=high" class="quick-link-item">
                        <div class="quick-link-icon" style="background:var(--red-pale);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--red)">
                                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <span style="color:var(--red)">High-Severity Logs</span>
                        <span style="margin-left:auto; background:var(--red-pale); color:var(--red); border:1px solid var(--red-border); border-radius:10px; font-size:10px; font-weight:700; padding:2px 8px;">
                            {{ $highSeverityLogs }}
                        </span>
                    </a>

                    <a href="{{ route('auditor.audit.trail') }}" class="quick-link-item">
                        <div class="quick-link-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
                            </svg>
                        </div>
                        All Audit Logs
                        <svg class="quick-link-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>

                </div>

            </div>
            {{-- ── END TWO-COLUMN BODY ── --}}

        </div>
        {{-- ═══ END CONTENT AREA ═══ --}}

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
        document.getElementById('top-time').textContent =
            pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent =
            days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        document.getElementById('main-date').textContent =
            days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    // ─── Sidebar ───
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburgerBtn = document.getElementById('hamburgerBtn');

    hamburgerBtn.addEventListener('click', function () {
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