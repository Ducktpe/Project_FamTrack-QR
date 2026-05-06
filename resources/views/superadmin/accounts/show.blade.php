@extends('superadmin.superadmin')

@section('title', 'Account Details')

@push('styles')
<style>
/* ── Detail row used in info panels ── */
.detail-row {
    display: flex; align-items: center;
    justify-content: space-between; gap: 12px;
    padding: 11px 0;
    border-bottom: 1px solid var(--gray-200);
}
.detail-row:last-child { border-bottom: none; }
.detail-key {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .9px; color: var(--gray-400); flex-shrink: 0;
}
.detail-val {
    font-size: 13px; font-weight: 600; color: var(--gray-700);
    text-align: right;
}
.detail-val.mono {
    font-family: 'Courier New', monospace;
    background: var(--gray-100); padding: 3px 8px;
    border-radius: 4px; font-size: 12px; color: var(--blue-dark);
}
.detail-val.muted { color: var(--gray-400); font-weight: 500; font-style: italic; }

/* ── Profile hero in identity panel ── */
.acct-hero {
    display: flex; align-items: center; gap: 16px;
    padding: 20px 20px 16px; border-bottom: 1px solid var(--gray-200);
}
.acct-hero-avatar {
    width: 54px; height: 54px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 800; color: var(--white);
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-light) 100%);
    box-shadow: 0 4px 14px rgba(27,63,122,.25);
}
.acct-hero-name {
    font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700;
    color: var(--blue-dark); line-height: 1.2;
}
.acct-hero-email {
    font-size: 12px; color: var(--gray-400); margin-top: 3px;
    font-family: 'Courier New', monospace;
}

/* ── Privilege list ── */
.priv-list { list-style: none; padding: 14px 20px; margin: 0; }
.priv-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 8px 0; border-bottom: 1px solid var(--gray-100);
    font-size: 13px; color: var(--gray-700);
}
.priv-item:last-child { border-bottom: none; }
.priv-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--green); flex-shrink: 0; margin-top: 4px;
}

/* ── Log table timestamp ── */
.log-ts { font-size: 11px; color: var(--gray-400); white-space: nowrap; line-height: 1.5; }
.log-ts strong { color: var(--gray-700); font-size: 12px; display: block; }

/* ── Show page two-column layout ── */
.show-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }

/* ── Mobile ── */
@media (max-width: 900px) {
    .show-two-col { grid-template-columns: 1fr; }
    .detail-key { font-size: 10px; }
    .detail-val { font-size: 12px; }
    .detail-val.mono { font-size: 11px; }
    .acct-hero-name { font-size: 16px; }
}
@media (max-width: 640px) {
    /* Page action buttons wrap nicely */
    .show-action-bar { flex-wrap: wrap; justify-content: flex-start !important; }
    .detail-row { flex-direction: column; align-items: flex-start; gap: 4px; }
    .detail-val { text-align: left; }
    .acct-hero { padding: 14px; gap: 12px; }
    .acct-hero-avatar { width: 44px; height: 44px; font-size: 16px; }
    .priv-list { padding: 12px 14px; }
}
</style>
@endpush

@section('content')

{{-- ══ PAGE TITLE ══ --}}
<div class="page-titlebar">
    <div>
        <div class="page-breadcrumb">
            Super Admin <span class="bc-sep">›</span>
            <a href="{{ route('superadmin.accounts.index') }}" class="bc-link" style="text-decoration:none;">Account Management</a>
            <span class="bc-sep">›</span>
            <span class="bc-link">Account Details</span>
        </div>
        <div class="page-h1">Account Details</div>
        <div class="page-sub">Full profile and activity for this system account.</div>
    </div>
    <div style="display:flex; gap:8px; align-items:center; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end;" class="show-action-bar">
        <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>

        @if($user->is_setup_complete)
        <form method="POST" action="{{ route('superadmin.accounts.toggle', $user) }}" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-sm {{ $user->status === 'active' ? 'btn-ghost' : 'btn-primary' }}"
                onclick="return confirm('{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }} this account?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
        @endif

        @if(!$user->is_setup_complete)
        <form method="POST" action="{{ route('superadmin.accounts.resend', $user) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm"
                onclick="return confirm('Resend invite to {{ $user->personal_email }}?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Resend Invite
            </button>
        </form>
        @endif

        <form method="POST" action="{{ route('superadmin.accounts.destroy', $user) }}" class="d-inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm"
                style="background:var(--red-pale);color:var(--red);border:1px solid #FECACA;"
                onclick="return confirm('Archive account {{ addslashes($user->email) }}?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12"/></svg>
                Archive
            </button>
        </form>
    </div>
</div>

