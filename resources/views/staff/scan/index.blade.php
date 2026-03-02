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
                <div class="page-sub">Scan household QR cards to validate and record ayuda distribution</div>
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

            {{-- ── FIX 1: Stats seeded from server, not hardcoded 0 ── --}}
            <div class="stats-row">
                <div class="stat-card green-top">
                    {{-- Starts as a shimmer, filled by fetchTodayStats() on load --}}
                    <div class="stat-number" id="scan-count"><span class="stat-loading"></span></div>
                    <div class="stat-label">Confirmed Today</div>
                </div>
                <div class="stat-card red-top">
                    <div class="stat-number" id="duplicate-count"><span class="stat-loading"></span></div>
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
                            <option value="{{ $event->id }}">
                                {{ $event->event_name }} &nbsp;|&nbsp; {{ $event->event_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- RESULT CARD --}}
            <div id="result-card"></div>

            {{-- SCANNER --}}
            <div class="section-card" id="scanner-container">
                <div class="section-card-header green-top">
                    <div class="ca-dot-green"></div>
                    <div class="section-title">Camera Scanner — Point at Household QR Card</div>
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
    const scannerContainer   = document.getElementById('scanner-container');
    const resultCard         = document.getElementById('result-card');
    let html5QrcodeScanner   = null;
    let scannerRunning       = false;   // ← FIX 2: track if scanner is already active
    let currentHouseholdData = null;
    let scanCount            = 0;       // local session tally (added to server count)
    let duplicateCount       = 0;

    /* ── FIX 1: Load today's real counts from the server on page load ── */
    async function fetchTodayStats() {
        try {
            const res  = await fetch('{{ route("staff.scan.history") }}?stats_only=1', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            // Server returns today's confirmed + duplicates for this staff member
            scanCount      = data.confirmed_today ?? 0;
            duplicateCount = data.duplicates_today ?? 0;
            document.getElementById('scan-count').textContent      = scanCount;
            document.getElementById('duplicate-count').textContent = duplicateCount;
        } catch {
            // If endpoint doesn't support stats_only yet, just show 0
            document.getElementById('scan-count').textContent      = 0;
            document.getElementById('duplicate-count').textContent = 0;
        }
    }
    fetchTodayStats();

    /* ── FIX 2: Only start scanner once; switching events does NOT restart camera ── */
    eventSelect?.addEventListener('change', function () {
        if (!this.value) {
            // No event chosen — hide scanner
            stopScanner();
            return;
        }
        if (!scannerRunning) {
            // First time an event is selected → start camera
            startScanner();
        }
        // If scanner is already running, just leave it — no restart
        // Clear any previous result when switching events
        resultCard.style.display = 'none';
        resultCard.innerHTML = '';
        currentHouseholdData = null;
    });

    function startScanner() {
        scannerContainer.style.display = 'block';
        html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10, qrbox: 250, rememberLastUsedCamera: true,
        });
        html5QrcodeScanner.render(onScanSuccess, onScanError);
        scannerRunning = true;
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
                // ← Barangay restriction response
                showBarangayBlockResult(data);
            } else {
                showErrorResult(data.message);
            }
        } catch {
            showErrorResult('Network error. Please check your connection.');
        }
    }

    /* ── Result renderers ── */

    function showSuccessResult(data) {
        const badges = [];
        if (data.household.is_4ps)    badges.push('<span class="badge badge-4ps">4Ps</span>');
        if (data.household.is_pwd)    badges.push('<span class="badge badge-pwd">PWD</span>');
        if (data.household.is_senior) badges.push('<span class="badge badge-senior">Senior</span>');

        resultCard.style.display = 'block';
        resultCard.innerHTML = `
            <div class="result-inner success">
                <div class="result-header success">
                    <div class="result-status-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="result-status-text">Household Found — Confirm Release</div>
                </div>
                <div class="result-body">
                    <table class="info-table">
                        <tr><td>Household Head</td><td><strong>${data.household.name}</strong></td></tr>
                        <tr><td>Serial Code</td><td><strong>${data.household.serial_code}</strong></td></tr>
                        <tr><td>Address</td><td>${data.household.address}</td></tr>
                        <tr><td>Members</td><td>${data.household.members_count} person(s)</td></tr>
                        ${badges.length ? `<tr><td>Program Flags</td><td>${badges.join('')}</td></tr>` : ''}
                    </table>
                    <div class="btn-row">
                        <button class="btn btn-confirm" onclick="confirmRelease()">
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

    async function confirmRelease() {
        const eventId   = eventSelect.value;
        const confirmBtn = document.querySelector('.btn-confirm');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;animation:spin 1s linear infinite">
                <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
            </svg>
            Recording...`;

        try {
            const response = await fetch('{{ route("staff.scan.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    household_id: currentHouseholdData.id,
                    event_id: eventId,
                }),
            });
            const data = await response.json();
            if (data.status === 'success') {
                scanCount++;
                document.getElementById('scan-count').textContent = scanCount;
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
                            <div class="btn-row" style="max-width:300px;margin:0 auto;">
                                <button class="btn btn-confirm" onclick="resetScanner()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                    Scan Next
                                </button>
                            </div>
                        </div>
                    </div>`;
            } else {
                showErrorResult(data.message);
            }
        } catch {
            showErrorResult('Failed to record distribution. Please try again.');
        }
    }

    function resetScanner() {
        resultCard.style.display = 'none';
        resultCard.innerHTML = '';
        currentHouseholdData = null;
        if (html5QrcodeScanner && scannerRunning) {
            html5QrcodeScanner.resume();
        }
    }
</script>
</body>
</html>