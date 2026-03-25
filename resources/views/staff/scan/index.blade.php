<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — QR Code Scanner</title>
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
            --green:      #16A34A;
            --green-pale: #DCFCE7;
            --green-dark: #15803D;
            --red:        #C0392B;
            --red-pale:   #FEF2F2;
            --orange:     #D97706;
            --orange-pale:#FFFBEB;
            --white:      #FFFFFF;
            --gray-50:    #F7F8FA;
            --gray-100:   #F0F2F5;
            --gray-200:   #DEE2E8;
            --gray-400:   #9AA3B0;
            --gray-600:   #5A6372;
            --gray-800:   #2C3340;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            font-family: 'Open Sans', sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            font-size: 14px;
        }

        .page-wrapper { min-height: 100%; display: flex; flex-direction: column; }

        /* ─── TOP UTILITY BAR ─── */
        .topbar { background: var(--blue-dark); height: 36px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; position: sticky; top: 0; z-index: 200; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date-inline { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-indicator::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 5px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        /* ─── HEADER ─── */
        .page-header { background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 6px rgba(0,0,0,0.08); display: flex; align-items: center; padding: 0 28px; gap: 14px; height: 76px; position: sticky; top: 36px; z-index: 190; }
        .header-logos { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .header-logos img { height: 54px; width: 54px; object-fit: contain; }
        .logo-divider { width: 1px; height: 44px; background: var(--gray-200); }
        .header-text { margin-left: 4px; }
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
        .header-spacer { flex: 1; }
        .header-right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .back-btn { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; padding: 8px 14px; border: 1px solid var(--gray-200); background: var(--gray-50); border-radius: 4px; transition: background 0.15s; white-space: nowrap; }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }
        .header-user-badge { display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: var(--green-pale); border: 1px solid #BBF7D0; border-radius: 4px; flex-shrink: 0; }
        .user-avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--green); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 12px; flex-shrink: 0; }
        .user-name { font-size: 12px; font-weight: 600; color: var(--green-dark); line-height: 1.2; }
        .user-role { font-size: 10px; color: var(--green); text-transform: uppercase; letter-spacing: 0.5px; }

        /* ─── PAGE BODY ─── */
        .page-body { flex: 1; max-width: 720px; margin: 0 auto; padding: 28px 20px 40px; width: 100%; }

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        /* ─── NO EVENTS ALERT ─── */
        .alert-no-event { background: var(--orange-pale); border: 1px solid #FDE68A; border-left: 4px solid var(--orange); padding: 18px 20px; display: flex; align-items: flex-start; gap: 12px; margin-bottom: 20px; }
        .alert-no-event svg { width: 18px; height: 18px; color: var(--orange); flex-shrink: 0; margin-top: 1px; }
        .alert-no-event-title { font-size: 13px; font-weight: 700; color: #92400E; margin-bottom: 3px; }
        .alert-no-event-text { font-size: 12px; color: #78350F; }

        /* ─── SECTION CARD ─── */
        .section-card { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 16px; }
        .section-card-header { padding: 13px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .section-card-header.green-top { border-top: 3px solid var(--green); }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .ca-dot-green { width: 8px; height: 8px; border-radius: 50%; background: var(--green); border: 2px solid var(--green-dark); flex-shrink: 0; }
        .section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .section-card-body { padding: 20px; }

        /* ─── EVENT SELECTOR ─── */
        .field-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); margin-bottom: 7px; }
        #event_id { width: 100%; padding: 11px 13px; font-size: 13.5px; font-family: 'Open Sans', sans-serif; color: var(--gray-800); background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 3px; outline: none; transition: border-color 0.15s; }
        #event_id:focus { border-color: var(--blue); background: var(--white); }

        /* ─── STATS ROW ─── */
        .stats-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
        .stat-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 18px 20px; text-align: center; }
        .stat-card.green-top { border-top-color: var(--green); }
        .stat-card.red-top   { border-top-color: var(--red); }
        .stat-number { font-family: 'PT Serif', serif; font-size: 36px; font-weight: 700; color: var(--blue-dark); line-height: 1; margin-bottom: 6px; }
        .stat-card.green-top .stat-number { color: var(--green); }
        .stat-card.red-top   .stat-number { color: var(--red); }
        .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }

        /* ─── loading shimmer for counters ─── */
        .stat-loading { display: inline-block; width: 48px; height: 36px; background: linear-gradient(90deg, var(--gray-100) 25%, var(--gray-200) 50%, var(--gray-100) 75%); background-size: 200% 100%; animation: shimmer 1.2s infinite; border-radius: 4px; vertical-align: middle; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

        /* ─── SCANNER ─── */
        #scanner-container { display: none; }
        .scanner-label { text-align: center; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--green); margin-bottom: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .scanner-label::before, .scanner-label::after { content: ''; flex: 1; height: 1px; background: var(--gray-200); }
        #reader { width: 100%; border-radius: 4px; overflow: hidden; border: 2px solid var(--gray-200); }
        .scanner-hint { text-align: center; font-size: 12px; color: var(--gray-400); margin-top: 10px; }

        /* ─── RESULT CARD ─── */
        #result-card { display: none; margin-bottom: 16px; }
        .result-inner { background: var(--white); border: 1px solid var(--gray-200); }
        .result-inner.success    { border-top: 4px solid var(--green); }
        .result-inner.duplicate  { border-top: 4px solid var(--orange); }
        .result-inner.error      { border-top: 4px solid var(--red); }
        .result-inner.confirmed  { border-top: 4px solid var(--green); }
        .result-inner.barangay   { border-top: 4px solid var(--red); }
        .result-header { padding: 14px 20px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; gap: 10px; }
        .result-header.success    { background: var(--green-pale); }
        .result-header.duplicate  { background: var(--orange-pale); }
        .result-header.error      { background: var(--red-pale); }
        .result-header.confirmed  { background: var(--green-pale); }
        .result-header.barangay   { background: var(--red-pale); }
        .result-status-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .result-status-icon svg { width: 15px; height: 15px; }
        .result-header.success   .result-status-icon { background: var(--green);  color: var(--white); }
        .result-header.duplicate .result-status-icon { background: var(--orange); color: var(--white); }
        .result-header.error     .result-status-icon { background: var(--red);    color: var(--white); }
        .result-header.confirmed .result-status-icon { background: var(--green);  color: var(--white); }
        .result-header.barangay  .result-status-icon { background: var(--red);    color: var(--white); }
        .result-status-text { font-size: 14px; font-weight: 700; }
        .result-header.success   .result-status-text { color: var(--green-dark); }
        .result-header.duplicate .result-status-text { color: #92400E; }
        .result-header.error     .result-status-text { color: var(--red); }
        .result-header.confirmed .result-status-text { color: var(--green-dark); }
        .result-header.barangay  .result-status-text { color: var(--red); }
        .result-body { padding: 20px; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .info-table tr { border-bottom: 1px solid var(--gray-100); }
        .info-table tr:last-child { border-bottom: none; }
        .info-table td { padding: 9px 10px; font-size: 13px; vertical-align: middle; }
        .info-table td:first-child { font-weight: 600; color: var(--gray-600); width: 38%; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; }
        .info-table td:last-child { color: var(--gray-800); }
        .info-table tr:nth-child(odd) td { background: var(--gray-50); }

        .badge { display: inline-block; padding: 3px 9px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-right: 4px; }
        .badge-4ps    { background: var(--blue-pale);  color: var(--blue); }
        .badge-pwd    { background: #F3E8FF; color: #6B21A8; }
        .badge-senior { background: var(--orange-pale); color: #92400E; }

        .btn-row { display: flex; gap: 10px; }
        .btn { flex: 1; padding: 13px 16px; border: none; border-radius: 4px; font-family: 'Open Sans', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s, transform 0.1s; text-decoration: none; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-confirm   { background: var(--green); color: var(--white); }
        .btn-confirm:hover:not(:disabled) { background: var(--green-dark); transform: translateY(-1px); }
        .btn-secondary { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
        .btn-secondary:hover { background: var(--gray-200); }
        .btn svg { width: 15px; height: 15px; }

        .btn-back-full { width: 100%; padding: 12px; background: var(--blue); color: var(--white); border: none; border-radius: 4px; font-family: 'Open Sans', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; margin-top: 4px; }
        .btn-back-full:hover { background: var(--blue-dark); }
        .btn-back-full svg { width: 14px; height: 14px; }

        .confirmed-body { padding: 28px 20px; text-align: center; }
        .confirmed-check { width: 56px; height: 56px; border-radius: 50%; background: var(--green); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .confirmed-check svg { width: 28px; height: 28px; color: var(--white); }
        .confirmed-title { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--green-dark); margin-bottom: 6px; }
        .confirmed-name  { font-size: 15px; font-weight: 600; color: var(--gray-800); margin-bottom: 4px; }
        .confirmed-time  { font-size: 12px; color: var(--gray-400); margin-bottom: 24px; }

        /* ─── PHOTO CAPTURE ─── */
        .photo-step { margin: 16px 0 0; border: 2px dashed var(--gray-200); border-radius: 6px; overflow: hidden; background: var(--gray-50); }
        .photo-step-header { padding: 10px 16px; background: #FFF7ED; border-bottom: 1px solid #FDE68A; display: flex; align-items: center; gap: 8px; }
        .photo-step-header svg { width: 16px; height: 16px; color: var(--orange); flex-shrink: 0; }
        .photo-step-title { font-size: 12px; font-weight: 700; color: #92400E; text-transform: uppercase; letter-spacing: 0.5px; }
        .photo-step-body { padding: 16px; }
        .photo-step.captured { border-color: var(--green); border-style: solid; }
        .photo-step.captured .photo-step-header { background: var(--green-pale); border-bottom-color: #BBF7D0; }
        .photo-step.captured .photo-step-header svg { color: var(--green); }
        .photo-step.captured .photo-step-title { color: var(--green-dark); }
        #photo-video { width: 100%; max-height: 260px; object-fit: cover; border-radius: 4px; display: block; background: #000; }
        #photo-canvas { display: none; }
        #photo-preview-wrap { display: none; text-align: center; }
        #photo-preview { width: 100%; max-height: 260px; object-fit: cover; border-radius: 4px; border: 2px solid var(--green); display: block; }
        .photo-btn-row { display: flex; gap: 8px; margin-top: 10px; }
        .btn-capture { flex: 1; padding: 11px 12px; background: var(--orange); color: var(--white); border: none; border-radius: 4px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px; transition: background 0.15s; }
        .btn-capture:hover { background: #B45309; }
        .btn-retake { flex: 1; padding: 11px 12px; background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 4px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px; transition: background 0.15s; }
        .btn-retake:hover { background: var(--gray-200); }
        .btn-capture svg, .btn-retake svg { width: 14px; height: 14px; }
        .photo-instruction { font-size: 12px; color: var(--gray-600); text-align: center; margin-top: 8px; line-height: 1.5; }
        .photo-required-note { font-size: 11px; color: var(--orange); font-weight: 600; text-align: center; margin-top: 4px; }

        /* ─── FOOTER ─── */
        .page-footer { background: var(--blue-dark); border-top: 3px solid var(--yellow); height: 48px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; gap: 8px; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .fb-link { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.15s; white-space: nowrap; }
        .fb-link:hover { color: var(--yellow); }
        .fb-link svg { width: 13px; height: 13px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        @media (max-width: 900px) {
            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }
            .page-header { padding: 0 16px; gap: 10px; height: auto; min-height: 68px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .header-user-badge { padding: 6px 10px; gap: 8px; }
            .user-name { font-size: 11px; }
            .user-role { display: none; }
            .page-body { padding: 20px 16px 32px; }
        }
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline { display: none; }
            .status-indicator { display: none; }
            .page-header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider { display: none; }
            .header-logos img:last-child { display: none; }
            .header-org { display: none; }
            .header-title { font-size: 13px; line-height: 1.3; }
            .back-btn span { display: none; }
            .back-btn { padding: 7px 10px; }
            .header-user-badge { padding: 5px 8px; }
            .user-avatar { width: 26px; height: 26px; font-size: 11px; }
            .user-name { display: none; }
            .page-body { padding: 16px 12px 28px; }
            .page-h1 { font-size: 18px; }
            .stats-row { gap: 10px; }
            .stat-number { font-size: 28px; }
            .stat-card { padding: 14px 12px; }
            .btn-row { flex-direction: column; }
            .btn { flex: none; width: 100%; }
            .page-footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }
        @media (max-width: 380px) {
            .stats-row { grid-template-columns: 1fr; }
        }
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
    </style>
</head>
<body>
<div class="page-wrapper">

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
    <div class="page-header">
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
        <div class="header-right">
            <a href="{{ route('staff.dashboard') }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Back to Dashboard</span>
            </a>
            <div class="header-user-badge">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Distribution Staff</div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE BODY -->
    <div class="page-body">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">Home / Staff / <span>QR Code Scanner</span></div>
                <div class="page-h1">QR Code Scanner</div>
                <div class="page-sub" id="page-sub-text">Scan QR cards to validate and record ayuda distribution</div>
            </div>
        </div>

        @if($events->isEmpty())

            <div class="alert-no-event">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <div>
                    <div class="alert-no-event-title">No Ongoing Distribution Events</div>
                    <div class="alert-no-event-text">There are currently no active events. Please wait for the Administrator to activate a distribution event before scanning.</div>
                </div>
            </div>
            <a href="{{ route('staff.dashboard') }}" class="btn-back-full">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Return to Dashboard
            </a>

        @else

            <div class="stats-row">
                <div class="stat-card green-top">
                    <div class="stat-number" id="scan-count">0</div>
                    <div class="stat-label">Confirmed Today</div>
                </div>
                <div class="stat-card red-top">
                    <div class="stat-number" id="duplicate-count">0</div>
                    <div class="stat-label">Duplicates Blocked</div>
                </div>
            </div>

            {{-- EVENT SELECTOR --}}
            <div class="section-card">
                <div class="section-card-header">
                    <div class="ca-dot"></div>
                    <div class="section-title">Select Distribution Event</div>
                </div>
                <div class="section-card-body">
                    <label class="field-label" for="event_id">Active Events</label>
                    <select id="event_id" required>
                        <option value="">— Choose an event to begin scanning —</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }} &nbsp;|&nbsp; {{ $event->event_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Scan Mode Indicator (shown after event is selected) --}}
                <div id="scan-mode-indicator" style="display:none;margin-top:12px;padding:10px 14px;border-radius:4px;align-items:center;gap:10px;font-size:12px;font-weight:600;">
                    <div id="smi-icon" style="width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
                    <div>
                        <div id="smi-title" style="font-size:12px;font-weight:700;"></div>
                        <div id="smi-hint" style="font-size:11px;font-weight:400;margin-top:1px;"></div>
                    </div>
                </div>
            </div>

            {{-- RESULT CARD --}}
            <div id="result-card"></div>

            {{-- SCANNER --}}
            <div class="section-card" id="scanner-container">
                <div class="section-card-header green-top">
                    <div class="ca-dot-green"></div>
                    <div class="section-title" id="scanner-title">Camera Scanner — Ready to Scan</div>
                </div>
                <div class="section-card-body">
                    <div class="scanner-label">Scan Area Active</div>
                    <div id="reader"></div>
                    <p class="scanner-hint">Scanning will start automatically when the camera detects a QR code.</p>
                </div>
            </div>

        @endif

    </div>

    <!-- FOOTER -->
    <div class="page-footer">
        <div class="footer-left">&copy; {{ date('Y') }} <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office</div>
        <div class="footer-center">Republic of the Philippines</div>
        <a class="fb-link" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            facebook.com/naicmdrrmo
        </a>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
</div>

<script>
    /* ── Clock ── */
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now    = new Date();
        const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ══════════════════════════════════════════════════════
       SCANNER LOGIC — FIXED
    ══════════════════════════════════════════════════════ */

    const eventSelect        = document.getElementById('event_id');

    // Auto-trigger event selection if ?event_id was passed in the URL
    (function() {
        const params = new URLSearchParams(window.location.search);
        const preselect = params.get('event_id');
        if (preselect && eventSelect) {
            eventSelect.value = preselect;
            eventSelect.dispatchEvent(new Event('change'));
        }
    })();
    const scannerContainer   = document.getElementById('scanner-container');
    const resultCard         = document.getElementById('result-card');
    let html5QrcodeScanner   = null;
    let scannerRunning       = false;   // ← FIX 2: track if scanner is already active
    let currentHouseholdData = null;
    let scanCount            = 0;       // local session tally (added to server count)
    let duplicateCount       = 0;
    let currentEditorItems   = {};      // ← FIX: declared here to avoid TDZ error
    let recipientStream      = null;   // active MediaStream for the recipient camera
    let capturedPhotoBlob    = null;   // Blob set after capturePhoto(), sent on confirm
    let photoTakenAt         = null;   // ISO timestamp captured client-side

    // Full master catalogue — all items available across all categories
    const ALL_ITEMS = {
        'rice':                  { name: 'Rice',                          unit: 'kg',    step: '0.5', cat: 'food_pack' },
        'canned_goods':          { name: 'Canned Goods',                  unit: 'cans',  step: '1',   cat: 'food_pack' },
        'instant_noodles':       { name: 'Instant Noodles',               unit: 'pcs',   step: '1',   cat: 'food_pack' },
        'coffee':                { name: 'Coffee',                        unit: 'pack',  step: '1',   cat: 'food_pack' },
        'bar_soap':              { name: 'Bar Soap',                      unit: 'bars',  step: '1',   cat: 'hygiene_kit' },
        'shampoo':               { name: 'Shampoo',                       unit: 'btl',   step: '1',   cat: 'hygiene_kit' },
        'toothbrush':            { name: 'Toothbrush',                    unit: 'pcs',   step: '1',   cat: 'hygiene_kit' },
        'toothpaste':            { name: 'Toothpaste',                    unit: 'tube',  step: '1',   cat: 'hygiene_kit' },
        'deodorant':             { name: 'Deodorant',                     unit: 'pcs',   step: '1',   cat: 'hygiene_kit' },
        'towel':                 { name: 'Towel / Face Towel',            unit: 'pcs',   step: '1',   cat: 'hygiene_kit' },
        'bucket':                { name: 'Bucket',                        unit: 'pcs',   step: '1',   cat: 'hygiene_kit' },
        'dipper':                { name: 'Dipper (Tabo)',                  unit: 'pcs',   step: '1',   cat: 'hygiene_kit' },
        'feminine_hygiene_wash': { name: 'Feminine Hygiene Wash',         unit: 'btl',   step: '1',   cat: 'dignity_kit' },
        'sanitary_pads':         { name: 'Sanitary Pads / Napkins',       unit: 'pack',  step: '1',   cat: 'dignity_kit' },
        'tissue_wipes':          { name: 'Tissue / Wipes',                unit: 'pack',  step: '1',   cat: 'dignity_kit' },
        'underwear':             { name: 'Underwear',                     unit: 'pcs',   step: '1',   cat: 'dignity_kit' },
        'alcohol':               { name: 'Alcohol',                       unit: 'btl',   step: '1',   cat: 'first_aid_kit' },
        'bandaid':               { name: 'Band-aid',                      unit: 'box',   step: '1',   cat: 'first_aid_kit' },
        'bandage':               { name: 'Bandage',                       unit: 'roll',  step: '1',   cat: 'first_aid_kit' },
        'betadine':              { name: 'Betadine',                      unit: 'btl',   step: '1',   cat: 'first_aid_kit' },
        'elastic_bandage':       { name: 'Elastic Bandage',               unit: 'roll',  step: '1',   cat: 'first_aid_kit' },
        'emergency_medicine':    { name: 'Emergency Medicine',            unit: 'pcs',   step: '1',   cat: 'first_aid_kit' },
        'gauze_pad':             { name: 'Gauze Pad',                     unit: 'pcs',   step: '1',   cat: 'first_aid_kit' },
        'gauze_roll':            { name: 'Gauze Roll',                    unit: 'roll',  step: '1',   cat: 'first_aid_kit' },
        'medical_tape':          { name: 'Medical Tape',                  unit: 'roll',  step: '1',   cat: 'first_aid_kit' },
        'cash_aid':              { name: 'Cash Aid',                      unit: 'PHP',   step: '0.01',cat: 'cash_aid' },
        // Family Clothing Kit
        'bath_towel':            { name: 'Bath Towel',                    unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'ladies_panties':        { name: "Ladies' Panties (Adult)",       unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'girls_panties':         { name: "Girls' Panties",                unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'mens_briefs':           { name: "Men's Briefs",                  unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'boys_briefs':           { name: "Boys' Briefs",                  unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'sando_bra_adult':       { name: 'Sando Bra (Adult)',             unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'sando_bra_girl':        { name: 'Sando Bra (Girls)',             unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'adults_tshirt':         { name: "Adults' T-Shirt",               unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'childrens_tshirt':      { name: "Children's T-Shirt",            unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'adults_short_pants':    { name: "Adults' Short Pants",           unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'childrens_shorts':      { name: "Children's Shorts",             unit: 'pcs',   step: '1',   cat: 'clothing_kit' },
        'adults_slippers':       { name: "Adults' Slippers",              unit: 'pairs', step: '1',   cat: 'clothing_kit' },
        'childrens_slippers':    { name: "Children's Slippers",           unit: 'pairs', step: '1',   cat: 'clothing_kit' },
        'clothing_plastic_box':  { name: 'Plastic Box (Clothing Kit)',    unit: 'pc',    step: '1',   cat: 'clothing_kit' },
        // Sleeping Kit
        'blanket':               { name: 'Blanket',                       unit: 'pcs',   step: '1',   cat: 'sleeping_kit' },
        'plastic_mat':           { name: 'Plastic Mat',                   unit: 'pc',    step: '1',   cat: 'sleeping_kit' },
        'mosquito_net':          { name: 'Mosquito Net',                  unit: 'pc',    step: '1',   cat: 'sleeping_kit' },
        'malong':                { name: 'Malong (wrap cloth)',            unit: 'pc',    step: '1',   cat: 'sleeping_kit' },
        'pillow':                { name: 'Pillow',                        unit: 'pc',    step: '1',   cat: 'sleeping_kit' },
        'sleeping_plastic_box':  { name: 'Plastic Box (Sleeping Kit)',    unit: 'pc',    step: '1',   cat: 'sleeping_kit' },
        // Kitchen Kit
        'spoon':                 { name: 'Spoon',                         unit: 'pcs',   step: '1',   cat: 'kitchen_kit' },
        'fork':                  { name: 'Fork',                          unit: 'pcs',   step: '1',   cat: 'kitchen_kit' },
        'drinking_glass':        { name: 'Drinking Glass',                unit: 'pcs',   step: '1',   cat: 'kitchen_kit' },
        'plate':                 { name: 'Plate',                         unit: 'pcs',   step: '1',   cat: 'kitchen_kit' },
        'frying_pan':            { name: 'Frying Pan',                    unit: 'pc',    step: '1',   cat: 'kitchen_kit' },
        'cooking_pan':           { name: 'Cooking Pan',                   unit: 'pc',    step: '1',   cat: 'kitchen_kit' },
        'ladle':                 { name: 'Ladle',                         unit: 'pc',    step: '1',   cat: 'kitchen_kit' },
        'kitchen_plastic_box':   { name: 'Plastic Box (Kitchen Kit)',     unit: 'pc',    step: '1',   cat: 'kitchen_kit' },
    };

    const CAT_META = {
        'food_pack':    { label: 'Food Pack',             icon: '🍱', accent: '#92400E', bg: '#FFFBEB', border: '#FDE68A', headBg: '#FEF3C7' },
        'hygiene_kit':  { label: 'Hygiene Kit',           icon: '🧴', accent: '#1D4ED8', bg: '#EFF6FF', border: '#BFDBFE', headBg: '#DBEAFE' },
        'dignity_kit':  { label: 'Dignity Kit',           icon: '🎀', accent: '#7E22CE', bg: '#FAF5FF', border: '#E9D5FF', headBg: '#F3E8FF' },
        'first_aid_kit':{ label: 'First Aid Kit',         icon: '🩹', accent: '#B91C1C', bg: '#FFF1F2', border: '#FECDD3', headBg: '#FFE4E6' },
        'cash_aid':     { label: 'Cash Aid',              icon: '💵', accent: '#15803D', bg: '#F0FDF4', border: '#BBF7D0', headBg: '#DCFCE7' },
        'clothing_kit': { label: 'Family Clothing Kit',   icon: '👕', accent: '#6D28D9', bg: '#FDF4FF', border: '#E9D5FF', headBg: '#F5F3FF' },
        'sleeping_kit': { label: 'Sleeping Kit',          icon: '🛏️', accent: '#065F46', bg: '#F0FDF4', border: '#6EE7B7', headBg: '#D1FAE5' },
        'kitchen_kit':  { label: 'Kitchen Kit',           icon: '🍳', accent: '#C2410C', bg: '#FFF7ED', border: '#FED7AA', headBg: '#FFEDD5' },
        'other':        { label: 'Other Items',           icon: '📦', accent: '#374151', bg: '#F9FAFB', border: '#E5E7EB', headBg: '#F3F4F6' },
    };

    /* ── Load today's real counts from the server, filtered by selected event ── */
    async function fetchTodayStats() {
        const eventId = eventSelect?.value ?? '';
        const url     = '{{ route("staff.scan.history") }}?stats_only=1' + (eventId ? `&event_id=${eventId}` : '');
        try {
            const res  = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            scanCount      = data.confirmed_today ?? 0;
            duplicateCount = data.duplicates_today ?? 0;
            document.getElementById('scan-count').textContent      = scanCount;
            document.getElementById('duplicate-count').textContent = duplicateCount;
        } catch {
            document.getElementById('scan-count').textContent      = 0;
            document.getElementById('duplicate-count').textContent = 0;
        }
    }
    // Don't fetch stats on load — show 0 until user picks an event
    document.getElementById('scan-count').textContent      = 0;
    document.getElementById('duplicate-count').textContent = 0;

    // Auto-start scanner & fetch stats if event already pre-selected on load
    if (eventSelect?.value && !scannerRunning) {
        startScanner();
        fetchTodayStats();
        updateScanModeUI(eventSelect.value);
    }

    /* ── Only start scanner once; switching events does NOT restart camera ── */
    // Map of event id → scan_mode (populated from blade)
    const eventScanModes = {
        @foreach($events as $event)
        {{ $event->id }}: '{{ $event->scan_mode ?? 'household' }}',
        @endforeach
    };

    function updateScanModeUI(eventId) {
        const indicator = document.getElementById('scan-mode-indicator');
        const mode = eventScanModes[eventId] || 'household';
        const isHousehold = mode === 'household';

        indicator.style.display = 'flex';
        indicator.style.background    = isHousehold ? 'var(--blue-pale)' : '#F5F3FF';
        indicator.style.border        = isHousehold ? '1px solid #C7D9F5' : '1px solid #DDD6FE';

        const icon = document.getElementById('smi-icon');
        icon.style.background = isHousehold ? 'var(--blue)' : '#7C3AED';
        icon.innerHTML = isHousehold
            ? '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>'
            : '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>';

        document.getElementById('smi-title').style.color = isHousehold ? 'var(--blue-dark)' : '#5B21B6';
        document.getElementById('smi-title').textContent = isHousehold ? 'Household QR Mode' : 'Family Head QR Mode';

        document.getElementById('smi-hint').style.color = isHousehold ? 'var(--blue)' : '#7C3AED';
        document.getElementById('smi-hint').textContent = isHousehold
            ? 'Scan the household QR card — one release per household'
            : 'Scan the family head personal QR — one release per family head';

        document.getElementById('scanner-title').textContent = isHousehold
            ? 'Camera Scanner — Point at Household QR Card'
            : 'Camera Scanner — Point at Family Head Personal QR Card';

        document.getElementById('page-sub-text').textContent = isHousehold
            ? 'Scan household QR cards to validate and record ayuda distribution'
            : 'Scan family head personal QR cards to validate and record ayuda distribution';
    }

    eventSelect?.addEventListener('change', function () {
        if (!this.value) {
            stopScanner();
            scanCount = 0;
            duplicateCount = 0;
            document.getElementById('scan-count').textContent      = 0;
            document.getElementById('duplicate-count').textContent = 0;
            document.getElementById('scan-mode-indicator').style.display = 'none';
            return;
        }
        if (!scannerRunning) {
            startScanner();
        }
        resultCard.style.display = 'none';
        resultCard.innerHTML = '';
        currentHouseholdData = null;
        fetchTodayStats();
        updateScanModeUI(this.value);
    });

    function startScanner() {
        scannerContainer.style.display = 'block';
        html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10, qrbox: 250, rememberLastUsedCamera: true,
        });
        html5QrcodeScanner.render(onScanSuccess, onScanError);
        scannerRunning = true;
        autoStartCamera();
    }

    // Html5QrcodeScanner renders in idle state — programmatically click
    // "Start Scanning" so the camera opens immediately without user interaction.
    function autoStartCamera() {
        // Poll ONLY for the specific camera-permission button by its known ID.
        // NEVER use a generic querySelector('#reader button') fallback — the library
        // also renders a file-upload button inside #reader, and clicking that would
        // open the OS file explorer unexpectedly.
        const interval = setInterval(() => {
            const btn = document.getElementById('html5-qrcode-button-camera-permission');
            if (btn) {
                clearInterval(interval);
                btn.click();
            }
        }, 100);
        // Give up after 5 s to avoid infinite polling
        setTimeout(() => clearInterval(interval), 5000);
    }

    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(() => {});
            scannerContainer.style.display = 'none';
            scannerRunning = false;
        }
    }

    function onScanSuccess(decodedText) {
        html5QrcodeScanner.pause(true);
        processQRCode(decodedText);
    }
    function onScanError() { /* ignore per-frame errors */ }

    async function processQRCode(serialCode) {
        const eventId = eventSelect.value;

        if (!eventId) {
            showErrorResult('Please select an active event before scanning.');
            return;
        }

        try {
            const response = await fetch('{{ route("staff.scan.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ serial_code: serialCode, event_id: eventId }),
            });
            const data = await response.json();

            if (data.status === 'success') {
                currentHouseholdData = data.household;
                showSuccessResult(data);
            } else if (data.status === 'duplicate') {
                duplicateCount++;
                document.getElementById('duplicate-count').textContent = duplicateCount;
                showDuplicateResult(data);
            } else if (data.status === 'wrong_barangay') {
                showBarangayBlockResult(data);
            } else if (data.status === 'wrong_qr_type') {
                showWrongQrTypeResult(data);
            } else {
                showErrorResult(data.message);
            }
        } catch (err) {
            showErrorResult('Network error: ' + err.message);
        }
    }

    /* ── Result renderers ── */

    // Tracks items currently shown in the editor (key → {name, qty, unit})

    function buildItemsEditor(reliefItems) {
        currentEditorItems = {};
        if (reliefItems && Object.keys(reliefItems).length > 0) {
            for (const [key, item] of Object.entries(reliefItems)) {
                currentEditorItems[key] = { name: item.name, qty: item.qty, unit: item.unit };
            }
        }
        return renderItemsEditor();
    }

    function renderItemsEditor() {
        if (Object.keys(currentEditorItems).length === 0) {
            return `
            <div id="items-editor-wrap" style="margin:16px 0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1B3F7A" stroke-width="2.5"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
                    <span style="font-size:12px;font-weight:700;color:#1B3F7A;text-transform:uppercase;letter-spacing:0.6px;">Items to Release</span>
                </div>
                <div id="items-sections"></div>
                ${buildAddItemButton()}
            </div>`;
        }

        // Group by category
        const grouped = {};
        for (const [key, item] of Object.entries(currentEditorItems)) {
            const catKey = ALL_ITEMS[key]?.cat || 'other';
            if (!grouped[catKey]) grouped[catKey] = [];
            grouped[catKey].push([key, item]);
        }

        let sectionsHtml = '';
        for (const [catKey, items] of Object.entries(grouped)) {
            const cat = CAT_META[catKey] || CAT_META['other'];
            let itemRows = '';
            items.forEach(([key, item]) => {
                const isCash = item.unit === 'PHP';
                const step = ALL_ITEMS[key]?.step || '1';
                itemRows += `
                    <div data-item-row="${key}" style="display:flex;align-items:center;justify-content:space-between;padding:9px 14px;border-bottom:1px solid ${cat.border};background:#fff;">
                        <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:0;">
                            <div style="width:6px;height:6px;border-radius:50%;background:${cat.accent};flex-shrink:0;"></div>
                            <span style="font-size:13px;color:#374151;font-weight:500;">${item.name}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                            ${isCash ? '<span style="font-size:13px;font-weight:700;color:'+cat.accent+';">₱</span>' : ''}
                            <input
                                type="number"
                                data-item-key="${key}"
                                value="${item.qty}"
                                min="0"
                                step="${step}"
                                style="width:72px;padding:5px 8px;border:1px solid ${cat.border};border-radius:4px;font-size:13px;font-family:inherit;text-align:right;background:${cat.bg};color:#1f2937;outline:none;transition:border-color 0.15s,box-shadow 0.15s;"
                                onfocus="this.style.borderColor='${cat.accent}';this.style.boxShadow='0 0 0 2px ${cat.border}';"
                                onblur="this.style.borderColor='${cat.border}';this.style.boxShadow='none';"
                            >
                            <span style="font-size:11px;color:${cat.accent};font-weight:700;min-width:28px;">${isCash ? 'PHP' : item.unit}</span>
                            <button type="button" onclick="removeItem('${key}', '${item.name}')"
                                style="width:24px;height:24px;border-radius:50%;border:1px solid #FECACA;background:#FEF2F2;color:#C0392B;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;line-height:1;transition:background 0.12s;"
                                onmouseover="this.style.background='#FECACA';" onmouseout="this.style.background='#FEF2F2';"
                                title="Remove this item">×</button>
                        </div>
                    </div>`;
            });

            sectionsHtml += `
                <div style="border:1px solid ${cat.border};border-radius:6px;overflow:hidden;margin-bottom:10px;">
                    <div style="padding:9px 14px;background:${cat.headBg};border-bottom:1px solid ${cat.border};display:flex;align-items:center;gap:8px;">
                        <span style="font-size:16px;line-height:1;">${cat.icon}</span>
                        <span style="font-size:12px;font-weight:700;color:${cat.accent};text-transform:uppercase;letter-spacing:0.6px;">${cat.label}</span>
                        <span style="margin-left:auto;font-size:11px;color:${cat.accent};opacity:0.7;">${items.length} item${items.length > 1 ? 's' : ''}</span>
                    </div>
                    ${itemRows}
                </div>`;
        }

        return `
            <div id="items-editor-wrap" style="margin:16px 0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1B3F7A" stroke-width="2.5"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
                    <span style="font-size:12px;font-weight:700;color:#1B3F7A;text-transform:uppercase;letter-spacing:0.6px;">Items to Release</span>
                    <span style="font-size:11px;color:#9AA3B0;margin-left:2px;">— adjust quantities, add or remove items</span>
                </div>
                <div id="items-sections">${sectionsHtml}</div>
                ${buildAddItemButton()}
            </div>`;
    }

    function buildAddItemButton() {
        // Build dropdown of items not already in currentEditorItems
        const missing = Object.entries(ALL_ITEMS).filter(([key]) => !currentEditorItems[key]);
        if (missing.length === 0) return '';

        const opts = missing.map(([key, meta]) => {
            const cat = CAT_META[meta.cat] || CAT_META['other'];
            return `<option value="${key}">${cat.icon} ${meta.name} (${meta.unit})</option>`;
        }).join('');

        return `
            <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                <select id="add-item-select" style="flex:1;padding:7px 10px;border:1px dashed #C5D9F5;border-radius:4px;font-size:12px;font-family:inherit;color:#1B3F7A;background:#EAF0FA;outline:none;">
                    <option value="">+ Add an item...</option>
                    ${opts}
                </select>
                <button type="button" onclick="addItemFromSelect()"
                    style="padding:7px 14px;background:#1B3F7A;color:#fff;border:none;border-radius:4px;font-size:12px;font-weight:700;font-family:inherit;cursor:pointer;white-space:nowrap;transition:background 0.15s;"
                    onmouseover="this.style.background='#122D5A';" onmouseout="this.style.background='#1B3F7A';">
                    Add
                </button>
            </div>`;
    }

    function addItemFromSelect() {
        const sel = document.getElementById('add-item-select');
        const key = sel?.value;
        if (!key || !ALL_ITEMS[key]) return;
        const meta = ALL_ITEMS[key];
        currentEditorItems[key] = { name: meta.name, qty: 1, unit: meta.unit };
        refreshItemsEditorInPlace();
        sel.value = '';
    }

    function removeItem(key, name) {
        if (!confirm(`Remove "${name}" from this release?\n\nThis will not affect future releases — only this specific one.`)) return;
        delete currentEditorItems[key];
        refreshItemsEditorInPlace();
    }

    function refreshItemsEditorInPlace() {
        const wrap = document.getElementById('items-editor-wrap');
        if (!wrap) return;
        wrap.outerHTML = renderItemsEditor();
    }

    function getEditedItems(reliefItems) {
        // Use currentEditorItems as the source of truth (add/remove already applied)
        if (Object.keys(currentEditorItems).length === 0) return null;
        const result = {};
        document.querySelectorAll('[data-item-key]').forEach(input => {
            const key = input.dataset.itemKey;
            if (currentEditorItems[key]) {
                result[key] = {
                    name: currentEditorItems[key].name,
                    qty:  parseFloat(input.value) || 0,
                    unit: currentEditorItems[key].unit,
                };
            }
        });
        // Include any items in currentEditorItems not yet rendered (edge case)
        for (const [key, item] of Object.entries(currentEditorItems)) {
            if (!result[key]) {
                result[key] = { name: item.name, qty: item.qty, unit: item.unit };
            }
        }
        return result;
    }

    function showSuccessResult(data) {
        const badges = [];
        if (data.household.is_4ps)    badges.push('<span class="badge badge-4ps">4Ps</span>');
        if (data.household.is_pwd)    badges.push('<span class="badge badge-pwd">PWD</span>');
        if (data.household.is_senior) badges.push('<span class="badge badge-senior">Senior</span>');

        // Store relief_items on the household data for use in confirmRelease
        currentHouseholdData.relief_items = data.relief_items || {};

        resultCard.style.display = 'block';
        resultCard.innerHTML = `
            <div class="result-inner success">
                <div class="result-header success">
                    <div class="result-status-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="result-status-text">Household Found — Ready to Release</div>
                </div>
                <div class="result-body">
                    <table class="info-table">
                        <tr><td>Household Head</td><td><strong>${data.household.name}</strong></td></tr>
                        <tr><td>Serial Code</td><td><strong>${data.household.serial_code}</strong></td></tr>
                        <tr><td>Address</td><td>${data.household.address}</td></tr>
                        <tr><td>Members</td><td>${data.household.members_count} person(s)</td></tr>
                        ${badges.length ? `<tr><td>Program Flags</td><td>${badges.join('')}</td></tr>` : ''}
                    </table>

                    ${buildItemsEditor(data.relief_items)}

                    <!-- ─── STEP 2: Photo capture ─── -->
                    <div class="photo-step" id="photo-step-wrap">
                        <div class="photo-step-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                            </svg>
                            <span class="photo-step-title" id="photo-step-title">Step 2 — Take Photo of Relief Recipient</span>
                        </div>
                        <div class="photo-step-body">
                            <!-- Live camera view -->
                            <div id="photo-camera-wrap">
                                <video id="photo-video" autoplay playsinline muted></video>
                                <canvas id="photo-canvas"></canvas>
                                <p class="photo-instruction">Position the <strong>relief recipient</strong> in frame, then tap <em>Capture Photo</em>.</p>
                                <p class="photo-required-note">⚠ A photo is required to enable Confirm Release</p>
                                <div class="photo-btn-row">
                                    <button class="btn-capture" onclick="capturePhoto()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                                            <circle cx="12" cy="13" r="4"/>
                                        </svg>
                                        Capture Photo
                                    </button>
                                </div>
                            </div>
                            <!-- Captured preview (hidden until photo taken) -->
                            <div id="photo-preview-wrap">
                                <img id="photo-preview" src="" alt="Recipient photo">
                                <div class="photo-btn-row">
                                    <button class="btn-retake" onclick="retakePhoto()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="23 4 23 10 17 10"/>
                                            <path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                                        </svg>
                                        Retake
                                    </button>
                                </div>
                                <p class="photo-instruction" style="color:var(--green);font-weight:600;margin-top:8px;">
                                    ✓ Photo captured — you may now confirm the release.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ─── Action buttons ─── -->
                    <div class="btn-row" style="margin-top:16px;">
                        <button class="btn btn-confirm" id="btn-confirm-release"
                                onclick="confirmRelease()"
                                title="Confirm and record release">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Confirm Release
                        </button>
                        <button class="btn btn-secondary" onclick="resetScanner()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Cancel
                        </button>
                    </div>
                </div>
            </div>`;

        resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Start the recipient camera stream immediately
        startRecipientCamera();
    }

    function showDuplicateResult(data) {
        resultCard.style.display = 'block';
        resultCard.innerHTML = `
            <div class="result-inner duplicate">
                <div class="result-header duplicate">
                    <div class="result-status-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="result-status-text">Already Received — Duplicate Blocked</div>
                </div>
                <div class="result-body">
                    <table class="info-table">
                        <tr><td>Household Head</td><td><strong>${data.household.name}</strong></td></tr>
                        <tr><td>Serial Code</td><td><strong>${data.household.serial_code}</strong></td></tr>
                        <tr><td>Previously Released</td><td>${data.previous_release.date}</td></tr>
                        <tr><td>Released By</td><td>${data.previous_release.staff}</td></tr>
                    </table>
                    <div class="btn-row">
                        <button class="btn btn-secondary" onclick="resetScanner()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Scan Next
                        </button>
                    </div>
                </div>
            </div>`;
        resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /* ── FIX 3: Barangay restriction block result ── */
    function showBarangayBlockResult(data) {
        resultCard.style.display = 'block';
        resultCard.innerHTML = `
            <div class="result-inner barangay">
                <div class="result-header barangay">
                    <div class="result-status-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <div class="result-status-text">Wrong Barangay — Not Eligible for This Event</div>
                </div>
                <div class="result-body">
                    <table class="info-table">
                        <tr><td>Household Head</td><td><strong>${data.household.name}</strong></td></tr>
                        <tr><td>Household Barangay</td><td><strong>${data.household.barangay}</strong></td></tr>
                        <tr><td>Event Target</td><td>${data.event_target}</td></tr>
                    </table>
                    <p style="font-size:12px;color:var(--red);margin-bottom:16px;">
                        This household is not in the target barangay for this event. Release is not allowed.
                    </p>
                    <div class="btn-row">
                        <button class="btn btn-secondary" onclick="resetScanner()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Scan Next
                        </button>
                    </div>
                </div>
            </div>`;
        resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function showWrongQrTypeResult(data) {
        resultCard.style.display = 'block';
        resultCard.innerHTML = `
            <div class="result-inner error">
                <div class="result-header error">
                    <div class="result-status-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <div class="result-status-text">Wrong QR Type — ${data.expected_mode === 'household' ? 'Household QR Required' : 'Family Head QR Required'}</div>
                </div>
                <div class="result-body">
                    <table class="info-table">
                        <tr><td>Scanned Code</td><td><strong>${data.scanned_code}</strong></td></tr>
                        <tr><td>Expected QR Type</td><td><strong>${data.expected_mode === 'household' ? '🏠 Household QR Card' : '👤 Family Head Personal QR'}</strong></td></tr>
                        <tr><td>Event Mode</td><td>${data.event_name}</td></tr>
                    </table>
                    <p style="font-size:12px;color:var(--red);margin-bottom:16px;">
                        ${data.expected_mode === 'household'
                            ? 'This event requires the <strong>household QR card</strong>. Please scan the correct card.'
                            : 'This event requires the <strong>family head personal QR card</strong>. Please scan the correct card.'}
                    </p>
                    <div class="btn-row">
                        <button class="btn btn-secondary" onclick="resetScanner()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Try Again
                        </button>
                    </div>
                </div>
            </div>`;
        resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function showErrorResult(message) {
        resultCard.style.display = 'block';
        resultCard.innerHTML = `
            <div class="result-inner error">
                <div class="result-header error">
                    <div class="result-status-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </div>
                    <div class="result-status-text">Error — QR Code Not Recognised</div>
                </div>
                <div class="result-body">
                    <p style="font-size:13px;color:var(--gray-600);margin-bottom:16px;">${message}</p>
                    <div class="btn-row">
                        <button class="btn btn-secondary" onclick="resetScanner()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Try Again
                        </button>
                    </div>
                </div>
            </div>`;
    }

    /* ══════════════════════════════════════════════════════
       RECIPIENT PHOTO — camera helpers
    ══════════════════════════════════════════════════════ */

    // recipientStream, capturedPhotoBlob, photoTakenAt declared at top of script

    async function startRecipientCamera() {
        try {
            recipientStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false,
            });
            const video = document.getElementById('photo-video');
            if (video) {
                video.srcObject = recipientStream;
            }
        } catch (err) {
            // Camera denied or unavailable — show an upload fallback
            const cameraWrap = document.getElementById('photo-camera-wrap');
            if (cameraWrap) {
                cameraWrap.innerHTML = `
                    <div style="padding:14px;background:var(--red-pale);border:1px solid #FECACA;border-radius:4px;font-size:12px;color:var(--red);margin-bottom:10px;">
                        <strong>Camera unavailable:</strong> ${err.message}.<br>
                        Please upload a photo of the recipient instead.
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--gray-600);margin-bottom:7px;">
                        Upload Recipient Photo
                    </label>
                    <input type="file" accept="image/*"
                           id="photo-file-input"
                           style="width:100%;padding:8px;border:1px solid var(--gray-200);border-radius:4px;font-size:13px;background:var(--white);"
                           onchange="handlePhotoFileInput(this)">
                    <p class="photo-required-note" style="margin-top:8px;">⚠ A photo is required to enable Confirm Release</p>`;
            }
        }
    }

    function stopRecipientCamera() {
        if (recipientStream) {
            recipientStream.getTracks().forEach(t => t.stop());
            recipientStream = null;
        }
    }

    function capturePhoto() {
        const video  = document.getElementById('photo-video');
        const canvas = document.getElementById('photo-canvas');
        if (!video || !canvas) return;

        canvas.width  = video.videoWidth  || 1280;
        canvas.height = video.videoHeight || 720;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        photoTakenAt = new Date().toISOString();

        canvas.toBlob(blob => {
            capturedPhotoBlob = blob;
            onPhotoReady(canvas.toDataURL('image/jpeg', 0.85));
        }, 'image/jpeg', 0.85);
    }

    function handlePhotoFileInput(input) {
        const file = input.files[0];
        if (!file) return;
        capturedPhotoBlob = file;
        photoTakenAt = new Date().toISOString();
        const reader = new FileReader();
        reader.onload = e => onPhotoReady(e.target.result);
        reader.readAsDataURL(file);
    }

    function onPhotoReady(dataUrl) {
        // Stop live stream only if it was started (file-upload fallback has no stream)
        if (recipientStream !== null) {
            stopRecipientCamera();
        }

        // Show preview
        const cameraWrap  = document.getElementById('photo-camera-wrap');
        const previewWrap = document.getElementById('photo-preview-wrap');
        const preview     = document.getElementById('photo-preview');
        const stepWrap    = document.getElementById('photo-step-wrap');
        const stepTitle   = document.getElementById('photo-step-title');

        if (cameraWrap)  cameraWrap.style.display  = 'none';
        if (previewWrap) previewWrap.style.display  = 'block';
        if (preview)     preview.src                = dataUrl;
        if (stepWrap)    stepWrap.classList.add('captured');
        if (stepTitle)   stepTitle.textContent      = 'Step 2 — Photo Captured ✓';
    }

    function retakePhoto() {
        capturedPhotoBlob = null;
        photoTakenAt      = null;

        // Restore camera view
        const cameraWrap  = document.getElementById('photo-camera-wrap');
        const previewWrap = document.getElementById('photo-preview-wrap');
        const stepWrap    = document.getElementById('photo-step-wrap');
        const stepTitle   = document.getElementById('photo-step-title');

        if (previewWrap) previewWrap.style.display = 'none';
        if (cameraWrap)  cameraWrap.style.display  = 'block';
        if (stepWrap)    stepWrap.classList.remove('captured');
        if (stepTitle)   stepTitle.textContent     = 'Step 2 — Take Photo of Relief Recipient';

        // Restart camera
        startRecipientCamera();
    }

    /* ══════════════════════════════════════════════════════
       CONFIRM RELEASE — now includes photo upload
    ══════════════════════════════════════════════════════ */

    async function confirmRelease() {
        const eventId    = eventSelect.value;
        const confirmBtn = document.getElementById('btn-confirm-release');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 style="width:15px;height:15px;animation:spin 1s linear infinite">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
            </svg>
            Recording...`;

        try {
            // Build multipart payload — photo + JSON fields together
            const formData = new FormData();
            formData.append('household_id',     currentHouseholdData.id);
            formData.append('event_id',         eventId);
            formData.append('serial_code',      currentHouseholdData.serial_code);
            formData.append('family_member_id', currentHouseholdData.family_member_id ?? '');
            formData.append('items_received',   JSON.stringify(getEditedItems(currentHouseholdData.relief_items)));
            formData.append('photo_taken_at',   photoTakenAt ?? '');
            if (capturedPhotoBlob) {
                formData.append('recipient_photo', capturedPhotoBlob, 'recipient.jpg');
            }
            formData.append('_token',           document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch('{{ route("staff.scan.confirm") }}', {
                method: 'POST',
                body:   formData,   // Content-Type set automatically (multipart/form-data)
            });

            const data = await response.json();

            if (data.status === 'success') {
                scanCount++;
                document.getElementById('scan-count').textContent = scanCount;

                // Grab the locally-captured preview src BEFORE clearing state —
                // this avoids depending on the server-returned URL which may not
                // be publicly accessible or may take time to resolve.
                const localPhotoSrc = document.getElementById('photo-preview')?.src || null;

                // Clean up camera resources
                stopRecipientCamera();
                capturedPhotoBlob = null;
                photoTakenAt      = null;

                resultCard.innerHTML = `
                    <div class="result-inner confirmed">
                        <div class="result-header confirmed">
                            <div class="result-status-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div class="result-status-text">Distribution Successfully Recorded</div>
                        </div>
                        <div class="confirmed-body">
                            <div class="confirmed-check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div class="confirmed-title">Release Confirmed</div>
                            <div class="confirmed-name">${data.log.household}</div>
                            <div class="confirmed-time">${data.log.time}</div>
                            ${localPhotoSrc ? `
                            <div style="margin:12px auto 0;max-width:240px;">
                                <img src="${localPhotoSrc}" alt="Recipient photo"
                                     style="width:100%;border-radius:6px;border:2px solid var(--green);object-fit:cover;max-height:160px;">
                                <div style="font-size:10px;color:var(--gray-400);text-align:center;margin-top:4px;">Photo recorded ✓</div>
                            </div>` : ''}
                            <div class="btn-row" style="max-width:300px;margin:16px auto 0;">
                                <button class="btn btn-confirm" onclick="resetScanner()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                    Scan Next
                                </button>
                            </div>
                        </div>
                    </div>`;
            } else {
                // Re-enable the button so staff can retry
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Confirm Release`;
                showErrorResult(data.message);
            }
        } catch (err) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Confirm Release`;
            showErrorResult('Failed to record distribution. Please try again.');
        }
    }

    function resetScanner() {
        // Stop the recipient camera only if it is actually running.
        // It is started exclusively by showSuccessResult — no other result type starts it.
        if (recipientStream !== null) {
            stopRecipientCamera();
        }
        capturedPhotoBlob = null;
        photoTakenAt      = null;

        resultCard.style.display = 'none';
        resultCard.innerHTML     = '';
        currentHouseholdData     = null;

        // Restart the QR scanner. Html5QrcodeScanner (the wrapper) has no
        // .resume() after .pause(true). Fire clear() best-effort (non-blocking),
        // then synchronously wipe #reader and mount a fresh scanner so the
        // restart is never blocked by the async clear() outcome.
        if (scannerRunning) {
            // clear() is async — wait for it to fully release the camera before
            // mounting a new instance, otherwise the new scanner silently fails
            // to acquire the camera stream.
            const oldScanner = html5QrcodeScanner;
            html5QrcodeScanner = null;
            scannerRunning = false;

            const doRestart = () => {
                const reader = document.getElementById('reader');
                if (reader) reader.innerHTML = '';
                if (scannerContainer) scannerContainer.style.display = 'block';
                html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                    fps: 10, qrbox: 250, rememberLastUsedCamera: true,
                });
                html5QrcodeScanner.render(onScanSuccess, onScanError);
                scannerRunning = true;
                autoStartCamera();
            };

            if (oldScanner) {
                oldScanner.clear().then(doRestart).catch(doRestart);
            } else {
                doRestart();
            }
        }
    }
</script>
</body>
</html>