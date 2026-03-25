@extends('superadmin.superadmin')

@section('title', 'Super Admin Dashboard')

@push('styles')
<style>
/* ══ WELCOME BANNER ══ */
.welcome-banner {
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-mid) 60%, #1a3a70 100%);
    border-left: 4px solid var(--yellow);
    border-radius: 8px; overflow: hidden;
    padding: 22px 28px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 20px;
    position: relative;
    box-shadow: 0 4px 20px rgba(18,45,90,.2);
}
.welcome-banner::after {
    content: ''; position: absolute; right: -30px; top: -30px;
    width: 180px; height: 180px; border-radius: 50%;
    background: rgba(255,255,255,.03); pointer-events: none;
}
.wb-logo {
    width: 52px; height: 52px; object-fit: contain;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,.3)); flex-shrink: 0;
}
.wb-text {}
.wb-eyebrow {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.5px; color: rgba(255,255,255,.45); margin-bottom: 4px;
}
.wb-heading {
    font-family: 'PT Serif', serif; font-size: 21px; font-weight: 700;
    color: var(--white); line-height: 1.2;
}
.wb-heading em { color: var(--yellow); font-style: normal; }
.wb-sub { font-size: 12px; color: rgba(255,255,255,.45); margin-top: 3px; }
.wb-meta {
    margin-left: auto; display: flex; flex-direction: column;
    gap: 8px; text-align: right; flex-shrink: 0;
}
.wb-meta-item {}
.wb-meta-label {
    font-size: 9px; text-transform: uppercase; letter-spacing: 1px;
    color: rgba(255,255,255,.3); font-weight: 700; margin-bottom: 1px;
}
.wb-meta-val { font-size: 11px; color: rgba(255,255,255,.65); font-weight: 500; }

/* ══ STAT CARDS ══ */
.dash-stats {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 12px; margin-bottom: 18px;
}
.dash-stat {
    background: var(--white); border: 1px solid var(--gray-200);
    border-radius: 8px; padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
    position: relative; overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.dash-stat::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 3px; border-radius: 8px 8px 0 0; background: var(--gray-200);
}
.dash-stat.c-blue::before   { background: var(--blue); }
.dash-stat.c-green::before  { background: var(--green); }
.dash-stat.c-orange::before { background: var(--orange); }
.dash-stat.c-gold::before   { background: linear-gradient(90deg, var(--super-mid), var(--yellow)); }
.dash-stat:hover { box-shadow: 0 6px 20px rgba(27,63,122,.1); transform: translateY(-2px); }
.ds-ico {
    width: 40px; height: 40px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.ds-ico svg { width: 18px; height: 18px; }
.ds-ico.blue   { background: var(--blue-pale);   color: var(--blue); }
.ds-ico.green  { background: var(--green-pale);  color: var(--green); }
.ds-ico.orange { background: var(--orange-pale); color: var(--orange); }
.ds-ico.gold   { background: var(--super-pale2); color: var(--super-mid); }
.ds-lbl  { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 4px; line-height: 1; }
.ds-num  { font-size: 26px; font-weight: 800; color: var(--gray-800); line-height: 1; font-variant-numeric: tabular-nums; }
.ds-num.green  { color: var(--green-dark); }
.ds-num.orange { color: var(--orange); }
.ds-num.gold   { color: var(--super-mid); }

/* ══ QUICK NAV ══ */
.qnav-grid {
    display: grid; grid-template-columns: repeat(3,1fr);
    gap: 10px; margin-bottom: 18px;
}
.qnav-item {
    background: var(--white); border: 1px solid var(--gray-200);
    border-radius: 8px; padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
    text-decoration: none; transition: all .15s;
    border-top: 3px solid var(--blue);
}
.qnav-item:hover { box-shadow: 0 4px 14px rgba(27,63,122,.1); border-top-color: var(--yellow); transform: translateY(-1px); }
.qnav-item.danger { border-top-color: var(--red); }
.qnav-item.danger:hover { border-top-color: var(--red); box-shadow: 0 4px 14px rgba(192,57,43,.12); }
.qnav-ico {
    width: 34px; height: 34px; border-radius: 7px;
    background: var(--blue-pale); color: var(--blue);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: background .15s;
}
.qnav-ico svg { width: 16px; height: 16px; }
.qnav-item.danger .qnav-ico { background: var(--red-pale); color: var(--red); }
.qnav-title { font-size: 12.5px; font-weight: 700; color: var(--blue-dark); }
.qnav-desc  { font-size: 11px; color: var(--gray-500); margin-top: 2px; }

/* ══ CONTENT CARDS ══ */
.content-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 14px; margin-bottom: 14px;
}
.cc {
    background: var(--white); border: 1px solid var(--gray-200);
    border-radius: 8px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.cc-header {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 18px; border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
}
.cc-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.cc-title { font-size: 13px; font-weight: 700; color: var(--blue-dark); }
.cc-action { margin-left: auto; font-size: 11px; font-weight: 600; color: var(--blue-light); text-decoration: none; }
.cc-action:hover { color: var(--blue); }

/* Role bar row */
.role-bar-row {
    display: flex; align-items: center; gap: 14px;
    padding: 11px 18px; border-bottom: 1px solid var(--gray-100);
}
.role-bar-row:last-child { border-bottom: none; }
.rbar-icon {
    width: 32px; height: 32px; border-radius: 6px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.rbar-icon svg { width: 15px; height: 15px; }
.rbar-body { flex: 1; min-width: 0; }
.rbar-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
.rbar-name { font-size: 12.5px; font-weight: 600; color: var(--gray-700); }
.rbar-count { font-size: 12px; font-weight: 800; }
.rbar-track { height: 5px; background: var(--gray-100); border-radius: 3px; overflow: hidden; }
.rbar-fill  { height: 100%; border-radius: 3px; transition: width .4s ease; }
.rbar-pct { font-size: 11px; color: var(--gray-400); width: 32px; text-align: right; flex-shrink: 0; }

/* Recent account row */
.recent-row {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 18px; border-bottom: 1px solid var(--gray-100);
}
.recent-row:last-child { border-bottom: none; }
.recent-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    background: var(--blue-dark); display: flex; align-items: center;
    justify-content: center; font-weight: 800; color: var(--yellow); font-size: 13px;
}
.recent-name { font-size: 12.5px; font-weight: 600; color: var(--blue-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.recent-email { font-size: 11px; color: var(--gray-400); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.recent-meta { flex-shrink: 0; text-align: right; }
.recent-ago { font-size: 10px; color: var(--gray-400); margin-top: 3px; }

/* ══ BOTTOM INFO ROW ══ */
.info-row {
    display: grid; grid-template-columns: repeat(3,1fr);
    gap: 14px;
}
.info-card {
    background: var(--white); border: 1px solid var(--gray-200);
    border-radius: 8px; overflow: hidden;
}
.info-card-header {
    display: flex; align-items: center; gap: 9px;
    padding: 11px 16px; border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
}
.info-card-title { font-size: 12.5px; font-weight: 700; color: var(--blue-dark); }
.info-card-body  { padding: 16px 18px; }

/* Logged-in badge */
.li-avatar {
    width: 42px; height: 42px; border-radius: 50%;
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-light) 100%);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; color: var(--yellow); font-size: 16px; flex-shrink: 0;
}

/* System status */
.sys-status-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--green); box-shadow: 0 0 5px var(--green);
    animation: blink 2s infinite; flex-shrink: 0;
}

