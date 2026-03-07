<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Create Distribution Event</title>
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

        /* ─── BARANGAY COUNTER BAR ─── */
        .brgy-counter-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 7px 12px; margin-bottom: 6px;
            background: var(--white); border: 1px solid var(--gray-200); border-radius: 3px;
        }
        .brgy-counter-label {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px; color: var(--gray-600);
        }
        .brgy-counter-badge {
            font-size: 12px; font-weight: 700;
            color: var(--blue); background: var(--blue-pale);
            border: 1px solid #C7D9F5; border-radius: 10px;
            padding: 2px 12px; letter-spacing: 0.3px;
            transition: background 0.2s, color 0.2s;
        }
        .brgy-counter-badge.all-selected {
            background: var(--green-pale); color: var(--green-dark);
            border-color: #86efac;
        }

        /* ─── BARANGAY MULTI-SELECT ─── */
        .brgy-selector {
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            background: var(--white);
            overflow: hidden;
        }

        .brgy-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }
        .brgy-search-wrap {
            flex: 1;
            position: relative;
        }
        .brgy-search-wrap svg {
            position: absolute;
            left: 8px; top: 50%;
            transform: translateY(-50%);
            width: 13px; height: 13px;
            color: var(--gray-400);
            pointer-events: none;
        }
        .brgy-search {
            width: 100%;
            padding: 6px 8px 6px 28px;
            border: 1px solid var(--gray-200);
            border-radius: 3px;
            font-size: 12px;
            font-family: 'Open Sans', sans-serif;
            background: var(--white);
            outline: none;
            transition: border-color 0.15s;
        }
        .brgy-search:focus { border-color: var(--blue-light); }

        .brgy-toolbar-btns { display: flex; gap: 5px; flex-shrink: 0; }
        .brgy-tb-btn {
            padding: 5px 10px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            border-radius: 3px; border: 1px solid var(--gray-200);
            cursor: pointer; font-family: 'Open Sans', sans-serif;
            transition: background 0.12s;
            white-space: nowrap;
        }
        .brgy-tb-btn.select-all { background: var(--blue-pale); color: var(--blue); border-color: #C7D9F5; }
        .brgy-tb-btn.select-all:hover { background: #C7D9F5; }
        .brgy-tb-btn.clear-all  { background: var(--gray-100); color: var(--gray-600); }
        .brgy-tb-btn.clear-all:hover  { background: var(--gray-200); }

        .brgy-list {
            max-height: 220px;
            overflow-y: auto;
            padding: 6px 0;
        }
        .brgy-list::-webkit-scrollbar { width: 4px; }
        .brgy-list::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

        .brgy-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 12px;
            cursor: pointer;
            transition: background 0.1s;
            user-select: none;
        }
        .brgy-item:hover { background: var(--blue-pale); }
        .brgy-item.hidden { display: none; }
        .brgy-item.checked { background: var(--blue-pale); }

        .brgy-checkbox {
            width: 16px; height: 16px;
            border: 2px solid var(--gray-200);
            border-radius: 3px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background 0.12s, border-color 0.12s;
        }
        .brgy-item.checked .brgy-checkbox {
            background: var(--blue);
            border-color: var(--blue);
        }
        .brgy-checkbox svg { width: 10px; height: 10px; color: var(--white); opacity: 0; transition: opacity 0.1s; }
        .brgy-item.checked .brgy-checkbox svg { opacity: 1; }

        .brgy-name { font-size: 13px; color: var(--gray-800); }
        .brgy-item.checked .brgy-name { color: var(--blue-dark); font-weight: 600; }

        .brgy-item.all-row {
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 4px;
        }
        .brgy-item.all-row .brgy-name { font-weight: 600; color: var(--blue-dark); }

        .brgy-footer {
            padding: 8px 12px;
            border-top: 1px solid var(--gray-100);
            background: var(--gray-50);
            min-height: 36px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .brgy-footer-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-400); flex-shrink: 0; }
        .brgy-tag {
            display: inline-flex; align-items: center; gap: 4px;
            background: var(--blue-pale); border: 1px solid #C7D9F5;
            color: var(--blue); font-size: 11px; font-weight: 600;
            padding: 2px 8px; border-radius: 10px;
        }
        .brgy-tag-remove { cursor: pointer; opacity: 0.6; line-height: 1; font-size: 13px; }
        .brgy-tag-remove:hover { opacity: 1; }
        .brgy-none { font-size: 11px; color: var(--gray-400); font-style: italic; }
        .brgy-count { font-size: 11px; color: var(--blue); font-weight: 700; }

        /* ─── RELIEF TYPE — CATEGORY PICKER ─── */
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

        /* ─── RELIEF ITEMS PANEL ─── */
        .relief-items-empty {
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            padding: 36px 20px; text-align: center; color: var(--gray-400);
        }
        .relief-items-empty svg { opacity: 0.25; }
        .relief-items-empty p { font-size: 13px; line-height: 1.6; }
        .relief-items-empty strong { color: var(--gray-600); }

        .relief-panel { animation: rpFadeIn 0.2s ease; }
        @keyframes rpFadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        /* Panel header */
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

        /* ─── ENHANCED RELIEF ITEM ROWS ─── */
        /* Items now have a checkbox + name on the left, qty input on the right */
        .rp-table {
            border: 1px solid var(--gray-200);
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        /* Column header row */
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

        /* Quantity input wrapper */
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
        /* Enable qty when checked */
        .rp-item:has(.rp-cb-native:checked) .rp-qty-wrap {
            opacity: 1;
            pointer-events: auto;
            border-color: var(--blue-light);
            box-shadow: 0 0 0 2px rgba(36,89,168,0.09);
        }
        .rp-qty-input {
            width: 60px; /* shrinks with unit label */
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

        /* Cash input */
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

        /* Status badge */
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

        /* Submit bar */
        .submit-bar { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--green); padding: 16px 24px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .btn-submit { display: inline-flex; align-items: center; gap: 7px; padding: 11px 24px; background: var(--green); color: var(--white); border: none; border-radius: 4px; cursor: pointer; font-family: 'Open Sans', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.15s; }
        .btn-submit:hover { background: var(--green-dark); }
        .btn-submit svg { width: 15px; height: 15px; }
        .btn-cancel { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: var(--white); color: var(--gray-600); border: 1px solid var(--gray-200); border-radius: 4px; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: none; transition: background 0.15s; }
        .btn-cancel:hover { background: var(--gray-100); }

        /* ─── INFO SIDEBAR PANEL ─── */
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

        /* ─── RESPONSIVE ─── */
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
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Back to Dashboard
            </a>
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

                {{-- Relief Items --}}
                <div class="form-section" id="reliefItemsSection">
                    <div class="form-section-header">
                        <div class="ca-dot"></div>
                        <div class="form-section-title">Relief Items</div>
                        <span id="reliefItemsBadge" style="display:none;margin-left:auto;font-size:11px;font-weight:700;color:var(--blue);background:var(--blue-pale);border:1px solid #C7D9F5;padding:2px 10px;border-radius:10px;"></span>
                    </div>
                    <div class="form-section-body">

                        {{-- Empty state --}}
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

                        {{-- Goods detail (auto-filled) --}}
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
                                <div class="brgy-counter-bar" id="brgyCounterBar">
                                    <span class="brgy-counter-label">Selected Barangays</span>
                                    <span class="brgy-counter-badge" id="brgyCounterBadge">0 / 30</span>
                                </div>
                                <div class="brgy-selector">
                                    <div class="brgy-toolbar">
                                        <div class="brgy-search-wrap">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                            <input type="text" class="brgy-search" id="brgySearch" placeholder="Filter barangays..." autocomplete="off">
                                        </div>
                                        <div class="brgy-toolbar-btns">
                                            <button type="button" class="brgy-tb-btn select-all" onclick="selectAllBarangays()">All</button>
                                            <button type="button" class="brgy-tb-btn clear-all"  onclick="clearAllBarangays()">Clear</button>
                                        </div>
                                    </div>
                                    <div class="brgy-list" id="brgyList">
                                        <div class="brgy-item all-row" id="allBarangaysRow" onclick="toggleAllBarangays(this)">
                                            <div class="brgy-checkbox"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                                            <span class="brgy-name">All Barangays (Municipality-Wide)</span>
                                        </div>
                                        <div id="brgyItems"></div>
                                    </div>
                                    <div class="brgy-footer" id="brgyFooter">
                                        <span class="brgy-footer-label">Selected:</span>
                                        <span class="brgy-none" id="brgyNone">None selected</span>
                                    </div>
                                </div>
                                <div class="form-hint">Select one or more barangays. Use "All Barangays" for municipality-wide events.</div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Submit --}}
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

    /* ── Relief type category picker — MULTI-SELECT ── */

    const CAT_PANELS = {
        'rcp-cash':     { panelId: 'items-cash',     type: 'Cash Aid' },
        'rcp-dignity':  { panelId: 'items-dignity',  type: 'Dignity Kit' },
        'rcp-firstaid': { panelId: 'items-firstaid', type: 'First Aid Kit' },
        'rcp-food':     { panelId: 'items-food',     type: 'Food Pack' },
        'rcp-hygiene':  { panelId: 'items-hygiene',  type: 'Hygiene Kit' },
    };

    // Tracks which categories are currently active (Set of btnIds)
    const activeCats = new Set();

    function toggleCategory(type, btnId, panelId) {
        const btn   = document.getElementById(btnId);
        const panel = document.getElementById(panelId);

        if (activeCats.has(btnId)) {
            // Deactivate: hide panel, uncheck all items inside it
            activeCats.delete(btnId);
            btn.classList.remove('active');
            panel.style.display = 'none';
            panel.querySelectorAll('.rp-cb-native').forEach(cb => cb.checked = false);
            if (btnId === 'rcp-cash') {
                const ca = document.getElementById('cashAmountInput');
                if (ca) ca.value = '';
            }
        } else {
            // Activate: show panel
            activeCats.add(btnId);
            btn.classList.add('active');
            panel.style.display = '';
        }

        // Hide/show empty state
        const emptyEl = document.getElementById('reliefItemsEmpty');
        if (emptyEl) emptyEl.style.display = activeCats.size === 0 ? '' : 'none';

        renderReliefTypeInputs();
        updateGoodsDetail();
        updateBadge();
        updateSelectAllBtn(panelId);
    }

    /* Render hidden inputs for relief_type[] */
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

        // whenever an individual item checkbox is changed, update the select-all
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

        // Cash Aid
        if (activeCats.has('rcp-cash')) {
            const amt = document.getElementById('cashAmountInput');
            if (amt && amt.value) {
                allParts.push('\u20B1' + parseFloat(amt.value).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' cash per household');
            }
        }

        // All other active kits
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

    /* Update goods detail live when qty inputs change */
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('rp-qty-input')) {
            const item = e.target.closest('.rp-item');
            if (item && item.querySelector('.rp-cb-native:checked')) {
                updateGoodsDetail();
            }
        }
    });

    // also handle checkbox toggles globally (so we don't need inline handlers on every input)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('rp-cb-native')) {
            onItemChange(e);
        }
    });


    /* ══════════════════════════════════════════════
       BARANGAY MULTI-SELECT
    ══════════════════════════════════════════════ */

    const selectedBarangays = new Set(
        @json(old('target_barangay', []))
    );

    let allBarangayNames = [];

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
        renderHiddenInputs();
        renderFooterTags();

        // restore any previously selected relief types (old input after validation error)
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
    });

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

        const allRow = document.getElementById('allBarangaysRow');
        if (selectedBarangays.has('All Barangays')) {
            allRow.classList.add('checked');
        }
    }

    function toggleBarangay(el, name) {
        if (selectedBarangays.has(name)) {
            selectedBarangays.delete(name);
            el.classList.remove('checked');
        } else {
            selectedBarangays.add(name);
            el.classList.add('checked');
        }
        if (selectedBarangays.has('All Barangays')) {
            selectedBarangays.delete('All Barangays');
            document.getElementById('allBarangaysRow').classList.remove('checked');
        }
        renderHiddenInputs();
        renderFooterTags();
    }

    function toggleAllBarangays(el) {
        const isChecked = el.classList.contains('checked');
        selectedBarangays.clear();
        document.querySelectorAll('#brgyItems .brgy-item').forEach(item => item.classList.remove('checked'));

        if (!isChecked) {
            selectedBarangays.add('All Barangays');
            el.classList.add('checked');
        } else {
            el.classList.remove('checked');
        }
        renderHiddenInputs();
        renderFooterTags();
    }

    function selectAllBarangays() {
        selectedBarangays.clear();
        selectedBarangays.add('All Barangays');
        document.getElementById('allBarangaysRow').classList.add('checked');
        document.querySelectorAll('#brgyItems .brgy-item:not(.hidden)').forEach(item => item.classList.remove('checked'));
        renderHiddenInputs();
        renderFooterTags();
    }

    function clearAllBarangays() {
        selectedBarangays.clear();
        document.getElementById('allBarangaysRow').classList.remove('checked');
        document.querySelectorAll('#brgyItems .brgy-item').forEach(item => item.classList.remove('checked'));
        renderHiddenInputs();
        renderFooterTags();
    }

    function renderHiddenInputs() {
        const container = document.getElementById('brgyHiddenInputs');
        container.innerHTML = '';
        selectedBarangays.forEach(name => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'target_barangay[]';
            input.value = name;
            container.appendChild(input);
        });
    }

    function renderFooterTags() {
        const footer   = document.getElementById('brgyFooter');
        const noneEl   = document.getElementById('brgyNone');
        const existing = footer.querySelectorAll('.brgy-tag, .brgy-count');
        existing.forEach(el => el.remove());

        // Update counter badge
        const badge = document.getElementById('brgyCounterBadge');
        const total = allBarangayNames.length;
        if (badge) {
            if (selectedBarangays.has('All Barangays')) {
                badge.textContent = `${total} / ${total}`;
                badge.classList.add('all-selected');
            } else {
                const n = selectedBarangays.size;
                badge.textContent = `${n} / ${total}`;
                badge.classList.toggle('all-selected', n === total);
            }
        }

        if (selectedBarangays.size === 0) {
            noneEl.style.display = '';
            return;
        }
        noneEl.style.display = 'none';

        if (selectedBarangays.has('All Barangays')) {
            const tag = document.createElement('span');
            tag.className = 'brgy-tag';
            tag.innerHTML = `All Barangays <span class="brgy-tag-remove" onclick="clearAllBarangays()">×</span>`;
            footer.appendChild(tag);
            return;
        }

        const names = [...selectedBarangays];
        const show = names.slice(0, 4);
        const rest = names.length - show.length;

        show.forEach(name => {
            const tag = document.createElement('span');
            tag.className = 'brgy-tag';
            tag.innerHTML = `${name} <span class="brgy-tag-remove" onclick="removeBarangay('${name.replace(/'/g,"\\'")}')">×</span>`;
            footer.appendChild(tag);
        });

        if (rest > 0) {
            const more = document.createElement('span');
            more.className = 'brgy-count';
            more.textContent = `+${rest} more`;
            footer.appendChild(more);
        }
    }

    function removeBarangay(name) {
        selectedBarangays.delete(name);
        const item = document.querySelector(`#brgyItems .brgy-item[data-name="${CSS.escape(name)}"]`);
        if (item) item.classList.remove('checked');
        renderHiddenInputs();
        renderFooterTags();
    }

    document.getElementById('brgySearch').addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('#brgyItems .brgy-item').forEach(item => {
            const match = item.dataset.name.toLowerCase().includes(q);
            item.classList.toggle('hidden', !match);
        });
        document.getElementById('allBarangaysRow').style.display = q ? 'none' : '';
    });

    document.getElementById('eventForm').addEventListener('submit', function (e) {
        if (activeCats.size === 0) {
            e.preventDefault();
            alert('Please select at least one Relief Type.');
            document.getElementById('reliefCatPicker').scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (selectedBarangays.size === 0) {
            e.preventDefault();
            alert('Please select at least one target barangay.');
            document.getElementById('brgySearch').focus();
        }
    });
</script>
</body>
</html>