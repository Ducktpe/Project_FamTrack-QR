@extends('superadmin.superadmin')

@section('title', 'Super Admin Dashboard')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header-row">
    <div>
        <div class="breadcrumb">Super Admin &rsaquo; <span>Dashboard</span></div>
        <h2 class="page-title">Dashboard</h2>
        <p class="page-sub">Welcome back, {{ Auth::user()->name }}. Here's the system overview.</p>
    </div>
    <div class="today-label">
        <span>{{ now()->format('l') }}</span>
        <strong>{{ now()->format('F j, Y') }}</strong>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stat-row">

    <div class="stat-card c-blue">
        <div class="stat-icon">👥</div>
        <div class="stat-label">Total Accounts</div>
        <div class="stat-val blue">{{ $stats['total'] }}</div>
        <div class="stat-desc">All roles combined</div>
    </div>

    <div class="stat-card c-green">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Active Accounts</div>
        <div class="stat-val green">{{ $stats['active'] }}</div>
        <div class="stat-desc">Currently enabled</div>
    </div>

    <div class="stat-card c-orange">
        <div class="stat-icon">🔒</div>
        <div class="stat-label">Inactive Accounts</div>
        <div class="stat-val orange">{{ $stats['inactive'] }}</div>
        <div class="stat-desc">Deactivated / pending</div>
    </div>

    <div class="stat-card c-purple">
        <div class="stat-icon">🔐</div>
        <div class="stat-label">Roles in System</div>
        <div class="stat-val purple">{{ $stats['by_role']->count() }}</div>
        <div class="stat-desc">Admin, Encoder, Staff, Auditor</div>
    </div>

</div>

