<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — {{ $household->household_head_name }}</title>
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
        }

        /* ─── TOP UTILITY BAR ─── */
        .topbar {
            grid-area: topbar;
            background: var(--blue-dark);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 100;
        }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
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
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--blue-pale); border: 1px solid var(--gray-200); border-radius: 4px; flex-shrink: 0; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }

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
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .titlebar-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }

        /* ─── ALERTS ─── */
        .alert-success { background: var(--green-pale); border: 1px solid #BBF7D0; border-left: 4px solid var(--green); padding: 12px 16px; margin-bottom: 16px; font-size: 13px; color: var(--green-dark); display: flex; align-items: center; gap: 10px; }
        .alert-success svg { width: 16px; height: 16px; flex-shrink: 0; }
        .alert-danger { background: var(--red-pale); border: 1px solid #FECACA; border-left: 4px solid var(--red); padding: 12px 16px; margin-bottom: 16px; font-size: 13px; color: var(--red); display: flex; align-items: flex-start; gap: 10px; }
        .alert-danger svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

        /* ─── HERO IDENTITY CARD ─── */
        .household-hero {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 4px solid var(--blue);
            padding: 24px 28px;
            display: flex; align-items: center; gap: 24px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .hero-avatar { width: 64px; height: 64px; border-radius: 4px; background: var(--blue-pale); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .hero-avatar svg { width: 30px; height: 30px; color: var(--blue); }
        .hero-info { flex: 1; min-width: 0; }
        .hero-name { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .hero-meta { font-size: 12px; color: var(--gray-600); margin-top: 4px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .hero-meta-sep { color: var(--gray-200); }
        .hero-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }
        .hero-serial { display: flex; align-items: center; gap: 8px; }
        .serial-display { font-family: monospace; font-size: 16px; font-weight: 700; color: var(--blue); letter-spacing: 1.5px; background: var(--blue-pale); border: 1px solid #C7D9F3; padding: 6px 14px; border-radius: 3px; }
        .serial-unassigned { font-size: 12px; color: var(--gray-400); font-style: italic; background: var(--gray-50); border: 1px dashed var(--gray-200); padding: 6px 14px; border-radius: 3px; }

        /* ─── HERO QR SCAN COUNTER ─── */
        .hero-scan-counter {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px;
            border-radius: 3px;
            border: 1px solid;
        }
        .hero-scan-counter.has-scans {
            background: var(--green-pale);
            border-color: #BBF7D0;
            color: var(--green-dark);
        }
        .hero-scan-counter.no-scans {
            background: var(--gray-100);
            border-color: var(--gray-200);
            color: var(--gray-400);
        }
        .hero-scan-counter svg { width: 13px; height: 13px; flex-shrink: 0; }
        .hero-scan-counter-number { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; line-height: 1; }
        .hero-scan-counter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1.3; }

        /* ─── STATUS + SECTOR BADGES ROW ─── */
        .hero-badges { display: flex; align-items: center; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .badge svg { width: 10px; height: 10px; }
        .badge-approved { background: var(--green-pale); color: var(--green-dark); }
        .badge-pending  { background: var(--orange-pale); color: #92400E; }
        .badge-blue     { background: var(--blue-pale); color: var(--blue); }
        .badge-gray     { background: var(--gray-100); color: var(--gray-600); }

        /* ─── TWO-COLUMN DETAIL LAYOUT ─── */
        .detail-layout { display: flex; gap: 20px; align-items: flex-start; }
        .detail-main { order: 1; flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 20px; }
        .detail-side { order: 2; width: 300px; flex-shrink: 0; display: flex; flex-direction: column; gap: 20px; }

        /* ─── SECTION CARD ─── */
        .section-card { background: var(--white); border: 1px solid var(--gray-200); }
        .section-header { padding: 12px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .section-body { padding: 20px; }

        /* ─── INFO GRID ─── */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
        .info-item { padding: 12px 14px; background: var(--gray-50); border: 1px solid var(--gray-100); border-left: 3px solid var(--blue-light); }
        .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); margin-bottom: 4px; }
        .info-value { font-size: 13px; color: var(--gray-800); font-weight: 500; line-height: 1.4; }
        .info-value.mono { font-family: monospace; font-size: 13px; color: var(--blue); font-weight: 700; letter-spacing: 0.5px; }
        .info-value em { color: var(--gray-400); font-style: italic; font-weight: 400; }

        /* ─── ADDRESS BLOCK ─── */
        .address-block { padding: 14px; background: var(--gray-50); border: 1px solid var(--gray-100); border-left: 3px solid var(--blue-light); }
        .address-line1 { font-size: 14px; color: var(--gray-800); font-weight: 500; }
        .address-line2 { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        /* ─── SECTOR FLAGS ─── */
        .sector-flags { display: flex; flex-wrap: wrap; gap: 8px; }
        .sector-flag { display: inline-flex; align-items: center; gap: 7px; padding: 7px 14px; background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; border-radius: 3px; font-size: 12px; font-weight: 600; }
        .sector-flag svg { width: 13px; height: 13px; }
        .sector-none { font-size: 12px; color: var(--gray-400); font-style: italic; }

        /* ─── MEMBERS TABLE ─── */
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .members-table { width: 100%; border-collapse: collapse; min-width: 700px; }
        .members-table thead th { padding: 10px 14px; background: var(--blue); color: var(--white); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; white-space: nowrap; }
        .members-table tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        .members-table tbody tr:hover { background: var(--blue-pale); }
        .members-table tbody tr:last-child { border-bottom: none; }
        .members-table tbody td { padding: 11px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        .members-table tbody tr:nth-child(even) td { background: var(--gray-50); }
        .members-table tbody tr:nth-child(even):hover td { background: var(--blue-pale); }
        .member-name { font-weight: 600; color: var(--blue-dark); }
        .no-members { padding: 32px; text-align: center; color: var(--gray-400); font-style: italic; font-size: 13px; }

        /* ─── QR CARD ─── */
        .qr-card { padding: 20px; text-align: center; }
        .qr-frame { display: inline-block; padding: 10px; background: var(--white); margin-bottom: 12px; }
        .qr-frame-household { border: 2px solid var(--blue-pale); }
        .qr-frame-head { border: 2px solid #DDD6FE; }
        .qr-frame img { width: 160px; height: 160px; display: block; }
        .qr-serial { font-family: monospace; font-size: 14px; font-weight: 700; color: var(--blue); letter-spacing: 1.5px; margin-bottom: 3px; }
        .qr-name { font-size: 12px; color: var(--gray-600); margin-bottom: 6px; }
        .qr-type-pill { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; margin-bottom: 6px; }
        .qr-pill-household { background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F3; }
        .qr-pill-head { background: #F5F3FF; color: #6D28D9; border: 1px solid #DDD6FE; }
        .qr-meta { font-size: 11px; color: var(--gray-400); line-height: 1.6; }
        .qr-placeholder { padding: 24px 20px; text-align: center; background: var(--gray-50); border: 2px dashed var(--gray-200); margin: 0 16px 16px; }
        .qr-placeholder-icon { width: 44px; height: 44px; border-radius: 50%; background: var(--gray-100); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
        .qr-placeholder-icon svg { width: 20px; height: 20px; color: var(--gray-400); }
        .qr-placeholder p { font-size: 12px; color: var(--gray-400); margin-bottom: 8px; }
        .qr-must-approve { font-size: 12px; font-weight: 700; color: var(--red); }

        /* QR section sub-labels */
        .qr-section-label { display: flex; align-items: center; gap: 7px; padding: 8px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid var(--gray-100); }
        .qr-section-label svg { width: 13px; height: 13px; flex-shrink: 0; }
        .qr-label-household { background: var(--blue-pale); color: var(--blue); }
        .qr-label-head      { background: #F5F3FF; color: #6D28D9; }
        .qr-divider { border: none; border-top: 2px dashed var(--gray-200); margin: 4px 0; }

        /* Member family head badge */
        .member-head-badge { display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; background: #FFF7ED; color: #92400E; border: 1px solid #FDE68A; margin-left: 6px; white-space: nowrap; }
        .member-head-badge svg { width: 9px; height: 9px; flex-shrink: 0; }

        /* Employment status badges */
        .emp-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .emp-employed        { background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; }
        .emp-unemployed      { background:#FEF2F2; color:#C0392B; border:1px solid #FECACA; }
        .emp-part_time       { background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; }
        .emp-full_time       { background:#EAF0FA; color:#1B3F7A; border:1px solid #C7D9F3; }
        .emp-self_employed   { background:#F5F3FF; color:#6D28D9; border:1px solid #DDD6FE; }
        .emp-pension         { background:#F0FDF4; color:#166534; border:1px solid #BBF7D0; }
        .emp-freelance       { background:#FFF7ED; color:#9A3412; border:1px solid #FED7AA; }
        .emp-other           { background:var(--gray-100); color:var(--gray-600); border:1px solid var(--gray-200); }

        /* QR type tag in members table */
        .qr-type-tag { display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 10px; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .qr-type-tag svg { width: 10px; height: 10px; flex-shrink: 0; }
        .qr-tag-head { background: #F5F3FF; color: #6D28D9; border: 1px solid #DDD6FE; }
        .qr-tag-none { background: var(--gray-100); color: var(--gray-400); border: 1px solid var(--gray-200); font-style: italic; font-weight: 500; }

        /* ─── CUSTOM CONFIRM MODAL ─── */
        .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center; pointer-events:none; }
        .modal-backdrop.show { display:flex; pointer-events:auto; }
        .modal-box { background:var(--white); border-radius:6px; box-shadow:0 8px 32px rgba(0,0,0,0.22); width:100%; max-width:400px; margin:16px; overflow:hidden; animation:modalIn .18s ease; }
        @keyframes modalIn { from{opacity:0;transform:scale(.96)} to{opacity:1;transform:scale(1)} }
        .modal-header { padding:18px 22px 14px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--gray-100); }
        .modal-icon { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .modal-icon svg { width:18px; height:18px; }
        .modal-icon.confirm  { background:var(--blue-pale); color:var(--blue); }
        .modal-icon.danger   { background:var(--red-pale);  color:var(--red); }
        .modal-icon.warning  { background:#FFFBEB; color:#D97706; }
        .modal-icon.purple   { background:#F5F3FF; color:#7C3AED; }
        .modal-title { font-family:'PT Serif',serif; font-size:16px; font-weight:700; color:var(--blue-dark); }
        .modal-body { padding:14px 22px 20px; font-size:13px; color:var(--gray-600); line-height:1.6; }
        .modal-footer { padding:12px 22px; background:var(--gray-50); border-top:1px solid var(--gray-100); display:flex; justify-content:flex-end; gap:8px; }
        .modal-btn { font-family:'Open Sans',sans-serif; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; padding:9px 20px; border-radius:3px; border:none; cursor:pointer; transition:background .15s; }
        .modal-btn-cancel  { background:var(--white); color:var(--gray-600); border:1px solid var(--gray-200); }
        .modal-btn-cancel:hover  { background:var(--gray-100); }
        .modal-btn-confirm { background:var(--green); color:var(--white); }
        .modal-btn-confirm:hover { background:var(--green-dark); }
        .modal-btn-danger  { background:var(--red); color:var(--white); }
        .modal-btn-danger:hover  { background:#A93226; }
        .modal-btn-blue    { background:var(--blue); color:var(--white); }
        .modal-btn-blue:hover    { background:var(--blue-dark); }
        .modal-btn-purple  { background:#7C3AED; color:var(--white); }
        .modal-btn-purple:hover  { background:#6D28D9; }
        .modal-btn-warning { background:#D97706; color:var(--white); }
        .modal-btn-warning:hover { background:#B45309; }
        .record-info-stack { display: flex; flex-direction: column; gap: 0; }
        .record-info-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; border-bottom: 1px solid var(--gray-100); gap: 12px; }
        .record-info-row:last-child { border-bottom: none; }
        .record-info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-400); flex-shrink: 0; }
        .record-info-value { font-size: 13px; color: var(--gray-800); font-weight: 500; text-align: right; }

        /* QR scan count inside record info */
        .scan-count-inline {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 13px; font-weight: 700;
        }
        .scan-count-inline.has-scans { color: var(--green-dark); }
        .scan-count-inline.no-scans  { color: var(--gray-400); font-weight: 500; }
        .scan-count-inline svg { width: 12px; height: 12px; }

        /* ─── ACTIONS CARD ─── */
        .actions-stack { padding: 16px 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-action { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 10px 16px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; transition: background 0.15s, color 0.15s; }
        .btn-action svg { width: 14px; height: 14px; flex-shrink: 0; }
        .btn-action-approve  { background: var(--green); color: var(--white); }
        .btn-action-approve:hover { background: var(--green-dark); }
        .btn-action-unapprove { background: var(--orange-pale); color: var(--orange); border: 1px solid #FDE68A; }
        .btn-action-unapprove:hover { background: #FDE68A; }
        .btn-action-edit     { background: var(--orange); color: var(--white); }
        .btn-action-edit:hover { background: #B45309; }
        .btn-action-download { background: var(--blue-pale); color: var(--blue); border: 1px solid #C7D9F3; }
        .btn-action-download:hover { background: #C7D9F3; }
        .btn-action-delete   { background: var(--red-pale); color: var(--red); border: 1px solid #FECACA; }
        .btn-action-delete:hover { background: #FECACA; }
        .actions-divider { border: none; border-top: 1px solid var(--gray-100); margin: 4px 0; }

        .back-btn { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; padding: 8px 16px; border: 1px solid var(--gray-200); background: var(--white); border-radius: 4px; transition: background 0.15s; white-space: nowrap; }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

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

        /* ════════════════════════════════════════
           RESPONSIVE
           ════════════════════════════════════════ */
        @media (max-width: 1100px) {
            .detail-layout { flex-direction: column; }
            .detail-side { width: 100%; flex-direction: row; flex-wrap: wrap; }
            .detail-side .section-card { flex: 1; min-width: 280px; }
        }
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
            .header-user-badge { padding: 6px 10px; gap: 8px; }
            .user-name { font-size: 12px; }
            .user-role { display: none; }
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .main-content { padding: 20px 16px; }
            .detail-side { grid-template-columns: 1fr; }
        }
        @media (max-width: 720px) {
            .info-grid { grid-template-columns: 1fr; }
            .info-grid-3 { grid-template-columns: 1fr 1fr; }
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
            .page-titlebar { flex-direction: column; align-items: flex-start; gap: 10px; }
            .page-h1 { font-size: 18px; }
            .titlebar-actions { width: 100%; }
            .back-btn { flex: 1; justify-content: center; }
            .household-hero { padding: 16px; gap: 14px; }
            .hero-name { font-size: 18px; }
            .hero-right { align-items: flex-start; }
            .info-grid-3 { grid-template-columns: 1fr; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
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
                <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="user-role">Full Access</div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close navigation">
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
            Dashboard
        </a>
        <a href="{{ route('admin.households.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            Households
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
                <div class="page-breadcrumb">Home / <span>Dashboard</span></div>
                <div class="page-h1">Dashboard Overview</div>
                <div class="page-sub">Barangay Family Track QR Relief Distribution System — MDRRMO Naic, Cavite</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            </div>
        @endif

        @php $scanCount = $household->distributionLogs->count(); @endphp

        {{-- Hero Identity Card --}}
        <div class="household-hero">
            <div class="hero-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
                </svg>
            </div>
            <div class="hero-info">
                <div class="hero-name">{{ $household->household_head_name }}</div>
                @php $heroHead = $household->members->firstWhere('is_family_head', 1); @endphp
                <div class="hero-meta">
                    @if($heroHead)
                        <span>{{ $heroHead->sex }}</span>
                        <span class="hero-meta-sep">|</span>
                        <span>{{ $heroHead->age }} years old</span>
                        <span class="hero-meta-sep">|</span>
                        <span>{{ $heroHead->civil_status ?? '—' }}</span>
                        <span class="hero-meta-sep">|</span>
                    @endif
                    <span>{{ $household->barangay }}, {{ $household->municipality }}</span>
                </div>
                <div class="hero-badges">
                    @if($household->isApproved())
                        <span class="badge badge-approved">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            Approved
                        </span>
                    @else
                        <span class="badge badge-pending">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Pending Approval
                        </span>
                    @endif
                    @if($household->is_4ps_beneficiary)<span class="badge badge-blue">4Ps</span>@endif
                    @if($household->is_pwd)<span class="badge badge-blue">PWD</span>@endif
                    @if($household->is_senior)<span class="badge badge-blue">Senior</span>@endif
                    @if($household->is_solo_parent)<span class="badge badge-blue">Solo Parent</span>@endif
                </div>
            </div>

            {{-- Right column: serial + scan counter --}}
            <div class="hero-right">
                <div class="hero-serial">
                    @if($household->serial_code)
                        <span class="serial-display">{{ $household->serial_code }}</span>
                    @else
                        <span class="serial-unassigned">No serial code yet</span>
                    @endif
                </div>
                <div class="hero-scan-counter {{ $scanCount > 0 ? 'has-scans' : 'no-scans' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                        <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                        <rect x="8" y="8" width="8" height="8" rx="1"/>
                    </svg>
                    <div>
                        <div class="hero-scan-counter-number">{{ $scanCount }}</div>
                        <div class="hero-scan-counter-label">QR Scan{{ $scanCount !== 1 ? 's' : '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Two-column detail layout --}}
        <div class="detail-layout">

            <div class="detail-main">

                {{-- Household Head Info --}}
                @php $headMember = $household->members->firstWhere('is_family_head', 1); @endphp
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot"></div>
                        <div class="section-title">Household Head Information</div>
                    </div>
                    <div class="section-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ $household->household_head_name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Sex</div>
                                <div class="info-value">{{ $headMember->sex ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Birthday</div>
                                <div class="info-value">
                                    {{ $headMember && $headMember->birthday ? $headMember->birthday->format('F d, Y') : '—' }}
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Age</div>
                                <div class="info-value">
                                    {{ $headMember && $headMember->age !== null ? $headMember->age . ' years old' : '—' }}
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Civil Status</div>
                                <div class="info-value">{{ $headMember->civil_status ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value">
                                    @if($household->contact_number) {{ $household->contact_number }}
                                    @else <em>N/A</em> @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email Address</div>
                                <div class="info-value">
                                    @if($household->email) {{ $household->email }}
                                    @else <em>N/A</em> @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Listahanan ID</div>
                                <div class="info-value">
                                    @if($household->listahanan_id) <span class="mono">{{ $household->listahanan_id }}</span>
                                    @else <em>Not enrolled</em> @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Total Household Members</div>
                                <div class="info-value">{{ $household->total_members }} person(s)</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot"></div>
                        <div class="section-title">Address</div>
                    </div>
                    <div class="section-body">
                        <div class="address-block">
                            <div class="address-line1">
                                @if($household->house_number){{ $household->house_number }}, @endif
                                {{ $household->street_purok }}
                            </div>
                            <div class="address-line2">
                                {{ $household->barangay }}, {{ $household->municipality }}, {{ $household->province }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nuclear Families — grouped with full member details per family --}}
                @if($household->nuclearFamilies && $household->nuclearFamilies->count() > 0)
                @foreach($household->nuclearFamilies as $nfIdx => $nf)
                <div class="section-card">
                    <div class="section-header" style="background:{{ $nf->is_primary ? 'var(--blue-pale)' : 'var(--gray-50)' }};">
                        <div class="ca-dot" style="background:#7C3AED;border-color:#6D28D9;"></div>
                        <div class="section-title" style="flex:1;">
                            Nuclear Family {{ $nfIdx + 1 }}
                            @if($nf->family_name) — {{ $nf->family_name }} @endif
                            @if($nf->is_primary)
                                <span class="badge badge-blue" style="margin-left:8px;font-size:9px;">Primary</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:16px;font-size:11px;color:var(--gray-600);">
                            @if($nf->family_type)
                                <span style="background:var(--gray-100);padding:2px 8px;border-radius:10px;font-weight:600;">{{ $nf->family_type }}</span>
                            @endif
                            <span>Head: <strong style="color:var(--blue-dark);">{{ $nf->family_head ?? '—' }}</strong></span>
                            <span>{{ $nf->members->count() }} member{{ $nf->members->count() !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    @if($nf->members->count() > 0)
                    <div class="table-wrapper">
                        <table class="members-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Relationship</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Birthday</th>
                                    <th>Civil Status</th>
                                    <th>Education</th>
                                    <th>Employment</th>
                                    <th>Vulnerable Sector</th>
                                    <th>Flags</th>
                                    <th>PhilHealth</th>
                                    <th>QR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nf->members as $mi => $member)
                                @php $det = $member->detail; @endphp
                                <tr @if($member->is_family_head) style="background:var(--blue-pale);" @endif>
                                    <td style="color:var(--gray-400);font-size:11px;">{{ $mi + 1 }}</td>
                                    <td>
                                        <span class="member-name">{{ $member->full_name }}</span>
                                        @if($member->is_family_head)
                                            <span class="member-head-badge">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                Head
                                            </span>
                                        @endif
                                    </td>
                                    <td style="font-size:12px;">{{ $member->relationship ?? '—' }}</td>
                                    <td style="font-size:12px;">{{ $member->sex ?? '—' }}</td>
                                    <td style="font-size:12px;white-space:nowrap;">
                                        {{ $member->age !== null ? $member->age . ' y/o' : '—' }}
                                    </td>
                                    <td style="white-space:nowrap;font-size:12px;">
                                        {{ $member->birthday ? $member->birthday->format('M d, Y') : '—' }}
                                    </td>
                                    <td style="font-size:12px;">{{ $member->civil_status ?? '—' }}</td>
                                    <td style="font-size:12px;">{{ $member->educational_attainment ?? '—' }}</td>
                                    <td>
                                        @if($det && $det->employment_status)
                                            <span class="emp-badge emp-{{ Str::slug($det->employment_status,'_') }}">{{ $det->employment_status }}</span>
                                            @if($det->job_title)
                                                <small style="display:block;font-size:10px;color:var(--gray-600);margin-top:2px;">{{ $det->job_title }}</small>
                                            @endif
                                        @else
                                            <span style="color:var(--gray-400);font-size:11px;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($det && $det->vulnerable_sector && $det->vulnerable_sector !== 'None')
                                            <span class="badge badge-blue" style="font-size:9px;">{{ $det->vulnerable_sector }}</span>
                                            @if($det->vuln_registered !== null)
                                                <small style="display:block;font-size:10px;color:var(--gray-600);margin-top:2px;">
                                                    {{ $det->vuln_registered ? '✓ Registered' : '✗ Not Registered' }}
                                                    @if($det->vuln_id_number) — ID: {{ $det->vuln_id_number }} @endif
                                                </small>
                                            @endif
                                        @else
                                            <span style="color:var(--gray-400);font-size:11px;">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex;flex-direction:column;gap:2px;">
                                            @if($det && $det->is_lgbtqia)
                                                <span class="badge" style="background:#FDF4FF;color:#7E22CE;border:1px solid #E9D5FF;font-size:9px;">LGBTQIA+</span>
                                            @endif
                                            @if($member->is_pwd)
                                                <span class="badge badge-blue" style="font-size:9px;">PWD</span>
                                            @endif
                                            @if($member->is_student)
                                                <span class="badge badge-gray" style="font-size:9px;">Student</span>
                                            @endif
                                            @if($member->is_senior_citizen)
                                                <span class="badge badge-blue" style="font-size:9px;">Senior</span>
                                            @endif
                                            @php
                                                $anyFlag = ($det && $det->is_lgbtqia) || $member->is_pwd || $member->is_student || $member->is_senior_citizen;
                                            @endphp
                                            @if(!$anyFlag)
                                                <span style="color:var(--gray-400);font-size:11px;">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="font-size:11px;color:var(--gray-600);">
                                        {{ $member->philhealth_no ?? '—' }}
                                    </td>
                                    <td>
                                        @if($member->is_family_head && $member->qr_code_path)
                                            <span class="qr-type-tag qr-tag-head">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="21" y2="14"/><line x1="14" y1="14" x2="14" y2="21"/></svg>
                                                Has QR
                                            </span>
                                        @elseif($member->is_family_head)
                                            <span class="qr-type-tag qr-tag-none">No QR</span>
                                        @else
                                            <span style="font-size:11px;color:var(--gray-400);">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="no-members">No members in this nuclear family.</div>
                    @endif
                </div>
                @endforeach
                @else
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot"></div>
                        <div class="section-title">Family Members ({{ $household->members->count() }})</div>
                    </div>
                    <div class="no-members">No nuclear families registered yet.</div>
                </div>
                @endif

                {{-- Housing Information --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot" style="background:#D97706;border-color:#B45309;"></div>
                        <div class="section-title">Housing Information</div>
                    </div>
                    <div class="section-body">
                        <div class="info-grid info-grid-3">
                            <div class="info-item">
                                <div class="info-label">Housing Type</div>
                                <div class="info-value">{{ $household->housing_type ? ucfirst(str_replace('_',' ',$household->housing_type)) : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Housing Material</div>
                                <div class="info-value">{{ $household->housing_material ? ucfirst(str_replace('_',' ',$household->housing_material)) : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Ownership Type</div>
                                <div class="info-value">{{ $household->ownership_type ? ucfirst(str_replace('_',' ',$household->ownership_type)) : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Year Built</div>
                                <div class="info-value">{{ $household->year_built ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Electricity Source</div>
                                <div class="info-value">{{ $household->electricity_source ? ucfirst(str_replace('_',' ',$household->electricity_source)) : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Water Source</div>
                                <div class="info-value">{{ $household->water_source ? ucfirst(str_replace('_',' ',$household->water_source)) : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Toilet Access</div>
                                <div class="info-value">{{ $household->toilet_access ? ucfirst(str_replace('_',' ',$household->toilet_access)) : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Waste Disposal</div>
                                <div class="info-value">{{ $household->waste_disposal ? ucfirst(str_replace('_',' ',$household->waste_disposal)) : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Barangay Area</div>
                                <div class="info-value">{{ $household->barangay_area ?? '—' }}</div>
                            </div>
                        </div>
                        @if($household->latitude && $household->longitude)
                        <div style="margin-top:12px;">
                            <div class="info-item" style="border-left-color:#16A34A;">
                                <div class="info-label">GPS Coordinates</div>
                                <div class="info-value mono">{{ $household->latitude }}, {{ $household->longitude }}</div>
                            </div>
                        </div>
                        @endif
                        @if($household->coordinates_image)
                        <div style="margin-top:12px;">
                            <div class="info-label" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--gray-400);margin-bottom:8px;">Location Photo</div>
                            <img src="{{ asset('storage/' . $household->coordinates_image) }}"
                                 alt="Location photo"
                                 style="max-width:100%;max-height:280px;border-radius:6px;border:1px solid var(--gray-200);object-fit:cover;display:block;">
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Risk Profile --}}
                @if($household->riskProfile)
                @php $rp = $household->riskProfile; @endphp
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot" style="background:#C0392B;border-color:#922B21;"></div>
                        <div class="section-title">Household Risk Profile</div>
                    </div>
                    <div class="section-body">
                        <div class="info-grid info-grid-3">
                            <div class="info-item">
                                <div class="info-label">Early Warning System</div>
                                <div class="info-value">
                                    @if($rp->early_warning)
                                        <span class="badge badge-approved">Yes</span>
                                        @if($rp->ews_sources)
                                            <div style="font-size:11px;color:var(--gray-600);margin-top:4px;">{{ implode(', ', array_map('strtoupper', explode(',', $rp->ews_sources))) }}</div>
                                        @endif
                                    @else
                                        <span class="badge badge-gray">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Hazard Awareness</div>
                                <div class="info-value">
                                    @if($rp->hazard_awareness)<span class="badge badge-approved">Yes</span>
                                    @else<span class="badge badge-gray">No</span>@endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Financial Assistance</div>
                                <div class="info-value">
                                    @if($rp->financial_assistance)<span class="badge badge-approved">Yes</span>
                                    @else<span class="badge badge-gray">No</span>@endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Access to Information</div>
                                <div class="info-value">
                                    @if($rp->access_info)<span class="badge badge-approved">Yes</span>
                                    @else<span class="badge badge-gray">No</span>@endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Willing to Relocate</div>
                                <div class="info-value">
                                    @if($rp->relocate_willingness)<span class="badge badge-approved">Yes</span>
                                    @else<span class="badge badge-gray">No</span>@endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Literacy Rate</div>
                                <div class="info-value">{{ $rp->literacy_rate !== null ? $rp->literacy_rate . '%' : '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Average Income</div>
                                <div class="info-value">{{ $rp->income_average ? '₱ ' . number_format($rp->income_average, 2) : '—' }}</div>
                            </div>
                        </div>
                        @if($rp->remarks)
                        <div style="margin-top:12px;">
                            <div class="info-item" style="border-left-color:#C0392B;">
                                <div class="info-label">Remarks</div>
                                <div class="info-value">{{ $rp->remarks }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Distribution History --}}
                @if($household->distributionLogs && $household->distributionLogs->count() > 0)
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot" style="background:#16A34A;border-color:#15803D;"></div>
                        <div class="section-title">Distribution History ({{ $household->distributionLogs->count() }} record{{ $household->distributionLogs->count() !== 1 ? 's' : '' }})</div>
                    </div>
                    <div class="table-wrapper">
                        <table class="members-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Event</th>
                                    <th>Serial Code</th>
                                    <th>Items Received</th>
                                    <th>Distributed By</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($household->distributionLogs->sortByDesc('distributed_at') as $li => $log)
                                <tr>
                                    <td style="color:var(--gray-400);font-size:11px;">{{ $li + 1 }}</td>
                                    <td style="font-weight:600;color:var(--blue-dark);font-size:12px;">
                                        {{ $log->event?->event_name ?? '—' }}
                                        @if($log->event)
                                            <small style="display:block;font-size:10px;color:var(--gray-400);font-weight:400;">
                                                {{ ucfirst($log->event->status) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <code style="background:var(--blue-pale);padding:2px 6px;border-radius:3px;font-size:11px;color:var(--blue);">
                                            {{ $log->serial_code ?? '—' }}
                                        </code>
                                    </td>
                                    <td style="font-size:12px;color:var(--gray-600);">
                                        @if($log->items_received && count($log->items_received))
                                            {{ collect($log->items_received)->map(fn($item) => ($item['qty'] ?? '') . ' ' . ($item['name'] ?? $item['key'] ?? ''))->implode(', ') }}
                                        @elseif($log->goods_detail)
                                            {{ $log->goods_detail }}
                                        @else
                                            <em style="color:var(--gray-400);">—</em>
                                        @endif
                                    </td>
                                    <td style="font-size:12px;color:var(--gray-600);">{{ $log->staff?->name ?? '—' }}</td>
                                    <td style="font-size:12px;white-space:nowrap;color:var(--gray-600);">
                                        {{ $log->distributed_at?->setTimezone('Asia/Manila')->format('M d, Y g:i A') ?? '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>{{-- /detail-main --}}

            <div class="detail-side">

                {{-- QR Codes --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot"></div>
                        <div class="section-title">QR Codes</div>
                    </div>

                    {{-- Household QR --}}
                    <div class="qr-section-label qr-label-household">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                        Household QR Code
                    </div>
                    @if($household->qrCode)
                        <div class="qr-card">
                            <div class="qr-frame qr-frame-household">
                                <img src="{{ asset('storage/' . $household->qrCode->file_path) }}" alt="Household QR Code">
                            </div>
                            <div class="qr-serial">{{ $household->serial_code }}</div>
                            <div class="qr-name">{{ $household->household_head_name }}</div>
                            <div class="qr-type-pill qr-pill-household">🏠 Household</div>
                            <div class="qr-meta">
                                Generated: {{ $household->qrCode->generated_at->format('M d, Y') }}<br>
                                Reprint Count: {{ $household->qrCode->reprint_count }}
                            </div>
                        </div>
                    @else
                        <div class="qr-placeholder">
                            <div class="qr-placeholder-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                    <rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="14" y2="21"/>
                                    <line x1="14" y1="14" x2="21" y2="14"/>
                                </svg>
                            </div>
                            <p>Household QR not generated yet.</p>
                            @if(!$household->isApproved())
                                <p class="qr-must-approve">Approve household first</p>
                            @endif
                        </div>
                    @endif

                    {{-- Divider --}}
                    <div class="qr-divider"></div>

                    {{-- Family Head QR — one card per family head (one per nuclear family) --}}
                    @php
    // Collect family heads from all nuclear families.
    // Primary source: is_family_head=1. Fallback: first member of each nuclear family
    // whose name matches nuclear_families.family_head (handles legacy data where is_family_head=0).
    $allFamilyHeads = $household->members->filter(function($m) use ($household) {
        if ($m->is_family_head) return true;
        // Fallback: check if this member is named as the head of their nuclear family
        if ($m->nuclearFamily && $m->nuclearFamily->family_head) {
            return strtolower(trim($m->full_name)) === strtolower(trim($m->nuclearFamily->family_head));
        }
        return false;
    })->unique('id');
@endphp
                    <div class="qr-section-label qr-label-head">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
                        Family Head QR Code{{ $allFamilyHeads->count() > 1 ? 's (' . $allFamilyHeads->count() . ')' : '' }}
                    </div>
                    @if($allFamilyHeads->isEmpty())
                        <div class="qr-placeholder">
                            <div class="qr-placeholder-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg></div>
                            <p>No family head registered yet.</p>
                        </div>
                    @else
                        @foreach($allFamilyHeads as $familyHead)
                            @if($familyHead->qr_code_path)
                                <div class="qr-card" style="{{ !$loop->first ? 'border-top:1px dashed var(--gray-200);padding-top:16px;' : '' }}">
                                    <div class="qr-frame qr-frame-head">
                                        <img src="{{ asset('storage/' . $familyHead->qr_code_path) }}" alt="Family Head QR Code">
                                    </div>
                                    <div class="qr-serial" style="color:#6D28D9;">{{ basename($familyHead->qr_code_path, '.svg') }}</div>
                                    <div class="qr-name">{{ $familyHead->full_name }}</div>
                                    <div class="qr-type-pill qr-pill-head">👤 Family Head</div>
                                    <div class="qr-meta">Linked to household record</div>
                                    <a href="{{ route('admin.households.qr.download-head', [$household, $familyHead]) }}"
                                       style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;padding:6px 12px;background:#F5F3FF;color:#6D28D9;border:1px solid #DDD6FE;border-radius:3px;font-size:11px;font-weight:700;text-decoration:none;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Download
                                    </a>
                                </div>
                            @else
                                <div class="qr-placeholder" style="{{ !$loop->first ? 'border-top:1px dashed var(--gray-200);margin-top:8px;' : '' }}">
                                    <div class="qr-placeholder-icon" style="background:#F5F3FF;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="1.5">
                                            <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                                        </svg>
                                    </div>
                                    <p style="font-size:12px;color:var(--gray-600);font-weight:600;">{{ $familyHead->full_name }}</p>
                                    <p>Family Head QR not generated yet.</p>
                                    @if($household->isApproved())
                                        <form method="POST" action="{{ route('admin.households.qr.generate-head', [$household, $familyHead]) }}" style="margin-top:8px;">
                                            @csrf
                                            <button type="button" class="btn-action btn-action-approve" style="background:#7C3AED;font-size:11px;padding:8px 12px;"
                                                onclick="openModal('head-qr', this.closest('form'), 'Generate Family Head QR', 'Generate a personal QR code for <strong>{{ $familyHead->full_name }}</strong>? This QR will be linked to their family head record.', 'Generate QR', 'purple')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="14" y2="21"/><line x1="14" y1="14" x2="21" y2="14"/></svg>
                                                Generate Head QR
                                            </button>
                                        </form>
                                    @else
                                        <p class="qr-must-approve">Approve household first</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                {{-- Actions --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot"></div>
                        <div class="section-title">Actions</div>
                    </div>
                    <div class="actions-stack">
                        @if(!$household->isApproved())
                            <form method="POST" action="{{ route('admin.households.approve', $household) }}">
                                @csrf
                                <button type="button" class="btn-action btn-action-approve"
                                    onclick="openModal('approve', this.closest('form'), 'Approve Household', 'Approve <strong>{{ $household->household_head_name }}</strong> and assign a serial code? This action will lock the record for editing.', 'Approve', 'confirm')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Approve Household
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.households.unapprove', $household) }}">
                                @csrf
                                <button type="button" class="btn-action btn-action-unapprove"
                                    onclick="openModal('unapprove', this.closest('form'), 'Remove Approval', 'Remove approval from <strong>{{ $household->household_head_name }}</strong>? The serial code will be revoked and the record will go back to pending.', 'Remove Approval', 'warning')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Unapprove
                                </button>
                            </form>
                        @endif
                        @if($household->isApproved() && !$household->qrCode)
                            <form method="POST" action="{{ route('admin.households.qr.generate', $household) }}">
                                @csrf
                                <button type="button" class="btn-action btn-action-approve" style="background: var(--blue);"
                                    onclick="openModal('qr', this.closest('form'), 'Generate Household QR Code', 'Generate a QR code for <strong>{{ $household->household_head_name }}</strong> ({{ $household->serial_code }})? The QR code will be linked to this household record.', 'Generate QR', 'blue')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                        <rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="14" y2="21"/>
                                        <line x1="14" y1="14" x2="21" y2="14"/>
                                    </svg>
                                    Generate Household QR
                                </button>
                            </form>
                        @endif
                        @if($household->qrCode)
                            <a href="{{ route('admin.households.qr.download', $household) }}" class="btn-action btn-action-download">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Download Household QR
                            </a>
                        @endif
                        {{-- Generate / Download for EVERY family head (one per nuclear family) --}}
                        @foreach($household->members->filter(function($m) use ($household) {
    if ($m->is_family_head) return true;
    if ($m->nuclearFamily && $m->nuclearFamily->family_head) {
        return strtolower(trim($m->full_name)) === strtolower(trim($m->nuclearFamily->family_head));
    }
    return false;
})->unique('id') as $familyHeadAction)
                            @if($household->isApproved() && !$familyHeadAction->qr_code_path)
                                <form method="POST" action="{{ route('admin.households.qr.generate-head', [$household, $familyHeadAction]) }}">
                                    @csrf
                                    <button type="button" class="btn-action" style="background:#7C3AED;color:#fff;"
                                        onclick="openModal('head-qr', this.closest('form'), 'Generate Family Head QR', 'Generate a personal QR code for <strong>{{ $familyHeadAction->full_name }}</strong>?', 'Generate QR', 'purple')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                                        </svg>
                                        Generate Head QR — {{ $familyHeadAction->full_name }}
                                    </button>
                                </form>
                            @endif
                            @if($familyHeadAction->qr_code_path)
                                <a href="{{ route('admin.households.qr.download-head', [$household, $familyHeadAction]) }}" class="btn-action" style="background:#F5F3FF;color:#6D28D9;border:1px solid #DDD6FE;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                    Download Head QR — {{ $familyHeadAction->full_name }}
                                </a>
                            @endif
                        @endforeach
                        @if(!$household->isApproved())
                            <hr class="actions-divider">
                            <form method="POST" action="{{ route('admin.households.destroy', $household) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-action-delete"
                                    onclick="openModal('delete', this.closest('form'), 'Delete Household', '⚠️ This will <strong>permanently delete</strong> the household record of <strong>{{ $household->household_head_name }}</strong> and all associated members. This cannot be undone.', 'Delete Permanently', 'danger')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                        <path d="M10 11v6"/><path d="M14 11v6"/>
                                    </svg>
                                    Delete Household
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Record Info --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="ca-dot"></div>
                        <div class="section-title">Record Information</div>
                    </div>
                    <div class="record-info-stack">
                        <div class="record-info-row">
                            <span class="record-info-label">Encoded By</span>
                            <span class="record-info-value">{{ $household->encoder->name }}</span>
                        </div>
                        <div class="record-info-row">
                            <span class="record-info-label">Approved By</span>
                            <span class="record-info-value">{{ $household->approver->name ?? '—' }}</span>
                        </div>
                        <div class="record-info-row">
                            <span class="record-info-label">Registered</span>
                            <span class="record-info-value">{{ $household->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="record-info-row">
                            <span class="record-info-label">Last Updated</span>
                            <span class="record-info-value">{{ $household->updated_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="record-info-row">
                            <span class="record-info-label">QR Scans</span>
                            <span class="record-info-value">
                                <span class="scan-count-inline {{ $scanCount > 0 ? 'has-scans' : 'no-scans' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                                        <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                                        <rect x="8" y="8" width="8" height="8" rx="1"/>
                                    </svg>
                                    {{ $scanCount }} time{{ $scanCount !== 1 ? 's' : '' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

            </div>{{-- /detail-side --}}

        </div>{{-- /detail-layout --}}

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



</div>{{-- /shell --}}

<!-- ── CUSTOM CONFIRM MODAL ── -->
<div class="modal-backdrop" id="confirmModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-icon" id="modalIcon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="modal-title" id="modalTitle">Confirm Action</div>
        </div>
        <div class="modal-body" id="modalBody">Are you sure?</div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="modal-btn" id="modalConfirmBtn" onclick="submitModalForm()">Confirm</button>
        </div>
    </div>
</div>

<script>
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now = new Date();
        const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeSidebar(); closeModal(); } });

    /* ── Custom Confirm Modal ── */
    let _modalForm = null;

    const iconMap = {
        confirm : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
        warning : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
        danger  : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>`,
        blue    : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="21" y2="14"/><line x1="14" y1="14" x2="14" y2="21"/></svg>`,
        purple  : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>`,
    };

    function openModal(type, form, title, body, confirmLabel, style) {
        _modalForm = form;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalBody').innerHTML = body;

        const icon = document.getElementById('modalIcon');
        icon.className = 'modal-icon ' + style;
        icon.innerHTML = iconMap[style] || iconMap.confirm;

        const btn = document.getElementById('modalConfirmBtn');
        btn.textContent = confirmLabel;
        btn.className = 'modal-btn modal-btn-' + style;

        document.getElementById('confirmModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('confirmModal').classList.remove('show');
        document.body.style.overflow = '';
        _modalForm = null;
    }

    function submitModalForm() {
        if (_modalForm) _modalForm.submit();
        closeModal();
    }

    // Close on backdrop click
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
</body>
</html>