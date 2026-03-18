<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Encoder Dashboard</title>
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
            --green:      #16A34A;
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

        /* ─── Layout ─── */
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
            background: #FFF7ED;
            border: 1px solid #D97706;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .user-avatar { width: 32px; 
        height: 32px; 
        border-radius: 50%; 
        background: #D97706; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        color: #FFFFFF; 
        font-weight: 700; 
        font-size: 13px; 
        flex-shrink: 0; }
        .user-name {
            font-size: 13px; font-weight: 600;
            color: var(--blue-dark); line-height: 1.2;
        }
        .user-role {
            font-size: 10px; color: #D97706;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none !important; /* Force hide until activated */
            position: fixed; 
            inset: 0;
            background: rgba(0,0,0,0.45); 
            z-index: 200;
            opacity: 0; 
            transition: opacity 0.25s;
            pointer-events: none; /* Don't block clicks when hidden */
        }
        .sidebar-overlay.active {
            display: block !important;
            pointer-events: auto; /* Allow clicks when active */
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
        .sidebar-close:hover { background: #FEF2F2; color: var(--red); }
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

        .sidebar-sep {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 8px 0;
        }

        /* Role notice box */
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
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong {
            display: block; font-size: 13px;
            font-weight: 600; color: var(--gray-800);
            white-space: nowrap;
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

        /* Info notice */
        .access-notice {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-left: 4px solid var(--blue);
            padding: 14px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .access-notice svg { width: 18px; height: 18px; color: var(--blue); flex-shrink: 0; margin-top: 1px; }
        .access-notice-text { font-size: 12px; color: var(--gray-600); line-height: 1.6; }
        .access-notice-text strong { color: var(--blue-dark); }

        /* Quick action cards */
        .quick-nav {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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

        /* Content area */
        .content-area {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .content-area-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; gap: 10px;
        }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); }
        .ca-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .content-placeholder-body { padding: 56px 40px; text-align: center; }
        .ph-icon-wrap {
            width: 48px; height: 48px;
            background: var(--gray-100); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .ph-icon-wrap svg { width: 22px; height: 22px; color: var(--gray-400); }
        .ph-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .ph-sub { font-size: 12px; color: var(--gray-400); }

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
            .quick-nav { grid-template-columns: repeat(2, 1fr); }
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
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }

            .welcome-card { padding: 16px 18px; gap: 14px; }
            .welcome-card img { width: 38px; height: 38px; }
            .welcome-heading { font-size: 16px; }
            .welcome-desc { display: none; }

            .access-notice { padding: 12px 14px; }
            .access-notice-text { font-size: 11px; }

            .quick-nav { grid-template-columns: 1fr 1fr; gap: 10px; }
            .qnav-card { padding: 14px; gap: 8px; }
            .qnav-title { font-size: 12px; }
            .qnav-desc { display: none; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        @media (max-width: 380px) {
            .quick-nav { grid-template-columns: 1fr; }
        }

        /* ─── STAT CARDS ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .stat-card.orange { border-top-color: #D97706; }
        .stat-card.green  { border-top-color: var(--green); }
        .stat-card.purple { border-top-color: #7C3AED; }
        .stat-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400);
        }
        .stat-value {
            font-family: 'PT Serif', serif;
            font-size: 32px; font-weight: 700;
            color: var(--blue-dark); line-height: 1;
        }
        .stat-card.orange .stat-value { color: #D97706; }
        .stat-card.green  .stat-value { color: var(--green); }
        .stat-card.purple .stat-value { color: #7C3AED; }
        .stat-sub {
            font-size: 11px; color: var(--gray-400);
            display: flex; align-items: center; gap: 5px;
        }
        .stat-sub .up   { color: var(--green); font-weight: 600; }
        .stat-sub .down { color: var(--red);   font-weight: 600; }

        /* ─── CHARTS ROW ─── */
        .charts-row {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }
        .chart-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
        }
        .chart-card-header {
            padding: 13px 18px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex; align-items: center; gap: 8px;
        }
        .chart-card-header .ca-title { font-size: 12px; font-weight: 600; color: var(--blue-dark); }
        .chart-card-header .ca-sub   { font-size: 10px; color: var(--gray-400); margin-left: auto; }
        .chart-body { padding: 16px 18px; }

        /* ─── BAR CHART ─── */
        .bar-chart { display: flex; flex-direction: column; gap: 10px; }
        .bar-row { display: grid; grid-template-columns: 110px 1fr 40px; align-items: center; gap: 10px; }
        .bar-label { font-size: 11px; color: var(--gray-600); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bar-track { background: var(--gray-100); border-radius: 3px; height: 10px; overflow: hidden; }
        .bar-fill  { height: 100%; border-radius: 3px; transition: width 0.8s ease; }
        .bar-pct   { font-size: 11px; font-weight: 700; color: var(--gray-600); text-align: right; }

        /* ─── DONUT CHART ─── */
        .donut-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding: 8px 0;
        }
        .donut-svg-wrap { position: relative; width: 150px; height: 150px; flex-shrink: 0; }
        .donut-center {
            position: absolute; inset: 0;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
        }
        .donut-center-val  { font-family: 'PT Serif', serif; font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .donut-center-lbl  { font-size: 9px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .donut-legend { width: 100%; display: flex; flex-direction: column; gap: 7px; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 11px; color: var(--gray-700); }
        .legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .legend-val  { margin-left: auto; font-weight: 700; color: var(--gray-800); }

        /* ─── BOTTOM ROW ─── */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }

        /* ─── PROGRESS LIST ─── */
        .progress-list { display: flex; flex-direction: column; gap: 12px; }
        .progress-item-label {
            display: flex; justify-content: space-between;
            font-size: 11px; margin-bottom: 4px;
        }
        .progress-item-label span { color: var(--gray-600); }
        .progress-item-label strong { color: var(--gray-800); font-weight: 700; }
        .progress-track { background: var(--gray-100); border-radius: 10px; height: 8px; overflow: hidden; }
        .progress-fill  { height: 100%; border-radius: 10px; transition: width 0.8s ease; }

        /* ─── RECENT TABLE ─── */
        .recent-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .recent-table th {
            padding: 8px 12px; background: var(--gray-50);
            border-bottom: 2px solid var(--gray-200);
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px;
            color: var(--gray-400); text-align: left;
        }
        .recent-table td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700); vertical-align: middle;
        }
        .recent-table tr:last-child td { border-bottom: none; }
        .recent-table tr:hover td { background: var(--blue-pale); }
        .status-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 10px;
            font-size: 9px; font-weight: 700; text-transform: uppercase;
        }
        .pill-pending  { background: #FFF7ED; color: #92400E; }
        .pill-approved { background: #DCFCE7; color: #15803D; }

        /* ─── RESPONSIVE CHARTS ─── */
        @media (max-width: 900px) {
            .stats-grid   { grid-template-columns: repeat(2, 1fr); }
            .charts-row   { grid-template-columns: 1fr; }
            .bottom-row   { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .stats-grid   { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-value   { font-size: 24px; }
            .bar-row      { grid-template-columns: 80px 1fr 36px; }
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
                <div class="user-role">Data Entry Access</div>
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

        <a href="#" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
            <span class="nav-badge">Live</span>
        </a>
        <a href="{{ route('encoder.households.index') }}" class="nav-item" onclick="closeSidebar()">
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

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">Home / <span>Encoder Dashboard</span></div>
                <div class="page-h1">Encoder Dashboard</div>
                <div class="page-sub">Barangay Family Track QR System — Data Entry &amp; Family Profiling</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <div class="welcome-card">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO">
            <div>
                <div class="welcome-label">Welcome Back</div>
                <div class="welcome-heading">Good day, <em>Encoder!</em></div>
                <div class="welcome-desc">You are logged in as a Data Encoder. Register family profiles, manage household members, and cross-reference with DSWD Listahanan.</div>
            </div>
        </div>

        <div class="access-notice">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="access-notice-text">
                <strong>Encoder Role — Data Entry Access.</strong> You may create and update family profiles following the RBI form, add or edit household members, and cross-reference entries with DSWD Listahanan. Submitted records will be reviewed and approved by the Administrator before QR codes are generated. You do not have access to distribution logs or QR code management.
            </div>
        </div>

        @php
            // ── Swap these with real DB queries in your controller ──
            $totalHouseholds  = App\Models\Household::where('encoded_by', auth()->id())->count();
            $pending          = App\Models\Household::where('encoded_by', auth()->id())->whereNull('approved_by')->count();
            $approved         = App\Models\Household::where('encoded_by', auth()->id())->whereNotNull('approved_by')->count();
            $totalMembers     = App\Models\FamilyMember::whereHas('household', fn($q) => $q->where('encoded_by', auth()->id()))->count();

            $approvalRate     = $totalHouseholds > 0 ? round(($approved / $totalHouseholds) * 100) : 0;
            $pendingRate      = $totalHouseholds > 0 ? round(($pending  / $totalHouseholds) * 100) : 0;

            // Vulnerability breakdown
            $is4ps        = App\Models\Household::where('encoded_by', auth()->id())->where('is_4ps_beneficiary', 1)->count();
            $isPwd        = App\Models\Household::where('encoded_by', auth()->id())->where('is_pwd',             1)->count();
            $isSenior     = App\Models\Household::where('encoded_by', auth()->id())->where('is_senior',         1)->count();
            $isSoloParent = App\Models\Household::where('encoded_by', auth()->id())->where('is_solo_parent',    1)->count();

            // Housing type breakdown (top 5)
            $housingTypes = App\Models\Household::where('encoded_by', auth()->id())
                ->whereNotNull('housing_type')
                ->selectRaw('housing_type, count(*) as total')
                ->groupBy('housing_type')
                ->orderByDesc('total')
                ->limit(5)
                ->pluck('total', 'housing_type')
                ->toArray();

            // Barangay breakdown (top 6)
            $barangayCounts = App\Models\Household::where('encoded_by', auth()->id())
                ->selectRaw('barangay, count(*) as total')
                ->groupBy('barangay')
                ->orderByDesc('total')
                ->limit(6)
                ->pluck('total', 'barangay')
                ->toArray();

            $maxBrgy = max(array_values($barangayCounts) ?: [1]);

            // Recent 5 households
            $recentHouseholds = App\Models\Household::where('encoded_by', auth()->id())
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        @endphp

        {{-- ── STAT CARDS ── --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Households</div>
                <div class="stat-value">{{ $totalHouseholds }}</div>
                <div class="stat-sub">All submitted records</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Pending Approval</div>
                <div class="stat-value">{{ $pending }}</div>
                <div class="stat-sub">
                    <span class="down">{{ $pendingRate }}%</span> of total
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Approved</div>
                <div class="stat-value">{{ $approved }}</div>
                <div class="stat-sub">
                    <span class="up">{{ $approvalRate }}%</span> approval rate
                </div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Total Members</div>
                <div class="stat-value">{{ $totalMembers }}</div>
                <div class="stat-sub">Across all households</div>
            </div>
        </div>

        {{-- ── CHARTS ROW ── --}}
        <div class="charts-row">

            {{-- Bar Chart: Households per Barangay --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Households per Barangay</div>
                    <span class="ca-sub">Top 6</span>
                </div>
                <div class="chart-body">
                    @if(count($barangayCounts) > 0)
                    <div class="bar-chart">
                        @foreach($barangayCounts as $brgy => $count)
                        @php $pct = $maxBrgy > 0 ? round(($count / $maxBrgy) * 100) : 0; @endphp
                        <div class="bar-row">
                            <div class="bar-label" title="{{ $brgy }}">{{ $brgy }}</div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ $pct }}%;background:var(--blue);"></div>
                            </div>
                            <div class="bar-pct">{{ $count }}</div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center;padding:32px;color:var(--gray-400);font-size:12px;">No data yet</div>
                    @endif
                </div>
            </div>

            {{-- Donut Chart: Approval Status --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Approval Status</div>
                </div>
                <div class="chart-body">
                    <div class="donut-wrap">
                        <div class="donut-svg-wrap">
                            <svg viewBox="0 0 36 36" width="150" height="150">
                                @php
                                    $r = 15.9;
                                    $circ = 2 * 3.14159 * $r;
                                    $approvedDash = $totalHouseholds > 0 ? ($approved / $totalHouseholds) * $circ : 0;
                                    $pendingDash  = $totalHouseholds > 0 ? ($pending  / $totalHouseholds) * $circ : 0;
                                    $gap = 0.5;
                                @endphp
                                <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#F0F2F5" stroke-width="3.5"/>
                                @if($totalHouseholds > 0)
                                <circle cx="18" cy="18" r="{{ $r }}" fill="none"
                                    stroke="#16A34A" stroke-width="3.5"
                                    stroke-dasharray="{{ round($approvedDash - $gap, 2) }} {{ round($circ - $approvedDash + $gap, 2) }}"
                                    stroke-dashoffset="{{ round($circ / 4, 2) }}"
                                    stroke-linecap="round"/>
                                <circle cx="18" cy="18" r="{{ $r }}" fill="none"
                                    stroke="#D97706" stroke-width="3.5"
                                    stroke-dasharray="{{ round($pendingDash - $gap, 2) }} {{ round($circ - $pendingDash + $gap, 2) }}"
                                    stroke-dashoffset="{{ round($circ / 4 - $approvedDash, 2) }}"
                                    stroke-linecap="round"/>
                                @endif
                            </svg>
                            <div class="donut-center">
                                <div class="donut-center-val">{{ $approvalRate }}%</div>
                                <div class="donut-center-lbl">Approved</div>
                            </div>
                        </div>
                        <div class="donut-legend">
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#16A34A;"></div>
                                Approved
                                <span class="legend-val">{{ $approved }}</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#D97706;"></div>
                                Pending
                                <span class="legend-val">{{ $pending }}</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#1B3F7A;"></div>
                                Total
                                <span class="legend-val">{{ $totalHouseholds }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── BOTTOM ROW ── --}}
        <div class="bottom-row">

            {{-- Vulnerability Progress Bars --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Vulnerability Classification</div>
                    <span class="ca-sub">% of total households</span>
                </div>
                <div class="chart-body">
                    <div class="progress-list">
                        @php
                            $vulns = [
                                ['label'=>'4Ps Beneficiary',    'count'=>$is4ps,        'color'=>'#1B3F7A'],
                                ['label'=>'Person w/ Disability','count'=>$isPwd,        'color'=>'#7C3AED'],
                                ['label'=>'Senior Citizen',      'count'=>$isSenior,     'color'=>'#D97706'],
                                ['label'=>'Solo Parent',         'count'=>$isSoloParent, 'color'=>'#DB2777'],
                            ];
                        @endphp
                        @foreach($vulns as $v)
                        @php $vpct = $totalHouseholds > 0 ? round(($v['count'] / $totalHouseholds) * 100) : 0; @endphp
                        <div>
                            <div class="progress-item-label">
                                <span>{{ $v['label'] }}</span>
                                <strong>{{ $v['count'] }} <span style="font-weight:400;color:var(--gray-400);">({{ $vpct }}%)</span></strong>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width:{{ $vpct }}%;background:{{ $v['color'] }};"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Housing Type mini bars --}}
                    @if(count($housingTypes) > 0)
                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--gray-100);">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gray-400);margin-bottom:10px;">Housing Type Breakdown</div>
                        <div class="bar-chart">
                            @php $maxHt = max(array_values($housingTypes)); @endphp
                            @foreach($housingTypes as $ht => $htCount)
                            @php $htPct = round(($htCount / $maxHt) * 100); @endphp
                            <div class="bar-row" style="grid-template-columns:100px 1fr 36px;">
                                <div class="bar-label">{{ ucfirst(str_replace('_',' ',$ht)) }}</div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="width:{{ $htPct }}%;background:#7C3AED;"></div>
                                </div>
                                <div class="bar-pct">{{ $htCount }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Recent Households Table --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Recently Registered</div>
                    <a href="{{ route('encoder.households.index') }}" style="margin-left:auto;font-size:11px;color:var(--blue);text-decoration:none;font-weight:600;">View All →</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Household Head</th>
                                <th>Barangay</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentHouseholds as $hh)
                            <tr>
                                <td>
                                    <strong style="display:block;color:var(--blue-dark);font-size:12px;">{{ Str::limit($hh->household_head_name, 22) }}</strong>
                                    <span style="font-size:10px;color:var(--gray-400);">{{ $hh->created_at->diffForHumans() }}</span>
                                </td>
                                <td style="font-size:11px;">{{ Str::limit($hh->barangay, 18) }}</td>
                                <td>
                                    @if($hh->isApproved())
                                        <span class="status-pill pill-approved">✓ Approved</span>
                                    @else
                                        <span class="status-pill pill-pending">⏳ Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center;padding:24px;color:var(--gray-400);font-size:12px;">No households registered yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
</script>
</body>
</html>