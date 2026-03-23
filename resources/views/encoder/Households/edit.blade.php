<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Edit Household</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue:#1B3F7A;--blue-dark:#122D5A;--blue-light:#2459A8;--blue-pale:#EAF0FA;
            --yellow:#F5C518;--yellow-dark:#D4A800;--green:#16A34A;--green-pale:#DCFCE7;
            --green-dark:#15803D;--orange:#D97706;--orange-pale:#FFFBEB;
            --red:#C0392B;--red-pale:#FEF2F2;--white:#FFFFFF;
            --gray-50:#F7F8FA;--gray-100:#F0F2F5;--gray-200:#DEE2E8;
            --gray-400:#9AA3B0;--gray-600:#5A6372;--gray-800:#2C3340;--sidebar-w:260px;
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        html,body{height:100%;font-family:'Open Sans',sans-serif;background:var(--gray-100);color:var(--gray-800);font-size:14px;}
        .shell{display:grid;grid-template-rows:36px 76px 1fr 48px;grid-template-columns:var(--sidebar-w) 1fr;grid-template-areas:"topbar topbar" "header header" "sidebar main" "footer footer";height:100vh;overflow:hidden;}
        .topbar{grid-area:topbar;background:var(--blue-dark);display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:100;}
        .topbar-left{font-size:11px;color:rgba(255,255,255,0.5);}
        .topbar-right{display:flex;align-items:center;gap:20px;}
        .clock-inline{font-size:12px;font-weight:600;color:var(--yellow);letter-spacing:1px;font-variant-numeric:tabular-nums;}
        .clock-date-inline{font-size:11px;color:rgba(255,255,255,0.45);}
        .status-indicator{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,0.45);}
        .status-indicator::before{content:'';width:6px;height:6px;border-radius:50%;background:#4CAF50;box-shadow:0 0 5px #4CAF50;animation:blink 2s infinite;}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:0.4}}
        header{grid-area:header;background:var(--white);border-bottom:3px solid var(--yellow);box-shadow:0 2px 6px rgba(0,0,0,0.08);display:flex;align-items:center;padding:0 28px;gap:14px;z-index:90;}
        .hamburger{display:none;background:none;border:none;cursor:pointer;padding:6px;margin-left:-4px;border-radius:4px;color:var(--blue-dark);flex-shrink:0;transition:background 0.15s;}
        .hamburger:hover{background:var(--blue-pale);}
        .hamburger svg{width:22px;height:22px;display:block;}
        .header-logos{display:flex;align-items:center;gap:12px;flex-shrink:0;}
        .header-logos img{height:54px;width:54px;object-fit:contain;}
        .logo-divider{width:1px;height:44px;background:var(--gray-200);}
        .header-text{margin-left:4px;}
        .header-org{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gray-400);margin-bottom:2px;}
        .header-title{font-family:'PT Serif',serif;font-size:18px;font-weight:700;color:var(--blue-dark);}
        .header-sub{font-size:11px;color:var(--gray-600);margin-top:2px;}
        .header-spacer{flex:1;}
        .header-user-badge{display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--orange-pale);border:1px solid var(--orange);border-radius:4px;flex-shrink:0;}
        .user-avatar{width:32px;height:32px;border-radius:50%;background:var(--orange);display:flex;align-items:center;justify-content:center;color:var(--white);font-weight:700;font-size:13px;flex-shrink:0;}
        .user-name{font-size:13px;font-weight:600;color:var(--gray-800);line-height:1.2;}
        .user-role{font-size:10px;color:var(--orange);text-transform:uppercase;letter-spacing:0.5px;}
        .sidebar-overlay{display:none !important;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:200;pointer-events:none;}
        .sidebar-overlay.active{display:block !important;pointer-events:auto;}
        .sidebar{grid-area:sidebar;background:var(--white);border-right:1px solid var(--gray-200);display:flex;flex-direction:column;overflow-y:auto;position:relative;}
        .sidebar-close{display:none;position:absolute;top:12px;right:12px;background:var(--gray-100);border:1px solid var(--gray-200);border-radius:4px;width:32px;height:32px;align-items:center;justify-content:center;cursor:pointer;z-index:10;color:var(--gray-600);transition:background 0.15s;}
        .sidebar-close:hover{background:var(--red-pale);color:var(--red);}
        .sidebar-close svg{width:16px;height:16px;}
        .nav-section-label{padding:18px 20px 8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gray-400);}
        .nav-item{display:flex;align-items:center;gap:12px;padding:11px 20px;font-size:13.5px;font-weight:500;color:var(--gray-600);text-decoration:none;border-left:3px solid transparent;transition:background 0.12s,color 0.12s,border-color 0.12s;}
        .nav-item:hover{background:var(--gray-50);color:var(--blue);border-left-color:var(--blue-light);}
        .nav-item.active{background:var(--blue-pale);color:var(--blue);border-left-color:var(--blue);font-weight:600;}
        .nav-icon{width:17px;height:17px;flex-shrink:0;color:inherit;opacity:0.7;}
        .nav-item.active .nav-icon,.nav-item:hover .nav-icon{opacity:1;}
        .sidebar-sep{border:none;border-top:1px solid var(--gray-100);margin:8px 0;}
        .sidebar-bottom{margin-top:auto;padding:16px 20px;border-top:1px solid var(--gray-200);}
        .logout-btn{width:100%;font-family:'Open Sans',sans-serif;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:1px;background:var(--blue);color:var(--white);border:none;padding:10px 16px;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background 0.15s;}
        .logout-btn:hover{background:var(--red);}
        .main-content{grid-area:main;background:var(--gray-50);overflow-y:auto;padding:28px 32px;}
        .page-titlebar{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--gray-200);gap:12px;flex-wrap:wrap;}
        .page-breadcrumb{font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;}
        .page-breadcrumb a{color:var(--blue-light);text-decoration:none;}
        .page-breadcrumb a:hover{text-decoration:underline;}
        .page-breadcrumb span{color:var(--blue-light);}
        .page-h1{font-family:'PT Serif',serif;font-size:22px;font-weight:700;color:var(--blue-dark);}
        .page-sub{font-size:12px;color:var(--gray-600);margin-top:3px;}
        .back-btn{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:600;color:var(--blue);text-decoration:none;padding:8px 16px;border:1px solid var(--gray-200);background:var(--white);border-radius:4px;transition:background 0.15s;white-space:nowrap;}
        .back-btn:hover{background:var(--blue-pale);}
        .back-btn svg{width:14px;height:14px;}
        .alert-error{background:var(--red-pale);border:1px solid #FECACA;border-left:4px solid var(--red);padding:12px 16px;margin-bottom:16px;font-size:12px;color:#7F1D1D;}
        .alert-error ul{margin-left:16px;margin-top:4px;}
        /* Form cards */
        .form-card{background:var(--white);border:1px solid var(--gray-200);margin-bottom:16px;}
        .form-card-header{padding:14px 20px;border-bottom:1px solid var(--gray-100);background:var(--gray-50);display:flex;align-items:center;gap:10px;}
        .card-dot{width:8px;height:8px;border-radius:50%;background:var(--yellow);border:2px solid var(--yellow-dark);flex-shrink:0;}
        .card-title{font-size:13px;font-weight:600;color:var(--blue-dark);flex:1;}
        .form-card-body{padding:24px;}
        /* Form fields */
        .form-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
        .form-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
        .form-group{display:flex;flex-direction:column;gap:6px;}
        .form-group.span-2{grid-column:span 2;}
        .form-group.span-3{grid-column:span 3;}
        .form-group label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--gray-600);}
        .form-group label .required{color:var(--red);margin-left:2px;}
        .form-control{font-family:'Open Sans',sans-serif;font-size:13px;color:var(--gray-800);border:1px solid var(--gray-200);border-radius:3px;padding:9px 12px;background:var(--white);outline:none;transition:border-color 0.15s,box-shadow 0.15s;width:100%;}
        .form-control:focus{border-color:var(--blue-light);box-shadow:0 0 0 3px rgba(36,89,168,0.1);}
        .form-control.is-error{border-color:var(--red);}
        .field-error{font-size:11px;color:var(--red);margin-top:2px;}
        /* Checkbox grid */
        .checkbox-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
        .checkbox-item{display:flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--gray-200);border-radius:3px;cursor:pointer;transition:background 0.12s,border-color 0.12s;}
        .checkbox-item:hover{background:var(--blue-pale);border-color:#C5D9F5;}
        .checkbox-item input[type="checkbox"]{width:15px;height:15px;accent-color:var(--blue);cursor:pointer;flex-shrink:0;}
        .checkbox-item span{font-size:12px;font-weight:600;color:var(--gray-700);}
        /* Nuclear family cards */
        .nf-edit-card{border:1px solid var(--gray-200);border-radius:6px;margin-bottom:16px;overflow:hidden;background:var(--white);}
        .nf-edit-header{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:linear-gradient(135deg,#f0f5ff,#e8f0fa);border-bottom:1px solid var(--gray-200);}
        .nf-edit-title{font-size:13px;font-weight:700;color:var(--blue-dark);display:flex;align-items:center;gap:8px;}
        .nf-num{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:var(--blue);color:#fff;font-size:11px;font-weight:700;border-radius:50%;}
        .nf-primary-badge{font-size:9px;font-weight:700;background:var(--yellow);color:var(--blue-dark);padding:2px 8px;border-radius:8px;text-transform:uppercase;letter-spacing:.5px;}
        .nf-edit-body{padding:20px;}
        /* Members table in edit */
        .members-edit-list{display:flex;flex-direction:column;gap:10px;margin-top:16px;}
        .member-edit-row{border:1px solid var(--gray-200);border-radius:4px;background:var(--gray-50);overflow:hidden;}
        .member-edit-row.is-head{border-left:3px solid var(--blue);}
        .member-edit-header{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--white);border-bottom:1px solid var(--gray-100);cursor:pointer;user-select:none;}
        .member-edit-label{font-size:12px;font-weight:700;color:var(--blue-dark);display:flex;align-items:center;gap:8px;}
        .head-tag{font-size:9px;font-weight:700;background:var(--blue);color:#fff;padding:2px 7px;border-radius:8px;letter-spacing:.3px;}
        .member-edit-toggle{font-size:11px;color:var(--gray-400);font-weight:600;}
        .member-edit-fields{padding:14px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
        .member-edit-fields.collapsed{display:none;}
        .btn-remove-member{background:none;border:1px solid var(--gray-200);border-radius:3px;padding:4px 10px;font-size:11px;font-weight:600;color:var(--red);cursor:pointer;display:flex;align-items:center;gap:5px;transition:background 0.12s;font-family:'Open Sans',sans-serif;}
        .btn-remove-member:hover{background:var(--red-pale);border-color:#FECACA;}
        .btn-remove-member svg{width:12px;height:12px;}
        .btn-add-member{display:inline-flex;align-items:center;gap:7px;font-family:'Open Sans',sans-serif;font-size:12px;font-weight:600;color:var(--blue);background:var(--blue-pale);border:1px dashed var(--blue-light);border-radius:3px;padding:9px 16px;cursor:pointer;transition:background 0.12s;margin-top:10px;}
        .btn-add-member:hover{background:#D6E8FA;}
        .btn-add-member svg{width:13px;height:13px;}
        /* Form actions */
        .form-actions{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:20px 24px;background:var(--gray-50);border:1px solid var(--gray-200);border-top:none;}
        .btn-cancel{font-family:'Open Sans',sans-serif;font-size:12px;font-weight:600;color:var(--gray-600);background:var(--white);border:1px solid var(--gray-200);border-radius:3px;padding:10px 20px;cursor:pointer;text-decoration:none;transition:background 0.15s;}
        .btn-cancel:hover{background:var(--gray-100);}
        .btn-submit{font-family:'Open Sans',sans-serif;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--white);background:var(--blue);border:none;border-radius:3px;padding:10px 28px;cursor:pointer;display:flex;align-items:center;gap:7px;transition:background 0.15s;}
        .btn-submit:hover{background:var(--blue-dark);}
        .btn-submit svg{width:14px;height:14px;}
        footer{grid-area:footer;background:var(--blue-dark);border-top:3px solid var(--yellow);display:flex;align-items:center;justify-content:space-between;padding:0 24px;gap:8px;z-index:100;}
        .footer-left{font-size:11px;color:rgba(255,255,255,0.4);}
        .footer-left strong{color:rgba(255,255,255,0.7);}
        .footer-center{font-size:10px;color:rgba(255,255,255,0.2);letter-spacing:1px;text-transform:uppercase;}
        .fb-link{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.15s;white-space:nowrap;}
        .fb-link:hover{color:var(--yellow);}
        .fb-link svg{width:13px;height:13px;}
        ::-webkit-scrollbar{width:5px;}::-webkit-scrollbar-track{background:var(--gray-100);}::-webkit-scrollbar-thumb{background:var(--gray-200);border-radius:4px;}
        @media(max-width:900px){
            .shell{grid-template-rows:36px auto 1fr 48px;grid-template-columns:1fr;grid-template-areas:"topbar" "header" "main" "footer";}
            .sidebar{grid-area:unset;position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);z-index:300;transform:translateX(-100%);transition:transform 0.28s cubic-bezier(0.4,0,0.2,1);box-shadow:4px 0 20px rgba(0,0,0,0.15);}
            .sidebar.open{transform:translateX(0);}
            .sidebar-overlay{display:block;}
            .sidebar-close{display:flex;}
            .sidebar .nav-section-label{padding-top:52px;}
            .hamburger{display:flex;}
            header{padding:0 16px;gap:10px;}
            .header-logos img{height:44px;width:44px;}
            .header-title{font-size:15px;}
            .header-sub{display:none;}
            .main-content{padding:20px 16px;}
            .form-grid{grid-template-columns:repeat(2,1fr);}
            .form-group.span-3{grid-column:span 2;}
            .checkbox-grid{grid-template-columns:repeat(2,1fr);}
            .member-edit-fields{grid-template-columns:repeat(2,1fr);}
        }
        @media(max-width:640px){
            header{padding:0 12px;gap:8px;}
            .header-logos img{height:36px;width:36px;}
            .logo-divider{display:none;}
            .header-logos img:last-child{display:none;}
            .header-org{display:none;}
            .header-title{font-size:13px;}
            .main-content{padding:16px 12px;}
            .form-grid,.member-edit-fields{grid-template-columns:1fr 1fr;}
            .form-grid-2{grid-template-columns:1fr;}
            .form-group.span-2,.form-group.span-3{grid-column:span 2;}
            .checkbox-grid{grid-template-columns:1fr 1fr;}
            .form-actions{flex-direction:column-reverse;gap:8px;}
            .btn-submit,.btn-cancel{width:100%;justify-content:center;}
            footer{padding:0 12px;}
            .footer-center{display:none;}
        }
    </style>
</head>
<body>
<div class="shell">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="topbar">
        <div class="topbar-left">Republic of the Philippines &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic</div>
        <div class="topbar-right">
            <span class="clock-date-inline" id="top-date">—</span>
            <span class="clock-inline" id="top-time">00:00:00</span>
            <span class="status-indicator">System Online</span>
        </div>
    </div>

    <header>
        <button class="hamburger" onclick="openSidebar()" aria-label="Open navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
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
        <div class="header-user-badge">
            <div class="user-avatar">E</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'Encoder' }}</div>
                <div class="user-role">Data Entry Access</div>
            </div>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <div class="nav-section-label">Encoder Menu</div>
        <a href="{{ route('encoder.dashboard') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('encoder.households.index') }}" class="nav-item active" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
            </svg>
            List of Households
        </a>
        <a href="{{ route('encoder.households.create') }}" class="nav-item" onclick="closeSidebar()">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Register New Household
        </a>
        <hr class="sidebar-sep">
        <div class="role-notice" style="margin:12px 14px;background:#FFFAE6;border:1px solid #F5C518;border-left:3px solid #D4A800;padding:10px 12px;border-radius:2px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#92400E;margin-bottom:3px;">&#9432; Encoder Access</div>
            <div style="font-size:11px;color:#78350F;line-height:1.5;">You can edit household profiles before Admin approval.</div>
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

    <main class="main-content">

        <div class="page-titlebar">
            <div>
                <div class="page-breadcrumb">
                    <a href="{{ route('encoder.dashboard') }}">Home</a> /
                    <a href="{{ route('encoder.households.index') }}">Households</a> /
                    <a href="{{ route('encoder.households.show', $household) }}">{{ $household->household_head_name }}</a> /
                    <span>Edit</span>
                </div>
                <div class="page-h1">Edit Household</div>
                <div class="page-sub">Update household and member information — changes saved until Admin approves</div>
            </div>
            <a href="{{ route('encoder.households.show', $household) }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Back to Profile
            </a>
        </div>

        @if($errors->any())
        <div class="alert-error">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('encoder.households.update', $household) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- ── Section 1A: Location & Contact ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot"></div>
                    <div class="card-title">Section 1A — Location &amp; Contact</div>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group span-2">
                            <label>Household Head Full Name <span class="required">*</span></label>
                            <input type="text" name="household_head_name" class="form-control @error('household_head_name') is-error @enderror"
                                value="{{ old('household_head_name', $household->household_head_name) }}" required>
                            @error('household_head_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-error @enderror"
                                value="{{ old('contact_number', $household->contact_number) }}" placeholder="09XX XXX XXXX">
                            @error('contact_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-error @enderror"
                                value="{{ old('email', $household->email) }}" placeholder="e.g. household@email.com">
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Valid ID</label>
                            <select name="valid_id_type" id="edit-sel-valid-id-type" class="form-control @error('valid_id_type') is-error @enderror"
                                onchange="onEditValidIdType(this)">
                                <option value="">— None / Not Applicable —</option>
                                <optgroup label="Government-Issued IDs">
                                    <option value="PhilSys (National ID)" @selected(old('valid_id_type', $household->valid_id_type) == 'PhilSys (National ID)')>PhilSys (National ID)</option>
                                    <option value="SSS ID" @selected(old('valid_id_type', $household->valid_id_type) == 'SSS ID')>SSS ID (Social Security System)</option>
                                    <option value="GSIS ID" @selected(old('valid_id_type', $household->valid_id_type) == 'GSIS ID')>GSIS ID (Gov't Service Insurance System)</option>
                                    <option value="PhilHealth ID" @selected(old('valid_id_type', $household->valid_id_type) == 'PhilHealth ID')>PhilHealth ID</option>
                                    <option value="Pag-IBIG ID" @selected(old('valid_id_type', $household->valid_id_type) == 'Pag-IBIG ID')>Pag-IBIG ID (HDMF)</option>
                                    <option value="Postal ID" @selected(old('valid_id_type', $household->valid_id_type) == 'Postal ID')>Postal ID</option>
                                    <option value="Voter's ID" @selected(old('valid_id_type', $household->valid_id_type) == "Voter's ID")>Voter's ID / COMELEC Card</option>
                                    <option value="Driver's License" @selected(old('valid_id_type', $household->valid_id_type) == "Driver's License")>Driver's License (LTO)</option>
                                    <option value="Passport" @selected(old('valid_id_type', $household->valid_id_type) == 'Passport')>Philippine Passport (DFA)</option>
                                    <option value="PRC ID" @selected(old('valid_id_type', $household->valid_id_type) == 'PRC ID')>PRC ID (Professional Regulation Commission)</option>
                                    <option value="NBI Clearance" @selected(old('valid_id_type', $household->valid_id_type) == 'NBI Clearance')>NBI Clearance</option>
                                    <option value="Police Clearance" @selected(old('valid_id_type', $household->valid_id_type) == 'Police Clearance')>Police Clearance</option>
                                    <option value="Senior Citizen ID" @selected(old('valid_id_type', $household->valid_id_type) == 'Senior Citizen ID')>Senior Citizen ID (OSCA)</option>
                                    <option value="PWD ID" @selected(old('valid_id_type', $household->valid_id_type) == 'PWD ID')>PWD ID (Persons with Disability)</option>
                                    <option value="Solo Parent ID" @selected(old('valid_id_type', $household->valid_id_type) == 'Solo Parent ID')>Solo Parent ID (DSWD)</option>
                                    <option value="4Ps / NHTS ID" @selected(old('valid_id_type', $household->valid_id_type) == '4Ps / NHTS ID')>4Ps / NHTS ID (DSWD)</option>
                                    <option value="OWWA ID" @selected(old('valid_id_type', $household->valid_id_type) == 'OWWA ID')>OWWA ID (Overseas Workers Welfare Admin)</option>
                                    <option value="OFW ID" @selected(old('valid_id_type', $household->valid_id_type) == 'OFW ID')>OFW ID (iDOLE)</option>
                                    <option value="UMID" @selected(old('valid_id_type', $household->valid_id_type) == 'UMID')>UMID (Unified Multi-Purpose ID)</option>
                                    <option value="TIN ID" @selected(old('valid_id_type', $household->valid_id_type) == 'TIN ID')>TIN ID (Bureau of Internal Revenue)</option>
                                    <option value="BIR Card" @selected(old('valid_id_type', $household->valid_id_type) == 'BIR Card')>BIR Card</option>
                                    <option value="TESDA Certificate" @selected(old('valid_id_type', $household->valid_id_type) == 'TESDA Certificate')>TESDA Certificate / ID</option>
                                </optgroup>
                                <optgroup label="Local / Other IDs">
                                    <option value="Barangay ID" @selected(old('valid_id_type', $household->valid_id_type) == 'Barangay ID')>Barangay ID</option>
                                    <option value="Company ID" @selected(old('valid_id_type', $household->valid_id_type) == 'Company ID')>Company / School ID</option>
                                    <option value="PhilHealth MDR" @selected(old('valid_id_type', $household->valid_id_type) == 'PhilHealth MDR')>PhilHealth Member Data Record</option>
                                </optgroup>
                            </select>
                            @error('valid_id_type')<div class="field-error">{{ $message }}</div>@enderror
                            <div id="edit-valid-id-num-wrap" style="margin-top:6px;">
                                <input type="text" name="valid_id_num" id="edit-inp-valid-id-num"
                                    class="form-control @error('valid_id_num') is-error @enderror"
                                    value="{{ old('valid_id_num', $household->valid_id_num) }}"
                                    placeholder="Paste or type ID number here">
                                @error('valid_id_num')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Barangay <span class="required">*</span></label>
                            <select name="barangay" class="form-control @error('barangay') is-error @enderror" required>
                                @foreach(['Bagong Kalsada','Balsahan','Bancaan','Bucana Malaki','Bucana Sasahan','Calubcob','Capt. C. Nazareno (Poblacion)','Gombalza (Poblacion)','Halang','Humbac','Ibayo Estacion','Ibayo Silangan','Kanluran','Latoria','Labac','Mabolo','Malainen Bago','Malainen Luma','Makina','Molino','Munting Mapino','Muzon','Palangue 2 & 3','Palangue Central','Sabang','San Roque','Santulan','Sapa','Timalan Balsahan','Timalan Concepcion'] as $brgy)
                                    <option value="{{ $brgy }}" @selected(old('barangay', $household->barangay) == $brgy)>{{ $brgy }}</option>
                                @endforeach
                            </select>
                            @error('barangay')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Barangay Area / Purok / Zoning</label>
                            <input type="text" name="barangay_area" class="form-control @error('barangay_area') is-error @enderror"
                                value="{{ old('barangay_area', $household->barangay_area) }}" placeholder="e.g. Purok 3 / Zone 1">
                            @error('barangay_area')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Location / Street / Sitio / Subdivision</label>
                            <input type="text" name="location" class="form-control @error('location') is-error @enderror"
                                value="{{ old('location', $household->location) }}" placeholder="e.g. Timalan St., Sitio Narra">
                            @error('location')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Latitude</label>
                            <input type="text" name="latitude" class="form-control @error('latitude') is-error @enderror"
                                value="{{ old('latitude', $household->latitude) }}" placeholder="e.g. 14.3124">
                            @error('latitude')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Longitude</label>
                            <input type="text" name="longitude" class="form-control @error('longitude') is-error @enderror"
                                value="{{ old('longitude', $household->longitude) }}" placeholder="e.g. 120.7606">
                            @error('longitude')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group span-3">
                            <label>Location Photo <span style="font-weight:400;color:var(--gray-400);text-transform:none;letter-spacing:0;">(optional — map screenshot or on-site photo)</span></label>
                            @if($household->coordinates_image)
                                <div style="margin-bottom:8px;position:relative;display:inline-block;">
                                    <img src="{{ asset('storage/' . $household->coordinates_image) }}"
                                         alt="Current location photo"
                                         style="max-height:160px;max-width:100%;border-radius:4px;border:1px solid var(--gray-200);">
                                    <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">Current photo — upload a new one to replace it</div>
                                </div>
                            @endif
                            <input type="file" name="coordinates_image" accept="image/*"
                                class="form-control" style="padding:6px;">
                            @error('coordinates_image')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 1B: Housing Unit ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot"></div>
                    <div class="card-title">Section 1B — Housing Unit</div>
                </div>
                <div class="form-card-body">
                    <div class="form-grid" style="margin-bottom:16px;">
                        <div class="form-group span-3">
                            <label>Year Built</label>
                            <input type="number" name="year_built" class="form-control @error('year_built') is-error @enderror"
                                value="{{ old('year_built', $household->year_built) }}" min="1900" max="{{ date('Y') }}" placeholder="e.g. 2005" style="max-width:160px;">
                            @error('year_built')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    @php
                        $housingOpts   = ['apartment'=>'Apartment','bungalow'=>'Bungalow','makeshift'=>'Makeshift','mobile_home'=>'Mobile Home','townhouse'=>'Townhouse','mansion'=>'Mansion','farmhouse'=>'Farmhouse','duplex'=>'Duplex','condo'=>'Condo','villa'=>'Villa','modular'=>'Modular Building','stilt'=>'Stilt House','hut'=>'Hut','single_detached'=>'Single Detached'];
                        $materialOpts  = ['concrete'=>'Concrete','semi_concrete'=>'Semi-Concrete','wood_light'=>'Wood and Light Materials','recycled'=>'Recycled Materials'];
                        $ownershipOpts = ['owned'=>'Owned','rented'=>'Rented','shared'=>'Shared','shared_renter'=>'Shared with Renter','isf'=>'Informal Settler Families','rights'=>'Rights'];
                        $electricOpts  = ['electric_company'=>'Electric Company','generator'=>'Generator','solar'=>'Solar','battery'=>'Battery','other'=>'Other'];
                    @endphp
                    <div class="form-grid">
                        <div class="form-group span-3">
                            <label>Type of Housing Unit</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;">
                                @foreach($housingOpts as $val => $lbl)
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-800);">
                                        <input type="radio" name="housing_type" value="{{ $val }}"
                                            @checked(old('housing_type', $household->housing_type) == $val)
                                            style="accent-color:var(--blue);width:14px;height:14px;">
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group span-3">
                            <label>Housing Material</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;">
                                @foreach($materialOpts as $val => $lbl)
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-800);">
                                        <input type="radio" name="housing_material" value="{{ $val }}"
                                            @checked(old('housing_material', $household->housing_material) == $val)
                                            style="accent-color:var(--blue);width:14px;height:14px;">
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group span-2">
                            <label>Type of Ownership</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;">
                                @foreach($ownershipOpts as $val => $lbl)
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-800);">
                                        <input type="radio" name="ownership_type" value="{{ $val }}"
                                            @checked(old('ownership_type', $household->ownership_type) == $val)
                                            style="accent-color:var(--blue);width:14px;height:14px;">
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Electricity Source</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;">
                                @foreach($electricOpts as $val => $lbl)
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-800);">
                                        <input type="radio" name="electricity_source" value="{{ $val }}"
                                            @checked(old('electricity_source', $household->electricity_source) == $val)
                                            style="accent-color:var(--blue);width:14px;height:14px;">
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 1C: Utilities & Sanitation ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot"></div>
                    <div class="card-title">Section 1C — Utilities &amp; Sanitation</div>
                </div>
                <div class="form-card-body">
                    @php
                        $waterOpts  = ['shallow_well'=>'Shallow Well (50 ft deep – Level 1) e.g. Poso, balon','deep_well'=>'Deep Well (100 ft deep – Level 1) e.g. Poso, balon','water_project'=>'Water Project (Other Source – Level 2)','maynilad'=>'Maynilad (Level 3)'];
                        $toiletOpts = ['safely_managed'=>'Yes — Safely Managed (with Septic Tank)','basic'=>'Yes — Basic (not shared with another household)','limited'=>'Yes — Limited (shared by 2 or more households)','unimproved'=>'Yes — Unimproved (pit / hukay)','open_defecation'=>'No — Open Defecation'];
                        $wasteOpts  = ['open_dump'=>'Open Dump Site','sanitary_landfill'=>'Sanitary Landfill','mrf'=>'MRF','garbage'=>'Garbage Collection','other'=>'Other'];
                    @endphp
                    <div class="form-grid">
                        <div class="form-group span-3">
                            <label>Source of Water</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;">
                                @foreach($waterOpts as $val => $lbl)
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-800);">
                                        <input type="radio" name="water_source" value="{{ $val }}"
                                            @checked(old('water_source', $household->water_source) == $val)
                                            style="accent-color:var(--blue);width:14px;height:14px;">
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group span-3">
                            <label>Access to Toilet Facilities</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;">
                                @foreach($toiletOpts as $val => $lbl)
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-800);">
                                        <input type="radio" name="toilet_access" value="{{ $val }}"
                                            @checked(old('toilet_access', $household->toilet_access) == $val)
                                            style="accent-color:var(--blue);width:14px;height:14px;">
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group span-3">
                            <label>Waste Disposal System</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;">
                                @foreach($wasteOpts as $val => $lbl)
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-800);">
                                        <input type="radio" name="waste_disposal" value="{{ $val }}"
                                            @checked(old('waste_disposal', $household->waste_disposal) == $val)
                                            style="accent-color:var(--blue);width:14px;height:14px;">
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Nuclear Families & Members ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot"></div>
                    <div class="card-title">Nuclear Families &amp; Members</div>
                    <span style="font-size:11px;color:var(--gray-400);margin-left:auto;">Edit each member's information below</span>
                </div>
                <div class="form-card-body" style="padding:16px;">

                    @foreach($household->nuclearFamilies as $nfIdx => $nf)
                    <div class="nf-edit-card">
                        <div class="nf-edit-header">
                            <div class="nf-edit-title">
                                <span class="nf-num">{{ $nfIdx + 1 }}</span>
                                Nuclear Family — {{ $nf->family_name ?: 'Unnamed' }}
                                @if($nf->is_primary)
                                    <span class="nf-primary-badge">Primary</span>
                                @endif
                            </div>
                        </div>
                        <div class="nf-edit-body">

                            {{-- Nuclear family meta fields --}}
                            <input type="hidden" name="families[{{ $nf->id }}][id]" value="{{ $nf->id }}">

                            <div class="form-grid" style="margin-bottom:16px;">
                                <div class="form-group">
                                    <label>Family Name / Surname</label>
                                    <input type="text" name="families[{{ $nf->id }}][family_name]" class="form-control"
                                        value="{{ $nf->family_name }}"
                                        {{ $nf->is_primary ? 'readonly style=background:var(--gray-50)' : '' }}>
                                </div>
                                <div class="form-group">
                                    <label>Family Type</label>
                                    <select name="families[{{ $nf->id }}][family_type]" class="form-control">
                                        <option value="">— Select —</option>
                                        @foreach(['Nuclear Family','Extended Family','Single Parent Family','Blended Family','Childless Couple','Grandparent-headed','Skipped Generation','Other'] as $ft)
                                            <option value="{{ $ft }}" @selected($nf->family_type == $ft)>{{ $ft }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Name of Family Head</label>
                                    <input type="text" name="families[{{ $nf->id }}][family_head]" class="form-control"
                                        value="{{ $nf->family_head }}"
                                        {{ $nf->is_primary ? 'readonly style=background:var(--gray-50)' : '' }}>
                                </div>
                            </div>

                            {{-- Members --}}
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--gray-600);margin-bottom:10px;padding-bottom:8px;border-bottom:2px solid var(--blue-pale);">
                                Members ({{ $nf->members->count() }})
                            </div>

                            <div class="members-edit-list" id="members_list_{{ $nf->id }}">
                                @foreach($nf->members as $mIdx => $member)
                                <div class="member-edit-row {{ $member->is_family_head ? 'is-head' : '' }}" id="member_row_{{ $member->id }}">
                                    <div class="member-edit-header" onclick="toggleMemberFields({{ $member->id }})">
                                        <div class="member-edit-label">
                                            <span style="color:var(--gray-400);font-size:11px;">{{ $mIdx + 1 }}</span>
                                            {{ $member->full_name ?: 'Member' }}
                                            @if($member->is_family_head)
                                                <span class="head-tag">HEAD</span>
                                            @endif
                                        </div>
                                        <span class="member-edit-toggle" id="member_tog_{{ $member->id }}">▲ Close</span>
                                    </div>
                                    @php
                                        $mid = $member->id;
                                        $det = $member->detail ?? $member->detail()->first();
                                    @endphp
                                    <div class="member-edit-fields" id="member_fields_{{ $mid }}">
                                        <input type="hidden" name="members[{{ $mid }}][id]" value="{{ $mid }}">

                                        <div class="form-group span-2" style="grid-column:span 2;">
                                            <label>Full Name <span class="required">*</span></label>
                                            <input type="text" name="members[{{ $mid }}][full_name]" class="form-control"
                                                value="{{ $member->full_name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Relationship</label>
                                            <select name="members[{{ $mid }}][relationship]" class="form-control"
                                                @if($member->is_family_head) style="background:var(--gray-50);color:var(--gray-600);" @endif>
                                                @foreach(['Head','Spouse','Son','Daughter','Father','Mother','Sibling','Grandchild','Grandparent','Uncle/Aunt','Nephew/Niece','Cousin','In-law','Other'] as $rel)
                                                    <option value="{{ $rel }}" @selected($member->relationship === $rel)>{{ $rel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Sex</label>
                                            <select name="members[{{ $mid }}][sex]" class="form-control">
                                                <option value="">— Select —</option>
                                                <option value="Male"   @selected($member->sex === 'Male')>Male</option>
                                                <option value="Female" @selected($member->sex === 'Female')>Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Birthday</label>
                                            <input type="date" name="members[{{ $mid }}][birthday]"
                                                id="bday_{{ $mid }}"
                                                class="form-control"
                                                value="{{ $member->birthday ? $member->birthday->format('Y-m-d') : '' }}"
                                                oninput="calcAge(this, 'age_{{ $mid }}')">
                                        </div>
                                        <div class="form-group">
                                            <label>Age</label>
                                            <input type="number" id="age_{{ $mid }}" class="form-control"
                                                style="background:var(--gray-50);color:var(--gray-600);"
                                                title="Auto-calculated from Birthdate" readonly tabindex="-1"
                                                value="{{ $member->birthday ? (int)\Carbon\Carbon::parse($member->birthday)->age : '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Civil Status</label>
                                            <select name="members[{{ $mid }}][civil_status]" class="form-control">
                                                <option value="">— Select —</option>
                                                @foreach(['Single','Married','Legally Separated','Widowed'] as $cs)
                                                    <option value="{{ $cs }}" @selected($member->civil_status === $cs)>{{ $cs }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Educational Attainment</label>
                                            <select name="members[{{ $mid }}][educational_attainment]" class="form-control">
                                                <option value="">— Select —</option>
                                                @foreach(['Elementary Undergraduate','Elementary Graduate','High School Undergraduate','High School Graduate','Vocational','College Undergraduate','College Graduate','Master','Doctorate','TESDA','Other'] as $ed)
                                                    <option value="{{ $ed }}" @selected($member->educational_attainment === $ed)>{{ $ed }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Employment Status</label>
                                            <select name="members[{{ $mid }}][employment_status]" class="form-control emp-status-sel"
                                                onchange="onEditEmp(this)">
                                                <option value="">— Select —</option>
                                                @foreach(['Unemployed','Employed','Part-time','Full-time','Self-employed','Pension/Retired','Freelance','Other'] as $emp)
                                                    <option value="{{ $emp }}" @selected($det && $det->employment_status === $emp)>{{ $emp }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group emp-job-group" style="{{ ($det && $det->employment_status && $det->employment_status !== 'Unemployed' && $det->employment_status !== 'Pension/Retired' && $det->employment_status !== 'Other') ? '' : 'display:none' }}">
                                            <label>Job Title</label>
                                            <input type="text" name="members[{{ $mid }}][job_title]" class="form-control"
                                                value="{{ $det?->job_title ?? '' }}" placeholder="If employed">
                                        </div>
                                        <div class="form-group emp-other-group" style="{{ ($det && $det->employment_status === 'Other') ? '' : 'display:none' }}">
                                            <label>Please Specify</label>
                                            <input type="text" name="members[{{ $mid }}][employment_other]" class="form-control"
                                                value="{{ $det?->employment_other ?? '' }}" placeholder="Specify employment type...">
                                        </div>
                                        <div class="form-group">
                                            <label>Vulnerable Sector</label>
                                            <select name="members[{{ $mid }}][vulnerable_sector]" class="form-control">
                                                <option value="">— None —</option>
                                                @foreach(['None','Senior','PWD','Solo Parent','4Ps Member','Young','Old'] as $vs)
                                                    <option value="{{ $vs }}" @selected($det && $det->vulnerable_sector === $vs)>{{ $vs }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" style="display:flex;flex-direction:row;align-items:center;gap:10px;padding-top:22px;">
                                            <label style="display:flex;align-items:center;gap:8px;text-transform:none;letter-spacing:0;font-size:12px;cursor:pointer;">
                                                <input type="checkbox" name="members[{{ $mid }}][is_lgbtqia]" value="1"
                                                    @checked($det && $det->is_lgbtqia)
                                                    style="width:15px;height:15px;accent-color:var(--blue);">
                                                LGBTQIA+
                                            </label>
                                        </div>
                                        <div class="form-group" style="display:flex;flex-direction:row;align-items:center;gap:10px;padding-top:22px;">
                                            <label style="display:flex;align-items:center;gap:8px;text-transform:none;letter-spacing:0;font-size:12px;cursor:pointer;font-weight:700;color:#6D28D9;">
                                                <input type="checkbox" name="members[{{ $mid }}][is_family_head]" value="1"
                                                    @checked($member->is_family_head)
                                                    style="width:15px;height:15px;accent-color:#7C3AED;">
                                                Family Head
                                            </label>
                                            <span style="font-size:10px;color:var(--gray-400);">(check if this member is the head of their nuclear family)</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            {{-- ── Section 3: Risk Profile ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="card-dot" style="background:#C0392B;border-color:#922B21;"></div>
                    <div class="card-title">Section 3 — Household Risk Profile</div>
                </div>
                <div class="form-card-body">
                    @php $rp = $household->riskProfile; @endphp
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Average Monthly Income (₱)</label>
                            <input type="number" name="risk[income_average]" class="form-control @error('risk.income_average') is-error @enderror"
                                value="{{ old('risk.income_average', $rp?->income_average) }}"
                                min="0" step="0.01" placeholder="e.g. 12000.00">
                            @error('risk.income_average')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Literacy Rate (%)</label>
                            <input type="number" name="risk[literacy_rate]" class="form-control @error('risk.literacy_rate') is-error @enderror"
                                value="{{ old('risk.literacy_rate', $rp?->literacy_rate) }}"
                                min="0" max="100" placeholder="0 – 100">
                            @error('risk.literacy_rate')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Remarks / Notes</label>
                            <input type="text" name="risk[remarks]" class="form-control @error('risk.remarks') is-error @enderror"
                                value="{{ old('risk.remarks', $rp?->remarks) }}" placeholder="Optional notes">
                            @error('risk.remarks')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="checkbox-grid" style="margin-top:16px;">
                        <label class="checkbox-item">
                            <input type="checkbox" name="risk[early_warning]" value="1" @checked(old('risk.early_warning', $rp?->early_warning))>
                            <span>Has Early Warning System</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="risk[hazard_awareness]" value="1" @checked(old('risk.hazard_awareness', $rp?->hazard_awareness))>
                            <span>Hazard Aware</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="risk[financial_assistance]" value="1" @checked(old('risk.financial_assistance', $rp?->financial_assistance))>
                            <span>Receives Financial Assistance</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="risk[access_info]" value="1" @checked(old('risk.access_info', $rp?->access_info))>
                            <span>Has Access to Information</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="risk[relocate_willingness]" value="1" @checked(old('risk.relocate_willingness', $rp?->relocate_willingness))>
                            <span>Willing to Relocate</span>
                        </label>
                    </div>

                    @if($rp?->early_warning)
                    <div class="form-group" style="margin-top:16px;">
                        <label>EWS Sources (comma-separated)</label>
                        <input type="text" name="risk[ews_sources]" class="form-control"
                            value="{{ old('risk.ews_sources', $rp?->ews_sources) }}"
                            placeholder="e.g. tv,radio,brgy,other">
                        <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">Options: tv, radio, brgy, other</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── Form Actions ── --}}
            <div class="form-actions">
                <a href="{{ route('encoder.households.show', $household) }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save All Changes
                </button>
            </div>

        </form>

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
    /* ─── Age auto-calculator ─── */
    function calcAge(bdayInput, ageFieldId){
        const ageEl = document.getElementById(ageFieldId);
        if(!ageEl) return;
        if(!bdayInput.value){ ageEl.value=''; return; }
        const today = new Date();
        const bday  = new Date(bdayInput.value);
        let age = today.getFullYear() - bday.getFullYear();
        const m = today.getMonth() - bday.getMonth();
        if(m < 0 || (m === 0 && today.getDate() < bday.getDate())) age--;
        ageEl.value = age >= 0 ? age : '';
    }

    // Re-calculate all ages on page load from existing birthday values
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('input[type="date"][id^="bday_"]').forEach(function(input){
            if(input.value){
                const mid = input.id.replace('bday_', '');
                calcAge(input, 'age_' + mid);
            }
        });
    });

    function pad(n){return String(n).padStart(2,'0');}
    function updateClock(){
        const now=new Date();
        const D=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const M=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent=pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent=D[now.getDay()]+', '+pad(now.getDate())+' '+M[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock(); setInterval(updateClock,1000);
    document.getElementById('footer-year').textContent=new Date().getFullYear();

    const sidebar=document.getElementById('sidebar'), overlay=document.getElementById('sidebarOverlay');
    function openSidebar(){sidebar.classList.add('open');overlay.classList.add('active');document.body.style.overflow='hidden';}
    function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('active');document.body.style.overflow='';}
    document.addEventListener('keydown',e=>{if(e.key==='Escape')closeSidebar();});

    function toggleMemberFields(id){
        const fields=document.getElementById('member_fields_'+id);
        const tog=document.getElementById('member_tog_'+id);
        const collapsed=fields.classList.toggle('collapsed');
        tog.textContent=collapsed?'▼ Edit':'▲ Close';
    }

    /* ─── Employment Status: show/hide job title or "other" field ─── */
    const EMPLOYED_OPTS = ['Employed','Part-time','Full-time','Self-employed','Freelance'];
    function onEditEmp(sel){
        const row = sel.closest('.form-group').parentElement;
        const jobGroup   = row.querySelector('.emp-job-group');
        const otherGroup = row.querySelector('.emp-other-group');
        const v = sel.value;
        if(v === 'Other'){
            if(jobGroup)   jobGroup.style.display   = 'none';
            if(otherGroup) otherGroup.style.display = '';
        } else if(EMPLOYED_OPTS.includes(v)){
            if(jobGroup)   jobGroup.style.display   = '';
            if(otherGroup) otherGroup.style.display = 'none';
        } else {
        /* ─── Valid ID type toggle ─── */
    function onEditValidIdType(sel){
        const wrap  = document.getElementById('edit-valid-id-num-wrap');
        const input = document.getElementById('edit-inp-valid-id-num');
        if(sel.value){
            wrap.style.display = 'block';
            input.placeholder  = `Enter ${sel.value} number`;
        } else {
            wrap.style.display = 'none';
            input.value        = '';
        }
    }
</script>
</body>
</html>