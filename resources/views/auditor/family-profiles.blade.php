<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Family Profiles</title>
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
            --green:      #1A7A4A;
            --green-pale: #EAF5EF;
            --green-border:#A8D8BE;
            --purple:     #5B3FA6;
            --purple-dark:#3D1F8A;
            --purple-pale:#F5F0FF;
            --purple-border:#D8CBF5;
            --sky:        #0EA5E9;
            --sky-dark:   #0369A1;
            --sky-pale:   #F0F9FF;
            --sky-border: #BAE6FD;
            --teal:       #0D9488;
            --teal-pale:  #F0FDFA;
            --teal-border:#99F6E4;
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
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
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
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 14px;
            z-index: 90;
        }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; margin-left: -4px; border-radius: 4px; color: var(--blue-dark); flex-shrink: 0; transition: background 0.15s; }
        .hamburger:hover { background: var(--blue-pale); }
        .hamburger svg { width: 22px; height: 22px; display: block; }
        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; min-width: 0; overflow: hidden; }
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
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
            flex-shrink: 0;
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
        .user-name { font-size: 13px; font-weight: 600; color: var(--sky-dark); line-height: 1.2; white-space: nowrap; }
        .user-role { font-size: 10px; color: #0284C7; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }

        /* ─── READ-ONLY BADGE ─── */
        .readonly-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; background: #FFFBEB;
            border: 1px solid #FDE68A; border-radius: 3px;
            font-size: 11px; font-weight: 700; color: #92400E;
            text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0;
        }
        .readonly-badge svg { width: 12px; height: 12px; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

        /* ─── SIDEBAR ─── */
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; z-index: 10; color: var(--gray-600); transition: background 0.15s; }
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-left: 3px solid transparent; transition: background 0.12s, color 0.12s, border-color 0.12s; cursor: pointer; }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; color: inherit; opacity: 0.7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .nav-badge-view { margin-left: auto; background: var(--gray-400); color: var(--white); font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 10px; letter-spacing: 0.5px; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .role-notice { margin: 12px 14px; background: var(--purple-pale); border: 1px solid var(--purple-border); border-left: 3px solid var(--purple); padding: 10px 12px; border-radius: 2px; }
        .role-notice-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--purple-dark); margin-bottom: 3px; }
        .role-notice-text { font-size: 11px; color: #4B3080; line-height: 1.5; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn { width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white); border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .logout-btn:hover { background: var(--red); }

        /* ─── MAIN ─── */
        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        /* ─── STATS ROW ─── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            padding: 16px 20px;
        }
        .stat-card.yellow { border-top-color: var(--yellow); }
        .stat-card.green  { border-top-color: var(--green); }
        .stat-card.purple { border-top-color: var(--purple); }
        .stat-card.teal   { border-top-color: var(--teal); }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 6px; }
        .stat-value { font-family: 'PT Serif', serif; font-size: 26px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .stat-card.yellow .stat-value { color: var(--yellow-dark); }
        .stat-card.green  .stat-value { color: var(--green); }
        .stat-card.purple .stat-value { color: var(--purple); }
        .stat-card.teal   .stat-value { color: var(--teal); }
        .stat-sub { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        /* ─── FILTER / SEARCH BAR ─── */
        .filter-bar {
            background: var(--white);
            border: 1px solid var(--gray-200);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search {
            flex: 1;
            min-width: 200px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            padding: 0 12px;
            background: var(--gray-50);
        }
        .filter-search svg { width: 14px; height: 14px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search input { border: none; background: none; font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); padding: 9px 0; width: 100%; outline: none; }
        .filter-search input::placeholder { color: var(--gray-400); }
        .filter-select {
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            background: var(--gray-50);
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
            color: var(--gray-600);
            padding: 8px 10px;
            outline: none;
            cursor: pointer;
        }

        /* Student filter toggle button */
        .filter-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Open Sans', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 14px;
            border-radius: 3px;
            border: 1px solid var(--teal-border);
            background: var(--teal-pale);
            color: var(--teal);
            cursor: pointer;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
            white-space: nowrap;
        }
        .filter-toggle-btn svg { width: 13px; height: 13px; flex-shrink: 0; }
        .filter-toggle-btn:hover { background: var(--teal); color: var(--white); border-color: var(--teal); }
        .filter-toggle-btn.active { background: var(--teal); color: var(--white); border-color: var(--teal); }

        .filter-count {
            font-size: 12px;
            color: var(--gray-400);
            margin-left: auto;
            white-space: nowrap;
        }
        .filter-count strong { color: var(--blue); }

        /* ─── TABLE WRAPPER ─── */
        .table-wrap {
            background: var(--white);
            border: 1px solid var(--gray-200);
            overflow: hidden;
        }
        .table-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .th-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); }
        .th-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .th-badge { margin-left: auto; background: var(--blue-pale); color: var(--blue); font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 10px; border: 1px solid #C5D9F5; }

        /* ─── PROFILE CARDS (responsive — replaces wide table) ─── */
        .profile-list { display: flex; flex-direction: column; gap: 0; }

        /*
         * 7-column grid (desktop ≥1100px):
         * [Serial 140px] [Name+Address 1fr] [Sex/DOB 110px] [Contact 130px] [Tags 160px] [Status 90px] [Action 80px]
         * Serial is 140px (was 110px) — long codes like NIC-TB-HH-2026-00001 need the room.
         * Each cell also gets min-width:0 so the 1fr name column can't push serial out.
         */
        .profile-card,
        .profile-card-header {
            display: grid;
            grid-template-columns: 140px 1fr 110px 130px 160px 90px 80px;
            align-items: center;
            column-gap: 12px;
            padding: 13px 20px;
        }
        /* Every direct child gets min-width:0 — prevents any cell from blowing past its column */
        .profile-card > *,
        .profile-card-header > * { min-width: 0; overflow: hidden; }
        .profile-card {
            border-bottom: 1px solid var(--gray-100);
            transition: background 0.1s;
        }
        .profile-card:last-child { border-bottom: none; }
        .profile-card:hover { background: var(--blue-pale); }

        /* Column header row */
        .profile-card-header {
            background: var(--gray-50);
            border-bottom: 2px solid var(--gray-200);
        }
        .profile-card-header .col-head {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray-400);
            white-space: nowrap;
        }

        .card-num { font-size: 11px; color: var(--gray-400); margin-bottom: 4px; }

        /* Serial badge: allow wrapping so long codes break within the 140px column */
        .serial-code {
            font-family: monospace;
            font-size: 10px;
            font-weight: 700;
            color: var(--blue);
            background: var(--blue-pale);
            padding: 3px 7px;
            border-radius: 3px;
            border: 1px solid #C5D9F5;
            /* break-all so NIC-TB-HH-2026-00001 wraps instead of overflowing */
            white-space: normal;
            word-break: break-all;
            display: inline-block;
            max-width: 100%;
            line-height: 1.5;
        }

        .household-name { font-weight: 600; color: var(--blue-dark); font-size: 13px; }
        .household-sub { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
        .address-line { font-size: 12px; color: var(--gray-600); }

        .col-sex { font-size: 12px; color: var(--gray-800); white-space: nowrap; }
        .col-dob { font-size: 11px; color: var(--gray-400); }
        .col-contact { font-size: 12px; color: var(--gray-600); white-space: nowrap; }

        /* badges */
        .badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 10px;
            margin: 1px 2px 1px 0;
            white-space: nowrap;
        }
        .badge-4ps    { background: #EAF5EF; color: #1A7A4A; border: 1px solid #A8D8BE; }
        .badge-pwd    { background: #FFF3E0; color: #BF6000; border: 1px solid #FFD08A; }
        .badge-senior { background: #EAF0FA; color: var(--blue); border: 1px solid #C5D9F5; }
        .badge-solo   { background: var(--purple-pale); color: var(--purple-dark); border: 1px solid var(--purple-border); }
        .badge-none   { background: var(--gray-100); color: var(--gray-400); border: 1px solid var(--gray-200); font-style: italic; }
        .badge-student { background: var(--teal-pale); color: var(--teal); border: 1px solid var(--teal-border); }

        .status-active   { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: var(--green); white-space: nowrap; }
        .status-active::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--green); flex-shrink: 0; }
        .status-inactive { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: var(--gray-400); white-space: nowrap; }
        .status-inactive::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--gray-400); flex-shrink: 0; }

        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: 'Open Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: var(--blue);
            background: var(--blue-pale);
            border: 1px solid #C5D9F5;
            border-radius: 3px;
            padding: 6px 12px;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.12s, color 0.12s;
        }
        .view-btn:hover { background: var(--blue); color: var(--white); }
        .view-btn svg { width: 12px; height: 12px; }

        /* Action column: needs overflow:visible so button isn't clipped by the global overflow:hidden on cells */
        .view-btn-col {
            overflow: visible !important;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        /* ─── TAGS COLUMN (wrappable on small screens) ─── */
        .badge-wrap { display: flex; flex-wrap: wrap; gap: 3px; min-width: 80px; }

        /* ─── EMPTY STATE ─── */
        .empty-state { padding: 56px 40px; text-align: center; }
        .empty-icon-wrap { width: 48px; height: 48px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-icon-wrap svg { width: 22px; height: 22px; color: var(--gray-400); }
        .empty-title { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 5px; }
        .empty-sub { font-size: 12px; color: var(--gray-400); }

        /* ─── PAGINATION ─── */
        .pagination-row {
            padding: 12px 20px;
            border-top: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .pag-info { font-size: 12px; color: var(--gray-400); }
        .pag-links { display: flex; gap: 4px; flex-wrap: wrap; }
        .pag-btn { font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; padding: 6px 12px; border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-600); border-radius: 3px; cursor: pointer; transition: background 0.12s; }
        .pag-btn:hover { background: var(--blue-pale); color: var(--blue); border-color: #C5D9F5; }
        .pag-btn.active { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .pag-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* ─── MODAL ─── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 500; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.open { display: flex; }
        .modal { background: var(--white); width: 100%; max-width: 640px; max-height: 90vh; overflow-y: auto; border-top: 4px solid var(--yellow); box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
        .modal-head { padding: 20px 24px 16px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .modal-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); }
        .modal-serial { font-size: 11px; color: var(--gray-400); margin-top: 4px; font-family: monospace; }
        .modal-close { background: none; border: none; cursor: pointer; padding: 4px; color: var(--gray-400); border-radius: 3px; transition: background 0.12s; flex-shrink: 0; }
        .modal-close:hover { background: var(--gray-100); color: var(--red); }
        .modal-close svg { width: 20px; height: 20px; }
        .modal-body { padding: 20px 24px; }
        .modal-section { margin-bottom: 20px; }
        .modal-section-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--blue-light); margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--blue-pale); }
        .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-field label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); display: block; margin-bottom: 4px; }
        .modal-field .field-val { font-size: 13px; color: var(--gray-800); font-weight: 500; }
        .modal-field .field-val.mono { font-family: monospace; font-size: 12px; }
        .modal-badges-wrap { display: flex; flex-wrap: wrap; gap: 6px; }
        .modal-foot { padding: 16px 24px; border-top: 1px solid var(--gray-100); background: var(--gray-50); display: flex; justify-content: flex-end; }
        .modal-close-btn { font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; background: var(--blue); color: var(--white); border: none; padding: 10px 24px; border-radius: 3px; cursor: pointer; transition: background 0.15s; }
        .modal-close-btn:hover { background: var(--blue-dark); }

        /* ─── FOOTER ─── */
        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        /* ── Mobile profile card — shown only on small screens (≤900px) ── */
        .profile-mobile-card {
            display: none;
            background: var(--white);
            border-bottom: 1px solid var(--gray-100);
            padding: 14px 16px;
        }
        .profile-mobile-card:last-child { border-bottom: none; }
        .pmc-top {
            display: flex; align-items: flex-start;
            justify-content: space-between; gap: 10px;
            margin-bottom: 8px;
        }
        .pmc-name { font-size: 13px; font-weight: 600; color: var(--gray-800); }
        .pmc-serial {
            font-family: monospace; font-size: 10px; font-weight: 700;
            color: var(--blue); background: var(--blue-pale);
            padding: 2px 7px; border-radius: 3px; border: 1px solid #C5D9F5;
            margin-top: 3px; display: inline-block;
        }
        .pmc-address { font-size: 11px; color: var(--gray-600); margin-top: 3px; line-height: 1.5; }
        .pmc-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 6px; }
        .pmc-contact { font-size: 11px; color: var(--gray-600); }
        .pmc-dob { font-size: 11px; color: var(--gray-400); }
        .pmc-badges { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 8px; }
        .pmc-footer {
            display: flex; align-items: center;
            justify-content: space-between; gap: 8px; margin-top: 10px;
        }
        .profile-mobile-cards { display: none; }

        /* ─── RESPONSIVE — ordered largest → smallest ─── */

        @media (max-width: 1200px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }

        /* ≤1100px: hide Sex/DOB and Contact columns, fold into name cell */
        @media (max-width: 1100px) {
            .profile-card,
            .profile-card-header {
                grid-template-columns: 120px 1fr 180px 90px 76px;
            }
            .col-sexdob   { display: none; }
            .col-contact  { display: none; }
            .head-sexdob  { display: none; }
            .head-contact { display: none; }
            .card-inline-contact { display: block; }
            .card-inline-dob     { display: block; }
        }

        /* ≤900px — sidebar off-canvas, header compresses, table→card swap */
        @media (max-width: 900px) {
            .shell {
                grid-template-rows: 36px 76px 1fr 48px;
                grid-template-columns: 1fr;
                grid-template-areas: "topbar" "header" "main" "footer";
                height: 100vh;
                overflow: hidden;
            }
            .sidebar {
                grid-area: unset;
                position: fixed; top: 0; left: 0; bottom: 0;
                width: var(--sidebar-w);
                z-index: 300;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
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
            /* Switch to card view — hide desktop grid, show mobile cards */
            .profile-list-desktop { display: none; }
            .profile-mobile-cards { display: block; }
            .profile-mobile-card  { display: block; }
        }

        /* ≤640px — header simplifies further, topbar collapses */
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
            .readonly-badge { padding: 5px 9px; font-size: 10px; }
            .main-content { padding: 16px 12px; }
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }
            .stats-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .filter-bar { flex-wrap: wrap; }
            .filter-search { min-width: 100%; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        /* ≤480px — tightest shell, mirrors audit trail exactly */
        @media (max-width: 480px) {
            .shell { grid-template-rows: 28px 52px 1fr 40px; }
            .topbar { height: 28px; padding: 0 10px; }
            header { padding: 0 8px; gap: 6px; }
            .header-logos img { height: 34px; width: 34px; }
            .header-title { font-size: 13px; }
            .header-user-badge { padding: 5px 8px; }
            .user-avatar { width: 28px; height: 28px; font-size: 11px; }
            /* readonly-badge: icon only */
            .readonly-badge { padding: 5px 7px; gap: 0; }
            .readonly-badge-text { display: none; }
            .main-content { padding: 10px 8px; }
            .page-h1 { font-size: 16px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            footer { height: 40px; padding: 0 10px; }
            .footer-center { display: none; }
            .footer-left { font-size: 9px; }
            .modal-grid { grid-template-columns: 1fr; }
        }

        /* ≤380px — single-column stats */
        @media (max-width: 380px) {
            .stats-row { grid-template-columns: 1fr; }
            .main-content { padding: 10px 8px; }
        }

        /* Helpers — hidden by default, shown via media queries above */
        .card-inline-contact { display: none; }
        .card-inline-dob     { display: none; }
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
        <span class="readonly-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            <span class="readonly-badge-text">Read-Only Access</span>
        </span>
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
        <a href="{{ route('auditor.dashboard') }}" class="nav-item" onclick="closeSidebar()">
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
        <a href="#" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Family Profiles
            <span class="nav-badge-view">View</span>
        </a>
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
            <div class="role-notice-text">You have view-only access. No records can be added, edited, or deleted. Access may be time-limited by the Administrator</div>
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
                <div class="page-breadcrumb">
                    <a href="{{ route('auditor.dashboard') }}">Home</a> /
                    <span>Family Profiles</span>
                </div>
                <div class="page-h1">Family Profiles</div>
                <div class="page-sub">Registered household heads — Barangay Family Track QR System</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Households</div>
                <div class="stat-value">{{ $households->total() }}</div>
                <div class="stat-sub">Registered records</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-label">4Ps Beneficiaries</div>
                <div class="stat-value">{{ $households->getCollection()->where('is_4ps_beneficiary', 1)->count() }}</div>
                <div class="stat-sub">This page</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Active Status</div>
                <div class="stat-value">{{ $households->getCollection()->where('status', 'active')->count() }}</div>
                <div class="stat-sub">This page</div>
            </div>
            <div class="stat-card teal">
                <div class="stat-label">With Students</div>
                {{-- Count households on this page that have at least one student member --}}
                <div class="stat-value">{{ $households->getCollection()->filter(fn($h) => in_array($h->id, $householdIdsWithStudents))->count() }}</div>
                <div class="stat-sub">Has student member</div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <span class="filter-label">Filter</span>
            <div class="filter-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Search household head, barangay, serial…" oninput="filterTable()">
            </div>
            <select class="filter-select" id="barangayFilter" onchange="filterTable()">
                <option value="">All Barangays</option>
                @foreach($households->getCollection()->pluck('barangay')->unique()->sort() as $brgy)
                    <option value="{{ strtolower($brgy) }}">{{ $brgy }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="statusFilter" onchange="filterTable()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            {{-- Student filter: based on employment_status = 'Student' in family_member_details --}}
            <button class="filter-toggle-btn" id="studentFilterBtn" onclick="toggleStudentFilter()" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
                With Students
            </button>
            <span class="filter-count">Showing <strong id="visibleCount">{{ $households->count() }}</strong> of {{ $households->total() }}</span>
        </div>

        <!-- PROFILE CARDS TABLE -->
        <div class="table-wrap">
            <div class="table-header">
                <div class="th-dot"></div>
                <div class="th-title">Household Profiles</div>
                <span class="th-badge">{{ $households->total() }} Total Records</span>
            </div>

            {{-- ── Desktop / tablet grid view (hidden at ≤900px) ── --}}
            <div class="profile-list-desktop">
                {{-- Column header row --}}
                <div class="profile-card profile-card-header">
                    <div class="col-head"># Serial</div>
                    <div class="col-head">Household Head / Address</div>
                    <div class="col-head col-sexdob head-sexdob">Sex / Birthday</div>
                    <div class="col-head col-contact head-contact">Contact</div>
                    <div class="col-head">Tags</div>
                    <div class="col-head col-status head-status">Status</div>
                    <div class="col-head"></div>
                </div>
                <div class="profile-list" id="profileList">
                    @forelse($households as $index => $hh)
                    @php
                        $hasStudent = in_array($hh->id, $householdIdsWithStudents);
                    @endphp
                    <div class="profile-card"
                         data-name="{{ strtolower($hh->household_head_name) }}"
                         data-serial="{{ strtolower($hh->serial_code ?? '') }}"
                         data-barangay="{{ strtolower($hh->barangay) }}"
                         data-status="{{ $hh->status }}"
                         data-student="{{ $hasStudent ? '1' : '0' }}">

                        {{-- Serial --}}
                        <div class="serial-cell" style="overflow:hidden;min-width:0;">
                            <div class="card-num">{{ $households->firstItem() + $index }}</div>
                            <span class="serial-code">{{ $hh->serial_code ?? 'N/A' }}</span>
                        </div>

                        {{-- Name + Address --}}
                        <div style="min-width:0;overflow:hidden;">
                            <div class="household-name">{{ $hh->household_head_name }}</div>
                            <div class="household-sub">{{ $hh->head_civil_status ? ucfirst($hh->head_civil_status) : '' }}</div>
                            <div class="address-line">
                                {{ collect([$hh->location, $hh->street_purok])->filter()->unique()->implode(', ') }}
                            </div>
                            <div class="address-line" style="color:var(--gray-400);">
                                {{ collect([$hh->barangay, $hh->municipality])->filter()->implode(', ') }}
                            </div>
                            <div class="card-inline-contact" style="font-size:11px;color:var(--gray-600);margin-top:2px;">
                                {{ $hh->contact_number ?? '—' }}
                            </div>
                            <div class="card-inline-dob" style="font-size:11px;color:var(--gray-400);margin-top:1px;">
                                {{ $hh->head_sex ?? '' }}
                                @if($hh->head_birthday) &bull; {{ \Carbon\Carbon::parse($hh->head_birthday)->format('M d, Y') }} @endif
                            </div>
                        </div>

                        {{-- Sex / DOB --}}
                        <div class="col-sexdob">
                            <div class="col-sex">{{ $hh->head_sex ?? '—' }}</div>
                            <div class="col-dob">
                                {{ $hh->head_birthday ? \Carbon\Carbon::parse($hh->head_birthday)->format('M d, Y') : '—' }}
                            </div>
                        </div>

                        {{-- Contact --}}
                        <div class="col-contact" style="overflow:hidden;">
                            <span style="font-size:12px;color:var(--gray-600);white-space:nowrap;">{{ $hh->contact_number ?? '—' }}</span>
                        </div>

                        {{-- Beneficiary Tags --}}
                        <div class="badge-wrap">
                            @if($hh->is_4ps_beneficiary) <span class="badge badge-4ps">4Ps</span> @endif
                            @if($hh->is_pwd)             <span class="badge badge-pwd">PWD</span> @endif
                            @if($hh->is_senior)          <span class="badge badge-senior">Senior</span> @endif
                            @if($hh->is_solo_parent)     <span class="badge badge-solo">Solo Parent</span> @endif
                            @if($hasStudent)             <span class="badge badge-student">Student</span> @endif
                            @if(!$hh->is_4ps_beneficiary && !$hh->is_pwd && !$hh->is_senior && !$hh->is_solo_parent && !$hasStudent)
                                <span class="badge badge-none">None</span>
                            @endif
                        </div>

                        {{-- Status --}}
                        <div class="col-status">
                            @if($hh->status === 'active')
                                <span class="status-active">Active</span>
                            @else
                                <span class="status-inactive">{{ ucfirst($hh->status) }}</span>
                            @endif
                        </div>

                        {{-- Action --}}
                        <div class="view-btn-col">
                            <button class="view-btn" onclick="openModal({{ $hh->id }})">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <div class="empty-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="empty-title">No household records found</div>
                        <div class="empty-sub">There are no registered family profiles in the system yet.</div>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ── Mobile card view (shown at ≤900px when desktop grid is hidden) ── --}}
            <div class="profile-mobile-cards">
                @forelse($households as $hh)
                @php $hasStudent = in_array($hh->id, $householdIdsWithStudents); @endphp
                <div class="profile-mobile-card profile-card"
                     data-name="{{ strtolower($hh->household_head_name) }}"
                     data-serial="{{ strtolower($hh->serial_code ?? '') }}"
                     data-barangay="{{ strtolower($hh->barangay) }}"
                     data-status="{{ $hh->status }}"
                     data-student="{{ $hasStudent ? '1' : '0' }}">
                    <div class="pmc-top">
                        <div>
                            <div class="pmc-name">{{ $hh->household_head_name }}</div>
                            <span class="pmc-serial">{{ $hh->serial_code ?? 'N/A' }}</span>
                        </div>
                        @if($hh->status === 'active')
                            <span class="status-active">Active</span>
                        @else
                            <span class="status-inactive">{{ ucfirst($hh->status) }}</span>
                        @endif
                    </div>
                    <div class="pmc-address">
                        {{ collect([$hh->location, $hh->street_purok, $hh->barangay, $hh->municipality])->filter()->implode(', ') }}
                    </div>
                    <div class="pmc-meta">
                        @if($hh->contact_number)
                            <span class="pmc-contact">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:3px;"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.0 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>{{ $hh->contact_number }}
                            </span>
                        @endif
                        @if($hh->head_sex || $hh->head_birthday)
                            <span class="pmc-dob">
                                {{ $hh->head_sex ?? '' }}@if($hh->head_sex && $hh->head_birthday) &bull; @endif{{ $hh->head_birthday ? \Carbon\Carbon::parse($hh->head_birthday)->format('M d, Y') : '' }}
                            </span>
                        @endif
                    </div>
                    <div class="pmc-badges">
                        @if($hh->is_4ps_beneficiary) <span class="badge badge-4ps">4Ps</span> @endif
                        @if($hh->is_pwd)             <span class="badge badge-pwd">PWD</span> @endif
                        @if($hh->is_senior)          <span class="badge badge-senior">Senior</span> @endif
                        @if($hh->is_solo_parent)     <span class="badge badge-solo">Solo Parent</span> @endif
                        @if($hasStudent)             <span class="badge badge-student">Student</span> @endif
                        @if(!$hh->is_4ps_beneficiary && !$hh->is_pwd && !$hh->is_senior && !$hh->is_solo_parent && !$hasStudent)
                            <span class="badge badge-none">None</span>
                        @endif
                    </div>
                    <div class="pmc-footer">
                        <button class="view-btn" onclick="openModal({{ $hh->id }})" style="width:100%;justify-content:center;padding:9px 16px;font-size:12px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            View Profile
                        </button>
                    </div>
                </div>
                @empty
                    <div style="padding:36px;text-align:center;color:var(--gray-400);font-style:italic;">No household records found.</div>
                @endforelse
            </div>

            <!-- EMPTY FILTER STATE -->
            <div id="emptyFilter" style="display:none;">
                <div class="empty-state">
                    <div class="empty-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <div class="empty-title">No matching records</div>
                    <div class="empty-sub">Try adjusting your search or filters.</div>
                </div>
            </div>

            <!-- PAGINATION -->
            <div class="pagination-row">
                <div class="pag-info">
                    Showing {{ $households->firstItem() }}–{{ $households->lastItem() }} of {{ $households->total() }} records
                </div>
                <div class="pag-links">
                    {{-- Previous --}}
                    @if($households->onFirstPage())
                        <button class="pag-btn" disabled>&laquo;</button>
                    @else
                        <a class="pag-btn" href="{{ $households->previousPageUrl() }}">&laquo;</a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach($households->getUrlRange(max(1, $households->currentPage()-2), min($households->lastPage(), $households->currentPage()+2)) as $page => $url)
                        <a class="pag-btn {{ $page == $households->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach

                    {{-- Next --}}
                    @if($households->hasMorePages())
                        <a class="pag-btn" href="{{ $households->nextPageUrl() }}">&raquo;</a>
                    @else
                        <button class="pag-btn" disabled>&raquo;</button>
                    @endif
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

<!-- ─── PROFILE MODAL ─── -->
<div class="modal-overlay" id="modalOverlay" onclick="handleOverlayClick(event)">
    <div class="modal" id="profileModal">
        <div class="modal-head">
            <div>
                <div class="modal-title" id="modal-name">—</div>
                <div class="modal-serial" id="modal-serial">—</div>
            </div>
            <button class="modal-close" onclick="closeModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-section">
                <div class="modal-section-label">Personal Information</div>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>Sex</label>
                        <div class="field-val" id="modal-sex">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Birthday</label>
                        <div class="field-val" id="modal-birthday">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Civil Status</label>
                        <div class="field-val" id="modal-civil">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Contact Number</label>
                        <div class="field-val" id="modal-contact">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Educational Attainment</label>
                        <div class="field-val" id="modal-education">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Employment Status</label>
                        <div class="field-val" id="modal-employment">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-label">Address</div>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>Location / Purok</label>
                        <div class="field-val" id="modal-address">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Barangay</label>
                        <div class="field-val" id="modal-barangay">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Municipality</label>
                        <div class="field-val" id="modal-municipality">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Province</label>
                        <div class="field-val" id="modal-province">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-section-label">Beneficiary Classification</div>
                <div class="modal-badges-wrap" id="modal-badges">—</div>
            </div>
            <div class="modal-section">
                <div class="modal-section-label">System Information</div>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>Valid ID</label>
                        <div class="field-val mono" id="modal-listahanan">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Status</label>
                        <div class="field-val" id="modal-status">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Date Encoded</label>
                        <div class="field-val" id="modal-created">—</div>
                    </div>
                    <div class="modal-field">
                        <label>Last Updated</label>
                        <div class="field-val" id="modal-updated">—</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="modal-close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

{{-- Pass household data as JSON for modal --}}
<script>
const householdsData = @json($households->getCollection()->keyBy('id'));
const householdIdsWithStudents = @json($householdIdsWithStudents);
</script>

<script>
    // ─── Clock ───
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

    // ─── Sidebar ───
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('hamburgerBtn').addEventListener('click', () => {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // ─── Student Filter Toggle ───
    let studentFilterActive = false;
    function toggleStudentFilter() {
        studentFilterActive = !studentFilterActive;
        const btn = document.getElementById('studentFilterBtn');
        btn.classList.toggle('active', studentFilterActive);
        filterTable();
    }

    // ─── Filter ───
    function filterTable() {
        const search  = document.getElementById('searchInput').value.toLowerCase();
        const brgy    = document.getElementById('barangayFilter').value.toLowerCase();
        const status  = document.getElementById('statusFilter').value.toLowerCase();
        const cards   = document.querySelectorAll('.profile-card[data-name], .profile-mobile-card[data-name]');
        let visible   = 0;
        const seen    = new Set();

        cards.forEach(card => {
            const matchSearch  = !search || card.dataset.name.includes(search) || card.dataset.serial.includes(search) || card.dataset.barangay.includes(search);
            const matchBrgy    = !brgy   || card.dataset.barangay === brgy;
            const matchStatus  = !status || card.dataset.status === status;
            const matchStudent = !studentFilterActive || card.dataset.student === '1';
            const show = matchSearch && matchBrgy && matchStatus && matchStudent;
            card.style.display = show ? '' : 'none';
            // Count each household once (desktop + mobile cards share same data)
            if (show && !seen.has(card.dataset.serial + card.dataset.name)) {
                seen.add(card.dataset.serial + card.dataset.name);
                visible++;
            }
        });

        document.getElementById('visibleCount').textContent = visible;
        document.getElementById('emptyFilter').style.display = visible === 0 ? '' : 'none';
    }

    // ─── Modal ───
    function openModal(id) {
        const hh = householdsData[id];
        if (!hh) return;

        // ── Identity ─────────────────────────────────────────────────────────
        document.getElementById('modal-name').textContent   = hh.household_head_name || '—';
        document.getElementById('modal-serial').textContent = hh.serial_code ? 'Serial: ' + hh.serial_code : 'No serial code';

        // ── Personal Info ─────────────────────────────────────────────────────
        // sex / birthday / civil_status live on family_members, NOT households.
        // The controller joins them in as head_sex / head_birthday / head_civil_status.
        document.getElementById('modal-sex').textContent      = hh.head_sex      || '—';
        document.getElementById('modal-birthday').textContent = hh.head_birthday  ? formatDate(hh.head_birthday) : '—';
        document.getElementById('modal-civil').textContent    = hh.head_civil_status ? capitalize(hh.head_civil_status) : '—';
        document.getElementById('modal-contact').textContent  = hh.contact_number || '—';

        // educational_attainment → family_members (joined as head_educational_attainment)
        // employment_status      → family_member_details (joined as head_employment_status)
        document.getElementById('modal-education').textContent  = hh.head_educational_attainment  ? capitalize(hh.head_educational_attainment)  : '—';
        document.getElementById('modal-employment').textContent = hh.head_employment_status ? capitalize(hh.head_employment_status) : '—';

        // ── Address ───────────────────────────────────────────────────────────
        // households has no house_number column — the field is called `location`.
        // street_purok is the purok/street name.
        document.getElementById('modal-address').textContent      = [hh.location, hh.street_purok].filter(Boolean).join(', ') || '—';
        document.getElementById('modal-barangay').textContent     = hh.barangay     || '—';
        document.getElementById('modal-municipality').textContent = hh.municipality  || '—';
        document.getElementById('modal-province').textContent     = hh.province      || '—';

        // ── System Info ───────────────────────────────────────────────────────
        // households has no national_id column — valid ID info is valid_id_type / valid_id_num.
        document.getElementById('modal-listahanan').textContent = hh.valid_id_num
            ? (hh.valid_id_type ? hh.valid_id_type + ': ' : '') + hh.valid_id_num
            : '—';
        document.getElementById('modal-created').textContent = hh.created_at ? formatDate(hh.created_at) : '—';
        document.getElementById('modal-updated').textContent = hh.updated_at ? formatDate(hh.updated_at) : '—';

        // ── Status ────────────────────────────────────────────────────────────
        const statusEl = document.getElementById('modal-status');
        statusEl.innerHTML = hh.status === 'active'
            ? '<span class="status-active">Active</span>'
            : '<span class="status-inactive">' + capitalize(hh.status || 'Unknown') + '</span>';

        // ── Badges ────────────────────────────────────────────────────────────
        // Cast id to Number — householdIdsWithStudents is int[] from PHP,
        // but JS object keys are always strings so id arrives as a string here.
        const numId    = Number(id);
        const badgesEl = document.getElementById('modal-badges');
        let badges = '';
        if (hh.is_4ps_beneficiary) badges += '<span class="badge badge-4ps">4Ps Beneficiary</span>';
        if (hh.is_pwd)             badges += '<span class="badge badge-pwd">Person with Disability (PWD)</span>';
        if (hh.is_senior)          badges += '<span class="badge badge-senior">Senior Citizen</span>';
        if (hh.is_solo_parent)     badges += '<span class="badge badge-solo">Solo Parent</span>';
        if (householdIdsWithStudents.includes(numId)) badges += '<span class="badge badge-student">Has Student Member</span>';
        badgesEl.innerHTML = badges || '<span class="badge badge-none">No beneficiary tags</span>';

        document.getElementById('modalOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    function handleOverlayClick(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function formatDate(str) {
        const d = new Date(str);
        if (isNaN(d)) return str;
        return d.toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
</body>
</html>