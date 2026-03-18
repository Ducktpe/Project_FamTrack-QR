<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic, Cavite — Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <style>
        :root {
            --blue:       #1B3F7A;
            --blue-dark:  #122D5A;
            --blue-light: #2459A8;
            --blue-pale:  #EAF0FA;
            --yellow:     #F5C518;
            --yellow-dark:#D4A800;
            --yellow-pale:#FFFAE6;
            --green:      #16A34A;
            --green-pale: #DCFCE7;
            --green-dark: #15803D;
            --red:        #C0392B;
            --red-pale:   #FEF2F2;
            --white:      #FFFFFF;
            --gray-50:    #F7F8FA;
            --gray-100:   #F0F2F5;
            --gray-200:   #DEE2E8;
            --gray-400:   #9AA3B0;
            --gray-600:   #5A6372;
            --gray-800:   #2C3340;
            --sidebar-w:  256px;
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

        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        header { grid-area: header; background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; align-items: center; padding: 0 28px; gap: 14px; z-index: 90; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; margin-left: -4px; border-radius: 4px; color: var(--blue-dark); flex-shrink: 0; transition: background 0.15s; }
        .hamburger:hover { background: var(--blue-pale); }
        .hamburger svg { width: 22px; height: 22px; display: block; }
        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-admin-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid var(--gray-200); border-radius: 4px; }
        .admin-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .admin-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .admin-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
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
        .logout-btn:hover { background: #C0392B; }

        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        .welcome-card { background: var(--blue); border-left: 5px solid var(--yellow); padding: 22px 28px; display: flex; align-items: center; gap: 20px; margin-bottom: 24px; }
        .welcome-card img { width: 50px; height: 50px; object-fit: contain; flex-shrink: 0; }
        .welcome-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.55); margin-bottom: 4px; }
        .welcome-heading { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--white); }
        .welcome-heading em { color: var(--yellow); font-style: normal; }
        .welcome-desc { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; }

        .quick-nav { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
        .qnav-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 18px 20px; cursor: pointer; text-decoration: none; display: flex; flex-direction: column; gap: 10px; transition: box-shadow 0.15s, border-top-color 0.15s; }
        .qnav-card:hover { box-shadow: 0 3px 12px rgba(27,63,122,0.12); border-top-color: var(--yellow); }
        .qnav-icon { width: 32px; height: 32px; background: var(--blue-pale); border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .qnav-icon svg { width: 17px; height: 17px; color: var(--blue); }
        .qnav-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .qnav-desc { font-size: 11px; color: var(--gray-600); }

        .dash-stats-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 16px; }
        .dash-stat-card { background: var(--white); border: 1px solid var(--gray-200); padding: 16px 18px; display: flex; align-items: center; gap: 14px; }
        .ds-icon { width: 42px; height: 42px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .ds-icon svg { width: 20px; height: 20px; }
        .ds-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); margin-bottom: 4px; }
        .ds-value { font-size: 24px; font-weight: 700; color: var(--blue-dark); line-height: 1; font-variant-numeric: tabular-nums; }

        .charts-row { display: grid; grid-template-columns: 1fr 340px; gap: 14px; margin-bottom: 16px; }
        .chart-card { background: var(--white); border: 1px solid var(--gray-200); }
        .chart-card-header { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .ca-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .ca-view-all { margin-left: auto; font-size: 11px; font-weight: 600; color: var(--blue-light); text-decoration: none; letter-spacing: 0.3px; }
        .ca-view-all:hover { color: var(--blue); }
        .chart-body { padding: 20px; height: 240px; position: relative; }
        .chart-body-doughnut { display: flex; flex-direction: column; align-items: center; gap: 16px; height: auto; padding: 20px; }
        .chart-canvas-wrap { position: relative; width: 180px; height: 180px; flex-shrink: 0; }
        .chart-canvas-wrap canvas { position: absolute; inset: 0; width: 100% !important; height: 100% !important; }
        .doughnut-legend { width: 100%; display: flex; flex-direction: column; gap: 7px; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 11px; color: var(--gray-600); }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .legend-val { margin-left: auto; font-weight: 700; color: var(--gray-800); font-variant-numeric: tabular-nums; }

        .content-area { background: var(--white); border: 1px solid var(--gray-200); }
        .content-area-header { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .dash-table { width: 100%; border-collapse: collapse; }
        .dash-table thead th { padding: 10px 16px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); background: var(--gray-50); border-bottom: 1px solid var(--gray-200); text-align: left; white-space: nowrap; }
        .dash-table tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        .dash-table tbody tr:last-child { border-bottom: none; }
        .dash-table tbody tr:hover { background: var(--blue-pale); }
        .dash-table tbody td { padding: 10px 16px; font-size: 12.5px; color: var(--gray-800); }
        .dt-name { font-weight: 600; color: var(--blue-dark); }

        .status-badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-ongoing   { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .status-done      { background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F5; }
        .status-upcoming  { background: var(--yellow-pale); color: var(--yellow-dark); border: 1px solid #FDE68A; }
        .status-cancelled { background: var(--red-pale); color: var(--red); border: 1px solid #FECACA; }

        /* ═══════════════════════════════════════════
           DISTRIBUTION MAP SECTION
        ═══════════════════════════════════════════ */
        .dist-map-section { background: var(--white); border: 1px solid var(--gray-200); margin-top: 20px; }
        .dist-map-header { padding: 14px 20px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .dist-map-title-wrap { display: flex; align-items: center; gap: 10px; }
        .dist-map-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .dist-map-sub   { font-size: 11px; color: var(--gray-400); margin-top: 1px; }
        .dist-map-legend { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .dm-legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: var(--gray-600); }
        .dm-legend-pin { width: 12px; height: 12px; border-radius: 50%; border: 2px solid rgba(0,0,0,0.12); flex-shrink: 0; }
        .dm-legend-pin.upcoming  { background: #2459A8; }
        .dm-legend-pin.ongoing   { background: #16A34A; }
        .dm-legend-pin.completed { background: #6B7280; }
        .dm-legend-pin.cancelled { background: #C0392B; }
        .dist-map-filters { display: flex; align-items: center; gap: 6px; padding: 9px 20px; background: var(--white); border-bottom: 1px solid var(--gray-100); flex-wrap: wrap; }
        .dm-filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); margin-right: 4px; }
        .dm-filter-btn { padding: 4px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; border: 1.5px solid var(--gray-200); background: var(--white); cursor: pointer; font-family: 'Open Sans', sans-serif; transition: all 0.15s; display: inline-flex; align-items: center; gap: 5px; }
        .dm-filter-btn .dm-fc { font-size: 10px; background: var(--gray-100); color: var(--gray-600); padding: 1px 6px; border-radius: 10px; font-weight: 700; }
        .dm-filter-btn:hover { background: var(--gray-50); }
        .dm-filter-btn.active.f-all      { border-color: var(--blue);    background: var(--blue-pale);  color: var(--blue-dark); }
        .dm-filter-btn.active.f-upcoming { border-color: #2459A8;         background: #EAF0FA;           color: #1B3F7A; }
        .dm-filter-btn.active.f-ongoing  { border-color: var(--green);    background: var(--green-pale); color: var(--green-dark); }
        .dm-filter-btn.active.f-completed{ border-color: var(--gray-400); background: var(--gray-100);   color: var(--gray-800); }
        .dm-filter-btn.active.f-cancelled{ border-color: var(--red);      background: var(--red-pale);   color: var(--red); }
        .dm-filter-btn.active.f-recent   { border-color: #7C3AED;         background: #F5F3FF;           color: #5B21B6; }
        .dm-filter-btn.active .dm-fc { background: rgba(0,0,0,0.1); color: inherit; }
        .dist-map-body { position: relative; }
        #dashDistMap { height: 400px; width: 100%; }
        .dist-map-no-pin { display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.93); border: 1px solid var(--gray-200); border-radius: 8px; padding: 18px 28px; text-align: center; font-size: 12px; color: var(--gray-400); pointer-events: none; z-index: 500; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .dist-map-no-pin.show { display: block; }
        .dist-map-footer { display: flex; border-top: 1px solid var(--gray-100); background: var(--gray-50); flex-wrap: wrap; }
        .dm-stat { flex: 1; min-width: 80px; padding: 10px 16px; border-right: 1px solid var(--gray-100); text-align: center; }
        .dm-stat:last-child { border-right: none; }
        .dm-stat-val { display: block; font-size: 20px; font-weight: 700; font-family: 'PT Serif', serif; line-height: 1.1; }
        .dm-stat-val.c-all      { color: var(--blue-dark); }
        .dm-stat-val.c-upcoming { color: var(--blue); }
        .dm-stat-val.c-ongoing  { color: var(--green); }
        .dm-stat-val.c-completed{ color: var(--gray-600); }
        .dm-stat-val.c-cancelled{ color: var(--red); }
        .dm-popup .leaflet-popup-content-wrapper { border-radius: 6px; padding: 0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.18); border: none; }
        .dm-popup .leaflet-popup-content { margin: 0; min-width: 210px; }
        .dm-popup-inner { font-family: 'Open Sans', sans-serif; }
        .dm-popup-head { padding: 10px 14px 8px; border-bottom: 1px solid #f0f0f0; }
        .dm-sbadge { display: inline-flex; align-items: center; gap: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; padding: 2px 8px; border-radius: 10px; margin-bottom: 5px; }
        .dm-sbadge.upcoming  { background: #EAF0FA; color: #1B3F7A; }
        .dm-sbadge.ongoing   { background: #DCFCE7; color: #15803D; }
        .dm-sbadge.completed { background: #F3F4F6; color: #4B5563; }
        .dm-sbadge.cancelled { background: #FEF2F2; color: #C0392B; }
        .dm-popup-name { font-size: 13px; font-weight: 700; color: #1B3F7A; display: block; line-height: 1.3; }
        .dm-popup-body { padding: 8px 14px 10px; }
        .dm-popup-row { display: flex; align-items: flex-start; gap: 6px; font-size: 11px; color: #5A6372; margin-bottom: 4px; line-height: 1.4; }
        .dm-popup-row:last-child { margin-bottom: 0; }
        .dm-popup-row svg { width: 11px; height: 11px; flex-shrink: 0; margin-top: 1px; color: #9AA3B0; }
        .dm-popup-link { margin-top: 8px; padding-top: 8px; border-top: 1px solid #f0f0f0; font-size: 11px; font-weight: 700; color: #2459A8; text-decoration: none; display: block; }
        .dm-popup-link:hover { color: #1B3F7A; }

        #dashDistMap {
            height: 400px;
            width: 100%;
            background: #1a1a2e;
        }

        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.45); }
        .footer-left strong { color: rgba(255,255,255,0.75); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.25); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        /* ── Extra dashboard sections ── */
        .mid-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 16px; }
        .bar-chart-list { display: flex; flex-direction: column; gap: 9px; }
        .bcl-row { display: grid; grid-template-columns: 120px 1fr 38px; align-items: center; gap: 8px; }
        .bcl-label { font-size: 11px; color: var(--gray-600); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .bcl-track { background: var(--gray-100); border-radius: 3px; height: 9px; overflow: hidden; }
        .bcl-fill  { height: 100%; border-radius: 3px; }
        .bcl-val   { font-size: 11px; font-weight: 700; color: var(--gray-600); text-align: right; }
        .prog-list { display: flex; flex-direction: column; gap: 10px; }
        .prog-row  { }
        .prog-label-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 3px; }
        .prog-label-row span   { color: var(--gray-600); }
        .prog-label-row strong { color: var(--gray-800); }
        .prog-track { background: var(--gray-100); border-radius: 10px; height: 8px; overflow: hidden; }
        .prog-fill  { height: 100%; border-radius: 10px; }
        .approval-ring-wrap { display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 4px 0; }
        .ring-svg-wrap { position: relative; width: 130px; height: 130px; flex-shrink: 0; }
        .ring-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .ring-center-val { font-family: 'PT Serif', serif; font-size: 24px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .ring-center-lbl { font-size: 9px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .ring-legend { width: 100%; display: flex; flex-direction: column; gap: 6px; }
        .ring-legend-item { display: flex; align-items: center; gap: 7px; font-size: 11px; color: var(--gray-600); }
        .ring-legend-dot  { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
        .ring-legend-val  { margin-left: auto; font-weight: 700; color: var(--gray-800); }
        .pending-list { }
        .pending-item { display: flex; align-items: center; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid var(--gray-100); font-size: 12px; gap: 10px; }
        .pending-item:last-child { border-bottom: none; }
        .pending-name { font-weight: 600; color: var(--blue-dark); }
        .pending-sub  { font-size: 10px; color: var(--gray-400); margin-top: 1px; }
        .pending-approve-btn { flex-shrink: 0; padding: 4px 12px; background: var(--blue); color: #fff; border: none; border-radius: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; cursor: pointer; text-decoration: none; font-family: 'Open Sans', sans-serif; transition: background .15s; }
        .pending-approve-btn:hover { background: var(--blue-dark); }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        /* ════════════════════════════════════════
           RESPONSIVE
           ════════════════════════════════════════ */

        /* ── Tablet (≤ 900px) ── */
        @media (max-width: 900px) {
            /* Shell: sidebar becomes off-canvas drawer */
            .shell {
                grid-template-rows: 36px auto 1fr 48px;
                grid-template-columns: 1fr;
                grid-template-areas: "topbar" "header" "main" "footer";
                height: 100vh; overflow: hidden;
            }
            .sidebar {
                grid-area: unset;
                position: fixed; top: 0; left: 0; bottom: 0;
                width: var(--sidebar-w); z-index: 300;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
                box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }
            .hamburger { display: flex; }

            /* Header */
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

            /* Quick nav: 2 columns */
            .quick-nav { grid-template-columns: repeat(2, 1fr); }

            /* Stat cards: 3 columns */
            .dash-stats-row { grid-template-columns: repeat(3, 1fr); }

            /* Mid-row: 2 columns */
            .mid-row { grid-template-columns: 1fr 1fr; }
            .bcl-row { grid-template-columns: 90px 1fr 32px; }

            /* Charts row: stack */
            .charts-row { grid-template-columns: 1fr; }

            /* Population charts: stack */
            .pop-charts-row { grid-template-columns: 1fr !important; }

            /* Map: shorter */
            #dashDistMap { height: 320px; }

            /* Distribution table: scrollable */
            .dist-table-wrap { overflow-x: auto; }
        }

        /* ── Large mobile (≤ 640px) ── */
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
            .page-titlebar { flex-direction: column; align-items: flex-start; gap: 6px; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }

            .welcome-card { padding: 16px 18px; gap: 14px; }
            .welcome-card img { width: 38px; height: 38px; }
            .welcome-heading { font-size: 16px; }
            .welcome-desc { display: none; }

            /* Quick nav: 2 columns compact */
            .quick-nav { grid-template-columns: 1fr 1fr; gap: 10px; }
            .qnav-card { padding: 14px; gap: 8px; }
            .qnav-title { font-size: 12px; }
            .qnav-desc { display: none; }

            /* Stat cards: 2 columns */
            .dash-stats-row { grid-template-columns: repeat(2, 1fr); }
            .dash-stat-card { padding: 12px 14px; gap: 10px; }
            .ds-icon { width: 36px; height: 36px; }
            .ds-icon svg { width: 16px; height: 16px; }
            .ds-value { font-size: 20px; }
            .ds-label { font-size: 9px; }

            /* Mid-row: stack */
            .mid-row { grid-template-columns: 1fr; }
            .bcl-row { grid-template-columns: 80px 1fr 30px; }

            /* Chart bodies: shorter on mobile */
            .chart-body { height: 200px; padding: 14px; }
            .chart-canvas-wrap { width: 150px; height: 150px; }
            .chart-body-doughnut canvas { max-width: 150px; height: 150px !important; }

            /* Map */
            #dashDistMap { height: 260px; }
            .dist-map-legend { display: none; }
            .dist-map-filters { gap: 4px; }
            .dm-filter-btn { padding: 3px 8px; font-size: 10px; }
            .dm-filter-btn .dm-fc { display: none; }
            .dm-stat-val { font-size: 16px; }

            /* Distribution table: hide less important columns */
            .dash-table .col-barangay,
            .dash-table .col-distributed { display: none; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        /* ── Small mobile (≤ 420px) ── */
        @media (max-width: 420px) {
            .main-content { padding: 12px 10px; }
            .quick-nav { grid-template-columns: 1fr 1fr; gap: 8px; }
            .qnav-card { padding: 12px 10px; gap: 6px; }

            /* Stat cards: 1 column */
            .dash-stats-row { grid-template-columns: 1fr 1fr; }
            .dash-stat-card { padding: 10px 12px; }

            /* Also hide Status column from table on tiny screens */
            .dash-table .col-status { display: none; }

            #dashDistMap { height: 220px; }
            .dist-map-footer { flex-wrap: wrap; }
            .dm-stat { min-width: 50%; border-bottom: 1px solid var(--gray-100); }
        }

        /* ── Very small (≤ 360px) ── */
        @media (max-width: 360px) {
            .quick-nav { grid-template-columns: 1fr; }
            .dash-stats-row { grid-template-columns: 1fr; }
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
            <img src="{{ asset('/images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
            <div class="logo-divider"></div>
            <img src="{{ asset('/images/naic-seal.png') }}" alt="Bayan ng Naic Seal">
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
                <div class="admin-role">Full Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="nav-section-label">Admin Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard Overview
        </a>
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Distribution Events
        </a>
        <a href="{{ route('admin.distribution.logs') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
        </a>
        <a href="{{ route('admin.residents.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            List of Residents
        </a>
        <a href="{{ route('admin.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
            </svg>
            List of Households
        </a>
        <a href="{{ route('admin.traillog.trail') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Trail Logs
        </a>
        <hr class="sidebar-sep">

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
                <div class="page-breadcrumb">Home / <span>Dashboard</span></div>
                <div class="page-h1">Dashboard Overview</div>
                <div class="page-sub">Barangay Family Track QR Relief Distribution System — MDRRMO Naic, Cavite</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <div class="welcome-card">
            <img src="{{ asset('/images/mdrrmo-logo.png') }}" alt="MDRRMO">
            <div>
                <div class="welcome-label">Welcome Back</div>
                <div class="welcome-heading">Good day, <em>{{ auth()->user()->name }}!</em></div>
                <div class="welcome-desc">Office of the Municipal Disaster Risk Reduction and Management Officer — Naic, Cavite</div>
            </div>
        </div>

        <div class="quick-nav">
            <a href="{{ route('admin.events.quick-create') }}" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="qnav-title">Distribution Events</div>
                <div class="qnav-desc">Create &amp; manage relief events</div>
            </a>
            <a href="{{ route('admin.distribution.logs') }}" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <div class="qnav-title">Distribution Logs</div>
                <div class="qnav-desc">Track all relief distributions</div>
            </a>
            <a href="{{ route('admin.residents.index') }}" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                    </svg>
                </div>
                <div class="qnav-title">List of Residents</div>
                <div class="qnav-desc">Manage resident profiles</div>
            </a>
            <a href="{{ route('admin.households.index') }}" class="qnav-card">
                <div class="qnav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
                    </svg>
                </div>
                <div class="qnav-title">List of Households</div>
                <div class="qnav-desc">View &amp; manage households</div>
            </a>
        </div>

        {{-- ── SUMMARY STAT CARDS ── --}}
        <div class="dash-stats-row">
            <div class="dash-stat-card">
                <div class="ds-icon" style="background:#EAF0FA;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#1B3F7A" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
                </div>
                <div>
                    <div class="ds-label">Total Residents</div>
                    <div class="ds-value">{{ number_format($totalResidents) }}</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="ds-icon" style="background:#EAF0FA;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#1B3F7A" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                </div>
                <div>
                    <div class="ds-label">Households</div>
                    <div class="ds-value">{{ number_format($totalHouseholds) }}</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="ds-icon" style="background:#DCFCE7;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><polyline points="9 12 11 14 15 10"/></svg>
                </div>
                <div>
                    <div class="ds-label">4Ps Beneficiaries</div>
                    <div class="ds-value" style="color:#16A34A;">{{ number_format($total4Ps) }}</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="ds-icon" style="background:#FFF7ED;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#C2410C" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <div>
                    <div class="ds-label">Senior Citizens</div>
                    <div class="ds-value" style="color:#C2410C;">{{ number_format($totalSeniors) }}</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="ds-icon" style="background:#FEF2F2;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#C0392B" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div class="ds-label">PWD</div>
                    <div class="ds-value" style="color:#C0392B;">{{ number_format($totalPwd) }}</div>
                </div>
            </div>
        </div>

        @php
            $totalHouseholdsAll = \App\Models\Household::count();
            $pendingCount       = \App\Models\Household::whereNull('approved_by')->count();
            $approvedCount      = \App\Models\Household::whereNotNull('approved_by')->count();
            $approvalRate       = $totalHouseholdsAll > 0 ? round(($approvedCount / $totalHouseholdsAll) * 100) : 0;
            $pendingRate        = $totalHouseholdsAll > 0 ? round(($pendingCount  / $totalHouseholdsAll) * 100) : 0;

            // ── Population summary ───────────────────────────────────────────
            $totalFamilyHeads   = \App\Models\FamilyMember::where('is_family_head', 1)->count();
            $totalAllResidents  = \App\Models\FamilyMember::count();
            $totalMale          = \App\Models\FamilyMember::where('sex', 'Male')->count();
            $totalFemale        = \App\Models\FamilyMember::where('sex', 'Female')->count();

            // Age group breakdown
            $ageGroups = [
                'Children (0–12)'   => \App\Models\FamilyMember::whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 0 AND 12')->count(),
                'Teen (13–17)'      => \App\Models\FamilyMember::whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 13 AND 17')->count(),
                'Adult (18–59)'     => \App\Models\FamilyMember::whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 18 AND 59')->count(),
                'Senior (60+)'      => \App\Models\FamilyMember::whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) >= 60')->count(),
            ];

            // Vulnerability breakdown
            $vulnData = [
                ['label' => '4Ps Beneficiary',     'count' => \App\Models\Household::where('is_4ps_beneficiary',1)->count(), 'color' => '#1B3F7A'],
                ['label' => 'Person w/ Disability', 'count' => \App\Models\Household::where('is_pwd',1)->count(),             'color' => '#7C3AED'],
                ['label' => 'Senior Citizen',       'count' => \App\Models\Household::where('is_senior',1)->count(),          'color' => '#D97706'],
                ['label' => 'Solo Parent',          'count' => \App\Models\Household::where('is_solo_parent',1)->count(),     'color' => '#DB2777'],
            ];

            // Housing type breakdown
            $housingBreakdown = \App\Models\Household::whereNotNull('housing_type')
                ->selectRaw('housing_type, count(*) as total')
                ->groupBy('housing_type')->orderByDesc('total')->limit(6)
                ->pluck('total','housing_type')->toArray();
            $maxHt = max(array_values($housingBreakdown) ?: [1]);

            // Water source breakdown
            $waterBreakdown = \App\Models\Household::whereNotNull('water_source')
                ->selectRaw('water_source, count(*) as total')
                ->groupBy('water_source')->orderByDesc('total')->limit(4)
                ->pluck('total','water_source')->toArray();
            $maxWater = max(array_values($waterBreakdown) ?: [1]);

            // Pending households (latest 5)
            $pendingHouseholds = \App\Models\Household::whereNull('approved_by')
                ->with('encoder')->latest()->limit(5)->get();

            // Employment breakdown (from family_member_details)
            $empBreakdown = \App\Models\FamilyMemberDetail::whereNotNull('employment_status')
                ->selectRaw('employment_status, count(*) as total')
                ->groupBy('employment_status')->orderByDesc('total')->limit(5)
                ->pluck('total','employment_status')->toArray();
            $maxEmp = max(array_values($empBreakdown) ?: [1]);

            // Monthly registrations (last 6 months)
            $monthlyRegs = collect();
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $monthlyRegs->push([
                    'month' => $m->format('M Y'),
                    'count' => \App\Models\Household::whereYear('created_at', $m->year)
                                    ->whereMonth('created_at', $m->month)->count(),
                ]);
            }
        @endphp

        {{-- ── POPULATION SUMMARY CARDS ── --}}
        <div class="dash-stats-row" style="grid-template-columns:repeat(3,1fr);margin-bottom:16px;">
            {{-- Total Households --}}
            <div class="dash-stat-card" style="border-left:4px solid #1B3F7A;">
                <div class="ds-icon" style="background:#EAF0FA;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#1B3F7A" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                </div>
                <div style="flex:1;">
                    <div class="ds-label">Total Households</div>
                    <div class="ds-value">{{ number_format($totalHouseholdsAll) }}</div>
                    <div style="font-size:10px;color:var(--gray-400);margin-top:3px;">
                        <span style="color:#16A34A;font-weight:600;">{{ number_format($approvedCount) }} approved</span>
                        &nbsp;·&nbsp;
                        <span style="color:#D97706;font-weight:600;">{{ number_format($pendingCount) }} pending</span>
                    </div>
                </div>
            </div>
            {{-- Total Family Heads --}}
            <div class="dash-stat-card" style="border-left:4px solid #7C3AED;">
                <div class="ds-icon" style="background:#F5F3FF;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a7 7 0 0113 0"/><path d="M12 11v4"/><path d="M10 13h4"/></svg>
                </div>
                <div style="flex:1;">
                    <div class="ds-label">Family Heads</div>
                    <div class="ds-value" style="color:#7C3AED;">{{ number_format($totalFamilyHeads) }}</div>
                    <div style="font-size:10px;color:var(--gray-400);margin-top:3px;">One per nuclear family unit</div>
                </div>
            </div>
            {{-- Total Residents with Gender --}}
            <div class="dash-stat-card" style="border-left:4px solid #0891B2;">
                <div class="ds-icon" style="background:#ECFEFF;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#0891B2" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/><path d="M16 3.13a4 4 0 010 7.75"/><path d="M21 21v-2a4 4 0 00-3-3.87"/></svg>
                </div>
                <div style="flex:1;">
                    <div class="ds-label">Total Residents</div>
                    <div class="ds-value" style="color:#0891B2;">{{ number_format($totalAllResidents) }}</div>
                    <div style="font-size:10px;color:var(--gray-400);margin-top:3px;">
                        <span style="color:#2459A8;font-weight:600;">♂ {{ number_format($totalMale) }} M</span>
                        &nbsp;·&nbsp;
                        <span style="color:#DB2777;font-weight:600;">♀ {{ number_format($totalFemale) }} F</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── MID ROW: Approval Ring + Vulnerability + Pending Approvals ── --}}
        <div class="mid-row" style="margin-bottom:16px;">

            {{-- Approval Status Ring --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Approval Status</div>
                    <span style="margin-left:auto;font-size:10px;color:var(--gray-400);">All Households</span>
                </div>
                <div class="chart-body chart-body-doughnut" style="height:auto;">
                    <div class="approval-ring-wrap">
                        <div class="ring-svg-wrap">
                            @php
                                $r = 15.9; $c = 2 * 3.14159 * $r;
                                $aDash = $totalHouseholdsAll > 0 ? ($approvedCount / $totalHouseholdsAll) * $c : 0;
                                $pDash = $totalHouseholdsAll > 0 ? ($pendingCount  / $totalHouseholdsAll) * $c : 0;
                            @endphp
                            <svg viewBox="0 0 36 36" width="130" height="130">
                                <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#F0F2F5" stroke-width="3.5"/>
                                @if($totalHouseholdsAll > 0)
                                <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#16A34A" stroke-width="3.5"
                                    stroke-dasharray="{{ round($aDash-.4,2) }} {{ round($c-$aDash+.4,2) }}"
                                    stroke-dashoffset="{{ round($c/4,2) }}" stroke-linecap="round"/>
                                <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#D97706" stroke-width="3.5"
                                    stroke-dasharray="{{ round($pDash-.4,2) }} {{ round($c-$pDash+.4,2) }}"
                                    stroke-dashoffset="{{ round($c/4-$aDash,2) }}" stroke-linecap="round"/>
                                @endif
                            </svg>
                            <div class="ring-center">
                                <div class="ring-center-val">{{ $approvalRate }}%</div>
                                <div class="ring-center-lbl">Approved</div>
                            </div>
                        </div>
                        <div class="ring-legend">
                            <div class="ring-legend-item"><div class="ring-legend-dot" style="background:#16A34A;"></div>Approved<span class="ring-legend-val">{{ number_format($approvedCount) }}</span></div>
                            <div class="ring-legend-item"><div class="ring-legend-dot" style="background:#D97706;"></div>Pending<span class="ring-legend-val">{{ number_format($pendingCount) }}</span></div>
                            <div class="ring-legend-item"><div class="ring-legend-dot" style="background:#1B3F7A;"></div>Total<span class="ring-legend-val">{{ number_format($totalHouseholdsAll) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vulnerability Progress Bars --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Vulnerability Classification</div>
                    <span style="margin-left:auto;font-size:10px;color:var(--gray-400);">% of households</span>
                </div>
                <div class="chart-body" style="height:auto;padding:16px 18px;">
                    <div class="prog-list">
                        @foreach($vulnData as $v)
                        @php $vpct = $totalHouseholdsAll > 0 ? round(($v['count'] / $totalHouseholdsAll) * 100) : 0; @endphp
                        <div class="prog-row">
                            <div class="prog-label-row">
                                <span>{{ $v['label'] }}</span>
                                <strong>{{ number_format($v['count']) }} <span style="font-weight:400;color:var(--gray-400);">({{ $vpct }}%)</span></strong>
                            </div>
                            <div class="prog-track">
                                <div class="prog-fill" style="width:{{ $vpct }}%;background:{{ $v['color'] }};"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Employment mini breakdown --}}
                    @if(count($empBreakdown) > 0)
                    <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--gray-100);">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gray-400);margin-bottom:8px;">Employment Status</div>
                        <div class="bar-chart-list">
                            @foreach($empBreakdown as $es => $ec)
                            @php $epct = round(($ec / $maxEmp) * 100); @endphp
                            <div class="bcl-row">
                                <div class="bcl-label">{{ $es }}</div>
                                <div class="bcl-track"><div class="bcl-fill" style="width:{{ $epct }}%;background:#7C3AED;"></div></div>
                                <div class="bcl-val">{{ $ec }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Pending Approvals --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot" style="background:#D97706;border-color:#B45309;"></div>
                    <div class="ca-title">Pending Approvals</div>
                    <span style="margin-left:auto;font-size:10px;color:var(--gray-400);">{{ $pendingCount }} total</span>
                </div>
                <div class="chart-body" style="height:auto;padding:0 18px 4px;">
                    @forelse($pendingHouseholds as $ph)
                    <div class="pending-item">
                        <div>
                            <div class="pending-name">{{ Str::limit($ph->household_head_name, 24) }}</div>
                            <div class="pending-sub">{{ $ph->barangay }} · by {{ $ph->encoder?->name ?? 'Encoder' }} · {{ $ph->created_at->diffForHumans() }}</div>
                        </div>
                        <a href="{{ route('admin.households.show', $ph) }}" class="pending-approve-btn">Review</a>
                    </div>
                    @empty
                    <div style="text-align:center;padding:28px;color:var(--gray-400);font-size:12px;">🎉 All households approved!</div>
                    @endforelse
                    @if($pendingCount > 5)
                    <div style="text-align:center;padding:8px 0 12px;"><a href="{{ route('admin.households.index') }}?status=pending" style="font-size:11px;font-weight:600;color:var(--blue);text-decoration:none;">View all {{ $pendingCount }} pending →</a></div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── BOTTOM ROW: Housing + Water + Monthly Trend ── --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:16px;" class="mid-row">

            {{-- Housing Type --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Housing Type</div>
                </div>
                <div class="chart-body" style="height:auto;padding:16px 18px;">
                    @if(count($housingBreakdown) > 0)
                    <div class="bar-chart-list">
                        @foreach($housingBreakdown as $ht => $hc)
                        @php $hpct = round(($hc / $maxHt) * 100); @endphp
                        <div class="bcl-row">
                            <div class="bcl-label">{{ ucfirst(str_replace('_',' ',$ht)) }}</div>
                            <div class="bcl-track"><div class="bcl-fill" style="width:{{ $hpct }}%;background:#1B3F7A;"></div></div>
                            <div class="bcl-val">{{ $hc }}</div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center;padding:24px;color:var(--gray-400);font-size:12px;">No data yet</div>
                    @endif
                </div>
            </div>

            {{-- Water Source --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot" style="background:#2459A8;border-color:#1B3F7A;"></div>
                    <div class="ca-title">Water Source</div>
                </div>
                <div class="chart-body" style="height:auto;padding:16px 18px;">
                    @if(count($waterBreakdown) > 0)
                    <div class="bar-chart-list">
                        @foreach($waterBreakdown as $ws => $wc)
                        @php $wpct = round(($wc / $maxWater) * 100); @endphp
                        <div class="bcl-row" style="grid-template-columns:140px 1fr 32px;">
                            <div class="bcl-label" style="font-size:10px;" title="{{ $ws }}">{{ Str::limit($ws, 28) }}</div>
                            <div class="bcl-track"><div class="bcl-fill" style="width:{{ $wpct }}%;background:#2459A8;"></div></div>
                            <div class="bcl-val">{{ $wc }}</div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center;padding:24px;color:var(--gray-400);font-size:12px;">No data yet</div>
                    @endif
                </div>
            </div>

            {{-- Monthly Registration Trend --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot" style="background:#16A34A;border-color:#15803D;"></div>
                    <div class="ca-title">Monthly Registrations</div>
                    <span style="margin-left:auto;font-size:10px;color:var(--gray-400);">Last 6 months</span>
                </div>
                <div class="chart-body" style="height:200px;padding:16px 18px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

        </div>

        {{-- ── CHARTS ── --}}
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot"></div>
                    <div class="ca-title">Households per Barangay</div>
                </div>
                <div class="chart-body">
                    <canvas id="householdsBarChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot" style="background:#16A34A;border-color:#15803D;"></div>
                    <div class="ca-title">Special Categories</div>
                </div>
                <div class="chart-body chart-body-doughnut">
                    <div class="chart-canvas-wrap">
                        <canvas id="categoriesDoughnut"></canvas>
                    </div>
                    <div class="doughnut-legend" id="doughnutLegend"></div>
                </div>
            </div>
        </div>

        {{-- ── POPULATION CHARTS ── --}}
        <div class="pop-charts-row" style="display:grid;grid-template-columns:minmax(0,340px) 1fr;gap:14px;margin-bottom:16px;">
            {{-- Gender Doughnut --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot" style="background:#0891B2;border-color:#0E7490;"></div>
                    <div class="ca-title">Gender Distribution</div>
                    <span style="margin-left:auto;font-size:10px;color:var(--gray-400);">All Residents</span>
                </div>
                <div class="chart-body chart-body-doughnut">
                    <div class="chart-canvas-wrap">
                        <canvas id="genderDoughnut"></canvas>
                    </div>
                    <div class="doughnut-legend" id="genderLegend"></div>
                </div>
            </div>
            {{-- Age Group Bar Chart --}}
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="ca-dot" style="background:#7C3AED;border-color:#6D28D9;"></div>
                    <div class="ca-title">Age Group Breakdown</div>
                    <span style="margin-left:auto;font-size:10px;color:var(--gray-400);">All Residents</span>
                </div>
                <div class="chart-body">
                    <canvas id="ageGroupChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ── RECENT DISTRIBUTION EVENTS ── --}}
        <div class="content-area">
            <div class="content-area-header">
                <div class="ca-dot" style="background:#16A34A;border-color:#15803D;"></div>
                <div class="ca-title">Recent Distribution Events</div>
                <a href="{{ route('admin.distribution.logs') }}" class="ca-view-all">View All →</a>
            </div>
            <div class="dist-table-wrap" style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Relief Type</th>
                            <th class="col-barangay">Barangay</th>
                            <th>Date</th>
                            <th class="col-status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEvents as $event)
                        <tr>
                            <td class="dt-name">{{ $event->event_name }}</td>
                            <td>{{ is_array($event->relief_type) ? implode(', ', $event->relief_type) : $event->relief_type }}</td>
                            <td class="col-barangay">{{ $event->target_barangay_display }}</td>
                            <td>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</td>
                            <td class="col-status">
                                @if($event->status === 'ongoing')
                                    <span class="status-badge status-ongoing">Ongoing</span>
                                @elseif($event->status === 'completed')
                                    <span class="status-badge status-done">Completed</span>
                                @elseif($event->status === 'upcoming')
                                    <span class="status-badge status-upcoming">Upcoming</span>
                                @else
                                    <span class="status-badge status-cancelled">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:32px;color:var(--gray-400);font-size:12px;">No distribution events yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── DISTRIBUTION MAP ── --}}
        @php
            $mapEvents = \App\Models\DistributionEvent::whereNotNull('distribution_lat')
                ->whereNotNull('distribution_lng')
                ->select('id','event_name','relief_type','target_barangay','status',
                         'started_at','ended_at','distribution_lat','distribution_lng',
                         'distribution_location','event_date')
                ->latest('event_date')
                ->get();

            $dmUpcoming  = $mapEvents->where('status','upcoming')->count();
            $dmOngoing   = $mapEvents->where('status','ongoing')->count();
            $dmCompleted = $mapEvents->where('status','completed')->count();
            $dmCancelled = $mapEvents->where('status','cancelled')->count();
            $dmTotal     = $mapEvents->count();
            $dmRecent    = $mapEvents->filter(function($e) {
                return $e->event_date && \Carbon\Carbon::parse($e->event_date)->gte(now()->subDays(30));
            })->count();

            $dmEventsJson = $mapEvents->map(function($e) {
                $brgy = $e->target_barangay;
                return [
                    'id'       => $e->id,
                    'name'     => $e->event_name,
                    'type'     => $e->relief_type,
                    'barangay' => is_array($brgy) ? implode(', ', $brgy) : ($brgy ?? '—'),
                    'status'   => $e->status,
                    'date'     => $e->event_date ? \Carbon\Carbon::parse($e->event_date)->format('M d, Y') : '—',
                    'date_raw' => $e->event_date ? \Carbon\Carbon::parse($e->event_date)->format('Y-m-d') : null,
                    'lat'      => (float) $e->distribution_lat,
                    'lng'      => (float) $e->distribution_lng,
                    'loc'      => $e->distribution_location ?? '',
                ];
            })->values();
        @endphp

        <div class="dist-map-section">

            {{-- Header --}}
            <div class="dist-map-header">
                <div class="dist-map-title-wrap">
                    <div class="ca-dot"></div>
                    <div>
                        <div class="dist-map-title">Distribution Events Map</div>
                        <div class="dist-map-sub">Pinned distribution points across Naic, Cavite — click a marker for details</div>
                    </div>
                </div>
                <div class="dist-map-legend">
                    <div class="dm-legend-item"><span class="dm-legend-pin upcoming"></span>Upcoming</div>
                    <div class="dm-legend-item"><span class="dm-legend-pin ongoing"></span>Ongoing</div>
                    <div class="dm-legend-item"><span class="dm-legend-pin completed"></span>Completed</div>
                    <div class="dm-legend-item"><span class="dm-legend-pin cancelled"></span>Cancelled</div>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="dist-map-filters">
                <span class="dm-filter-label">Filter:</span>
                <button class="dm-filter-btn active f-all"       onclick="dmFilter('all',this)">All <span class="dm-fc">{{ $dmTotal }}</span></button>
                <button class="dm-filter-btn f-upcoming"         onclick="dmFilter('upcoming',this)">Upcoming <span class="dm-fc">{{ $dmUpcoming }}</span></button>
                <button class="dm-filter-btn f-ongoing"          onclick="dmFilter('ongoing',this)">Ongoing <span class="dm-fc">{{ $dmOngoing }}</span></button>
                <button class="dm-filter-btn f-completed"        onclick="dmFilter('completed',this)">Completed <span class="dm-fc">{{ $dmCompleted }}</span></button>
                <button class="dm-filter-btn f-cancelled"        onclick="dmFilter('cancelled',this)">Cancelled <span class="dm-fc">{{ $dmCancelled }}</span></button>
                <button class="dm-filter-btn f-recent"           onclick="dmFilter('recent',this)">Recent 30d <span class="dm-fc">{{ $dmRecent }}</span></button>
            </div>

            {{-- Map --}}
            <div class="dist-map-body">
                <div id="dashDistMap"></div>
                <div class="dist-map-no-pin" id="dmNoPin">
                    <div style="font-size:22px;margin-bottom:6px;">📍</div>
                    <strong style="color:var(--gray-600);">No pinned events</strong><br>
                    No events with this status have a pinned location yet.
                </div>
            </div>

            {{-- Stats footer --}}
            <div class="dist-map-footer">
                <div class="dm-stat">
                    <span class="dm-stat-val c-all">{{ $dmTotal }}</span>
                    <span class="dm-stat-label">Pinned Total</span>
                </div>
                <div class="dm-stat">
                    <span class="dm-stat-val c-upcoming">{{ $dmUpcoming }}</span>
                    <span class="dm-stat-label">Upcoming</span>
                </div>
                <div class="dm-stat">
                    <span class="dm-stat-val c-ongoing">{{ $dmOngoing }}</span>
                    <span class="dm-stat-label">Ongoing</span>
                </div>
                <div class="dm-stat">
                    <span class="dm-stat-val c-completed">{{ $dmCompleted }}</span>
                    <span class="dm-stat-label">Completed</span>
                </div>
                <div class="dm-stat">
                    <span class="dm-stat-val c-cancelled">{{ $dmCancelled }}</span>
                    <span class="dm-stat-label">Cancelled</span>
                </div>
            </div>

        </div>
        {{-- END DISTRIBUTION MAP --}}

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">
            &copy; <span id="footer-year"></span> <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office
        </div>
        <div class="footer-center">Republic of the Philippines</div>
        <a class="fb-link" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            facebook.com/naicmdrrmo
        </a>
    </footer>

</div>

<script>
    /* ── Clock ── */
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now = new Date();
        const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        document.getElementById('main-date').textContent = days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    /* ── Sidebar ── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    /* ── Charts ── */
    const barLabels = @json($householdsPerBarangay->pluck('barangay'));
    const barData   = @json($householdsPerBarangay->pluck('total'));
    const catLabels = ['4Ps Beneficiaries', 'Senior Citizens', 'PWD'];
    const catData   = [@json($total4Ps), @json($totalSeniors), @json($totalPwd)];
    const catColors = ['#16A34A', '#D4A800', '#C0392B'];

    new Chart(document.getElementById('householdsBarChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Households', data: barData,
                backgroundColor: 'rgba(27,63,122,0.15)', borderColor: '#1B3F7A',
                borderWidth: 2, borderRadius: 4, hoverBackgroundColor: 'rgba(27,63,122,0.28)',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#122D5A', titleColor: '#F5C518', bodyColor: '#fff', padding: 10, cornerRadius: 4,
                    callbacks: { label: ctx => ` ${ctx.parsed.y} household${ctx.parsed.y !== 1 ? 's' : ''}` }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10, family: 'Open Sans' }, color: '#9AA3B0', maxRotation: 35 } },
                y: { beginAtZero: true, grid: { color: '#F0F2F5' }, ticks: { font: { size: 11, family: 'Open Sans' }, color: '#9AA3B0', precision: 0 } }
            }
        }
    });

    new Chart(document.getElementById('categoriesDoughnut').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{ data: catData, backgroundColor: catColors.map(c => c+'33'), borderColor: catColors, borderWidth: 2, hoverOffset: 6 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#122D5A', titleColor: '#F5C518', bodyColor: '#fff', padding: 10, cornerRadius: 4 } }
        }
    });

    const legendEl = document.getElementById('doughnutLegend');
    catLabels.forEach((label, i) => {
        legendEl.innerHTML += `<div class="legend-item"><span class="legend-dot" style="background:${catColors[i]};"></span><span>${label}</span><span class="legend-val">${catData[i].toLocaleString()}</span></div>`;
    });

    /* ── Monthly Trend Chart ── */
    new Chart(document.getElementById('monthlyTrendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($monthlyRegs->pluck('month')),
            datasets: [{
                label: 'Households',
                data: @json($monthlyRegs->pluck('count')),
                borderColor: '#16A34A',
                backgroundColor: 'rgba(22,163,74,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#16A34A',
                pointRadius: 4,
                tension: 0.35,
                fill: true,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#122D5A', titleColor: '#F5C518', bodyColor: '#fff', padding: 10, cornerRadius: 4 }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 9, family: 'Open Sans' }, color: '#9AA3B0' } },
                y: { beginAtZero: true, grid: { color: '#F0F2F5' }, ticks: { font: { size: 10, family: 'Open Sans' }, color: '#9AA3B0', precision: 0 } }
            }
        }
    });

    /* ── Gender Distribution Chart ── */
    const genderLabels = ['Male', 'Female'];
    const genderData   = [@json($totalMale), @json($totalFemale)];
    const genderColors = ['#2459A8', '#DB2777'];

    new Chart(document.getElementById('genderDoughnut').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{ data: genderData, backgroundColor: genderColors.map(c => c+'33'), borderColor: genderColors, borderWidth: 2, hoverOffset: 6 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#122D5A', titleColor: '#F5C518', bodyColor: '#fff', padding: 10, cornerRadius: 4 } }
        }
    });

    const genderLegendEl = document.getElementById('genderLegend');
    const genderIcons = ['♂', '♀'];
    genderLabels.forEach((label, i) => {
        const total = genderData.reduce((a, b) => a + b, 0);
        const pct = total > 0 ? Math.round((genderData[i] / total) * 100) : 0;
        genderLegendEl.innerHTML += `<div class="legend-item"><span class="legend-dot" style="background:${genderColors[i]};"></span><span>${genderIcons[i]} ${label}</span><span class="legend-val">${genderData[i].toLocaleString()} <span style="font-weight:400;color:var(--gray-400);font-size:10px;">(${pct}%)</span></span></div>`;
    });

    /* ── Age Group Chart ── */
    const ageLabels = @json(array_keys($ageGroups));
    const ageData   = @json(array_values($ageGroups));
    const ageColors = ['#16A34A', '#F5C518', '#2459A8', '#C0392B'];

    new Chart(document.getElementById('ageGroupChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ageLabels,
            datasets: [{
                label: 'Residents',
                data: ageData,
                backgroundColor: ageColors.map(c => c + '33'),
                borderColor: ageColors,
                borderWidth: 2,
                borderRadius: 5,
                hoverBackgroundColor: ageColors.map(c => c + '66'),
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#122D5A', titleColor: '#F5C518', bodyColor: '#fff', padding: 10, cornerRadius: 4,
                    callbacks: { label: ctx => ` ${ctx.parsed.y.toLocaleString()} resident${ctx.parsed.y !== 1 ? 's' : ''}` }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10, family: 'Open Sans' }, color: '#9AA3B0' } },
                y: { beginAtZero: true, grid: { color: '#F0F2F5' }, ticks: { font: { size: 11, family: 'Open Sans' }, color: '#9AA3B0', precision: 0 } }
            }
        }
    });

    /* ── Force Chart.js to resize on window resize ── */
    window.addEventListener('resize', () => {
        Chart.instances && Object.values(Chart.instances).forEach(chart => {
            chart.resize();
        });
    });

    /* ══════════════════════════════════════════
       DISTRIBUTION MAP
    ══════════════════════════════════════════ */
    const DM_EVENTS = @json($dmEventsJson);

    const DM_COLORS = { upcoming:'#2459A8', ongoing:'#16A34A', completed:'#6B7280', cancelled:'#C0392B' };
    const DM_LABELS = { upcoming:'Upcoming', ongoing:'Ongoing', completed:'Completed', cancelled:'Cancelled' };
    const DM_ICONS  = { upcoming:'🕐', ongoing:'⚡', completed:'✅', cancelled:'✕' };

    function dmPinIcon(status) {
        const c = DM_COLORS[status] || '#2459A8';
        return L.divIcon({
            className: '',
            html: `<svg width="28" height="38" viewBox="0 0 28 38" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="14" cy="36" rx="5" ry="2" fill="rgba(0,0,0,.22)"/>
                <path d="M14 1C8.48 1 4 5.48 4 11c0 7.5 10 25 10 25S24 18.5 24 11C24 5.48 19.52 1 14 1z"
                    fill="${c}" stroke="rgba(255,255,255,0.85)" stroke-width="2"/>
                <circle cx="14" cy="11" r="5" fill="white" opacity="0.9"/>
            </svg>`,
            iconSize: [28,38], iconAnchor: [14,38], popupAnchor: [0,-40],
        });
    }

    function dmPopupHTML(ev) {
        const c = DM_COLORS[ev.status] || '#2459A8';
        return `<div class="dm-popup-inner">
            <div class="dm-popup-head" style="border-top:3px solid ${c}">
                <span class="dm-sbadge ${ev.status}">${DM_ICONS[ev.status]} ${DM_LABELS[ev.status]}</span>
                <span class="dm-popup-name">${ev.name}</span>
            </div>
            <div class="dm-popup-body">
                <div class="dm-popup-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
                    <span>${ev.type || '—'}</span>
                </div>
                <div class="dm-popup-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    <span>${ev.barangay}</span>
                </div>
                <div class="dm-popup-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>${ev.date}</span>
                </div>
                ${ev.loc ? `<div class="dm-popup-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span style="font-size:10px;color:#9AA3B0">${ev.loc}</span>
                </div>` : ''}
                <a class="dm-popup-link" href="{{ route('admin.distribution.logs') }}#event-${ev.id}">View Distribution Log →</a>
            </div>
        </div>`;
    }

    /* Init map */
    const NAIC = [14.3294, 120.7614];
    let dmMap = null;
    let dmMarkers = [];
    let dmCurrentFilter = 'all';

    function initDashMap() {
        if (dmMap) return;
        dmMap = L.map('dashDistMap').setView(NAIC, 13);

        const baseLayers = {
            "🛰️ Satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '© Esri, Maxar, Earthstar Geographics', maxZoom: 19
            }),
            "🗺️ Street Map": L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap © CARTO', subdomains: 'abcd', maxZoom: 19
            }),
            "🏙️ Street (Detailed)": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors', maxZoom: 19
            }),
            "📐 Topo": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap © OpenTopoMap', maxZoom: 17
            }),
        };

        baseLayers["🛰️ Satellite"].addTo(dmMap);
        L.control.layers(baseLayers, null, { position: 'topright', collapsed: false }).addTo(dmMap);

        DM_EVENTS.forEach(ev => {
            if (!ev.lat || !ev.lng) return;
            const marker = L.marker([ev.lat, ev.lng], { icon: dmPinIcon(ev.status) })
                .addTo(dmMap)
                .bindPopup(dmPopupHTML(ev), { className:'dm-popup', maxWidth:260, minWidth:215 });
            dmMarkers.push({ marker, status: ev.status, date_raw: ev.date_raw || null });
        });

        if (dmMarkers.length === 1) {
            dmMap.setView(dmMarkers[0].marker.getLatLng(), 15);
        } else if (dmMarkers.length > 1) {
            dmMap.fitBounds(L.latLngBounds(dmMarkers.map(m => m.marker.getLatLng())), { padding:[40,40] });
        }

        dmUpdateNotice();
        setTimeout(() => dmMap.invalidateSize(true), 100);
        setTimeout(() => dmMap.invalidateSize(true), 500);
    }

    window.dmFilter = function(status, btn) {
        dmCurrentFilter = status;
        document.querySelectorAll('.dm-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const now30 = new Date();
        now30.setDate(now30.getDate() - 30);

        dmMarkers.forEach(({ marker, status: ms, date_raw }) => {
            let show;
            if (status === 'all') {
                show = true;
            } else if (status === 'recent') {
                show = date_raw && new Date(date_raw) >= now30;
            } else {
                show = ms === status;
            }
            if (show && !dmMap.hasLayer(marker)) marker.addTo(dmMap);
            if (!show && dmMap.hasLayer(marker))  dmMap.removeLayer(marker);
        });

        dmUpdateNotice();

        const visible = dmMarkers.filter(({ status:ms }) => status === 'all' || ms === status).map(({marker}) => marker.getLatLng());
        if (visible.length === 1)      dmMap.setView(visible[0], 15);
        else if (visible.length > 1)   dmMap.fitBounds(L.latLngBounds(visible), { padding:[40,40] });
    };

    function dmUpdateNotice() {
        const now30 = new Date();
        now30.setDate(now30.getDate() - 30);
        const count = dmMarkers.filter(({ status, date_raw }) => {
            if (dmCurrentFilter === 'all')    return true;
            if (dmCurrentFilter === 'recent') return date_raw && new Date(date_raw) >= now30;
            return status === dmCurrentFilter;
        }).length;
        document.getElementById('dmNoPin').classList.toggle('show', count === 0);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashMap);
    } else {
        initDashMap();
    }

    /* ══════════════════════════════════════════
       AUTO-REFRESH — every 30 seconds
    ══════════════════════════════════════════ */
    const REFRESH_MS  = 30_000;
    const SCROLL_KEY  = 'dash_scroll';
    const mainContent = document.querySelector('.main-content');

    const savedScroll = sessionStorage.getItem(SCROLL_KEY);
    if (savedScroll !== null && mainContent) {
        mainContent.scrollTop = parseInt(savedScroll, 10);
        sessionStorage.removeItem(SCROLL_KEY);
    }

    function doRefresh() {
        if (mainContent) sessionStorage.setItem(SCROLL_KEY, mainContent.scrollTop);
        location.reload();
    }

    let remaining = REFRESH_MS / 1000;
    const timerEl = document.createElement('span');
    timerEl.style.cssText = 'font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:0.5px;';
    timerEl.title = 'Auto-refresh countdown';
    document.querySelector('.topbar-right').prepend(timerEl);

    const countdownInterval = setInterval(() => {
        remaining--;
        timerEl.textContent = `Refresh in ${remaining}s`;
        if (remaining <= 0) {
            clearInterval(countdownInterval);
            doRefresh();
        }
    }, 1000);

    mainContent && mainContent.addEventListener('scroll', () => {
        remaining = REFRESH_MS / 1000;
    });
</script>
</body>
</html>