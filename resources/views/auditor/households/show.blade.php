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
            --purple:     #5B3FA6;
            --purple-dark:#3D1F8A;
            --purple-pale:#F5F0FF;
            --purple-border:#D8CBF5;
            --sky:        #0EA5E9;
            --sky-dark:   #0369A1;
            --sky-pale:   #F0F9FF;
            --sky-border: #BAE6FD;
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

        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ─── READ-ONLY BADGE ─── */
                .readonly-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; background: #FFFBEB;
            border: 1px solid #FDE68A; border-radius: 3px;
            font-size: 11px; font-weight: 700; color: #92400E;
            text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0;
        }
        .readonly-badge svg { width: 12px; height: 12px; }


        header { grid-area: header; background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; align-items: center; padding: 0 28px; gap: 14px; z-index: 90; }
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
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--sky-pale); border: 1px solid var(--sky-border); border-radius: 4px; flex-shrink: 0; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--sky); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--sky-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: #0284C7; text-transform: uppercase; letter-spacing: 0.5px; }

        .readonly-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 3px; font-size: 11px; font-weight: 700; color: #92400E; text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; }
        .readonly-badge svg { width: 12px; height: 12px; }

        .sidebar-overlay { display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; opacity: 0; transition: opacity 0.25s; pointer-events: none; }
        .sidebar-overlay.active { display: block !important; pointer-events: auto; }

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

        .main-content { grid-area: main; background: var(--gray-50); overflow-y: auto; padding: 28px 32px; }

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .titlebar-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }
        .back-btn { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; padding: 8px 16px; border: 1px solid var(--gray-200); background: var(--white); border-radius: 4px; transition: background 0.15s; white-space: nowrap; }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }

        .household-hero { background: var(--white); border: 1px solid var(--gray-200); border-top: 4px solid var(--blue); padding: 24px 28px; display: flex; align-items: center; gap: 24px; margin-bottom: 20px; flex-wrap: wrap; }
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

        .hero-scan-counter { display: flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 3px; border: 1px solid; }
        .hero-scan-counter.has-scans { background: var(--green-pale); border-color: #BBF7D0; color: var(--green-dark); }
        .hero-scan-counter.no-scans { background: var(--gray-100); border-color: var(--gray-200); color: var(--gray-400); }
        .hero-scan-counter svg { width: 13px; height: 13px; flex-shrink: 0; }
        .hero-scan-counter-number { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; line-height: 1; }
        .hero-scan-counter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1.3; }

        .hero-badges { display: flex; align-items: center; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .badge svg { width: 10px; height: 10px; }
        .badge-approved { background: var(--green-pale); color: var(--green-dark); }
        .badge-pending  { background: var(--orange-pale); color: #92400E; }
        .badge-blue     { background: var(--blue-pale); color: var(--blue); }
        .badge-gray     { background: var(--gray-100); color: var(--gray-600); }

        .detail-layout { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }
        .detail-main { display: flex; flex-direction: column; gap: 20px; }
        .detail-side { display: flex; flex-direction: column; gap: 20px; }

        .section-card { background: var(--white); border: 1px solid var(--gray-200); }
        .section-header { padding: 12px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .section-body { padding: 20px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-item { padding: 12px 14px; background: var(--gray-50); border: 1px solid var(--gray-100); border-left: 3px solid var(--blue-light); }
        .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); margin-bottom: 4px; }
        .info-value { font-size: 13px; color: var(--gray-800); font-weight: 500; line-height: 1.4; }
        .info-value.mono { font-family: monospace; font-size: 13px; color: var(--blue); font-weight: 700; letter-spacing: 0.5px; }
        .info-value em { color: var(--gray-400); font-style: italic; font-weight: 400; }

        .address-block { padding: 14px; background: var(--gray-50); border: 1px solid var(--gray-100); border-left: 3px solid var(--blue-light); }
        .address-line1 { font-size: 14px; color: var(--gray-800); font-weight: 500; }
        .address-line2 { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        .sector-flags { display: flex; flex-wrap: wrap; gap: 8px; }
        .sector-flag { display: inline-flex; align-items: center; gap: 7px; padding: 7px 14px; background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; border-radius: 3px; font-size: 12px; font-weight: 600; }
        .sector-flag svg { width: 13px; height: 13px; }
        .sector-none { font-size: 12px; color: var(--gray-400); font-style: italic; }

        /* ─── TABLE (desktop) ─── */
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; position: relative; }
        .members-table { width: 100%; border-collapse: collapse; min-width: 560px; }
        .members-table thead th { padding: 10px 14px; background: var(--blue); color: var(--white); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; white-space: nowrap; }
        .members-table tbody tr { border-bottom: 1px solid var(--gray-100); transition: background 0.1s; }
        .members-table tbody tr:hover { background: var(--blue-pale); }
        .members-table tbody tr:last-child { border-bottom: none; }
        .members-table tbody td { padding: 11px 14px; font-size: 13px; color: var(--gray-800); vertical-align: middle; }
        .members-table tbody tr:nth-child(even) td { background: var(--gray-50); }
        .members-table tbody tr:nth-child(even):hover td { background: var(--blue-pale); }
        .member-name { font-weight: 600; color: var(--blue-dark); }
        .no-members { padding: 32px; text-align: center; color: var(--gray-400); font-style: italic; font-size: 13px; }

        /* ─── TABLE → CARD layout on small screens ─── */
        @media (max-width: 600px) {
            .table-wrapper { overflow-x: visible; }
            .members-table { min-width: 0; }
            .members-table thead { display: none; } /* hide column headers */
            .members-table tbody { display: flex; flex-direction: column; gap: 10px; padding: 12px; }
            .members-table tbody tr {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                border: 1px solid var(--gray-200);
                border-bottom: 1px solid var(--gray-200) !important;
                border-radius: 4px;
                padding: 12px;
                background: var(--white);
                box-shadow: 0 1px 3px rgba(0,0,0,0.04);
                position: relative;
            }
            .members-table tbody tr:nth-child(even) td { background: transparent; }
            .members-table tbody tr:hover { background: var(--blue-pale); }
            /* Each cell gets a label via data-label */
            .members-table tbody td {
                padding: 0;
                font-size: 12px;
                display: flex;
                flex-direction: column;
                gap: 3px;
            }
            .members-table tbody td::before {
                content: attr(data-label);
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                color: var(--gray-400);
            }
            /* Full-width cells */
            .members-table tbody td:first-child { grid-column: 1 / -1; }
            .members-table tbody td:last-child  { grid-column: 1 / -1; }
            .member-name { font-size: 13px; }
        }

        .readonly-notice { padding: 20px; background: #FFFBEB; border: 1px dashed #FDE68A; display: flex; flex-direction: column; align-items: center; gap: 10px; text-align: center; }
        .readonly-notice-icon { width: 40px; height: 40px; border-radius: 50%; background: #FEF3C7; display: flex; align-items: center; justify-content: center; }
        .readonly-notice-icon svg { width: 20px; height: 20px; color: #D97706; }
        .readonly-notice-title { font-size: 13px; font-weight: 700; color: #92400E; }
        .readonly-notice-sub { font-size: 11px; color: #B45309; line-height: 1.5; }

        .record-info-stack { display: flex; flex-direction: column; gap: 0; }
        .record-info-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; border-bottom: 1px solid var(--gray-100); gap: 12px; }
        .record-info-row:last-child { border-bottom: none; }
        .record-info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-400); flex-shrink: 0; }
        .record-info-value { font-size: 13px; color: var(--gray-800); font-weight: 500; text-align: right; }

        .scan-count-inline { display: inline-flex; align-items: center; gap: 5px; font-size: 13px; font-weight: 700; }
        .scan-count-inline.has-scans { color: var(--green-dark); }
        .scan-count-inline.no-scans  { color: var(--gray-400); font-weight: 500; }
        .scan-count-inline svg { width: 12px; height: 12px; }

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

        /* ─── HOUSING / UTILITIES ─── */
        .info-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .info-item.accent-green { border-left-color: var(--green); }
        .info-item.accent-orange { border-left-color: var(--orange); }
        .info-item.accent-purple { border-left-color: var(--purple); }
        .info-value.pill {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--blue-pale); color: var(--blue);
            border: 1px solid #C3D8F5; border-radius: 10px;
            font-size: 11px; font-weight: 700; padding: 3px 10px;
            text-transform: uppercase; letter-spacing: 0.4px;
        }
        .info-value.pill.green { background: var(--green-pale); color: var(--green-dark); border-color: #BBF7D0; }
        .info-value.pill.orange { background: var(--orange-pale); color: var(--orange); border-color: #FDE68A; }
        .info-value.pill.purple { background: var(--purple-pale); color: var(--purple); border-color: var(--purple-border); }

        /* ─── COORDINATES ─── */
        .coords-block { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: var(--gray-50); border: 1px solid var(--gray-100); border-left: 3px solid var(--blue-light); }
        .coords-icon { width: 36px; height: 36px; border-radius: 4px; background: var(--blue-pale); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .coords-icon svg { width: 18px; height: 18px; color: var(--blue); }
        .coords-values { display: flex; gap: 20px; flex-wrap: wrap; }
        .coords-pair { display: flex; flex-direction: column; gap: 2px; }
        .coords-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }
        .coords-val { font-family: monospace; font-size: 13px; font-weight: 700; color: var(--blue); }
        .coords-dms { font-size: 11px; color: var(--gray-600); margin-top: 1px; }

        /* ─── RISK PROFILE ─── */
        .risk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .risk-item { padding: 12px 14px; background: var(--gray-50); border: 1px solid var(--gray-100); border-left: 3px solid var(--blue-light); display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .risk-item-label { font-size: 11px; font-weight: 600; color: var(--gray-600); }
        .risk-pill { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap; }
        .risk-pill.yes { background: var(--green-pale); color: var(--green-dark); border: 1px solid #BBF7D0; }
        .risk-pill.no  { background: var(--gray-100); color: var(--gray-400); border: 1px solid var(--gray-200); }
        .risk-pill svg { width: 9px; height: 9px; }
        .risk-stat { display: flex; flex-direction: column; align-items: flex-end; gap: 1px; }
        .risk-stat-num { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1; }
        .risk-stat-sub { font-size: 9px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; }
        .ews-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .ews-tag { background: var(--sky-pale); color: var(--sky-dark); border: 1px solid var(--sky-border); border-radius: 3px; font-size: 10px; font-weight: 700; padding: 3px 9px; text-transform: uppercase; letter-spacing: 0.4px; }
        .remarks-block { padding: 10px 14px; background: var(--gray-50); border: 1px solid var(--gray-100); border-left: 3px solid var(--orange); font-size: 12px; color: var(--gray-800); line-height: 1.6; }
        .literacy-bar-wrap { display: flex; align-items: center; gap: 8px; flex: 1; }
        .literacy-bar-bg { flex: 1; height: 6px; background: var(--gray-200); border-radius: 3px; overflow: hidden; }
        .literacy-bar-fill { height: 100%; background: var(--blue); border-radius: 3px; transition: width 0.4s; }
        .literacy-pct { font-size: 12px; font-weight: 700; color: var(--blue); min-width: 36px; text-align: right; }

        /* ─── RESPONSIVE BREAKPOINTS ─── */

        /* Tablet landscape: collapse sidebar into drawer, stack detail columns */
        @media (max-width: 1100px) {
            .detail-layout { grid-template-columns: 1fr; }
            .detail-side { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        }

        /* Tablet portrait: off-canvas sidebar */
        @media (max-width: 900px) {
            .shell {
                grid-template-rows: 36px auto 1fr 48px;
                grid-template-columns: 1fr;
                grid-template-areas: "topbar" "header" "main" "footer";
                height: 100vh; overflow: hidden;
            }
            .sidebar {
                grid-area: unset; position: fixed; top: 0; left: 0; bottom: 0;
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

        /* Small tablet / large phone */
        @media (max-width: 720px) {
            .info-grid { grid-template-columns: 1fr; }
            .info-grid-3 { grid-template-columns: 1fr 1fr; }
            .risk-grid { grid-template-columns: 1fr; }
            /* Hero: stack avatar row and right-side badges */
            .household-hero { flex-direction: column; align-items: flex-start; gap: 12px; }
            .hero-right { flex-direction: row; align-items: center; gap: 12px; width: 100%; }
            .hero-info { width: 100%; }
            .hero-name { white-space: normal; font-size: 18px; }
        }

        /* Phone landscape / large phone portrait */
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
            .household-hero { padding: 16px; gap: 12px; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            .readonly-badge { display: none; }
            /* Side panel: actions and record info go full width */
            .detail-side { grid-template-columns: 1fr; }
            /* Literacy bar: stack label above bar */
            .info-item[style*="display:flex"] { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
            .literacy-bar-wrap { width: 100%; }
        }

        /* Small phones */
        @media (max-width: 400px) {
            .info-grid-3 { grid-template-columns: 1fr; }
            .hero-right { flex-direction: column; align-items: flex-start; }
            .record-info-row { flex-direction: column; align-items: flex-start; gap: 4px; }
            .record-info-value { text-align: left; }
            .coords-values { flex-direction: column; gap: 8px; }
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
        <span class="readonly-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Read-Only Access
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
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <div class="nav-section-label">Auditor Menu</div>
        <a href="{{ route('auditor.households.index') }}" class="nav-item active" onclick="closeSidebar()">
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
                <div class="page-breadcrumb">Auditor / <span>Households</span> / <span>Details</span></div>
                <div class="page-h1">Household Details</div>
                <div class="page-sub">RBI-aligned household profile — read-only view</div>
            </div>
            <div class="titlebar-actions">
                <a href="{{ route('auditor.households.index') }}" class="back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Back to List
                </a>
            </div>
        </div>

        @php
            $scanCount    = $household->distributionLogs->count();
            $headMember   = $household->members->firstWhere('is_family_head', 1);
            $riskProfile  = $household->riskProfile;

            // Label maps
            $housingTypeLabels = [
                'single_detached' => 'Single Detached', 'duplex' => 'Duplex',
                'townhouse' => 'Townhouse', 'apartment' => 'Apartment / Condo',
                'bungalow' => 'Bungalow', 'villa' => 'Villa',
                'makeshift' => 'Makeshift / Informal', 'mixed' => 'Mixed Use',
            ];
            $materialLabels = [
                'concrete' => 'Concrete / Masonry', 'wood' => 'Wood',
                'mixed' => 'Mixed (Concrete + Wood)', 'bamboo' => 'Bamboo / Nipa',
                'light' => 'Light Materials', 'salvaged' => 'Salvaged / Scrap',
            ];
            $ownershipLabels = [
                'owned' => 'Owned', 'rented' => 'Rented',
                'shared' => 'Shared / Rent-Free', 'mortgaged' => 'Mortgaged',
                'informal' => 'Informal Settler',
            ];
            $electricityLabels = [
                'electric_company' => 'Electric Company (Meralco/CLPC)',
                'solar' => 'Solar Panel', 'generator' => 'Generator',
                'none' => 'No Electricity', 'shared' => 'Shared Connection',
            ];
            $waterLabels = [
                'maynilad' => 'Maynilad / Waterworks', 'water_project' => 'Barangay Water Project',
                'shallow_well' => 'Shallow Well', 'deep_well' => 'Deep Well',
                'spring' => 'Spring / River', 'bottled' => 'Bottled Water',
                'rain' => 'Rain Collection', 'none' => 'No Water Source',
            ];
            $toiletLabels = [
                'safely_managed' => 'Safely Managed (Flush / Septic)',
                'pit_latrine' => 'Pit Latrine', 'open_defecation' => 'Open Defecation',
                'shared' => 'Shared Facility', 'none' => 'None',
            ];
            $wasteLabels = [
                'garbage' => 'Garbage Collection', 'composting' => 'Composting',
                'burning' => 'Burning', 'sanitary_landfill' => 'Sanitary Landfill',
                'open_dump' => 'Open Dumping', 'none' => 'No Disposal System',
            ];
            $ewsSourceLabels = ['tv' => 'TV', 'radio' => 'Radio', 'brgy' => 'Barangay Announcement', 'other' => 'Other'];

            $housingTypeLabel   = $housingTypeLabels[$household->housing_type]     ?? ucfirst(str_replace('_', ' ', $household->housing_type ?? ''));
            $materialLabel      = $materialLabels[$household->housing_material]    ?? ucfirst(str_replace('_', ' ', $household->housing_material ?? ''));
            $ownershipLabel     = $ownershipLabels[$household->ownership_type]     ?? ucfirst(str_replace('_', ' ', $household->ownership_type ?? ''));
            $electricityLabel   = $electricityLabels[$household->electricity_source] ?? ucfirst(str_replace('_', ' ', $household->electricity_source ?? ''));
            $waterLabel         = $waterLabels[$household->water_source]           ?? ucfirst(str_replace('_', ' ', $household->water_source ?? ''));
            $toiletLabel        = $toiletLabels[$household->toilet_access]         ?? ucfirst(str_replace('_', ' ', $household->toilet_access ?? ''));
            $wasteLabel         = $wasteLabels[$household->waste_disposal]         ?? ucfirst(str_replace('_', ' ', $household->waste_disposal ?? ''));

            $ewsSources = $riskProfile && $riskProfile->ews_sources
                ? array_map('trim', explode(',', $riskProfile->ews_sources))
                : [];
        @endphp

        <div class="household-hero">
            <div class="hero-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
                </svg>
            </div>
            <div class="hero-info">
                <div class="hero-name">{{ $household->household_head_name }}</div>
                <div class="hero-meta">
                    <span>{{ $headMember?->sex ?? '—' }}</span>
                    <span class="hero-meta-sep">|</span>
                    <span>{{ $headMember?->age !== null ? $headMember->age . ' years old' : '—' }}</span>
                    <span class="hero-meta-sep">|</span>
                    <span>{{ $headMember?->civil_status ?? '—' }}</span>
                    <span class="hero-meta-sep">|</span>
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

        <div class="detail-layout">
            <div class="detail-main">
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Household Head Information</div></div>
                    <div class="section-body">
                        <div class="info-grid">
                            <div class="info-item"><div class="info-label">Full Name</div><div class="info-value">{{ $household->household_head_name }}</div></div>
                            <div class="info-item"><div class="info-label">Sex</div><div class="info-value">{{ $headMember?->sex ?? '—' }}</div></div>
                            <div class="info-item"><div class="info-label">Birthday</div><div class="info-value">{{ $headMember?->birthday ? \Carbon\Carbon::parse($headMember->birthday)->format('F d, Y') : '—' }}</div></div>
                            <div class="info-item"><div class="info-label">Age</div><div class="info-value">{{ $headMember?->age !== null ? $headMember->age . ' years old' : '—' }}</div></div>
                            <div class="info-item"><div class="info-label">Civil Status</div><div class="info-value">{{ $headMember?->civil_status ?? '—' }}</div></div>
                            <div class="info-item"><div class="info-label">Contact Number</div><div class="info-value">@if($household->contact_number){{ $household->contact_number }}@else<em>N/A</em>@endif</div></div>
                            <div class="info-item"><div class="info-label">Email Address</div><div class="info-value">@if($household->email){{ $household->email }}@else<em>—</em>@endif</div></div>
                            <div class="info-item"><div class="info-label">Valid ID</div><div class="info-value">@if($household->valid_id_type){{ $household->valid_id_type }}@else<em>—</em>@endif</div></div>
                            <div class="info-item"><div class="info-label">ID Number</div><div class="info-value">@if($household->valid_id_num)<span class="mono">{{ $household->valid_id_num }}</span>@else<em>—</em>@endif</div></div>
                            <div class="info-item"><div class="info-label">Educational Attainment</div><div class="info-value">{{ $headMember?->educational_attainment ?? '—' }}</div></div>
                            <div class="info-item"><div class="info-label">Occupation</div><div class="info-value">{{ $headMember?->occupation ?? $headMember?->detail?->job_title ?? '—' }}</div></div>
                            <div class="info-item"><div class="info-label">Total Household Members</div><div class="info-value">{{ $household->total_members }} person(s)</div></div>
                        </div>
                    </div>
                </div>
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Address &amp; Location</div></div>
                    <div class="section-body" style="display:flex;flex-direction:column;gap:12px;">
                        <div class="address-block">
                            <div class="address-line1">
                                @if($household->street_purok){{ $household->street_purok }}@else<em style="color:var(--gray-400);font-style:italic;">No street/purok recorded</em>@endif
                            </div>
                            <div class="address-line2">
                                @if($household->barangay_area)Area {{ $household->barangay_area }} — @endif
                                Brgy. {{ $household->barangay }}, {{ $household->municipality }}, {{ $household->province }}
                            </div>
                            @if($household->location)
                                <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:3px;"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $household->location }}
                                </div>
                            @endif
                        </div>

                        @if($household->latitude && $household->longitude)
                            <div class="coords-block">
                                <div class="coords-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="3"/><line x1="12" y1="2" x2="12" y2="6"/>
                                        <line x1="12" y1="18" x2="12" y2="22"/><line x1="2" y1="12" x2="6" y2="12"/>
                                        <line x1="18" y1="12" x2="22" y2="12"/>
                                    </svg>
                                </div>
                                <div class="coords-values">
                                    <div class="coords-pair">
                                        <div class="coords-label">Latitude</div>
                                        <div class="coords-val">{{ $household->latitude }}</div>
                                    </div>
                                    <div class="coords-pair">
                                        <div class="coords-label">Longitude</div>
                                        <div class="coords-val">{{ $household->longitude }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="info-grid">
                            <div class="info-item"><div class="info-label">Municipality</div><div class="info-value">{{ $household->municipality }}</div></div>
                            <div class="info-item"><div class="info-label">Province</div><div class="info-value">{{ $household->province }}</div></div>
                            <div class="info-item"><div class="info-label">Barangay</div><div class="info-value">{{ $household->barangay }}</div></div>
                            <div class="info-item"><div class="info-label">Barangay Area</div><div class="info-value">@if($household->barangay_area){{ $household->barangay_area }}@else<em>Not specified</em>@endif</div></div>
                        </div>
                    </div>
                </div>
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">DSWD / Listahanan Sector Flags</div></div>
                    <div class="section-body">
                        @php $hasAnyFlag = $household->is_4ps_beneficiary || $household->is_pwd || $household->is_senior || $household->is_solo_parent; @endphp
                        @if($hasAnyFlag)
                            <div class="sector-flags">
                                @if($household->is_4ps_beneficiary)<span class="sector-flag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>4Ps Beneficiary</span>@endif
                                @if($household->is_pwd)<span class="sector-flag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Has PWD Member</span>@endif
                                @if($household->is_senior)<span class="sector-flag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Has Senior Citizen</span>@endif
                                @if($household->is_solo_parent)<span class="sector-flag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Has Solo Parent</span>@endif
                            </div>
                        @else
                            <span class="sector-none">No special sector flags recorded.</span>
                        @endif
                    </div>
                </div>
                {{-- ── Section 1B: Housing Unit ── --}}
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Housing Unit</div></div>
                    <div class="section-body">
                        <div class="info-grid-3">
                            <div class="info-item">
                                <div class="info-label">Year Built</div>
                                <div class="info-value">@if($household->year_built){{ $household->year_built }}@else<em>Not recorded</em>@endif</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Housing Type</div>
                                <div class="info-value">@if($household->housing_type)<span class="pill">{{ $housingTypeLabel }}</span>@else<em>—</em>@endif</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Construction Material</div>
                                <div class="info-value">@if($household->housing_material)<span class="pill">{{ $materialLabel }}</span>@else<em>—</em>@endif</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Ownership Type</div>
                                <div class="info-value">@if($household->ownership_type)<span class="pill orange">{{ $ownershipLabel }}</span>@else<em>—</em>@endif</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Electricity Source</div>
                                <div class="info-value">@if($household->electricity_source)<span class="pill green">{{ $electricityLabel }}</span>@else<em>—</em>@endif</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Section 1C: Utilities & Sanitation ── --}}
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Utilities &amp; Sanitation</div></div>
                    <div class="section-body">
                        <div class="info-grid-3">
                            <div class="info-item accent-green">
                                <div class="info-label">Water Source</div>
                                <div class="info-value">@if($household->water_source)<span class="pill green">{{ $waterLabel }}</span>@else<em>—</em>@endif</div>
                            </div>
                            <div class="info-item accent-purple">
                                <div class="info-label">Toilet / Sanitation Access</div>
                                <div class="info-value">@if($household->toilet_access)<span class="pill purple">{{ $toiletLabel }}</span>@else<em>—</em>@endif</div>
                            </div>
                            <div class="info-item accent-orange">
                                <div class="info-label">Waste Disposal</div>
                                <div class="info-value">@if($household->waste_disposal)<span class="pill orange">{{ $wasteLabel }}</span>@else<em>—</em>@endif</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Risk Profile ── --}}
                @if($riskProfile)
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Household Risk Profile</div></div>
                    <div class="section-body" style="display:flex;flex-direction:column;gap:14px;">

                        <div class="risk-grid">
                            <div class="risk-item">
                                <span class="risk-item-label">Early Warning System Access</span>
                                <span class="risk-pill {{ $riskProfile->early_warning ? 'yes' : 'no' }}">
                                    @if($riskProfile->early_warning)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Yes
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> No
                                    @endif
                                </span>
                            </div>
                            <div class="risk-item">
                                <span class="risk-item-label">Hazard Awareness</span>
                                <span class="risk-pill {{ $riskProfile->hazard_awareness ? 'yes' : 'no' }}">
                                    @if($riskProfile->hazard_awareness)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Yes
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> No
                                    @endif
                                </span>
                            </div>
                            <div class="risk-item">
                                <span class="risk-item-label">Financial Assistance Received</span>
                                <span class="risk-pill {{ $riskProfile->financial_assistance ? 'yes' : 'no' }}">
                                    @if($riskProfile->financial_assistance)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Yes
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> No
                                    @endif
                                </span>
                            </div>
                            <div class="risk-item">
                                <span class="risk-item-label">Access to Disaster Info</span>
                                <span class="risk-pill {{ $riskProfile->access_info ? 'yes' : 'no' }}">
                                    @if($riskProfile->access_info)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Yes
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> No
                                    @endif
                                </span>
                            </div>
                            <div class="risk-item">
                                <span class="risk-item-label">Willing to Relocate</span>
                                <span class="risk-pill {{ $riskProfile->relocate_willingness ? 'yes' : 'no' }}">
                                    @if($riskProfile->relocate_willingness)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Yes
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> No
                                    @endif
                                </span>
                            </div>
                            @if($riskProfile->income_average !== null)
                                <div class="risk-item">
                                    <span class="risk-item-label">Average Monthly Income</span>
                                    <div class="risk-stat">
                                        <span class="risk-stat-num">₱{{ number_format($riskProfile->income_average, 0) }}</span>
                                        <span class="risk-stat-sub">per month</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($riskProfile->literacy_rate !== null)
                            <div class="info-item" style="display:flex;align-items:center;gap:12px;padding:12px 14px;">
                                <div style="min-width:120px;">
                                    <div class="info-label">Literacy Rate</div>
                                </div>
                                <div class="literacy-bar-wrap">
                                    <div class="literacy-bar-bg">
                                        <div class="literacy-bar-fill" style="width:{{ $riskProfile->literacy_rate }}%"></div>
                                    </div>
                                    <span class="literacy-pct">{{ $riskProfile->literacy_rate }}%</span>
                                </div>
                            </div>
                        @endif

                        @if(!empty($ewsSources))
                            <div>
                                <div class="info-label" style="margin-bottom:8px;">Early Warning Sources</div>
                                <div class="ews-tags">
                                    @foreach($ewsSources as $src)
                                        <span class="ews-tag">{{ $ewsSourceLabels[$src] ?? ucfirst($src) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($riskProfile->remarks)
                            <div>
                                <div class="info-label" style="margin-bottom:6px;">Remarks</div>
                                <div class="remarks-block">{{ $riskProfile->remarks }}</div>
                            </div>
                        @endif

                    </div>
                </div>
                @endif

                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Family Members ({{ $household->members->count() }})</div></div>
                    @if($household->members->count() > 0)
                        <div class="table-wrapper">
                            <table class="members-table">
                                <thead><tr><th>Full Name</th><th>Relationship</th><th>Sex / Age</th><th>Birthday</th><th>Occupation</th><th>Flags</th></tr></thead>
                                <tbody>
                                    @foreach($household->members as $member)
                                        <tr>
                                            <td data-label="Full Name"><span class="member-name">{{ $member->full_name }}</span></td>
                                            <td data-label="Relationship">{{ $member->relationship }}</td>
                                            <td data-label="Sex / Age">{{ $member->sex }}, {{ $member->age }} y/o</td>
                                            <td data-label="Birthday">{{ $member->birthday ? $member->birthday->format('M d, Y') : 'N/A' }}</td>
                                            <td data-label="Occupation">
                                                @php
                                                    $emp   = $member->detail?->employment_status;
                                                    $job   = $member->detail?->job_title;
                                                    $other = $member->detail?->employment_other;
                                                    $label = $job ?: ($other ?: $emp);
                                                @endphp
                                                {{ $label ?? '—' }}
                                            </td>
                                            <td data-label="Flags">
                                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                                    @if($member->is_pwd)
                                                        <span class="badge badge-blue">PWD</span>
                                                    @endif
                                                    @if($member->is_student)
                                                        <span class="badge badge-gray">Student</span>
                                                    @endif
                                                    @if($member->isSenior())
                                                        <span class="badge badge-blue">Senior</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="no-members">No additional family members registered.</div>
                    @endif
                </div>
            </div>

            <div class="detail-side">
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Actions</div></div>
                    <div class="readonly-notice">
                        <div class="readonly-notice-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </div>
                        <div class="readonly-notice-title">Read-Only Account</div>
                        <div class="readonly-notice-sub">Auditor accounts cannot approve, modify, or delete household records.</div>
                    </div>
                </div>
                <div class="section-card">
                    <div class="section-header"><div class="ca-dot"></div><div class="section-title">Record Information</div></div>
                    <div class="record-info-stack">
                        <div class="record-info-row"><span class="record-info-label">Encoded By</span><span class="record-info-value">{{ $household->encoder->name }}</span></div>
                        <div class="record-info-row"><span class="record-info-label">Approved By</span><span class="record-info-value">{{ $household->approver->name ?? '&mdash;' }}</span></div>
                        <div class="record-info-row"><span class="record-info-label">Registered</span><span class="record-info-value">{{ $household->created_at->format('M d, Y g:i A') }}</span></div>
                        <div class="record-info-row"><span class="record-info-label">Last Updated</span><span class="record-info-value">{{ $household->updated_at->format('M d, Y g:i A') }}</span></div>
                        <div class="record-info-row">
                            <span class="record-info-label">QR Scans</span>
                            <span class="record-info-value">
                                <span class="scan-count-inline {{ $scanCount > 0 ? 'has-scans' : 'no-scans' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/><polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/><rect x="8" y="8" width="8" height="8" rx="1"/></svg>
                                    {{ $scanCount }} time{{ $scanCount !== 1 ? 's' : '' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

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
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>
</body>
</html>