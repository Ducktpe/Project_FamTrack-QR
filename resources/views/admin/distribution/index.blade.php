<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Distribution Logs</title>
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
        .topbar {
            grid-area: topbar;
            background: var(--blue-dark);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 100;
        }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock-inline {
            font-size: 12px; font-weight: 600;
            color: var(--yellow); letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
        }
        .status-indicator {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.45);
        }
        .status-indicator::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: #4CAF50; box-shadow: 0 0 5px #4CAF50;
            animation: blink 2s infinite;
        }
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

        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            margin-left: -4px;
            border-radius: 4px;
            color: var(--blue-dark);
            flex-shrink: 0;
        }
        .hamburger:hover { background: var(--gray-50); }
        .hamburger svg { width: 20px; height: 20px; }

        .header-logos {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .header-logos img {
            height: 54px;
            width: 54px;
            object-fit: contain;
        }
        .logo-divider {
            width: 1px;
            height: 44px;
            background: var(--gray-200);
        }

        .header-text { margin-left: 4px; }
        .header-org {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray-400);
            margin-bottom: 2px;
        }
        .header-title {
            font-family: 'PT Serif', serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-dark);
            line-height: 1.2;
        }
        .header-sub {
            font-size: 11px;
            color: var(--gray-600);
            margin-top: 2px;
        }

        .header-spacer { flex: 1; }

        .header-admin-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background: var(--blue-pale);
            border: 1px solid var(--gray-200);
            border-radius: 4px;
        }
        .admin-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--blue);
            display: flex; align-items: center; justify-content: center;
            color: var(--white);
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }
        .admin-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-dark);
            line-height: 1.2;
        }
        .admin-role {
            font-size: 10px;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            grid-area: sidebar;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Close button — only shown on mobile */
        .sidebar-close {
            display: none;
            position: absolute;
            top: 12px; right: 12px;
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: 4px;
            width: 32px; height: 32px;
            align-items: center; justify-content: center;
            cursor: pointer; z-index: 10;
            color: var(--gray-600);
            transition: background 0.15s;
        }
        .sidebar-close:hover { background: #FEF2F2; color: #C0392B; }
        .sidebar-close svg { width: 16px; height: 16px; }

        .nav-section-label {
            padding: 18px 20px 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gray-400);
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--gray-600);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background 0.12s, color 0.12s, border-color 0.12s;
        }
        .nav-item:hover {
            background: var(--gray-50);
            color: var(--blue);
            border-left-color: var(--blue-light);
        }
        .nav-item.active {
            background: var(--blue-pale);
            color: var(--blue);
            border-left-color: var(--blue);
            font-weight: 600;
        }
        .nav-icon {
            width: 17px; height: 17px;
            flex-shrink: 0;
            color: inherit;
            opacity: 0.7;
        }
        .nav-item.active .nav-icon,
        .nav-item:hover .nav-icon { opacity: 1; }
        .nav-badge {
            margin-left: auto;
            background: var(--blue);
            color: var(--white);
            font-size: 9px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            letter-spacing: 0.5px;
        }
        .sidebar-sep {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 8px 0;
        }
        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
        }
        .logout-btn {
            width: 100%;
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: var(--blue);
            color: var(--white);
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s;
        }
        .logout-btn:hover { background: #C0392B; }

        /* ─── SIDEBAR OVERLAY ─── */
        .sidebar-overlay {
            display: none !important; /* Force hide until activated */
            position: fixed; 
            inset: 0;
            background: rgba(0,0,0,0.45); 
            z-index: 200;
            opacity: 0; 
            transition: opacity 0.25s;
            pointer-events: none; /* Don't block clicks when hidden */
        }
        .sidebar-overlay.active {
            display: block !important;
            pointer-events: auto; /* Allow clicks when active */
        }

        /* ─── MAIN CONTENT ─── */
        .main-content {
            grid-area: main;
            background: var(--gray-50);
            overflow-y: auto;
            padding: 28px 32px;
        }

        .page-titlebar {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
        }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--blue-light); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

        /* Filter Box */
        .filter-box {
            background: #fff;
            border: 1px solid var(--gray-200);
            padding: 16px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            align-items: end;
        }
        input, select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid var(--gray-200);
            border-radius: 4px;
            font-family: 'Open Sans', sans-serif;
            font-size: 13px;
            background: #fff;
            color: var(--gray-800);
            position: relative;
            z-index: 10;
        }
        input:focus, select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 2px rgba(27,63,122,0.2);
        }
        input::placeholder { color: #999; }

        .btn {
            padding: 8px 14px;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }
        .btn:hover { background: var(--blue-dark); }
        .btn-secondary { background: var(--gray-600); }
        .btn-secondary:hover { background: var(--gray-800); }
        .btn-view {
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid var(--gray-200);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
            text-align: left;
            font-size: 13px;
        }
        th {
            background: var(--gray-50);
            font-weight: 700;
        }
        tbody tr:hover { background: var(--gray-50); }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-content {
            background: #fff;
            border-radius: 6px;
            max-width: 900px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--gray-200);
        }
        .modal-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--blue);
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-600);
        }
        .modal-body { padding: 20px; }

        /* ─── FOOTER ─── */
        footer {
            grid-area: footer;
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
            gap: 8px;
        }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .footer-left strong { color: rgba(255,255,255,0.7); }

        @media (max-width: 900px) {
            .shell {
                grid-template-rows: 36px 76px 1fr 48px;
                grid-template-columns: 1fr;
                grid-template-areas:
                    "topbar"
                    "header"
                    "main"
                    "footer";
            }
            .sidebar {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                width: var(--sidebar-w);
                z-index: 300;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
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

            .filter-box { padding: 12px; }
            .filters { grid-template-columns: 1fr; }
            
            footer { padding: 0 12px; }
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
        <div class="topbar-left">Republic of the Philippines | Province of Cavite | Municipality of Naic</div>
        <div class="topbar-right">
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
            <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal">
        </div>
        <div class="header-text">
            <div class="header-org">Office of the Municipal DRRMO</div>
            <div class="header-title">MDRRMO — Naic, Cavite</div>
            <div class="header-sub">Municipal Disaster Risk Reduction and Management Office</div>
        </div>
        <div class="header-spacer"></div>
        <div class="header-admin-badge">
            <div class="admin-avatar">A</div>
            <div class="admin-info">
                <div class="admin-name">Administrator</div>
                <div class="admin-role">Full Access</div>
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
                <div class="page-breadcrumb">Admin / Distribution / <span>Logs & Events</span></div>
                <div class="page-h1">Distribution Events</div>
                <div class="page-sub">View and manage distribution events with household details</div>
            </div>
        </div>

        <div class="filter-box">
            <form method="GET" class="filters">
                <input type="text" name="search" placeholder="Search event name, status..." value="{{ request('search') }}" style="grid-column: 1 / -1;">
                <input type="date" name="date_from" placeholder="From Date" value="{{ request('date_from') }}">
                <input type="date" name="date_to" placeholder="To Date" value="{{ request('date_to') }}">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button class="btn" type="submit">Filter</button>
                <a href="{{ route('admin.distribution.logs') }}" class="btn btn-secondary" style="text-decoration:none;">Clear</a>
            </form>
        </div>

        @if($events->isEmpty())
            <div style="text-align: center; padding: 40px; background: #fff; border-radius: 6px;">
                <p style="color: var(--gray-600);">No distribution events found.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total Distributed</th>
                        <th>Unique Households</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td><strong>{{ $event->event_name }}</strong></td>
                            <td>{{ $event->event_date }}</td>
                            <td>{{ $event->status }}</td>
                            <td>{{ $event->total_distributed }}</td>
                            <td>{{ $event->unique_households }}</td>
                            <td>
                                <button class="btn btn-view" onclick="openModal('{{ route('admin.distribution.events.households', $event) }}', '{{ $event->event_name }}')" style="cursor:pointer;">View Details</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Modal Overlay -->
        <div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 id="modalTitle">Event Details</h2>
                    <button class="modal-close" onclick="closeModal()">✕</button>
                </div>
                <div class="modal-body" id="modalBody">Loading...</div>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">
            <span>© <span id="footer-year">2026</span> <strong>MDRRMO Naic, Cavite</strong> — All Rights Reserved</span>
        </div>
    </footer>

</div>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('active');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }

    function openModal(url, eventName) {
        document.getElementById('modalTitle').textContent = eventName + ' — Households';
        document.getElementById('modalOverlay').classList.add('active');
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const content = doc.querySelector('.modal-body');
                if (content) {
                    document.getElementById('modalBody').innerHTML = content.innerHTML;
                }
            })
            .catch(error => {
                document.getElementById('modalBody').innerHTML = '<p style="color: red;">Error loading households. Please try again.</p>';
            });
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('top-time').textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    }
    updateClock();
    setInterval(updateClock, 1000);
    document.getElementById('footer-year').textContent = new Date().getFullYear();
</script>
</body>
</html>
