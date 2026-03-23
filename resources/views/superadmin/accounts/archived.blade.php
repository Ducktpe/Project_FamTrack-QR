@extends('superadmin.superadmin')

@section('title', 'Archived Accounts')

@section('content')

<div class="page-header-row">
    <div>
        <div class="breadcrumb">Super Admin &rsaquo; <a href="{{ route('superadmin.accounts.index') }}" style="color:var(--blue-acc);text-decoration:none;">Account Management</a> &rsaquo; <span>Archived Accounts</span></div>
        <h2 class="page-title">Archived Accounts</h2>
        <p class="page-sub">Accounts that have been archived (soft deleted). Restore them or permanently delete.</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <div class="today-label">
            <span>{{ now()->format('l') }}</span>
            <strong>{{ now()->format('F j, Y') }}</strong>
        </div>
        <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-ghost">← Active Accounts</a>
    </div>
</div>

{{-- NOTICE --}}
<div style="background:#fff7ed;border:1px solid #fed7aa;border-left:4px solid #f97316;border-radius:0 8px 8px 0;padding:12px 18px;margin-bottom:22px;font-size:13px;color:#9a3412;">
    ⚠️ <strong>Archived accounts</strong> are not permanently deleted. You can restore them to make them active again, or permanently delete them — which <strong>cannot be undone</strong>.
</div>

<div class="panel">
    {{-- FILTER --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('superadmin.accounts.archived') }}"
              style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;width:100%;">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input wide"
                       placeholder="Name, login email, or Gmail…" value="{{ request('search') }}"/>
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
            <div class="filter-group" style="align-self:flex-end;">
                <button type="submit" class="btn btn-primary">🔍 Filter</button>
            </div>
            @if(request()->anyFilled(['search','role']))
            <div class="filter-group" style="align-self:flex-end;">
                <a href="{{ route('superadmin.accounts.archived') }}" class="btn btn-ghost">✕ Clear</a>
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
                    <th>Full Name</th>
                    <th>Personal Gmail</th>
                    <th>Role</th>
                    <th>Invited By</th>
                    <th>Archived On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($archivedUsers as $u)
                <tr style="opacity:.85;">
                    <td style="color:var(--muted);font-size:12px;">
                        {{ $loop->iteration + ($archivedUsers->currentPage()-1) * $archivedUsers->perPage() }}
                    </td>
                    <td>
                        <span style="font-family:'Courier New',monospace;font-size:12px;font-weight:600;color:var(--muted);">
                            {{ $u->email }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:var(--muted);">
                        {{ $u->is_setup_complete ? $u->name : '— Not set up —' }}
                    </td>
                    <td style="font-size:12px;color:var(--muted);">{{ $u->personal_email ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $u->role }}" style="opacity:.7;">
                            <span class="badge-dot"></span>{{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--muted);">{{ $u->creator?->name ?? '—' }}</td>
                    <td style="font-size:12px;color:var(--muted);">
                        {{ $u->deleted_at->format('M d, Y') }}<br/>
                        <span style="font-size:11px;">{{ $u->deleted_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            {{-- Restore --}}
                            <form method="POST"
                                  action="{{ route('superadmin.accounts.restore', $u->id) }}"
                                  class="d-inline">
                                @csrf
                                <button type="submit" class="icon-btn view" title="Restore Account"
                                    onclick="return confirm('Restore account {{ addslashes($u->email) }}? It will be set to inactive.')">
                                    ♻️
                                </button>
                            </form>

                            {{-- Force Delete --}}
                            <form method="POST"
                                  action="{{ route('superadmin.accounts.force-delete', $u->id) }}"
                                  class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn delete" title="Permanently Delete"
                                    onclick="return confirm('⚠️ PERMANENTLY delete {{ addslashes($u->email) }}? This CANNOT be undone.')">
                                    💀
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">No archived accounts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($archivedUsers->hasPages())
    <div class="pagination-bar">
        <span class="pagination-info">
            Showing {{ $archivedUsers->firstItem() }}–{{ $archivedUsers->lastItem() }} of {{ $archivedUsers->total() }} archived accounts
        </span>
        <div class="pagination-links">{{ $archivedUsers->links() }}</div>
    </div>
    @endif
</div>

@endsection