/* Responsive */
@media (max-width: 1100px) {
    .dash-stats   { grid-template-columns: repeat(2,1fr); }
    .qnav-grid    { grid-template-columns: repeat(2,1fr); }
    .content-grid { grid-template-columns: 1fr; }
    .info-row     { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
    .dash-stats { grid-template-columns: 1fr 1fr; }
    .info-row   { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- ══ PAGE TITLE ══ --}}
<div class="page-titlebar">
    <div>
        <div class="page-breadcrumb">
            Home <span class="bc-sep">›</span>
            <span class="bc-link">Dashboard</span>
        </div>
        <div class="page-h1">Dashboard Overview</div>
        <div class="page-sub">Super Administrator — MDRRMO RBI System, Naic, Cavite</div>
    </div>
    <div class="page-date-badge">
        <span class="day">{{ now()->format('l') }}</span>
        <span class="full-date">{{ now()->format('F j, Y') }}</span>
    </div>
</div>

{{-- ══ WELCOME BANNER ══ --}}
<div class="welcome-banner">
    <img src="{{ asset('/images/mdrrmo-logo.png') }}" alt="MDRRMO" class="wb-logo"
         onerror="this.style.display='none'">
    <div class="wb-text">
        <div class="wb-eyebrow">Welcome Back</div>
        <div class="wb-heading">Good day, <em>{{ Auth::user()->name }}!</em></div>
        <div class="wb-sub">Super Administrator — Full system control &amp; user account management</div>
    </div>
    <div class="wb-meta">
        <div class="wb-meta-item">
            <div class="wb-meta-label">Last Login</div>
            <div class="wb-meta-val">
                {{ Auth::user()->last_login_at
                    ? Auth::user()->last_login_at->format('M d, Y · h:i A')
                    : 'First login' }}
            </div>
        </div>
        <div class="wb-meta-item">
            <div class="wb-meta-label">System Time</div>
            <div class="wb-meta-val">{{ now()->format('h:i A') }} · PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</div>
        </div>
    </div>
</div>

{{-- ══ STAT CARDS ══ --}}
<div class="dash-stats">

    <div class="dash-stat c-blue">
        <div class="ds-ico blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <div class="ds-lbl">Total Accounts</div>
            <div class="ds-num">{{ number_format($stats['total']) }}</div>
        </div>
    </div>

    <div class="dash-stat c-green">
        <div class="ds-ico green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="ds-lbl">Active</div>
            <div class="ds-num green">{{ number_format($stats['active']) }}</div>
        </div>
    </div>

    <div class="dash-stat c-orange">
        <div class="ds-ico orange">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <div>
            <div class="ds-lbl">Inactive</div>
            <div class="ds-num orange">{{ number_format($stats['inactive']) }}</div>
        </div>
    </div>

    <div class="dash-stat c-gold">
        <div class="ds-ico gold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <div>
            <div class="ds-lbl">Roles in System</div>
            <div class="ds-num gold">{{ $stats['by_role']->count() }}</div>
        </div>
    </div>

</div>

{{-- ══ QUICK NAV ══ --}}
<div class="qnav-grid">

    <a href="{{ route('superadmin.accounts.create') }}" class="qnav-item">
        <div class="qnav-ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        </div>
        <div>
            <div class="qnav-title">Send Invite</div>
            <div class="qnav-desc">Add admin, encoder, staff or auditor</div>
        </div>
    </a>

    <a href="{{ route('superadmin.accounts.index') }}" class="qnav-item">
        <div class="qnav-ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <div class="qnav-title">Manage Accounts</div>
            <div class="qnav-desc">Activate, deactivate or remove accounts</div>
        </div>
    </a>

    <a href="{{ route('superadmin.roles.index') }}" class="qnav-item">
        <div class="qnav-ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <div>
            <div class="qnav-title">Role Permissions</div>
            <div class="qnav-desc">View privileges assigned to each role</div>
        </div>
    </a>

    <a href="{{ route('superadmin.trails.index') }}" class="qnav-item">
        <div class="qnav-ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="qnav-title">Trail Logs</div>
            <div class="qnav-desc">Recent system activity summary</div>
        </div>
    </a>

    <a href="{{ route('superadmin.trails.advanced') }}" class="qnav-item">
        <div class="qnav-ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        </div>
        <div>
            <div class="qnav-title">Advanced Audit Logs</div>
            <div class="qnav-desc">Full live audit log across all users</div>
        </div>
    </a>

    <a href="{{ route('superadmin.accounts.index') }}?status=inactive"
       class="qnav-item {{ $stats['inactive'] > 0 ? 'danger' : '' }}">
        <div class="qnav-ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <div>
            <div class="qnav-title">
                Inactive Accounts
                @if($stats['inactive'] > 0)
                    <span style="color:var(--red); font-size:11px; margin-left:4px;">({{ $stats['inactive'] }})</span>
                @endif
            </div>
            <div class="qnav-desc">Review deactivated or pending accounts</div>
        </div>
    </a>

</div>

{{-- ══ ROLE BREAKDOWN + RECENT ACCOUNTS ══ --}}
<div class="content-grid">

    {{-- Role breakdown --}}
    <div class="cc">
        <div class="cc-header">
            <span class="cc-dot" style="background:var(--super-mid);"></span>
            <span class="cc-title">Accounts by Role</span>
            <a href="{{ route('superadmin.accounts.index') }}" class="cc-action">View All →</a>
        </div>
        @php
            $roleMeta = [
                'admin'   => ['label'=>'Administrator', 'ico_bg'=>'#EAF0FA', 'ico_color'=>'#1B3F7A', 'bar'=>'#1B3F7A',
                    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
                'encoder' => ['label'=>'Encoder',        'ico_bg'=>'#E0F2FE', 'ico_color'=>'#0369A1', 'bar'=>'#0369A1',
                    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                'staff'   => ['label'=>'Staff',           'ico_bg'=>'#DCFCE7', 'ico_color'=>'#16A34A', 'bar'=>'#16A34A',
                    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                'auditor' => ['label'=>'Auditor',         'ico_bg'=>'#FFF7ED', 'ico_color'=>'#D97706', 'bar'=>'#D97706',
                    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
            ];
        @endphp
        @foreach($roleMeta as $roleKey => $meta)
        @php
            $count = $stats['by_role'][$roleKey] ?? 0;
            $pct   = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
        @endphp
        <div class="role-bar-row">
            <div class="rbar-icon" style="background:{{ $meta['ico_bg'] }}; color:{{ $meta['ico_color'] }};">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">{!! $meta['icon'] !!}</svg>
            </div>
            <div class="rbar-body">
                <div class="rbar-top">
                    <span class="rbar-name">{{ $meta['label'] }}</span>
                    <span class="rbar-count" style="color:{{ $meta['bar'] }};">{{ number_format($count) }}</span>
                </div>
                <div class="rbar-track">
                    <div class="rbar-fill" style="width:{{ $pct }}%; background:{{ $meta['bar'] }};"></div>
                </div>
            </div>
            <span class="rbar-pct">{{ $pct }}%</span>
        </div>
        @endforeach
    </div>

    {{-- Recently created --}}
    <div class="cc">
        <div class="cc-header">
            <span class="cc-dot" style="background:var(--yellow-dark);"></span>
            <span class="cc-title">Recently Created Accounts</span>
            <a href="{{ route('superadmin.accounts.create') }}" class="cc-action" style="color:var(--green-dark);">+ New</a>
        </div>
        @php
            $roleColors = [
                'admin'   => ['bg'=>'#EAF0FA','color'=>'#1B3F7A'],
                'encoder' => ['bg'=>'#E0F2FE','color'=>'#0369A1'],
                'staff'   => ['bg'=>'#DCFCE7','color'=>'#15803D'],
                'auditor' => ['bg'=>'#FFF7ED','color'=>'#B45309'],
            ];
        @endphp
        @forelse($recentUsers as $u)
        @php $rc = $roleColors[$u->role] ?? ['bg'=>'#F0F3F7','color'=>'#5A6372']; @endphp
        <div class="recent-row">
            <div class="recent-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
            <div style="flex:1; min-width:0;">
                <div class="recent-name">{{ $u->name }}</div>
                <div class="recent-email">{{ $u->email }}</div>
            </div>
            <div class="recent-meta">
                <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;background:{{ $rc['bg'] }};color:{{ $rc['color'] }};">
                    {{ ucfirst($u->role) }}
                </span>
                <div class="recent-ago">{{ $u->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div style="padding:36px;text-align:center;color:var(--gray-400);font-size:12px;font-style:italic;">
            No accounts created yet.
        </div>
        @endforelse
    </div>

</div>

{{-- ══ BOTTOM INFO ROW ══ --}}
<div class="info-row">

    {{-- Logged in as --}}
    <div class="info-card">
        <div class="info-card-header">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--super-mid);flex-shrink:0;"></span>
            <span class="info-card-title">Logged In As</span>
        </div>
        <div class="info-card-body" style="display:flex; align-items:center; gap:14px;">
            <div class="li-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div style="font-size:13px; font-weight:700; color:var(--blue-dark);">{{ Auth::user()->name }}</div>
                <div style="font-size:11px; color:var(--gray-400); margin-top:1px;">{{ Auth::user()->email }}</div>
                <span style="display:inline-flex;align-items:center;gap:5px;background:var(--super-pale2);color:var(--super-mid);padding:2px 8px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;margin-top:6px;border:1px solid var(--super-border);">
                    <span style="width:5px;height:5px;border-radius:50%;background:var(--super-mid);"></span>
                    Super Administrator
                </span>
            </div>
        </div>
    </div>

    {{-- Last login --}}
    <div class="info-card">
        <div class="info-card-header">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--yellow-dark);flex-shrink:0;"></span>
            <span class="info-card-title">Last Login</span>
        </div>
        <div class="info-card-body">
            <div style="font-family:'PT Serif',serif; font-size:22px; font-weight:700; color:var(--blue-dark); line-height:1.1;">
                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('M d, Y') : 'First login' }}
            </div>
            <div style="font-size:12px; color:var(--gray-400); margin-top:4px;">
                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('h:i A') : 'Welcome to the system' }}
            </div>
        </div>
    </div>

    {{-- System status --}}
    <div class="info-card">
        <div class="info-card-header">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--green);flex-shrink:0;"></span>
            <span class="info-card-title">System</span>
        </div>
        <div class="info-card-body">
            <div style="font-size:13px; font-weight:700; color:var(--blue-dark);">MDRRMO RBI System</div>
            <div style="font-size:11px; color:var(--gray-400); margin-top:2px;">Naic, Cavite &nbsp;·&nbsp; PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</div>
            <div style="display:flex; align-items:center; gap:6px; margin-top:10px; font-size:11px; color:var(--green); font-weight:700;">
                <span class="sys-status-dot"></span>
                System Online
            </div>
        </div>
    </div>

</div>

@endsection