{{-- ROLE BREAKDOWN + RECENT ACCOUNTS (2-column) --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;">

    {{-- ROLE BREAKDOWN --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot purple"></span>
                Accounts by Role
            </div>
            <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-ghost btn-sm">View All →</a>
        </div>

        @php
            $roleMeta = [
                'admin'   => ['icon' => '🛡️', 'label' => 'Administrator', 'badge' => 'badge-admin'],
                'encoder' => ['icon' => '📝', 'label' => 'Encoder',        'badge' => 'badge-encoder'],
                'staff'   => ['icon' => '📡', 'label' => 'Staff',          'badge' => 'badge-staff'],
                'auditor' => ['icon' => '🔍', 'label' => 'Auditor',        'badge' => 'badge-auditor'],
            ];
        @endphp

        <div style="padding:8px 0;">
            @foreach($roleMeta as $roleKey => $meta)
            @php $count = $stats['by_role'][$roleKey] ?? 0; @endphp
            <div style="display:flex;align-items:center;gap:14px;padding:12px 20px;border-bottom:1px solid var(--border);">
                <span style="font-size:20px;width:28px;text-align:center;">{{ $meta['icon'] }}</span>
                <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                        <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $meta['label'] }}</span>
                        <span class="badge {{ $meta['badge'] }}">{{ $count }} {{ Str::plural('user', $count) }}</span>
                    </div>
                    {{-- Progress bar --}}
                    @php $pct = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0; @endphp
                    <div style="height:5px;background:#eef1f8;border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:var(--blue-acc);border-radius:3px;transition:width .4s ease;"></div>
                    </div>
                </div>
                <span style="font-size:12px;color:var(--muted);width:32px;text-align:right;">{{ $pct }}%</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- RECENTLY CREATED ACCOUNTS --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot blue"></span>
                Recently Created Accounts
            </div>
            <a href="{{ route('superadmin.accounts.create') }}" class="btn btn-super btn-sm">➕ New</a>
        </div>

        @forelse($recentUsers as $u)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);">
            {{-- Avatar --}}
            <div style="width:36px;height:36px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--gold);font-size:14px;flex-shrink:0;">
                {{ strtoupper(substr($u->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $u->name }}
                </div>
                <div style="font-size:11px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $u->email }}
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <span class="badge badge-{{ $u->role }}" style="margin-bottom:3px;display:inline-flex;">
                    <span class="badge-dot"></span>{{ ucfirst($u->role) }}
                </span>
                <div style="font-size:10px;color:var(--muted);">
                    {{ $u->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
        @empty
        <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;font-style:italic;">
            No accounts created yet.
        </div>
        @endforelse
    </div>

</div>

{{-- QUICK ACTIONS --}}
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <span class="panel-dot orange"></span>
            Quick Actions
            <span class="super-only-badge">SUPER ADMIN ONLY</span>
        </div>
    </div>
    <div class="quick-grid">

        <a href="{{ route('superadmin.accounts.create') }}" class="quick-card">
            <span class="quick-icon">➕</span>
            <div>
                <div class="quick-label">Create Account</div>
                <div class="quick-desc">Add a new admin, encoder, staff, or auditor</div>
            </div>
        </a>

        <a href="{{ route('superadmin.accounts.index') }}" class="quick-card">
            <span class="quick-icon">👤</span>
            <div>
                <div class="quick-label">Manage Accounts</div>
                <div class="quick-desc">Activate, deactivate, or remove accounts</div>
            </div>
        </a>

        <a href="{{ route('superadmin.roles.index') }}" class="quick-card">
            <span class="quick-icon">🔐</span>
            <div>
                <div class="quick-label">Role Permissions</div>
                <div class="quick-desc">View privileges assigned to each role</div>
            </div>
        </a>

        <a href="{{ route('superadmin.trails.advanced') }}" class="quick-card">
            <span class="quick-icon">🕵️</span>
            <div>
                <div class="quick-label">Advanced Trail Logs</div>
                <div class="quick-desc">Full live audit log across all users</div>
            </div>
        </a>

        <a href="{{ route('superadmin.trails.index') }}" class="quick-card">
            <span class="quick-icon">🔍</span>
            <div>
                <div class="quick-label">Trail Logs</div>
                <div class="quick-desc">Recent system activity summary</div>
            </div>
        </a>

        {{-- Inactive accounts shortcut --}}
        <a href="{{ route('superadmin.accounts.index') }}?status=inactive" class="quick-card">
            <span class="quick-icon">🔒</span>
            <div>
                <div class="quick-label">Inactive Accounts</div>
                <div class="quick-desc">
                    Review deactivated accounts
                    @if($stats['inactive'] > 0)
                        <span style="color:var(--red);font-weight:700;">({{ $stats['inactive'] }} pending)</span>
                    @endif
                </div>
            </div>
        </a>

    </div>
</div>

{{-- SYSTEM INFO FOOTER --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">

    <div class="panel" style="margin-bottom:0;">
        <div style="padding:16px 20px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:6px;">Logged In As</div>
            <div style="font-size:14px;font-weight:700;color:var(--navy);">{{ Auth::user()->name }}</div>
            <div style="font-size:12px;color:var(--muted);">{{ Auth::user()->email }}</div>
            <span class="badge badge-super" style="margin-top:6px;">
                <span class="badge-dot"></span> Super Administrator
            </span>
        </div>
    </div>

    <div class="panel" style="margin-bottom:0;">
        <div style="padding:16px 20px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:6px;">Last Login</div>
            <div style="font-size:14px;font-weight:700;color:var(--navy);">
                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('M d, Y') : 'First login' }}
            </div>
            <div style="font-size:12px;color:var(--muted);">
                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('h:i A') : '' }}
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:0;">
        <div style="padding:16px 20px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:6px;">System</div>
            <div style="font-size:14px;font-weight:700;color:var(--navy);">MDRRMO RBI System</div>
            <div style="font-size:12px;color:var(--muted);">Naic, Cavite &nbsp;·&nbsp; PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</div>
        </div>
    </div>

</div>

@endsection