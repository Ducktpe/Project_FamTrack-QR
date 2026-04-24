@extends('superadmin.superadmin')

@section('title', 'Role Permissions')

@push('styles')
<style>
/* ── Role card grid ── */
.roles-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

/* ── Role card ── */
.role-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    transition: box-shadow .2s, transform .2s;
    display: flex;
    flex-direction: column;
}
.role-card:hover {
    box-shadow: 0 6px 20px rgba(27,63,122,.09);
    transform: translateY(-2px);
}

/* Coloured top accent per role */
.role-card.role-admin   { border-top: 3px solid var(--blue); }
.role-card.role-encoder { border-top: 3px solid #0369A1; }
.role-card.role-staff   { border-top: 3px solid var(--green); }
.role-card.role-auditor { border-top: 3px solid var(--orange); }

/* ── Card header ── */
.rc-header {
    padding: 16px 20px 14px;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.rc-header-left { display: flex; align-items: center; gap: 12px; }

/* Role icon box */
.rc-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rc-icon svg { width: 20px; height: 20px; }
.rc-icon.admin   { background: var(--blue-pale);   color: var(--blue); }
.rc-icon.encoder { background: #E0F2FE;             color: #0369A1; }
.rc-icon.staff   { background: var(--green-pale);  color: var(--green-dark); }
.rc-icon.auditor { background: var(--orange-pale); color: var(--orange); }

.rc-title-block {}
.rc-label {
    font-family: 'PT Serif', serif;
    font-size: 16px; font-weight: 700;
    color: var(--blue-dark); line-height: 1.2;
}
.rc-sub {
    font-size: 11px; color: var(--gray-400);
    margin-top: 2px; font-weight: 500;
}

/* Account count chip */
.rc-count {
    display: flex; flex-direction: column;
    align-items: flex-end; flex-shrink: 0;
}
.rc-count-num {
    font-size: 22px; font-weight: 800;
    color: var(--gray-700); line-height: 1;
    font-variant-numeric: tabular-nums;
}
.rc-count-lbl {
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .9px;
    color: var(--gray-400); margin-top: 1px;
}

/* ── Privilege list ── */
.rc-body { padding: 14px 20px; flex: 1; }

.rc-section-label {
    font-size: 9.5px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 1.2px;
    color: var(--gray-400); margin-bottom: 10px;
    display: flex; align-items: center; gap: 8px;
}
.rc-section-label::after {
    content: ''; flex: 1; height: 1px;
    background: var(--gray-200);
}

.priv-list { list-style: none; padding: 0; margin: 0; }
.priv-item {
    display: flex; align-items: flex-start;
    gap: 10px; padding: 8px 0;
    border-bottom: 1px solid var(--gray-100);
    font-size: 13px; color: var(--gray-700);
    line-height: 1.4;
}
.priv-item:last-child { border-bottom: none; }
.priv-check {
    width: 16px; height: 16px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
}
.priv-check svg { width: 10px; height: 10px; }
.priv-check.admin   { background: var(--blue-pale);   color: var(--blue); }
.priv-check.encoder { background: #E0F2FE;             color: #0369A1; }
.priv-check.staff   { background: var(--green-pale);  color: var(--green-dark); }
.priv-check.auditor { background: var(--orange-pale); color: var(--orange); }

/* ── Card footer ── */
.rc-footer {
    padding: 12px 20px;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex; align-items: center; justify-content: space-between;
}
.rc-footer-stat {
    font-size: 11px; color: var(--gray-400); font-weight: 500;
}
.rc-footer-stat strong {
    color: var(--gray-700); font-weight: 700;
}

/* ── Responsive ── */
@media (max-width: 900px)  { .roles-grid { grid-template-columns: 1fr; } }
@media (max-width: 640px)  {
    .roles-grid { grid-template-columns: 1fr; gap: 10px; }
    .rc-header { padding: 12px 14px; flex-wrap: wrap; gap: 8px; }
    .rc-header-left { gap: 8px; }
    .rc-icon { width: 36px; height: 36px; border-radius: 8px; }
    .rc-label { font-size: 14px; }
    .rc-sub   { font-size: 10.5px; }
    .rc-count-num { font-size: 18px; }
    .rc-body  { padding: 12px 14px; }
    .priv-item { font-size: 12px; padding: 6px 0; }
    .rc-footer { padding: 10px 14px; flex-direction: column; align-items: flex-start; gap: 8px; }
    .rc-footer .btn { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')

{{-- ══ PAGE TITLE ══ --}}
<div class="page-titlebar">
    <div>
        <div class="page-breadcrumb">
            Super Admin <span class="bc-sep">›</span>
            <span class="bc-link">Role Permissions</span>
        </div>
        <div class="page-h1">Role Permissions</div>
        <div class="page-sub">Privileges and access levels assigned to each system role.</div>
    </div>
    <div class="page-date-badge">
        <span class="day">{{ now()->format('l') }}</span>
        <span class="full-date">{{ now()->format('F j, Y') }}</span>
    </div>
</div>

{{-- ══ ROLE ICONS MAP ══ --}}
@php
$roleIcons = [
    'admin' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
    'encoder' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
    'staff'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>',
    'auditor' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
];
$roleSubtitles = [
    'admin'   => 'Full management access across the system',
    'encoder' => 'Data entry and household record management',
    'staff'   => 'Field operations and QR scanning',
    'auditor' => 'Read-only audit and compliance view',
];
@endphp

{{-- ══ ROLE GRID ══ --}}
<div class="roles-grid">
    @foreach($roles as $roleKey => $role)
    <div class="role-card role-{{ $roleKey }}">

        {{-- Header ── --}}
        <div class="rc-header">
            <div class="rc-header-left">
                <div class="rc-icon {{ $roleKey }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        {!! $roleIcons[$roleKey] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>' !!}
                    </svg>
                </div>
                <div class="rc-title-block">
                    <div class="rc-label">{{ $role['label'] }}</div>
                    <div class="rc-sub">{{ $roleSubtitles[$roleKey] ?? 'System role' }}</div>
                </div>
            </div>
            <div class="rc-count">
                <div class="rc-count-num">{{ $role['count'] }}</div>
                <div class="rc-count-lbl">{{ Str::plural('account', $role['count']) }}</div>
            </div>
        </div>

        {{-- Privilege list ── --}}
        <div class="rc-body">
            <div class="rc-section-label">Allowed Actions</div>
            <ul class="priv-list">
                @foreach($role['privileges'] as $priv)
                <li class="priv-item">
                    <span class="priv-check {{ $roleKey }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    {{ $priv }}
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Footer ── --}}
        <div class="rc-footer">
            <span class="rc-footer-stat">
                <strong>{{ $role['count'] }}</strong> {{ Str::plural('user', $role['count']) }} assigned
            </span>
            <a href="{{ route('superadmin.accounts.index') }}?role={{ $roleKey }}"
               class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                View {{ $role['label'] }} Accounts
            </a>
        </div>

    </div>
    @endforeach
</div>

@endsection