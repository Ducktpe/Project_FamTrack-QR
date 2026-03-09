<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Audit Trail</title>
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
            --green:      #16A34A;
            --green-pale: #DCFCE7;
            --green-dark: #15803D;
            --red:        #C0392B;
            --red-pale:   #FEF2F2;
            --orange:     #D97706;
            --orange-pale:#FFFBEB;
            --purple:     #5B3FA6;
            --purple-pale:#F5F0FF;
            --sky:        #0EA5E9;
            --sky-dark:   #0369A1;
            --sky-pale:   #F0F9FF;
            --sky-border: #BAE6FD;
            --white:      #FFFFFF;
            --gray-50:    #F7F8FA;
            --gray-100:   #F0F2F5;
            --gray-200:   #DEE2E8;
            --gray-400:   #9AA3B0;
            --gray-600:   #5A6372;
            --gray-800:   #2C3340;
            --sidebar-w:  260px;
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Open Sans', sans-serif; background: var(--gray-100); color: var(--gray-800); font-size: 14px; }

        .shell { display: grid; grid-template-rows: 36px 76px 1fr 48px; grid-template-columns: var(--sidebar-w) 1fr; grid-template-areas: "topbar topbar" "header header" "sidebar main" "footer footer"; height: 100vh; overflow: hidden; }

        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }

        header { grid-area: header; background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 6px rgba(0,0,0,.08); display: flex; align-items: center; padding: 0 28px; gap: 14px; z-index: 90; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; border-radius: 4px; color: var(--blue-dark); }
        .hamburger svg { width: 22px; height: 22px; display: block; }
        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--sky-pale); border: 1px solid var(--sky-border); border-radius: 4px; flex-shrink: 0; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--sky); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--sky-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: #0284C7; text-transform: uppercase; letter-spacing: .5px; }

        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; opacity: 0; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }
        .sidebar { grid-area: sidebar; background: var(--white); border-right: 1px solid var(--gray-200); display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .sidebar-close { display: none; position: absolute; top: 12px; right: 12px; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 4px; width: 32px; height: 32px; align-items: center; justify-content: center; cursor: pointer; color: var(--gray-600); }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-left: 3px solid transparent; transition: background .12s, color .12s; }
        .nav-item:hover { background: var(--gray-50); color: var(--blue); border-left-color: var(--blue-light); }
        .nav-item.active { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue); font-weight: 600; }
        .nav-icon { width: 17px; height: 17px; flex-shrink: 0; opacity: .7; }
        .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
        .sidebar-sep { border: none; border-top: 1px solid var(--gray-100); margin: 8px 0; }
        .nav-badge-view { margin-left: auto; background: var(--gray-400); color: var(--white); font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 10px; letter-spacing: .5px; }
        .role-notice { margin: 12px 14px; background: var(--purple-pale); border: 1px solid #D8CBF5; border-left: 3px solid var(--purple); padding: 10px 12px; border-radius: 2px; }
        .role-notice-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #3D1F8A; margin-bottom: 3px; }
        .role-notice-text { font-size: 11px; color: #4B3080; line-height: 1.5; }
        .sidebar-bottom { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--gray-200); }
        .logout-btn { width: 100%; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: var(--blue); color: var(--white); border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background .15s; }
        .logout-btn:hover { background: var(--red); }

        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        /* ── Stats ── */
        .stats-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 16px 18px; }
        .stat-card.stat-total    { border-top-color: var(--blue); }
        .stat-card.stat-auth     { border-top-color: var(--green); }
        .stat-card.stat-household{ border-top-color: var(--blue-light); }
        .stat-card.stat-distrib  { border-top-color: var(--orange); }
        .stat-card.stat-users    { border-top-color: var(--purple); }
        .stat-number { font-family: 'PT Serif', serif; font-size: 28px; font-weight: 700; color: var(--blue-dark); line-height: 1; margin-bottom: 4px; }
        .stat-card.stat-auth      .stat-number { color: var(--green-dark); }
        .stat-card.stat-distrib   .stat-number { color: var(--orange); }
        .stat-card.stat-users     .stat-number { color: var(--purple); }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }

        /* ── Filter bar ── */
        .filter-bar { background: var(--white); border: 1px solid var(--gray-200); padding: 14px 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search { flex: 1; min-width: 200px; display: flex; align-items: center; gap: 8px; border: 1px solid var(--gray-200); border-radius: 3px; padding: 0 12px; background: var(--gray-50); }
        .filter-search svg { width: 14px; height: 14px; color: var(--gray-400); flex-shrink: 0; }
        .filter-search input { border: none; background: none; font-family: 'Open Sans', sans-serif; font-size: 13px; color: var(--gray-800); padding: 9px 0; width: 100%; outline: none; }
        .filter-search input::placeholder { color: var(--gray-400); }
        .filter-select { border: 1px solid var(--gray-200); border-radius: 3px; background: var(--gray-50); font-family: 'Open Sans', sans-serif; font-size: 12px; color: var(--gray-600); padding: 8px 10px; outline: none; cursor: pointer; }
        .filter-count { font-size: 12px; color: var(--gray-400); margin-left: auto; white-space: nowrap; }
        .filter-count strong { color: var(--blue); }

        /* ── Table ── */
        .table-wrap { background: var(--white); border: 1px solid var(--gray-200); overflow: hidden; }
        .table-header { padding: 13px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .table-header-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .table-header-count { font-size: 11px; color: var(--gray-400); margin-left: 6px; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        thead th { padding: 10px 14px; background: var(--blue); color: var(--white); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid var(--gray-100); transition: background .1s; }
        tbody tr:hover { background: var(--blue-pale); }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 11px 14px; font-size: 12.5px; color: var(--gray-800); vertical-align: middle; }
        tbody tr:nth-child(even) td { background: var(--gray-50); }
        tbody tr:nth-child(even):hover td { background: var(--blue-pale); }

        /* ── Action type badges ── */
        .act { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        .act-auth         { background: var(--green-pale);  color: var(--green-dark); }
        .act-household    { background: var(--blue-pale);   color: var(--blue); }
        .act-distribution { background: var(--orange-pale); color: var(--orange); }
        .act-qr           { background: var(--purple-pale); color: var(--purple); }
        .act-general      { background: var(--gray-100);    color: var(--gray-600); }

        /* ── Action text ── */
        .action-text { font-weight: 600; color: var(--gray-800); font-size: 12px; }
        .action-desc { font-size: 11px; color: var(--gray-600); margin-top: 2px; }

        /* ── Details toggle ── */
        .btn-details { background: none; border: 1px solid var(--gray-200); border-radius: 3px; padding: 4px 10px; font-size: 11px; font-weight: 600; color: var(--blue); cursor: pointer; font-family: 'Open Sans', sans-serif; transition: background .15s; white-space: nowrap; }
        .btn-details:hover { background: var(--blue-pale); }

        /* ── Details panel ── */
        .details-row { display: none; }
        .details-row td { padding: 0 !important; }
        .details-inner { padding: 14px 20px; background: var(--gray-50); border-top: 1px solid var(--gray-100); }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .details-block-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--gray-400); margin-bottom: 6px; }
        .details-json { background: var(--white); border: 1px solid var(--gray-200); border-radius: 3px; padding: 10px 12px; font-family: monospace; font-size: 11px; color: var(--gray-800); white-space: pre-wrap; word-break: break-all; max-height: 160px; overflow-y: auto; }

        /* ── Distribution details ── */
        .distrib-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .distrib-detail-block { display: flex; flex-direction: column; gap: 6px; }
        .distrib-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .distrib-tag { background: var(--orange-pale); color: var(--orange); border: 1px solid #FDE68A; border-radius: 3px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
        .distrib-value { font-size: 13px; color: var(--gray-800); font-weight: 500; background: var(--white); border: 1px solid var(--gray-200); padding: 8px 12px; border-radius: 3px; }
        .distrib-value.mono { font-family: monospace; color: var(--blue); font-weight: 700; letter-spacing: 1px; }
        .distrib-items-table { width: 100%; border-collapse: collapse; background: var(--white); border: 1px solid var(--gray-200); border-radius: 3px; overflow: hidden; }
        .distrib-items-table thead th { padding: 7px 12px; background: var(--blue); color: var(--white); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; text-align: left; }
        .distrib-items-table tbody td { padding: 8px 12px; font-size: 12px; border-bottom: 1px solid var(--gray-100); color: var(--gray-800); }
        .distrib-items-table tbody tr:last-child td { border-bottom: none; }

        /* ── QR Code details ── */
        .qr-detail-wrap { display: flex; gap: 20px; align-items: flex-start; }
        .qr-code-box {
            flex-shrink: 0;
            width: 110px; height: 110px;
            background: var(--white);
            border: 2px solid var(--purple);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 6px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(91,63,166,.12);
        }
        .qr-code-box svg { width: 64px; height: 64px; color: var(--purple); }
        .qr-code-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--purple); }
        .qr-fields { flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .qr-field { display: flex; flex-direction: column; gap: 3px; }
        .qr-field-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }
        .qr-field-value {
            font-size: 12.5px; color: var(--gray-800); font-weight: 500;
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: 4px; padding: 7px 10px; line-height: 1.4;
        }
        .qr-field-value.serial {
            font-family: monospace; font-size: 13px; font-weight: 700;
            color: var(--purple); letter-spacing: 1.5px;
            background: var(--purple-pale); border-color: #D8CBF5;
        }
        .qr-field-value.scan-ok {
            color: var(--green-dark); background: var(--green-pale);
            border-color: #BBF7D0; font-weight: 700;
            display: flex; align-items: center; gap: 6px;
        }
        .qr-field-value.scan-ok::before {
            content: '';
            display: inline-block; width: 8px; height: 8px;
            border-radius: 50%; background: var(--green);
            flex-shrink: 0;
        }
        .qr-field-value.scan-fail {
            color: var(--red); background: var(--red-pale);
            border-color: #FECACA; font-weight: 700;
            display: flex; align-items: center; gap: 6px;
        }
        .qr-field-value.scan-fail::before {
            content: '';
            display: inline-block; width: 8px; height: 8px;
            border-radius: 50%; background: var(--red);
            flex-shrink: 0;
        }
        .qr-field-value.ip { font-family: monospace; font-size: 12px; color: var(--gray-600); }
        .qr-field.full-width { grid-column: 1 / -1; }
        .qr-meta-bar {
            margin-top: 10px; padding: 8px 12px;
            background: var(--purple-pale); border: 1px solid #D8CBF5;
            border-left: 3px solid var(--purple); border-radius: 4px;
            display: flex; gap: 20px; flex-wrap: wrap;
        }
        .qr-meta-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #4B3080; }
        .qr-meta-item strong { font-weight: 700; }

        /* ── Household details ── */
        .hh-wrap { display: flex; gap: 18px; align-items: flex-start; }
        .hh-icon-box {
            flex-shrink: 0; width: 96px; height: 96px;
            background: var(--white); border: 2px solid var(--blue-light);
            border-radius: 8px; display: flex; align-items: center;
            justify-content: center; flex-direction: column; gap: 5px;
            box-shadow: 0 2px 8px rgba(27,63,122,.10);
        }
        .hh-icon-box svg { width: 48px; height: 48px; color: var(--blue-light); }
        .hh-icon-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: var(--blue-light); }
        .hh-body { flex: 1; display: flex; flex-direction: column; gap: 10px; min-width: 0; }
        .hh-name-bar {
            display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
            background: var(--blue-pale); border: 1px solid #C3D8F5;
            border-left: 3px solid var(--blue); border-radius: 4px; padding: 10px 14px;
        }
        .hh-name-text { font-size: 15px; font-weight: 700; color: var(--blue-dark); line-height: 1.3; }
        .hh-name-sub  { font-size: 11px; color: var(--gray-600); margin-top: 3px; display: flex; align-items: center; gap: 4px; }
        .hh-badge { flex-shrink: 0; padding: 3px 10px; border-radius: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
        .hh-badge.created { background: var(--green-pale);  color: var(--green-dark); border: 1px solid #BBF7D0; }
        .hh-badge.updated { background: var(--orange-pale); color: var(--orange);     border: 1px solid #FDE68A; }
        .hh-badge.deleted { background: var(--red-pale);    color: var(--red);        border: 1px solid #FECACA; }
        .hh-badge.general { background: var(--blue-pale);   color: var(--blue);       border: 1px solid #C3D8F5; }
        .hh-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .hh-field { display: flex; flex-direction: column; gap: 3px; }
        .hh-field.span2 { grid-column: span 2; }
        .hh-field.span3 { grid-column: 1 / -1; }
        .hh-field-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }
        .hh-field-val { font-size: 12.5px; color: var(--gray-800); font-weight: 500; background: var(--white); border: 1px solid var(--gray-200); border-radius: 4px; padding: 6px 10px; line-height: 1.4; }
        .hh-field-val.mono { font-family: monospace; color: var(--blue); font-weight: 700; background: var(--blue-pale); border-color: #C3D8F5; font-size: 12px; }
        .hh-field-val.bool-yes { background: var(--green-pale); border-color: #BBF7D0; color: var(--green-dark); font-weight: 700; display: flex; align-items: center; gap: 5px; }
        .hh-field-val.bool-no  { background: var(--gray-100);   border-color: var(--gray-200); color: var(--gray-400); display: flex; align-items: center; gap: 5px; }
        .hh-field-val.status-active   { background: var(--green-pale); border-color: #BBF7D0; color: var(--green-dark); font-weight: 700; }
        .hh-field-val.status-inactive { background: var(--red-pale);   border-color: #FECACA; color: var(--red);        font-weight: 700; }
        .hh-field-val.status-pending  { background: var(--orange-pale);border-color: #FDE68A; color: var(--orange);     font-weight: 700; }
        .hh-section-label { grid-column: 1 / -1; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: var(--blue-light); border-bottom: 1px solid var(--gray-200); padding-bottom: 4px; margin-top: 4px; }
        .hh-diff-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .hh-diff-block { border-radius: 4px; overflow: hidden; border: 1px solid var(--gray-200); }
        .hh-diff-head { padding: 6px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; display: flex; align-items: center; gap: 6px; }
        .hh-diff-head.before { background: #FEF2F2; color: var(--red); border-bottom: 1px solid #FECACA; }
        .hh-diff-head.after  { background: var(--green-pale); color: var(--green-dark); border-bottom: 1px solid #BBF7D0; }
        .hh-diff-body { padding: 10px 12px; background: var(--white); display: flex; flex-direction: column; gap: 6px; }
        .hh-diff-row { display: flex; gap: 8px; font-size: 11.5px; }
        .hh-diff-key { font-weight: 700; color: var(--gray-600); min-width: 130px; flex-shrink: 0; }
        .hh-diff-val { color: var(--gray-800); word-break: break-word; }
        .hh-diff-val.new-val { color: var(--green-dark); font-weight: 600; }
        .hh-meta-bar { padding: 8px 12px; background: var(--blue-pale); border: 1px solid #C3D8F5; border-left: 3px solid var(--blue); border-radius: 4px; display: flex; gap: 20px; flex-wrap: wrap; }
        .hh-meta-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--blue-dark); }
        .hh-meta-item strong { font-weight: 700; }

        /* ── Pagination ── */
        .pagination-wrap { padding: 14px 20px; border-top: 1px solid var(--gray-200); background: var(--white); }
        .pagination-wrap nav { display: none; }
        .pg-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .pg-info { font-size: 12px; color: var(--gray-400); }
        .pg-info strong { color: var(--blue-dark); font-weight: 700; }
        .pg-controls { display: flex; align-items: center; gap: 4px; }
        .pg-btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 34px; min-width: 34px; padding: 0 10px;
            font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600;
            border-radius: 6px; border: 1.5px solid var(--gray-200);
            background: var(--white); color: var(--gray-600);
            text-decoration: none; cursor: pointer; line-height: 1; gap: 5px;
            transition: background 0.13s, color 0.13s, border-color 0.13s, box-shadow 0.13s;
            white-space: nowrap;
        }
        .pg-btn:hover { background: var(--blue-pale); color: var(--blue); border-color: var(--blue-light); box-shadow: 0 2px 5px rgba(27,63,122,0.10); }
        .pg-btn.active { background: var(--blue); color: var(--white); border-color: var(--blue); box-shadow: 0 2px 8px rgba(27,63,122,0.20); font-weight: 700; pointer-events: none; }
        .pg-btn.nav-btn { padding: 0 14px; color: var(--blue); background: var(--blue-pale); border-color: var(--blue-light); font-weight: 700; }
        .pg-btn.nav-btn:hover { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .pg-btn.disabled { color: var(--gray-400); background: var(--gray-100); border-color: var(--gray-200); cursor: not-allowed; pointer-events: none; box-shadow: none; }
        .pg-dots { display: inline-flex; align-items: center; justify-content: center; height: 34px; min-width: 24px; font-size: 13px; color: var(--gray-400); letter-spacing: 1px; padding: 0 2px; }

        footer { grid-area: footer; background: var(--blue-dark); border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; z-index: 100; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,.4); }
        .footer-left strong { color: rgba(255,255,255,.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,.4); text-decoration: none; transition: color .15s; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        @media (max-width: 1200px) { .stats-row { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 900px) {
            .shell { grid-template-rows: 36px auto 1fr 48px; grid-template-columns: 1fr; grid-template-areas: "topbar" "header" "main" "footer"; }
            .sidebar { grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 300; transform: translateX(-100%); transition: transform .28s; box-shadow: 4px 0 20px rgba(0,0,0,.15); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }
            .hamburger { display: flex; }
            .main-content { padding: 20px 16px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .stats-row { grid-template-columns: 1fr 1fr; }
            .filter-bar { flex-wrap: wrap; }
            .filter-search { min-width: 100%; }
            .details-grid { grid-template-columns: 1fr; }
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
            <span class="status-dot">System Online</span>
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
        <div class="header-user-badge">
            <div class="user-avatar">A</div>
            <div>
                <div class="user-name">Auditor</div>
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

        <a href="#" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Reports &amp; Exports
            <span class="nav-badge-view">View</span>
        </a>

        <a href="{{ route('auditor.audit.trail') }}" class="nav-item active" onclick="closeSidebar()">
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

    <!-- MAIN -->
    <main class="main-content">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">System / <span>Audit Trail</span></div>
                <div class="page-h1">Audit Trail</div>
                <div class="page-sub">Complete log of all user activity — read-only</div>
            </div>
        </div>

        @php
        // Derive category from action name for display
        function getActionType($action) {
            $action = strtolower($action);
            if (str_contains($action, 'login') || str_contains($action, 'logout') || str_contains($action, 'register') || str_contains($action, 'password')) return 'auth';
            if (str_contains($action, 'household') || str_contains($action, 'member') || str_contains($action, 'family')) return 'household';
            if (str_contains($action, 'distribution') || str_contains($action, 'event') || str_contains($action, 'ayuda') || str_contains($action, 'relief') || str_contains($action, 'distributed')) return 'distribution';
            if (str_contains($action, 'qr') || str_contains($action, 'scan') || str_contains($action, 'serial')) return 'qr';
            return 'general';
        }
        $typeLabels = ['auth' => 'Auth / Login', 'household' => 'Household', 'distribution' => 'Distribution', 'qr' => 'QR / Scan', 'general' => 'General'];
        @endphp

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card stat-total">
                <div class="stat-number">{{ $totalLogs }}</div>
                <div class="stat-label">Total Entries</div>
            </div>
            <div class="stat-card stat-auth">
                <div class="stat-number">{{ $authCount }}</div>
                <div class="stat-label">Login / Auth</div>
            </div>
            <div class="stat-card stat-household">
                <div class="stat-number">{{ $householdCount }}</div>
                <div class="stat-label">Household Actions</div>
            </div>
            <div class="stat-card stat-distrib">
                <div class="stat-number">{{ $distributionCount }}</div>
                <div class="stat-label">Distribution Actions</div>
            </div>
            <div class="stat-card stat-users">
                <div class="stat-number">{{ $logs->pluck('user_id')->unique()->filter()->count() }}</div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <span class="filter-label">Filter</span>
            <div class="filter-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" placeholder="Search user, action, description…" oninput="filterTable()">
            </div>
            <select class="filter-select" id="typeFilter" onchange="filterTable()">
                <option value="">All Activity Types</option>
                <option value="auth">Auth / Login</option>
                <option value="household">Household</option>
                <option value="distribution">Distribution</option>
                <option value="qr">QR / Scan</option>
                <option value="general">General</option>
            </select>
            <span class="filter-count">Showing <strong id="visibleCount">{{ $logs->count() }}</strong> of {{ $logs->total() }}</span>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <div class="table-header">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span class="table-header-title">System Activity Log</span>
                <span class="table-header-count">{{ $logs->total() }} total entries</span>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th style="width:140px;">Timestamp</th>
                            <th style="width:150px;">User</th>
                            <th style="width:120px;">Activity Type</th>
                            <th>Action / Description</th>
                            <th style="width:200px;">Affected Record</th>
                            <th style="width:70px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $actionType = getActionType($log->action);
                            @endphp
                            <tr class="audit-row"
                                data-search="{{ strtolower(($log->user_name ?? '') . ' ' . $log->action . ' ' . ($log->description ?? '') . ' ' . ($log->affected_name ?? '')) }}"
                                data-type="{{ $actionType }}">
                                <td style="white-space:nowrap;font-size:11px;color:var(--gray-600);">
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    <strong style="color:var(--gray-800);">{{ $log->created_at->format('h:i:s A') }}</strong>
                                </td>
                                <td>
                                    <div style="font-weight:600;font-size:12.5px;">{{ $log->user_name ?? '—' }}</div>
                                    @if($log->user_id)
                                        <div style="font-size:10px;color:var(--gray-400);">User ID #{{ $log->user_id }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="act act-{{ $actionType }}">
                                        {{ $typeLabels[$actionType] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-text">{{ ucwords(str_replace('_', ' ', $log->action)) }}</div>
                                    @if($log->description)
                                        <div class="action-desc">{{ $log->description }}</div>
                                    @endif
                                </td>
                                <td style="font-size:12px;">
                                    @php
                                        $action = strtolower($log->action);
                                        $isAuth = str_contains($action, 'login') || str_contains($action, 'logout') || str_contains($action, 'register') || str_contains($action, 'password');
                                        $modelName = $log->model ? class_basename($log->model) : null;
                                        // Human-readable model labels
                                        $modelLabels = [
                                            'Household'         => 'Household',
                                            'FamilyMember'      => 'Family Member',
                                            'DistributionEvent' => 'Distribution Event',
                                            'DistributionLog'   => 'Distribution Log',
                                            'User'              => 'User Account',
                                            'AuditLog'          => 'Audit Log',
                                        ];
                                        $modelLabel = $modelLabels[$modelName] ?? $modelName;
                                    @endphp

                                    @if($log->affected_name)
                                        {{-- Meaningful name exists --}}
                                        <div style="font-weight:600;color:var(--gray-800);">{{ $log->affected_name }}</div>
                                        @if($modelLabel && $log->record_id)
                                            <div style="font-size:10px;color:var(--gray-400);margin-top:2px;">{{ $modelLabel }} #{{ $log->record_id }}</div>
                                        @endif
                                    @elseif($isAuth)
                                        {{-- Login/logout: the user themselves is the subject --}}
                                        <div style="font-weight:600;color:var(--gray-800);">{{ $log->user_name ?? '—' }}</div>
                                        <div style="font-size:10px;color:var(--gray-400);margin-top:2px;">Own Account</div>
                                    @elseif($modelLabel && $log->record_id)
                                        {{-- Has model + ID but no name --}}
                                        <div style="font-weight:600;color:var(--gray-800);">{{ $modelLabel }} #{{ $log->record_id }}</div>
                                        @if($log->description)
                                            <div style="font-size:10px;color:var(--gray-400);margin-top:2px;">See description</div>
                                        @endif
                                    @else
                                        <span style="color:var(--gray-400);font-style:italic;font-size:11px;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->old_values || $log->new_values)
                                        <button class="btn-details" onclick="toggleDetails({{ $log->id }})">Details</button>
                                    @endif
                                </td>
                            </tr>
                            @if($log->old_values || $log->new_values)
                                <tr class="details-row" id="details-{{ $log->id }}">
                                    <td colspan="6">
                                        <div class="details-inner">
                                            @php
                                                $isDistrib = str_contains(strtolower($log->action), 'distribut') || str_contains(strtolower($log->action), 'ayuda');
                                                $actionType = getActionType($log->action);
                                                $nv = $log->new_values ?? [];
                                                if (is_string($nv)) $nv = json_decode($nv, true) ?? [];
                                            @endphp

                                            @if($isDistrib && !empty($nv))
                                                {{-- Structured distribution details --}}
                                                <div class="distrib-detail-grid">
                                                    @if(!empty($nv['relief_type']))
                                                        <div class="distrib-detail-block">
                                                            <div class="details-block-title">Relief Type</div>
                                                            <div class="distrib-tags">
                                                                @foreach((array)$nv['relief_type'] as $rt)
                                                                    <span class="distrib-tag">{{ $rt }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(!empty($nv['event_name']))
                                                        <div class="distrib-detail-block">
                                                            <div class="details-block-title">Distribution Event</div>
                                                            <div class="distrib-value">{{ $nv['event_name'] }}</div>
                                                        </div>
                                                    @endif

                                                    @if(!empty($nv['goods_detail']))
                                                        <div class="distrib-detail-block">
                                                            <div class="details-block-title">Goods / Items Given</div>
                                                            <div class="distrib-value">{{ $nv['goods_detail'] }}</div>
                                                        </div>
                                                    @endif

                                                    @if(!empty($nv['relief_items']) && is_array($nv['relief_items']))
                                                        <div class="distrib-detail-block" style="grid-column: 1 / -1;">
                                                            <div class="details-block-title">Relief Items &amp; Quantity</div>
                                                            <table class="distrib-items-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Quantity</th>
                                                                        <th>Unit</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($nv['relief_items'] as $itemName => $itemData)
                                                                        <tr>
                                                                            <td>{{ ucwords(str_replace('_', ' ', $itemName)) }}</td>
                                                                            <td>{{ $itemData['qty'] ?? $itemData['quantity'] ?? $itemData ?? '—' }}</td>
                                                                            <td>{{ $itemData['unit'] ?? '—' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @elseif(!empty($nv['items_received']) && is_array($nv['items_received']))
                                                        <div class="distrib-detail-block" style="grid-column: 1 / -1;">
                                                            <div class="details-block-title">Items Received &amp; Quantity</div>
                                                            <table class="distrib-items-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Quantity</th>
                                                                        <th>Unit</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($nv['items_received'] as $itemName => $itemData)
                                                                        <tr>
                                                                            <td>{{ ucwords(str_replace('_', ' ', $itemName)) }}</td>
                                                                            <td>{{ is_array($itemData) ? ($itemData['qty'] ?? $itemData['quantity'] ?? '—') : $itemData }}</td>
                                                                            <td>{{ is_array($itemData) ? ($itemData['unit'] ?? '—') : '—' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif

                                                    @if(!empty($nv['remarks']))
                                                        <div class="distrib-detail-block">
                                                            <div class="details-block-title">Remarks</div>
                                                            <div class="distrib-value">{{ $nv['remarks'] }}</div>
                                                        </div>
                                                    @endif

                                                    @if(!empty($nv['serial_code']))
                                                        <div class="distrib-detail-block">
                                                            <div class="details-block-title">Serial Code Scanned</div>
                                                            <div class="distrib-value mono">{{ $nv['serial_code'] }}</div>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Fallback: also show raw JSON if no structured fields matched --}}
                                                @if(empty($nv['relief_type']) && empty($nv['goods_detail']) && empty($nv['relief_items']))
                                                    <div class="details-grid">
                                                        <div>
                                                            <div class="details-block-title">Distribution Data</div>
                                                            <div class="details-json">{{ json_encode($nv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                                                        </div>
                                                    </div>
                                                @endif

                                            @elseif($actionType === 'qr')
                                                {{-- Structured QR / Scan details --}}
                                                @php
                                                    $qv = array_merge((array)($log->old_values ?? []), (array)$nv);
                                                    if (is_string($log->old_values)) $qv = array_merge(json_decode($log->old_values, true) ?? [], $qv);
                                                    $scanStatus = $qv['status'] ?? $qv['scan_status'] ?? $qv['result'] ?? null;
                                                    $isOk = $scanStatus && in_array(strtolower($scanStatus), ['success','ok','valid','found','verified','scanned']);
                                                    $isFail = $scanStatus && in_array(strtolower($scanStatus), ['fail','failed','invalid','not found','error','duplicate']);
                                                @endphp
                                                <div class="qr-detail-wrap">
                                                    <div class="qr-code-box">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                                                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                                                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                                                            <rect x="7" y="7" width="1" height="1" fill="currentColor"/>
                                                            <rect x="16" y="7" width="1" height="1" fill="currentColor"/>
                                                            <rect x="7" y="16" width="1" height="1" fill="currentColor"/>
                                                            <path d="M14 14h1v1h-1zM16 14h1v3h-3v-1h2zM14 17h1v3h-1zM17 18h3v1h-3zM20 14h1v3h-1z"/>
                                                        </svg>
                                                        <div class="qr-code-label">QR Scan</div>
                                                    </div>
                                                    <div style="flex:1;">
                                                        <div class="qr-fields">
                                                            @if(!empty($qv['serial_code']) || !empty($qv['qr_code']) || !empty($qv['code']))
                                                                <div class="qr-field full-width">
                                                                    <div class="qr-field-label">Serial / QR Code Scanned</div>
                                                                    <div class="qr-field-value serial">{{ $qv['serial_code'] ?? $qv['qr_code'] ?? $qv['code'] }}</div>
                                                                </div>
                                                            @endif
                                                            @if($scanStatus)
                                                                <div class="qr-field">
                                                                    <div class="qr-field-label">Scan Result</div>
                                                                    <div class="qr-field-value {{ $isOk ? 'scan-ok' : ($isFail ? 'scan-fail' : '') }}">
                                                                        {{ ucfirst($scanStatus) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if(!empty($qv['household_name']) || !empty($qv['beneficiary']) || !empty($qv['name']))
                                                                <div class="qr-field">
                                                                    <div class="qr-field-label">Beneficiary / Household</div>
                                                                    <div class="qr-field-value">{{ $qv['household_name'] ?? $qv['beneficiary'] ?? $qv['name'] }}</div>
                                                                </div>
                                                            @endif
                                                            @if(!empty($qv['event_name']) || !empty($qv['distribution_event']))
                                                                <div class="qr-field">
                                                                    <div class="qr-field-label">Distribution Event</div>
                                                                    <div class="qr-field-value">{{ $qv['event_name'] ?? $qv['distribution_event'] }}</div>
                                                                </div>
                                                            @endif
                                                            @if(!empty($qv['scanned_by']) || !empty($qv['officer']))
                                                                <div class="qr-field">
                                                                    <div class="qr-field-label">Scanned By</div>
                                                                    <div class="qr-field-value">{{ $qv['scanned_by'] ?? $qv['officer'] }}</div>
                                                                </div>
                                                            @endif
                                                            @if(!empty($qv['location']) || !empty($qv['barangay']))
                                                                <div class="qr-field">
                                                                    <div class="qr-field-label">Location / Barangay</div>
                                                                    <div class="qr-field-value">{{ $qv['location'] ?? $qv['barangay'] }}</div>
                                                                </div>
                                                            @endif
                                                            @if(!empty($qv['ip_address']) || !empty($qv['ip']))
                                                                <div class="qr-field">
                                                                    <div class="qr-field-label">IP Address</div>
                                                                    <div class="qr-field-value ip">{{ $qv['ip_address'] ?? $qv['ip'] }}</div>
                                                                </div>
                                                            @endif
                                                            @if(!empty($qv['device']) || !empty($qv['user_agent']))
                                                                <div class="qr-field">
                                                                    <div class="qr-field-label">Device / Agent</div>
                                                                    <div class="qr-field-value" style="font-size:11px;word-break:break-all;">{{ Str::limit($qv['device'] ?? $qv['user_agent'], 80) }}</div>
                                                                </div>
                                                            @endif
                                                            @if(!empty($qv['remarks']) || !empty($qv['notes']))
                                                                <div class="qr-field full-width">
                                                                    <div class="qr-field-label">Remarks / Notes</div>
                                                                    <div class="qr-field-value">{{ $qv['remarks'] ?? $qv['notes'] }}</div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if($log->created_at || $log->user_name)
                                                            <div class="qr-meta-bar">
                                                                @if($log->user_name)
                                                                    <div class="qr-meta-item">
                                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                                                        <strong>Officer:</strong> {{ $log->user_name }}
                                                                    </div>
                                                                @endif
                                                                @if($log->created_at)
                                                                    <div class="qr-meta-item">
                                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 6 12 12 16 14"/></svg>
                                                                        <strong>Time:</strong> {{ $log->created_at->format('M d, Y h:i:s A') }}
                                                                    </div>
                                                                @endif
                                                                @if($log->record_id)
                                                                    <div class="qr-meta-item">
                                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-3-3v6"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                                                                        <strong>Record ID:</strong> #{{ $log->record_id }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>


                                            @elseif($actionType === 'household')
                                                {{-- ── Structured Household Details (based on Household model) ── --}}
                                                @php
                                                    $ov = $log->old_values ?? [];
                                                    if (is_string($ov)) $ov = json_decode($ov, true) ?? [];
                                                    $hv = !empty($nv) ? $nv : $ov;
                                                    $hasDiff = !empty($ov) && !empty($nv);

                                                    $al = strtolower($log->action);
                                                    $badge = str_contains($al,'creat')||str_contains($al,'add')||str_contains($al,'register') ? 'created'
                                                           : (str_contains($al,'updat')||str_contains($al,'edit')||str_contains($al,'modif')   ? 'updated'
                                                           : (str_contains($al,'delet')||str_contains($al,'remov')                             ? 'deleted' : 'general'));
                                                    $badgeLabel = ['created'=>'Created','updated'=>'Updated','deleted'=>'Deleted','general'=>'Viewed'][$badge];

                                                    // Bool helper
                                                    $yesNo = fn($v) => ($v == 1 || $v === true || $v === 'true' || $v === '1') ? true : false;

                                                    // Date helper — convert to PH timezone (UTC+8) before formatting
                                                    $fmtDate = function($v) {
                                                        if (!$v) return null;
                                                        try { return \Carbon\Carbon::parse($v)->setTimezone('Asia/Manila')->format('M d, Y'); } catch (\Exception $e) { return $v; }
                                                    };

                                                    // Hide noisy fields depending on the action
                                                    $isCreated  = str_contains($al,'creat') || str_contains($al,'add') || str_contains($al,'register');
                                                    $isApproved = str_contains($al,'approv');

                                                    // For diff: only changed scalar fields, exclude noisy timestamps
                                                    $skipDiff = ['updated_at','created_at','deleted_at'];
                                                    $diffKeys = $hasDiff
                                                        ? array_keys(array_filter((array)$nv, fn($v,$k) => !in_array($k,$skipDiff) && is_scalar($v) && isset($ov[$k]) && (string)$ov[$k] !== (string)$v, ARRAY_FILTER_USE_BOTH))
                                                        : [];

                                                    // Label map
                                                    $lbl = [
                                                        'household_head_name' => 'Household Head',
                                                        'sex'                 => 'Sex',
                                                        'birthday'            => 'Birthday',
                                                        'civil_status'        => 'Civil Status',
                                                        'contact_number'      => 'Contact Number',
                                                        'house_number'        => 'House No.',
                                                        'street_purok'        => 'Street / Purok',
                                                        'barangay'            => 'Barangay',
                                                        'municipality'        => 'Municipality',
                                                        'province'            => 'Province',
                                                        'listahanan_id'       => 'Listahanan ID',
                                                        'is_4ps_beneficiary'  => '4Ps Beneficiary',
                                                        'is_pwd'              => 'PWD',
                                                        'is_senior'           => 'Senior Citizen',
                                                        'is_solo_parent'      => 'Solo Parent',
                                                        'status'              => 'Status',
                                                        'encoded_by'          => 'Encoded By (ID)',
                                                        'approved_by'         => 'Approved By (ID)',
                                                        'qr_code_path'        => 'QR Code Path',
                                                        'created_at'          => 'Date Created',
                                                        'updated_at'          => 'Last Updated',
                                                    ];
                                                @endphp
                                                <div class="hh-wrap">
                                                    {{-- Icon --}}
                                                    <div class="hh-icon-box">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                                            <path d="M9 22V12h6v10"/>
                                                        </svg>
                                                        <div class="hh-icon-label">Household</div>
                                                    </div>

                                                    <div class="hh-body">
                                                        {{-- Name / address header bar --}}
                                                        <div class="hh-name-bar">
                                                            <div>
                                                                <div class="hh-name-text">
                                                                    {{ $hv['household_head_name'] ?? $log->affected_name ?? 'Household Record' }}
                                                                </div>
                                                                @php
                                                                    $addrParts = array_filter([
                                                                        !empty($hv['house_number']) ? 'No. '.$hv['house_number'] : null,
                                                                        $hv['street_purok'] ?? null,
                                                                        !empty($hv['barangay']) ? 'Brgy. '.$hv['barangay'] : null,
                                                                        $hv['municipality'] ?? null,
                                                                        $hv['province'] ?? null,
                                                                    ]);
                                                                @endphp
                                                                @if(count($addrParts))
                                                                    <div class="hh-name-sub">
                                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                                        {{ implode(', ', $addrParts) }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <span class="hh-badge {{ $badge }}">{{ $badgeLabel }}</span>
                                                        </div>

                                                        @if(!$hasDiff)
                                                            {{-- ── Full / partial record view ── --}}
                                                            @php
                                                                // Pre-check which sections have data, so we don't render empty section headers
                                                                $hasPersonal = !empty($hv['sex']) || !empty($hv['birthday']) || !empty($hv['civil_status']) || !empty($hv['contact_number']);
                                                                $hasClassify = isset($hv['is_4ps_beneficiary']) || isset($hv['is_pwd']) || isset($hv['is_senior']) || isset($hv['is_solo_parent']) || !empty($hv['listahanan_id']);
                                                                $hasRecord   = !empty($hv['status']) || (isset($hv['encoded_by']) && $hv['encoded_by'] !== null && !$isCreated) || (isset($hv['approved_by']) && $hv['approved_by'] !== null && !$isApproved) || !empty($hv['created_at']) || (!empty($hv['updated_at']) && !$isCreated);
                                                                $totalFields = ($hasPersonal?1:0) + ($hasClassify?1:0) + ($hasRecord?1:0);
                                                            @endphp
                                                            <div class="hh-grid">

                                                                {{-- SECTION: Personal Info --}}
                                                                @if($hasPersonal)
                                                                    @if($totalFields > 1)<div class="hh-section-label">Personal Information</div>@endif
                                                                    @if(!empty($hv['sex']))
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Sex</div>
                                                                            <div class="hh-field-val">{{ ucfirst($hv['sex']) }}</div>
                                                                        </div>
                                                                    @endif
                                                                    @if(!empty($hv['birthday']))
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Birthday</div>
                                                                            <div class="hh-field-val">{{ $fmtDate($hv['birthday']) }}</div>
                                                                        </div>
                                                                    @endif
                                                                    @if(!empty($hv['civil_status']))
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Civil Status</div>
                                                                            <div class="hh-field-val">{{ ucfirst($hv['civil_status']) }}</div>
                                                                        </div>
                                                                    @endif
                                                                    @if(!empty($hv['contact_number']))
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Contact Number</div>
                                                                            <div class="hh-field-val">{{ $hv['contact_number'] }}</div>
                                                                        </div>
                                                                    @endif
                                                                @endif

                                                                {{-- SECTION: Classifications --}}
                                                                @if($hasClassify)
                                                                    @if($totalFields > 1)<div class="hh-section-label">Classifications</div>@endif
                                                                    @foreach(['is_4ps_beneficiary'=>'4Ps Beneficiary','is_pwd'=>'PWD','is_senior'=>'Senior Citizen','is_solo_parent'=>'Solo Parent'] as $bk => $bl)
                                                                        @if(isset($hv[$bk]))
                                                                            @php $bval = $yesNo($hv[$bk]); @endphp
                                                                            <div class="hh-field">
                                                                                <div class="hh-field-label">{{ $bl }}</div>
                                                                                <div class="hh-field-val {{ $bval ? 'bool-yes' : 'bool-no' }}">
                                                                                    @if($bval)
                                                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Yes
                                                                                    @else
                                                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> No
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                    @if(!empty($hv['listahanan_id']))
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Listahanan ID</div>
                                                                            <div class="hh-field-val mono">{{ $hv['listahanan_id'] }}</div>
                                                                        </div>
                                                                    @endif
                                                                @endif

                                                                {{-- SECTION: Record Info --}}
                                                                @if($hasRecord)
                                                                    @if($totalFields > 1)<div class="hh-section-label">Record Information</div>@endif
                                                                    @if(!empty($hv['status']))
                                                                        @php $st = strtolower($hv['status']); @endphp
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Status</div>
                                                                            <div class="hh-field-val status-{{ in_array($st,['active','inactive','pending']) ? $st : '' }}">
                                                                                {{ ucfirst($hv['status']) }}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($hv['encoded_by']) && $hv['encoded_by'] !== null && !$isCreated)
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Encoded By (User ID)</div>
                                                                            <div class="hh-field-val mono">{{ $hv['encoded_by'] }}</div>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($hv['approved_by']) && $hv['approved_by'] !== null && !$isApproved)
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Approved By (User ID)</div>
                                                                            <div class="hh-field-val mono">{{ $hv['approved_by'] }}</div>
                                                                        </div>
                                                                    @endif
                                                                    @if(!empty($hv['created_at']))
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Date Created</div>
                                                                            <div class="hh-field-val">{{ $fmtDate($hv['created_at']) }}</div>
                                                                        </div>
                                                                    @endif
                                                                    @if(!empty($hv['updated_at']) && !$isCreated)
                                                                        <div class="hh-field">
                                                                            <div class="hh-field-label">Last Updated</div>
                                                                            <div class="hh-field-val">{{ $fmtDate($hv['updated_at']) }}</div>
                                                                        </div>
                                                                    @endif
                                                                @endif

                                                            </div>{{-- /.hh-grid --}}

                                                        @else
                                                            {{-- ── Before / After diff view ── --}}
                                                            <div class="hh-diff-wrap">
                                                                <div class="hh-diff-block">
                                                                    <div class="hh-diff-head before">
                                                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
                                                                        Before
                                                                    </div>
                                                                    <div class="hh-diff-body">
                                                                        @forelse(array_intersect_key((array)$ov, array_flip($diffKeys)) as $dk => $dv)
                                                                            @php
                                                                                $dlbl = $lbl[$dk] ?? ucwords(str_replace('_',' ',$dk));
                                                                                $dfmt = in_array($dk,['birthday','created_at','updated_at']) ? $fmtDate($dv) : (in_array($dk,['is_pwd','is_senior','is_solo_parent','is_4ps_beneficiary']) ? ($yesNo($dv)?'Yes':'No') : $dv);
                                                                            @endphp
                                                                            <div class="hh-diff-row">
                                                                                <div class="hh-diff-key">{{ $dlbl }}</div>
                                                                                <div class="hh-diff-val">{{ is_array($dv) ? json_encode($dv) : ucfirst((string)$dfmt) }}</div>
                                                                            </div>
                                                                        @empty
                                                                            <div style="font-size:11px;color:var(--gray-400);font-style:italic;">No prior values.</div>
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                                <div class="hh-diff-block">
                                                                    <div class="hh-diff-head after">
                                                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><polyline points="9 12 11 14 15 10"/></svg>
                                                                        After
                                                                    </div>
                                                                    <div class="hh-diff-body">
                                                                        @forelse(array_intersect_key((array)$nv, array_flip($diffKeys)) as $dk => $dv)
                                                                            @php
                                                                                $dlbl = $lbl[$dk] ?? ucwords(str_replace('_',' ',$dk));
                                                                                $dfmt = in_array($dk,['birthday','created_at','updated_at']) ? $fmtDate($dv) : (in_array($dk,['is_pwd','is_senior','is_solo_parent','is_4ps_beneficiary']) ? ($yesNo($dv)?'Yes':'No') : $dv);
                                                                            @endphp
                                                                            <div class="hh-diff-row">
                                                                                <div class="hh-diff-key">{{ $dlbl }}</div>
                                                                                <div class="hh-diff-val new-val">{{ is_array($dv) ? json_encode($dv) : ucfirst((string)$dfmt) }}</div>
                                                                            </div>
                                                                        @empty
                                                                            <div style="font-size:11px;color:var(--gray-400);font-style:italic;">No updated values.</div>
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Meta bar --}}
                                                        <div class="hh-meta-bar">
                                                            @if($log->user_name)
                                                                <div class="hh-meta-item">
                                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                                                    <strong>By:</strong> {{ $log->user_name }}
                                                                </div>
                                                            @endif
                                                            @if($log->created_at)
                                                                <div class="hh-meta-item">
                                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 6 12 12 16 14"/></svg>
                                                                    <strong>Time:</strong> {{ $log->created_at->format('M d, Y h:i:s A') }}
                                                                </div>
                                                            @endif
                                                            @if($log->record_id)
                                                                <div class="hh-meta-item">
                                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-3-3v6"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                                                                    <strong>Record ID:</strong> #{{ $log->record_id }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                    </div>{{-- /.hh-body --}}
                                                </div>{{-- /.hh-wrap --}}

                                            @else
                                                {{-- Default: old/new JSON view --}}
                                                <div class="details-grid">
                                                    @if($log->old_values)
                                                        <div>
                                                            <div class="details-block-title">Before (Old Values)</div>
                                                            <div class="details-json">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                                                        </div>
                                                    @endif
                                                    @if($log->new_values)
                                                        <div>
                                                            <div class="details-block-title">After (New Values)</div>
                                                            <div class="details-json">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" style="padding:48px;text-align:center;color:var(--gray-400);font-style:italic;">
                                    No audit log entries found.
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

<script>
    function pad(n) { return String(n).padStart(2, '0'); }
    function updateClock() {
        const now = new Date();
        const days  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const months= ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+months[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const type   = document.getElementById('typeFilter').value;
        const rows   = document.querySelectorAll('.audit-row');
        let visible  = 0;
        rows.forEach(row => {
            const matchSearch = !search || row.dataset.search.includes(search);
            const matchType   = !type   || row.dataset.type === type;
            const show = matchSearch && matchType;
            row.style.display = show ? '' : 'none';
            const next = row.nextElementSibling;
            if (next && next.classList.contains('details-row')) {
                next.style.display = show ? (next.dataset.open ? '' : 'none') : 'none';
            }
            if (show) visible++;
        });
        document.getElementById('visibleCount').textContent = visible;
    }

    function toggleDetails(id) {
        const row = document.getElementById('details-' + id);
        const isOpen = row.style.display === 'table-row';
        row.style.display = isOpen ? 'none' : 'table-row';
        row.dataset.open = isOpen ? '' : '1';
    }

    // ── Rebuild pagination cleanly ──
    (function normalizePagination() {
        const container = document.getElementById('paginationRow');
        if (!container) return;
        const nav = container.querySelector('nav');
        if (!nav) return;

        const CHEVRON_L = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0"><polyline points="15 18 9 12 15 6"/></svg>`;
        const CHEVRON_R = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>`;

        const infoEl = nav.querySelector('p');
        const infoHTML = infoEl ? infoEl.innerHTML : '';

        let prevHTML = '', nextHTML = '', pageButtons = '';

        // Only grab direct children of the button wrapper div to avoid duplicates
        const btnGroup = nav.querySelector('div') || nav;
        Array.from(btnGroup.children).forEach(el => {
            // For spans wrapping a span (Laravel's current page pattern), unwrap
            const inner = el.children.length === 1 && el.children[0].tagName === 'SPAN' ? el.children[0] : el;
            const text = inner.textContent.trim().replace(/[\s\u00a0]+/g, ' ');
            const isLink = el.tagName === 'A';
            const isCurrent = el.getAttribute('aria-current') === 'page'
                           || inner.getAttribute('aria-current') === 'page';
            const href = el.getAttribute('href') || '#';

            if (/previous/i.test(text) || text === '\u00ab' || text === '\u2039') {
                prevHTML = isLink
                    ? `<a href="${href}" class="pg-btn nav-btn">${CHEVRON_L} Previous</a>`
                    : `<span class="pg-btn nav-btn disabled">${CHEVRON_L} Previous</span>`;
                return;
            }
            if (/next/i.test(text) || text === '\u00bb' || text === '\u203a') {
                nextHTML = isLink
                    ? `<a href="${href}" class="pg-btn nav-btn">Next ${CHEVRON_R}</a>`
                    : `<span class="pg-btn nav-btn disabled">Next ${CHEVRON_R}</span>`;
                return;
            }
            if (text === '...' || text === '\u2026') {
                pageButtons += `<span class="pg-dots">\u00b7\u00b7\u00b7</span>`;
                return;
            }
            if (!/^\d+$/.test(text)) return;
            pageButtons += isCurrent
                ? `<span class="pg-btn active">${text}</span>`
                : `<a href="${href}" class="pg-btn">${text}</a>`;
        });

        container.innerHTML = `
            <div class="pg-bar">
                <div class="pg-controls">
                    ${prevHTML}
                    <div style="display:flex;align-items:center;gap:4px;">${pageButtons}</div>
                    ${nextHTML}
                </div>
                ${infoHTML ? `<div class="pg-info">${infoHTML}</div>` : ''}
            </div>`;
    })();
</script>
</body>
</html>