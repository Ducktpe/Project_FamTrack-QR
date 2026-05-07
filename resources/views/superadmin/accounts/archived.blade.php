@extends('superadmin.superadmin')

@section('title', 'Archived Accounts')

@push('styles')
<style>
/* ── Warning notice banner ── */
.archive-notice {
    display: flex; align-items: flex-start; gap: 14px;
    background: var(--orange-pale);
    border: 1px solid #FED7AA;
    border-left: 4px solid var(--orange);
    border-radius: 0 6px 6px 0;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 13px;
    color: #7C2D12;
}
.archive-notice-icon {
    width: 36px; height: 36px; border-radius: 8px;
    background: rgba(217,119,6,.15); color: var(--orange);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.archive-notice-icon svg { width: 18px; height: 18px; }
.archive-notice strong { color: #92400e; }

/* ── Archived table row styling ── */
.archived-row { opacity: .82; }
.archived-row:hover { opacity: 1; background: var(--orange-pale) !important; }

/* ── Archive-specific action buttons ── */
.icon-btn.restore:hover {
    border-color: var(--green); color: var(--green);
    background: var(--green-pale);
}
.icon-btn.perma-delete:hover {
    border-color: var(--red); color: var(--red);
    background: var(--red-pale);
}

/* ── Archived date cell ── */
.arch-date { font-size: 12px; color: var(--gray-500); line-height: 1.5; }
.arch-date span { font-size: 11px; color: var(--gray-400); display: block; }

/* ── Mobile ── */
@media (max-width: 640px) {
    .archive-notice { flex-direction: column; gap: 10px; padding: 12px 14px; font-size: 12px; }
    .archive-notice-icon { width: 30px; height: 30px; }
}

/* ── Action Modals ── */
.arch-modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center; }
.arch-modal-backdrop.show { display:flex; }
.arch-modal-box { background:var(--white); border-radius:6px; box-shadow:0 8px 32px rgba(0,0,0,0.22); width:100%; max-width:420px; margin:16px; overflow:hidden; animation:archModalIn .18s ease; }
@keyframes archModalIn { from{opacity:0;transform:scale(.96)} to{opacity:1;transform:scale(1)} }
.arch-modal-header { padding:18px 22px 14px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--gray-100); }
.arch-modal-icon { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.arch-modal-icon svg { width:18px; height:18px; }
.arch-modal-icon.restore  { background:var(--green-pale); color:var(--green); }
.arch-modal-icon.delete   { background:var(--red-pale);   color:var(--red);   }
.arch-modal-title { font-family:'PT Serif',serif; font-size:16px; font-weight:700; color:var(--blue-dark); }
.arch-modal-body  { padding:14px 22px 20px; font-size:13px; color:var(--gray-600); line-height:1.65; }
.arch-modal-body strong { color:var(--gray-800); }
.arch-modal-footer { padding:12px 22px; background:var(--gray-50); border-top:1px solid var(--gray-100); display:flex; justify-content:flex-end; gap:8px; }
.arch-modal-btn { font-family:'Open Sans',sans-serif; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; padding:9px 20px; border-radius:3px; border:none; cursor:pointer; transition:background .15s; }
.arch-modal-btn-cancel  { background:var(--white); color:var(--gray-600); border:1px solid var(--gray-200); }
.arch-modal-btn-cancel:hover  { background:var(--gray-100); }
.arch-modal-btn-restore { background:var(--green); color:var(--white); }
.arch-modal-btn-restore:hover { background:var(--green-dark); }
.arch-modal-btn-delete  { background:var(--red);   color:var(--white); }
.arch-modal-btn-delete:hover  { filter:brightness(.9); }
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
            <span class="bc-link">Archived Accounts</span>
        </div>
        <div class="page-h1">Archived Accounts</div>
        <div class="page-sub">Soft-deleted accounts. Restore to re-activate, or permanently remove.</div>
    </div>
    <div style="display:flex; gap:8px; align-items:center; flex-shrink:0;">
        <div class="page-date-badge" style="margin-right:8px;">
            <span class="day">{{ now()->format('l') }}</span>
            <span class="full-date">{{ now()->format('F j, Y') }}</span>
        </div>
        <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Active Accounts
        </a>
    </div>
</div>

{{-- ══ WARNING NOTICE ══ --}}
<div class="archive-notice">
    <div class="archive-notice-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <div>
        <strong>These accounts are archived, not permanently deleted.</strong><br/>
        You can <strong>restore</strong> them to make them active again, or <strong>permanently delete</strong> them —
        which <em>cannot be undone</em>.
    </div>
</div>

{{-- ══ TABLE PANEL ══ --}}
<div class="panel">

    {{-- Panel header --}}
    <div class="panel-header">
        <div class="panel-title">
            <span class="panel-dot orange"></span>
            Archived Accounts
            @if($archivedUsers->total() > 0)
                <small>({{ $archivedUsers->total() }} total)</small>
            @endif
        </div>
        <span class="super-only-badge">Super Admin Only</span>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('superadmin.accounts.archived') }}" style="display:contents;">
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
            <div style="align-self:flex-end; display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search','role']))
                    <a href="{{ route('superadmin.accounts.archived') }}" class="btn btn-cancel btn-sm">Clear</a>
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
                    <th>Invited By</th>
                    <th>Archived On</th>
                    <th style="width:90px; text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($archivedUsers as $u)
                <tr class="archived-row">
                    <td data-label="#">
                        {{ $loop->iteration + ($archivedUsers->currentPage()-1) * $archivedUsers->perPage() }}
                    </td>

                    <td data-label="Account">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--gray-200);color:var(--gray-500);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;">
                                {{ strtoupper(substr($u->email, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--gray-600);">
                                    {{ $u->is_setup_complete ? $u->name : '— Not set up —' }}
                                </div>
                                <div style="font-size:11px; color:var(--gray-400); font-family:'Courier New',monospace;">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td data-label="Personal Gmail" style="font-size:12px; color:var(--gray-400);">{{ $u->personal_email ?? '—' }}</td>

                    <td data-label="Role">
                        <span class="badge badge-{{ $u->role }}" style="opacity:.75;">
                            <span class="badge-dot"></span>{{ ucfirst($u->role) }}
                        </span>
                    </td>

                    <td data-label="Invited By" style="font-size:12px; color:var(--gray-500);">{{ $u->creator?->name ?? '—' }}</td>

                    <td data-label="Archived On">
                        <div class="arch-date">
                            {{ $u->deleted_at->format('M d, Y') }}
                            <span>{{ $u->deleted_at->diffForHumans() }}</span>
                        </div>
                    </td>

                    <td data-label="Actions">
                        <div class="action-btns" style="justify-content:center;">
                            {{-- Restore --}}
                            <button type="button" class="icon-btn restore" title="Restore Account"
                                onclick="openRestoreModal(
                                    '{{ route('superadmin.accounts.restore', $u->id) }}',
                                    '{{ addslashes($u->email) }}'
                                )">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </button>

                            {{-- Permanent Delete --}}
                            <button type="button" class="icon-btn perma-delete" title="Permanently Delete"
                                onclick="openDeleteModal(
                                    '{{ route('superadmin.accounts.force-delete', $u->id) }}',
                                    '{{ addslashes($u->email) }}'
                                )">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:48px 20px; text-align:center;">
                        <div style="width:48px;height:48px;background:var(--gray-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--gray-400);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12"/></svg>
                        </div>
                        <div style="font-size:14px; font-weight:700; color:var(--gray-600); margin-bottom:5px;">No archived accounts</div>
                        <div style="font-size:12px; color:var(--gray-400);">
                            @if(request()->anyFilled(['search','role']))
                                No archived accounts match your filters.
                                <a href="{{ route('superadmin.accounts.archived') }}" style="color:var(--blue);font-weight:600;">Clear filters</a>
                            @else
                                No accounts have been archived yet.
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($archivedUsers->hasPages())
    <div class="pagination-bar">
        <span class="pagination-info">
            Showing {{ $archivedUsers->firstItem() }}–{{ $archivedUsers->lastItem() }}
            of {{ $archivedUsers->total() }} archived accounts
        </span>
        <div class="pagination-links">
            {{ $archivedUsers->appends(request()->query())->links() }}
        </div>
    </div>
    @endif

</div>


{{-- ══ RESTORE MODAL ══ --}}
<div class="arch-modal-backdrop" id="restoreModal">
    <div class="arch-modal-box">
        <div class="arch-modal-header">
            <div class="arch-modal-icon restore">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div class="arch-modal-title">Restore Account</div>
        </div>
        <div class="arch-modal-body" id="restoreModalBody"></div>
        <div class="arch-modal-footer">
            <button class="arch-modal-btn arch-modal-btn-cancel" onclick="closeArchModal('restoreModal')">Cancel</button>
            <button class="arch-modal-btn arch-modal-btn-restore" onclick="submitArchForm('restoreForm')">Restore</button>
        </div>
    </div>
</div>

{{-- ══ PERMANENT DELETE MODAL ══ --}}
<div class="arch-modal-backdrop" id="deleteModal">
    <div class="arch-modal-box">
        <div class="arch-modal-header">
            <div class="arch-modal-icon delete">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div class="arch-modal-title">Permanently Delete</div>
        </div>
        <div class="arch-modal-body" id="deleteModalBody"></div>
        <div class="arch-modal-footer">
            <button class="arch-modal-btn arch-modal-btn-cancel" onclick="closeArchModal('deleteModal')">Cancel</button>
            <button class="arch-modal-btn arch-modal-btn-delete" onclick="submitArchForm('deleteForm')">Delete Permanently</button>
        </div>
    </div>
</div>

{{-- Hidden forms --}}
<form id="restoreForm" method="POST" style="display:none;">
    @csrf
</form>
<form id="deleteForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

@push('scripts')
<script>
    function openRestoreModal(url, email) {
        document.getElementById('restoreForm').action = url;
        document.getElementById('restoreModalBody').innerHTML =
            `Restore account <strong>${email}</strong>?<br>
             <span style="font-size:12px;color:var(--gray-400);margin-top:6px;display:block;">
                The account will be set to <strong>inactive</strong> and the user will need to log in to reactivate.
             </span>`;
        openArchModal('restoreModal');
    }

    function openDeleteModal(url, email) {
        document.getElementById('deleteForm').action = url;
        document.getElementById('deleteModalBody').innerHTML =
            `<strong style="color:var(--red);">This action cannot be undone.</strong><br><br>
             Permanently delete account <strong>${email}</strong>?<br>
             <span style="font-size:12px;color:var(--gray-400);margin-top:6px;display:block;">
                All data associated with this account will be <strong>irreversibly removed</strong> from the system.
             </span>`;
        openArchModal('deleteModal');
    }

    function openArchModal(id) {
        document.getElementById(id).classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeArchModal(id) {
        document.getElementById(id).classList.remove('show');
        document.body.style.overflow = '';
    }

    function submitArchForm(formId) {
        document.getElementById(formId).submit();
    }

    // Close on backdrop click
    document.querySelectorAll('.arch-modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) closeArchModal(this.id);
        });
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.arch-modal-backdrop.show')
                .forEach(el => closeArchModal(el.id));
        }
    });
</script>
@endpush

@endsection