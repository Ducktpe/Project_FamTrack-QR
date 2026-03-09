{{-- resources/views/admin/distribution/logs.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Distribution Logs</title>
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

        /* ─── TOP UTILITY BAR ─── */
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); letter-spacing: 0.3px; }
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
        .header-org { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }

        /* ── Admin badge (blue, matches dashboard) ── */
        .header-admin-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid var(--gray-200); border-radius: 4px; flex-shrink: 0; }
        .admin-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .admin-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .admin-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
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

        /* ─── MAIN ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        /* ─── PAGE TITLEBAR ─── */
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; flex-wrap: wrap; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── SUMMARY CARDS ─── */
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

        /* ─── FILTER BOX ─── */
        .filter-box { background: var(--white); border: 1px solid var(--gray-200); padding: 16px 20px; margin-bottom: 16px; }
        .filter-box-header { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 12px; }
        .filters { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap: 10px; align-items: end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }
        .filter-box input[type="text"],
        .filter-box input[type="date"],
        .filter-box select,
        .modal-body input[type="text"],
        .export-filter-group input[type="date"],
        .export-filter-group select {
            width: 100%; padding: 8px 10px; border: 1px solid var(--gray-200); border-radius: 3px;
            font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); background: var(--white); outline: none;
        }
        .filter-box input:focus, .filter-box select:focus,
        .export-filter-group input:focus, .export-filter-group select:focus,
        .modal-body input:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.1); }
        .filter-box input::placeholder, .modal-body input::placeholder { color: var(--gray-400); }
        .btn-filter { padding: 8px 16px; background: var(--blue); color: var(--white); border: none; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background 0.15s; white-space: nowrap; align-self: end; }
        .btn-filter:hover { background: var(--blue-dark); }
        .btn-clear { padding: 8px 14px; background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-decoration: none; white-space: nowrap; align-self: end; display: inline-block; text-align: center; transition: background 0.15s; }
        .btn-clear:hover { background: var(--gray-100); }

        /* ─── TABLE ─── */
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

        /* ─── MODAL ─── */
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

        /* ─── EXPORT MODAL ─── */
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

        /* Column group sections */
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

        /* Filter row */
        .export-filters { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 18px; }
        .export-filter-group { display: flex; flex-direction: column; gap: 4px; }
        .export-filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }

        /* Footer btns */
        .export-info { font-size: 11px; color: var(--gray-400); }
        .export-info strong { color: var(--blue-dark); }
        .export-footer-btns { display: flex; align-items: center; gap: 8px; }
        .btn-do-export { display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px; background: var(--green); color: var(--white); border: none; border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background 0.15s; }
        .btn-do-export:hover { background: var(--green-dark); }
        .btn-do-export:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-export-cancel { padding: 9px 16px; background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 3px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
        .btn-export-cancel:hover { background: var(--gray-100); }

        /* ─── RESPONSIVE ─── */
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
        <div class="header-admin-badge">
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="admin-name">{{ auth()->user()->name }}</div>
                <div class="admin-role">Full Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR — Admin Menu (matches dashboard) -->
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

        <!-- Active: Distribution Logs -->
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
                    <a href="{{ route('admin.dashboard') }}">Home</a> / <span>Distribution Logs</span>
                </div>
                <div class="page-h1">Distribution Logs</div>
                <div class="page-sub">View and manage all relief distribution events — Barangay Family Track QR System</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

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
        <div class="filter-box">
            <div class="filter-box-header">Filter Events</div>
            <form method="GET">
                <div class="filters">
                    <div class="filter-group">
                        <div class="filter-label">Search</div>
                        <input type="text" name="search" placeholder="Event name, relief type…" value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">From Date</div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">To Date</div>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">Status</div>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="upcoming"  {{ request('status')=='upcoming'  ? 'selected':'' }}>Upcoming</option>
                            <option value="ongoing"   {{ request('status')=='ongoing'   ? 'selected':'' }}>Ongoing</option>
                            <option value="completed" {{ request('status')=='completed' ? 'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-filter">Filter</button>
                    <a href="{{ route('admin.distribution.logs') }}" class="btn-clear">Clear</a>
                </div>
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
                                <th>Date</th>
                                <th>Status</th>
                                <th style="text-align:center">Distributed</th>
                                <th style="text-align:center">Households</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $i => $event)
                                <tr>
                                    <td style="color:var(--gray-400); font-size:12px;">{{ $events->firstItem() + $i }}</td>
                                    <td>
                                        <div style="font-weight:600; color:var(--blue-dark);">{{ $event->event_name }}</div>
                                        @if($event->description)
                                            <div style="font-size:11px; color:var(--gray-400); margin-top:2px;">{{ Str::limit($event->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td style="font-size:12px; color:var(--gray-600);">{{ is_array($event->relief_type) ? implode(', ', $event->relief_type) : ($event->relief_type ?? '—') }}</td>
                                    <td style="font-size:12px; white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                    </td>
                                    <td>
                                        @php $s = strtolower($event->status); @endphp
                                        <span class="badge badge-{{ $s }}">{{ ucfirst($s) }}</span>
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:var(--blue-dark);">
                                        {{ $event->total_distributed ?? 0 }}
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:var(--gray-600);">
                                        {{ $event->unique_households ?? 0 }}
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            {{-- FIX 1: View button now only passes the numeric ID.
                                                 Name/status are read safely from data-panel inside openModal(). --}}
                                            <button class="btn-view" onclick="openModal({{ $event->id }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View
                                            </button>
                                            {{-- FIX 2: Export button data-barangays now uses e() instead of
                                                 htmlspecialchars(..., ENT_QUOTES) so JSON.parse() works correctly. --}}
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
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- FIX 3: Panel data stored in data-panel attribute via e(json_encode()).
                     This is immune to apostrophes and special chars in household names.
                     The browser automatically unescapes dataset values before JSON.parse(). --}}
                @foreach($events as $event)
                @php
                    $dlogs = $event->logs ?? collect();
                    $logRows = $dlogs->map(function($dlog, $li) {
                        if (!empty($dlog->items_received) && is_array($dlog->items_received)) {
                            $items = implode(', ', array_map(fn($k) => ucwords(str_replace('_',' ',$k)), array_keys($dlog->items_received)));
                        } elseif (!empty($dlog->goods_detail)) {
                            $items = $dlog->goods_detail;
                        } else {
                            $items = '—';
                        }
                        return [
                            'num'      => $li + 1,
                            'head'     => $dlog->household->household_head_name ?? '—',
                            'barangay' => $dlog->household->barangay ?? '—',
                            'serial'   => $dlog->serial_code ?? '—',
                            'items'    => $items,
                            'date'     => $dlog->distributed_at
                                ? \Carbon\Carbon::parse($dlog->distributed_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                                : '—',
                            'staff'    => $dlog->staff->name ?? ('User #' . ($dlog->distributed_by ?? '—')),
                            'search'   => strtolower(($dlog->household->household_head_name ?? '') . ' ' . ($dlog->household->barangay ?? '') . ' ' . ($dlog->serial_code ?? '')),
                        ];
                    })->values()->toArray();

                    $reliefPills = [];
                    if (!empty($event->relief_items) && is_array($event->relief_items)) {
                        foreach ($event->relief_items as $key => $item) {
                            $name = $item['name'] ?? ucwords(str_replace('_',' ',$key));
                            $qty  = !empty($item['qty']) ? ' × ' . $item['qty'] . ' ' . ($item['unit'] ?? '') : '';
                            $reliefPills[] = $name . $qty;
                        }
                    } elseif (!empty($event->relief_type) && is_array($event->relief_type)) {
                        $reliefPills = $event->relief_type;
                    }

                    $barangays = $event->logs->pluck('household.barangay')->filter()->unique()->sort()->values()->toArray();

                    $panelData = [
                        'id'          => $event->id,
                        'name'        => $event->event_name,
                        'status'      => ucfirst(strtolower($event->status)),
                        'date'        => \Carbon\Carbon::parse($event->event_date)->format('M d, Y'),
                        'createdBy'   => $event->creator->name ?? '—',
                        'createdAt'   => $event->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                        'hhCount'     => $event->logs->unique('household_id')->count(),
                        'logCount'    => $event->logs->count(),
                        'reliefPills' => $reliefPills,
                        'barangays'   => $barangays,
                        'logs'        => $logRows,
                    ];
                @endphp
                <div id="panel-data-{{ $event->id }}"
                     style="display:none"
                     data-panel="{{ json_encode($panelData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"></div>
                @endforeach

                @if($events->hasPages())
                    <div class="pagination-wrap">
                        <div>Showing {{ $events->firstItem() }}–{{ $events->lastItem() }} of {{ $events->total() }}</div>
                        <div class="links">{{ $events->withQueryString()->links() }}</div>
                    </div>
                @endif
            @endif
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

<!-- MODAL -->
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
                    <div class="modal-head-sub" id="modalSub">Loading details…</div>
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
                <div class="spinner"></div> Loading households…
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

            {{-- Filters --}}
            <div class="export-section">
                <div class="export-section-title">Filters <span style="font-weight:400;color:var(--gray-400);text-transform:none;letter-spacing:0;font-size:10px;">(optional — leave blank to include all)</span></div>
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

            {{-- Distribution Log Columns --}}
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

            {{-- Household Profile Columns --}}
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
                            'id'                 => 'ID',
                            'qr_code_path'       => 'QR Code Path',
                            'household_head_name'=> 'Household Head Name',
                            'sex'                => 'Sex',
                            'birthday'           => 'Birthday',
                            'civil_status'       => 'Civil Status',
                            'contact_number'     => 'Contact Number',
                            'house_number'       => 'House Number',
                            'street_purok'       => 'Street / Purok',
                            'barangay'           => 'Barangay',
                            'municipality'       => 'Municipality',
                            'province'           => 'Province',
                            'listahanan_id'      => 'Listahan ID',
                            'is_4ps_beneficiary' => 'Is 4Ps Beneficiary',
                            'is_pwd'             => 'Is PWD',
                            'is_senior'          => 'Is Senior',
                            'is_solo_parent'     => 'Is Solo Parent',
                            'status'             => 'Status',
                            'encoded_by'         => 'Encoded By',
                            'approved_by'        => 'Approved By',
                            'created_at'         => 'Created At',
                            'updated_at'         => 'Updated At',
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

        </div>

        <div class="export-modal-footer">
            <div class="export-info">
                <strong id="exportColCount">28</strong> columns selected &nbsp;·&nbsp; XLSX format
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

<script>
    /* ─── Clock ─── */
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

    /* ─── Sidebar ─── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }

    /* ─── Panel data helper ─── */
    function getPanelData(eventId) {
        const el = document.getElementById('panel-data-' + eventId);
        if (!el) return null;
        try {
            // Browser auto-unescapes data-* attribute values, so JSON.parse
            // always receives clean text regardless of apostrophes in names.
            return JSON.parse(el.dataset.panel);
        } catch(e) {
            console.error('panel-data parse error for event ' + eventId, e);
            return null;
        }
    }

    function esc(str) {
        return String(str ?? '—')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function buildModalHTML(d) {
        const statusClass = 'badge-' + d.status.toLowerCase();

        // Relief pills
        let pillsHTML = '';
        if (d.reliefPills && d.reliefPills.length) {
            pillsHTML = `<div style="margin-top:12px;padding-top:12px;border-top:1px solid #C5D9F5;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9AA3B0;margin-bottom:6px;">Relief Items</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    ${d.reliefPills.map(p => `<span style="background:#fff;border:1px solid #C5D9F5;border-radius:3px;padding:3px 10px;font-size:11px;font-weight:600;color:#122D5A;">${esc(p)}</span>`).join('')}
                </div></div>`;
        }

        // Info header
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
                    </div>
                </div>
                <span class="badge ${statusClass}" style="flex-shrink:0;align-self:flex-start;">${esc(d.status)}</span>
            </div>
            ${pillsHTML}
        </div>`;

        // FIX: Export button inside modal now calls openExportModalFromPanel(id)
        // instead of passing data through HTML attributes — avoids all escaping issues.
        const searchExportRow = `
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
            <input type="text" id="modalSearchInput" placeholder="Search household name, barangay, or serial code…"
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

        // Log table
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

    /* ─── View Modal ─── */
    // FIX: openModal now only takes eventId. Name and status come from panel data,
    // so no special characters in event names can ever break the JS call.
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
        document.getElementById('modalSub').textContent   = 'Status: ' + d.status + ' — Household recipients';
        document.getElementById('modalBody').innerHTML    = buildModalHTML(d);

        // Wire up search input
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

    /* ─── Export Modal helpers ─── */
    let currentExportEventId = null;

    function _populateExportModal(eventId, eventName, barangays) {
        currentExportEventId = eventId;

        document.getElementById('exportModalTitle').textContent = 'Export — ' + eventName;
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
            cb.checked = true;
            syncToggleStyle(cb);
        });

        updateExportColCount();
        document.getElementById('exportModalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Called by the Export button in the main table row
    function openExportModal(btn) {
        const barangays = JSON.parse(btn.dataset.barangays || '[]');
        _populateExportModal(btn.dataset.eventId, btn.dataset.eventName, barangays);
    }

    // FIX: Called by the Export Excel button inside the View modal.
    // Reads everything from the already-parsed panel data — no HTML attribute escaping at all.
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
        const wrapper = cb.closest('.export-col-toggle');
        if (cb.checked) wrapper.classList.add('checked');
        else wrapper.classList.remove('checked');
        updateExportColCount();
    }

    function toggleGroup(group, checked) {
        document.querySelectorAll(`.export-col-toggle[data-group="${group}"] input[type="checkbox"]`).forEach(cb => {
            cb.checked = checked;
            syncToggleStyle(cb);
        });
    }

    function updateExportColCount() {
        const count = document.querySelectorAll('.export-col-toggle input[type="checkbox"]:checked').length;
        document.getElementById('exportColCount').textContent = count;
        document.getElementById('btnDoExport').disabled = count === 0;
    }

    function doExport() {
        if (!currentExportEventId) return;

        const logCols  = [...document.querySelectorAll('input[name="log_cols[]"]:checked')].map(c => c.value);
        const hhCols   = [...document.querySelectorAll('input[name="hh_cols[]"]:checked')].map(c => c.value);
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
            input.type  = 'hidden';
            input.name  = name;
            input.value = value;
            form.appendChild(input);
        };

        addField('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        logCols.forEach(v => addField('log_cols[]', v));
        hhCols.forEach(v  => addField('hh_cols[]', v));
        if (barangay) addField('barangay', barangay);
        if (dateFrom) addField('date_from', dateFrom);
        if (dateTo)   addField('date_to', dateTo);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        closeExportModal();
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeSidebar(); closeModal(); closeExportModal(); }
    });
</script>
</body>
</html>