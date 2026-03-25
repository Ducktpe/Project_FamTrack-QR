{{-- resources/views/admin/traillog/trail.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Admin Trail Logs</title>
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
            --yellow-pale:#FFFAE6;
            --green:      #16A34A;
            --green-pale: #DCFCE7;
            --green-dark: #15803D;
            --red:        #C0392B;
            --red-pale:   #FEF2F2;
            --orange:     #D97706;
            --orange-pale:#FFFBEB;
            --purple:     #5B3FA6;
            --purple-pale:#F5F0FF;
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
        html, body { height: 100%; font-family: 'Open Sans', sans-serif; background: var(--gray-100); color: var(--gray-800); font-size: 14px; }

        .shell { display: grid; grid-template-rows: 36px 76px 1fr 48px; grid-template-columns: var(--sidebar-w) 1fr; grid-template-areas: "topbar topbar" "header header" "sidebar main" "footer footer"; height: 100vh; overflow: hidden; }

        /* TOPBAR */
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.55); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* HEADER */
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

        /* SIDEBAR OVERLAY */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* SIDEBAR */
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
        .logout-btn:hover { background: #C0392B; }

        /* MAIN */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        /* STAT CARDS */
        .sev-bar { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .sev-card { background: var(--white); border: 1px solid var(--gray-200); padding: 16px 20px; border-top: 3px solid var(--gray-200); }
        .sev-card.sev-total  { border-top-color: var(--blue); }
        .sev-card.sev-high   { border-top-color: var(--red); }
        .sev-card.sev-medium { border-top-color: var(--orange); }
        .sev-card.sev-low    { border-top-color: var(--green); }
        .sev-number { font-family: 'PT Serif', serif; font-size: 28px; font-weight: 700; color: var(--blue-dark); line-height: 1; margin-bottom: 3px; }
        .sev-card.sev-high   .sev-number { color: var(--red); }
        .sev-card.sev-medium .sev-number { color: var(--orange); }
        .sev-card.sev-low    .sev-number { color: var(--green-dark); }
        .sev-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }

        /* ROLE TABS */
        .role-tabs { display: flex; gap: 0; margin-bottom: 16px; border-bottom: 2px solid var(--gray-200); overflow-x: auto; }
        .role-tab { display: flex; align-items: center; gap: 7px; padding: 9px 18px; font-size: 12.5px; font-weight: 600; color: var(--gray-600); text-decoration: none; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: color 0.12s, border-color 0.12s; white-space: nowrap; }
        .role-tab:hover { color: var(--blue); border-bottom-color: var(--blue-light); }
        .role-tab.active { color: var(--blue); border-bottom-color: var(--blue); }
        .role-tab-count { background: var(--gray-100); color: var(--gray-600); font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; }
        .role-tab.active .role-tab-count { background: var(--blue-pale); color: var(--blue); }

        /* FILTER BAR */
        .filter-bar { background: var(--white); border: 1px solid var(--gray-200); padding: 14px 20px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search { flex: 1; min-width: 200px; display: flex; align-items: center; gap: 8px; border: 1px solid var(--gray-200); border-radius: 3px; padding: 0 12px; background: var(--gray-50); }
        .filter-search svg { width: 14px; height: 14px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search input { border: none; background: none; font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); padding: 9px 0; width: 100%; outline: none; }
        .filter-search input::placeholder { color: var(--gray-400); }
        .filter-select { border: 1px solid var(--gray-200); border-radius: 3px; background: var(--gray-50); font-family: 'Open Sans', sans-serif; font-size: 12px; color: var(--gray-600); padding: 8px 10px; outline: none; cursor: pointer; }
        .filter-date { border: 1px solid var(--gray-200); border-radius: 3px; background: var(--gray-50); font-family: 'Open Sans', sans-serif; font-size: 12px; color: var(--gray-600); padding: 8px 10px; outline: none; }
        .filter-btn { background: var(--blue); color: var(--white); border: none; border-radius: 3px; padding: 8px 16px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .filter-btn:hover { background: var(--blue-light); }
        .filter-reset { background: none; border: 1px solid var(--gray-200); border-radius: 3px; padding: 8px 12px; font-family: 'Open Sans', sans-serif; font-size: 12px; color: var(--gray-600); cursor: pointer; text-decoration: none; }
        .filter-reset:hover { background: var(--gray-100); }
        .filter-count { font-size: 12px; color: var(--gray-400); margin-left: auto; white-space: nowrap; }
        .filter-count strong { color: var(--blue); }

        /* TABLE */
        .table-wrap { background: var(--white); border: 1px solid var(--gray-200); overflow: hidden; }
        .table-header { padding: 13px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .table-header-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .table-header-count { font-size: 11px; color: var(--gray-400); margin-left: 6px; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        thead th { padding: 10px 14px; background: var(--blue); color: var(--white); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background .1s; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 11px 14px; font-size: 12.5px; color: var(--gray-800); vertical-align: middle; }
        tbody tr:nth-child(even) td { background: var(--gray-50); }
        tbody tr:nth-child(even):hover td { background: var(--blue-pale); }

        /* PILLS & BADGES */
        .role-pill { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        .role-admin   { background: var(--blue-pale);   color: var(--blue); }
        .role-staff   { background: var(--green-pale);  color: var(--green-dark); }
        .role-encoder { background: var(--orange-pale); color: var(--orange); }
        .role-auditor { background: var(--purple-pale); color: var(--purple); }
        .sev-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        .sev-high   { background: var(--red-pale);    color: var(--red); }
        .sev-medium { background: var(--orange-pale); color: var(--orange); }
        .sev-low    { background: var(--green-pale);  color: var(--green-dark); }
        .cat { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        .cat-auth         { background: var(--green-pale);  color: var(--green-dark); }
        .cat-household    { background: var(--blue-pale);   color: var(--blue); }
        .cat-distribution { background: var(--orange-pale); color: var(--orange); }
        .cat-qr_code      { background: var(--purple-pale); color: var(--purple); }
        .cat-general      { background: var(--gray-100);    color: var(--gray-600); }
        .action-text { font-weight: 600; color: var(--gray-800); font-size: 12px; }
        .action-desc { font-size: 11px; color: var(--gray-600); margin-top: 2px; }

        /* DETAILS BUTTON */
        .btn-details { background: none; border: 1px solid var(--gray-200); border-radius: 3px; padding: 4px 10px; font-size: 11px; font-weight: 600; color: var(--blue); cursor: pointer; font-family: 'Open Sans', sans-serif; transition: background .15s; white-space: nowrap; }
        .btn-details:hover { background: var(--blue-pale); border-color: #C3D8F5; }

        /* ══════════════════════════════════════
           MODAL
        ══════════════════════════════════════ */
        .modal-backdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(10,20,50,0.55);
            z-index: 1000;
            backdrop-filter: blur(3px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-backdrop.open { display: flex; }

        .modal {
            background: var(--white);
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 860px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: modalIn 0.2s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(-12px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Modal header */
        .modal-header {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 18px 22px 16px;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
            flex-shrink: 0;
        }
        .modal-header-icon {
            width: 36px; height: 36px; border-radius: 6px;
            background: var(--blue-pale);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        .modal-header-icon svg { width: 18px; height: 18px; color: var(--blue); }
        .modal-title { font-family: 'PT Serif', serif; font-size: 17px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .modal-meta { font-size: 11px; color: var(--gray-400); margin-top: 3px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .modal-meta-badge {
            display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 10px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
        }
        .modal-close {
            margin-left: auto; flex-shrink: 0;
            background: none; border: 1px solid var(--gray-200); border-radius: 4px;
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--gray-600);
            transition: background 0.15s, color 0.15s;
        }
        .modal-close:hover { background: var(--red-pale); color: var(--red); border-color: #FECACA; }
        .modal-close svg { width: 16px; height: 16px; }

        /* Modal body */
        .modal-body { overflow-y: auto; padding: 20px 22px; flex: 1; }

        /* Changed fields summary bar */
        .modal-summary-bar {
            display: flex; align-items: center; gap: 8px;
            background: var(--blue-pale); border: 1px solid #C3D8F5;
            border-radius: 4px; padding: 8px 14px;
            margin-bottom: 16px; font-size: 12px;
        }
        .modal-summary-bar svg { width: 14px; height: 14px; color: var(--blue); flex-shrink: 0; }
        .modal-summary-bar strong { color: var(--blue-dark); }

        /* Diff section headings */
        .diff-section-head {
            display: flex; align-items: center; gap: 7px;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--gray-600); margin-bottom: 8px;
        }
        .diff-tag {
            display: inline-flex; align-items: center;
            padding: 1px 7px; border-radius: 3px;
            font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .diff-tag.old  { background: var(--red-pale);   color: var(--red); }
        .diff-tag.new  { background: var(--green-pale);  color: var(--green-dark); }
        .diff-tag.snap { background: var(--blue-pale);   color: var(--blue); }

        /* Side-by-side diff layout */
        .diff-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* The readable diff table */
        .diff-table-wrap { border: 1px solid var(--gray-200); border-radius: 2px; overflow: hidden; }
        .diff-tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
        .diff-tbl tr { border-bottom: 1px solid var(--gray-100); }
        .diff-tbl tr:last-child { border-bottom: none; }
        .diff-tbl td { padding: 7px 10px; vertical-align: top; line-height: 1.45; }
        .diff-tbl .cell-field { width: 36%; font-size: 11px; font-weight: 600; color: var(--gray-600); background: var(--gray-50); border-right: 1px solid var(--gray-200); white-space: nowrap; }
        .diff-tbl .cell-val { background: var(--white); color: var(--gray-800); word-break: break-word; }
        .diff-tbl .cell-val.changed-old { background: #FFF5F5; color: #9B1C1C; }
        .diff-tbl .cell-val.changed-new { background: #F0FDF4; color: #14532D; }
        .diff-tbl .cell-val.same        { color: var(--gray-400); }
        .cell-null { color: var(--gray-400); font-style: italic; font-size: 11px; }

        /* Modal footer */
        .modal-footer {
            padding: 12px 22px;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
            display: flex; align-items: center; justify-content: flex-end; gap: 8px;
            flex-shrink: 0;
        }
        .modal-footer-btn {
            font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600;
            padding: 8px 20px; border-radius: 4px; cursor: pointer;
            border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-600);
            transition: background 0.15s;
        }
        .modal-footer-btn:hover { background: var(--gray-100); }
        .modal-footer-btn.primary { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .modal-footer-btn.primary:hover { background: var(--blue-light); }

        /* PAGINATION */
        .pagination-wrap { padding: 14px 20px; border-top: 1px solid var(--gray-200); background: var(--white); }
        .pagination-wrap nav { display: none; }
        .pg-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .pg-info { font-size: 12px; color: var(--gray-400); }
        .pg-info strong { color: var(--blue-dark); font-weight: 700; }
        .pg-controls { display: flex; align-items: center; gap: 4px; }
        .pg-btn { display: inline-flex; align-items: center; justify-content: center; height: 34px; min-width: 34px; padding: 0 10px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; border-radius: 6px; border: 1.5px solid var(--gray-200); background: var(--white); color: var(--gray-600); text-decoration: none; cursor: pointer; gap: 5px; transition: background .13s, color .13s, border-color .13s; white-space: nowrap; }
        .pg-btn:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); }
        .pg-btn.active { background: var(--blue); color: var(--white); border-color: var(--blue); font-weight: 700; pointer-events: none; }
        .pg-btn.nav-btn { padding: 0 14px; color: var(--blue); background: var(--blue-pale); border-color: var(--blue-light); font-weight: 700; }
        .pg-btn.nav-btn:hover { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .pg-btn.disabled { color: var(--gray-400); background: var(--gray-100); border-color: var(--gray-200); cursor: not-allowed; pointer-events: none; }
        .pg-dots { display: inline-flex; align-items: center; justify-content: center; height: 34px; min-width: 24px; font-size: 13px; color: var(--gray-400); letter-spacing: 1px; }

        /* FOOTER */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,.45); }
        .footer-left strong { color: rgba(255,255,255,.75); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,.25); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,.45); text-decoration: none; transition: color .15s; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; height: 100vh; overflow: hidden; }
            .sidebar { grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 300; transform: translateX(-100%); transition: transform 0.28s cubic-bezier(0.4,0,0.2,1); box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }
            .hamburger { display: flex; }
            .main-content { padding: 20px 16px; }
            .sev-bar { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .sev-bar { grid-template-columns: 1fr 1fr; }
            .filter-bar { flex-wrap: wrap; }
            .filter-search { min-width: 100%; }
            .diff-layout { grid-template-columns: 1fr; }
            .modal { max-height: 95vh; }
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
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="admin-name">{{ auth()->user()->name ?? 'Admin' }}</div>
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
        <a href="{{ route('admin.traillog.trail') }}" class="nav-item active" onclick="closeSidebar()">
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

    <!-- MAIN -->
    <main class="main-content">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">Admin / <span>Trail Logs</span></div>
                <div class="page-h1">Trail Logs</div>
                <div class="page-sub">Complete record of all user activity across all roles</div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="sev-bar">
            <div class="sev-card sev-total">
                <div class="sev-number">{{ number_format($totalAll) }}</div>
                <div class="sev-label">Total Entries</div>
            </div>
            <div class="sev-card sev-high">
                <div class="sev-number">{{ number_format($sevHigh) }}</div>
                <div class="sev-label">🔴 High Severity</div>
            </div>
            <div class="sev-card sev-medium">
                <div class="sev-number">{{ number_format($sevMedium) }}</div>
                <div class="sev-label">🟡 Medium Severity</div>
            </div>
            <div class="sev-card sev-low">
                <div class="sev-number">{{ number_format($sevLow) }}</div>
                <div class="sev-label">🟢 Low Severity</div>
            </div>
        </div>

        <!-- Role Tabs -->
        <div class="role-tabs">
            <a href="{{ route('admin.traillog.trail') }}" class="role-tab {{ !request('role') ? 'active' : '' }}">
                All Users <span class="role-tab-count">{{ number_format($totalAll) }}</span>
            </a>
            <a href="{{ route('admin.traillog.trail', ['role' => 'admin']) }}" class="role-tab {{ request('role') === 'admin' ? 'active' : '' }}">
                Admin <span class="role-tab-count">{{ number_format($totalAdmin) }}</span>
            </a>
            <a href="{{ route('admin.traillog.trail', ['role' => 'staff']) }}" class="role-tab {{ request('role') === 'staff' ? 'active' : '' }}">
                Staff <span class="role-tab-count">{{ number_format($totalStaff) }}</span>
            </a>
            <a href="{{ route('admin.traillog.trail', ['role' => 'encoder']) }}" class="role-tab {{ request('role') === 'encoder' ? 'active' : '' }}">
                Encoder <span class="role-tab-count">{{ number_format($totalEncoder) }}</span>
            </a>
            <a href="{{ route('admin.traillog.trail', ['role' => 'auditor']) }}" class="role-tab {{ request('role') === 'auditor' ? 'active' : '' }}">
                Auditor <span class="role-tab-count">{{ number_format($totalAuditor) }}</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.traillog.trail') }}">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <div class="filter-bar">
                <span class="filter-label">Filter</span>
                <div class="filter-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" placeholder="Search user, action, description…" value="{{ request('search') }}">
                </div>
                <select name="user_id" class="filter-select">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ ucfirst($u->role) }})
                        </option>
                    @endforeach
                </select>
                <select name="category" class="filter-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $cat)) }}
                        </option>
                    @endforeach
                </select>
                <select name="severity" class="filter-select">
                    <option value="">All Severity</option>
                    <option value="high"   {{ request('severity') === 'high'   ? 'selected' : '' }}>🔴 High</option>
                    <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="low"    {{ request('severity') === 'low'    ? 'selected' : '' }}>🟢 Low</option>
                </select>
                <input type="date" name="date_from" class="filter-date" value="{{ request('date_from') }}" title="From date">
                <input type="date" name="date_to"   class="filter-date" value="{{ request('date_to') }}"   title="To date">
                <button type="submit" class="filter-btn">Apply</button>
                <a href="{{ route('admin.traillog.trail', request('role') ? ['role' => request('role')] : []) }}" class="filter-reset">Reset</a>
                <span class="filter-count">Showing <strong>{{ $logs->count() }}</strong> of {{ number_format($logs->total()) }}</span>
            </div>
        </form>

        <!-- Table -->
        <div class="table-wrap">
            <div class="table-header">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span class="table-header-title">System Activity Log</span>
                <span class="table-header-count">{{ number_format($logs->total()) }} total entries</span>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th style="width:140px;">Timestamp</th>
                            <th style="width:170px;">User</th>
                            <th style="width:90px;">Role</th>
                            <th style="width:120px;">Category</th>
                            <th style="width:90px;">Severity</th>
                            <th>Action / Description</th>
                            <th style="width:180px;">Affected Record</th>
                            <th style="width:110px;">IP Address</th>
                            <th style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $role       = $log->user->role ?? null;
                                $category   = $log->category ?? 'general';
                                $severity   = $log->severity ?? 'low';
                                $modelName  = $log->model ? class_basename($log->model) : null;
                                $modelLabels = [
                                    'Household'         => 'Household',
                                    'FamilyMember'      => 'Family Member',
                                    'DistributionEvent' => 'Distribution Event',
                                    'DistributionLog'   => 'Distribution Log',
                                    'User'              => 'User Account',
                                    'AuditLog'          => 'Audit Log',
                                ];
                                $modelLabel = $modelLabels[$modelName] ?? $modelName;
                                $isAuth     = str_contains(strtolower($log->action), 'login') || str_contains(strtolower($log->action), 'logout');
                                $hasDetails = $log->old_values || $log->new_values;

                                // Pre-parse values for the modal data attribute
                                $ov = $log->old_values ?? [];
                                if (is_string($ov)) $ov = json_decode($ov, true) ?? [];
                                $nv = $log->new_values ?? [];
                                if (is_string($nv)) $nv = json_decode($nv, true) ?? [];
                            @endphp
                            <tr>
                                <td style="color:var(--gray-400);font-size:11px;">{{ $logs->firstItem() + $loop->index }}</td>
                                <td style="white-space:nowrap;font-size:11px;color:var(--gray-600);">
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    <strong style="color:var(--gray-800);">{{ $log->created_at->format('h:i:s A') }}</strong>
                                </td>
                                <td>
                                    <div style="font-weight:600;font-size:12.5px;">{{ $log->user_name ?? '—' }}</div>
                                    @if($log->user?->email)
                                        <div style="font-size:10px;color:var(--gray-400);">{{ $log->user->email }}</div>
                                    @elseif($log->user_id)
                                        <div style="font-size:10px;color:var(--gray-400);">User #{{ $log->user_id }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($role)
                                        <span class="role-pill role-{{ $role }}">{{ ucfirst($role) }}</span>
                                    @else
                                        <span style="color:var(--gray-400);font-size:11px;">—</span>
                                    @endif
                                </td>
                                <td><span class="cat cat-{{ $category }}">{{ ucwords(str_replace('_', ' ', $category)) }}</span></td>
                                <td>
                                    <span class="sev-badge sev-{{ $severity }}">
                                        {{ $severity === 'high' ? '🔴' : ($severity === 'medium' ? '🟡' : '🟢') }}
                                        {{ ucfirst($severity) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-text">{{ ucwords(str_replace('_', ' ', $log->action)) }}</div>
                                    @if($log->description)
                                        <div class="action-desc">{{ $log->description }}</div>
                                    @endif
                                </td>
                                <td style="font-size:12px;">
                                    @if($log->affected_name)
                                        <div style="font-weight:600;color:var(--gray-800);">{{ $log->affected_name }}</div>
                                        @if($modelLabel && $log->record_id)
                                            <div style="font-size:10px;color:var(--gray-400);margin-top:2px;">{{ $modelLabel }} #{{ $log->record_id }}</div>
                                        @endif
                                    @elseif($isAuth)
                                        <div style="font-weight:600;color:var(--gray-800);">{{ $log->user_name ?? '—' }}</div>
                                        <div style="font-size:10px;color:var(--gray-400);margin-top:2px;">Own Account</div>
                                    @elseif($modelLabel && $log->record_id)
                                        <div style="font-weight:600;color:var(--gray-800);">{{ $modelLabel }} #{{ $log->record_id }}</div>
                                    @else
                                        <span style="color:var(--gray-400);font-style:italic;font-size:11px;">—</span>
                                    @endif
                                </td>
                                <td style="font-size:11px;color:var(--gray-600);font-family:monospace;">
                                    {{ $log->ip_address ?? '—' }}
                                </td>
                                <td>
                                    @if($hasDetails)
                                        <button
                                            class="btn-details"
                                            onclick="openModal(this)"
                                            data-id="{{ $log->id }}"
                                            data-action="{{ ucwords(str_replace('_', ' ', $log->action)) }}"
                                            data-user="{{ $log->user_name ?? '—' }}"
                                            data-role="{{ ucfirst($role ?? '') }}"
                                            data-severity="{{ $severity }}"
                                            data-date="{{ $log->created_at->format('M d, Y · h:i A') }}"
                                            data-old="{{ json_encode($ov, JSON_HEX_QUOT | JSON_HEX_APOS) }}"
                                            data-new="{{ json_encode($nv, JSON_HEX_QUOT | JSON_HEX_APOS) }}"
                                        >Details</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding:48px;text-align:center;color:var(--gray-400);font-style:italic;">
                                    No trail log entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="pagination-wrap" id="paginationRow">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
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
</div>

{{-- ══════════════════════════════════════════
     DETAILS MODAL (single, reused for all rows)
══════════════════════════════════════════ --}}
<div class="modal-backdrop" id="detailsModalBackdrop" onclick="handleBackdropClick(event)">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">

        <div class="modal-header">
            <div class="modal-header-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="modal-title" id="modalTitle">Change Details</div>
                <div class="modal-meta" id="modalMeta"></div>
            </div>
            <button class="modal-close" onclick="closeModal()" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="modal-body" id="modalBody">
            {{-- Populated by JS --}}
        </div>

        <div class="modal-footer">
            <button class="modal-footer-btn primary" onclick="closeModal()">Close</button>
        </div>

    </div>
</div>

<script>
    /* ── Clock ── */
    function pad(n) { return String(n).padStart(2,'0'); }
    function updateClock() {
        const now = new Date();
        const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+months[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock(); setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    /* ── Sidebar ── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal() || closeSidebar(); });

    /* ══════════════════════════════════════════
       DETAILS MODAL
    ══════════════════════════════════════════ */
    const SKIP_KEYS = new Set(['remember_token','password','updated_at','created_at']);

    function fmtVal(val) {
        if (val === null || val === undefined) return null;
        if (typeof val === 'boolean') return val ? 'true' : 'false';
        if (typeof val === 'object')  return JSON.stringify(val);
        return String(val);
    }

    function truncate(str, max) {
        return str && str.length > max ? str.slice(0, max) + '…' : str;
    }

    function severityColor(sev) {
        return { high: '#C0392B', medium: '#D97706', low: '#16A34A' }[sev] || '#9AA3B0';
    }

    function buildDiffTable(keys, valFn, cssClass) {
        if (!keys.length) return '<p style="padding:14px;color:var(--gray-400);font-size:12px;font-style:italic;">No data.</p>';
        let rows = '';
        keys.forEach(key => {
            const { display, cls } = valFn(key);
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            rows += `<tr>
                <td class="cell-field">${label}</td>
                <td class="cell-val ${cls}">
                    ${display === null ? '<span class="cell-null">—</span>' : escHtml(truncate(display, 160))}
                </td>
            </tr>`;
        });
        return `<div class="diff-table-wrap"><table class="diff-tbl">${rows}</table></div>`;
    }

    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function openModal(btn) {
        const id       = btn.dataset.id;
        const action   = btn.dataset.action;
        const user     = btn.dataset.user;
        const role     = btn.dataset.role;
        const severity = btn.dataset.severity;
        const date     = btn.dataset.date;

        let ov = {}, nv = {};
        try { ov = JSON.parse(btn.dataset.old  || '{}'); } catch(e) {}
        try { nv = JSON.parse(btn.dataset.new || '{}'); } catch(e) {}

        // Filter skip keys
        const allKeys = [...new Set([...Object.keys(ov), ...Object.keys(nv)])]
            .filter(k => !SKIP_KEYS.has(k));

        const hasBoth   = Object.keys(ov).length > 0 && Object.keys(nv).length > 0;
        const hasOld    = Object.keys(ov).length > 0 && Object.keys(nv).length === 0;
        const hasNew    = Object.keys(ov).length === 0 && Object.keys(nv).length > 0;

        const changedCount = hasBoth
            ? allKeys.filter(k => JSON.stringify(ov[k] ?? null) !== JSON.stringify(nv[k] ?? null)).length
            : allKeys.length;

        const sevColor = severityColor(severity);
        const sevLabel = severity.charAt(0).toUpperCase() + severity.slice(1);
        const sevEmoji = severity === 'high' ? '🔴' : (severity === 'medium' ? '🟡' : '🟢');

        // Build meta line
        document.getElementById('modalTitle').textContent = action;
        document.getElementById('modalMeta').innerHTML = `
            <span>Log #${escHtml(id)}</span>
            <span>·</span>
            <span>${escHtml(date)}</span>
            <span>·</span>
            <span><strong style="color:var(--gray-800)">${escHtml(user)}</strong>${role ? ` <span class="role-pill role-${escHtml(role.toLowerCase())}" style="margin-left:4px;">${escHtml(role)}</span>` : ''}</span>
            <span>·</span>
            <span class="sev-badge sev-${escHtml(severity)}">${sevEmoji} ${sevLabel}</span>
        `;

        // Build body
        let bodyHtml = '';

        if (hasBoth) {
            bodyHtml += `<div class="modal-summary-bar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <span><strong>${changedCount} field${changedCount !== 1 ? 's' : ''} changed</strong> — highlighted rows show what was modified</span>
            </div>`;

            const oldTable = buildDiffTable(allKeys, key => {
                const oval = fmtVal(ov[key] ?? null);
                const nval = fmtVal(nv[key] ?? null);
                const changed = JSON.stringify(ov[key] ?? null) !== JSON.stringify(nv[key] ?? null);
                return { display: oval, cls: changed ? 'changed-old' : 'same' };
            });

            const newTable = buildDiffTable(allKeys, key => {
                const oval = fmtVal(ov[key] ?? null);
                const nval = fmtVal(nv[key] ?? null);
                const changed = JSON.stringify(ov[key] ?? null) !== JSON.stringify(nv[key] ?? null);
                return { display: nval, cls: changed ? 'changed-new' : 'same' };
            });

            bodyHtml += `<div class="diff-layout">
                <div>
                    <div class="diff-section-head">Before &nbsp;<span class="diff-tag old">Old Values</span></div>
                    ${oldTable}
                </div>
                <div>
                    <div class="diff-section-head">After &nbsp;<span class="diff-tag new">New Values</span></div>
                    ${newTable}
                </div>
            </div>`;

        } else if (hasOld) {
            const tbl = buildDiffTable(allKeys, key => ({ display: fmtVal(ov[key] ?? null), cls: '' }));
            bodyHtml += `<div class="diff-section-head" style="margin-bottom:8px;">Snapshot &nbsp;<span class="diff-tag old">Before Delete</span></div>${tbl}`;

        } else if (hasNew) {
            const tbl = buildDiffTable(allKeys, key => ({ display: fmtVal(nv[key] ?? null), cls: 'changed-new' }));
            bodyHtml += `<div class="diff-section-head" style="margin-bottom:8px;">Snapshot &nbsp;<span class="diff-tag new">Created Record</span></div>${tbl}`;
        }

        document.getElementById('modalBody').innerHTML = bodyHtml;
        document.getElementById('detailsModalBackdrop').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('detailsModalBackdrop').classList.remove('open');
        document.body.style.overflow = '';
    }

    function handleBackdropClick(e) {
        if (e.target === document.getElementById('detailsModalBackdrop')) closeModal();
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    /* ── Rebuild Laravel pagination ── */
    (function normalizePagination() {
        const container = document.getElementById('paginationRow');
        if (!container) return;
        const nav = container.querySelector('nav');
        if (!nav) return;
        const CL = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0"><polyline points="15 18 9 12 15 6"/></svg>`;
        const CR = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>`;
        const infoEl   = nav.querySelector('p');
        const infoHTML = infoEl ? infoEl.innerHTML : '';
        let prevHTML = '', nextHTML = '', pageButtons = '';
        const btnGroup = nav.querySelector('div') || nav;
        Array.from(btnGroup.children).forEach(el => {
            const inner = el.children.length === 1 && el.children[0].tagName === 'SPAN' ? el.children[0] : el;
            const text  = inner.textContent.trim().replace(/[\s\u00a0]+/g,' ');
            const isLink    = el.tagName === 'A';
            const isCurrent = el.getAttribute('aria-current') === 'page' || inner.getAttribute('aria-current') === 'page';
            const href      = el.getAttribute('href') || '#';
            if (/previous/i.test(text) || text === '«') {
                prevHTML = isLink ? `<a href="${href}" class="pg-btn nav-btn">${CL} Previous</a>` : `<span class="pg-btn nav-btn disabled">${CL} Previous</span>`;
                return;
            }
            if (/next/i.test(text) || text === '»') {
                nextHTML = isLink ? `<a href="${href}" class="pg-btn nav-btn">Next ${CR}</a>` : `<span class="pg-btn nav-btn disabled">Next ${CR}</span>`;
                return;
            }
            if (text === '...' || text === '…') { pageButtons += `<span class="pg-dots">···</span>`; return; }
            if (!/^\d+$/.test(text)) return;
            pageButtons += isCurrent
                ? `<span class="pg-btn active">${text}</span>`
                : `<a href="${href}" class="pg-btn">${text}</a>`;
        });
        container.innerHTML = `<div class="pg-bar"><div class="pg-controls">${prevHTML}<div style="display:flex;align-items:center;gap:4px;">${pageButtons}</div>${nextHTML}</div>${infoHTML ? `<div class="pg-info">${infoHTML}</div>` : ''}</div>`;
    })();
</script>
</body>
</html>