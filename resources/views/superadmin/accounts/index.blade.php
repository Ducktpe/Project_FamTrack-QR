@extends('superadmin.superadmin')

@section('title', 'Account Management')

@push('styles')
<style>
/* ── Stat cards row ── */
.acct-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 22px;
}
.acct-stat {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
    position: relative; overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.acct-stat::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px; border-radius: 8px 8px 0 0;
    background: var(--gray-300);
}
.acct-stat.c-blue::before   { background: var(--blue); }
.acct-stat.c-green::before  { background: var(--green); }
.acct-stat.c-gold::before   { background: linear-gradient(90deg, var(--super-mid), var(--yellow)); }
.acct-stat.c-orange::before { background: var(--orange); }
.acct-stat:hover { box-shadow: 0 6px 20px rgba(27,63,122,.1); transform: translateY(-2px); }

.acct-stat-icon {
    width: 40px; height: 40px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.acct-stat-icon svg { width: 18px; height: 18px; }
.acct-stat-icon.blue   { background: var(--blue-pale);   color: var(--blue); }
.acct-stat-icon.green  { background: var(--green-pale);  color: var(--green); }
.acct-stat-icon.gold   { background: var(--super-pale2); color: var(--super-mid); }
.acct-stat-icon.orange { background: var(--orange-pale); color: var(--orange); }
.acct-stat-icon.navy   { background: var(--blue-pale);   color: var(--blue-dark); }

.acct-stat-body .lbl {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: var(--gray-400); margin-bottom: 4px; line-height: 1;
}
.acct-stat-body .val {
    font-size: 26px; font-weight: 800; color: var(--gray-800);
    line-height: 1; font-variant-numeric: tabular-nums;
}
.acct-stat-body .val.gold   { color: var(--super-mid); }
.acct-stat-body .val.orange { color: var(--orange); }
.acct-stat-body .sub {
    font-size: 11px; color: var(--gray-400); margin-top: 3px; font-weight: 500;
}

/* ── Name cell ── */
.name-cell { display: flex; align-items: center; gap: 10px; }
.name-avatar {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; letter-spacing: 0;
}
.na-admin   { background: var(--blue-pale);   color: var(--blue); }
.na-encoder { background: var(--green-pale);  color: var(--green-dark); }
.na-staff   { background: var(--super-pale2); color: var(--super-mid); }
.na-auditor { background: var(--yellow-pale); color: #92400e; }
.name-main  { font-size: 13px; font-weight: 600; color: var(--blue-dark); line-height: 1.2; }
.name-sub   { font-size: 11px; color: var(--gray-400); margin-top: 1px; font-family: 'Courier New', monospace; }

/* ── Responsive ── */
@media (max-width: 1200px) { .acct-stats { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 768px)  { .acct-stats { grid-template-columns: repeat(2,1fr); } }
</style>
@endpush

@section('content')

{{-- ══ PAGE TITLE ══ --}}
<div class="page-titlebar">
    <div>
        <div class="page-breadcrumb">
            Super Admin <span class="bc-sep">›</span>
            <span class="bc-link">Account Management</span>
        </div>
        <div class="page-h1">Account Management</div>
        <div class="page-sub">Send invites and manage system user accounts for all roles.</div>
    </div>
    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px; flex-shrink:0;">
        <div class="page-date-badge">
            <span class="day">{{ now()->format('l') }}</span>
            <span class="full-date">{{ now()->format('F j, Y') }}</span>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('superadmin.accounts.archived') }}" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12a1 1 0 102 0 1 1 0 00-2 0z"/></svg>
                Archived
            </a>
            <a href="{{ route('superadmin.accounts.create') }}" class="btn btn-super btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Send Invite
            </a>
        </div>
    </div>
</div>

{{-- ══ STAT CARDS ══ --}}
<div class="acct-stats">

    <div class="acct-stat">
        <div class="acct-stat-icon navy">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="acct-stat-body">
            <div class="lbl">Total Accounts</div>
            <div class="val">{{ $users->total() }}</div>
            <div class="sub">All roles</div>
        </div>
    </div>

    <div class="acct-stat c-blue">
        <div class="acct-stat-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="acct-stat-body">
            <div class="lbl">Admins</div>
            <div class="val">{{ \App\Models\User::where('role','admin')->count() }}</div>
        </div>
    </div>

    <div class="acct-stat c-green">
        <div class="acct-stat-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div class="acct-stat-body">
            <div class="lbl">Encoders</div>
            <div class="val">{{ \App\Models\User::where('role','encoder')->count() }}</div>
        </div>
    </div>

    <div class="acct-stat c-gold">
        <div class="acct-stat-icon gold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div class="acct-stat-body">
            <div class="lbl">Staff</div>
            <div class="val gold">{{ \App\Models\User::where('role','staff')->count() }}</div>
        </div>
    </div>

    <div class="acct-stat c-orange">
        <div class="acct-stat-icon orange">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="acct-stat-body">
            <div class="lbl">Pending Setup</div>
            <div class="val orange">{{ $pendingUsers }}</div>
            <div class="sub">Invite sent, not set up yet</div>
        </div>
    </div>

</div>

{{-- ══ TABLE PANEL ══ --}}
<div class="panel">

    {{-- Panel header --}}
    <div class="panel-header">
        <div class="panel-title">
            <span class="panel-dot blue"></span>
            All Accounts
            <small>({{ $users->total() }} total)</small>
        </div>
        <span class="super-only-badge">Super Admin Only</span>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('superadmin.accounts.index') }}" style="display:contents;">
            <div class="filter-group">
                <span class="filter-label">Search</span>
                <input type="text" name="search" class="filter-input wide"
                       placeholder="Name, login email, or Gmail…"
                       value="{{ request('search') }}"/>
            </div>
            <div class="filter-group">
                <span class="filter-label">Role</span>
                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="admin"   @selected(request('role')=='admin')>Admin</option>
                    <option value="encoder" @selected(request('role')=='encoder')>Encoder</option>
                    <option value="staff"   @selected(request('role')=='staff')>Staff</option>
                    <option value="auditor" @selected(request('role')=='auditor')>Auditor</option>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Status</span>
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active"   @selected(request('status')=='active')>Active</option>
                    <option value="inactive" @selected(request('status')=='inactive')>Inactive</option>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Setup</span>
                <select name="setup" class="filter-select">
                    <option value="">All</option>
                    <option value="complete" @selected(request('setup')=='complete')>Complete</option>
                    <option value="pending"  @selected(request('setup')=='pending')>Pending</option>
                </select>
            </div>
            <div style="align-self:flex-end; display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search','role','status','setup']))
                    <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-cancel btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Account</th>
                    <th>Personal Gmail</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Setup</th>
                    <th>Invited By</th>
                    <th>Last Login</th>
                    <th style="width:110px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td style="color:var(--gray-400); font-size:12px; font-weight:600;">
                        {{ $loop->iteration + ($users->currentPage()-1) * $users->perPage() }}
                    </td>

                    <td>
                        <div class="name-cell">
                            <div class="name-avatar na-{{ $u->role }}">
                                {{ strtoupper(substr($u->is_setup_complete ? $u->name : $u->email, 0, 2)) }}
                            </div>
                            <div>
                                @if($u->is_setup_complete)
                                    <div class="name-main">{{ $u->name }}</div>
                                @else
                                    <div class="name-main" style="color:var(--gray-400); font-style:italic; font-size:12px;">Not set up yet</div>
                                @endif
                                <div class="name-sub">{{ $u->email }}</div>
                                @if($u->account_code)
                                    <div style="font-size:10px; color:var(--gray-400); margin-top:1px;">{{ $u->account_code }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td style="font-size:12px; color:var(--gray-500);">{{ $u->personal_email ?? '—' }}</td>

                    <td>
                        <span class="badge badge-{{ $u->role }}">
                            <span class="badge-dot"></span>{{ ucfirst($u->role) }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-{{ $u->status }}">
                            <span class="badge-dot"></span>{{ ucfirst($u->status) }}
                        </span>
                    </td>

                    <td>
                        @if($u->is_setup_complete)
                            <span class="badge badge-active"><span class="badge-dot"></span>Complete</span>
                        @elseif($u->invite_expires_at?->isFuture())
                            <span class="badge badge-pending" title="Expires {{ $u->invite_expires_at->diffForHumans() }}">
                                <span class="badge-dot"></span>Invite Sent
                            </span>
                        @else
                            <span class="badge badge-inactive"><span class="badge-dot"></span>Expired</span>
                        @endif
                    </td>

                    <td style="font-size:12px; color:var(--gray-500);">{{ $u->creator?->name ?? '—' }}</td>

                    <td style="font-size:12px; color:var(--gray-400);">
                        {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}
                    </td>

                    <td>
                        <div class="action-btns">
                            <a href="{{ route('superadmin.accounts.show', $u) }}" class="icon-btn view" title="View Details">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>

                            @if(!$u->is_setup_complete)
                            <form method="POST" action="{{ route('superadmin.accounts.resend', $u) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="icon-btn edit" title="Resend Invite"
                                    onclick="return confirm('Resend invite to {{ $u->personal_email }}?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </button>
                            </form>
                            @endif

                            @if($u->is_setup_complete)
                            <form method="POST" action="{{ route('superadmin.accounts.toggle', $u) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="icon-btn lock"
                                    title="{{ $u->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                    onclick="return confirm('{{ $u->status === 'active' ? 'Deactivate' : 'Activate' }} {{ addslashes($u->name) }}?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('superadmin.accounts.destroy', $u) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn delete" title="Archive Account"
                                    onclick="return confirm('Archive account {{ addslashes($u->email) }}?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding:48px 20px; text-align:center;">
                        <div style="width:48px;height:48px;background:var(--gray-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--gray-400);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-600);margin-bottom:5px;">No accounts found</div>
                        <div style="font-size:12px;color:var(--gray-400);">
                            @if(request()->anyFilled(['search','role','status','setup']))
                                No accounts match your filters. <a href="{{ route('superadmin.accounts.index') }}" style="color:var(--blue);font-weight:600;">Clear filters</a>
                            @else
                                No accounts yet. Use <strong>Send Invite</strong> to create one.
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="pagination-bar">
        <span class="pagination-info">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} accounts
        </span>
        <div class="pagination-links">{{ $users->appends(request()->query())->links() }}</div>
    </div>
    @endif

</div>

@endsection