{{-- ══ TWO-COLUMN LAYOUT ══ --}}
<div class="show-two-col">

    {{-- ── LEFT COLUMN ── --}}
    <div style="display:flex; flex-direction:column; gap:18px;">

        {{-- Account Identity --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot blue"></span> Account Identity</div>
                <span class="badge badge-{{ $user->role }}">
                    <span class="badge-dot"></span>{{ ucfirst($user->role) }}
                </span>
            </div>

            <div class="acct-hero">
                <div class="acct-hero-avatar">
                    {{ strtoupper(substr($user->is_setup_complete ? $user->name : '?', 0, 1)) }}
                </div>
                <div>
                    @if($user->is_setup_complete)
                        <div class="acct-hero-name">{{ $user->name }}</div>
                    @else
                        <div class="acct-hero-name" style="color:var(--gray-400); font-style:italic; font-size:15px;">Not set up yet</div>
                    @endif
                    <div class="acct-hero-email">{{ $user->email }}</div>
                </div>
            </div>

            <div style="padding:0 20px 4px;">
                <div class="detail-row">
                    <span class="detail-key">Login Email</span>
                    <span class="detail-val mono">{{ $user->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Account Code</span>
                    <span class="detail-val {{ $user->account_code ? 'mono' : 'muted' }}">{{ $user->account_code ?? 'Not assigned' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Personal Gmail</span>
                    <span class="detail-val {{ $user->personal_email ? '' : 'muted' }}">{{ $user->personal_email ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Role</span>
                    <span class="detail-val">{{ $user->roleLabel() }}</span>
                </div>
            </div>
        </div>

        {{-- Status Info --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot green"></span> Status Information</div>
            </div>
            <div style="padding:0 20px 4px;">
                <div class="detail-row">
                    <span class="detail-key">Account Status</span>
                    <span class="badge badge-{{ $user->status }}">
                        <span class="badge-dot"></span>{{ ucfirst($user->status) }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Setup Status</span>
                    @if($user->is_setup_complete)
                        <span class="badge badge-active"><span class="badge-dot"></span>Complete</span>
                    @elseif($user->invite_expires_at?->isFuture())
                        <span class="badge badge-pending"><span class="badge-dot"></span>Invite Sent</span>
                    @else
                        <span class="badge badge-inactive"><span class="badge-dot"></span>Invite Expired</span>
                    @endif
                </div>
                <div class="detail-row">
                    <span class="detail-key">Last Login</span>
                    <span class="detail-val {{ $user->last_login_at ? '' : 'muted' }}">
                        {{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never logged in' }}
                    </span>
                </div>
                @if(!$user->is_setup_complete && $user->invite_expires_at)
                <div class="detail-row">
                    <span class="detail-key">Invite Expires</span>
                    <span class="detail-val" style="color:{{ $user->invite_expires_at->isFuture() ? 'var(--green)' : 'var(--red)' }}">
                        {{ $user->invite_expires_at->format('M d, Y h:i A') }}
                        <span style="font-size:11px; font-weight:500; display:block;">{{ $user->invite_expires_at->diffForHumans() }}</span>
                    </span>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div style="display:flex; flex-direction:column; gap:18px;">

        {{-- Account History --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot orange"></span> Account History</div>
            </div>
            <div style="padding:0 20px 4px;">
                <div class="detail-row">
                    <span class="detail-key">Invited By</span>
                    <span class="detail-val">{{ $user->creator?->name ?? 'System' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Date Invited</span>
                    <span class="detail-val">{{ $user->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Account Age</span>
                    <span class="detail-val muted">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-key">Last Updated</span>
                    <span class="detail-val muted">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- Role Privileges --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot yellow"></span> Role Privileges</div>
                <span class="badge badge-{{ $user->role }}">
                    <span class="badge-dot"></span>{{ $user->roleLabel() }}
                </span>
            </div>
            <ul class="priv-list">
                @foreach($user->rolePrivileges() as $priv)
                <li class="priv-item">
                    <span class="priv-dot"></span>
                    {{ $priv }}
                </li>
                @endforeach
            </ul>
        </div>

    </div>
</div>

{{-- ══ RECENT ACTIVITY LOGS ══ --}}
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <span class="panel-dot blue"></span>
            Recent Activity Logs
        </div>
        <span style="font-size:12px; color:var(--gray-400); font-weight:500;">Last 10 entries</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                <tr>
                    <td data-label="Timestamp">
                        <div class="log-ts">
                            {{ $log->created_at->format('M d, Y') }}
                            <strong>{{ $log->created_at->format('h:i A') }}</strong>
                        </div>
                    </td>
                    <td data-label="Action">
                        @php
                            $cls = match(true) {
                                str_starts_with($log->action, 'created'),
                                str_starts_with($log->action, 'sent'),
                                str_starts_with($log->action, 'restored') => 'trail-create',
                                str_starts_with($log->action, 'updated'),
                                str_starts_with($log->action, 'toggled')  => 'trail-update',
                                str_starts_with($log->action, 'deleted'),
                                str_starts_with($log->action, 'permanently') => 'trail-delete',
                                str_starts_with($log->action, 'login'),
                                str_starts_with($log->action, 'account') => 'trail-login',
                                default => 'trail-system',
                            };
                        @endphp
                        <span class="trail-type {{ $cls }}">{{ $log->action }}</span>
                    </td>
                    <td data-label="Description" style="font-size:12px; color:var(--gray-600); max-width:320px;">{{ $log->description }}</td>
                    <td data-label="IP Address"><span class="ip-code">{{ $log->ip_address ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-state">No activity logs found for this account.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection