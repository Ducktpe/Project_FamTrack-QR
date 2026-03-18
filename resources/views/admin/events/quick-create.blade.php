<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Create Distribution Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
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
            overflow: hidden;
        }

        /* ─── TOP UTILITY BAR ─── */
        .topbar { grid-area: topbar; background: var(--blue-dark); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
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
        .header-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .header-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
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
        .sidebar-close:hover { background: var(--red-pale); color: var(--red); }
        .sidebar-close svg { width: 16px; height: 16px; }
        .nav-section-label { padding: 18px 20px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 13.5px; font-weight: 500; color: var(--gray-600); text-decoration: none; border-left: 3px solid transparent; transition: background 0.12s, color 0.12s, border-color 0.12s; cursor: pointer; }
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

        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); gap: 12px; flex-wrap: wrap; }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .page-breadcrumb a:hover { text-decoration: underline; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }
        .back-btn { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; padding: 8px 16px; border: 1px solid var(--gray-200); background: var(--white); border-radius: 4px; transition: background 0.15s; flex-shrink: 0; }
        .back-btn:hover { background: var(--blue-pale); }
        .back-btn svg { width: 14px; height: 14px; }
        .page-date { font-size: 12px; color: var(--gray-600); text-align: right; flex-shrink: 0; }
        .page-date span { display: block; }
        .page-date strong { display: block; font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; }

        .create-layout { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }

        /* ─── FORM SECTION CARD ─── */
        .form-section { background: var(--white); border: 1px solid var(--gray-200); margin-bottom: 16px; }
        .form-section:last-child { margin-bottom: 0; }
        .form-section-header { padding: 13px 20px; border-bottom: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; gap: 10px; }
        .ca-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); border: 2px solid var(--yellow-dark); flex-shrink: 0; }
        .form-section-title { font-size: 13px; font-weight: 600; color: var(--blue-dark); }
        .form-section-body { padding: 22px 24px; }

        .form-row { display: grid; gap: 16px; margin-bottom: 16px; }
        .form-row.cols-1 { grid-template-columns: 1fr; }
        .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
        .form-row:last-child { margin-bottom: 0; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-600); }
        .form-label .req { color: var(--red); margin-left: 2px; }
        .form-label .opt { color: var(--gray-400); font-weight: 400; text-transform: none; letter-spacing: 0; font-size: 10px; margin-left: 4px; }
        .form-hint { font-size: 11px; color: var(--gray-400); margin-top: 4px; line-height: 1.5; }

        input[type="text"],
        input[type="date"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%; padding: 9px 12px;
            border: 1px solid var(--gray-200); border-radius: 3px;
            font-family: 'Open Sans', sans-serif; font-size: 13px;
            color: var(--gray-800); background: var(--white);
            transition: border-color 0.15s, box-shadow 0.15s; outline: none;
        }
        input[type="text"]:focus, input[type="date"]:focus,
        input[type="datetime-local"]:focus, select:focus, textarea:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(36,89,168,0.1);
        }
        input::placeholder, textarea::placeholder { color: var(--gray-400); }
        select { cursor: pointer; }
        textarea { resize: vertical; min-height: 80px; }

        .brgy-field-wrap { position: relative; z-index: 60; }
        .brgy-trigger {
            width: 100%; padding: 0;
            border: 1.5px solid var(--gray-200); border-radius: 6px;
            background: var(--white); cursor: pointer; text-align: left;
            display: flex; align-items: stretch;
            transition: border-color 0.15s, box-shadow 0.15s;
            user-select: none; overflow: hidden;
        }
        .brgy-trigger:focus { outline: none; }
        .brgy-trigger.open, .brgy-trigger:focus-within {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(36,89,168,0.12);
        }
        .brgy-trigger-icon {
            width: 40px; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; background: var(--blue-pale); border-right: 1.5px solid var(--gray-200);
            color: var(--blue);
        }
        .brgy-trigger-icon svg { width: 16px; height: 16px; }
        .brgy-trigger.open .brgy-trigger-icon { background: var(--blue); color: var(--white); border-color: var(--blue); }
        .brgy-trigger-body { flex: 1; padding: 9px 10px; min-width: 0; }
        .brgy-trigger-label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-400); display: block; margin-bottom: 2px; }
        .brgy-trigger-value { font-size: 13px; color: var(--gray-800); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .brgy-trigger-value.placeholder { color: var(--gray-400); }
        .brgy-trigger-right {
            display: flex; align-items: center; gap: 6px; padding: 0 10px;
            flex-shrink: 0; border-left: 1px solid var(--gray-100);
        }
        .brgy-count-badge {
            background: var(--blue); color: var(--white);
            font-size: 11px; font-weight: 700;
            min-width: 22px; height: 22px; border-radius: 11px;
            display: none; align-items: center; justify-content: center; padding: 0 6px;
        }
        .brgy-count-badge.show { display: flex; }
        .brgy-count-badge.green { background: var(--green); }
        .brgy-chevron { width: 15px; height: 15px; color: var(--gray-400); transition: transform 0.2s; flex-shrink: 0; }
        .brgy-trigger.open .brgy-chevron { transform: rotate(180deg); color: var(--blue); }

        .brgy-dropdown {
            display: none;
            position: absolute; top: calc(100% + 6px); left: 0; right: 0;
            background: var(--white);
            border: 1.5px solid var(--blue-light);
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(27,63,122,0.18);
            z-index: 9999; overflow: hidden;
        }
        .brgy-dropdown.open { display: block; animation: ddSlideIn 0.15s ease; }
        @keyframes ddSlideIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

        .brgy-dd-head {
            background: var(--blue-dark);
            padding: 10px 14px;
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
        }
        .brgy-dd-head-title { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.85); text-transform: uppercase; letter-spacing: 0.8px; }
        .brgy-dd-head-count { font-size: 11px; font-weight: 700; color: var(--yellow); }

        .brgy-dd-toolbar {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }
        .brgy-dd-search-wrap { flex: 1; position: relative; }
        .brgy-dd-search-wrap svg {
            position: absolute; left: 9px; top: 50%; transform: translateY(-50%);
            width: 13px; height: 13px; color: var(--gray-400); pointer-events: none;
        }
        .brgy-dd-search {
            width: 100%; padding: 7px 10px 7px 30px;
            border: 1px solid var(--gray-200); border-radius: 5px;
            font-size: 12px; font-family: 'Open Sans', sans-serif;
            background: var(--white); outline: none; transition: border-color 0.15s, box-shadow 0.15s;
        }
        .brgy-dd-search:focus { border-color: var(--blue-light); box-shadow: 0 0 0 2px rgba(36,89,168,0.1); }
        .brgy-dd-btns { display: flex; gap: 4px; flex-shrink: 0; }
        .brgy-dd-btn {
            padding: 6px 11px; font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            border-radius: 5px; border: 1px solid var(--gray-200);
            cursor: pointer; font-family: 'Open Sans', sans-serif;
            transition: background 0.12s; white-space: nowrap;
        }
        .brgy-dd-btn.select-all { background: var(--blue-pale); color: var(--blue); border-color: #C7D9F5; }
        .brgy-dd-btn.select-all:hover { background: #C7D9F5; }
        .brgy-dd-btn.clear-all { background: var(--gray-100); color: var(--gray-600); }
        .brgy-dd-btn.clear-all:hover { background: var(--gray-200); }

        .brgy-dd-list { max-height: 230px; overflow-y: auto; padding: 4px 0; }
        .brgy-dd-list::-webkit-scrollbar { width: 4px; }
        .brgy-dd-list::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        .brgy-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px; cursor: pointer;
            transition: background 0.1s; user-select: none;
            border-left: 3px solid transparent;
        }
        .brgy-item:hover { background: #F0F6FF; border-left-color: #C7D9F5; }
        .brgy-item.hidden { display: none; }
        .brgy-item.checked { background: var(--blue-pale); border-left-color: var(--blue); }
        .brgy-item.all-row { border-bottom: 1px solid var(--gray-200); margin-bottom: 4px; }
        .brgy-item.all-row .brgy-name { font-weight: 700; color: var(--blue-dark); }

        .brgy-checkbox {
            width: 17px; height: 17px; border: 2px solid var(--gray-300); border-radius: 4px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            transition: background 0.12s, border-color 0.12s;
        }
        .brgy-item.checked .brgy-checkbox { background: var(--blue); border-color: var(--blue); }
        .brgy-checkbox svg { width: 10px; height: 10px; color: var(--white); opacity: 0; transition: opacity 0.1s; }
        .brgy-item.checked .brgy-checkbox svg { opacity: 1; }
        .brgy-name { font-size: 13px; color: var(--gray-700); line-height: 1.3; }
        .brgy-item.checked .brgy-name { color: var(--blue-dark); font-weight: 600; }

        .brgy-dd-empty { padding: 18px; text-align: center; font-size: 12px; color: var(--gray-400); display: none; }
        .brgy-dd-empty.show { display: block; }

        .brgy-tags-wrap { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
        .brgy-tag {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--blue-pale); border: 1px solid #C7D9F5;
            color: var(--blue-dark); font-size: 11px; font-weight: 600;
            padding: 3px 8px 3px 10px; border-radius: 20px;
            transition: background 0.1s;
        }
        .brgy-tag:hover { background: #D6E6F8; }
        .brgy-tag-remove {
            cursor: pointer; opacity: 0.5; font-size: 14px; line-height: 1;
            width: 14px; height: 14px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.1s, background 0.1s;
        }
        .brgy-tag-remove:hover { opacity: 1; background: rgba(27,63,122,0.15); }
        .brgy-tags-more { font-size: 11px; color: var(--blue); font-weight: 700; align-self: center; cursor: pointer; }
        .brgy-tags-more:hover { text-decoration: underline; }

        .brgy-map-strip {
            margin-top: 10px;
            border: 1.5px solid var(--gray-200);
            border-radius: 6px;
            overflow: hidden;
            display: block;
            position: relative;
            isolation: isolate;
        }

        .map-strip-header {
            background: var(--blue-dark); border-bottom: 2px solid var(--yellow);
            padding: 8px 12px;
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
        }
        .map-strip-left { display: flex; align-items: center; gap: 8px; }
        .map-strip-title { font-size: 11px; font-weight: 700; color: var(--white); }
        .map-strip-hint { font-size: 10px; color: rgba(255,255,255,0.45); margin-top: 1px; }
        .map-strip-actions { display: flex; gap: 5px; }
        .map-strip-btn {
            padding: 4px 9px; font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.4px;
            border-radius: 4px; border: 1px solid rgba(255,255,255,0.2);
            cursor: pointer; font-family: 'Open Sans', sans-serif;
            background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);
            transition: background 0.12s; white-space: nowrap;
        }
        .map-strip-btn:hover { background: rgba(255,255,255,0.18); }
        .map-strip-btn.pin { background: rgba(245,197,24,0.15); border-color: rgba(245,197,24,0.4); color: var(--yellow); }
        .map-strip-btn.pin:hover { background: rgba(245,197,24,0.28); }
        .map-strip-btn.expand {
            background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);
            color: rgba(255,255,255,0.9);
        }
        .map-strip-btn.expand:hover { background: rgba(255,255,255,0.2); }

        #distMapMini { height: 240px; width: 100%; cursor: pointer; }

        .map-click-hint {
            position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%);
            background: rgba(18,45,90,0.82); color: #fff;
            font-size: 10px; font-weight: 600; padding: 5px 12px; border-radius: 20px;
            pointer-events: none; white-space: nowrap; letter-spacing: 0.3px;
            opacity: 0; transition: opacity 0.3s;
        }
        .map-click-hint.show { opacity: 1; }

        .map-pin-note {
            padding: 6px 12px; background: #FFFBEB;
            border-top: 1px solid #FDE68A;
            font-size: 11px; color: #92400E;
            display: flex; align-items: center; gap: 6px;
        }
        .map-pin-note.hidden { display: none; }

        #pinnedLocationDisplay {
            margin-top: 5px; font-size: 11px; color: var(--green-dark);
            display: none; align-items: center; gap: 5px;
        }
        #pinnedLocationDisplay.show { display: flex; }

        .map-modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(10,20,40,0.75);
            z-index: 10000;
            align-items: center; justify-content: center;
            backdrop-filter: blur(3px);
        }
        .map-modal-overlay.open { display: flex; animation: modalFadeIn 0.2s ease; }
        @keyframes modalFadeIn { from{opacity:0} to{opacity:1} }

        .map-modal {
            width: 94vw; max-width: 1100px;
            height: 88vh; max-height: 800px;
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            display: flex; flex-direction: column;
            box-shadow: 0 24px 80px rgba(0,0,0,0.5);
            animation: modalSlideUp 0.22s ease;
        }
        @keyframes modalSlideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }

        .map-modal-header {
            background: var(--blue-dark);
            border-bottom: 3px solid var(--yellow);
            padding: 12px 18px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            flex-shrink: 0;
        }
        .map-modal-title-wrap { display: flex; align-items: center; gap: 10px; }
        .map-modal-icon { width: 32px; height: 32px; background: rgba(255,255,255,0.1); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--yellow); }
        .map-modal-icon svg { width: 16px; height: 16px; }
        .map-modal-title { font-size: 14px; font-weight: 700; color: var(--white); }
        .map-modal-subtitle { font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 1px; }

        .map-modal-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .map-modal-btn {
            padding: 7px 14px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            border-radius: 5px; border: 1px solid rgba(255,255,255,0.2);
            cursor: pointer; font-family: 'Open Sans', sans-serif;
            background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.85);
            transition: background 0.15s; display: flex; align-items: center; gap: 6px;
        }
        .map-modal-btn:hover { background: rgba(255,255,255,0.18); }
        .map-modal-btn.pin-btn { background: rgba(245,197,24,0.15); border-color: rgba(245,197,24,0.4); color: var(--yellow); }
        .map-modal-btn.pin-btn:hover { background: rgba(245,197,24,0.3); }
        .map-modal-btn.close-btn { background: rgba(192,57,43,0.2); border-color: rgba(192,57,43,0.4); color: #FF8A80; }
        .map-modal-btn.close-btn:hover { background: rgba(192,57,43,0.35); }

        .map-modal-body { flex: 1; display: flex; overflow: hidden; }
        .map-modal-sidebar {
            width: 220px; flex-shrink: 0;
            background: var(--white); border-right: 1px solid var(--gray-200);
            display: flex; flex-direction: column; overflow: hidden;
        }
        .map-modal-sidebar-head {
            padding: 10px 12px; background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: var(--gray-600);
            display: flex; align-items: center; justify-content: space-between;
        }
        .map-modal-sidebar-count { background: var(--blue-pale); color: var(--blue); font-size: 10px; font-weight: 700; padding: 1px 8px; border-radius: 10px; }
        .map-modal-brgy-list { flex: 1; overflow-y: auto; padding: 4px 0; }
        .map-modal-brgy-list::-webkit-scrollbar { width: 4px; }
        .map-modal-brgy-list::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        .map-modal-brgy-item {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 12px; cursor: pointer; font-size: 12px;
            color: var(--gray-600); transition: background 0.1s;
            border-left: 3px solid transparent;
        }
        .map-modal-brgy-item:hover { background: var(--blue-pale); color: var(--blue); border-left-color: var(--blue-light); }
        .map-modal-brgy-item.active { background: var(--blue-pale); color: var(--blue-dark); font-weight: 700; border-left-color: var(--blue); }
        .map-modal-brgy-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--blue); flex-shrink: 0; }

        .map-modal-pin-bar {
            padding: 8px 16px; background: #FFFBEB; border-top: 1px solid #FDE68A;
            font-size: 11px; color: #92400E; display: flex; align-items: center; gap: 8px;
            flex-shrink: 0;
        }
        .map-modal-pin-bar.pinned { background: var(--green-pale); border-top-color: #86EFAC; color: var(--green-dark); }

        #distMapFull { flex: 1; }
        .map-modal-map-wrap { flex: 1; display: flex; flex-direction: column; position: relative; }

        .dms-input-wrap {
            margin-bottom: 8px;
            padding: 12px 14px;
            background: var(--blue-pale);
            border: 1.5px solid #C7D9F5;
            border-radius: 6px;
        }
        .dms-input-wrap-title {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--blue-dark); margin-bottom: 10px;
            display: flex; align-items: center; gap: 6px;
        }
        .dms-row {
            display: grid;
            grid-template-columns: 60px 1fr 8px 1fr 8px 1fr 8px 36px auto;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }
        .dms-row:last-of-type { margin-bottom: 10px; }
        .dms-axis-label {
            font-size: 11px; font-weight: 700;
            color: var(--blue-dark);
            white-space: nowrap;
        }
        .dms-field {
            display: flex; flex-direction: column; gap: 2px;
        }
        .dms-field-label {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--gray-400); text-align: center;
        }
        .dms-sep {
            font-size: 14px; font-weight: 700;
            color: var(--blue); text-align: center;
            line-height: 1; padding-top: 12px;
        }
        .dms-input {
            padding: 7px 6px !important;
            border: 1.5px solid var(--gray-200) !important;
            border-radius: 4px !important;
            font-size: 13px !important;
            font-family: 'Open Sans', sans-serif !important;
            color: var(--gray-800) !important;
            background: var(--white) !important;
            width: 100% !important;
            outline: none !important;
            text-align: center !important;
            transition: border-color 0.15s, box-shadow 0.15s !important;
        }
        .dms-input:focus {
            border-color: var(--blue-light) !important;
            box-shadow: 0 0 0 3px rgba(36,89,168,0.1) !important;
        }
        .dms-input.error {
            border-color: var(--red) !important;
            box-shadow: 0 0 0 3px rgba(192,57,43,0.1) !important;
        }
        .dms-dir-select {
            padding: 7px 4px !important;
            border: 1.5px solid var(--gray-200) !important;
            border-radius: 4px !important;
            font-size: 12px !important; font-weight: 700 !important;
            font-family: 'Open Sans', sans-serif !important;
            color: var(--blue-dark) !important;
            background: var(--white) !important;
            cursor: pointer !important;
            text-align: center !important;
            outline: none !important;
            transition: border-color 0.15s !important;
            width: 100% !important;
        }
        .dms-dir-select:focus { border-color: var(--blue-light) !important; }
        .dms-pin-btn {
            grid-column: span 1;
            padding: 8px 14px;
            background: var(--blue);
            color: var(--white);
            border: none; border-radius: 4px;
            font-family: 'Open Sans', sans-serif;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            cursor: pointer; white-space: nowrap;
            display: flex; align-items: center; gap: 5px;
            transition: background 0.15s;
            height: 34px; align-self: end;
        }
        .dms-pin-btn:hover:not(:disabled) { background: var(--blue-dark); }
        .dms-pin-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        .dms-result-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 4px;
            flex-wrap: wrap;
        }
        .dms-result-item {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px;
        }
        .dms-result-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--gray-400);
        }
        .dms-result-value {
            font-size: 12px; font-weight: 700;
            color: var(--blue-dark);
            font-variant-numeric: tabular-nums;
            font-family: monospace;
        }
        .dms-result-value.empty { color: var(--gray-400); font-weight: 400; font-family: 'Open Sans', sans-serif; }
        .dms-result-sep { color: var(--gray-200); font-size: 16px; }
        .dms-error-msg {
            font-size: 11px; color: var(--red);
            margin-top: 6px; display: none;
        }
        .dms-error-msg.show { display: block; }

        .relief-cat-picker { display: flex; flex-wrap: wrap; gap: 8px; }
        .rcp-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 18px; border: 2px solid var(--gray-200); border-radius: 8px;
            background: var(--white); cursor: pointer; font-family: 'Open Sans', sans-serif;
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
            user-select: none;
        }
        .rcp-btn:hover { border-color: #9DB8E8; background: var(--blue-pale); box-shadow: 0 1px 4px rgba(27,63,122,0.08); }
        .rcp-btn.active { border-color: var(--blue); background: var(--blue-pale); box-shadow: 0 0 0 3px rgba(36,89,168,0.12); }
        .rcp-btn.active::after { content: '✓'; font-size: 11px; font-weight: 700; color: var(--blue); margin-left: 4px; }
        .rcp-icon { font-size: 17px; line-height: 1; }
        .rcp-label { font-size: 13px; font-weight: 600; color: var(--gray-700); }
        .rcp-btn.active .rcp-label { color: var(--blue-dark); }

        .relief-items-empty {
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            padding: 36px 20px; text-align: center; color: var(--gray-400);
        }
        .relief-items-empty svg { opacity: 0.25; }
        .relief-items-empty p { font-size: 13px; line-height: 1.6; }
        .relief-items-empty strong { color: var(--gray-600); }

        .relief-panel { animation: rpFadeIn 0.2s ease; }
        @keyframes rpFadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        .rp-header {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px 12px;
            background: var(--gray-50); border: 1px solid var(--gray-200);
            border-radius: 8px 8px 0 0; border-bottom: none;
            margin-bottom: 0;
        }
        .rp-icon-badge {
            width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 19px;
        }
        .rp-icon-badge.dignity  { background: #FDF4FF; }
        .rp-icon-badge.firstaid { background: #FFF1F2; }
        .rp-icon-badge.food     { background: #FEFCE8; }
        .rp-icon-badge.hygiene  { background: #EFF6FF; }
        .rp-icon-badge.cash     { background: #F0FDF4; }
        .rp-header-text { flex: 1; min-width: 0; }
        .rp-title { font-size: 14px; font-weight: 700; color: var(--gray-800); display: block; }
        .rp-hint  { font-size: 11px; color: var(--gray-400); margin-top: 1px; display: block; }
        .rp-select-all-btn {
            font-family: 'Open Sans', sans-serif; font-size: 11px; font-weight: 600;
            color: var(--blue-light); background: var(--blue-pale);
            border: 1px solid #C7D9F5; border-radius: 4px;
            padding: 5px 12px; cursor: pointer; flex-shrink: 0;
            transition: background 0.12s, color 0.12s;
        }
        .rp-select-all-btn:hover { background: #D6E6F8; color: var(--blue-dark); }

        .rp-table {
            border: 1px solid var(--gray-200);
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        .rp-table-head {
            display: grid;
            grid-template-columns: 1fr auto;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 6px 14px 6px 46px;
            gap: 12px;
        }
        .rp-th {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--gray-400);
        }
        .rp-th.qty-head { width: 130px; text-align: center; }

        .rp-item {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 12px;
            padding: 9px 14px;
            cursor: pointer;
            user-select: none;
            background: var(--white);
            border-bottom: 1px solid var(--gray-100);
            transition: background 0.1s;
            position: relative;
        }
        .rp-item:last-child { border-bottom: none; }
        .rp-item:hover { background: #F4F8FF; }
        .rp-item:has(.rp-cb-native:checked) { background: var(--blue-pale); }

        .rp-item-left {
            display: flex; align-items: center; gap: 10px;
            min-width: 0;
        }

        .rp-cb-native { position: absolute; opacity: 0; width: 0; height: 0; }
        .rp-box {
            width: 18px; height: 18px; flex-shrink: 0;
            border: 2px solid var(--gray-300); border-radius: 5px;
            display: flex; align-items: center; justify-content: center;
            background: var(--white);
            transition: background 0.12s, border-color 0.12s;
        }
        .rp-item:has(.rp-cb-native:checked) .rp-box { background: var(--blue); border-color: var(--blue); }
        .rp-box svg { width: 10px; height: 10px; color: var(--white); opacity: 0; transition: opacity 0.1s; }
        .rp-item:has(.rp-cb-native:checked) .rp-box svg { opacity: 1; }
        .rp-name { font-size: 13px; color: var(--gray-700); line-height: 1.4; }
        .rp-item:has(.rp-cb-native:checked) .rp-name { color: var(--blue-dark); font-weight: 600; }

        .rp-qty-wrap {
            display: flex;
            align-items: center;
            width: 130px;
            flex-shrink: 0;
            border: 1px solid var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            background: var(--white);
            transition: border-color 0.15s, box-shadow 0.15s;
            opacity: 0.45;
            pointer-events: none;
        }
        .rp-item:has(.rp-cb-native:checked) .rp-qty-wrap {
            opacity: 1;
            pointer-events: auto;
            border-color: var(--blue-light);
            box-shadow: 0 0 0 2px rgba(36,89,168,0.09);
        }
        .rp-qty-input {
            width: 60px;
            flex: 1;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 7px 8px;
            font-size: 13px;
            font-family: 'Open Sans', sans-serif;
            color: var(--gray-800);
            text-align: right;
            background: transparent;
        }
        .rp-qty-unit {
            padding: 7px 9px 7px 6px;
            font-size: 11px; font-weight: 700;
            color: var(--gray-500);
            background: var(--gray-50);
            border-left: 1px solid var(--gray-200);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .cash-input-wrap {
            display: flex; align-items: center;
            border: 1px solid var(--gray-200); border-radius: 4px;
            overflow: hidden; background: var(--white);
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .cash-input-wrap:focus-within { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(36,89,168,0.1); }
        .cash-prefix {
            padding: 9px 13px; background: var(--gray-50);
            border-right: 1px solid var(--gray-200);
            font-size: 14px; font-weight: 700; color: var(--gray-600); flex-shrink: 0;
        }
        .cash-input-wrap input {
            flex: 1; border: none; outline: none; padding: 9px 12px;
            font-size: 13px; font-family: 'Open Sans', sans-serif;
            color: var(--gray-800); box-shadow: none !important;
        }

        .status-upcoming-badge {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 18px; background: var(--blue-pale);
            border: 2px solid var(--blue); border-radius: 6px;
        }
        .status-upcoming-badge svg { color: var(--blue); flex-shrink: 0; margin-top: 2px; }
        .status-upcoming-label {
            display: block; font-size: 13px; font-weight: 700;
            color: var(--blue); text-transform: uppercase; letter-spacing: 0.5px;
        }
        .status-upcoming-sub { display: block; font-size: 11px; color: var(--gray-600); margin-top: 2px; line-height: 1.5; }

        .submit-bar { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--green); padding: 16px 24px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .btn-submit { display: inline-flex; align-items: center; gap: 7px; padding: 11px 24px; background: var(--green); color: var(--white); border: none; border-radius: 4px; cursor: pointer; font-family: 'Open Sans', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.15s; }
        .btn-submit:hover { background: var(--green-dark); }
        .btn-submit svg { width: 15px; height: 15px; }
        .btn-cancel { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 4px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: none; transition: background 0.15s; }
        .btn-cancel:hover { background: var(--gray-100); }

        .info-panel { display: flex; flex-direction: column; gap: 16px; }
        .info-card { background: var(--white); border: 1px solid var(--gray-200); }
        .info-card-header { padding: 11px 16px; background: var(--gray-50); border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; gap: 8px; }
        .info-card-title { font-size: 12px; font-weight: 600; color: var(--blue-dark); }
        .info-card-body { padding: 16px; }

        .workflow-steps { display: flex; flex-direction: column; gap: 0; }
        .workflow-step { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--gray-100); }
        .workflow-step:last-child { border-bottom: none; padding-bottom: 0; }
        .workflow-step:first-child { padding-top: 0; }
        .step-num { width: 22px; height: 22px; border-radius: 50%; background: var(--blue); color: var(--white); font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .step-text { font-size: 12px; color: var(--gray-600); line-height: 1.5; }
        .step-text strong { color: var(--gray-800); display: block; margin-bottom: 2px; }

        .status-legend { display: flex; flex-direction: column; gap: 8px; }
        .status-legend-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 3px; font-size: 12px; }
        .status-legend-item.upcoming  { background: var(--blue-pale);  color: var(--blue); }
        .status-legend-item.ongoing   { background: var(--green-pale); color: var(--green-dark); }
        .status-legend-item.completed { background: var(--gray-100);   color: var(--gray-600); }
        .status-legend-item svg { width: 14px; height: 14px; flex-shrink: 0; }
        .status-legend-item div strong { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-legend-item div span { font-size: 11px; opacity: 0.8; }

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

        @media (max-width: 1100px) { .create-layout { grid-template-columns: 1fr 280px; } }
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
            .create-layout { grid-template-columns: 1fr; }
            .info-panel { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
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
            .page-titlebar { flex-direction: column; align-items: flex-start; }
            .page-h1 { font-size: 18px; }
            .back-btn { align-self: flex-start; }
            .form-row.cols-2 { grid-template-columns: 1fr; }
            .form-section-body { padding: 16px; }
            .info-panel { grid-template-columns: 1fr; }
            .submit-bar { padding: 14px 16px; }
            .btn-submit { font-size: 12px; padding: 10px 18px; width: 100%; justify-content: center; }
            .btn-cancel { width: 100%; justify-content: center; }
            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
            .rp-qty-wrap { width: 110px; }
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
        <button class="hamburger" id="hamburgerBtn" aria-label="Open navigation">
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
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Full Access</div>
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
        <a href="{{ route('admin.events.quick-create') }}" class="nav-item active" onclick="closeSidebar()">
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
        <a href="{{ route('admin.traillog.trail') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Trail Logs
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
                    <a href="{{ route('admin.dashboard') }}">Admin</a> /
                    <a href="{{ route('admin.distribution.logs') }}">Distribution Events</a> /
                    <span>Create New Event</span>
                </div>
                <div class="page-h1">Create Distribution Event</div>
                <div class="page-sub">Configure a new ayuda / relief distribution event for QR scan tracking</div>
            </div>
            <div class="page-date">
                <span>Today</span>
                <strong id="main-date">—</strong>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.events.quick-store') }}" id="eventForm">
        @csrf

        @if ($errors->any())
        <div style="background:#C0392B;color:#fff;padding:14px 20px;margin-bottom:16px;border-radius:4px;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="create-layout">

            <!-- LEFT: Form -->
            <div>

                {{-- Event Details --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Event Details</div>
                    </div>
                    <div class="form-section-body">

                        <div class="form-row cols-1">
                            <div class="form-group">
                                <label class="form-label">Event Name <span class="req">*</span></label>
                                <input type="text" name="event_name"
                                    value="{{ old('event_name') }}"
                                    required
                                    placeholder="e.g. Typhoon Carina Relief Round 1">
                                <div class="form-hint">Use a clear, descriptive name that identifies the calamity or program and the round number.</div>
                            </div>
                        </div>

                        <div class="form-row cols-1">
                            <div class="form-group">
                                <label class="form-label">Relief Type <span class="req">*</span></label>
                                <div id="relief_type_hidden_inputs"></div>
                                <div class="relief-cat-picker" id="reliefCatPicker">
                                    <button type="button" class="rcp-btn" id="rcp-cash"     onclick="toggleCategory('Cash Aid',    'rcp-cash',    'items-cash')">    <span class="rcp-icon">💵</span><span class="rcp-label">Cash Aid</span></button>
                                    <button type="button" class="rcp-btn" id="rcp-dignity"  onclick="toggleCategory('Dignity Kit', 'rcp-dignity',  'items-dignity')"> <span class="rcp-icon">🎀</span><span class="rcp-label">Dignity Kit</span></button>
                                    <button type="button" class="rcp-btn" id="rcp-firstaid" onclick="toggleCategory('First Aid Kit','rcp-firstaid','items-firstaid')"><span class="rcp-icon">🩹</span><span class="rcp-label">First Aid Kit</span></button>
                                    <button type="button" class="rcp-btn" id="rcp-food"     onclick="toggleCategory('Food Pack',   'rcp-food',    'items-food')">    <span class="rcp-icon">🍱</span><span class="rcp-label">Food Pack</span></button>
                                    <button type="button" class="rcp-btn" id="rcp-hygiene"  onclick="toggleCategory('Hygiene Kit', 'rcp-hygiene',  'items-hygiene')"> <span class="rcp-icon">🧴</span><span class="rcp-label">Hygiene Kit</span></button>
                                </div>
                                <div class="form-hint">Choose one or more categories of goods being distributed in this event.</div>
                            </div>
                        </div>

                    </div>
                </div>


                {{-- Scan Mode --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Scan Mode</div>
                    </div>
                    <div class="form-section-body">
                        <div class="form-row cols-1">
                            <div class="form-group">
                                <label class="form-label">Scan Mode <span class="req">*</span></label>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:4px;" id="scanModePicker">

                                    <div id="sm-household" onclick="selectScanMode('household')" style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:2px solid var(--blue);border-radius:6px;cursor:pointer;background:var(--blue-pale);transition:border-color 0.15s,background 0.15s;user-select:none;">
                                        <div id="sm-household-icon" style="width:36px;height:36px;border-radius:8px;background:var(--blue);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                                        </div>
                                        <div>
                                            <div id="sm-household-title" style="font-size:13px;font-weight:700;color:var(--blue-dark);margin-bottom:3px;">Per Household</div>
                                            <div style="font-size:11px;color:var(--gray-600);line-height:1.5;">Accepts the <strong>household QR card</strong>. One release per household. Best for general relief distribution.</div>
                                        </div>
                                    </div>

                                    <div id="sm-family_head" onclick="selectScanMode('family_head')" style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:2px solid var(--gray-200);border-radius:6px;cursor:pointer;background:var(--white);transition:border-color 0.15s,background 0.15s;user-select:none;">
                                        <div id="sm-family_head-icon" style="width:36px;height:36px;border-radius:8px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gray-600)" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
                                        </div>
                                        <div>
                                            <div id="sm-family_head-title" style="font-size:13px;font-weight:700;color:var(--gray-800);margin-bottom:3px;">Per Family Head</div>
                                            <div style="font-size:11px;color:var(--gray-600);line-height:1.5;">Accepts the <strong>family head personal QR</strong>. One release per family head. Best for targeted beneficiary programs.</div>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-hint" id="scanModeHint">
                                    <strong>Per Household:</strong> Staff scans the household QR card — only one claim allowed per household per event.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Relief Items --}}
                <div class="form-section" id="reliefItemsSection">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Relief Items</div>
                        <span id="reliefItemsBadge" style="display:none;margin-left:auto;font-size:11px;font-weight:700;color:var(--blue);background:var(--blue-pale);border:1px solid #C7D9F5;padding:2px 10px;border-radius:10px;"></span>
                    </div>
                    <div class="form-section-body">

                        <div class="relief-items-empty" id="reliefItemsEmpty">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
                            <p>Select one or more <strong>Relief Types</strong> above to see available items.</p>
                        </div>

                        {{-- Dignity Kit --}}
                        <div class="relief-panel" id="items-dignity" style="display:none;">
                            <div class="rp-header">
                                <span class="rp-icon-badge dignity">🎀</span>
                                <div class="rp-header-text">
                                    <span class="rp-title">Dignity Kit</span>
                                    <span class="rp-hint">Check items included and enter quantity per household</span>
                                </div>
                                <button type="button" class="rp-select-all-btn" onclick="selectAllItems('items-dignity')">Select All</button>
                            </div>
                            <div class="rp-table">
                                <div class="rp-table-head">
                                    <span class="rp-th">Item</span>
                                    <span class="rp-th qty-head">Qty / Amount</span>
                                </div>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[feminine_hygiene_wash][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Feminine Hygiene Wash</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[feminine_hygiene_wash][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">btl</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[sanitary_pads][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Sanitary Pads / Napkins</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[sanitary_pads][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pack</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[tissue_wipes][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Tissue / Wipes</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[tissue_wipes][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pack</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[underwear][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Underwear</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[underwear][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                            </div>
                        </div>

                        {{-- First Aid Kit --}}
                        <div class="relief-panel" id="items-firstaid" style="display:none;">
                            <div class="rp-header">
                                <span class="rp-icon-badge firstaid">🩹</span>
                                <div class="rp-header-text">
                                    <span class="rp-title">First Aid Kit</span>
                                    <span class="rp-hint">Check items included and enter quantity per household</span>
                                </div>
                                <button type="button" class="rp-select-all-btn" onclick="selectAllItems('items-firstaid')">Select All</button>
                            </div>
                            <div class="rp-table">
                                <div class="rp-table-head">
                                    <span class="rp-th">Item</span>
                                    <span class="rp-th qty-head">Qty / Amount</span>
                                </div>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[alcohol][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Alcohol</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[alcohol][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">btl</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[bandaid][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Band-aid</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[bandaid][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">box</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[bandage][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Bandage</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[bandage][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">roll</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[betadine][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Betadine</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[betadine][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">btl</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[elastic_bandage][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Elastic Bandage</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[elastic_bandage][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">roll</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[emergency_medicine][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Emergency Medicine / CAMPOLAS</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[emergency_medicine][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[gauze_pad][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Gauze Pad</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[gauze_pad][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[gauze_roll][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Gauze Roll</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[gauze_roll][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">roll</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[medical_tape][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Medical Tape</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[medical_tape][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">roll</span></div>
                                </label>
                            </div>
                        </div>

                        {{-- Food Pack --}}
                        <div class="relief-panel" id="items-food" style="display:none;">
                            <div class="rp-header">
                                <span class="rp-icon-badge food">🍱</span>
                                <div class="rp-header-text">
                                    <span class="rp-title">Food Pack</span>
                                    <span class="rp-hint">Check items included and enter quantity per household</span>
                                </div>
                                <button type="button" class="rp-select-all-btn" onclick="selectAllItems('items-food')">Select All</button>
                            </div>
                            <div class="rp-table">
                                <div class="rp-table-head">
                                    <span class="rp-th">Item</span>
                                    <span class="rp-th qty-head">Qty / Amount</span>
                                </div>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[canned_goods][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Canned Goods (Corned Beef / Sardines)</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[canned_goods][qty]" class="rp-qty-input" min="1" step="1" placeholder="2" onclick="event.stopPropagation()"><span class="rp-qty-unit">cans</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[coffee][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Coffee</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[coffee][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pack</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[instant_noodles][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Instant Noodles</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[instant_noodles][qty]" class="rp-qty-input" min="1" step="1" placeholder="3" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[rice][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Rice</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[rice][qty]" class="rp-qty-input" min="0.5" step="0.5" placeholder="5" onclick="event.stopPropagation()"><span class="rp-qty-unit">kg</span></div>
                                </label>
                            </div>
                        </div>

                        {{-- Hygiene Kit --}}
                        <div class="relief-panel" id="items-hygiene" style="display:none;">
                            <div class="rp-header">
                                <span class="rp-icon-badge hygiene">🧴</span>
                                <div class="rp-header-text">
                                    <span class="rp-title">Hygiene Kit</span>
                                    <span class="rp-hint">Check items included and enter quantity per household</span>
                                </div>
                                <button type="button" class="rp-select-all-btn" onclick="selectAllItems('items-hygiene')">Select All</button>
                            </div>
                            <div class="rp-table">
                                <div class="rp-table-head">
                                    <span class="rp-th">Item</span>
                                    <span class="rp-th qty-head">Qty / Amount</span>
                                </div>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[bar_soap][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Bar Soap</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[bar_soap][qty]" class="rp-qty-input" min="1" step="1" placeholder="2" onclick="event.stopPropagation()"><span class="rp-qty-unit">bars</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[bucket][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Bucket</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[bucket][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[deodorant][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Deodorant</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[deodorant][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[dipper][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Dipper (Tabo)</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[dipper][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[shampoo][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Shampoo</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[shampoo][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">btl</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[toothbrush][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Toothbrush</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[toothbrush][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[toothpaste][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Toothpaste</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[toothpaste][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">tube</span></div>
                                </label>
                                <label class="rp-item">
                                    <div class="rp-item-left"><input type="checkbox" name="items[towel][included]" value="1" class="rp-cb-native" onchange="onItemChange()"><span class="rp-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span><span class="rp-name">Towel / Face Towel</span></div>
                                    <div class="rp-qty-wrap"><input type="number" name="items[towel][qty]" class="rp-qty-input" min="1" step="1" placeholder="1" onclick="event.stopPropagation()"><span class="rp-qty-unit">pcs</span></div>
                                </label>
                            </div>
                        </div>

                        {{-- Cash Aid --}}
                        <div class="relief-panel" id="items-cash" style="display:none;">
                            <div class="rp-header">
                                <span class="rp-icon-badge cash">💵</span>
                                <div class="rp-header-text">
                                    <span class="rp-title">Cash Aid</span>
                                    <span class="rp-hint">Enter the monetary amount per household</span>
                                </div>
                            </div>
                            <div style="padding:4px 0 8px;">
                                <div class="form-group" style="max-width:300px;">
                                    <label class="form-label">Amount per Household <span class="req">*</span></label>
                                    <div class="cash-input-wrap">
                                        <span class="cash-prefix">₱</span>
                                        <input type="number" name="cash_amount" id="cashAmountInput"
                                            min="1" step="0.01" placeholder="0.00"
                                            oninput="onItemChange()">
                                    </div>
                                    <div class="form-hint">Cash amount each household will receive.</div>
                                </div>
                            </div>
                        </div>

                        <div id="goodsDetailWrap" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--gray-100);">
                            <div class="form-group">
                                <label class="form-label">Goods Detail / Notes <span class="opt">(auto-filled — editable)</span></label>
                                <textarea name="goods_detail" id="goodsDetailField" placeholder="e.g. 5kg rice, 2 canned goods, cooking oil — per household">{{ old('goods_detail') }}</textarea>
                                <div class="form-hint">Auto-filled from checked items above. You may edit before submitting.</div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Event Status & Target Barangay --}}
                <div class="form-section">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Event Status &amp; Target Barangay</div>
                    </div>
                    <div class="form-section-body">
                        <div class="form-row cols-2" style="margin-top:20px;">
                            <div class="form-group">
                                <label class="form-label">Planned Start Date &amp; Time <span class="req">*</span></label>
                                <input type="datetime-local" name="started_at" value="{{ old('started_at') }}" required>
                                <div class="form-hint">When do you plan to start this distribution event?</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Planned End Date &amp; Time <span class="req">*</span></label>
                                <input type="datetime-local" name="ended_at" value="{{ old('ended_at') }}" required>
                                <div class="form-hint">When do you plan to end this distribution event?</div>
                            </div>
                        </div>

                        <div class="form-row cols-1">
                            <div class="form-group">
                                <label class="form-label">Event Date <span class="opt">(optional)</span></label>
                                <input type="date" name="event_date" value="{{ old('event_date', date('Y-m-d')) }}">
                                <div class="form-hint">Scheduled date of the distribution activity.</div>
                            </div>
                        </div>

                        <div class="form-row cols-1">
                            <div class="form-group">
                                <label class="form-label">Target Barangay <span class="req">*</span> <span class="opt">(select one or more)</span></label>
                                <div id="brgyHiddenInputs"></div>
                                <input type="hidden" name="distribution_lat"      id="distLat">
                                <input type="hidden" name="distribution_lng"      id="distLng">
                                <input type="hidden" name="distribution_location" id="distLocName">
                                <input type="hidden" name="distribution_dms"      id="distDms">

                                <!-- Improved Dropdown Trigger -->
                                <div class="brgy-field-wrap" id="brgyFieldWrap">
                                    <button type="button" class="brgy-trigger" id="brgyTrigger" onclick="toggleBrgyDropdown()">
                                        <div class="brgy-trigger-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                                        </div>
                                        <div class="brgy-trigger-body">
                                            <span class="brgy-trigger-label">Target Barangay</span>
                                            <span class="brgy-trigger-value placeholder" id="brgyTriggerValue">Click to select barangays…</span>
                                        </div>
                                        <div class="brgy-trigger-right">
                                            <span class="brgy-count-badge" id="brgyCountBadge">0</span>
                                            <svg class="brgy-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                        </div>
                                    </button>

                                    <!-- Dropdown Panel -->
                                    <div class="brgy-dropdown" id="brgyDropdown">
                                        <div class="brgy-dd-head">
                                            <span class="brgy-dd-head-title">Select Barangays</span>
                                            <span class="brgy-dd-head-count" id="brgyDdCount">0 / 30 selected</span>
                                        </div>
                                        <div class="brgy-dd-toolbar">
                                            <div class="brgy-dd-search-wrap">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                                <input type="text" class="brgy-dd-search" id="brgySearch" placeholder="Filter barangays…" autocomplete="off">
                                            </div>
                                            <div class="brgy-dd-btns">
                                                <button type="button" class="brgy-dd-btn select-all" onclick="selectAllBarangays()">All</button>
                                                <button type="button" class="brgy-dd-btn clear-all" onclick="clearAllBarangays()">Clear</button>
                                            </div>
                                        </div>
                                        <div class="brgy-dd-list" id="brgyList">
                                            <div class="brgy-item all-row" id="allBarangaysRow" onclick="toggleAllBarangays(this)">
                                                <div class="brgy-checkbox"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                                                <span class="brgy-name">All Barangays (Municipality-Wide)</span>
                                            </div>
                                            <div id="brgyItems"></div>
                                            <div class="brgy-dd-empty" id="brgyDdEmpty">No barangays match your search.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Tags -->
                                <div class="brgy-tags-wrap" id="brgyTagsWrap"></div>

                                <!-- Pinned location display -->
                                <div id="pinnedLocationDisplay">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span id="pinnedLocationText">—</span>
                                </div>

                                <!-- DMS Coordinate Input -->
                                <div class="dms-input-wrap" id="dmsInputWrap">
                                    <div class="dms-input-wrap-title">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Enter Coordinates (Degrees° Minutes' Seconds")
                                    </div>

                                    {{-- Latitude Row --}}
                                    <div class="dms-row">
                                        <span class="dms-axis-label">Latitude</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Degrees °</span>
                                            <input type="number" class="dms-input" id="latDeg" min="0" max="90" step="1" placeholder="14" oninput="onDmsInput()">
                                        </div>
                                        <span class="dms-sep">°</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Minutes '</span>
                                            <input type="number" class="dms-input" id="latMin" min="0" max="59" step="1" placeholder="19" oninput="onDmsInput()">
                                        </div>
                                        <span class="dms-sep">'</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Seconds "</span>
                                            <input type="number" class="dms-input" id="latSec" min="0" max="59.9" step="0.1" placeholder="45.8" oninput="onDmsInput()">
                                        </div>
                                        <span class="dms-sep">"</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Dir</span>
                                            <select class="dms-dir-select" id="latDir" onchange="onDmsInput()">
                                                <option value="N">N</option>
                                                <option value="S">S</option>
                                            </select>
                                        </div>
                                        <button type="button" class="dms-pin-btn" id="dmsPinBtn" onclick="pinFromDms()" disabled>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            Pin
                                        </button>
                                    </div>

                                    {{-- Longitude Row --}}
                                    <div class="dms-row">
                                        <span class="dms-axis-label">Longitude</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Degrees °</span>
                                            <input type="number" class="dms-input" id="lngDeg" min="0" max="180" step="1" placeholder="120" oninput="onDmsInput()">
                                        </div>
                                        <span class="dms-sep">°</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Minutes '</span>
                                            <input type="number" class="dms-input" id="lngMin" min="0" max="59" step="1" placeholder="45" oninput="onDmsInput()">
                                        </div>
                                        <span class="dms-sep">'</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Seconds "</span>
                                            <input type="number" class="dms-input" id="lngSec" min="0" max="59.9" step="0.1" placeholder="41.0" oninput="onDmsInput()">
                                        </div>
                                        <span class="dms-sep">"</span>
                                        <div class="dms-field">
                                            <span class="dms-field-label">Dir</span>
                                            <select class="dms-dir-select" id="lngDir" onchange="onDmsInput()">
                                                <option value="E">E</option>
                                                <option value="W">W</option>
                                            </select>
                                        </div>
                                        <div></div>
                                    </div>

                                    {{-- Live conversion result --}}
                                    <div class="dms-result-row">
                                        <div class="dms-result-item">
                                            <span class="dms-result-label">Lat DD</span>
                                            <span class="dms-result-value empty" id="dmsResultLat">—</span>
                                        </div>
                                        <span class="dms-result-sep">·</span>
                                        <div class="dms-result-item">
                                            <span class="dms-result-label">Lng DD</span>
                                            <span class="dms-result-value empty" id="dmsResultLng">—</span>
                                        </div>
                                        <span class="dms-result-sep">·</span>
                                        <div class="dms-result-item" style="flex:1;">
                                            <span class="dms-result-label">DMS</span>
                                            <span class="dms-result-value empty" id="dmsResultFull" style="font-family:'Open Sans',sans-serif;font-size:11px;">Fill in fields above</span>
                                        </div>
                                    </div>
                                    <div class="dms-error-msg" id="dmsErrorMsg">⚠ Invalid coordinates. Lat must be 0–90, Lng 0–180, minutes 0–59, seconds 0–59.9.</div>
                                </div>

                                <!-- Mini Map Strip -->
                                <div class="brgy-map-strip" id="distMapStrip">
                                    <div class="map-strip-header">
                                        <div class="map-strip-left">
                                            <div>
                                                <div class="map-strip-title">📍 Distribution Map</div>
                                                <div class="map-strip-hint">Click map to expand · Set pin for distribution point</div>
                                            </div>
                                        </div>
                                        <div class="map-strip-actions">
                                            <button type="button" class="map-strip-btn pin" id="pinStripBtn" onclick="togglePinMode()">📍 Pin</button>
                                            <button type="button" class="map-strip-btn" onclick="clearPin()">✕ Clear</button>
                                            <button type="button" class="map-strip-btn expand" onclick="openMapModal()">⤢ Expand</button>
                                        </div>
                                    </div>
                                    <div id="distMapMini" title="Click to expand to fullscreen"></div>
                                    <div class="map-click-hint" id="mapClickHint">Click map to expand fullscreen</div>
                                    <div class="map-pin-note" id="pinNote">📍 Enable "Pin" mode then click the map to set your distribution point.</div>
                                </div>

                                <div class="form-hint">Select one or more barangays — the map will appear below. Click "Expand" for fullscreen view. Use "Pin" to mark the exact distribution point.</div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit -->
                <input type="hidden" name="status" value="upcoming">
                <input type="hidden" name="scan_mode" id="scanModeInput" value="household">
                <div class="submit-bar">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Create Distribution Event
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn-cancel">Cancel</a>
                </div>

            </div>


            <!-- RIGHT: Info Panel -->
            <div class="info-panel">

                <div class="info-card">
                    <div class="info-card-header">
                        <div class="ca-dot"></div>
                        <div class="info-card-title">Distribution Workflow</div>
                    </div>
                    <div class="info-card-body">
                        <div class="workflow-steps">
                            <div class="workflow-step">
                                <div class="step-num">1</div>
                                <div class="step-text"><strong>Create Event</strong>Set event name, relief type, date, and target barangays.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">2</div>
                                <div class="step-text"><strong>Set to Ongoing</strong>Distribution Staff can only scan QR codes on active ongoing events.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">3</div>
                                <div class="step-text"><strong>Staff Scan QR Cards</strong>System validates serial code, checks for duplicates, and logs receipt.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">4</div>
                                <div class="step-text"><strong>Duplicate Alert</strong>If a household already received goods, a red alert blocks re-release.</div>
                            </div>
                            <div class="workflow-step">
                                <div class="step-num">5</div>
                                <div class="step-text"><strong>Complete &amp; Export</strong>Mark event as completed. Export to PDF or Excel for DSWD submission.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header">
                        <div class="ca-dot"></div>
                        <div class="info-card-title">Event Status Reference</div>
                    </div>
                    <div class="info-card-body">
                        <div class="status-legend">
                            <div class="status-legend-item upcoming">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div><strong>Upcoming</strong><span>Scheduled. No scanning yet.</span></div>
                            </div>
                            <div class="status-legend-item ongoing">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                <div><strong>Ongoing</strong><span>Active — staff can scan and log.</span></div>
                            </div>
                            <div class="status-legend-item completed">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <div><strong>Completed</strong><span>Closed. Logs locked, export ready.</span></div>
                            </div>
                            <div class="status-legend-item" style="background:var(--red-pale);color:var(--red);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                <div><strong>Cancelled</strong><span>Terminated. Reason required.</span></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        </form>

    </main>


    <!-- FULLSCREEN MAP MODAL -->
    <div class="map-modal-overlay" id="mapModalOverlay">
        <div class="map-modal" id="mapModal">
            <div class="map-modal-header">
                <div class="map-modal-title-wrap">
                    <div class="map-modal-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <div class="map-modal-title">Distribution Point Map — Naic, Cavite</div>
                        <div class="map-modal-subtitle" id="modalSubtitle">Select a barangay from the list to navigate · Click the map to pin distribution point</div>
                    </div>
                </div>
                <div class="map-modal-actions">
                    <button type="button" class="map-modal-btn pin-btn" id="modalPinBtn" onclick="togglePinMode()">
                        📍 Pin Location
                    </button>
                    <button type="button" class="map-modal-btn" onclick="clearPin()">✕ Clear Pin</button>
                    <button type="button" class="map-modal-btn close-btn" onclick="closeMapModal()">✕ Close</button>
                </div>
            </div>
            <div class="map-modal-body">
                <div class="map-modal-sidebar">
                    <div class="map-modal-sidebar-head">Barangays <span class="map-modal-sidebar-count" id="modalBrgyCount">0</span></div>
                    <div class="map-modal-brgy-list" id="modalBrgyList"></div>
                </div>
                <div class="map-modal-map-wrap">
                    <div id="distMapFull"></div>
                    <div class="map-modal-pin-bar" id="modalPinBar">
                        📍 Click <strong>"Pin Location"</strong> then click anywhere on the map to mark the distribution point. You can drag the pin to adjust.
                    </div>
                </div>
            </div>
        </div>
    </div>

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

<script src="{{ asset('js/ph-locations.js') }}"></script>
<script>

    /* ══════════════════════════════════════════════
       DMS HELPER FUNCTIONS
       Defined first so they are available to all
       code below (avoids "not defined" errors).
    ══════════════════════════════════════════════ */

    /**
     * Convert DMS components + direction to decimal degrees.
     */
    function dmsToDecimal(deg, min, sec, dir) {
        const d  = parseFloat(deg) || 0;
        const m  = parseFloat(min) || 0;
        const s  = parseFloat(sec) || 0;
        let   dd = d + (m / 60) + (s / 3600);
        if (dir === 'S' || dir === 'W') dd = -dd;
        return dd;
    }

    /**
     * Convert a decimal-degree value to { deg, min, sec } object.
     */
    function decimalToDms(decimal) {
        const abs      = Math.abs(decimal);
        const deg      = Math.floor(abs);
        const minFloat = (abs - deg) * 60;
        const min      = Math.floor(minFloat);
        const sec      = parseFloat(((minFloat - min) * 60).toFixed(1));
        return { deg, min, sec };
    }

    /**
     * Format a DMS string from decimal lat/lng.
     * e.g.  14°19'45.80"N, 120°45'41.00"E
     */
    function decimalToDmsString(lat, lng) {
        const la     = decimalToDms(lat);
        const lo     = decimalToDms(lng);
        const latDir = lat >= 0 ? 'N' : 'S';
        const lngDir = lng >= 0 ? 'E' : 'W';
        return `${la.deg}°${la.min}'${la.sec}"${latDir}, ${lo.deg}°${lo.min}'${lo.sec}"${lngDir}`;
    }

    /**
     * Sync decimal degrees back into the on-screen DMS input fields
     * and refresh the live-preview row.
     */
    function syncDmsFields(lat, lng) {
        const la = decimalToDms(lat);
        const lo = decimalToDms(lng);

        document.getElementById('latDeg').value = la.deg;
        document.getElementById('latMin').value = la.min;
        document.getElementById('latSec').value = la.sec;
        document.getElementById('latDir').value = lat >= 0 ? 'N' : 'S';

        document.getElementById('lngDeg').value = lo.deg;
        document.getElementById('lngMin').value = lo.min;
        document.getElementById('lngSec').value = lo.sec;
        document.getElementById('lngDir').value = lng >= 0 ? 'E' : 'W';

        onDmsInput(); // refresh preview row
    }

    /* ── Clock ── */
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now    = new Date();
        const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        document.getElementById('main-date').textContent = days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();

    /* ── Sidebar ── */
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
    document.addEventListener('keydown', e => { if(e.key === 'Escape') closeSidebar(); });

    /* ══════════════════════════════════════════════
       RELIEF TYPE CATEGORY PICKER — MULTI-SELECT
    ══════════════════════════════════════════════ */

    const CAT_PANELS = {
        'rcp-cash':     { panelId: 'items-cash',     type: 'Cash Aid' },
        'rcp-dignity':  { panelId: 'items-dignity',  type: 'Dignity Kit' },
        'rcp-firstaid': { panelId: 'items-firstaid', type: 'First Aid Kit' },
        'rcp-food':     { panelId: 'items-food',     type: 'Food Pack' },
        'rcp-hygiene':  { panelId: 'items-hygiene',  type: 'Hygiene Kit' },
    };

    const activeCats = new Set();

    function toggleCategory(type, btnId, panelId) {
        const btn   = document.getElementById(btnId);
        const panel = document.getElementById(panelId);

        if (activeCats.has(btnId)) {
            activeCats.delete(btnId);
            btn.classList.remove('active');
            panel.style.display = 'none';
            panel.querySelectorAll('.rp-cb-native').forEach(cb => cb.checked = false);
            if (btnId === 'rcp-cash') {
                const ca = document.getElementById('cashAmountInput');
                if (ca) ca.value = '';
            }
        } else {
            activeCats.add(btnId);
            btn.classList.add('active');
            panel.style.display = '';
        }

        const emptyEl = document.getElementById('reliefItemsEmpty');
        if (emptyEl) emptyEl.style.display = activeCats.size === 0 ? '' : 'none';

        renderReliefTypeInputs();
        updateGoodsDetail();
        updateBadge();
        updateSelectAllBtn(panelId);
    }

    function renderReliefTypeInputs() {
        const container = document.getElementById('relief_type_hidden_inputs');
        container.innerHTML = '';
        activeCats.forEach(btnId => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'relief_type[]';
            input.value = CAT_PANELS[btnId].type;
            container.appendChild(input);
        });
    }

    function onItemChange(e) {
        updateGoodsDetail();
        updateBadge();
        if (e && e.target) {
            const panel = e.target.closest('.relief-panel');
            if (panel) updateSelectAllBtn(panel.id);
        }
    }

    function selectAllItems(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        const cbs = panel.querySelectorAll('.rp-cb-native');
        const allChecked = Array.from(cbs).every(cb => cb.checked);
        cbs.forEach(cb => cb.checked = !allChecked);
        onItemChange();
        updateSelectAllBtn(panelId);
    }

    function updateSelectAllBtn(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        const btn = panel.querySelector('.rp-select-all-btn');
        if (!btn) return;
        const cbs = panel.querySelectorAll('.rp-cb-native');
        const allChecked = cbs.length && Array.from(cbs).every(cb => cb.checked);
        btn.textContent = allChecked ? 'Unselect All' : 'Select All';
    }

    function updateGoodsDetail() {
        const gdField = document.getElementById('goodsDetailField');
        const gdWrap  = document.getElementById('goodsDetailWrap');
        if (!gdField) return;

        const allParts = [];

        if (activeCats.has('rcp-cash')) {
            const amt = document.getElementById('cashAmountInput');
            if (amt && amt.value) {
                allParts.push('\u20B1' + parseFloat(amt.value).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' cash per household');
            }
        }

        activeCats.forEach(btnId => {
            if (btnId === 'rcp-cash') return;
            const panelId = CAT_PANELS[btnId].panelId;
            const checked = document.querySelectorAll('#' + panelId + ' .rp-cb-native:checked');
            checked.forEach(cb => {
                const item     = cb.closest('.rp-item');
                const name     = item.querySelector('.rp-name').textContent.trim();
                const qtyInput = item.querySelector('.rp-qty-input');
                const unit     = item.querySelector('.rp-qty-unit') ? item.querySelector('.rp-qty-unit').textContent.trim() : '';
                const qty      = qtyInput && qtyInput.value ? qtyInput.value : '';
                allParts.push(qty ? `${qty} ${unit} ${name}` : name);
            });
        });

        if (allParts.length > 0) {
            gdField.value = allParts.join(', ');
            gdField.dataset.autoFilled = '1';
            if (gdWrap) gdWrap.style.display = '';
        } else {
            gdField.value = '';
            delete gdField.dataset.autoFilled;
            if (gdWrap) gdWrap.style.display = 'none';
        }
    }

    function updateBadge() {
        const badge = document.getElementById('reliefItemsBadge');
        if (!badge) return;

        if (activeCats.size === 0) { badge.style.display = 'none'; return; }

        let totalItems = 0;
        let cashLabel  = '';

        activeCats.forEach(btnId => {
            if (btnId === 'rcp-cash') {
                const amt = document.getElementById('cashAmountInput');
                if (amt && amt.value) cashLabel = '\u20B1' + parseFloat(amt.value).toLocaleString('en-PH', {minimumFractionDigits:2});
            } else {
                totalItems += document.querySelectorAll('#' + CAT_PANELS[btnId].panelId + ' .rp-cb-native:checked').length;
            }
        });

        const parts = [];
        if (totalItems > 0) parts.push(totalItems + ' item' + (totalItems > 1 ? 's' : ''));
        if (cashLabel)      parts.push(cashLabel);

        if (parts.length > 0) {
            badge.textContent = parts.join(' · ');
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    }

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('rp-qty-input')) {
            const item = e.target.closest('.rp-item');
            if (item && item.querySelector('.rp-cb-native:checked')) {
                updateGoodsDetail();
            }
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('rp-cb-native')) {
            onItemChange(e);
        }
    });


    /* ══════════════════════════════════════════════
       BARANGAY DROPDOWN + MAP (MINI + FULLSCREEN)
    ══════════════════════════════════════════════ */

    const selectedBarangays = new Set(@json(old('target_barangay', [])));
    let allBarangayNames = [];

    const BRGY_COORDS = {
        'Bagong Kalsada':               [14.3167, 120.7611],
        'Balsahan':                     [14.2972, 120.7375],
        'Bancaan':                      [14.3422, 120.7856],
        'Bucana Malaki':                [14.3044, 120.7503],
        'Bucana Sasahan':               [14.3008, 120.7467],
        'Calubcob':                     [14.3356, 120.7786],
        'Capt. C. Nazareno (Poblacion)':[14.3297, 120.7647],
        'Gombalza (Poblacion)':         [14.3281, 120.7631],
        'Halang':                       [14.3219, 120.7733],
        'Humbac':                       [14.3508, 120.7558],
        'Ibayo Estacion':               [14.3333, 120.7722],
        'Ibayo Silangan':               [14.3350, 120.7756],
        'Kanluran Rizal':               [14.3261, 120.7578],
        'Latoria':                      [14.3183, 120.7503],
        'Labac':                        [14.3397, 120.7683],
        'Mabolo':                       [14.3139, 120.7639],
        'Malainen Bago':                [14.3578, 120.7472],
        'Malainen Luma':                [14.3556, 120.7433],
        'Makina':                       [14.3467, 120.7533],
        'Molino':                       [14.3094, 120.7561],
        'Munting Mapino':               [14.3211, 120.7444],
        'Muzon':                        [14.3317, 120.7806],
        'Palangue 2 & 3':               [14.3372, 120.7839],
        'Palangue Central':             [14.3344, 120.7817],
        'Sabang':                       [14.3122, 120.7694],
        'San Roque':                    [14.3244, 120.7492],
        'Santulan':                     [14.3056, 120.7608],
        'Sapa':                         [14.3078, 120.7639],
        'Timalan Balsahan':             [14.3489, 120.7614],
        'Timalan Concepcion':           [14.3519, 120.7650],
    };

    let miniMap = null, fullMap = null;
    let pinMode = false, pinMarker = null, pinMarkerFull = null;
    const miniMarkers = {}, fullMarkers = {};

    /* ── DOMContentLoaded ── */
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof naicBarangays !== 'undefined') {
            allBarangayNames = [...naicBarangays];
        } else {
            allBarangayNames = [
                'Bagong Kalsada','Balsahan','Bancaan','Bucana Malaki','Bucana Sasahan',
                'Calubcob','Capt. C. Nazareno (Poblacion)','Gombalza (Poblacion)',
                'Halang','Humbac','Ibayo Estacion','Ibayo Silangan','Kanluran Rizal',
                'Latoria','Labac','Mabolo','Malainen Bago','Malainen Luma','Makina',
                'Molino','Munting Mapino','Muzon','Palangue 2 & 3','Palangue Central',
                'Sabang','San Roque','Santulan','Sapa','Timalan Balsahan','Timalan Concepcion'
            ];
        }
        buildBarangayList();
        onBrgyChange();
        initMiniMap();

        // Restore old relief types on validation failure
        const oldCats = @json(old('relief_type', []));
        if (Array.isArray(oldCats) && oldCats.length) {
            oldCats.forEach(type => {
                for (const btnId in CAT_PANELS) {
                    if (CAT_PANELS[btnId].type === type) {
                        toggleCategory(type, btnId, CAT_PANELS[btnId].panelId);
                        break;
                    }
                }
            });
        }

        // Hover hint on mini map area
        const strip = document.getElementById('distMapStrip');
        if (strip) {
            strip.addEventListener('mouseenter', () => document.getElementById('mapClickHint').classList.add('show'));
            strip.addEventListener('mouseleave', () => document.getElementById('mapClickHint').classList.remove('show'));
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMapModal(); });
    });

    /* ── Build barangay checkbox list ── */
    function buildBarangayList() {
        const container = document.getElementById('brgyItems');
        container.innerHTML = '';
        allBarangayNames.forEach(name => {
            const div = document.createElement('div');
            div.className = 'brgy-item' + (selectedBarangays.has(name) ? ' checked' : '');
            div.dataset.name = name;
            div.innerHTML = `
                <div class="brgy-checkbox">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span class="brgy-name">${name}</span>`;
            div.addEventListener('click', () => toggleBarangay(div, name));
            container.appendChild(div);
        });
        if (selectedBarangays.has('All Barangays')) {
            document.getElementById('allBarangaysRow').classList.add('checked');
        }
    }

    /* ── Tile layer helper ── */
    function addTiles(map) {
        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19, crossOrigin: true
        });
        const carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '© <a href="https://carto.com">CARTO</a>', subdomains: 'abcd', maxZoom: 19
        });
        osm.on('tileerror', () => { if (!map.hasLayer(carto)) { map.removeLayer(osm); carto.addTo(map); } });
        osm.addTo(map);
    }

    function makeStarPinIcon() {
        return L.divIcon({
            className: '',
            html: `<svg width="32" height="42" viewBox="0 0 28 38" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="14" cy="36" rx="5" ry="2" fill="rgba(0,0,0,.3)"/>
                <path d="M14 1C8.48 1 4 5.48 4 11c0 7.5 10 25 10 25S24 18.5 24 11C24 5.48 19.52 1 14 1z" fill="#C0392B" stroke="#fff" stroke-width="2.5"/>
                <circle cx="14" cy="11" r="4.5" fill="#fff" opacity=".92"/>
                <text x="14" y="15" text-anchor="middle" font-size="8" fill="#C0392B" font-weight="bold">★</text>
            </svg>`,
            iconSize:[32,42], iconAnchor:[16,42], popupAnchor:[0,-42]
        });
    }

    const NAIC_CENTER = [14.3294, 120.7614];
    let _mapCenter = NAIC_CENTER;

    function getMapCenter(callback) {
        if (!navigator.geolocation) { callback(NAIC_CENTER); return; }
        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude, lng = pos.coords.longitude;
                const inRange = lat > 13.8 && lat < 14.8 && lng > 120.3 && lng < 121.2;
                _mapCenter = inRange ? [lat, lng] : NAIC_CENTER;
                callback(_mapCenter);
            },
            () => callback(NAIC_CENTER),
            { timeout: 4000, maximumAge: 60000 }
        );
    }

    /* ── Init Mini Map ── */
    function initMiniMap() {
        if (miniMap) return;
        miniMap = L.map('distMapMini', { zoomControl: true, attributionControl: true })
            .setView(NAIC_CENTER, 13);
        addTiles(miniMap);

        getMapCenter(center => {
            miniMap.setView(center, 14);
            invalidateMiniMap();
        });

        miniMap.on('click', function (e) {
            if (pinMode) {
                dropPin(e.latlng.lat, e.latlng.lng);
            } else {
                openMapModal();
            }
        });

        invalidateMiniMap();
    }

    function invalidateMiniMap() {
        setTimeout(() => miniMap && miniMap.invalidateSize(true), 50);
        setTimeout(() => miniMap && miniMap.invalidateSize(true), 300);
        setTimeout(() => miniMap && miniMap.invalidateSize(true), 700);
    }

    /* ── Init Full Map ── */
    function initFullMap() {
        if (fullMap) return;
        fullMap = L.map('distMapFull', { zoomControl: true })
            .setView(_mapCenter, 14);
        addTiles(fullMap);

        fullMap.on('click', function (e) {
            if (pinMode) dropPin(e.latlng.lat, e.latlng.lng);
        });
    }

    /* ── Refresh markers on a given map ── */
    function refreshMarkers(map, markersCache) {
        if (!map) return;
        const isAll = selectedBarangays.has('All Barangays');
        const names = isAll ? allBarangayNames : [...selectedBarangays];

        Object.keys(markersCache).forEach(n => {
            map.removeLayer(markersCache[n]); delete markersCache[n];
        });

        const bounds = names.map(n => BRGY_COORDS[n]).filter(Boolean);
        if (bounds.length === 1) {
            map.setView(bounds[0], 15);
        } else if (bounds.length > 1) {
            map.fitBounds(L.latLngBounds(bounds), { padding: [32, 32] });
        }
    }

    /* ── Fly to a specific barangay on fullmap ── */
    function flyToBrgy(name) {
        const coords = BRGY_COORDS[name]; if (!coords) return;
        fullMap.flyTo(coords, 16, { duration: 0.8 });

        document.querySelectorAll('.map-modal-brgy-item').forEach(el => {
            el.classList.toggle('active', el.dataset.name === name);
        });

        const activeEl = document.querySelector(`.map-modal-brgy-item[data-name="${CSS.escape(name)}"]`);
        if (activeEl) activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        document.getElementById('modalSubtitle').textContent = `Viewing: ${name} · Click "Pin Location" to mark distribution point`;
    }

    /* ── Build modal sidebar list ── */
    function buildModalSidebar() {
        const list = document.getElementById('modalBrgyList');
        list.innerHTML = '';
        const isAll = selectedBarangays.has('All Barangays');
        const names = isAll ? allBarangayNames : [...selectedBarangays];
        document.getElementById('modalBrgyCount').textContent = names.length;

        names.forEach(name => {
            const div = document.createElement('div');
            div.className = 'map-modal-brgy-item';
            div.dataset.name = name;
            div.innerHTML = `<div class="map-modal-brgy-dot"></div>${name}`;
            div.addEventListener('click', () => flyToBrgy(name));
            list.appendChild(div);
        });
    }

    /* ── Open / Close modal ── */
    function openMapModal() {
        document.getElementById('mapModalOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';

        initFullMap();

        setTimeout(() => {
            fullMap.invalidateSize(true);
            refreshMarkers(fullMap, fullMarkers);
            buildModalSidebar();

            // Sync existing pin marker to full map
            if (pinMarker) {
                const pos = pinMarker.getLatLng();
                if (pinMarkerFull) fullMap.removeLayer(pinMarkerFull);
                pinMarkerFull = L.marker([pos.lat, pos.lng], {
                    icon: makeStarPinIcon(), draggable: true, zIndexOffset: 500
                }).addTo(fullMap)
                .bindPopup('<b>📍 Distribution Point</b><br><small>Drag to adjust</small>');
                pinMarkerFull.on('dragend', () => {
                    const p = pinMarkerFull.getLatLng();
                    updatePinInputs(p.lat, p.lng);
                    syncPinToMini(p.lat, p.lng);
                });
            }

            const names = selectedBarangays.has('All Barangays') ? allBarangayNames : [...selectedBarangays];
            if (names.length > 0) {
                setTimeout(() => flyToBrgy(names[0]), 200);
            }
        }, 80);
    }

    function closeMapModal() {
        document.getElementById('mapModalOverlay').classList.remove('open');
        document.body.style.overflow = '';
        if (pinMarkerFull) {
            const p = pinMarkerFull.getLatLng();
            syncPinToMini(p.lat, p.lng);
        }
    }

    /* ── Pin logic ── */
    function togglePinMode() {
        pinMode = !pinMode;
        updatePinModeUI();
        if (miniMap) miniMap.getContainer().style.cursor = pinMode ? 'crosshair' : '';
        if (fullMap) fullMap.getContainer().style.cursor = pinMode ? 'crosshair' : '';
    }

    function updatePinModeUI() {
        const stripBtn = document.getElementById('pinStripBtn');
        const modalBtn = document.getElementById('modalPinBtn');
        const active = 'rgba(245,197,24,0.35)';
        if (stripBtn) {
            stripBtn.textContent = pinMode ? '🎯 Click to Pin…' : '📍 Pin';
            stripBtn.style.background = pinMode ? active : '';
        }
        if (modalBtn) {
            modalBtn.textContent = pinMode ? '🎯 Click to Pin…' : '📍 Pin Location';
            modalBtn.style.background = pinMode ? active : '';
        }
    }

    function dropPin(lat, lng) {
        if (pinMarker)     miniMap && miniMap.removeLayer(pinMarker);
        if (pinMarkerFull) fullMap  && fullMap.removeLayer(pinMarkerFull);

        if (miniMap) {
            pinMarker = L.marker([lat, lng], { icon: makeStarPinIcon(), draggable: true, zIndexOffset: 500 })
                .addTo(miniMap)
                .bindPopup('<b>📍 Distribution Point</b><br><small>Drag to adjust</small>');
            pinMarker.on('dragend', () => {
                const p = pinMarker.getLatLng();
                updatePinInputs(p.lat, p.lng);
                syncPinToFull(p.lat, p.lng);
            });
        }

        if (fullMap) {
            pinMarkerFull = L.marker([lat, lng], { icon: makeStarPinIcon(), draggable: true, zIndexOffset: 500 })
                .addTo(fullMap)
                .bindPopup('<b>📍 Distribution Point</b><br><small>Drag to adjust</small>').openPopup();
            pinMarkerFull.on('dragend', () => {
                const p = pinMarkerFull.getLatLng();
                updatePinInputs(p.lat, p.lng);
                syncPinToMini(p.lat, p.lng);
            });
        }

        updatePinInputs(lat, lng);
        pinMode = false;
        updatePinModeUI();
        if (miniMap) miniMap.getContainer().style.cursor = '';
        if (fullMap) fullMap.getContainer().style.cursor = '';
    }

    function syncPinToFull(lat, lng) {
        if (!fullMap) return;
        if (pinMarkerFull) fullMap.removeLayer(pinMarkerFull);
        pinMarkerFull = L.marker([lat, lng], { icon: makeStarPinIcon(), draggable: true, zIndexOffset: 500 }).addTo(fullMap);
        pinMarkerFull.on('dragend', () => { const p = pinMarkerFull.getLatLng(); updatePinInputs(p.lat, p.lng); syncPinToMini(p.lat, p.lng); });
    }

    function syncPinToMini(lat, lng) {
        if (!miniMap) return;
        if (pinMarker) miniMap.removeLayer(pinMarker);
        pinMarker = L.marker([lat, lng], { icon: makeStarPinIcon(), draggable: true, zIndexOffset: 500 }).addTo(miniMap);
        pinMarker.on('dragend', () => { const p = pinMarker.getLatLng(); updatePinInputs(p.lat, p.lng); syncPinToFull(p.lat, p.lng); });
        updatePinInputs(lat, lng);
    }

    /**
     * FIX: This function now reliably writes distribution_dms to the hidden
     * input by building the DMS string BEFORE touching any other DOM element.
     * decimalToDmsString() is defined at the top of the script so it is
     * always available when this function runs.
     */
    function updatePinInputs(lat, lng) {
        // Build DMS string first — function is guaranteed to exist at this point
        const dmsStr = decimalToDmsString(lat, lng);

        // Write all four hidden inputs
        document.getElementById('distLat').value      = lat.toFixed(7);
        document.getElementById('distLng').value      = lng.toFixed(7);
        document.getElementById('distLocName').value  = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        document.getElementById('distDms').value      = dmsStr;   // ← THE FIX

        // Update visible UI
        document.getElementById('pinnedLocationText').textContent =
            `📍 ${dmsStr}  (${lat.toFixed(5)}, ${lng.toFixed(5)})`;
        document.getElementById('pinnedLocationDisplay').classList.add('show');
        document.getElementById('pinNote').classList.add('hidden');

        // Update modal pin bar if visible
        const bar = document.getElementById('modalPinBar');
        if (bar) {
            bar.classList.add('pinned');
            bar.innerHTML = `✅ Pinned: <strong>${dmsStr}</strong> &nbsp;·&nbsp; DD: ${lat.toFixed(5)}, ${lng.toFixed(5)} &nbsp;·&nbsp; Drag to adjust.`;
        }

        // Mirror coordinates back into the DMS input fields
        syncDmsFields(lat, lng);
    }

    function clearPin() {
        ['distLat','distLng','distLocName','distDms'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        if (pinMarker     && miniMap) { miniMap.removeLayer(pinMarker);     pinMarker     = null; }
        if (pinMarkerFull && fullMap)  { fullMap.removeLayer(pinMarkerFull); pinMarkerFull = null; }
        document.getElementById('pinnedLocationDisplay').classList.remove('show');
        document.getElementById('pinNote').classList.remove('hidden');
        const bar = document.getElementById('modalPinBar');
        if (bar) {
            bar.classList.remove('pinned');
            bar.innerHTML = '📍 Click <strong>"Pin Location"</strong> then click anywhere on the map to mark the distribution point. You can drag the pin to adjust.';
        }
        pinMode = false;
        updatePinModeUI();
        if (miniMap) miniMap.getContainer().style.cursor = '';
        if (fullMap) fullMap.getContainer().style.cursor = '';
    }

    /* ── Show/hide mini map strip ── */
    function showOrHideMap() {
        initMiniMap();
        refreshMarkers(miniMap, miniMarkers);
    }

    /* ── Dropdown open/close ── */
    function toggleBrgyDropdown() {
        const dd      = document.getElementById('brgyDropdown');
        const trigger = document.getElementById('brgyTrigger');
        const isOpen  = dd.classList.contains('open');
        dd.classList.toggle('open');
        trigger.classList.toggle('open');
        if (!isOpen) setTimeout(() => document.getElementById('brgySearch').focus(), 60);
    }

    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('brgyFieldWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('brgyDropdown').classList.remove('open');
            document.getElementById('brgyTrigger').classList.remove('open');
        }
    });

    /* ── Update trigger display ── */
    function updateTrigger() {
        const valEl  = document.getElementById('brgyTriggerValue');
        const badge  = document.getElementById('brgyCountBadge');
        const ddCnt  = document.getElementById('brgyDdCount');
        const total  = allBarangayNames.length;
        const n      = selectedBarangays.size;
        const isAll  = selectedBarangays.has('All Barangays');

        ddCnt.textContent = `${isAll ? total : n} / ${total} selected`;

        if (n === 0) {
            valEl.textContent = 'Click to select barangays…';
            valEl.classList.add('placeholder');
            badge.classList.remove('show','green');
        } else if (isAll) {
            valEl.textContent = `All ${total} Barangays — Municipality-Wide`;
            valEl.classList.remove('placeholder');
            badge.textContent = total; badge.classList.add('show','green');
        } else {
            const names = [...selectedBarangays];
            valEl.textContent = names.slice(0,2).join(', ') + (names.length > 2 ? ` +${names.length-2} more` : '');
            valEl.classList.remove('placeholder');
            badge.textContent = n; badge.classList.add('show'); badge.classList.remove('green');
        }
    }

    /* ── Tags ── */
    function renderTags() {
        const wrap = document.getElementById('brgyTagsWrap');
        wrap.innerHTML = '';
        if (selectedBarangays.size === 0) return;

        if (selectedBarangays.has('All Barangays')) {
            wrap.innerHTML = `<span class="brgy-tag">All Barangays <span class="brgy-tag-remove" onclick="clearAllBarangays()">×</span></span>`;
            return;
        }
        const names = [...selectedBarangays];
        const show  = names.slice(0, 5);
        const rest  = names.length - show.length;
        show.forEach(name => {
            const tag = document.createElement('span');
            tag.className = 'brgy-tag';
            tag.innerHTML = `${name} <span class="brgy-tag-remove" onclick="removeBarangay('${name.replace(/'/g,"\\'")}')">×</span>`;
            wrap.appendChild(tag);
        });
        if (rest > 0) {
            const more = document.createElement('span');
            more.className = 'brgy-tags-more';
            more.textContent = `+${rest} more`;
            more.onclick = () => toggleBrgyDropdown();
            wrap.appendChild(more);
        }
    }

    /* ── Hidden inputs ── */
    function renderHiddenInputs() {
        const c = document.getElementById('brgyHiddenInputs');
        c.innerHTML = '';
        selectedBarangays.forEach(name => {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = 'target_barangay[]'; i.value = name;
            c.appendChild(i);
        });
    }

    /* ── Search filter ── */
    document.getElementById('brgySearch').addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        let visible = 0;
        document.querySelectorAll('#brgyItems .brgy-item').forEach(item => {
            const match = item.dataset.name.toLowerCase().includes(q);
            item.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        document.getElementById('allBarangaysRow').style.display = q ? 'none' : '';
        document.getElementById('brgyDdEmpty').classList.toggle('show', visible === 0 && q.length > 0);
    });

    /* ── Master change handler ── */
    function onBrgyChange() {
        renderHiddenInputs();
        renderTags();
        updateTrigger();
        showOrHideMap();
    }

    /* ── Toggle individual barangay ── */
    function toggleBarangay(el, name) {
        if (selectedBarangays.has(name)) { selectedBarangays.delete(name); el.classList.remove('checked'); }
        else { selectedBarangays.add(name); el.classList.add('checked'); }
        if (selectedBarangays.has('All Barangays')) {
            selectedBarangays.delete('All Barangays');
            document.getElementById('allBarangaysRow').classList.remove('checked');
        }
        onBrgyChange();
    }

    function toggleAllBarangays(el) {
        const wasChecked = el.classList.contains('checked');
        selectedBarangays.clear();
        document.querySelectorAll('#brgyItems .brgy-item').forEach(i => i.classList.remove('checked'));
        if (!wasChecked) { selectedBarangays.add('All Barangays'); el.classList.add('checked'); }
        else { el.classList.remove('checked'); }
        onBrgyChange();
    }

    function selectAllBarangays() {
        selectedBarangays.clear();
        selectedBarangays.add('All Barangays');
        document.getElementById('allBarangaysRow').classList.add('checked');
        document.querySelectorAll('#brgyItems .brgy-item').forEach(i => i.classList.remove('checked'));
        onBrgyChange();
    }

    function clearAllBarangays() {
        selectedBarangays.clear();
        document.getElementById('allBarangaysRow').classList.remove('checked');
        document.querySelectorAll('#brgyItems .brgy-item').forEach(i => i.classList.remove('checked'));
        onBrgyChange();
    }

    function removeBarangay(name) {
        selectedBarangays.delete(name);
        const el = document.querySelector(`#brgyItems .brgy-item[data-name="${CSS.escape(name)}"]`);
        if (el) el.classList.remove('checked');
        onBrgyChange();
    }

    /* ── Form submit validation ── */
    document.getElementById('eventForm').addEventListener('submit', function (e) {
        if (activeCats.size === 0) {
            e.preventDefault();
            alert('Please select at least one Relief Type.');
            document.getElementById('reliefCatPicker').scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (selectedBarangays.size === 0) {
            e.preventDefault();
            alert('Please select at least one target barangay.');
            document.getElementById('brgyTrigger').focus();
        }

        /* ── Guard: re-assert scan_mode value on submit so browser autofill cannot reset it ── */
        var scanInput = document.getElementById('scanModeInput');
        if (!scanInput.value || (scanInput.value !== 'household' && scanInput.value !== 'family_head')) {
            scanInput.value = 'household';
        }
    });

    /* ══════════════════════════════════════════════
       DMS INPUT PANEL — live preview + Pin button
    ══════════════════════════════════════════════ */

    function onDmsInput() {
        const latDeg = document.getElementById('latDeg').value;
        const latMin = document.getElementById('latMin').value;
        const latSec = document.getElementById('latSec').value;
        const latDir = document.getElementById('latDir').value;
        const lngDeg = document.getElementById('lngDeg').value;
        const lngMin = document.getElementById('lngMin').value;
        const lngSec = document.getElementById('lngSec').value;
        const lngDir = document.getElementById('lngDir').value;

        const errEl  = document.getElementById('dmsErrorMsg');
        const pinBtn = document.getElementById('dmsPinBtn');
        const resLat = document.getElementById('dmsResultLat');
        const resLng = document.getElementById('dmsResultLng');
        const resFull= document.getElementById('dmsResultFull');

        const hasLat = latDeg !== '' || latMin !== '' || latSec !== '';
        const hasLng = lngDeg !== '' || lngMin !== '' || lngSec !== '';

        if (!hasLat && !hasLng) {
            resLat.textContent = '—'; resLat.classList.add('empty');
            resLng.textContent = '—'; resLng.classList.add('empty');
            resFull.textContent = 'Fill in fields above'; resFull.classList.add('empty');
            pinBtn.disabled = true;
            errEl.classList.remove('show');
            return;
        }

        const dLat = parseFloat(latDeg) || 0;
        const mLat = parseFloat(latMin) || 0;
        const sLat = parseFloat(latSec) || 0;
        const dLng = parseFloat(lngDeg) || 0;
        const mLng = parseFloat(lngMin) || 0;
        const sLng = parseFloat(lngSec) || 0;

        const valid =
            dLat >= 0 && dLat <= 90  &&
            mLat >= 0 && mLat < 60   &&
            sLat >= 0 && sLat < 60   &&
            dLng >= 0 && dLng <= 180 &&
            mLng >= 0 && mLng < 60   &&
            sLng >= 0 && sLng < 60;

        document.getElementById('latDeg').classList.toggle('error', dLat < 0 || dLat > 90);
        document.getElementById('latMin').classList.toggle('error', mLat < 0 || mLat >= 60);
        document.getElementById('latSec').classList.toggle('error', sLat < 0 || sLat >= 60);
        document.getElementById('lngDeg').classList.toggle('error', dLng < 0 || dLng > 180);
        document.getElementById('lngMin').classList.toggle('error', mLng < 0 || mLng >= 60);
        document.getElementById('lngSec').classList.toggle('error', sLng < 0 || sLng >= 60);

        if (!valid) {
            resLat.textContent = 'Error'; resLat.classList.add('empty');
            resLng.textContent = 'Error'; resLng.classList.add('empty');
            resFull.textContent = 'Fix values above'; resFull.classList.add('empty');
            errEl.classList.add('show');
            pinBtn.disabled = true;
            return;
        }

        errEl.classList.remove('show');

        const decLat = dmsToDecimal(dLat, mLat, sLat, latDir);
        const decLng = dmsToDecimal(dLng, mLng, sLng, lngDir);

        resLat.textContent = decLat.toFixed(7); resLat.classList.remove('empty');
        resLng.textContent = decLng.toFixed(7); resLng.classList.remove('empty');
        resFull.textContent = decimalToDmsString(decLat, decLng);
        resFull.classList.remove('empty');

        pinBtn.disabled = !(hasLat && hasLng);
    }

    function pinFromDms() {
        const lat = dmsToDecimal(
            document.getElementById('latDeg').value,
            document.getElementById('latMin').value,
            document.getElementById('latSec').value,
            document.getElementById('latDir').value
        );
        const lng = dmsToDecimal(
            document.getElementById('lngDeg').value,
            document.getElementById('lngMin').value,
            document.getElementById('lngSec').value,
            document.getElementById('lngDir').value
        );

        if (isNaN(lat) || isNaN(lng)) return;

        dropPin(lat, lng);
        if (miniMap) miniMap.setView([lat, lng], 16);
        if (fullMap)  fullMap.setView([lat, lng], 16);
    }




</script>

<script>
function selectScanMode(val) {
    document.getElementById("scanModeInput").value = val;

    /* Update hint text to match selection */
    var hints = {
        'household':   '<strong>Per Household:</strong> Staff scans the household QR card — only one claim allowed per household per event.',
        'family_head': '<strong>Per Family Head:</strong> Staff scans the family head\'s personal QR card — one claim per family head per event.'
    };
    var hintEl = document.getElementById('scanModeHint');
    if (hintEl) hintEl.innerHTML = hints[val] || hints['household'];

    var modes = ["household", "family_head"];
    for (var i = 0; i < modes.length; i++) {
        var m = modes[i];
        var card = document.getElementById("sm-" + m);
        var icon = document.getElementById("sm-" + m + "-icon");
        var titl = document.getElementById("sm-" + m + "-title");
        if (!card || !icon || !titl) continue;
        var sel = (m === val);
        card.style.border     = sel ? "2px solid #1B3F7A" : "2px solid #DEE2E8";
        card.style.background = sel ? "#EAF0FA"           : "#FFFFFF";
        icon.style.background = sel ? "#1B3F7A"           : "#DEE2E8";
        var svg = icon.querySelector("svg");
        if (svg) svg.setAttribute("stroke", sel ? "#fff" : "#5A6372");
        titl.style.color = sel ? "#122D5A" : "#2C3340";
    }
}
</script>
</body>
</html>