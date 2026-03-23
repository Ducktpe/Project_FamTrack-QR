@extends('superadmin.superadmin')

@section('title', 'Account Management')

@section('content')

<div class="page-header-row">
    <div>
        <div class="breadcrumb">Super Admin &rsaquo; <span>Account Management</span></div>
        <h2 class="page-title">Account Management</h2>
        <p class="page-sub">Send invites and manage system user accounts for all roles.</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <div class="today-label">
            <span>{{ now()->format('l') }}</span>
            <strong>{{ now()->format('F j, Y') }}</strong>
        </div>
        <a href="{{ route('superadmin.accounts.archived') }}" class="btn btn-ghost" style="color:var(--orange);border-color:var(--orange);">🗂️ Archived</a>
        <a href="{{ route('superadmin.accounts.create') }}" class="btn btn-super">
            📧 Send Invite
        </a>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stat-row" style="grid-template-columns:repeat(5,1fr);">
    <div class="stat-card" style="border-top-color:#6b7a99;">
        <div class="stat-label">Total Accounts</div>
        <div class="stat-val" style="color:#1a2a4a;">{{ $users->total() }}</div>
        <div class="stat-desc">All roles</div>
    </div>
    <div class="stat-card c-blue">
        <div class="stat-label">Admins</div>
        <div class="stat-val blue">{{ \App\Models\User::where('role','admin')->count() }}</div>
    </div>
    <div class="stat-card c-green">
        <div class="stat-label">Encoders</div>
        <div class="stat-val green">{{ \App\Models\User::where('role','encoder')->count() }}</div>
    </div>
    <div class="stat-card c-purple">
        <div class="stat-label">Staff</div>
        <div class="stat-val purple">{{ \App\Models\User::where('role','staff')->count() }}</div>
    </div>
    <div class="stat-card c-orange">
        <div class="stat-label">Pending Setup</div>
        <div class="stat-val orange">{{ $pendingUsers }}</div>
        <div class="stat-desc">Invite sent, not set up yet</div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="panel">
    <div class="filter-bar">
        <form method="GET" action="{{ route('superadmin.accounts.index') }}"
              style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;width:100%;">

            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input wide"
                       placeholder="Name, login email, or Gmail…"
                       value="{{ request('search') }}"/>
            </div>

            <div class="filter-group">
                <label class="filter-label">Role</label>
                <select name="role" class="filter-select" style="width:140px;">
                    <option value="">All Roles</option>
                    <option value="admin"   @selected(request('role')=='admin')>Admin</option>
                    <option value="encoder" @selected(request('role')=='encoder')>Encoder</option>
                    <option value="staff"   @selected(request('role')=='staff')>Staff</option>
                    <option value="auditor" @selected(request('role')=='auditor')>Auditor</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select" style="width:140px;">
                    <option value="">All Status</option>
                    <option value="active"   @selected(request('status')=='active')>Active</option>
                    <option value="inactive" @selected(request('status')=='inactive')>Inactive</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Setup</label>
                <select name="setup" class="filter-select" style="width:150px;">
                    <option value="">All</option>
                    <option value="complete"  @selected(request('setup')=='complete')>Setup Complete</option>
                    <option value="pending"   @selected(request('setup')=='pending')>Pending Setup</option>
                </select>
            </div>

            <div class="filter-group" style="align-self:flex-end;">
                <button type="submit" class="btn btn-primary">🔍 Filter</button>
            </div>

            @if(request()->anyFilled(['search','role','status','setup']))
            <div class="filter-group" style="align-self:flex-end;">
                <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-ghost">✕ Clear</a>
            </div>
            @endif
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Login Email</th>
                    <th>Personal Gmail</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Setup</th>
                    <th>Invited By</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    {{-- Row number --}}
                    <td style="color:var(--muted);font-size:12px;">
                        {{ $loop->iteration + ($users->currentPage()-1) * $users->perPage() }}
                    </td>

                    {{-- Login email --}}
                    <td>
                        <span style="font-family:'Courier New',monospace;font-size:12px;font-weight:600;color:var(--navy);">
                            {{ $u->email }}
                        </span>
                        @if($u->account_code)
                        <span style="font-size:10px;color:var(--muted);display:block;">
                            Code: {{ $u->account_code }}
                        </span>
                        @endif
                    </td>

                    {{-- Personal Gmail --}}
                    <td style="font-size:12px;color:var(--muted);">
                        {{ $u->personal_email ?? '—' }}
                    </td>

                    {{-- Full name --}}
                    <td style="font-weight:600;">
                        @if($u->is_setup_complete)
                            {{ $u->name }}
                        @else
                            <span style="color:var(--muted);font-style:italic;font-size:12px;">Not set up yet</span>
                        @endif
                    </td>

                    {{-- Role --}}
                    <td>
                        <span class="badge badge-{{ $u->role }}">
                            <span class="badge-dot"></span>{{ ucfirst($u->role) }}
                        </span>
                    </td>

                    {{-- Account status --}}
                    <td>
                        <span class="badge badge-{{ $u->status }}">
                            <span class="badge-dot"></span>{{ ucfirst($u->status) }}
                        </span>
                    </td>

                    {{-- Setup status --}}
                    <td>
                        @if($u->is_setup_complete)
                            <span class="badge badge-active">
                                <span class="badge-dot"></span> Complete
                            </span>
                        @else
                            @if($u->invite_expires_at && $u->invite_expires_at->isFuture())
                                <span class="badge badge-pending" title="Expires {{ $u->invite_expires_at->diffForHumans() }}">
                                    <span class="badge-dot"></span> Invite Sent
                                </span>
                            @else
                                <span class="badge badge-inactive">
                                    <span class="badge-dot"></span> Invite Expired
                                </span>
                            @endif
                        @endif
                    </td>

                    {{-- Invited by --}}
                    <td style="color:var(--muted);font-size:12px;">
                        {{ $u->creator?->name ?? '—' }}
                    </td>

                    {{-- Last login --}}
                    <td style="color:var(--muted);font-size:12px;">
                        {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="action-btns">

                            {{-- View Details --}}
                            <a href="{{ route('superadmin.accounts.show', $u) }}" class="icon-btn view" title="View Details">👁️</a>

                            {{-- Resend Invite (only if setup not complete) --}}
                            @if(! $u->is_setup_complete)
                            <form method="POST"
                                  action="{{ route('superadmin.accounts.resend', $u) }}"
                                  class="d-inline">
                                @csrf
                                <button type="submit" class="icon-btn edit"
                                        title="Resend Invite"
                                        onclick="return confirm('Resend invite to {{ $u->personal_email }}? This will reset the 24-hour timer.')">
                                    📧
                                </button>
                            </form>
                            @endif

                            {{-- Toggle Status (only if setup complete) --}}
                            @if($u->is_setup_complete)
                            <form method="POST"
                                  action="{{ route('superadmin.accounts.toggle', $u) }}"
                                  class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="icon-btn lock"
                                        title="{{ $u->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                        onclick="return confirm('{{ $u->status === 'active' ? 'Deactivate' : 'Activate' }} {{ addslashes($u->name) }}?')">
                                    {{ $u->status === 'active' ? '🔒' : '🔓' }}
                                </button>
                            </form>
                            @endif

                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('superadmin.accounts.destroy', $u) }}"
                                  class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn delete"
                                        title="Delete Account"
                                        onclick="return confirm('Permanently delete account {{ addslashes($u->email) }}? This cannot be undone.')">
                                    🗑️
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="empty-state">No accounts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($users->hasPages())
    <div class="pagination-bar">
        <span class="pagination-info">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} accounts
        </span>
        <div class="pagination-links">
            {{ $users->links() }}
        </div>
    </div>
    @endif

</div>

@endsection