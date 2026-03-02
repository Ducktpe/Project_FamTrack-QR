<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Distribution Logs (Auditor)</title>
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
            --purple:     #5B3FA6;
            --purple-dark:#3D1F8A;
            --purple-pale:#F5F0FF;
            --purple-border:#D8CBF5;
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
        .topbar-left {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 0.3px;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .clock-inline {
            font-size: 12px;
            font-weight: 600;
            color: var(--yellow);
            letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
        }
        .clock-date-inline {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }
        .status-indicator::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #4CAF50;
            box-shadow: 0 0 5px #4CAF50;
            animation: blink 2s infinite;
        }
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

        /* Hamburger — hidden on desktop */
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
            transition: background 0.15s;
        }
        .hamburger:hover { background: var(--blue-pale); }
        .hamburger svg { width: 22px; height: 22px; display: block; }

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
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--gray-400); margin-bottom: 2px;
        }
        .header-title {
            font-family: 'PT Serif', serif;
            font-size: 18px; font-weight: 700;
            color: var(--blue-dark); line-height: 1.2;
        }
        .header-sub { font-size: 11px; color: var(--gray-600); margin-top: 2px; }

        .header-spacer { flex: 1; }

        .header-spacer { flex: 1; }

        .header-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: #E8D4F0;
            border-radius: 4px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--purple);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }
        .user-name { font-size: 12px; font-weight: 600; color: var(--purple-dark); }
        .user-role { font-size: 10px; color: var(--gray-600); }

        .sidebar {
            grid-area: sidebar;
            background: var(--purple);
            color: rgba(255,255,255,0.85);
            overflow-y: auto;
            padding: 0;
            z-index: 80;
        }
        .sidebar-close { display: none; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 16px 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }
        .nav-item:hover {
            background: rgba(0,0,0,0.2);
            color: var(--white);
        }
        .nav-item.active {
            background: rgba(0,0,0,0.3);
            color: var(--white);
            font-weight: 600;
            border-left-color: var(--yellow);
        }
        .nav-icon { width: 18px; height: 18px; flex-shrink: 0; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 70;
        }
        .sidebar-overlay.active { display: block; }

        .main-content {
            grid-area: main;
            background: var(--gray-50);
            overflow-y: auto;
            padding: 28px 32px;
        }

        .page-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-200);
        }
        .page-breadcrumb { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-breadcrumb span { color: var(--purple); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--purple-dark); }
        .page-sub { font-size: 12px; color: var(--gray-600); margin-top: 3px; }

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
            border-color: var(--purple);
            box-shadow: 0 0 2px rgba(123,46,140,0.2);
        }
        input::placeholder { color: #999; }

        .btn {
            padding: 8px 14px;
            background: var(--purple);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }
        .btn:hover { background: var(--purple-dark); }
        .btn-secondary { background: var(--gray-600); }
        .btn-secondary:hover { background: var(--gray-800); }
        .btn-view {
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
        }

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
            color: var(--purple);
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-600);
        }
        .modal-body { padding: 20px; }

        footer {
            grid-area: footer;
            background: var(--purple-dark);
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

        .badge-readonly {
            display: inline-block;
            padding: 2px 6px;
            background: #E8D4F0;
            color: var(--purple-dark);
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
        }

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
            .header-auditor-badge { padding: 6px 10px; gap: 8px; }
            .auditor-name { font-size: 12px; }
            .auditor-role { display: none; }

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
            .header-auditor-badge { padding: 5px 8px; }
            .auditor-avatar { width: 28px; height: 28px; font-size: 11px; }
            .auditor-name { font-size: 11px; }

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
        <div class="header-auditor-badge">
            <div class="auditor-avatar">A</div>
            <div class="auditor-info">
                <div class="auditor-name">Auditor</div>
                <div class="auditor-role">Read-Only Access</div>
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
            Dashboard Overview
        </a>

        <a href="#" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Distribution Events
        </a>

        <a href="{{ route('auditor.distribution.logs') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Distribution Logs
        </a>

        <a href="#" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            List of Residents
        </a>

        <a href="{{ route('auditor.households.index') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            List of Households
        </a>

        <a href="{{ route('auditor.family-profiles') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                <path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Family Profiles
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
                <div class="page-breadcrumb">Auditor / Distribution / <span>Logs & Events</span></div>
                <div class="page-h1">Distribution Events <span class="badge-readonly">READ ONLY</span></div>
                <div class="page-sub">View distribution events and household details (audit purposes)</div>
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
                <a href="{{ route('auditor.distribution.logs') }}" class="btn btn-secondary" style="text-decoration:none;">Clear</a>
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
                                <button class="btn btn-view" onclick="openModal('{{ route('auditor.distribution.events.households', $event) }}', '{{ $event->event_name }}')" style="cursor:pointer;">View Details</button>
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
