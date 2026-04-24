<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — List of Residents</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue:        #1B3F7A;
            --blue-dark:   #122D5A;
            --blue-light:  #2459A8;
            --blue-pale:   #EAF0FA;
            --yellow:      #F5C518;
            --yellow-dark: #D4A800;
            --green:       #16A34A;
            --green-pale:  #DCFCE7;
            --green-dark:  #15803D;
            --orange:      #C2410C;
            --orange-pale: #FFF7ED;
            --purple:      #7C3AED;
            --purple-pale: #F5F3FF;
            --white:       #FFFFFF;
            --gray-50:     #F7F8FA;
            --gray-100:    #F0F2F5;
            --gray-200:    #DEE2E8;
            --gray-400:    #9AA3B0;
            --gray-600:    #5A6372;
            --gray-800:    #2C3340;
            --red:         #C0392B;
            --red-pale:    #FEF2F2;
            --cyan:         #0891B2;
            --cyan-pale:   #ECFEFF;
            --sidebar-w:   256px;
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
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
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

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 250; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; opacity: 1; }

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
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 24px 28px; }

        /* ─── PAGE TITLEBAR ─── */
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--gray-400); text-decoration: none; }
        .page-breadcrumb a:hover { color: var(--blue-light); }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date span { display: block; font-size: 12px; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── SUMMARY STATS ─── */
        .stats-row { display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px; margin-bottom: 20px; }
        .stat-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 14px 16px; border-radius: 2px; }
        .stat-card.yellow  { border-top-color: var(--yellow); }
        .stat-card.green   { border-top-color: var(--green); }
        .stat-card.red     { border-top-color: var(--red); }
        .stat-card.orange  { border-top-color: var(--orange); }
        .stat-card.purple  { border-top-color: var(--purple); }
        .stat-card.cyan    { border-top-color: var(--cyan); }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 6px; }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .stat-card.yellow .stat-value  { color: var(--yellow-dark); }
        .stat-card.green  .stat-value  { color: var(--green); }
        .stat-card.red    .stat-value  { color: var(--red); }
        .stat-card.orange .stat-value  { color: var(--orange); }
        .stat-card.purple .stat-value  { color: var(--purple); }
        .stat-card.cyan   .stat-value  { color: var(--cyan); }
        .stat-card.active-filter { box-shadow: 0 0 0 2px var(--blue); cursor: pointer; }
        .stat-meta { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        /* ─── FILTERS BAR ─── */
        .filters-bar { background: var(--white); border: 1px solid var(--gray-200); padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 0; border-bottom: none; }
        .filter-group { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); white-space: nowrap; }
        .filter-input, .filter-select { border: 1px solid var(--gray-200); background: var(--gray-50); padding: 7px 10px; font-size: 12px; color: var(--gray-800); font-family: 'Open Sans', sans-serif; outline: none; border-radius: 3px; transition: border-color 0.15s; }
        .filter-select { padding-right: 28px; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center; }
        .filter-input:focus, .filter-select:focus { border-color: var(--blue); background: var(--white); }
        .filter-spacer { flex: 1; min-width: 8px; }
        .filter-count { font-size: 12px; color: var(--gray-600); white-space: nowrap; }
        .filter-count strong { color: var(--blue-dark); }

        /* Search scope combo */
        .search-combo { display: flex; align-items: stretch; border: 1px solid var(--gray-200); border-radius: 3px; overflow: hidden; background: var(--gray-50); transition: border-color 0.15s; }
        .search-combo:focus-within { border-color: var(--blue); background: var(--white); }
        .search-combo .scope-select { border: none; border-right: 1px solid var(--gray-200); background: var(--gray-100); padding: 7px 22px 7px 8px; font-size: 11px; font-weight: 700; color: var(--gray-600); font-family: 'Open Sans', sans-serif; outline: none; appearance: none; cursor: pointer; flex-shrink: 0; min-width: 86px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 5px center; }
        .search-combo .scope-select:hover { background-color: var(--blue-pale); color: var(--blue); }
        .search-combo .search-input { border: none; background: transparent; padding: 7px 10px; font-size: 12px; color: var(--gray-800); font-family: 'Open Sans', sans-serif; outline: none; width: 190px; }
        .search-combo .clear-btn { display: none; align-items: center; justify-content: center; padding: 0 8px; border: none; background: transparent; color: var(--gray-400); cursor: pointer; font-size: 16px; line-height: 1; }
        .search-combo .clear-btn:hover { color: var(--red); }
        .search-combo.has-value .clear-btn { display: flex; }

        /* Highlight matched text */
        mark.hl { background: #FFF176; color: inherit; border-radius: 2px; padding: 0 1px; font-style: normal; }

        /* Mobile filter toggle */
        .filter-toggle-btn { display: none; align-items: center; gap: 6px; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 3px; padding: 7px 12px; font-size: 12px; font-weight: 600; color: var(--gray-600); cursor: pointer; font-family: 'Open Sans', sans-serif; white-space: nowrap; transition: background 0.15s; flex-shrink: 0; }
        .filter-toggle-btn:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }
        .filter-toggle-btn .ftb-count { background: var(--blue); color: #fff; border-radius: 10px; font-size: 10px; padding: 1px 6px; margin-left: 2px; display: none; }
        .filter-toggle-btn .ftb-count.show { display: inline; }
        .filters-collapsible { display: contents; }

        /* Active filter tags */
        .active-filters { background: var(--white); border: 1px solid var(--gray-200); border-top: none; padding: 8px 16px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 0; }
        .active-filters.hidden { display: none; }
        .filter-tag { display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; background: var(--blue-pale); border: 1px solid #C7D9F5; border-radius: 10px; font-size: 11px; color: var(--blue); font-weight: 600; }
        .filter-tag a { color: var(--blue); text-decoration: none; margin-left: 2px; opacity: 0.6; font-weight: 700; }
        .filter-tag a:hover { opacity: 1; }
        .clear-all { font-size: 11px; color: var(--red); text-decoration: none; font-weight: 600; margin-left: 4px; }
        .clear-all:hover { text-decoration: underline; }

        /* ─── TABLE ─── */
        .table-wrap { background: var(--white); border: 1px solid var(--gray-200); border-top: none; overflow-x: hidden; margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); background: var(--gray-50); border-bottom: 2px solid var(--gray-200); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .th-sortable { cursor: pointer; user-select: none; }
        .th-sortable:hover { color: var(--blue); }
        .th-sortable.sorted { color: var(--blue); }
        .sort-icon { display: inline-block; margin-left: 3px; opacity: 0.4; font-size: 9px; }
        .th-sortable.sorted .sort-icon { opacity: 1; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; cursor: pointer; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 9px 14px; font-size: 12.5px; color: var(--gray-800); vertical-align: middle; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        tbody td.td-tags { white-space: normal; }
        .td-name { font-weight: 600; color: var(--blue-dark); font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .td-sub  { font-size: 11px; color: var(--gray-400); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .td-num  { font-variant-numeric: tabular-nums; color: var(--gray-400); font-size: 12px; }
        .td-muted { color: var(--gray-400); font-style: italic; font-size: 11px; }
        .td-serial { font-family: 'Courier New', monospace; font-size: 11px; color: var(--blue); background: var(--blue-pale); padding: 2px 6px; border-radius: 3px; white-space: nowrap; }

        /* ─── MOBILE CARD LIST ─── */
        .resident-cards { display: none; }
        .resident-card { background: var(--white); border-bottom: 2px solid var(--gray-200); padding: 14px 16px; }
        .resident-card:last-child { border-bottom: none; }
        .card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .card-name-block { flex: 1; min-width: 0; }
        .card-name { font-size: 14px; font-weight: 700; color: var(--blue-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .card-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .card-badges { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px; }
        .card-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 12px; margin-top: 10px; }
        .card-meta-item { min-width: 0; }
        .card-meta-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); margin-bottom: 2px; }
        .card-meta-value { font-size: 12px; color: var(--gray-800); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; gap: 8px; }
        .card-serial { font-family: 'Courier New', monospace; font-size: 11px; color: var(--blue); background: var(--blue-pale); padding: 3px 8px; border-radius: 3px; }
        .card-detail-btn { display: flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--blue-pale); border: 1px solid #C7D9F5; border-radius: 4px; font-size: 11px; font-weight: 700; color: var(--blue); cursor: pointer; font-family: 'Open Sans', sans-serif; transition: background 0.15s; white-space: nowrap; }
        .card-detail-btn:hover { background: var(--blue); color: var(--white); }
        .card-detail-btn svg { width: 13px; height: 13px; flex-shrink: 0; }

        /* ─── DETAIL DRAWER (BOTTOM SHEET) ─── */
        .drawer-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 500; opacity: 0; transition: opacity 0.25s; }
        .drawer-overlay.active { display: block; opacity: 1; }
        .drawer { position: fixed; bottom: 0; left: 0; right: 0; background: var(--white); border-radius: 16px 16px 0 0; z-index: 501; transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); max-height: 85vh; overflow-y: auto; }
        .drawer.open { transform: translateY(0); }
        .drawer-handle { width: 40px; height: 4px; background: var(--gray-200); border-radius: 2px; margin: 12px auto 0; }
        .drawer-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 14px 20px 12px; border-bottom: 1px solid var(--gray-100); gap: 12px; }
        .drawer-title { font-size: 15px; font-weight: 700; color: var(--blue-dark); line-height: 1.3; flex: 1; }
        .drawer-close { width: 30px; height: 30px; border-radius: 50%; background: var(--gray-100); border: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--gray-600); flex-shrink: 0; transition: background 0.15s; }
        .drawer-close:hover { background: var(--red-pale); color: var(--red); }
        .drawer-close svg { width: 14px; height: 14px; }
        .drawer-body { padding: 16px 20px 32px; }
        .drawer-section { margin-bottom: 18px; }
        .drawer-section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid var(--gray-100); }
        .drawer-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .drawer-field { }
        .drawer-field-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-400); margin-bottom: 3px; }
        .drawer-field-value { font-size: 13px; color: var(--gray-800); }
        .drawer-field-value.muted { color: var(--gray-400); font-style: italic; font-size: 12px; }
        .drawer-tags { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 6px; }
        .drawer-view-btn { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 12px; background: var(--blue); color: var(--white); border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: 'Open Sans', sans-serif; margin-top: 20px; transition: background 0.15s; text-decoration: none; }
        .drawer-view-btn:hover { background: var(--blue-dark); }

        /* Badges */
        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .badge-head    { background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F5; }
        .badge-member  { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
        .badge-male    { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
        .badge-female  { background: #FDF2F8; color: #9D174D; border: 1px solid #FBCFE8; }
        .badge-pwd     { background: var(--orange-pale); color: var(--orange); border: 1px solid #FED7AA; }
        .badge-senior  { background: #FFFAE6; color: var(--yellow-dark); border: 1px solid #FDE68A; }
        .badge-4ps     { background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; }
        .badge-solo    { background: var(--purple-pale); color: var(--purple); border: 1px solid #DDD6FE; }
        .badge-lgbtqia { background: #FDF4FF; color: #7E22CE; border: 1px solid #E9D5FF; }
        .badge-student { background: var(--cyan-pale); color: var(--cyan); border: 1px solid #A5F3FC; }

        /* ─── EMPTY STATE ─── */
        .empty-state { padding: 56px 24px; text-align: center; }
        .empty-icon { width: 52px; height: 52px; border-radius: 50%; background: var(--gray-100); border: 2px solid var(--gray-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon svg { width: 24px; height: 24px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── PAGINATION ─── */
        .pagination { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: var(--white); border: 1px solid var(--gray-200); border-top: 1px solid var(--gray-100); gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
        .pagination-info { font-size: 12px; color: var(--gray-600); }
        .pagination-info strong { color: var(--gray-800); }
        .pagination-btns { display: flex; align-items: center; gap: 4px; }
        .pg-btn { min-width: 32px; height: 32px; border-radius: 3px; border: 1px solid var(--gray-200); background: var(--white); font-size: 12px; font-weight: 600; color: var(--gray-600); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0 8px; transition: background 0.12s, color 0.12s, border-color 0.12s; font-family: 'Open Sans', sans-serif; }
        .pg-btn:hover:not(:disabled) { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }
        .pg-btn.active { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .pg-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* ─── FOOTER ─── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.45); }
        .footer-left strong { color: rgba(255,255,255,0.75); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.25); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; height: 100vh; overflow: hidden; }
            .sidebar { grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 1200; transform: translateX(-100%); transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block !important; z-index: 1100; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }
            .hamburger { display: flex; }
            .filter-toggle-btn { display: flex; }
            .filters-collapsible { display: none; width: 100%; }
            .filters-collapsible.open { display: flex; flex-direction: column; align-items: flex-start; }
            .filters-bar { flex-wrap: wrap; gap: 8px; }
            .filter-group { width: 100%; }
            .filter-group .filter-select { flex: 1; }
            .search-combo { width: 100%; }
            .search-combo .search-input { flex: 1; width: auto; min-width: 0; }
            /* Switch to card view — hide desktop table, show card list */
            .table-wrap table { display: none; }
            .resident-cards { display: block; }
            header { padding: 0 16px; gap: 10px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .header-admin-badge { padding: 6px 10px; gap: 8px; }
            .admin-name { font-size: 12px; }
            .admin-role { display: none; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .stats-row { grid-template-columns: repeat(4, 1fr); }
            .main-content { padding: 20px 16px; }
        }
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline, .status-indicator { display: none; }
            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider, .header-logos img:last-child, .header-org { display: none; }
            .header-title { font-size: 13px; line-height: 1.3; }
            .header-admin-badge { padding: 5px 8px; }
            .admin-avatar { width: 28px; height: 28px; font-size: 11px; }
            .admin-name { font-size: 11px; }
            .admin-role { display: none; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .filters-bar { gap: 8px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }
            .main-content { padding: 16px 12px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            .card-meta { grid-template-columns: 1fr 1fr; }
            .drawer-grid { grid-template-columns: 1fr 1fr; }
            .pagination { flex-direction: column; align-items: flex-start; gap: 8px; }
        }
        @media (max-width: 480px) {
            .shell { grid-template-rows: 28px 52px 1fr 40px; }
            .main-content { padding: 10px 8px; }
            .topbar { padding: 0 10px; }
            header { padding: 0 8px; }
            .header-title { font-size: 13px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- TOP BAR -->
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

        <a href="{{ route('admin.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard Overview
        </a>

        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
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

        <a href="{{ route('admin.residents.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            List of Residents
        </a>

        <a href="{{ route('admin.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Households
        </a>

        <a href="{{ route('admin.traillog.trail') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Trail Logs
        </a>

        <a href="{{ route('admin.distribution.scan-history') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                <rect x="8" y="8" width="8" height="8" rx="1"/>
            </svg>
            Staff Scan History
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
                    <a href="{{ route('admin.dashboard') }}">Home</a> / <span>List of Residents</span>
                </div>
                <div class="page-h1">List of Residents</div>
                <div class="page-sub">All registered household heads and family members across all barangays.</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        {{-- SUMMARY STATS (6 cards) --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Residents</div>
                <div class="stat-value">{{ number_format($totalResidents) }}</div>
                <div class="stat-meta">heads + members</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Household Heads</div>
                <div class="stat-value">{{ number_format($totalHeads) }}</div>
                <div class="stat-meta">registered households</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-label">4Ps Beneficiaries</div>
                <div class="stat-value">{{ number_format($total4Ps) }}</div>
                <div class="stat-meta">households enrolled</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Senior Citizens</div>
                <div class="stat-value">{{ number_format($totalSeniors) }}</div>
                <div class="stat-meta">aged 60 and above</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">PWD</div>
                <div class="stat-value">{{ number_format($totalPwd) }}</div>
                <div class="stat-meta">persons w/ disability</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Solo Parents</div>
                <div class="stat-value">{{ number_format($totalSoloParents ?? 0) }}</div>
                <div class="stat-meta">registered solo parents</div>
            </div>
            <a href="{{ route('admin.residents.index', array_merge(request()->query(), ['tag' => 'student'])) }}"
               class="stat-card cyan {{ request('tag') === 'student' ? 'active-filter' : '' }}"
               style="text-decoration:none;display:block;">
                <div class="stat-label">Students</div>
                <div class="stat-value">{{ number_format($totalStudents ?? 0) }}</div>
                <div class="stat-meta">enrolled / in school</div>
            </a>
        </div>

        {{-- FILTERS --}}
        <form method="GET" action="{{ route('admin.residents.index') }}" id="filterForm">
            {{-- Hidden fields carried through --}}
            <input type="hidden" name="scope" id="scopeHidden" value="{{ request('scope', 'all') }}">

            <div class="filters-bar">

                {{-- Mobile toggle --}}
                <button type="button" class="filter-toggle-btn" onclick="toggleFilters()" id="filterToggleBtn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filters
                    <span class="ftb-count" id="filterBadge"></span>
                </button>

                {{-- Search scope combo --}}
                <div class="filter-group">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2" style="flex-shrink:0">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <div class="search-combo" id="searchCombo">
                        <select class="scope-select" id="scopeSelect" onchange="updateScope(this.value)" title="Search field">
                            <option value="all"          {{ request('scope','all') === 'all'          ? 'selected' : '' }}>All Fields</option>
                            <option value="name"         {{ request('scope') === 'name'         ? 'selected' : '' }}>Name</option>
                            <option value="barangay"     {{ request('scope') === 'barangay'     ? 'selected' : '' }}>Barangay</option>
                            <option value="serial"       {{ request('scope') === 'serial'       ? 'selected' : '' }}>Serial Code</option>
                            <option value="age"          {{ request('scope') === 'age'          ? 'selected' : '' }}>Age</option>
                            <option value="civil_status" {{ request('scope') === 'civil_status' ? 'selected' : '' }}>Civil Status</option>
                            <option value="employment"   {{ request('scope') === 'employment'   ? 'selected' : '' }}>Employment</option>
                            <option value="relationship" {{ request('scope') === 'relationship' ? 'selected' : '' }}>Relationship</option>
                            <option value="student"     {{ request('scope') === 'student'     ? 'selected' : '' }}>Student</option>
                        </select>
                        <input type="text" name="search" class="search-input" id="searchInput"
                            placeholder="Search residents..."
                            value="{{ request('search') }}"
                            oninput="onSearchInput(this)">
                        <button type="button" class="clear-btn" onclick="clearSearch()" title="Clear search">×</button>
                    </div>
                </div>

                {{-- Collapsible filter groups --}}
                <div class="filters-collapsible" id="filtersCollapsible">
                    <div class="filter-group">
                        <span class="filter-label">Type</span>
                        <select name="type" class="filter-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="head"   {{ request('type') === 'head'   ? 'selected' : '' }}>Heads Only</option>
                            <option value="member" {{ request('type') === 'member' ? 'selected' : '' }}>Members Only</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Sex</span>
                        <select name="sex" class="filter-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="Male"   {{ request('sex') === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ request('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Barangay</span>
                        <select name="barangay" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy }}" {{ request('barangay') === $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Tag</span>
                        <select name="tag" class="filter-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="4ps"     {{ request('tag') === '4ps'     ? 'selected' : '' }}>4Ps</option>
                            <option value="pwd"     {{ request('tag') === 'pwd'     ? 'selected' : '' }}>PWD</option>
                            <option value="senior"  {{ request('tag') === 'senior'  ? 'selected' : '' }}>Senior Citizen</option>
                            <option value="solo"    {{ request('tag') === 'solo'    ? 'selected' : '' }}>Solo Parent</option>
                            <option value="student" {{ request('tag') === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="lgbtqia" {{ request('tag') === 'lgbtqia' ? 'selected' : '' }}>LGBTQIA+</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <span class="filter-label">Age</span>
                        <select name="age_group" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Ages</option>
                            <option value="child"  {{ request('age_group') === 'child'  ? 'selected' : '' }}>Child (0–17)</option>
                            <option value="adult"  {{ request('age_group') === 'adult'  ? 'selected' : '' }}>Adult (18–59)</option>
                            <option value="senior" {{ request('age_group') === 'senior' ? 'selected' : '' }}>Senior (60+)</option>
                        </select>
                    </div>
                </div>

                <div class="filter-spacer"></div>
                <div class="filter-count">
                    Showing <strong>{{ $residents->count() }}</strong> of <strong>{{ $residents->total() }}</strong>
                </div>
            </div>

            {{-- Active filter tags --}}
            @php
                $activeFilters = array_filter([
                    'search'    => request('search'),
                    'type'      => request('type'),
                    'sex'       => request('sex'),
                    'barangay'  => request('barangay'),
                    'tag'       => request('tag'),
                    'age_group' => request('age_group'),
                ]);
            @endphp
            @if(count($activeFilters))
            <div class="active-filters">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2.5" style="flex-shrink:0">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                @foreach($activeFilters as $key => $val)
                    <span class="filter-tag">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $val }}
                        <a href="{{ request()->fullUrlWithQuery([$key => null]) }}">×</a>
                    </span>
                @endforeach
                <a href="{{ route('admin.residents.index') }}" class="clear-all">Clear all</a>
            </div>
            @endif
        </form>

        {{-- TABLE --}}
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th class="th-sortable {{ request('sort') === 'name' ? 'sorted' : '' }}"
                            onclick="sortBy('name')">
                            Full Name <span class="sort-icon">{{ request('sort') === 'name' ? (request('dir') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </th>
                        <th>Type</th>
                        <th>Sex</th>
                        <th class="th-sortable {{ request('sort') === 'age' ? 'sorted' : '' }}"
                            onclick="sortBy('age')">
                            Age <span class="sort-icon">{{ request('sort') === 'age' ? (request('dir') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </th>
                        <th class="th-sortable {{ request('sort') === 'barangay' ? 'sorted' : '' }}"
                            onclick="sortBy('barangay')">
                            Barangay <span class="sort-icon">{{ request('sort') === 'barangay' ? (request('dir') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </th>
                        <th>Household Head</th>
                        <th>Relationship</th>
                        <th>Serial Code</th>
                        <th>Tags</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($residents as $i => $person)
                    <tr onclick="window.location='{{ route('admin.households.show', $person['household_id']) }}'" title="View household">
                        <td class="td-num" data-label="#">{{ $residents->firstItem() + $i }}</td>
                        <td data-label="Full Name">
                            <div class="td-name hl-name">{{ $person['name'] }}</div>
                            @if($person['type'] === 'head' && !empty($person['contact_number']))
                                <div class="td-sub">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:2px"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 0h3a2 2 0 012 1.72c.12.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.58 2.81.7A2 2 0 0122 14.92z"/></svg>
                                    {{ $person['contact_number'] }}
                                </div>
                            @endif
                            @if(!empty($person['occupation']))
                                <div class="td-sub hl-employment">{{ $person['occupation'] }}</div>
                            @endif
                        </td>
                        <td data-label="Type">
                            @if($person['type'] === 'head')
                                <span class="badge badge-head">Head</span>
                            @else
                                <span class="badge badge-member">Member</span>
                            @endif
                        </td>
                        <td data-label="Sex">
                            @if($person['sex'] === 'Male')
                                <span class="badge badge-male">M</span>
                            @elseif($person['sex'] === 'Female')
                                <span class="badge badge-female">F</span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>
                        <td class="td-num hl-age" data-label="Age">{{ $person['age'] ?? '—' }}</td>
                        <td class="hl-barangay" data-label="Barangay">{{ $person['barangay'] ?? '—' }}</td>
                        <td data-label="Household Head">
                            @if($person['type'] === 'head')
                                <span class="td-muted">—</span>
                            @else
                                <span class="hl-name">{{ $person['household_head'] ?? '—' }}</span>
                            @endif
                        </td>
                        <td class="hl-relationship" data-label="Relationship">{{ $person['relationship'] ?? ($person['type'] === 'head' ? 'Head' : '—') }}</td>
                        <td class="hl-serial" data-label="Serial Code">
                            @if(!empty($person['serial_code']))
                                <span class="td-serial">{{ $person['serial_code'] }}</span>
                            @else
                                <span class="td-muted">Pending</span>
                            @endif
                        </td>
                        <td class="td-tags" data-label="Tags">
                            <div style="display:flex;gap:3px;flex-wrap:wrap;">
                                @if($person['is_4ps'])    <span class="badge badge-4ps">4Ps</span> @endif
                                @if($person['is_pwd'])    <span class="badge badge-pwd">PWD</span> @endif
                                @if($person['is_senior']) <span class="badge badge-senior">Senior</span> @endif
                                @if($person['is_solo'] ?? false) <span class="badge badge-solo">Solo Parent</span> @endif
                                @if($person['is_student'] ?? false) <span class="badge badge-student hl-student">Student</span> @endif
                                @if($person['is_lgbtqia'] ?? false) <span class="badge badge-lgbtqia">LGBTQIA+</span> @endif
                                @if(!$person['is_4ps'] && !$person['is_pwd'] && !$person['is_senior']
                                    && !($person['is_solo'] ?? false) && !($person['is_student'] ?? false)
                                    && !($person['is_lgbtqia'] ?? false))
                                    <span class="td-muted">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="8" r="4"/>
                                        <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                                    </svg>
                                </div>
                                <div class="empty-title">No residents found</div>
                                <div class="empty-sub">Try adjusting your search or filter criteria.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE CARD LIST (shown on ≤900px instead of the table) --}}
        <div class="resident-cards table-wrap" style="border-top:none;">
            @forelse($residents as $i => $person)
            <div class="resident-card">
                <div class="card-top">
                    <div class="card-name-block">
                        <div class="card-name hl-name">{{ $person['name'] }}</div>
                        <div class="card-sub hl-barangay">{{ $person['barangay'] ?? '—' }}</div>
                        @if(!empty($person['occupation']))
                            <div class="card-sub hl-employment">{{ $person['occupation'] }}</div>
                        @endif
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                        @if($person['sex'] === 'Male')
                            <span class="badge badge-male">M</span>
                        @elseif($person['sex'] === 'Female')
                            <span class="badge badge-female">F</span>
                        @endif
                        @if($person['type'] === 'head')
                            <span class="badge badge-head">Head</span>
                        @else
                            <span class="badge badge-member">Member</span>
                        @endif
                    </div>
                </div>
                <div class="card-badges">
                    @if($person['is_4ps'])    <span class="badge badge-4ps">4Ps</span> @endif
                    @if($person['is_pwd'])    <span class="badge badge-pwd">PWD</span> @endif
                    @if($person['is_senior']) <span class="badge badge-senior">Senior</span> @endif
                    @if($person['is_solo'] ?? false) <span class="badge badge-solo">Solo Parent</span> @endif
                    @if($person['is_student'] ?? false) <span class="badge badge-student">Student</span> @endif
                    @if($person['is_lgbtqia'] ?? false) <span class="badge badge-lgbtqia">LGBTQIA+</span> @endif
                </div>
                <div class="card-meta">
                    <div class="card-meta-item">
                        <div class="card-meta-label">Age</div>
                        <div class="card-meta-value hl-age">{{ $person['age'] ?? '—' }}</div>
                    </div>
                    <div class="card-meta-item">
                        <div class="card-meta-label">Relationship</div>
                        <div class="card-meta-value hl-relationship">{{ $person['relationship'] ?? ($person['type'] === 'head' ? 'Head' : '—') }}</div>
                    </div>
                    @if($person['type'] !== 'head')
                    <div class="card-meta-item" style="grid-column:1/-1;">
                        <div class="card-meta-label">Household Head</div>
                        <div class="card-meta-value hl-name">{{ $person['household_head'] ?? '—' }}</div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    @if(!empty($person['serial_code']))
                        <span class="card-serial hl-serial">{{ $person['serial_code'] }}</span>
                    @else
                        <span class="td-muted">No Serial</span>
                    @endif
                    <button class="card-detail-btn" onclick="event.stopPropagation(); openDrawer({{ $i }})" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        View Details
                    </button>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                    </svg>
                </div>
                <div class="empty-title">No residents found</div>
                <div class="empty-sub">Try adjusting your search or filter criteria.</div>
            </div>
            @endforelse
        </div>

        {{-- DETAIL DRAWER --}}
        <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
        <div class="drawer" id="detailDrawer">
            <div class="drawer-handle"></div>
            <div class="drawer-header">
                <div class="drawer-title" id="drawerName">—</div>
                <button class="drawer-close" onclick="closeDrawer()" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="drawer-body">
                <div class="drawer-section">
                    <div class="drawer-section-title">Personal Info</div>
                    <div class="drawer-grid">
                        <div class="drawer-field">
                            <div class="drawer-field-label">Type</div>
                            <div class="drawer-field-value" id="drawerType">—</div>
                        </div>
                        <div class="drawer-field">
                            <div class="drawer-field-label">Sex</div>
                            <div class="drawer-field-value" id="drawerSex">—</div>
                        </div>
                        <div class="drawer-field">
                            <div class="drawer-field-label">Age</div>
                            <div class="drawer-field-value" id="drawerAge">—</div>
                        </div>
                        <div class="drawer-field">
                            <div class="drawer-field-label">Relationship</div>
                            <div class="drawer-field-value" id="drawerRelationship">—</div>
                        </div>
                        <div class="drawer-field" id="drawerOccupationField">
                            <div class="drawer-field-label">Occupation</div>
                            <div class="drawer-field-value" id="drawerOccupation">—</div>
                        </div>
                    </div>
                </div>
                <div class="drawer-section">
                    <div class="drawer-section-title">Household Info</div>
                    <div class="drawer-grid">
                        <div class="drawer-field">
                            <div class="drawer-field-label">Barangay</div>
                            <div class="drawer-field-value" id="drawerBarangay">—</div>
                        </div>
                        <div class="drawer-field">
                            <div class="drawer-field-label">Serial Code</div>
                            <div class="drawer-field-value" id="drawerSerial">—</div>
                        </div>
                        <div class="drawer-field" id="drawerHeadField">
                            <div class="drawer-field-label">Household Head</div>
                            <div class="drawer-field-value" id="drawerHead">—</div>
                        </div>
                        <div class="drawer-field" id="drawerContactField">
                            <div class="drawer-field-label">Contact</div>
                            <div class="drawer-field-value" id="drawerContact">—</div>
                        </div>
                    </div>
                </div>
                <div class="drawer-section" id="drawerTagsSection">
                    <div class="drawer-section-title">Tags</div>
                    <div class="drawer-tags" id="drawerTags"></div>
                </div>
                <a class="drawer-view-btn" id="drawerViewBtn" href="#">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    View Full Household
                </a>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($residents->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing <strong>{{ $residents->firstItem() }}–{{ $residents->lastItem() }}</strong>
                of <strong>{{ $residents->total() }}</strong> residents
            </div>
            <div class="pagination-btns">
                <button class="pg-btn" {{ $residents->onFirstPage() ? 'disabled' : '' }}
                    onclick="goToPage(1)" title="First page">«</button>
                <button class="pg-btn" {{ $residents->onFirstPage() ? 'disabled' : '' }}
                    onclick="goToPage({{ $residents->currentPage() - 1 }})">‹</button>
                @foreach($residents->getUrlRange(max(1, $residents->currentPage()-2), min($residents->lastPage(), $residents->currentPage()+2)) as $page => $url)
                    <button class="pg-btn {{ $page == $residents->currentPage() ? 'active' : '' }}"
                        onclick="goToPage({{ $page }})">{{ $page }}</button>
                @endforeach
                <button class="pg-btn" {{ !$residents->hasMorePages() ? 'disabled' : '' }}
                    onclick="goToPage({{ $residents->currentPage() + 1 }})">›</button>
                <button class="pg-btn" {{ !$residents->hasMorePages() ? 'disabled' : '' }}
                    onclick="goToPage({{ $residents->lastPage() }})" title="Last page">»</button>
            </div>
        </div>
        @endif

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
    /* ─── Resident data for mobile drawer ─── */
    const residentData = @json($residentsJson);

    const householdRoute = '{{ rtrim(route("admin.households.show", ["household" => "__ID__"]), "") }}';

    function openDrawer(index) {
        const p = residentData[index];
        if (!p) return;

        document.getElementById('drawerName').textContent = p.name;

        const typeEl = document.getElementById('drawerType');
        typeEl.innerHTML = p.type === 'head'
            ? '<span class="badge badge-head">Head</span>'
            : '<span class="badge badge-member">Member</span>';

        const sexEl = document.getElementById('drawerSex');
        sexEl.innerHTML = p.sex === 'Male'
            ? '<span class="badge badge-male">Male</span>'
            : p.sex === 'Female'
                ? '<span class="badge badge-female">Female</span>'
                : '<span class="td-muted">—</span>';

        document.getElementById('drawerAge').textContent = p.age;
        document.getElementById('drawerRelationship').textContent = p.relationship;
        document.getElementById('drawerBarangay').textContent = p.barangay;

        const serialEl = document.getElementById('drawerSerial');
        serialEl.innerHTML = p.serial_code
            ? `<span class="td-serial">${p.serial_code}</span>`
            : '<span class="td-muted">Pending</span>';

        const occEl = document.getElementById('drawerOccupation');
        const occField = document.getElementById('drawerOccupationField');
        if (p.occupation) { occEl.textContent = p.occupation; occField.style.display = ''; }
        else { occField.style.display = 'none'; }

        const headField = document.getElementById('drawerHeadField');
        const headEl = document.getElementById('drawerHead');
        if (p.type !== 'head' && p.household_head) {
            headEl.textContent = p.household_head; headField.style.display = '';
        } else { headField.style.display = 'none'; }

        const contactField = document.getElementById('drawerContactField');
        const contactEl = document.getElementById('drawerContact');
        if (p.type === 'head' && p.contact_number) {
            contactEl.textContent = p.contact_number; contactField.style.display = '';
        } else { contactField.style.display = 'none'; }

        const tagMap = [
            ['is_4ps',    'badge badge-4ps',     '4Ps'],
            ['is_pwd',    'badge badge-pwd',     'PWD'],
            ['is_senior', 'badge badge-senior',  'Senior Citizen'],
            ['is_solo',   'badge badge-solo',    'Solo Parent'],
            ['is_student','badge badge-student', 'Student'],
            ['is_lgbtqia','badge badge-lgbtqia', 'LGBTQIA+'],
        ];
        const tagsEl = document.getElementById('drawerTags');
        tagsEl.innerHTML = '';
        let hasTags = false;
        tagMap.forEach(([key, cls, label]) => {
            if (p[key]) { tagsEl.innerHTML += `<span class="${cls}">${label}</span>`; hasTags = true; }
        });
        document.getElementById('drawerTagsSection').style.display = hasTags ? '' : 'none';

        document.getElementById('drawerViewBtn').href = householdRoute.replace('__ID__', p.household_id);

        const overlay = document.getElementById('drawerOverlay');
        const drawer  = document.getElementById('detailDrawer');
        overlay.style.display = 'block';
        requestAnimationFrame(() => { overlay.classList.add('active'); drawer.classList.add('open'); });
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        const overlay = document.getElementById('drawerOverlay');
        const drawer  = document.getElementById('detailDrawer');
        overlay.classList.remove('active');
        drawer.classList.remove('open');
        setTimeout(() => { overlay.style.display = 'none'; }, 300);
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

    /* ─── Clock ─── */
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

    /* ─── Sidebar ─── */
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); sidebarOverlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); sidebarOverlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    /* ─── Pagination ─── */
    function goToPage(page) {
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location = url.toString();
    }

    /* ─── Sort ─── */
    function sortBy(col) {
        const url = new URL(window.location);
        const currentSort = url.searchParams.get('sort');
        const currentDir  = url.searchParams.get('dir');
        url.searchParams.set('sort', col);
        url.searchParams.set('dir', currentSort === col && currentDir === 'asc' ? 'desc' : 'asc');
        url.searchParams.set('page', 1);
        window.location = url.toString();
    }

    /* ─── Search scope ─── */
    function updateScope(val) {
        document.getElementById('scopeHidden').value = val;
        const input = document.getElementById('searchInput');
        const placeholders = {
            all:          'Search name, barangay, serial, age...',
            name:         'Search by full name...',
            barangay:     'Search by barangay...',
            serial:       'Search by serial code...',
            age:          'Search by age (e.g. 25)...',
            civil_status: 'e.g. Single, Married, Widowed...',
            employment:   'e.g. Employed, Unemployed...',
            relationship: 'e.g. Head, Spouse, Son...',
            student:      'Search student by name...',
        };
        input.placeholder = placeholders[val] || 'Search residents...';
    }

    /* ─── Search input: debounce submit + live highlight + clear btn ─── */
    let searchTimer;
    function onSearchInput(input) {
        const combo = document.getElementById('searchCombo');
        combo.classList.toggle('has-value', input.value.length > 0);
        applyHighlight(input.value, document.getElementById('scopeSelect').value);
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 600);
    }

    function clearSearch() {
        const input = document.getElementById('searchInput');
        input.value = '';
        document.getElementById('searchCombo').classList.remove('has-value');
        removeHighlight();
        document.getElementById('filterForm').submit();
    }

    /* ─── Live highlight ─── */
    const SCOPE_CLASS = {
        all:          null,   // highlight all hl-* cells
        name:         ['hl-name'],
        barangay:     ['hl-barangay'],
        serial:       ['hl-serial'],
        age:          ['hl-age'],
        civil_status: [],     // no dedicated cell — skip highlight
        employment:   ['hl-employment'],
        relationship: ['hl-relationship'],
        student:      ['hl-student'],
    };

    function escapeRe(s){ return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }

    function applyHighlight(term, scope) {
        removeHighlight();
        if (!term) return;
        const re = new RegExp('(' + escapeRe(term) + ')', 'gi');
        let classes = SCOPE_CLASS[scope];
        if (classes === null) classes = ['hl-name','hl-barangay','hl-serial','hl-age','hl-employment','hl-relationship','hl-student'];
        classes.forEach(cls => {
            document.querySelectorAll('.' + cls).forEach(el => {
                el.innerHTML = el.textContent.replace(re, '<mark class="hl">$1</mark>');
            });
        });
    }

    function removeHighlight() {
        document.querySelectorAll('mark.hl').forEach(mark => {
            const parent = mark.parentNode;
            parent.replaceChild(document.createTextNode(mark.textContent), mark);
            parent.normalize();
        });
    }

    /* ─── Mobile filter toggle ─── */
    function toggleFilters() {
        const col = document.getElementById('filtersCollapsible');
        const btn = document.getElementById('filterToggleBtn');
        col.classList.toggle('open');
        btn.style.color = col.classList.contains('open') ? 'var(--blue)' : '';
    }

    /* ─── Active filter badge on toggle button ─── */
    (function() {
        const active = ['type','sex','barangay','tag','age_group']
            .filter(k => new URLSearchParams(window.location.search).get(k));
        if (active.length) {
            const badge = document.getElementById('filterBadge');
            if (badge) { badge.textContent = active.length; badge.classList.add('show'); }
        }
    })();

    /* ─── Init: restore combo state on page load ─── */
    (function() {
        const input = document.getElementById('searchInput');
        const scope = document.getElementById('scopeSelect');
        if (input && input.value) {
            document.getElementById('searchCombo').classList.add('has-value');
            applyHighlight(input.value, scope ? scope.value : 'all');
        }
        if (scope) updateScope(scope.value);
    })();
</script>
</body>
</html>