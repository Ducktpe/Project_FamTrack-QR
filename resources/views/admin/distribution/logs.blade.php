{{-- resources/views/admin/distribution/logs.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic - Distribution Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
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

        /* --- TOP UTILITY BAR --- */
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* --- HEADER --- */
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

        /* --- SIDEBAR OVERLAY --- */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 250; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; opacity: 1; }

        /* --- SIDEBAR --- */
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
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
        .logout-btn:hover { background: var(--red); }

        /* --- MAIN --- */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 24px 28px; }

        /* --- PAGE TITLEBAR --- */
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; flex-wrap: wrap; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date span { display: block; font-size: 12px; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* --- SUMMARY CARDS --- */
        .summary-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .summary-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 16px 18px; display: flex; align-items: center; gap: 14px; }
        .summary-card.green  { border-top-color: var(--green); }
        .summary-card.orange { border-top-color: var(--orange); }
        .summary-card.yellow { border-top-color: var(--yellow-dark); }
        .summary-icon { width: 42px; height: 42px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .summary-icon svg { width: 20px; height: 20px; }
        .summary-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 4px; }
        .summary-value { font-family: 'PT Serif', serif; font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .summary-card.green  .summary-value { color: var(--green); }
        .summary-card.orange .summary-value { color: var(--orange); }
        .summary-card.yellow .summary-value { color: var(--yellow-dark); }
        .summary-sub { font-size: 11px; color: var(--gray-400); margin-top: 3px; }

        /* --- FILTER BOX --- */
        .filter-box { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 16px; overflow: hidden; }
        .filter-box-header { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); padding: 10px 20px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
        .filter-box-header .ftb-count { background: var(--blue); color: #fff; border-radius: 10px; font-size: 10px; padding: 1px 7px; display: none; margin-left: 4px; }
        .filter-box-header .ftb-count.show { display: inline; }
        .filter-box-body { padding: 14px 20px; display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }

        /* Search scope combo — scoped carefully so it doesn't bleed into other selects */
        .search-combo { display: flex; align-items: stretch; border: 1px solid var(--gray-200); border-radius: 3px; overflow: hidden; background: var(--gray-50); transition: border-color 0.15s, box-shadow 0.15s; flex: 1; min-width: 280px; }
        .search-combo:focus-within { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.1); background: var(--white); }
        .search-combo .scope-sel { border: none !important; border-right: 1px solid var(--gray-200) !important; background: var(--gray-100) !important; padding: 8px 22px 8px 10px !important; font-size: 11px !important; font-weight: 700 !important; color: var(--gray-600) !important; font-family: 'Open Sans', sans-serif !important; outline: none !important; appearance: none !important; -webkit-appearance: none !important; cursor: pointer; flex-shrink: 0; min-width: 100px; width: auto !important; box-shadow: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important; background-position: right 6px center !important; }
        .search-combo .scope-sel:hover { background-color: var(--blue-pale) !important; color: var(--blue) !important; }
        .search-combo .srch-input { border: none !important; background: transparent !important; padding: 8px 10px !important; font-size: 13px !important; color: var(--gray-800) !important; font-family: 'Open Sans', sans-serif !important; outline: none !important; flex: 1; min-width: 0; width: auto !important; box-shadow: none !important; }
        .search-combo .srch-input::placeholder { color: var(--gray-400); }
        .search-combo .srch-clear { display: none; align-items: center; justify-content: center; padding: 0 10px; border: none !important; background: transparent !important; color: var(--gray-400); cursor: pointer; font-size: 18px; line-height: 1; transition: color 0.12s; width: auto !important; }
        .search-combo .srch-clear:hover { color: var(--red) !important; background: transparent !important; border: none !important; box-shadow: none !important; }
        .search-combo.has-value .srch-clear { display: flex; }

        /* Shared date/select/text styles for filter row (NOT the combo) */
        .filter-box input[type="date"],
        .filter-group select,
        .modal-body input[type="text"],
        .export-filter-group input[type="date"],
        .export-filter-group select {
            width: 100%; padding: 8px 10px; border: 1px solid var(--gray-200); border-radius: 3px;
            font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); background: var(--white); outline: none;
        }
        .filter-box input[type="date"]:focus, .filter-group select:focus,
        .export-filter-group input:focus, .export-filter-group select:focus,
        .modal-body input:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.1); }
        .modal-body input::placeholder { color: var(--gray-400); }

        /* Highlight */
        mark.hl { background: #FFF176; color: inherit; border-radius: 2px; padding: 0 1px; font-style: normal; }

        /* Active filter tags */
        .active-filters { padding: 8px 20px; border-top: 1px solid var(--gray-100); display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .filter-tag { display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; background: var(--blue-pale); border: 1px solid #C7D9F5; border-radius: 10px; font-size: 11px; color: var(--blue); font-weight: 600; }
        .filter-tag a { color: var(--blue); text-decoration: none; margin-left: 2px; opacity: 0.6; font-weight: 700; }
        .filter-tag a:hover { opacity: 1; }
        .clear-all-link { font-size: 11px; color: var(--red); text-decoration: none; font-weight: 600; margin-left: 4px; }
        .clear-all-link:hover { text-decoration: underline; }



        /* --- TABLE --- */
        .table-wrap { background: var(--white); border: 1px solid var(--gray-200); overflow: hidden; }
        .table-header { padding: 13px 20px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; gap: 10px; justify-content: space-between; }
        .table-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); display: flex; align-items: center; gap: 8px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .table-count { font-size: 11px; color: var(--gray-400); }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        thead tr { background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
        thead th { padding: 11px 16px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody td { padding: 12px 16px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }

        /* --- EVENT CARDS (mobile) --- */
        .event-cards { display: none; flex-direction: column; gap: 10px; padding: 12px; background: var(--gray-100); }
        .event-card { background: var(--white); border: 1px solid var(--gray-200); border-left: 4px solid var(--blue); border-radius: 6px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.06); transition: box-shadow 0.15s; }
        .event-card:hover { box-shadow: 0 3px 10px rgba(27,63,122,0.1); }
        .event-card.status-ongoing   { border-left-color: var(--green); }
        .event-card.status-upcoming  { border-left-color: var(--blue-light); }
        .event-card.status-completed { border-left-color: var(--gray-400); }
        .event-card.status-cancelled { border-left-color: var(--red); }
        .event-card-head { padding: 11px 14px 9px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
        .event-card-name { font-size: 13px; font-weight: 700; color: var(--blue-dark); line-height: 1.3; flex: 1; min-width: 0; }
        .event-card-desc { font-size: 11px; color: var(--gray-400); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .event-card-badges { display: flex; gap: 4px; flex-wrap: wrap; flex-shrink: 0; }
        .event-card-body { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .event-card-field { padding: 8px 14px; border-bottom: 1px solid var(--gray-100); border-right: 1px solid var(--gray-100); }
        .event-card-field:nth-child(even) { border-right: none; }
        .event-card-field:nth-last-child(-n+2) { border-bottom: none; }
        .event-card-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: var(--gray-400); margin-bottom: 3px; }
        .event-card-value { font-size: 12px; color: var(--gray-800); line-height: 1.4; }
        .event-card-value.bold { font-weight: 700; color: var(--blue-dark); font-size: 14px; }
        .event-card-foot { padding: 10px 12px; background: var(--gray-50); border-top: 1px solid var(--gray-100); display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

        /* -- Cancellation reason inline -- */
        .cancel-reason-inline { display: flex; align-items: flex-start; gap: 5px; margin-top: 5px; padding: 5px 8px; background: var(--red-pale); border: 1px solid #FECACA; border-radius: 3px; }
        .cancel-reason-inline svg { flex-shrink: 0; margin-top: 1px; }
        .cancel-reason-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--red); margin-bottom: 1px; }
        .cancel-reason-text { font-size: 11px; color: #7f1d1d; line-height: 1.4; }

        /* -- Barangay pills -- */
        .brgy-pill { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; background: var(--blue-pale); color: var(--blue-dark); border: 1px solid #C5D9F5; white-space: nowrap; }
        .brgy-pill-more { background: var(--gray-100); color: var(--gray-600); border-color: var(--gray-200); cursor: default; position: relative; }
        .brgy-pill-more:hover { background: var(--blue-pale); color: var(--blue); border-color: #C5D9F5; }
        #brgyTooltip { position: fixed; z-index: 9999; background: var(--gray-800); color: #fff; font-size: 11px; font-family: 'Open Sans', sans-serif; padding: 7px 12px; border-radius: 4px; pointer-events: none; max-width: 260px; line-height: 1.6; box-shadow: 0 4px 14px rgba(0,0,0,0.2); }

        /* -- highlight class for map pin deep-link -- */
        tbody tr.pin-highlight { background: var(--yellow-pale) !important; transition: background 1.5s ease; }

        /* Status badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-upcoming  { background: var(--yellow-pale); color: var(--yellow-dark); border: 1px solid #FDE68A; }
        .badge-ongoing   { background: var(--green-pale);  color: var(--green-dark);  border: 1px solid #BBF7D0; }
        .badge-completed { background: var(--blue-pale);   color: var(--blue);        border: 1px solid #C7D9F5; }
        .badge-cancelled { background: var(--red-pale);    color: var(--red);         border: 1px solid #FECACA; }

        /* Action buttons */
        .btn-view { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--blue-pale); color: var(--blue); border: 1px solid #C5D9F5; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 11px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.12s, color 0.12s; white-space: nowrap; pointer-events: auto; position: relative; z-index: 2; }
        .btn-view:hover { background: var(--blue); color: var(--white); }
        .btn-view svg { width: 12px; height: 12px; pointer-events: none; }
        .btn-export { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 11px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.12s, color 0.12s; white-space: nowrap; pointer-events: auto; position: relative; z-index: 2; }
        .btn-export:hover { background: var(--green); color: var(--white); }
        .btn-export svg { width: 12px; height: 12px; pointer-events: none; }
        .action-btns { display: flex; align-items: center; gap: 6px; position: relative; z-index: 1; }
        .btn-cancel { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--red-pale); color: var(--red); border: 1px solid #FECACA; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 11px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.12s, color 0.12s; white-space: nowrap; pointer-events: auto; position: relative; z-index: 2; }
        .btn-cancel:hover { background: var(--red); color: var(--white); }
        .btn-cancel svg { width: 12px; height: 12px; pointer-events: none; }

        /* Empty state */
        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon-wrap { width: 48px; height: 48px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon-wrap svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* Pagination */
        .pagination-wrap { padding: 12px 20px; border-top: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--gray-600); flex-wrap: wrap; gap: 8px; }
        .pagination-wrap .links a, .pagination-wrap .links span { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-600); font-size: 12px; text-decoration: none; border-radius: 3px; margin: 0 2px; transition: all 0.15s; padding: 0 6px; }
        .pagination-wrap .links span[aria-current] { background: var(--blue); color: var(--white); border-color: var(--blue); font-weight: 700; }
        .pagination-wrap .links a:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }

        /* --- MODAL --- */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 500; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: var(--white); width: 100%; max-width: 980px; max-height: 88vh; display: flex; flex-direction: column; border-top: 4px solid var(--yellow); box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); flex-shrink: 0; }
        .modal-head-left { display: flex; align-items: center; gap: 10px; }
        .modal-head-icon { width: 32px; height: 32px; background: var(--blue-pale); border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .modal-head-icon svg { width: 16px; height: 16px; color: var(--blue); }
        .modal-head h2 { font-family: 'PT Serif', serif; font-size: 16px; font-weight: 700; color: var(--blue-dark); }
        .modal-head-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .modal-close-btn { background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--gray-600); transition: background 0.15s; flex-shrink: 0; }
        .modal-close-btn:hover { background: var(--red-pale); color: var(--red); }
        .modal-close-btn svg { width: 15px; height: 15px; }
        .modal-body { padding: 20px 22px; overflow-y: auto; flex: 1; }
        .spinner { width: 18px; height: 18px; border: 2px solid var(--gray-200); border-top-color: var(--blue); border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* --- FOOTER --- */
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

        /* --- EXPORT MODAL --- */
        .export-modal-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 600; align-items: center; justify-content: center; padding: 20px; }
        .export-modal-overlay.active { display: flex !important; }
        .export-modal-box { background: var(--white); width: 100%; max-width: 780px; max-height: 90vh; display: flex; flex-direction: column; border-top: 4px solid var(--green); box-shadow: 0 20px 60px rgba(0,0,0,0.3); border-radius: 2px; }
        .export-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); flex-shrink: 0; }
        .export-modal-head-left { display: flex; align-items: center; gap: 10px; }
        .export-modal-head-icon { width: 32px; height: 32px; background: var(--green-pale); border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .export-modal-head-icon svg { width: 16px; height: 16px; color: var(--green-dark); }
        .export-modal-head h2 { font-family: 'PT Serif', serif; font-size: 16px; font-weight: 700; color: var(--blue-dark); }
        .export-modal-head-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .export-modal-body { padding: 20px 22px; overflow-y: auto; flex: 1; }
        .export-modal-footer { padding: 14px 22px; border-top: 1px solid var(--gray-200); background: var(--gray-50); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-shrink: 0; flex-wrap: wrap; }
        .export-section { margin-bottom: 18px; }
        .export-section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; justify-content: space-between; }
        .export-section-title a { font-size: 10px; font-weight: 600; color: var(--blue-light); text-decoration: none; letter-spacing: 0; text-transform: none; cursor: pointer; }
        .export-section-title a:hover { color: var(--blue); }
        .export-cols-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .export-col-toggle { display: flex; align-items: center; gap: 8px; padding: 7px 10px; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 3px; cursor: pointer; transition: background 0.12s, border-color 0.12s; user-select: none; }
        .export-col-toggle:hover { background: var(--blue-pale); border-color: #C5D9F5; }
        .export-col-toggle.checked { background: var(--blue-pale); border-color: var(--blue-light); }
        .export-col-toggle input[type="checkbox"] { width: 14px !important; height: 14px !important; min-width: 14px !important; max-width: 14px !important; flex-shrink: 0; accent-color: var(--blue); cursor: pointer; margin: 0 !important; padding: 0 !important; border: none !important; box-shadow: none !important; }
        .export-col-toggle span { font-size: 12px; color: var(--gray-800); cursor: pointer; line-height: 1.3; pointer-events: none; }
        .export-col-toggle.checked span { color: var(--blue-dark); font-weight: 600; }
        .export-filters { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 18px; }
        .export-filter-group { display: flex; flex-direction: column; gap: 4px; }
        .export-filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }
        .export-info { font-size: 11px; color: var(--gray-400); }
        .export-info strong { color: var(--blue-dark); }
        .export-footer-btns { display: flex; align-items: center; gap: 8px; }
        .btn-do-export { display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px; background: var(--green); color: var(--white); border: none; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background 0.15s; }
        .btn-do-export:hover { background: var(--green-dark); }
        .btn-do-export:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-export-cancel { padding: 9px 16px; background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
        .btn-export-cancel:hover { background: var(--gray-100); }

        /* --- CANCEL MODAL --- */
        .cancel-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 700; align-items: center; justify-content: center; padding: 20px; }
        .cancel-modal-overlay.active { display: flex; }
        .cancel-modal-box { background: var(--white); width: 100%; max-width: 520px; display: flex; flex-direction: column; border-top: 4px solid var(--red); box-shadow: 0 20px 60px rgba(0,0,0,0.3); border-radius: 2px; }
        .cancel-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); flex-shrink: 0; }
        .cancel-modal-head-left { display: flex; align-items: center; gap: 10px; }
        .cancel-modal-head-icon { width: 32px; height: 32px; background: var(--red-pale); border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .cancel-modal-head-icon svg { width: 16px; height: 16px; color: var(--red); }
        .cancel-modal-head h2 { font-family: 'PT Serif', serif; font-size: 16px; font-weight: 700; color: var(--blue-dark); }
        .cancel-modal-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .cancel-modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 16px; }
        .cancel-modal-footer { padding: 14px 22px; border-top: 1px solid var(--gray-200); background: var(--gray-50); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .cancel-warning-banner { display: flex; align-items: flex-start; gap: 10px; background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid var(--red); padding: 12px 14px; border-radius: 2px; color: var(--red); font-size: 12px; }
        .cancel-warning-banner strong { font-size: 12px; display: block; margin-bottom: 2px; }
        .cancel-warning-banner span { font-size: 11px; opacity: 0.85; }
        .cancel-field-group { display: flex; flex-direction: column; gap: 5px; }
        .cancel-field-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }
        .cancel-field-label em { font-style: normal; font-weight: 400; color: var(--gray-400); text-transform: none; letter-spacing: 0; margin-left: 4px; }
        .cancel-select,
        .cancel-textarea {
            width: 100%; padding: 9px 10px;
            border: 1px solid var(--gray-200); border-radius: 3px;
            font-family: 'Open Sans', sans-serif; font-size: 13px;
            color: var(--gray-800); background: var(--white); outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .cancel-select:focus,
        .cancel-textarea:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(192,57,43,0.1); }
        .cancel-textarea { resize: vertical; line-height: 1.5; min-height: 80px; }
        .cancel-char-count { text-align: right; font-size: 10px; color: var(--gray-400); margin-top: 3px; }
        .btn-do-cancel { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; background: var(--red); color: var(--white); border: none; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background 0.15s; }
        .btn-do-cancel:hover:not(:disabled) { background: #a93226; }
        .btn-do-cancel:disabled { opacity: 0.4; cursor: not-allowed; }

        /* --- RESPONSIVE --- */
        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; height: 100vh; overflow: hidden; }
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
            .header-admin-badge { padding: 6px 10px; gap: 8px; }
            .admin-name { font-size: 12px; }
            .admin-role { display: none; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .main-content { padding: 20px 16px; }
            .summary-row { grid-template-columns: repeat(2, 1fr); }
            .filters { grid-template-columns: 1fr 1fr; }
            /* Switch to card view on tablet/mobile */
            .table-scroll { display: none; }
            .event-cards { display: flex; }
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
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }
            .summary-row { grid-template-columns: 1fr 1fr; gap: 10px; }
            .filters { grid-template-columns: 1fr; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }
        @media (max-width: 480px) {
            .shell { grid-template-rows: 28px 52px 1fr 40px; }
            .main-content { padding: 10px 8px; }
            .topbar { padding: 0 10px; }
            header { padding: 0 8px; }
            .header-title { font-size: 13px; }
        }
    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="topbar">
        <div class="topbar-left">Republic of the Philippines &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic</div>
        <div class="topbar-right">
            <span class="clock-date-inline" id="top-date">-</span>
            <span class="clock-inline" id="top-time">00:00:00</span>
            <span class="status-indicator">System Online</span>
        </div>
    </div>

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
            <div class="header-title">MDRRMO - Naic, Cavite</div>
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

    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <div class="nav-section-label">Admin Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard Overview
        </a>
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Distribution Events
        </a>
        <a href="{{ route('admin.distribution.logs') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
        </a>
        <a href="{{ route('admin.residents.index') }}" class="nav-item" onclick="closeSidebar()">
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
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
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

    <main class="main-content" id="mainContent">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Home</a> / <span>Distribution Logs</span>
                </div>
                <div class="page-h1">Distribution Logs</div>
                <div class="page-sub">View and manage all relief distribution events - Barangay Family Track QR System</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">-</strong>
            </div>
        </div>

        {{-- Flash success message --}}
        @if(session('success'))
            <div style="background:var(--green-pale);border:1px solid #BBF7D0;border-left:4px solid var(--green);padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;border-radius:2px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green-dark)" stroke-width="2.5" style="flex-shrink:0;">
                    <circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/>
                </svg>
                <span style="font-size:13px;font-weight:600;color:var(--green-dark);">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-icon" style="background:#EAF0FA;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#1B3F7A" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <div>
                    <div class="summary-label">Total Events</div>
                    <div class="summary-value">{{ $events->total() }}</div>
                    <div class="summary-sub">All recorded events</div>
                </div>
            </div>
            <div class="summary-card green">
                <div class="summary-icon" style="background:#DCFCE7;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#15803D" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>
                </div>
                <div>
                    <div class="summary-label">Ongoing</div>
                    <div class="summary-value">{{ $events->where('status','ongoing')->count() }}</div>
                    <div class="summary-sub">Currently active</div>
                </div>
            </div>
            <div class="summary-card orange">
                <div class="summary-icon" style="background:#FFFBEB;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div>
                    <div class="summary-label">Upcoming</div>
                    <div class="summary-value">{{ $events->where('status','upcoming')->count() }}</div>
                    <div class="summary-sub">Scheduled events</div>
                </div>
            </div>
            <div class="summary-card yellow">
                <div class="summary-icon" style="background:#FFFAE6;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D4A800" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div>
                    <div class="summary-label">Completed</div>
                    <div class="summary-value">{{ $events->where('status','completed')->count() }}</div>
                    <div class="summary-sub">Finished events</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        @php
            $activeFilters = array_filter([
                'search'    => request('search'),
                'date_from' => request('date_from'),
                'date_to'   => request('date_to'),
                'status'    => request('status'),
                'barangay'  => request('barangay'),
            ]);
        @endphp

        <div class="filter-box">
            <div class="filter-box-header">
                <span style="display:flex;align-items:center;gap:7px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter Events
                    <span class="ftb-count {{ count($activeFilters) ? 'show' : '' }}">{{ count($activeFilters) ?: '' }}</span>
                </span>
                @if(count($activeFilters))
                    <a href="{{ route('admin.distribution.logs') }}" class="clear-all-link">Clear all filters</a>
                @endif
            </div>

            <form method="GET" id="filterForm">
                <input type="hidden" name="scope" id="scopeHidden" value="{{ request('scope','all') }}">

                <div class="filter-box-body">

                    {{-- ── Search scope combo ── --}}
                    <div class="filter-group" style="flex:1;min-width:280px;">
                        <div class="filter-label">Search</div>
                        <div class="search-combo" id="searchCombo">
                            <select class="scope-sel" id="scopeSelect" onchange="updateScope(this.value)">
                                <option value="all"        {{ request('scope','all')==='all'        ? 'selected':'' }}>All Fields</option>
                                <option value="event_name" {{ request('scope')==='event_name' ? 'selected':'' }}>Event Name</option>
                                <option value="relief"     {{ request('scope')==='relief'     ? 'selected':'' }}>Relief Type</option>
                                <option value="barangay"   {{ request('scope')==='barangay'   ? 'selected':'' }}>Barangay</option>
                                <option value="status"     {{ request('scope')==='status'     ? 'selected':'' }}>Status</option>
                                <option value="date"       {{ request('scope')==='date'       ? 'selected':'' }}>Date</option>
                            </select>
                            <input type="text" name="search" class="srch-input" id="searchInput"
                                placeholder="Search distribution events..."
                                value="{{ request('search') }}"
                                oninput="onSearchInput(this)"
                                autocomplete="off">
                            <button type="button" class="srch-clear" onclick="clearSearch()" title="Clear">×</button>
                        </div>
                    </div>

                    {{-- ── Date range ── --}}
                    <div class="filter-group">
                        <div class="filter-label">From Date</div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">To Date</div>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()">
                    </div>

                    {{-- ── Status ── --}}
                    <div class="filter-group">
                        <div class="filter-label">Status</div>
                        <select name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="upcoming"  {{ request('status')=='upcoming'  ? 'selected':'' }}>Upcoming</option>
                            <option value="ongoing"   {{ request('status')=='ongoing'   ? 'selected':'' }}>Ongoing</option>
                            <option value="completed" {{ request('status')=='completed' ? 'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                        </select>
                    </div>

                    {{-- ── Barangay ── --}}
                    <div class="filter-group">
                        <div class="filter-label">Barangay</div>
                        <select name="barangay" onchange="this.form.submit()">
                            <option value="">All Barangays</option>
                            @foreach($allBarangays as $brgy)
                                <option value="{{ $brgy }}" {{ request('barangay')==$brgy ? 'selected':'' }}>{{ $brgy }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>

                {{-- ── Active filter tags ── --}}
                @if(count($activeFilters))
                <div class="active-filters">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2.5" style="flex-shrink:0"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    @foreach($activeFilters as $key => $val)
                        <span class="filter-tag">
                            {{ ucfirst(str_replace('_',' ',$key)) }}: {{ $val }}
                            <a href="{{ request()->fullUrlWithQuery([$key => null]) }}">×</a>
                        </span>
                    @endforeach
                    <a href="{{ route('admin.distribution.logs') }}" class="clear-all-link">Clear all</a>
                </div>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            <div class="table-header">
                <div class="table-title">
                    <div class="ca-dot"></div>
                    Distribution Events Log
                </div>
                <div class="table-count">{{ $events->total() }} event(s) found</div>
            </div>

            @if($events->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="empty-title">No distribution events found</div>
                    <div class="empty-sub">Try adjusting your filters or check back later.</div>
                </div>
            @else
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Event Name</th>
                                <th>Relief Type</th>
                                <th>Barangay</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Scan Mode</th>
                                <th style="text-align:center">Distributed</th>
                                <th style="text-align:center">Households</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $i => $event)
                                <tr id="event-{{ $event->id }}">
                                    <td style="color:var(--gray-400); font-size:12px;">{{ $events->firstItem() + $i }}</td>

                                    {{-- Event Name + cancellation reason shown inline when cancelled --}}
                                    <td>
                                        <div style="font-weight:600; color:var(--blue-dark);" class="hl-event">{{ $event->event_name }}</div>
                                        @if($event->description)
                                            <div style="font-size:11px; color:var(--gray-400); margin-top:2px;">{{ Str::limit($event->description, 50) }}</div>
                                        @endif
                                    </td>

                                    <td style="font-size:12px; color:var(--gray-600);" class="hl-relief">{{ is_array($event->relief_type) ? implode(', ', $event->relief_type) : ($event->relief_type ?? '-') }}</td>
                                    <td>
                                        @php
                                            $brgys = is_array($event->target_barangay)
                                                ? array_values(array_filter($event->target_barangay))
                                                : (($event->target_barangay && $event->target_barangay !== '[]') ? [$event->target_barangay] : []);
                                            $showCount  = 2;
                                            $visible    = array_slice($brgys, 0, $showCount);
                                            $hidden     = array_slice($brgys, $showCount);
                                            $hiddenTip  = implode(', ', $hidden);
                                        @endphp
                                        @if(empty($brgys))
                                            <span style="color:var(--gray-400);font-size:12px;">-</span>
                                        @else
                                            <div style="display:flex;flex-wrap:wrap;gap:4px;align-items:center;">
                                                @foreach($visible as $b)
                                                    <span class="brgy-pill">{{ $b }}</span>
                                                @endforeach
                                                @if(count($hidden) > 0)
                                                    <span class="brgy-pill brgy-pill-more"
                                                          data-tip="{{ $hiddenTip }}"
                                                          onmouseenter="showBrgyTip(this)"
                                                          onmouseleave="hideBrgyTip()">+{{ count($hidden) }} more</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td style="font-size:12px; white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                        @if(strtolower($event->status) === 'cancelled' && $event->cancelled_at)
                                            <div style="font-size:10px;color:var(--red);margin-top:2px;">
                                                Cancelled {{ \Carbon\Carbon::parse($event->cancelled_at)->setTimezone('Asia/Manila')->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php $s = strtolower($event->status); @endphp
                                        <span class="badge badge-{{ $s }} hl-status">{{ ucfirst($s) }}</span>
                                    </td>
                                    <td>
                                        @if(($event->scan_mode ?? 'household') === 'family_head')
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;background:#F5F3FF;color:#6D28D9;border:1px solid #DDD6FE;">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
                                                Per Family Head
                                            </span>
                                        @else
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;background:#EAF0FA;color:#1B3F7A;border:1px solid #C7D9F5;">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                                                Per Household
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:var(--blue-dark);">
                                        {{ $event->total_distributed ?? 0 }}
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:var(--gray-600);">
                                        {{ $event->unique_households ?? 0 }}
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn-view" onclick="openModal({{ $event->id }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View
                                            </button>
                                            <button class="btn-export"
                                                data-event-id="{{ $event->id }}"
                                                data-event-name="{{ e($event->event_name) }}"
                                                data-barangays="{{ json_encode($event->logs->pluck('household.barangay')->filter()->unique()->sort()->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
                                                onclick="openExportModal(this)">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                                    <polyline points="7 10 12 15 17 10"/>
                                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                                </svg>
                                                Export
                                            </button>

                                            {{-- Cancel button: only show for upcoming/ongoing events --}}
                                            @if(!in_array(strtolower($event->status), ['cancelled', 'completed']))
                                                <button type="button" class="btn-cancel"
                                                        onclick="openCancelModal({{ $event->id }}, '{{ addslashes($event->event_name) }}')">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <circle cx="12" cy="12" r="10"/>
                                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                                    </svg>
                                                    Cancel
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ── CARD VIEW (mobile/tablet) ── --}}
                <div class="event-cards">
                    @foreach($events as $i => $event)
                    @php
                        $s = strtolower($event->status);
                        $brgys = is_array($event->target_barangay)
                            ? array_values(array_filter($event->target_barangay))
                            : (($event->target_barangay && $event->target_barangay !== '[]') ? [$event->target_barangay] : []);
                        $visible = array_slice($brgys, 0, 2);
                        $hidden  = array_slice($brgys, 2);
                    @endphp
                    <div class="event-card status-{{ $s }}" id="event-card-{{ $event->id }}">
                        <div class="event-card-head">
                            <div style="flex:1;min-width:0;">
                                <div class="event-card-name hl-event">{{ $event->event_name }}</div>
                                @if($event->description)
                                    <div class="event-card-desc">{{ Str::limit($event->description, 60) }}</div>
                                @endif
                            </div>
                            <div class="event-card-badges">
                                <span class="badge badge-{{ $s }} hl-status">{{ ucfirst($s) }}</span>
                            </div>
                        </div>
                        <div class="event-card-body">
                            <div class="event-card-field">
                                <div class="event-card-label">Date</div>
                                <div class="event-card-value">{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                    @if($s === 'cancelled' && $event->cancelled_at)
                                        <div style="font-size:10px;color:var(--red);margin-top:1px;">Cancelled {{ \Carbon\Carbon::parse($event->cancelled_at)->setTimezone('Asia/Manila')->format('M d, Y') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="event-card-field">
                                <div class="event-card-label">Scan Mode</div>
                                <div class="event-card-value">
                                    @if(($event->scan_mode ?? 'household') === 'family_head')
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;background:#F5F3FF;color:#6D28D9;border:1px solid #DDD6FE;">👤 Family Head</span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;background:#EAF0FA;color:#1B3F7A;border:1px solid #C7D9F5;">🏠 Household</span>
                                    @endif
                                </div>
                            </div>
                            <div class="event-card-field">
                                <div class="event-card-label">Distributed</div>
                                <div class="event-card-value bold">{{ $event->total_distributed ?? 0 }}</div>
                            </div>
                            <div class="event-card-field">
                                <div class="event-card-label">Households</div>
                                <div class="event-card-value bold" style="color:var(--gray-600);">{{ $event->unique_households ?? 0 }}</div>
                            </div>
                            <div class="event-card-field" style="grid-column:1/-1;border-right:none;">
                                <div class="event-card-label">Barangay</div>
                                <div class="event-card-value" style="display:flex;flex-wrap:wrap;gap:4px;margin-top:2px;">
                                    @forelse($visible as $b)
                                        <span class="brgy-pill">{{ $b }}</span>
                                    @empty
                                        <span style="color:var(--gray-400);font-size:12px;">—</span>
                                    @endforelse
                                    @if(count($hidden) > 0)
                                        <span class="brgy-pill brgy-pill-more" data-tip="{{ implode(', ', $hidden) }}" onmouseenter="showBrgyTip(this)" onmouseleave="hideBrgyTip()">+{{ count($hidden) }} more</span>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($event->relief_type))
                            <div class="event-card-field" style="grid-column:1/-1;border-right:none;border-bottom:none;">
                                <div class="event-card-label">Relief Type</div>
                                <div class="event-card-value hl-relief" style="font-size:11px;color:var(--gray-600);">{{ is_array($event->relief_type) ? implode(', ', $event->relief_type) : $event->relief_type }}</div>
                            </div>
                            @endif
                        </div>
                        <div class="event-card-foot">
                            <button class="btn-view" style="flex:1;justify-content:center;" onclick="openModal({{ $event->id }})">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>View
                            </button>
                            <button class="btn-export" style="flex:1;justify-content:center;"
                                data-event-id="{{ $event->id }}"
                                data-event-name="{{ e($event->event_name) }}"
                                data-barangays="{{ json_encode($event->logs->pluck('household.barangay')->filter()->unique()->sort()->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
                                onclick="openExportModal(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Export
                            </button>
                            @if(!in_array($s, ['cancelled','completed']))
                                <button type="button" class="btn-cancel" style="flex:1;justify-content:center;" onclick="openCancelModal({{ $event->id }}, '{{ addslashes($event->event_name) }}')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>Cancel
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @foreach($events as $event)
                @php
                    $dlogs = $event->logs ?? collect();
                    $logRows = $dlogs->map(function($dlog, $li) {
                        if (!empty($dlog->items_received) && is_array($dlog->items_received)) {
                            $items = implode(', ', array_map(fn($k) => ucwords(str_replace('_',' ',$k)), array_keys($dlog->items_received)));
                        } elseif (!empty($dlog->goods_detail)) {
                            $items = $dlog->goods_detail;
                        } else {
                            $items = '-';
                        }
                        return [
                            'num'      => $li + 1,
                            'head'     => $dlog->household->household_head_name ?? '-',
                            'barangay' => $dlog->household->barangay ?? '-',
                            'serial'   => $dlog->serial_code ?? '-',
                            'items'    => $items,
                            'date'     => $dlog->distributed_at
                                ? \Carbon\Carbon::parse($dlog->distributed_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                                : '-',
                            'staff'    => $dlog->staff->name ?? ('User #' . ($dlog->distributed_by ?? '-')),
                            'search'   => strtolower(($dlog->household->household_head_name ?? '') . ' ' . ($dlog->household->barangay ?? '') . ' ' . ($dlog->serial_code ?? '')),
                        ];
                    })->values()->toArray();

                    $reliefPills = [];
                    if (!empty($event->relief_items) && is_array($event->relief_items)) {
                        foreach ($event->relief_items as $key => $item) {
                            $name = $item['name'] ?? ucwords(str_replace('_',' ',$key));
                            $qty  = !empty($item['qty']) ? ' x ' . $item['qty'] . ' ' . ($item['unit'] ?? '') : '';
                            $reliefPills[] = $name . $qty;
                        }
                    } elseif (!empty($event->relief_type) && is_array($event->relief_type)) {
                        $reliefPills = $event->relief_type;
                    }

                    $barangays = $event->logs->pluck('household.barangay')->filter()->unique()->sort()->values()->toArray();

                    $panelData = [
                        'id'                  => $event->id,
                        'name'                => $event->event_name,
                        'status'              => ucfirst(strtolower($event->status)),
                        'date'                => \Carbon\Carbon::parse($event->event_date)->format('M d, Y'),
                        'createdBy'           => $event->creator->name ?? '-',
                        'createdAt'           => $event->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                        'hhCount'             => $event->logs->unique('household_id')->count(),
                        'logCount'            => $event->logs->count(),
                        'scanMode'            => $event->scan_mode ?? 'household',
                        'reliefPills'         => $reliefPills,
                        'barangays'           => $barangays,
                        'logs'                => $logRows,
                        'cancellationReason'  => $event->cancellation_reason ?? null,
                        'cancelledAt'         => $event->cancelled_at
                            ? \Carbon\Carbon::parse($event->cancelled_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                            : null,
                    ];
                @endphp
                <div id="panel-data-{{ $event->id }}"
                     style="display:none"
                     data-panel="{{ json_encode($panelData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"></div>
                @endforeach

                @if($events->hasPages())
                    <div class="pagination-wrap">
                        <div>Showing {{ $events->firstItem() }}-{{ $events->lastItem() }} of {{ $events->total() }}</div>
                        <div class="links">{{ $events->withQueryString()->links() }}</div>
                    </div>
                @endif
            @endif
        </div>

    </main>

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

<!-- VIEW / HOUSEHOLDS MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div class="modal-head-left">
                <div class="modal-head-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <div>
                    <h2 id="modalTitle">Event Households</h2>
                    <div class="modal-head-sub" id="modalSub">Loading details...</div>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <div style="display:flex;align-items:center;justify-content:center;height:120px;color:var(--gray-400);font-size:13px;gap:10px;">
                <div class="spinner"></div> Loading households...
            </div>
        </div>
    </div>
</div>

<!-- EXPORT OPTIONS MODAL -->
<div class="export-modal-overlay" id="exportModalOverlay" onclick="closeExportModal()">
    <div class="export-modal-box" onclick="event.stopPropagation()">
        <div class="export-modal-head">
            <div class="export-modal-head-left">
                <div class="export-modal-head-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </div>
                <div>
                    <h2 id="exportModalTitle">Export Distribution Data</h2>
                    <div class="export-modal-head-sub" id="exportModalSub">Choose columns and filters before downloading</div>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeExportModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="export-modal-body">
            <div class="export-section">
                <div class="export-section-title">Filters <span style="font-weight:400;color:var(--gray-400);text-transform:none;letter-spacing:0;font-size:10px;">(optional - leave blank to include all)</span></div>
                <div class="export-filters">
                    <div class="export-filter-group">
                        <div class="export-filter-label">Barangay</div>
                        <select id="exportBarangay">
                            <option value="">All Barangays</option>
                        </select>
                    </div>
                    <div class="export-filter-group">
                        <div class="export-filter-label">Date Distributed From</div>
                        <input type="date" id="exportDateFrom">
                    </div>
                    <div class="export-filter-group">
                        <div class="export-filter-label">Date Distributed To</div>
                        <input type="date" id="exportDateTo">
                    </div>
                </div>
            </div>

            <div class="export-section">
                <div class="export-section-title">
                    Distribution Log Columns
                    <span>
                        <a onclick="toggleGroup('log', true)">Select All</a> &nbsp;·&nbsp;
                        <a onclick="toggleGroup('log', false)">Deselect All</a>
                    </span>
                </div>
                <div class="export-cols-grid" id="logColsGrid">
                    @php
                        $logCols = [
                            'serial_code'     => 'Serial Code',
                            'items_received'  => 'Items Received',
                            'distributed_at'  => 'Date Distributed',
                            'distributed_by'  => 'Distributed By (Staff)',
                        ];
                    @endphp
                    @foreach($logCols as $key => $label)
                        <div class="export-col-toggle checked" data-group="log" onclick="this.querySelector('input').click()">
                            <input type="checkbox" name="log_cols[]" value="{{ $key }}" checked onchange="syncToggleStyle(this)" onclick="event.stopPropagation()">
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="export-section">
                <div class="export-section-title">
                    Household Profile Columns
                    <span>
                        <a onclick="toggleGroup('hh', true)">Select All</a> &nbsp;·&nbsp;
                        <a onclick="toggleGroup('hh', false)">Deselect All</a>
                    </span>
                </div>
                <div class="export-cols-grid" id="hhColsGrid">
                    @php
                        $hhCols = [
                            'household_head_name' => 'Household Head Name',
                            'national_id'        => 'Listahan ID',
                            'contact_number'       => 'Contact Number',
                            'email'                => 'Email',
                            'street_purok'         => 'Street / Purok',
                            'location'             => 'Location / Address',
                            'barangay'             => 'Barangay',
                            'municipality'         => 'Municipality',
                            'province'             => 'Province',
                            'housing_type'         => 'Housing Type',
                            'housing_material'     => 'Housing Material',
                            'ownership_type'       => 'Ownership Type',
                            'electricity_source'   => 'Electricity Source',
                            'water_source'         => 'Water Source',
                            'toilet_access'        => 'Toilet Access',
                            'waste_disposal'       => 'Waste Disposal',
                            'is_4ps_beneficiary'   => 'Is 4Ps Beneficiary',
                            'is_pwd'               => 'Is PWD',
                            'is_senior'            => 'Is Senior',
                            'is_solo_parent'       => 'Is Solo Parent',
                            'status'               => 'Status',
                            'encoded_by'           => 'Encoded By',
                            'approved_by'          => 'Approved By',
                            'created_at'           => 'Created At',
                            'updated_at'           => 'Updated At',
                        ];
                    @endphp
                    @foreach($hhCols as $key => $label)
                        <div class="export-col-toggle checked" data-group="hh" onclick="this.querySelector('input').click()">
                            <input type="checkbox" name="hh_cols[]" value="{{ $key }}" checked onchange="syncToggleStyle(this)" onclick="event.stopPropagation()">
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="export-section">
                <div class="export-section-title">
                    Family Head Columns
                    <span>
                        <a onclick="toggleGroup('fh', true)">Select All</a> &nbsp;·&nbsp;
                        <a onclick="toggleGroup('fh', false)">Deselect All</a>
                    </span>
                </div>
                <div class="export-cols-grid" id="fhColsGrid">
                    @php
                        $fhCols = [
                            'fh_name'                   => 'Family Head Name',
                            'fh_sex'                    => 'Sex',
                            'fh_birthday'               => 'Birthday',
                            'fh_civil_status'           => 'Civil Status',
                            'fh_occupation'             => 'Occupation',
                            'fh_educational_attainment' => 'Educational Attainment',
                        ];
                    @endphp
                    @foreach($fhCols as $key => $label)
                        <div class="export-col-toggle checked" data-group="fh" onclick="this.querySelector('input').click()">
                            <input type="checkbox" name="fh_cols[]" value="{{ $key }}" checked onchange="syncToggleStyle(this)" onclick="event.stopPropagation()">
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="export-section">
                <div class="export-section-title">
                    Risk Profile Columns
                    <span>
                        <a onclick="toggleGroup('risk', true)">Select All</a> &nbsp;·&nbsp;
                        <a onclick="toggleGroup('risk', false)">Deselect All</a>
                    </span>
                </div>
                <div class="export-cols-grid" id="riskColsGrid">
                    @php
                        $riskCols = [
                            'income_average'       => 'Monthly Income (Avg)',
                            'early_warning'        => 'Early Warning System',
                            'hazard_awareness'     => 'Hazard Awareness',
                            'financial_assistance' => 'Financial Assistance',
                            'access_info'          => 'Access to Info',
                            'relocate_willingness' => 'Willing to Relocate',
                        ];
                    @endphp
                    @foreach($riskCols as $key => $label)
                        <div class="export-col-toggle checked" data-group="risk" onclick="this.querySelector('input').click()">
                            <input type="checkbox" name="risk_cols[]" value="{{ $key }}" checked onchange="syncToggleStyle(this)" onclick="event.stopPropagation()">
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="export-modal-footer">
            <div class="export-info">
                <strong id="exportColCount">42</strong> columns selected &nbsp;·&nbsp; XLSX format
            </div>
            <div class="export-footer-btns">
                <button class="btn-export-cancel" onclick="closeExportModal()">Cancel</button>
                <button class="btn-do-export" id="btnDoExport" onclick="doExport()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download XLSX
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CANCEL EVENT MODAL - Uses POST route, no HTTP verb spoofing needed -->
<div class="cancel-modal-overlay" id="cancelModalOverlay" onclick="closeCancelModal()">
    <div class="cancel-modal-box" onclick="event.stopPropagation()">
        <div class="cancel-modal-head">
            <div class="cancel-modal-head-left">
                <div class="cancel-modal-head-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div>
                    <h2>Cancel Distribution Event</h2>
                    <div class="cancel-modal-sub" id="cancelModalSub">Provide a reason before confirming</div>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeCancelModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="cancelEventForm" method="POST" action="">
            @csrf
            {{-- route is POST, no method spoofing needed --}}

            <div class="cancel-modal-body">

                {{-- Warning banner --}}
                <div class="cancel-warning-banner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" style="flex-shrink:0;margin-top:1px;">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <div>
                        <strong>This action cannot be undone.</strong>
                        <span>The event will be permanently marked as <strong>Cancelled</strong> and the reason will be saved to the record.</span>
                    </div>
                </div>

                {{-- Reason preset select --}}
                <div class="cancel-field-group">
                    <label class="cancel-field-label" for="cancelReasonSelect">
                        Cancellation Reason <span style="color:var(--red)">*</span>
                    </label>
                    <select id="cancelReasonSelect" name="cancellation_reason_preset"
                            class="cancel-select"
                            onchange="handleReasonChange(this)" required>
                        <option value="">- Select a reason -</option>
                        <option value="Weather conditions (typhoon/flood)">Weather conditions (typhoon/flood)</option>
                        <option value="Insufficient relief goods or supplies">Insufficient relief goods or supplies</option>
                        <option value="Logistics or transport issues">Logistics or transport issues</option>
                        <option value="Security concerns in the area">Security concerns in the area</option>
                        <option value="Personnel unavailability">Personnel unavailability</option>
                        <option value="Barangay official request">Barangay official request</option>
                        <option value="Event rescheduled">Event rescheduled (update via Edit to set new date)</option>
                        <option value="Other">Other (please specify below)</option>
                    </select>
                </div>

                {{-- Additional details textarea --}}
                <div class="cancel-field-group">
                    <label class="cancel-field-label" for="cancelReasonText">
                        Additional Details
                        <span id="cancelDescRequired" style="color:var(--red);display:none;"> *</span>
                        <em id="cancelDescHint">(optional unless "Other" is selected)</em>
                    </label>
                    <textarea id="cancelReasonText"
                              name="cancellation_reason"
                              class="cancel-textarea"
                              rows="3"
                              maxlength="500"
                              placeholder="Describe the circumstances in more detail..."></textarea>
                    <div class="cancel-char-count"><span id="cancelCharCount">0</span>/500</div>
                </div>

            </div>{{-- /.cancel-modal-body --}}

            <div class="cancel-modal-footer">
                <div style="font-size:11px;color:var(--gray-400);">
                    Cancellation timestamp will be recorded automatically.
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" class="btn-export-cancel" onclick="closeCancelModal()">Go Back</button>
                    <button type="submit" class="btn-do-cancel" id="btnDoCancel" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        Confirm Cancellation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Barangay tooltip -->
<div id="brgyTooltip" style="display:none;"></div>

<script>
    /* --- Search scope --- */
    const SEARCH_PH = {
        all:        'Search event name, relief type, barangay, status...',
        event_name: 'Search by event name...',
        relief:     'e.g. Rice, Canned Goods...',
        barangay:   'Search by barangay...',
        status:     'e.g. Upcoming, Ongoing, Completed...',
        date:       'e.g. 2025-06-15...',
    };
    function updateScope(val) {
        document.getElementById('scopeHidden').value = val;
        const input = document.getElementById('searchInput');
        if (input) input.placeholder = SEARCH_PH[val] || 'Search...';
    }

    function onSearchType(input) {
        const combo = document.getElementById('searchCombo');
        if (combo) combo.classList.toggle('has-value', input.value.length > 0);
        applyHighlight(input.value, document.getElementById('scopeSelect').value);
    }
    function clearSearch() {
        const input = document.getElementById('searchInput');
        if (!input) return;
        input.value = '';
        const combo = document.getElementById('searchCombo');
        if (combo) combo.classList.remove('has-value');
        removeHighlight();
        document.getElementById('filterForm').submit();
    }

    const HL_SCOPE = {
        all:        ['hl-event','hl-relief','hl-status'],
        event_name: ['hl-event'],
        relief:     ['hl-relief'],
        barangay:   [],
        status:     ['hl-status'],
        date:       [],
    };
    function _re(s) { return s.replace(/[-[\]{}()*+?.,\^$|#\s]/g, '\$&'); }
    function applyHighlight(term, scope) {
        removeHighlight();
        if (!term) return;
        const re = new RegExp('(' + _re(term) + ')', 'gi');
        (HL_SCOPE[scope] || HL_SCOPE.all).forEach(cls => {
            document.querySelectorAll('.' + cls).forEach(el => {
                el.innerHTML = el.textContent.replace(re, '<mark class="hl">$1</mark>');
            });
        });
    }
    function removeHighlight() {
        document.querySelectorAll('mark.hl').forEach(m => {
            m.parentNode.replaceChild(document.createTextNode(m.textContent), m);
            m.parentNode.normalize();
        });
    }

    /* Init on load */
    (function() {
        const input = document.getElementById('searchInput');
        const scope = document.getElementById('scopeSelect');
        if (input && input.value) {
            const combo = document.getElementById('searchCombo');
            if (combo) combo.classList.add('has-value');
            applyHighlight(input.value, scope ? scope.value : 'all');
        }
        if (scope) updateScope(scope.value);
    })();

    /* --- Clock --- */
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now    = new Date();
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

    /* --- Sidebar --- */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }

    /* --- Panel data helper --- */
    function getPanelData(eventId) {
        const el = document.getElementById('panel-data-' + eventId);
        if (!el) return null;
        try { return JSON.parse(el.dataset.panel); }
        catch(e) { console.error('panel-data parse error for event ' + eventId, e); return null; }
    }

    function esc(str) {
        return String(str ?? '-')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function buildModalHTML(d) {
        const statusClass = 'badge-' + d.status.toLowerCase();

        let pillsHTML = '';
        if (d.reliefPills && d.reliefPills.length) {
            pillsHTML = `<div style="margin-top:12px;padding-top:12px;border-top:1px solid #C5D9F5;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;margin-bottom:6px;">Relief Items</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    ${d.reliefPills.map(p => `<span style="background:#fff;border:1px solid #C5D9F5;border-radius:3px;padding:3px 10px;font-size:11px;font-weight:600;color:#122D5A;">${esc(p)}</span>`).join('')}
                </div></div>`;
        }

        /* Cancellation reason block - shown inside modal for cancelled events */
        let cancelHTML = '';
        if (d.status.toLowerCase() === 'cancelled' && d.cancellationReason) {
            cancelHTML = `
            <div style="margin-top:10px;padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-left:4px solid #C0392B;border-radius:2px;display:flex;align-items:flex-start;gap:8px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#C0392B" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <div>
                    <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#C0392B;margin-bottom:2px;">Cancellation Reason</div>
                    <div style="font-size:12px;color:#7f1d1d;line-height:1.5;">${esc(d.cancellationReason)}</div>
                    ${d.cancelledAt ? `<div style="font-size:10px;color:#9AA3B0;margin-top:4px;">Cancelled on ${esc(d.cancelledAt)}</div>` : ''}
                </div>
            </div>`;
        }

        const infoHTML = `
        <div style="background:#EAF0FA;border:1px solid #C5D9F5;border-left:4px solid #1B3F7A;padding:14px 16px;margin-bottom:16px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9AA3B0;margin-bottom:4px;">Event Details</div>
                    <div style="font-family:'PT Serif',serif;font-size:15px;font-weight:700;color:#122D5A;margin-bottom:8px;">${esc(d.name)}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;">
                        <div><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;">Event Date</div>
                             <div style="font-size:12px;font-weight:600;color:#2C3340;margin-top:2px;">${esc(d.date)}</div></div>
                        <div><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;">Created By</div>
                             <div style="font-size:12px;font-weight:600;color:#2C3340;margin-top:2px;">${esc(d.createdBy)}</div></div>
                        <div><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;">Created At</div>
                             <div style="font-size:12px;font-weight:600;color:#2C3340;margin-top:2px;">${esc(d.createdAt)}</div></div>
                        <div><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;">Households Reached</div>
                             <div style="font-size:12px;font-weight:600;color:#122D5A;margin-top:2px;">${d.hhCount} household(s)</div></div>
                        <div><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;">Total Distributions</div>
                             <div style="font-size:12px;font-weight:600;color:#122D5A;margin-top:2px;">${d.logCount} record(s)</div></div>
                        <div><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;">Scan Mode</div>
                             <div style="margin-top:4px;">${d.scanMode === 'family_head'
                                ? '<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;background:#F5F3FF;color:#6D28D9;border:1px solid #DDD6FE;">👤 Per Family Head</span>'
                                : '<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;background:#EAF0FA;color:#1B3F7A;border:1px solid #C7D9F5;">🏠 Per Household</span>'
                             }</div></div>
                    </div>
                </div>
                <span class="badge ${statusClass}" style="flex-shrink:0;align-self:flex-start;">${esc(d.status)}</span>
            </div>
            ${pillsHTML}
            ${cancelHTML}
        </div>`;

        const searchExportRow = `
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
            <input type="text" id="modalSearchInput" placeholder="Search household name, barangay, or serial code..."
                style="flex:1;min-width:200px;padding:8px 12px;border:1px solid #DEE2E8;border-radius:4px;font-size:13px;font-family:'Open Sans',sans-serif;outline:none;">
            <button type="button" class="btn-export" style="flex-shrink:0;" onclick="openExportModalFromPanel(${d.id})">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="pointer-events:none">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export Excel
            </button>
        </div>`;

        let tableHTML = '';
        if (!d.logs || d.logs.length === 0) {
            tableHTML = `<div style="text-align:center;padding:40px;color:#9AA3B0;font-size:13px;font-style:italic;">No households have been distributed to yet for this event.</div>`;
        } else {
            const rows = d.logs.map(r => `
                <tr class="modal-hh-row" data-search="${esc(r.search)}" style="border-bottom:1px solid #F0F2F5;">
                    <td style="padding:10px 12px;color:#9AA3B0;font-size:12px;">${r.num}</td>
                    <td style="padding:10px 12px;font-weight:600;color:#122D5A;">${esc(r.head)}</td>
                    <td style="padding:10px 12px;font-size:12px;color:#5A6372;">${esc(r.barangay)}</td>
                    <td style="padding:10px 12px;font-size:12px;font-family:monospace;color:#1B3F7A;font-weight:700;letter-spacing:.5px;">${esc(r.serial)}</td>
                    <td style="padding:10px 12px;font-size:12px;color:#5A6372;">${esc(r.items)}</td>
                    <td style="padding:10px 12px;font-size:12px;white-space:nowrap;">${esc(r.date)}</td>
                    <td style="padding:10px 12px;font-size:12px;color:#5A6372;">${esc(r.staff)}</td>
                </tr>`).join('');

            tableHTML = `
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead><tr style="background:#1B3F7A;color:#fff;">
                    <th style="padding:9px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">#</th>
                    <th style="padding:9px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Household Head</th>
                    <th style="padding:9px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Barangay</th>
                    <th style="padding:9px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Serial Code</th>
                    <th style="padding:9px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Items Received</th>
                    <th style="padding:9px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Date Distributed</th>
                    <th style="padding:9px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Staff</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>
            <div style="margin-top:10px;font-size:11px;color:#9AA3B0;text-align:right;">${d.logs.length} household(s) distributed</div>`;
        }

        return infoHTML + searchExportRow + tableHTML;
    }

    /* --- View Modal --- */
    function openModal(eventId) {
        const d = getPanelData(eventId);
        if (!d) {
            document.getElementById('modalTitle').textContent = 'Event Details';
            document.getElementById('modalSub').textContent   = 'Could not load data.';
            document.getElementById('modalBody').innerHTML    = '<p style="color:#9AA3B0;padding:20px;text-align:center;">No data available. Try refreshing the page.</p>';
            document.getElementById('modalOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
            return;
        }
        document.getElementById('modalTitle').textContent = d.name;
        document.getElementById('modalSub').textContent   = 'Status: ' + d.status + ' - Household recipients';
        document.getElementById('modalBody').innerHTML    = buildModalHTML(d);
        const inp = document.getElementById('modalSearchInput');
        if (inp) {
            inp.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('#modalBody .modal-hh-row').forEach(row => {
                    row.style.display = row.dataset.search.includes(q) ? '' : 'none';
                });
            });
        }
        document.getElementById('modalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    /* --- Export Modal --- */
    let currentExportEventId = null;

    function _populateExportModal(eventId, eventName, barangays) {
        currentExportEventId = eventId;
        document.getElementById('exportModalTitle').textContent = 'Export - ' + eventName;
        document.getElementById('exportModalSub').textContent   = 'Customise columns and filters before downloading';
        document.getElementById('exportDateFrom').value = '';
        document.getElementById('exportDateTo').value   = '';
        const sel = document.getElementById('exportBarangay');
        sel.innerHTML = '<option value="">All Barangays</option>';
        (barangays || []).forEach(b => {
            const opt = document.createElement('option');
            opt.value = b; opt.textContent = b;
            sel.appendChild(opt);
        });
        document.querySelectorAll('.export-col-toggle input[type="checkbox"]').forEach(cb => {
            cb.checked = true; syncToggleStyle(cb);
        });
        updateExportColCount();
        document.getElementById('exportModalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function openExportModal(btn) {
        const barangays = JSON.parse(btn.dataset.barangays || '[]');
        _populateExportModal(btn.dataset.eventId, btn.dataset.eventName, barangays);
    }

    function openExportModalFromPanel(eventId) {
        const d = getPanelData(eventId);
        if (!d) return;
        _populateExportModal(eventId, d.name, d.barangays);
    }

    function closeExportModal() {
        document.getElementById('exportModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function syncToggleStyle(cb) {
        const toggle = cb.closest('.export-col-toggle');
        if (toggle) toggle.classList.toggle('checked', cb.checked);
        updateExportColCount();
    }
    function toggleGroup(group, checked) {
        document.querySelectorAll(`[data-group="${group}"] input[type="checkbox"]`).forEach(cb => {
            cb.checked = checked; syncToggleStyle(cb);
        });
    }
    function updateExportColCount() {
        const count = document.querySelectorAll(
            'input[name="log_cols[]"]:checked, input[name="hh_cols[]"]:checked, input[name="fh_cols[]"]:checked, input[name="risk_cols[]"]:checked'
        ).length;
        document.getElementById('exportColCount').textContent = count;
        document.getElementById('btnDoExport').disabled = count === 0;
    }

    function doExport() {
        if (!currentExportEventId) return;
        const logCols  = [...document.querySelectorAll('input[name="log_cols[]"]:checked')].map(c => c.value);
        const hhCols   = [...document.querySelectorAll('input[name="hh_cols[]"]:checked')].map(c => c.value);
        const fhCols   = [...document.querySelectorAll('input[name="fh_cols[]"]:checked')].map(c => c.value);
        const riskCols = [...document.querySelectorAll('input[name="risk_cols[]"]:checked')].map(c => c.value);
        const barangay = document.getElementById('exportBarangay').value;
        const dateFrom = document.getElementById('exportDateFrom').value;
        const dateTo   = document.getElementById('exportDateTo').value;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.distribution.events.export.custom.xlsx", ["event" => "__ID__"]) }}'.replace('__ID__', currentExportEventId);
        form.target = '_blank';
        form.style.display = 'none';
        const addField = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = name; input.value = value;
            form.appendChild(input);
        };
        addField('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        logCols.forEach(v    => addField('log_cols[]', v));
        hhCols.forEach(v     => addField('hh_cols[]', v));
        fhCols.forEach(v     => addField('fh_cols[]', v));
        riskCols.forEach(v   => addField('risk_cols[]', v));
        if (barangay) addField('barangay', barangay);
        if (dateFrom) addField('date_from', dateFrom);
        if (dateTo)   addField('date_to', dateTo);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        closeExportModal();
    }

    /* CANCEL MODAL - Route must be: Route::post(...) */
    function openCancelModal(eventId, eventName) {
        // Set form action - route must be POST in web.php
        document.getElementById('cancelEventForm').action =
            '{{ url("admin/distribution/events") }}/' + eventId + '/cancel';
        document.getElementById('cancelModalSub').textContent = '<< ' + eventName + ' >>';

        // Reset fields
        document.getElementById('cancelReasonSelect').value = '';
        document.getElementById('cancelReasonText').value   = '';
        document.getElementById('cancelCharCount').textContent = '0';
        document.getElementById('cancelDescRequired').style.display = 'none';
        document.getElementById('cancelDescHint').textContent = '(optional unless "Other" is selected)';
        document.getElementById('btnDoCancel').disabled = true;

        document.getElementById('cancelModalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCancelModal() {
        document.getElementById('cancelModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function handleReasonChange(sel) {
        const isOther  = sel.value === 'Other';
        const reqStar  = document.getElementById('cancelDescRequired');
        const hint     = document.getElementById('cancelDescHint');
        reqStar.style.display = isOther ? 'inline' : 'none';
        hint.textContent = isOther ? '(required)' : '(optional unless "Other" is selected)';
        if (isOther) document.getElementById('cancelReasonText').focus();
        validateCancelForm();
    }

    function validateCancelForm() {
        const preset  = document.getElementById('cancelReasonSelect').value;
        const text    = document.getElementById('cancelReasonText').value.trim();
        const isOther = preset === 'Other';
        const valid   = preset !== '' && (!isOther || text.length > 0);
        document.getElementById('btnDoCancel').disabled = !valid;
    }

    document.getElementById('cancelReasonText').addEventListener('input', function() {
        document.getElementById('cancelCharCount').textContent = this.value.length;
        validateCancelForm();
    });
    document.getElementById('cancelReasonSelect').addEventListener('change', validateCancelForm);

    /* --- Global keyboard close --- */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeSidebar();
            closeModal();
            closeExportModal();
            closeCancelModal();
        }
    });

    /* Auto-open modal when arriving from dashboard map pin (#event-{id}) */
    (function() {
        const hash = window.location.hash;
        if (!hash.startsWith('#event-')) return;
        const eventId = parseInt(hash.replace('#event-', ''), 10);
        if (!eventId) return;
        const mainContent = document.getElementById('mainContent');
        const row = document.getElementById('event-' + eventId);
        if (row && mainContent) {
            const rowTop = row.offsetTop - mainContent.offsetTop - 80;
            mainContent.scrollTo({ top: rowTop, behavior: 'smooth' });
            row.classList.add('pin-highlight');
            setTimeout(() => row.classList.remove('pin-highlight'), 2500);
        }
        setTimeout(() => openModal(eventId), 400);
    })();

    /* -- Barangay pill tooltip -- */
    const brgyTip = document.getElementById('brgyTooltip');
    function showBrgyTip(el) {
        brgyTip.textContent = el.dataset.tip.split(', ').join('\n');
        brgyTip.style.display = 'block';
        document.addEventListener('mousemove', moveBrgyTip);
    }
    function hideBrgyTip() {
        brgyTip.style.display = 'none';
        document.removeEventListener('mousemove', moveBrgyTip);
    }
    function moveBrgyTip(e) {
        const p = 14;
        let x = e.clientX + p, y = e.clientY + p;
        if (x + 280 > window.innerWidth)  x = e.clientX - 280 - p;
        if (y + 100 > window.innerHeight) y = e.clientY - 60;
        brgyTip.style.left = x + 'px';
        brgyTip.style.top  = y + 'px';
    }
</script>
</body>
</html>