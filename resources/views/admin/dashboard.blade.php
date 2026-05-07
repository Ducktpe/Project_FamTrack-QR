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

        /* ── TOPBAR ── */
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ── HEADER ── */
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
        .header-admin-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid var(--gray-200); border-radius: 4px; flex-shrink: 0; }
        .admin-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .admin-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .admin-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ── SIDEBAR ── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 250; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; opacity: 1; }
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-left: 3px solid transparent; transition: background 0.12s, color 0.12s, border-color 0.12s; }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn { width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white); border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .logout-btn:hover { background: #C0392B; }

        /* ── MAIN ── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 24px 28px; }

        /* ── PAGE TITLEBAR ── */
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 11px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ── WELCOME CARD ── */
        .welcome-card { background: var(--blue); border-left: 5px solid var(--yellow); padding: 18px 24px; display: flex; align-items: center; gap: 18px; margin-bottom: 20px; }
        .welcome-card img { width: 46px; height: 46px; object-fit: contain; flex-shrink: 0; }
        .welcome-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.55); margin-bottom: 3px; }
        .welcome-heading { font-family: 'PT Serif', serif; font-size: 19px; font-weight: 700; color: var(--white); }
        .welcome-heading em { color: var(--yellow); font-style: normal; }
        .welcome-desc { font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 3px; }

        /* ── QUICK NAV ── */
        .quick-nav { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .qnav-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 16px 18px; cursor: pointer; text-decoration: none; display: flex; flex-direction: column; gap: 8px; transition: box-shadow 0.15s, border-top-color 0.15s; }
        .qnav-card:hover { box-shadow: 0 3px 12px rgba(27,63,122,0.12); border-top-color: var(--yellow); }
        .qnav-icon { width: 30px; height: 30px; background: var(--blue-pale); border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .qnav-icon svg { width: 16px; height: 16px; color: var(--blue); }
        .qnav-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .qnav-desc { font-size: 11px; color: var(--gray-600); }

        /* ══════════════════════════════════════════════════════════
           PLAN B — INTERACTIVE SECTOR DASHBOARD
        ══════════════════════════════════════════════════════════ */

        /* Summary strip */
        .pb-strip { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 20px; }
        .pb-strip-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 12px 14px; display: flex; align-items: center; gap: 11px; transition: box-shadow 0.15s, transform 0.15s; }
        .pb-strip-card:hover { box-shadow: 0 3px 10px rgba(27,63,122,0.1); transform: translateY(-1px); }
        .pb-si { width: 34px; height: 34px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .pb-si svg { width: 17px; height: 17px; }
        .pb-sl { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: var(--gray-400); margin-bottom: 2px; }
        .pb-sv { font-size: 20px; font-weight: 700; line-height: 1; font-variant-numeric: tabular-nums; }
        .pb-ss { font-size: 10px; color: var(--gray-400); margin-top: 1px; }

        /* Main donut + summary row */
        .pb-top { display: flex; gap: 16px; align-items: center; margin-bottom: 16px; }
        .pb-donut-col { flex: 0 0 300px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; }
        .pb-donut-wrap { position: relative; width: 280px; height: 280px; }
        .pb-donut-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; }
        .pb-dc-val { font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; font-variant-numeric: tabular-nums; }
        .pb-dc-lbl { font-size: 10px; color: var(--gray-400); margin-top: 2px; text-transform: uppercase; letter-spacing: .5px; text-align: center; }
        .pb-leg { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; max-width: 300px; }
        .pb-li { display: flex; align-items: center; gap: 4px; font-size: 10px; color: var(--gray-600); }
        .pb-lsq { width: 8px; height: 8px; border-radius: 2px; flex-shrink: 0; }
        .pb-cards-left { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 14px; }
        .pb-cards-right { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 14px; }
        .pb-card { background: var(--white); border: 1px solid var(--gray-200); padding: 18px 16px; border-left: 3px solid #ccc; display: flex; flex-direction: column; justify-content: center; flex: 1; }
        .pb-card-l { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--gray-400); margin-bottom: 4px; }
        .pb-card-v { font-size: 22px; font-weight: 700; line-height: 1; color: var(--blue-dark); font-variant-numeric: tabular-nums; }
        .pb-card-s { font-size: 10px; color: var(--gray-400); margin-top: 3px; }

        /* Sector toggle buttons */
        .pb-sector-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 14px; }
        .pb-sbtn { display: flex; flex-direction: column; align-items: center; gap: 5px; padding: 12px 8px 10px; border-radius: 8px; border: 1px solid var(--gray-200); background: var(--white); cursor: pointer; transition: all .18s; position: relative; user-select: none; font-family: 'Open Sans', sans-serif; }
        .pb-sbtn:hover { background: var(--gray-50); }
        .pb-sbtn.on { color: #fff; border-color: transparent; }
        .pb-sbtn svg { width: 20px; height: 20px; }
        .pb-sbtn-count { font-size: 18px; font-weight: 700; line-height: 1; font-variant-numeric: tabular-nums; }
        .pb-sbtn-lbl { font-size: 11px; font-weight: 600; text-align: center; line-height: 1.3; }
        .pb-sbtn-dot { position: absolute; top: 7px; right: 7px; width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,.85); opacity: 0; transition: opacity .15s; }
        .pb-sbtn.on .pb-sbtn-dot { opacity: 1; }

        /* Explore zone */
        .pb-ez { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 24px; border-radius: 6px; overflow: hidden; overflow-x: hidden; }
        .pb-ez-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 12px 16px; border-bottom: 2px solid var(--gray-100); background: linear-gradient(to right, var(--gray-50), var(--white)); }
        .pb-ez-title { font-size: 12px; font-weight: 700; color: var(--blue-dark); letter-spacing: 0.3px; }
        .pb-ez-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; max-width: 100%; }
        .pb-frow { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .pb-flbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: var(--gray-400); white-space: nowrap; }
        .pb-select { height: 30px; padding: 0 28px 0 10px; border-radius: 5px; border: 1px solid var(--gray-200); background: var(--white); font-size: 11px; font-weight: 600; color: var(--gray-800); cursor: pointer; font-family: 'Open Sans', sans-serif; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%239AA3B0' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 8px center; transition: border-color .15s, box-shadow .15s; min-width: 160px; }
        .pb-select:hover { border-color: var(--blue-light); }
        .pb-select:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(27,63,122,0.1); }
        .pb-fp { padding: 4px 12px; border-radius: 5px; border: 1px solid var(--gray-200); background: var(--white); font-size: 11px; font-weight: 600; color: var(--gray-600); cursor: pointer; transition: all .15s; white-space: nowrap; font-family: 'Open Sans', sans-serif; height: 30px; }
        .pb-fp:hover { background: var(--gray-50); border-color: var(--gray-400); }
        .pb-fp.on { color: #fff; border-color: transparent; }
        .pb-ct { display: flex; gap: 3px; border: 1px solid var(--gray-200); border-radius: 5px; overflow: hidden; padding: 2px; background: var(--gray-50); }
        .pb-ctb { width: 26px; height: 26px; border-radius: 3px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .15s; }
        .pb-ctb:hover { background: var(--white); }
        .pb-ctb.on { background: var(--blue-dark); border-color: transparent; }
        .pb-ctb.on svg { stroke: #fff !important; }
        .pb-ctb svg { width: 13px; height: 13px; stroke: var(--gray-400); fill: none; stroke-width: 2; }

        /* Explore body */
        .pb-ez-body { display: flex; min-height: 260px; flex-wrap: wrap; align-content: flex-start; width: 100%; overflow-x: hidden; }
        .pb-col { flex: 1 1 260px; min-width: 260px; padding: 14px 16px; border-right: 1px solid var(--gray-100); }
        .pb-col:last-child { border-right: none; }
        .pb-col-head { display: flex; align-items: center; gap: 6px; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--gray-100); flex-wrap: wrap; }
        .pb-col-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .pb-col-title { font-size: 12px; font-weight: 600; color: var(--blue-dark); }
        .pb-col-sub { font-size: 10px; color: var(--gray-400); margin-left: auto; min-width: 0; }
        .pb-chart-area { position: relative; width: 100%; height: 200px; margin-bottom: 10px; }
        .pb-bar-list { display: flex; flex-direction: column; gap: 7px; }
        .pb-br { display: grid; grid-template-columns: 100px 1fr 34px; align-items: center; gap: 6px; }
        .pb-bn { font-size: 11px; color: var(--gray-600); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pb-bt { height: 7px; background: var(--gray-100); border-radius: 3px; overflow: hidden; }
        .pb-bf { height: 100%; border-radius: 3px; transition: width .4s ease; }
        .pb-bv { font-size: 11px; text-align: right; font-variant-numeric: tabular-nums; color: var(--gray-800); font-weight: 600; }
        .pb-col-sublabel { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: var(--gray-400); margin: 12px 0 7px; }

        /* Cross insight */
        .pb-cross { border-top: 1px solid var(--gray-100); padding: 12px 16px; background: var(--gray-50); }
        .pb-cross-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--gray-400); margin-bottom: 10px; }
        .pb-cross-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .pb-cc { background: var(--white); border: 1px solid var(--gray-200); padding: 10px 12px; }
        .pb-cc-val { font-size: 18px; font-weight: 700; line-height: 1; margin-bottom: 2px; font-variant-numeric: tabular-nums; }
        .pb-cc-lbl { font-size: 11px; font-weight: 600; color: var(--blue-dark); margin-bottom: 1px; }
        .pb-cc-sub { font-size: 10px; color: var(--gray-400); }

        /* Map in explore */
        .pb-map-row { border-top: 1px solid var(--gray-100); }
        .pb-map-leg { display: flex; gap: 10px; flex-wrap: wrap; padding: 8px 16px; background: var(--gray-50); border-bottom: 1px solid var(--gray-100); }
        #pbMap { width: 100%; height: 280px; }

        /* QR switch */
        .pb-qrsw-row { display: flex; gap: 5px; flex-wrap: wrap; }

        /* Empty state */
        .pb-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 260px; color: var(--gray-400); font-size: 12px; gap: 8px; width: 100%; }
        .pb-empty svg { width: 28px; height: 28px; opacity: .25; }

        /* Consolidated multi-sector view */
        #pbConsolidated { display: flex; flex-direction: column; gap: 14px; padding: 14px 16px; width: 100%; }
        #pbConsolidated > div { animation: pbsli 0.25s ease; }
        #pbMultiSectorChart { max-height: 240px; }


        /* Status badges */
        .status-badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-ongoing   { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .status-done      { background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F5; }
        .status-upcoming  { background: var(--yellow-pale); color: var(--yellow-dark); border: 1px solid #FDE68A; }
        .status-cancelled { background: var(--red-pale); color: var(--red); border: 1px solid #FECACA; }

        /* Existing dist map styles preserved */
        .dist-map-section { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 24px; }
        .dist-map-header { padding: 14px 20px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .dist-map-title-wrap { display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
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
        #dashDistMap { height: 380px; width: 100%; }
        .dist-map-no-pin { display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); background: rgba(255,255,255,0.93); border: 1px solid var(--gray-200); border-radius: 8px; padding: 18px 28px; text-align: center; font-size: 12px; color: var(--gray-400); pointer-events: none; z-index: 500; }
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
        .dm-stat-label { font-size: 10px; color: var(--gray-400); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .dm-popup .leaflet-popup-content-wrapper { border-radius: 6px; padding: 0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.18); }
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
        .dm-popup-row svg { width: 11px; height: 11px; flex-shrink: 0; margin-top: 1px; color: #9AA3B0; }
        .dm-popup-link { margin-top: 8px; padding-top: 8px; border-top: 1px solid #f0f0f0; font-size: 11px; font-weight: 700; color: #2459A8; text-decoration: none; display: block; }

        /* ── FOOTER ── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.45); }
        .footer-left strong { color: rgba(255,255,255,0.75); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.25); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); text-decoration: none; white-space: nowrap; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        /* ═══════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════ */

        /* ── Tablet / collapsed sidebar (≤ 900px) ── */
        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; height: 100vh; overflow: hidden; }
            .sidebar { grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 1200; transform: translateX(-100%); transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .sidebar.open { transform: translateX(0); left: 0; }
            .sidebar-overlay { display: block !important; z-index: 1100; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }
            .hamburger { display: flex; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            header { padding: 0 16px; gap: 10px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .header-admin-badge { padding: 6px 10px; gap: 8px; }
            .admin-name { font-size: 12px; }
            .admin-role { display: none; }
            .main-content { padding: 20px 16px; }
            .pb-strip { grid-template-columns: repeat(3, 1fr); gap: 8px; }
            .quick-nav { grid-template-columns: repeat(4, 1fr); gap: 8px; }
        }

        /* ── Large mobile (≤ 640px) ── */
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline, .status-indicator { display: none; }
            .shell { grid-template-rows: 36px auto 1fr 48px; }
            .main-content { padding: 16px 12px; }
            /* Header */
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider, .header-logos img:last-child, .header-org { display: none; }
            .header-title { font-size: 13px; line-height: 1.3; }
            .header-admin-badge { padding: 5px 8px; }
            .admin-avatar { width: 28px; height: 28px; font-size: 11px; }
            .admin-name { font-size: 11px; }
            /* Page titlebar */
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }
            /* Welcome */
            .welcome-card { padding: 12px 14px; gap: 10px; }
            .welcome-card img { width: 36px; height: 36px; }
            .welcome-heading { font-size: 15px; }
            .welcome-desc, .welcome-label { display: none; }
            /* Quick nav */
            .quick-nav { grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 14px; }
            .qnav-desc { display: none; }
            .qnav-card { padding: 12px 14px; gap: 6px; }
            /* Summary strip */
            .pb-strip { grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 14px; }
            .pb-strip-card--qr { grid-column: 1 / -1; max-width: 50%; justify-self: center; width: 100%; }
            .pb-strip-card { padding: 10px 11px; gap: 8px; }
            .pb-si { width: 28px; height: 28px; }
            .pb-si svg { width: 14px; height: 14px; }
            .pb-sv { font-size: 18px; }
            .pb-sl { font-size: 9px; }
            /* Donut + cards top row */
            .pb-top { flex-direction: column; align-items: stretch; gap: 12px; margin-bottom: 12px; }
            .pb-donut-col { flex: none; width: 100%; align-items: center; order: -1; }
            .pb-donut-wrap { width: 220px; height: 220px; }
            .pb-dc-val { font-size: 20px; }
            .pb-cards-left { flex-direction: row; gap: 8px; }
            .pb-cards-right { flex-direction: row; gap: 8px; }
            .pb-card { padding: 10px 12px; flex: 1; }
            .pb-card-v { font-size: 17px; }
            /* Sector buttons */
            .pb-sector-row { grid-template-columns: repeat(2, 1fr); gap: 6px; margin-bottom: 10px; }
            .pb-sbtn { padding: 10px 8px 8px; border-radius: 6px; }
            .pb-sbtn-count { font-size: 16px; }
            .pb-sbtn-lbl { font-size: 10px; }
            /* Explore zone */
            .pb-ez-head { padding: 10px 12px; gap: 8px; flex-direction: column; align-items: flex-start; }
            .pb-ez-controls { width: 100%; }
            .pb-select { min-width: 0; width: 100%; }
            .pb-ez-body { flex-direction: column; min-height: auto; }
            .pb-col { border-right: none; border-bottom: 1px solid var(--gray-100); padding: 12px; }
            .pb-col:last-child { border-bottom: none; }
            .pb-br { grid-template-columns: 75px 1fr 30px; }
            #pbConsolidated { padding: 12px !important; }
            #pbConsolidated > div:first-child { grid-template-columns: 1fr 1fr !important; }

            /* Cross insight */
            .pb-cross-grid { grid-template-columns: repeat(2, 1fr); }
            /* Map */
            #pbMap { height: 220px; }
            #dashDistMap { height: 260px; }
            .dist-map-legend { display: none; }
            .dist-map-filters { gap: 4px; padding: 7px 12px; }
            .dm-filter-btn { padding: 3px 8px; font-size: 10px; }
            .dm-filter-btn .dm-fc { display: none; }
            .dist-map-footer { flex-wrap: wrap; }
            .dm-stat { min-width: 50%; }
            /* Footer */
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            .fb-link { font-size: 10px; }
        }

        /* ── Small mobile (≤ 480px) — targets 442px viewport ── */
        @media (max-width: 480px) {
            .shell { grid-template-rows: 28px 52px 1fr 40px; }
            .main-content { padding: 10px 8px; }
            /* Topbar */
            .topbar { padding: 0 10px; }
            .topbar-left { display: none; }
            .status-indicator { font-size: 10px; }
            /* Header */
            header { padding: 0 8px; }
            .header-title { font-size: 13px; }
            /* Strip: 2 col but tighter */
            .pb-strip { grid-template-columns: repeat(2, 1fr); gap: 6px; }
            .pb-strip-card--qr { grid-column: 1 / -1; max-width: 50%; justify-self: center; width: 100%; }
            .pb-strip-card { padding: 8px 10px; gap: 7px; }
            .pb-ss { display: none; }
            /* Donut smaller */
            .pb-donut-wrap { width: 140px; height: 140px; }
            .pb-dc-val { font-size: 16px; }
            /* Sector btns: hide label, icon only layout */
            .pb-sector-row { grid-template-columns: repeat(4, 1fr); gap: 5px; }
            .pb-sbtn { padding: 8px 4px 6px; border-radius: 6px; }
            .pb-sbtn-lbl { display: none; }
            .pb-sbtn-count { font-size: 13px; }
            .pb-sbtn svg { width: 16px; height: 16px; }
            /* Cards: donut top, cards side by side */
            .pb-donut-col { order: -1; }
            .pb-cards-left { flex-direction: row; }
            .pb-cards-right { flex-direction: row; }
            /* Cross */
            .pb-cross-grid { grid-template-columns: 1fr 1fr; }
            /* Quick nav 2 col */
            .quick-nav { grid-template-columns: repeat(2, 1fr); }
        }

        /* ── Very small (≤ 380px) ── */
        @media (max-width: 380px) {
            .pb-strip { grid-template-columns: repeat(2, 1fr); }
            .pb-sector-row { grid-template-columns: repeat(2, 1fr); }
            .pb-sbtn-lbl { display: none; }
            #pbConsolidated > div:first-child { grid-template-columns: 1fr !important; }
            #pbConsolidated > div:nth-child(3) { grid-template-columns: 1fr !important; }

        }

        /* slide-in animation */
        .pb-slide { animation: pbsli .2s ease; }
        @keyframes pbsli { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
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
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard Overview
        </a>
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Distribution Events
        </a>
        <a href="{{ route('admin.distribution.logs') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
            Distribution Logs
        </a>
        <a href="{{ route('admin.residents.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
            List of Residents
        </a>
        <a href="{{ route('admin.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
            List of Households
        </a>
        <a href="{{ route('admin.traillog.trail') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Trail Logs
        </a>
        <a href="{{ route('admin.distribution.scan-history') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/><polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/><rect x="8" y="8" width="8" height="8" rx="1"/></svg>
            Staff Scan History
        </a>
        <hr class="sidebar-sep">
        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
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
                <div class="page-breadcrumb">Home / <span>Dashboard</span></div>
                <div class="page-h1">Dashboard Overview</div>
                <div class="page-sub">Barangay Family Track QR Relief Distribution System — MDRRMO Naic, Cavite</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <img src="{{ asset('/images/mdrrmo-logo.png') }}" alt="MDRRMO">
            <div>
                <div class="welcome-label">Welcome Back</div>
                <div class="welcome-heading">Good day, <em>{{ auth()->user()->name }}!</em></div>
                <div class="welcome-desc">Office of the Municipal Disaster Risk Reduction and Management Officer — Naic, Cavite</div>
            </div>
        </div>

        <!-- Quick Nav -->
        <div class="quick-nav">
            <a href="{{ route('admin.events.quick-create') }}" class="qnav-card">
                <div class="qnav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <div class="qnav-title">Distribution Events</div>
                <div class="qnav-desc">Create &amp; manage relief events</div>
            </a>
            <a href="{{ route('admin.distribution.logs') }}" class="qnav-card">
                <div class="qnav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg></div>
                <div class="qnav-title">Distribution Logs</div>
                <div class="qnav-desc">Track all relief distributions</div>
            </a>
            <a href="{{ route('admin.residents.index') }}" class="qnav-card">
                <div class="qnav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg></div>
                <div class="qnav-title">List of Residents</div>
                <div class="qnav-desc">Manage resident profiles</div>
            </a>
            <a href="{{ route('admin.households.index') }}" class="qnav-card">
                <div class="qnav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg></div>
                <div class="qnav-title">List of Households</div>
                <div class="qnav-desc">View &amp; manage households</div>
            </a>
        </div>

        {{-- ══════════════════════════════════════════════════════
             PLAN B — INTERACTIVE SECTOR DASHBOARD
        ══════════════════════════════════════════════════════ --}}

        {{-- Persistent summary strip --}}
        <div class="pb-strip">
            <div class="pb-strip-card" style="border-top-color:#1B3F7A">
                <div class="pb-si" style="background:#EAF0FA"><svg viewBox="0 0 24 24" fill="none" stroke="#1B3F7A" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg></div>
                <div><div class="pb-sl">Residents</div><div class="pb-sv" style="color:#1B3F7A">{{ number_format($totalResidents) }}</div><div class="pb-ss">Family members</div></div>
            </div>
            <div class="pb-strip-card" style="border-top-color:#2459A8">
                <div class="pb-si" style="background:#EAF0FA"><svg viewBox="0 0 24 24" fill="none" stroke="#2459A8" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg></div>
                <div><div class="pb-sl">Households</div><div class="pb-sv" style="color:#2459A8">{{ number_format($totalHouseholds) }}</div><div class="pb-ss">{{ number_format($approvedCount) }} approved</div></div>
            </div>
            <div class="pb-strip-card" style="border-top-color:#7C3AED">
                <div class="pb-si" style="background:#F5F3FF"><svg viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg></div>
                <div><div class="pb-sl">Vulnerable</div><div class="pb-sv" style="color:#7C3AED">{{ number_format($totalVulnerable) }}</div><div class="pb-ss">Flagged households</div></div>
            </div>
            <div class="pb-strip-card" style="border-top-color:#BA7517">
                <div class="pb-si" style="background:#FFFAE6"><svg viewBox="0 0 24 24" fill="none" stroke="#BA7517" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <div><div class="pb-sl">Events</div><div class="pb-sv" style="color:#BA7517">{{ number_format($totalEvents) }}</div><div class="pb-ss">{{ number_format($ongoingEvents) }} ongoing</div></div>
            </div>
            <div class="pb-strip-card pb-strip-card--qr" style="border-top-color:#1D9E75">
                <div class="pb-si" style="background:#DCFCE7"><svg viewBox="0 0 24 24" fill="none" stroke="#1D9E75" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
                <div><div class="pb-sl">Active QR</div><div class="pb-sv" style="color:#1D9E75">{{ number_format($activeQr) }}</div><div class="pb-ss">of {{ number_format($totalQr) }} total</div></div>
            </div>
        </div>

        {{-- Main donut + summary cards --}}
        <div class="pb-top">
            <div class="pb-cards-left">
                <div class="pb-card" style="border-left-color:#1B3F7A"><div class="pb-card-l">Residents</div><div class="pb-card-v">{{ number_format($totalResidents) }}</div><div class="pb-card-s">Family members</div></div>
                <div class="pb-card" style="border-left-color:#7C3AED"><div class="pb-card-l">Vulnerable HH</div><div class="pb-card-v">{{ number_format($totalVulnerable) }}</div><div class="pb-card-s">4Ps · PWD · Senior · Solo</div></div>
            </div>
            <div class="pb-donut-col">
                <div class="pb-donut-wrap">
                    <canvas id="pbMainDonut"></canvas>
                    <div class="pb-donut-center">
                        <div class="pb-dc-val" id="pbDcVal">All</div>
                        <div class="pb-dc-lbl" id="pbDcLbl">Overview</div>
                    </div>
                </div>
                <div class="pb-leg" id="pbMainLeg"></div>
            </div>
            <div class="pb-cards-right" id="pbCards">
                <div class="pb-card" style="border-left-color:#2459A8"><div class="pb-card-l">Households</div><div class="pb-card-v">{{ number_format($totalHouseholds) }}</div><div class="pb-card-s">{{ number_format($approvedCount) }} approved</div></div>
                <div class="pb-card" style="border-left-color:#1D9E75"><div class="pb-card-l">Active QR</div><div class="pb-card-v">{{ number_format($activeQr) }}</div><div class="pb-card-s">of {{ number_format($totalQr) }} issued</div></div>
            </div>
        </div>

        {{-- Sector toggle buttons --}}
        <div class="pb-sector-row" id="pbSectorRow">
            <button class="pb-sbtn" data-key="population" style="">
                <div class="pb-sbtn-dot"></div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
                <div class="pb-sbtn-count">{{ number_format($totalResidents) }}</div>
                <div class="pb-sbtn-lbl">Population</div>
            </button>
            <button class="pb-sbtn" data-key="vulnerable" style="">
                <div class="pb-sbtn-dot"></div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
                <div class="pb-sbtn-count">{{ number_format($totalVulnerable) }}</div>
                <div class="pb-sbtn-lbl">Vulnerable Sector</div>
            </button>
            <button class="pb-sbtn" data-key="distribution" style="">
                <div class="pb-sbtn-dot"></div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <div class="pb-sbtn-count">{{ number_format($totalEvents) }}</div>
                <div class="pb-sbtn-lbl">Distribution</div>
            </button>
            <button class="pb-sbtn" data-key="qr" style="">
                <div class="pb-sbtn-dot"></div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <div class="pb-sbtn-count">{{ number_format($totalQr) }}</div>
                <div class="pb-sbtn-lbl">QR Information</div>
            </button>
        </div>

        {{-- Explore zone --}}
        <div class="pb-ez" id="pbEz">
            <div class="pb-ez-head">
                <div class="pb-ez-title" id="pbEzTitle">Select a sector above to explore data</div>
                <div class="pb-ez-controls" id="pbEzControls"></div>
            </div>
            <div class="pb-ez-body" id="pbEzBody">
                <div class="pb-empty" style="flex:1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Click a sector button to explore its data
                </div>
            </div>
        </div>

        {{-- ── EXISTING DISTRIBUTION MAP (below Plan B) ── --}}
        @php
            $dmEventsJson = $mapEvents->map(function($e) {
                $brgy = $e->target_barangay;
                if (is_string($brgy)) { $d = json_decode($brgy, true); $brgy = is_array($d) ? $d : [$brgy]; }
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
            $dmRecent = $mapEvents->filter(fn($e) => $e->event_date && \Carbon\Carbon::parse($e->event_date)->gte(now()->subDays(30)))->count();
        @endphp

        <div class="dist-map-section" id="distMapSection" style="display:none">
            <div class="dist-map-header">
                <div class="dist-map-title-wrap">
                    <div class="ca-dot"></div>
                    <div>
                        <div class="dist-map-title">Distribution Events Map</div>
                        <div class="dist-map-sub">All pinned distribution points — click a marker for details</div>
                    </div>
                </div>
                <div class="dist-map-legend">
                    <div class="dm-legend-item"><span class="dm-legend-pin upcoming"></span>Upcoming</div>
                    <div class="dm-legend-item"><span class="dm-legend-pin ongoing"></span>Ongoing</div>
                    <div class="dm-legend-item"><span class="dm-legend-pin completed"></span>Completed</div>
                    <div class="dm-legend-item"><span class="dm-legend-pin cancelled"></span>Cancelled</div>
                </div>
            </div>
            <div class="dist-map-filters">
                <span class="dm-filter-label">Filter:</span>
                <button class="dm-filter-btn active f-all"  onclick="dmFilter('all',this)">All <span class="dm-fc">{{ $dmTotal }}</span></button>
                <button class="dm-filter-btn f-upcoming"    onclick="dmFilter('upcoming',this)">Upcoming <span class="dm-fc">{{ $dmUpcoming }}</span></button>
                <button class="dm-filter-btn f-ongoing"     onclick="dmFilter('ongoing',this)">Ongoing <span class="dm-fc">{{ $dmOngoing }}</span></button>
                <button class="dm-filter-btn f-completed"   onclick="dmFilter('completed',this)">Completed <span class="dm-fc">{{ $dmCompleted }}</span></button>
                <button class="dm-filter-btn f-cancelled"   onclick="dmFilter('cancelled',this)">Cancelled <span class="dm-fc">{{ $dmCancelled }}</span></button>
                <button class="dm-filter-btn f-recent"      onclick="dmFilter('recent',this)">Recent 30d <span class="dm-fc">{{ $dmRecent }}</span></button>
            </div>
            <div class="dist-map-body">
                <div id="dashDistMap"></div>
                <div class="dist-map-no-pin" id="dmNoPin">
                    <div style="font-size:22px;margin-bottom:6px;">📍</div>
                    <strong style="color:var(--gray-600);">No pinned events</strong><br>
                    No events with this filter have a pinned location.
                </div>
            </div>
            <div class="dist-map-footer">
                <div class="dm-stat"><span class="dm-stat-val c-all">{{ $dmTotal }}</span><span class="dm-stat-label">Pinned Total</span></div>
                <div class="dm-stat"><span class="dm-stat-val c-upcoming">{{ $dmUpcoming }}</span><span class="dm-stat-label">Upcoming</span></div>
                <div class="dm-stat"><span class="dm-stat-val c-ongoing">{{ $dmOngoing }}</span><span class="dm-stat-label">Ongoing</span></div>
                <div class="dm-stat"><span class="dm-stat-val c-completed">{{ $dmCompleted }}</span><span class="dm-stat-label">Completed</span></div>
                <div class="dm-stat"><span class="dm-stat-val c-cancelled">{{ $dmCancelled }}</span><span class="dm-stat-label">Cancelled</span></div>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">&copy; <span id="footer-year"></span> <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office</div>
        <div class="footer-center">Republic of the Philippines</div>
        <a class="fb-link" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            facebook.com/naicmdrrmo
        </a>
    </footer>

</div><!-- /shell -->

<script>
/* ── Clock ── */
function pad(n){ return String(n).padStart(2,'0'); }
function updateClock(){
    const now=new Date();
    const days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const months=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const shortM=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    document.getElementById('top-time').textContent=pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
    document.getElementById('top-date').textContent=days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    document.getElementById('main-date').textContent=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
}
updateClock(); setInterval(updateClock,1000);
document.getElementById('footer-year').textContent=new Date().getFullYear();

/* ── Sidebar ── */
const sidebar=document.getElementById('sidebar');
const overlay=document.getElementById('sidebarOverlay');
function openSidebar(){ sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow='hidden'; }
function closeSidebar(){ sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow=''; }
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeSidebar(); });

/* PLAN B DATA - from Laravel controller */
const HOUSEHOLDS = {!! json_encode($householdsJson) !!};
const MEMBERS    = {!! json_encode($membersJson) !!};
const DETAILS    = {!! json_encode($detailsJson) !!};
const EVENTS     = {!! json_encode($eventsJson) !!};
const QR_CODES   = {!! json_encode($qrJson) !!};

/* ── Config ── */
const SECTOR_CFG = [
    { key:'population',   label:'Population',        color:'#1B3F7A' },
    { key:'vulnerable',   label:'Vulnerable Sector',  color:'#7C3AED' },
    { key:'distribution', label:'Distribution',        color:'#BA7517' },
    { key:'qr',           label:'QR Information',      color:'#1D9E75' },
];
const STATUS_COLORS = { upcoming:'#2459A8', ongoing:'#16A34A', completed:'#6B7280', cancelled:'#C0392B' };

/* ── State ── */
const PB = { active: new Set(), brgy:'All', qrType:new Set(), chartType:'bar', sexFilter:new Set(), ageFilter:new Set(), housingFilter:new Set(), vulnFilter:new Set(), distFilter:new Set(), _lastKeys:'' };
/* Helper: get the active QR type string ('all','household','family_head') from the Set */
function getQrType(){ return PB.qrType.size===0 ? 'all' : [...PB.qrType][0]; }

/* Full official barangay list for Naic, Cavite */
const BRGYS = [
    'All',
    'Bagong Kalsada','Balsahan','Bancaan','Bucana Malaki','Bucana Sasahan',
    'Calubcob','Capt. C. Nazareno (Poblacion)','Gombalza (Poblacion)',
    'Halang','Humbac','Ibayo Estacion','Ibayo Silangan','Kanluran',
    'Labac','Latoria','Mabolo','Makina','Malainen Bago','Malainen Luma',
    'Molino','Munting Mapino','Muzon','Palangue 2 & 3','Palangue Central',
    'Sabang','San Roque','Santulan','Sapa','Timalan Balsahan','Timalan Concepcion'
];

let pbMainChart = null;
let pbLeafMap   = null;
let pbDetCharts = {};

/* ── Computed helpers ── */
function filtH(brgy){ return brgy==='All' ? HOUSEHOLDS : HOUSEHOLDS.filter(h=>h.barangay===brgy); }
function memOfH(hids){ return MEMBERS.filter(m=>hids.includes(m.household_id)); }
function detOfM(mids){ return DETAILS.filter(d=>mids.includes(d.family_member_id)); }
function ageGroup(bday){
    if(!bday) return 'Unknown';
    const age=Math.floor((Date.now()-new Date(bday))/(365.25*24*3600*1000));
    if(age<=12) return 'Children 0-12';
    if(age<=17) return 'Teen 13-17';
    if(age<=59) return 'Adult 18-59';
    return 'Senior 60+';
}
function countBy(arr,fn){ const o={}; arr.forEach(x=>{ const k=fn(x)||'Unknown'; o[k]=(o[k]||0)+1; }); return o; }
function normalizeHousing(value){
    if(!value) return '';
    return value.trim().replace(/\s+/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
}
function filtE(brgy){
    if(brgy==='All') return EVENTS;
    return EVENTS.filter(e=>e.barangay.includes('All Barangays')||e.barangay.includes(brgy));
}
function filtQR(brgy,type){
    const hids=filtH(brgy).map(h=>h.id);
    let codes=QR_CODES.filter(q=>hids.includes(q.household_id));
    const t = (typeof type === 'string') ? type : getQrType();
    if(t==='family_head'){
        const headHids=new Set(MEMBERS.filter(m=>m.is_family_head).map(m=>m.household_id));
        codes=codes.filter(q=>headHids.has(q.household_id));
    } else if(t==='household'){
        // Every QR code is household-linked, so household mode shows all household QR cards.
        codes=codes;
    }
    return codes;
}

/* ── Donut data per sector ── */
function donutData(key){
    const h=filtH(PB.brgy); const hids=h.map(x=>x.id); const m=memOfH(hids);
    if(key==='population'){
        const male=m.filter(x=>x.sex==='Male').length, female=m.filter(x=>x.sex==='Female').length;
        return {labels:['Male','Female'],data:[male,female],colors:['#1B3F7A','#85B7EB']};
    }
    if(key==='vulnerable'){
        return {labels:['4Ps','PWD','Senior','Solo Parent'],data:[h.filter(x=>x.is_4ps).length,h.filter(x=>x.is_pwd).length,h.filter(x=>x.is_senior).length,h.filter(x=>x.is_solo).length],colors:['#1B3F7A','#7C3AED','#D97706','#DB2777']};
    }
    if(key==='distribution'){
        const evs=filtE(PB.brgy); const bs=countBy(evs,e=>e.status);
        return {labels:Object.keys(bs),data:Object.values(bs),colors:Object.keys(bs).map(s=>STATUS_COLORS[s]||'#888')};
    }
    if(key==='qr'){
        const codes=filtQR(PB.brgy,getQrType());
        const a=codes.filter(x=>x.is_active).length, i=codes.filter(x=>!x.is_active).length;
        return {labels:['Active','Inactive'],data:[a,i],colors:['#1D9E75','#C0392B']};
    }
}

/* ── Update main donut ── */
function updateDonut(){
    let labels=[],data=[],colors=[];
    if(PB.active.size===0){
        const tot=MEMBERS.length; const vuln=HOUSEHOLDS.filter(h=>h.is_4ps||h.is_pwd||h.is_senior||h.is_solo).length;
        labels=['Population','Vulnerable','Distribution','QR']; data=[tot,vuln,EVENTS.length,QR_CODES.length]; colors=['#1B3F7A','#7C3AED','#BA7517','#1D9E75'];
        document.getElementById('pbDcVal').textContent='All'; document.getElementById('pbDcLbl').textContent='Overview';
    } else if(PB.active.size===1){
        const key=[...PB.active][0]; const d=donutData(key);
        labels=d.labels; data=d.data; colors=d.colors;
        const tot=data.reduce((a,b)=>a+b,0);
        document.getElementById('pbDcVal').textContent=tot.toLocaleString();
        document.getElementById('pbDcLbl').textContent=SECTOR_CFG.find(x=>x.key===key).label;
    } else {
        [...PB.active].forEach(key=>{
            const cfg=SECTOR_CFG.find(x=>x.key===key); const d=donutData(key);
            labels.push(cfg.label); data.push(d.data.reduce((a,b)=>a+b,0)); colors.push(cfg.color);
        });
        document.getElementById('pbDcVal').textContent=data.reduce((a,b)=>a+b,0).toLocaleString();
        document.getElementById('pbDcLbl').textContent='Combined';
    }
    if(pbMainChart){ pbMainChart.data.labels=labels; pbMainChart.data.datasets[0].data=data; pbMainChart.data.datasets[0].backgroundColor=colors; pbMainChart.update('active'); }
    const tot2=data.reduce((a,b)=>a+b,0);
    document.getElementById('pbMainLeg').innerHTML=labels.map((l,i)=>`<span class="pb-li"><span class="pb-lsq" style="background:${colors[i]}"></span>${l} ${tot2?Math.round(data[i]/tot2*100):0}%</span>`).join('');
}

/* ── Update summary cards ── */
function updateCards(){
    const keys=[...PB.active]; const lastKey=keys[keys.length-1]||null;
    const leftCol=document.querySelector('.pb-cards-left');
    const rightCol=document.getElementById('pbCards');
    if(!lastKey){ return; }
    const h=filtH(PB.brgy); const hids=h.map(x=>x.id);
    const mAll=memOfH(hids);
    const m = PB.sexFilter.size===0 ? mAll : mAll.filter(x=>PB.sexFilter.has(x.sex));
    let cards=[];
    if(lastKey==='population'){
        cards=[
            {l:'Residents',v:m.length.toLocaleString(),s:'in '+(PB.brgy==='All'?'all barangays':PB.brgy),c:'#1B3F7A'},
            {l:'Households',v:h.length.toLocaleString(),s:h.filter(x=>x.approved).length+' approved',c:'#2459A8'},
            {l:'Male',v:mAll.filter(x=>x.sex==='Male').length.toLocaleString(),s:Math.round(mAll.filter(x=>x.sex==='Male').length/Math.max(mAll.length,1)*100)+'%',c:'#1B3F7A'},
            {l:'Female',v:mAll.filter(x=>x.sex==='Female').length.toLocaleString(),s:Math.round(mAll.filter(x=>x.sex==='Female').length/Math.max(mAll.length,1)*100)+'%',c:'#DB2777'},
        ];
    } else if(lastKey==='vulnerable'){
        cards=[
            {l:'4Ps Households',v:h.filter(x=>x.is_4ps).length+'',s:"Gov't beneficiary",c:'#1B3F7A'},
            {l:'PWD Households',v:h.filter(x=>x.is_pwd).length+'',s:'Persons w/ disability',c:'#7C3AED'},
            {l:'Senior Households',v:h.filter(x=>x.is_senior).length+'',s:'Age 60+ flagged',c:'#D97706'},
            {l:'Solo Parent HH',v:h.filter(x=>x.is_solo).length+'',s:'Registered',c:'#DB2777'},
        ];
    } else if(lastKey==='distribution'){
        const evs=filtE(PB.brgy);
        cards=[
            {l:'Total Events',v:evs.length+'',s:'in scope',c:'#BA7517'},
            {l:'Upcoming',v:evs.filter(e=>e.status==='upcoming').length+'',s:'Scheduled',c:'#2459A8'},
            {l:'Household Scan',v:evs.filter(e=>e.scan_mode==='household').length+'',s:'QR scan mode',c:'#1D9E75'},
            {l:'Family Head Scan',v:evs.filter(e=>e.scan_mode==='family_head').length+'',s:'QR scan mode',c:'#7C3AED'},
        ];
    } else if(lastKey==='qr'){
        const codes=filtQR(PB.brgy,getQrType());
        cards=[
            {l:'Total QR',v:codes.length.toLocaleString(),s:getQrType()==='all'?'All types':getQrType()+' QR',c:'#1D9E75'},
            {l:'Active',v:codes.filter(x=>x.is_active).length.toLocaleString(),s:Math.round(codes.filter(x=>x.is_active).length/Math.max(codes.length,1)*100)+'% rate',c:'#16A34A'},
            {l:'Inactive',v:codes.filter(x=>!x.is_active).length+'',s:'Deactivated',c:'#C0392B'},
            {l:'Unique HH',v:new Set(codes.map(x=>x.household_id)).size+'',s:'with QR codes',c:'#2459A8'},
        ];
    }
    const cardHTML=c=>`<div class="pb-card" style="border-left-color:${c.c}"><div class="pb-card-l">${c.l}</div><div class="pb-card-v" style="color:${c.c}">${c.v}</div><div class="pb-card-s">${c.s}</div></div>`;
    if(leftCol) leftCol.innerHTML=[cards[0],cards[2]].filter(Boolean).map(cardHTML).join('');
    if(rightCol) rightCol.innerHTML=[cards[1],cards[3]].filter(Boolean).map(cardHTML).join('');
}

/* ── Explore zone ── */
function updateExplore(){
    const keys=[...PB.active];
    const title=document.getElementById('pbEzTitle');
    const controls=document.getElementById('pbEzControls');
    const body=document.getElementById('pbEzBody');
    const keysStr=keys.join(',');
    const sectorsChanged=(PB._lastKeys!==keysStr);

    if(keys.length===0){
        // Full reset when all sectors deselected
        Object.values(pbDetCharts).forEach(c=>{ try{c.destroy();}catch(e){} });
        pbDetCharts={};
        if(pbLeafMap){ pbLeafMap.remove(); pbLeafMap=null; }
        const existCross=document.getElementById('pbCross'); if(existCross) existCross.remove();
        title.textContent='Select a sector above to explore data'; controls.innerHTML='';
        body.innerHTML=`<div class="pb-empty" style="flex:1"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Click a sector button to explore its data</div>`;
        PB._lastKeys='';
        return;
    }

    const names=keys.map(k=>SECTOR_CFG.find(x=>x.key===k).label);
    title.textContent=names.join(' + ')+' — detailed view';

    if(sectorsChanged){
        // Sectors changed — destroy everything and rebuild
        Object.values(pbDetCharts).forEach(c=>{ try{c.destroy();}catch(e){} });
        pbDetCharts={};
        if(pbLeafMap){ pbLeafMap.remove(); pbLeafMap=null; }
        const existCross=document.getElementById('pbCross'); if(existCross) existCross.remove();

        buildControls(controls, keys);
        PB._lastKeys=keysStr;

        body.innerHTML=''; body.className='pb-ez-body pb-slide';
        
        // NEW: Use consolidated layout for multiple sectors, column layout for single
        if(keys.length===1){
            // Single sector: show detailed column view as before
            keys.forEach(key=>{ const col=document.createElement('div'); col.className='pb-col'; col.innerHTML=`<div class="pb-col-head"><div class="pb-col-dot" style="background:${SECTOR_CFG.find(x=>x.key===key).color}"></div><div class="pb-col-title">${SECTOR_CFG.find(x=>x.key===key).label}</div><div class="pb-col-sub">${PB.brgy==='All'?'All barangays':PB.brgy}</div></div><div id="pbCol-${key}"></div>`; body.appendChild(col); });
        } else {
            // Multiple sectors: show consolidated comparison view
            renderConsolidatedView(body, keys);
        }

        // Don't add map to explore zone — keep it only in main distribution section below
    } else {
        // Only a filter changed
        Object.values(pbDetCharts).forEach(c=>{ try{c.destroy();}catch(e){} });
        pbDetCharts={};
        if(pbLeafMap){ pbLeafMap.remove(); pbLeafMap=null; }
        const existCross=document.getElementById('pbCross'); if(existCross) existCross.remove();
        
        if(keys.length===1){
            // Single sector: update columns
            keys.forEach(key=>{
                const colEl=document.getElementById('pbCol-'+key);
                if(colEl){
                    const sub=colEl.closest('.pb-col')&&colEl.closest('.pb-col').querySelector('.pb-col-sub');
                    if(sub) sub.textContent=PB.brgy==='All'?'All barangays':PB.brgy;
                }
            });
        } else {
            // Multiple sectors: refresh consolidated view
            const consViewEl = document.getElementById('pbConsolidated');
            if(consViewEl) consViewEl.remove();
            renderConsolidatedView(body, keys, true);
        }
        

    }

    if(keys.length===1){
        buildCross(keys);
        keys.forEach(key=>renderCol(key));
    } else {
        renderConsolidatedCharts(keys);
    }
}

/* NEW: Consolidated view for multiple sectors */
function renderConsolidatedView(body, keys, isUpdate=false){
    const consDiv=document.createElement('div'); 
    consDiv.id='pbConsolidated'; 
    consDiv.style.cssText='display:flex;flex-direction:column;gap:14px;padding:14px 16px;width:100%;';
    
    // Key metrics comparison grid
    const h=filtH(PB.brgy); const hids=h.map(x=>x.id); const m=memOfH(hids); const evs=filtE(PB.brgy); const codes=filtQR(PB.brgy,getQrType());
    const metricsCards = buildMultiSectorMetrics(keys, {h,hids,m,evs,codes});
    
    // Add metrics grid
    const metricsDiv=document.createElement('div');
    metricsDiv.style.cssText='display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;';
    metricsCards.forEach(card=>{
        const cardEl=document.createElement('div');
        cardEl.style.cssText=`background:#fff;border:1px solid var(--gray-200);border-left:3px solid ${card.color};padding:14px;border-radius:4px;`;
        cardEl.innerHTML=`<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:4px">${card.label}</div>
            <div style="font-size:20px;font-weight:700;color:${card.color};margin-bottom:2px;font-variant-numeric:tabular-nums">${card.value}</div>
            <div style="font-size:10px;color:var(--gray-600)">${card.sublabel}</div>`;
        metricsDiv.appendChild(cardEl);
    });
    consDiv.appendChild(metricsDiv);
    
    // Consolidated chart (if applicable)
    const chartDiv=document.createElement('div');
    chartDiv.style.cssText='background:#fff;border:1px solid var(--gray-200);padding:12px;border-radius:4px;';
    const chartCanvas=document.createElement('canvas');
    chartCanvas.id='pbMultiSectorChart';
    chartDiv.appendChild(chartCanvas);
    consDiv.appendChild(chartDiv);
    
    // Detailed breakdown cards
    const breakdownDiv=document.createElement('div');
    breakdownDiv.style.cssText='display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:10px;';
    
    keys.forEach(key=>{
        const sectorDiv=document.createElement('div');
        sectorDiv.style.cssText=`background:#fff;border:1px solid var(--gray-200);border-radius:4px;overflow:hidden;`;
        const cfg=SECTOR_CFG.find(x=>x.key===key);
        sectorDiv.innerHTML=`<div style="background:${cfg.color};color:#fff;padding:10px 12px;font-weight:700;font-size:12px">${cfg.label}</div><div id="pbSector-${key}" style="padding:12px"></div>`;
        breakdownDiv.appendChild(sectorDiv);
    });
    consDiv.appendChild(breakdownDiv);
    
    body.appendChild(consDiv);
    
    // Render sector breakdowns
    keys.forEach(key=>renderSectorBreakdown(key));
}

function buildMultiSectorMetrics(keys, data){
    const {h,hids,m,evs,codes}=data;
    const cards=[];
    
    // Apply filters to data
    const mFiltered = (PB.sexFilter.size===0 && PB.ageFilter.size===0 && PB.housingFilter.size===0) ? m 
        : m.filter(x=>{
            const sexOk = PB.sexFilter.size===0 || PB.sexFilter.has(x.sex);
            const ageOk = PB.ageFilter.size===0 || PB.ageFilter.has(ageGroup(x.birthday));
            const hh = HOUSEHOLDS.find(hh=>hh.id===x.household_id);
            const housingOk = PB.housingFilter.size===0 || !hh || !hh.housing || PB.housingFilter.has(normalizeHousing(hh.housing));
            return sexOk && ageOk && housingOk;
        });
    
    const hFiltered = (PB.vulnFilter.size===0 && PB.housingFilter.size===0) ? h
        : h.filter(hh=>{
            const vulnOk = PB.vulnFilter.size===0 || (hh.is_4ps||hh.is_pwd||hh.is_senior||hh.is_solo);
            const housingOk = PB.housingFilter.size===0 || !hh.housing || PB.housingFilter.has(normalizeHousing(hh.housing));
            return vulnOk && housingOk;
        });
    
    const evsFiltered = PB.distFilter.size===0 ? evs : evs.filter(e=>PB.distFilter.has(e.status));
    const codesFiltered = codes; // QR filters handled separately by getQrType()
    
    keys.forEach(key=>{
        const cfg=SECTOR_CFG.find(x=>x.key===key);
        if(key==='population'){
            cards.push({label:'Total Residents',value:mFiltered.length.toLocaleString(),sublabel:'Family members',color:cfg.color});
            cards.push({label:'Approved HH',value:hFiltered.filter(x=>x.approved).length.toLocaleString(),sublabel:'Ready for aid',color:'#16A34A'});
        } else if(key==='vulnerable'){
            const vulnCount=hFiltered.filter(x=>x.is_4ps||x.is_pwd||x.is_senior||x.is_solo).length;
            cards.push({label:'Vulnerable HH',value:vulnCount.toLocaleString(),sublabel:`${Math.round(vulnCount/Math.max(hFiltered.length,1)*100)}% of households`,color:cfg.color});
        } else if(key==='distribution'){
            cards.push({label:'Total Events',value:evsFiltered.length.toLocaleString(),sublabel:`${evsFiltered.filter(e=>e.status==='ongoing').length} ongoing`,color:cfg.color});
        } else if(key==='qr'){
            const activeCount=codesFiltered.filter(x=>x.is_active).length;
            cards.push({label:'Active QR Codes',value:activeCount.toLocaleString(),sublabel:`${Math.round(activeCount/Math.max(codesFiltered.length,1)*100)}% active`,color:cfg.color});
        }
    });
    
    // Cross-sector insight cards
    if(keys.includes('population')&&keys.includes('vulnerable')){
        const vulnHH=hFiltered.filter(x=>x.is_4ps||x.is_pwd||x.is_senior||x.is_solo);
        cards.push({label:'Vulnerable Rate',value:Math.round(vulnHH.length/Math.max(hFiltered.length,1)*100)+'%',sublabel:'of all households',color:'#7C3AED'});
    }
    if(keys.includes('vulnerable')&&keys.includes('qr')){
        const vulnHH=hFiltered.filter(x=>x.is_4ps||x.is_pwd||x.is_senior||x.is_solo);
        const coverage=codesFiltered.filter(q=>vulnHH.map(x=>x.id).includes(q.household_id)&&q.is_active).length;
        cards.push({label:'QR Coverage (Vuln)',value:Math.round(coverage/Math.max(vulnHH.length,1)*100)+'%',sublabel:'vulnerable with active QR',color:'#1D9E75'});
    }
    if(keys.includes('population')&&keys.includes('distribution')){
        cards.push({label:'Events Planned',value:evsFiltered.length.toLocaleString(),sublabel:`for ${hFiltered.length} households`,color:'#BA7517'});
    }
    
    return cards;
}

function renderSectorBreakdown(key){
    const el=document.getElementById('pbSector-'+key); if(!el) return;
    const h=filtH(PB.brgy); const hids=h.map(x=>x.id); const m=memOfH(hids); const evs=filtE(PB.brgy); const codes=filtQR(PB.brgy,getQrType());
    
    // Apply active filters
    const mFiltered = (PB.sexFilter.size===0 && PB.ageFilter.size===0 && PB.housingFilter.size===0) ? m 
        : m.filter(x=>{
            const sexOk = PB.sexFilter.size===0 || PB.sexFilter.has(x.sex);
            const ageOk = PB.ageFilter.size===0 || PB.ageFilter.has(ageGroup(x.birthday));
            const hh = HOUSEHOLDS.find(hh=>hh.id===x.household_id);
            const housingOk = PB.housingFilter.size===0 || !hh || !hh.housing || PB.housingFilter.has(normalizeHousing(hh.housing));
            return sexOk && ageOk && housingOk;
        });
    
    const hFiltered = (PB.vulnFilter.size===0 && PB.housingFilter.size===0) ? h
        : h.filter(hh=>{
            const vulnOk = PB.vulnFilter.size===0 || (hh.is_4ps||hh.is_pwd||hh.is_senior||hh.is_solo);
            const housingOk = PB.housingFilter.size===0 || !hh.housing || PB.housingFilter.has(normalizeHousing(hh.housing));
            return vulnOk && housingOk;
        });
    
    const evsFiltered = PB.distFilter.size===0 ? evs : evs.filter(e=>PB.distFilter.has(e.status));
    
    let html='';
    if(key==='population'){
        const male=mFiltered.filter(x=>x.sex==='Male').length, female=mFiltered.filter(x=>x.sex==='Female').length;
        html=`<div style="margin-bottom:8px"><span style="font-size:10px;color:var(--gray-600)">Male:</span> <span style="font-weight:700">${male.toLocaleString()}</span></div>
              <div style="margin-bottom:8px"><span style="font-size:10px;color:var(--gray-600)">Female:</span> <span style="font-weight:700">${female.toLocaleString()}</span></div>
              <div style="margin-bottom:8px"><span style="font-size:10px;color:var(--gray-600)">Households:</span> <span style="font-weight:700">${hFiltered.length.toLocaleString()}</span></div>`;
    } else if(key==='vulnerable'){
        const cats=['4Ps','PWD','Senior','Solo Parent'];
        const getVal=(t)=>hFiltered.filter(x=>(t==='4Ps'?x.is_4ps:t==='PWD'?x.is_pwd:t==='Senior'?x.is_senior:x.is_solo)).length;
        html=cats.map(t=>`<div style="margin-bottom:6px;display:flex;justify-content:space-between"><span style="font-size:10px;color:var(--gray-600)">${t}:</span> <span style="font-weight:700">${getVal(t).toLocaleString()}</span></div>`).join('');
    } else if(key==='distribution'){
        const stats=['upcoming','ongoing','completed','cancelled'];
        html=stats.map(s=>`<div style="margin-bottom:6px;display:flex;justify-content:space-between"><span style="font-size:10px;color:var(--gray-600)">${s.charAt(0).toUpperCase()+s.slice(1)}:</span> <span style="font-weight:700">${evsFiltered.filter(e=>e.status===s).length}</span></div>`).join('');
    } else if(key==='qr'){
        const active=codes.filter(x=>x.is_active).length;
        html=`<div style="margin-bottom:6px;display:flex;justify-content:space-between"><span style="font-size:10px;color:var(--gray-600)">Active:</span> <span style="font-weight:700">${active.toLocaleString()}</span></div>
              <div style="margin-bottom:6px;display:flex;justify-content:space-between"><span style="font-size:10px;color:var(--gray-600)">Inactive:</span> <span style="font-weight:700">${(codes.length-active).toLocaleString()}</span></div>
              <div style="margin-bottom:6px;display:flex;justify-content:space-between"><span style="font-size:10px;color:var(--gray-600)">Coverage:</span> <span style="font-weight:700">${Math.round(active/Math.max(codes.length,1)*100)}%</span></div>`;
    }
    el.innerHTML=html;
}

function renderConsolidatedCharts(keys){
    const h=filtH(PB.brgy); const hids=h.map(x=>x.id); const m=memOfH(hids); const evs=filtE(PB.brgy); const codes=filtQR(PB.brgy,getQrType());
    
    // Apply active filters
    const mFiltered = (PB.sexFilter.size===0 && PB.ageFilter.size===0 && PB.housingFilter.size===0) ? m 
        : m.filter(x=>{
            const sexOk = PB.sexFilter.size===0 || PB.sexFilter.has(x.sex);
            const ageOk = PB.ageFilter.size===0 || PB.ageFilter.has(ageGroup(x.birthday));
            const hh = HOUSEHOLDS.find(hh=>hh.id===x.household_id);
            const housingOk = PB.housingFilter.size===0 || !hh || !hh.housing || PB.housingFilter.has(normalizeHousing(hh.housing));
            return sexOk && ageOk && housingOk;
        });
    
    const hFiltered = (PB.vulnFilter.size===0 && PB.housingFilter.size===0) ? h
        : h.filter(hh=>{
            const vulnOk = PB.vulnFilter.size===0 || (hh.is_4ps||hh.is_pwd||hh.is_senior||hh.is_solo);
            const housingOk = PB.housingFilter.size===0 || !hh.housing || PB.housingFilter.has(normalizeHousing(hh.housing));
            return vulnOk && housingOk;
        });
    
    const evsFiltered = PB.distFilter.size===0 ? evs : evs.filter(e=>PB.distFilter.has(e.status));
    
    const datasets=[];
    keys.forEach((key,idx)=>{
        if(key==='population') datasets.push(mFiltered.length);
        else if(key==='vulnerable') datasets.push(hFiltered.filter(x=>x.is_4ps||x.is_pwd||x.is_senior||x.is_solo).length);
        else if(key==='distribution') datasets.push(evsFiltered.length);
        else if(key==='qr') datasets.push(codes.filter(q=>hids.includes(q.household_id)).length);
    });
    
    const canvasEl=document.getElementById('pbMultiSectorChart');
    if(canvasEl&&datasets.length>0){
        pbDetCharts['multi']=new Chart(canvasEl,{
            type:'bar',
            data:{labels:keys.map(k=>SECTOR_CFG.find(x=>x.key===k).label),datasets:[{label:'Count',data:datasets,backgroundColor:keys.map(k=>SECTOR_CFG.find(x=>x.key===k).color),borderWidth:0}]},
            options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{callback:v=>v.toLocaleString()}}}}
        });
    }
}

function makeSwitcherSingle(options, currentSet, onChange){
    // Single-select version: only one value active at a time; clicking active value deselects
    const sw=document.createElement('div');
    sw.style.cssText='display:flex;gap:3px;border:1px solid var(--gray-200);border-radius:5px;overflow:hidden;padding:2px;background:var(--gray-50);flex-wrap:wrap;';
    function applyStyles(){
        sw.querySelectorAll('button[data-val]').forEach(b=>{
            const val=b.dataset.val; const col=b.dataset.col;
            const isAll=(val==='__all__');
            const active=isAll?(currentSet.size===0):currentSet.has(val);
            b.style.background=active?col:'transparent';
            b.style.color=active?'#fff':'var(--gray-600)';
        });
    }
    const allBtn=document.createElement('button');
    allBtn.dataset.val='__all__'; allBtn.dataset.col='#1B3F7A';
    allBtn.style.cssText='padding:3px 10px;border:none;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;font-family:Open Sans,sans-serif;transition:all .15s;white-space:nowrap;';
    allBtn.textContent='All';
    allBtn.addEventListener('click',()=>{ currentSet.clear(); applyStyles(); onChange(currentSet); });
    sw.appendChild(allBtn);
    options.forEach(([val,label,col])=>{
        if(val==='All') return;
        const c=col||'#1B3F7A';
        const b=document.createElement('button');
        b.dataset.val=val; b.dataset.col=c;
        b.style.cssText='padding:3px 10px;border:none;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;font-family:Open Sans,sans-serif;transition:all .15s;white-space:nowrap;';
        b.textContent=label;
        b.addEventListener('click',()=>{
            if(currentSet.has(val)){ currentSet.clear(); } // toggle off
            else { currentSet.clear(); currentSet.add(val); } // switch to this one
            applyStyles(); onChange(currentSet);
        });
        sw.appendChild(b);
    });
    applyStyles();
    return sw;
}
function makeSwitcher(options, currentSet, onChange){
    // currentSet is a Set of active values; empty Set = "All" (no filter)
    const sw=document.createElement('div');
    sw.style.cssText='display:flex;gap:3px;border:1px solid var(--gray-200);border-radius:5px;overflow:hidden;padding:2px;background:var(--gray-50);flex-wrap:wrap;';

    function applyStyles(){
        sw.querySelectorAll('button[data-val]').forEach(b=>{
            const val=b.dataset.val;
            const col=b.dataset.col;
            const isAll=(val==='__all__');
            const active=isAll?(currentSet.size===0):currentSet.has(val);
            b.style.background=active?col:'transparent';
            b.style.color=active?'#fff':'var(--gray-600)';
        });
    }

    // "All" button
    const allBtn=document.createElement('button');
    allBtn.dataset.val='__all__'; allBtn.dataset.col='#1B3F7A';
    allBtn.style.cssText='padding:3px 10px;border:none;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;font-family:Open Sans,sans-serif;transition:all .15s;white-space:nowrap;';
    allBtn.textContent='All';
    allBtn.addEventListener('click',()=>{ currentSet.clear(); applyStyles(); onChange(currentSet); });
    sw.appendChild(allBtn);

    options.forEach(([val,label,col])=>{
        if(val==='All') return; // skip if caller passed an All option
        const c=col||'#1B3F7A';
        const b=document.createElement('button');
        b.dataset.val=val; b.dataset.col=c;
        b.style.cssText='padding:3px 10px;border:none;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;font-family:Open Sans,sans-serif;transition:all .15s;white-space:nowrap;';
        b.textContent=label;
        b.addEventListener('click',()=>{
            if(currentSet.has(val)) currentSet.delete(val); else currentSet.add(val);
            if(currentSet.size===0){} // show All active
            applyStyles();
            onChange(currentSet);
        });
        sw.appendChild(b);
    });
    applyStyles();
    return sw;
}
function addFilterRow(container,label,control){
    container.className='pb-frow';
    const lbl=document.createElement('span');
    lbl.className='pb-flbl';
    lbl.textContent=label;
    container.appendChild(lbl);
    container.appendChild(control);
    return container;
}
function addFilterGroup(container,title,rows){
    const group=document.createElement('div');
    group.style.cssText='border:1px solid var(--gray-200);border-radius:10px;background:#fff;padding:12px;margin-bottom:12px;';
    const head=document.createElement('div');
    head.style.cssText='font-size:12px;font-weight:700;color:#111827;margin-bottom:10px;';
    head.textContent=title;
    group.appendChild(head);
    rows.forEach(row=>group.appendChild(row));
    container.appendChild(group);
}

function buildControls(container, keys){
    container.innerHTML='';
    // Barangay selector applies to all sector filters that use barangay scoping
    if(keys.some(k=>['population','vulnerable','qr'].includes(k))){
        const activeColor=keys.length===1?SECTOR_CFG.find(x=>x.key===keys[0]).color:'#1B3F7A';
        const wrap=document.createElement('div'); wrap.className='pb-frow';
        const lbl=document.createElement('span'); lbl.className='pb-flbl'; lbl.textContent='Barangay:';
        const sel=document.createElement('select'); sel.className='pb-select'; sel.id='pbBrgySelect';
        sel.style.borderColor=activeColor;
        BRGYS.forEach(b=>{ const opt=document.createElement('option'); opt.value=b; opt.textContent=b==='All'?'All Barangays':b; if(b===PB.brgy) opt.selected=true; sel.appendChild(opt); });
        sel.addEventListener('change',()=>{ PB.brgy=sel.value; refresh(); });
        wrap.appendChild(lbl); wrap.appendChild(sel); container.appendChild(wrap);
    }

    if(keys.includes('population')){
        const rows=[];
        const row1=document.createElement('div'); addFilterRow(row1,'Sex:',makeSwitcherSingle(
            [['Male','Male','#1B3F7A'],['Female','Female','#DB2777']],
            PB.sexFilter, ()=>refresh()
        ));
        rows.push(row1);
        const row2=document.createElement('div'); addFilterRow(row2,'Age Group:',makeSwitcher(
            [['Children 0-12','Children','#2459A8'],['Teen 13-17','Teen','#2459A8'],['Adult 18-59','Adult','#2459A8'],['Senior 60+','Senior','#2459A8']],
            PB.ageFilter, ()=>refresh()
        ));
        rows.push(row2);
        const row3=document.createElement('div'); addFilterRow(row3,'Housing:',makeSwitcher(
            [['Single','Single','#2459A8'],['Duplex','Duplex','#2459A8'],['Apartment/Condominium','Apt/Condo','#2459A8'],['Townhouse','Townhouse','#2459A8'],['Improvised','Improvised','#2459A8'],['Mixed Use','Mixed Use','#2459A8'],['Other','Other','#2459A8']],
            PB.housingFilter, ()=>refresh()
        ));
        rows.push(row3);
        addFilterGroup(container,'Population Filters',rows);
    }

    if(keys.includes('vulnerable')){
        const rows=[];
        const row=document.createElement('div'); addFilterRow(row,'Type:',makeSwitcher(
            [['4Ps','4Ps','#1B3F7A'],['PWD','PWD','#7C3AED'],['Senior','Senior','#D97706'],['Solo Parent','Solo Parent','#DB2777']],
            PB.vulnFilter, ()=>refresh()
        ));
        rows.push(row);
        addFilterGroup(container,'Vulnerable Filters',rows);
    }

    if(keys.includes('distribution')){
        const rows=[];
        const row=document.createElement('div'); addFilterRow(row,'Status:',makeSwitcher(
            [['upcoming','Upcoming','#2459A8'],['ongoing','Ongoing','#16A34A'],['completed','Completed','#6B7280'],['cancelled','Cancelled','#C0392B']],
            PB.distFilter, ()=>refresh()
        ));
        rows.push(row);
        addFilterGroup(container,'Distribution Filters',rows);
    }

    if(keys.includes('qr')){
        const rows=[];
        const row=document.createElement('div'); addFilterRow(row,'QR Type:',makeSwitcherSingle(
            [['all','All QR','#1D9E75'],['household','Household','#1D9E75'],['family_head','Family Head','#1D9E75']],
            PB.qrType, ()=>{ refresh(); }
        ));
        rows.push(row);
        addFilterGroup(container,'QR Filters',rows);
    }

    if(!keys.every(k=>k==='distribution')){
        const ct=document.createElement('div'); ct.className='pb-ct';
        ct.innerHTML=`<button class="pb-ctb${PB.chartType==='bar'?' on':''}" id="pbCtBar" title="Bar"><svg viewBox="0 0 24 24"><rect x="3" y="12" width="4" height="9" fill="currentColor" style="stroke:none"/><rect x="10" y="7" width="4" height="14" fill="currentColor" style="stroke:none"/><rect x="17" y="3" width="4" height="18" fill="currentColor" style="stroke:none"/></svg></button>
        <button class="pb-ctb${PB.chartType==='doughnut'?' on':''}" id="pbCtPie" title="Donut"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4" style="fill:currentColor;stroke:none"/></svg></button>`;
        container.appendChild(ct);
        ct.querySelector('#pbCtBar').addEventListener('click',()=>{
            PB.chartType='bar';
            ct.querySelector('#pbCtBar').classList.add('on');
            ct.querySelector('#pbCtPie').classList.remove('on');
            refresh();
        });
        ct.querySelector('#pbCtPie').addEventListener('click',()=>{
            PB.chartType='doughnut';
            ct.querySelector('#pbCtPie').classList.add('on');
            ct.querySelector('#pbCtBar').classList.remove('on');
            refresh();
        });
    }
}

function renderCol(key){
    const el=document.getElementById('pbCol-'+key); if(!el) return;
    const cfg=SECTOR_CFG.find(x=>x.key===key);

    if(key==='population'){
        const h=filtH(PB.brgy); const hids=h.map(x=>x.id); const mAll=memOfH(hids);

        // --- Sex filter (Set-based, empty = All) ---
        const mSex = PB.sexFilter.size===0 ? mAll : mAll.filter(x=>PB.sexFilter.has(x.sex));

        // --- Age filter on top of sex filter ---
        const mAge = PB.ageFilter.size===0 ? mSex : mSex.filter(x=>PB.ageFilter.has(ageGroup(x.birthday)));

        // --- Sex chart: show only selected, or both if none selected ---
        const male=mAll.filter(x=>x.sex==='Male').length, female=mAll.filter(x=>x.sex==='Female').length;
        const sexAll={'Male':male,'Female':female};
        const sexD = PB.sexFilter.size===0 ? sexAll
            : Object.fromEntries(Object.entries(sexAll).filter(([k])=>PB.sexFilter.has(k)));

        // --- Age groups: always all 4, filtered by sexFilter only ---
        const AGE_GROUPS=['Children 0-12','Teen 13-17','Adult 18-59','Senior 60+'];
        const ageRaw=countBy(mSex,x=>ageGroup(x.birthday));
        const ageAllD={}; AGE_GROUPS.forEach(g=>{ ageAllD[g]=ageRaw[g]||0; });
        const ageD = PB.ageFilter.size===0 ? ageAllD
            : Object.fromEntries(Object.entries(ageAllD).filter(([k])=>PB.ageFilter.has(k)));

        // --- Housing: filtered by sexFilter + ageFilter ---
        const filteredHids=new Set(mAge.map(x=>x.household_id));
        const hFiltered=h.filter(x=>filteredHids.has(x.id));
        const HOUSING_TYPES=['Single','Duplex','Apartment/Condominium','Townhouse','Improvised','Mixed Use','Other'];
        const housingRaw=countBy(hFiltered,x=>x.housing?normalizeHousing(x.housing):null);
        const housingAllD={}; HOUSING_TYPES.forEach(t=>{ housingAllD[t]=housingRaw[t]||0; });
        Object.keys(housingRaw).forEach(k=>{ if(!(k in housingAllD)) housingAllD[k]=housingRaw[k]; });
        const housingD = PB.housingFilter.size===0 ? housingAllD
            : Object.fromEntries(Object.entries(housingAllD).filter(([k])=>PB.housingFilter.has(k)));

        el.innerHTML=
            `<div class="pb-col-sublabel">Population Sex</div>`
            +(PB.chartType==='bar'?barH2(sexD,'#1B3F7A','#DB2777'):`<div class="pb-chart-area"><canvas id="pbChart-pop-sex"></canvas></div>`)
            +`<div class="pb-col-sublabel" style="margin-top:14px">Age Groups</div>`
            +(PB.chartType==='bar'?barH(ageD,cfg.color):`<div class="pb-chart-area"><canvas id="pbChart-pop-age"></canvas></div>`)
            +`<div class="pb-col-sublabel" style="margin-top:14px">Housing Type</div>`
            +(PB.chartType==='bar'?barH(housingD,'#2459A8'):`<div class="pb-chart-area"><canvas id="pbChart-pop-house"></canvas></div>`);
        if(PB.chartType==='doughnut'){
            pbDetCharts['pop-sex']  =mkPie('pbChart-pop-sex',  Object.keys(sexD),  Object.values(sexD),  ['#1B3F7A','#DB2777']);
            pbDetCharts['pop-age']  =mkPie('pbChart-pop-age',  Object.keys(ageD),  Object.values(ageD),  ['#1B3F7A','#2459A8','#85B7EB','#EAF0FA','#CBD5E1']);
            pbDetCharts['pop-house']=mkPie('pbChart-pop-house',Object.keys(housingD),Object.values(housingD),['#2459A8','#1B3F7A','#85B7EB','#B5D4F4','#0C447C','#EAF0FA','#64748B']);
        }
    }
    else if(key==='vulnerable'){
        const h=filtH(PB.brgy);
        // Apply vulnFilter
        const vulnMap={'4Ps':x=>x.is_4ps,'PWD':x=>x.is_pwd,'Senior':x=>x.is_senior,'Solo Parent':x=>x.is_solo};
        const hFiltered = PB.vulnFilter.size===0 ? h
            : h.filter(x=>[...PB.vulnFilter].some(v=>vulnMap[v]&&vulnMap[v](x)));
        const hids=hFiltered.map(x=>x.id); const m=memOfH(hids); const det=detOfM(m.map(x=>x.id));
        // Always show all 4 vuln types from full h
        const vAllD={'4Ps':h.filter(x=>x.is_4ps).length,'PWD':h.filter(x=>x.is_pwd).length,'Senior':h.filter(x=>x.is_senior).length,'Solo Parent':h.filter(x=>x.is_solo).length};
        const vD = PB.vulnFilter.size===0 ? vAllD : Object.fromEntries(Object.entries(vAllD).filter(([k])=>PB.vulnFilter.has(k)));
        // Employment: always show known statuses
        const EMP_STATUSES=['Employed','Self-employed','Unemployed','Student','Retired','OFW'];
        const empRaw=countBy(det,x=>x.employment_status);
        const empD={}; EMP_STATUSES.forEach(s=>{ empD[s]=empRaw[s]||0; });
        Object.keys(empRaw).forEach(k=>{ if(k&&!(k in empD)) empD[k]=empRaw[k]; });
        el.innerHTML=`<div class="pb-col-sublabel">Vulnerable Households</div>${PB.chartType==='bar'?barH(vD,cfg.color):`<div class="pb-chart-area"><canvas id="pbChart-vuln-cat"></canvas></div>`}
        <div class="pb-col-sublabel" style="margin-top:14px">Employment Status${PB.vulnFilter.size>0?' ('+[...PB.vulnFilter].join(', ')+')':''}</div>${PB.chartType==='bar'?barH(empD,'#6D28D9'):`<div class="pb-chart-area"><canvas id="pbChart-vuln-emp"></canvas></div>`}`;
        if(PB.chartType==='doughnut'){ pbDetCharts['vuln-cat']=mkPie('pbChart-vuln-cat',Object.keys(vD),Object.values(vD),['#1B3F7A','#7C3AED','#D97706','#DB2777']); pbDetCharts['vuln-emp']=mkPie('pbChart-vuln-emp',Object.keys(empD),Object.values(empD),['#6D28D9','#7C3AED','#A78BFA','#C4B5FD','#3C3489','#D97706']); }
    }
    else if(key==='distribution'){
        const evsAll=filtE(PB.brgy);
        const evs = PB.distFilter.size===0 ? evsAll : evsAll.filter(e=>PB.distFilter.has(e.status));
        // Always show all statuses
        const STATUS_LIST=['upcoming','ongoing','completed','cancelled'];
        const byStRaw=countBy(evsAll,e=>e.status);
        const byStAll={}; STATUS_LIST.forEach(s=>{ byStAll[s]=byStRaw[s]||0; });
        const bySt = PB.distFilter.size===0 ? byStAll : Object.fromEntries(Object.entries(byStAll).filter(([k])=>PB.distFilter.has(k)));
        // Scan modes: always show both
        const scanRaw=countBy(evs,e=>e.scan_mode);
        const byScan={'household':scanRaw['household']||0,'family_head':scanRaw['family_head']||0};
        Object.keys(scanRaw).forEach(k=>{ if(!(k in byScan)) byScan[k]=scanRaw[k]; });
        el.innerHTML=`<div class="pb-col-sublabel">By Status</div>${barH(bySt,cfg.color,true)}<div class="pb-col-sublabel" style="margin-top:14px">Scan Mode${PB.distFilter.size>0?' ('+[...PB.distFilter].join(', ')+')':''}</div>${barH(byScan,'#EF9F27')}`;
    }
    else if(key==='qr'){
        const codes=filtQR(PB.brgy,getQrType());
        const byBrgy=countBy(codes,q=>{ const hh=HOUSEHOLDS.find(x=>x.id===q.household_id); return hh?hh.barangay:'Unknown'; });
        // Always show Active and Inactive even if 0
        const stD={'Active':codes.filter(x=>x.is_active).length,'Inactive':codes.filter(x=>!x.is_active).length};
        el.innerHTML=`<div class="pb-col-sublabel">QR Status</div>${PB.chartType==='bar'?barH(stD,cfg.color):`<div class="pb-chart-area"><canvas id="pbChart-qr-st"></canvas></div>`}
        <div class="pb-col-sublabel" style="margin-top:14px">QR per Barangay</div>${barH(byBrgy,'#0F6E56')}`;
        if(PB.chartType==='doughnut'){ pbDetCharts['qr-st']=mkPie('pbChart-qr-st',Object.keys(stD),Object.values(stD),['#1D9E75','#C0392B']); }
    }
}

function buildCross(keys){
    const h=filtH(PB.brgy); const hids=h.map(x=>x.id); const m=memOfH(hids); const evs=filtE(PB.brgy); const codes=filtQR(PB.brgy,getQrType());
    const cards=[];
    if(keys.includes('population')&&keys.includes('vulnerable')){
        const vulnHH=h.filter(x=>x.is_4ps||x.is_pwd||x.is_senior||x.is_solo);
        const vulnM=memOfH(vulnHH.map(x=>x.id));
        cards.push({v:vulnHH.length,l:'Vulnerable households',s:'of '+h.length+' total',c:'#7C3AED'});
        cards.push({v:vulnM.length,l:'Residents in vuln. HH',s:'family members',c:'#7C3AED'});
        cards.push({v:Math.round(vulnHH.length/Math.max(h.length,1)*100)+'%',l:'Vulnerability rate',s:'of all households',c:'#6D28D9'});
    }
    if(keys.includes('vulnerable')&&keys.includes('qr')){
        const vulnHH=h.filter(x=>x.is_4ps||x.is_pwd||x.is_senior||x.is_solo); const vhids=vulnHH.map(x=>x.id);
        const withQR=codes.filter(q=>vhids.includes(q.household_id)&&q.is_active).length;
        cards.push({v:withQR,l:'Vulnerable HH with active QR',s:'can receive aid',c:'#1D9E75'});
        cards.push({v:vulnHH.length-withQR,l:'Vulnerable HH without QR',s:'may miss distribution',c:'#C0392B'});
        cards.push({v:Math.round(withQR/Math.max(vulnHH.length,1)*100)+'%',l:'QR coverage',s:'of vulnerable HH',c:'#7C3AED'});
    }
    if(keys.includes('distribution')&&keys.includes('qr')){
        cards.push({v:codes.filter(x=>x.is_active).length,l:'Active QR for events',s:'can be scanned',c:'#1D9E75'});
        cards.push({v:evs.filter(e=>e.scan_mode==='household').length,l:'Household-scan events',s:'scan_mode = household',c:'#BA7517'});
        cards.push({v:evs.filter(e=>e.scan_mode==='family_head').length,l:'Family head-scan events',s:'scan_mode = family_head',c:'#2459A8'});
    }
    if(keys.includes('population')&&keys.includes('distribution')){
        cards.push({v:evs.length,l:'Events in scope',s:PB.brgy==='All'?'all barangays':PB.brgy,c:'#BA7517'});
        cards.push({v:m.length,l:'Residents eligible',s:'in target barangay',c:'#1B3F7A'});
        cards.push({v:h.filter(x=>x.approved).length,l:'Approved households',s:'ready to receive aid',c:'#16A34A'});
    }
    if(cards.length===0) return;
    const div=document.createElement('div'); div.id='pbCross'; div.className='pb-cross';
    div.innerHTML=`<div class="pb-cross-title">Cross-sector insight — ${PB.brgy==='All'?'all barangays':PB.brgy}</div><div class="pb-cross-grid">${cards.map(c=>`<div class="pb-cc"><div class="pb-cc-val" style="color:${c.c}">${typeof c.v==='number'?c.v.toLocaleString():c.v}</div><div class="pb-cc-lbl">${c.l}</div><div class="pb-cc-sub">${c.s}</div></div>`).join('')}</div>`;
    document.getElementById('pbEz').appendChild(div);
}

function initPbMap(){
    if(pbLeafMap){ pbLeafMap.remove(); pbLeafMap=null; }
    const el=document.getElementById('pbMap'); if(!el) return;
    const evs=filtE(PB.brgy).filter(e=>e.lat&&e.lng);
    pbLeafMap=L.map('pbMap',{zoomControl:true}).setView([14.3294,120.7614],13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{attribution:'© OpenStreetMap © CARTO',subdomains:'abcd',maxZoom:19}).addTo(pbLeafMap);
    evs.forEach(ev=>{
        const c=STATUS_COLORS[ev.status]||'#888';
        const icon=L.divIcon({className:'',html:`<div style="width:13px;height:13px;border-radius:50%;background:${c};border:2.5px solid #fff;box-shadow:0 1px 5px rgba(0,0,0,.3)"></div>`,iconSize:[13,13],iconAnchor:[6,6]});
        L.marker([ev.lat,ev.lng],{icon}).addTo(pbLeafMap).bindPopup(`<div style="font-family:'Open Sans',sans-serif;font-size:12px;min-width:175px"><div style="font-weight:700;margin-bottom:4px;color:#1B3F7A">${ev.name}</div><div style="color:#5A6372;margin-bottom:2px">📅 ${ev.date}</div><div style="color:#5A6372;margin-bottom:5px">Mode: ${ev.scan_mode}</div><span style="padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;background:${c}22;color:${c};border:1px solid ${c}55">${ev.status}</span></div>`,{maxWidth:220});
    });
    if(evs.length>1) pbLeafMap.fitBounds(L.latLngBounds(evs.map(e=>[e.lat,e.lng])),{padding:[30,30]});
    setTimeout(()=>pbLeafMap&&pbLeafMap.invalidateSize(),150);
}

/* ── Helpers ── */
function barHFocus(data,color,activeSet){
    // activeSet is a Set; empty = show all, otherwise show only selected keys
    const filtered = (!activeSet||activeSet.size===0) ? data
        : Object.fromEntries(Object.entries(data).filter(([k])=>activeSet.has(k)));
    const vals=Object.values(filtered); const max=Math.max(...vals,1);
    return `<div class="pb-bar-list">${Object.entries(filtered).map(([n,v])=>
        `<div class="pb-br"><div class="pb-bn" title="${n}">${n}</div><div class="pb-bt"><div class="pb-bf" style="width:${Math.round(v/max*100)}%;background:${color}"></div></div><div class="pb-bv">${v.toLocaleString()}</div></div>`
    ).join('')}</div>`;
}
function barH(data,color,statusColor=false){
    const vals=Object.values(data); const max=Math.max(...vals,1);
    return `<div class="pb-bar-list">${Object.entries(data).map(([n,v])=>`<div class="pb-br"><div class="pb-bn" title="${n}">${n}</div><div class="pb-bt"><div class="pb-bf" style="width:${Math.round(v/max*100)}%;background:${statusColor?(STATUS_COLORS[n]||color):color}"></div></div><div class="pb-bv">${v.toLocaleString()}</div></div>`).join('')}</div>`;
}
function barH2(data,colorM,colorF){
    const vals=Object.values(data); const max=Math.max(...vals,1);
    const colors={'Male':colorM,'Female':colorF};
    return `<div class="pb-bar-list">${Object.entries(data).map(([n,v])=>`<div class="pb-br"><div class="pb-bn" title="${n}" style="color:${colors[n]||colorM};font-weight:700;">● ${n}</div><div class="pb-bt"><div class="pb-bf" style="width:${Math.round(v/max*100)}%;background:${colors[n]||colorM}"></div></div><div class="pb-bv" style="color:${colors[n]||colorM}">${v.toLocaleString()}</div></div>`).join('')}</div>`;
}
function mkPie(id,labels,data,colors){
    const el=document.getElementById(id); if(!el) return null;
    return new Chart(el,{type:'doughnut',data:{labels,datasets:[{data,backgroundColor:colors,borderWidth:0,hoverOffset:6}]},options:{responsive:true,maintainAspectRatio:false,cutout:'60%',animation:{animateScale:true,duration:400},plugins:{legend:{display:true,position:'bottom',labels:{boxWidth:8,font:{size:10,family:'Open Sans'}}},tooltip:{callbacks:{label:ctx=>` ${ctx.label}: ${ctx.parsed.toLocaleString()}`}}}}});
}

function refresh(){
    updateDonut(); updateCards(); updateExplore();
    document.querySelectorAll('.pb-sbtn').forEach(btn=>{
        const k=btn.dataset.key; const cfg=SECTOR_CFG.find(x=>x.key===k); const on=PB.active.has(k);
        btn.classList.toggle('on',on); btn.style.background=on?cfg.color:''; btn.style.borderColor=on?'transparent':''; btn.style.color=on?'#fff':'';
    });
    /* Show/hide the distribution map section */
    const mapSection=document.getElementById('distMapSection');
    if(mapSection){
        const distActive=PB.active.has('distribution');
        mapSection.style.display=distActive?'':'none';
        if(distActive){ setTimeout(()=>{ initDashMap(); if(dmMap) dmMap.invalidateSize(true); },80); }
    }
}

/* ── Init sector buttons ── */
function initDashboard(){
    document.querySelectorAll('.pb-sbtn').forEach(btn=>{
        btn.addEventListener('click',()=>{
            const k=btn.dataset.key;
            if(PB.active.has(k)){
                PB.active.delete(k);
            } else {
                PB.active.add(k);
                // Reset filters for the newly activated sector so they start fresh
                // IMPORTANT: clear() mutates in place so existing closure references stay valid
                if(k==='population')   { PB.sexFilter.clear(); PB.ageFilter.clear(); PB.housingFilter.clear(); }
                if(k==='vulnerable')   { PB.vulnFilter.clear(); }
                if(k==='distribution') { PB.distFilter.clear(); }
                if(k==='qr')           { PB.qrType.clear(); }
            }
            PB._lastKeys=''; // force controls rebuild on next render
            refresh();
        });
    });
    refresh();
}
if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded', initDashboard);
} else {
    initDashboard();
}

/* ── Init main donut ── */
pbMainChart=new Chart(document.getElementById('pbMainDonut'),{
    type:'doughnut',
    data:{labels:['Population','Vulnerable','Distribution','QR'],datasets:[{data:[MEMBERS.length,HOUSEHOLDS.filter(h=>h.is_4ps||h.is_pwd||h.is_senior||h.is_solo).length,EVENTS.length,QR_CODES.length],backgroundColor:['#1B3F7A','#7C3AED','#BA7517','#1D9E75'],borderWidth:0,hoverOffset:8}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'65%',animation:{animateScale:true,duration:500},plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>` ${ctx.label}: ${ctx.parsed.toLocaleString()}`}}}}
});
(()=>{
    const tot=MEMBERS.length+HOUSEHOLDS.filter(h=>h.is_4ps||h.is_pwd||h.is_senior||h.is_solo).length+EVENTS.length+QR_CODES.length;
    document.getElementById('pbMainLeg').innerHTML=[['Population',MEMBERS.length,'#1B3F7A'],['Vulnerable',HOUSEHOLDS.filter(h=>h.is_4ps||h.is_pwd||h.is_senior||h.is_solo).length,'#7C3AED'],['Distribution',EVENTS.length,'#BA7517'],['QR',QR_CODES.length,'#1D9E75']].map(([l,v,c])=>`<span class="pb-li"><span class="pb-lsq" style="background:${c}"></span>${l} ${Math.round(v/tot*100)}%</span>`).join('');
})();

/* ══════════════════════════════════════════════════════════
   EXISTING DISTRIBUTION MAP
══════════════════════════════════════════════════════════ */
const DM_EVENTS = @json($dmEventsJson);
const DM_COLORS = { upcoming:'#2459A8', ongoing:'#16A34A', completed:'#6B7280', cancelled:'#C0392B' };
const DM_LABELS = { upcoming:'Upcoming', ongoing:'Ongoing', completed:'Completed', cancelled:'Cancelled' };
const DM_ICONS  = { upcoming:'📅', ongoing:'🟢', completed:'✅', cancelled:'❌' };

function dmPinIcon(status){
    const c = DM_COLORS[status] || '#2459A8';
    return L.divIcon({
        className:'',
        html:`<svg width="28" height="38" viewBox="0 0 28 38" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 0C6.268 0 0 6.268 0 14c0 9.333 14 24 14 24S28 23.333 28 14C28 6.268 21.732 0 14 0z"
                fill="${c}" stroke="rgba(255,255,255,0.85)" stroke-width="2"/>
            <circle cx="14" cy="11" r="5" fill="white" opacity="0.9"/>
        </svg>`,
        iconSize:[28,38], iconAnchor:[14,38], popupAnchor:[0,-40],
    });
}

function dmPopupHTML(ev){
    const c = DM_COLORS[ev.status] || '#2459A8';
    return `<div class="dm-popup-inner">
        <div class="dm-popup-head" style="border-top:3px solid ${c}">
            <span class="dm-sbadge ${ev.status}">${DM_ICONS[ev.status]} ${DM_LABELS[ev.status]}</span>
            <span class="dm-popup-name">${ev.name}</span>
        </div>
        <div class="dm-popup-body">
            <div class="dm-popup-row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg><span>${ev.type||'—'}</span></div>
            <div class="dm-popup-row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg><span>${ev.barangay}</span></div>
            <div class="dm-popup-row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span>${ev.date}</span></div>
            ${ev.loc?`<div class="dm-popup-row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg><span style="font-size:10px;color:#9AA3B0">${ev.loc}</span></div>`:''}
            <a class="dm-popup-link" href="{{ route('admin.distribution.logs') }}#event-${ev.id}">View Distribution Log →</a>
        </div>
    </div>`;
}

const NAIC=[14.3294,120.7614]; let dmMap=null; let dmMarkers=[]; let dmCurrentFilter='all';
function initDashMap(){
    if(dmMap) return;
    dmMap=L.map('dashDistMap').setView(NAIC,13);
    const baseLayers={
        "🛰️ Satellite":L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{attribution:'© Esri',maxZoom:19}),
        "🗺️ Street Map":L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{attribution:'© OpenStreetMap © CARTO',subdomains:'abcd',maxZoom:19}),
        "🏙️ Street (Detailed)":L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap contributors',maxZoom:19}),
    };
    baseLayers["🛰️ Satellite"].addTo(dmMap);
    L.control.layers(baseLayers,null,{position:'topright',collapsed:false}).addTo(dmMap);
    DM_EVENTS.forEach(ev=>{
        if(!ev.lat||!ev.lng) return;
        const marker=L.marker([ev.lat,ev.lng],{icon:dmPinIcon(ev.status)}).addTo(dmMap).bindPopup(dmPopupHTML(ev),{className:'dm-popup',maxWidth:260,minWidth:215});
        dmMarkers.push({marker,status:ev.status,date_raw:ev.date_raw||null});
    });
    if(dmMarkers.length===1) dmMap.setView(dmMarkers[0].marker.getLatLng(),15);
    else if(dmMarkers.length>1) dmMap.fitBounds(L.latLngBounds(dmMarkers.map(m=>m.marker.getLatLng())),{padding:[40,40]});
    dmUpdateNotice(); setTimeout(()=>dmMap.invalidateSize(true),100); setTimeout(()=>dmMap.invalidateSize(true),500);
}
window.dmFilter=function(status,btn){
    dmCurrentFilter=status;
    document.querySelectorAll('.dm-filter-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active');
    const now30=new Date(); now30.setDate(now30.getDate()-30);
    dmMarkers.forEach(({marker,status:ms,date_raw})=>{
        let show=status==='all'?true:status==='recent'?(date_raw&&new Date(date_raw)>=now30):(ms===status);
        if(show&&!dmMap.hasLayer(marker)) marker.addTo(dmMap);
        if(!show&&dmMap.hasLayer(marker)) dmMap.removeLayer(marker);
    });
    dmUpdateNotice();
    const visible=dmMarkers.filter(({status:ms,date_raw})=>status==='all'||(status==='recent'&&date_raw&&new Date(date_raw)>=new Date(new Date().setDate(new Date().getDate()-30)))||ms===status).map(({marker})=>marker.getLatLng());
    if(visible.length===1) dmMap.setView(visible[0],15);
    else if(visible.length>1) dmMap.fitBounds(L.latLngBounds(visible),{padding:[40,40]});
};
function dmUpdateNotice(){
    const now30=new Date(); now30.setDate(now30.getDate()-30);
    const count=dmMarkers.filter(({status,date_raw})=>dmCurrentFilter==='all'?true:dmCurrentFilter==='recent'?(date_raw&&new Date(date_raw)>=now30):(status===dmCurrentFilter)).length;
    document.getElementById('dmNoPin').classList.toggle('show',count===0);
}
if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',initDashMap);
else initDashMap();


</script>
</body>
</html>