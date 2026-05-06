@extends('superadmin.superadmin')

@section('title', 'Advanced Trail Logs')

@push('styles')
<style>
/* ── Live pulse badge ── */
.live-badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 10.5px; font-weight: 800; letter-spacing: .9px;
    text-transform: uppercase; color: var(--green);
    background: var(--green-pale); border: 1px solid #BBF7D0;
    padding: 4px 10px; border-radius: 20px;
}
.live-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--green); box-shadow: 0 0 6px var(--green);
    animation: blink 1.8s infinite; flex-shrink: 0;
}

/* ── Severity badges ── */
.sev-high   { background: var(--red-pale);    color: var(--red);    border: 1px solid #FECACA; }
.sev-medium { background: var(--yellow-pale); color: #92400e;       border: 1px solid #FDE68A; }
.sev-low    { background: var(--green-pale);  color: var(--green-dark); border: 1px solid #BBF7D0; }

/* ── Log timestamp ── */
.log-ts-date { font-size: 11px; color: var(--gray-400); display: block; }
.log-ts-time { font-size: 12px; font-weight: 700; color: var(--gray-700); display: block; margin-top: 1px; }

/* ── User cell ── */
.log-user { display: flex; align-items: center; gap: 9px; }
.log-avatar {
    width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800; color: var(--white);
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-light) 100%);
}
.log-avatar.sys { background: var(--gray-200); color: var(--gray-500); }
.log-user-name { font-size: 12.5px; font-weight: 600; color: var(--gray-700); line-height: 1.2; }
.log-user-role { font-size: 10.5px; color: var(--gray-400); }

/* ── Summary strip ── */
.log-summary {
    display: grid; grid-template-columns: repeat(5, 1fr);
    border-bottom: 1px solid var(--gray-200); background: var(--gray-50);
}
.log-sum-item {
    padding: 13px 16px; border-right: 1px solid var(--gray-200);
    display: flex; align-items: center; gap: 11px;
}
.log-sum-item:last-child { border-right: none; }
.log-sum-icon {
    width: 32px; height: 32px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.log-sum-icon svg { width: 15px; height: 15px; }
.log-sum-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); line-height: 1; }
.log-sum-val   { font-size: 19px; font-weight: 800; color: var(--gray-800); line-height: 1; margin-top: 2px; font-variant-numeric: tabular-nums; }

/* ── Category tag ── */
.cat-tag {
    font-size: 10px; font-weight: 700; color: var(--gray-500);
    background: var(--gray-100); border: 1px solid var(--gray-200);
    padding: 2px 7px; border-radius: 4px; white-space: nowrap;
    text-transform: capitalize;
}

/* ── Table row highlight by severity ── */
tbody tr.sev-row-high   td:first-child { border-left: 3px solid var(--red); }
tbody tr.sev-row-medium td:first-child { border-left: 3px solid var(--yellow-dark); }
tbody tr.sev-row-low    td:first-child { border-left: 3px solid var(--yellow); }

/* ── Advanced: titlebar right side ── */
@media (max-width: 640px) {
    .log-summary { grid-template-columns: repeat(3, 1fr); }
    .log-sum-item { padding: 10px 12px; border-right: none !important; border-bottom: 1px solid var(--gray-200); }
    .log-sum-item:nth-child(1), .log-sum-item:nth-child(2), .log-sum-item:nth-child(4) { border-right: 1px solid var(--gray-200) !important; }
    .log-sum-val { font-size: 16px; }
}
</style>
@endpush

@section('content')

{{-- ══ PAGE TITLE ══ --}}
<div class="page-titlebar">
    <div>
        <div class="page-breadcrumb">
            Super Admin <span class="bc-sep">›</span>
            <span class="bc-link">Advanced Trail Logs</span>
        </div>
        <div class="page-h1">Advanced Trail Logs</div>
        <div class="page-sub">Full system audit — all actions across all roles, all users.</div>
    </div>
    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px; flex-shrink:0;">
        <div class="page-date-badge">
            <span class="day">{{ now()->format('l') }}</span>
            <span class="full-date">{{ now()->format('F j, Y') }}</span>
        </div>
        <span class="live-badge">
            <span class="live-dot"></span>
            Live View
        </span>
    </div>
</div>

{{-- ══ TABLE PANEL ══ --}}
<div class="panel">

    {{-- Panel header --}}
    <div class="panel-header">
        <div class="panel-title">
            <span class="panel-dot green"></span>
            Audit Log
            @if($logs->total() > 0)
                <small>({{ number_format($logs->total()) }} entries)</small>
            @endif
        </div>
        <span class="super-only-badge">Super Admin Only</span>
    </div>

    {{-- Summary strip ── computed from current page ── --}}
    @php
        $col      = $logs->getCollection();
        $creates  = $col->filter(fn($l) => str_starts_with($l->action, 'created') || str_starts_with($l->action, 'sent') || str_starts_with($l->action, 'restored'))->count();
        $updates  = $col->filter(fn($l) => str_starts_with($l->action, 'updated') || str_starts_with($l->action, 'toggled'))->count();
        $deletes  = $col->filter(fn($l) => str_starts_with($l->action, 'deleted'))->count();
        $logins   = $col->filter(fn($l) => str_starts_with($l->action, 'login'))->count();
        $highs    = $col->where('severity', 'high')->count();
    @endphp
    <div class="log-summary">
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--green-pale); color:var(--green);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><div class="log-sum-label">Created</div><div class="log-sum-val">{{ $creates }}</div></div>
        </div>
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--blue-pale); color:var(--blue);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div><div class="log-sum-label">Updated</div><div class="log-sum-val">{{ $updates }}</div></div>
        </div>
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--red-pale); color:var(--red);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div><div class="log-sum-label">Deleted</div><div class="log-sum-val">{{ $deletes }}</div></div>
        </div>
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--super-pale2); color:var(--super-mid);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <div><div class="log-sum-label">Logins</div><div class="log-sum-val">{{ $logins }}</div></div>
        </div>
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--red-pale); color:var(--red);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div><div class="log-sum-label">High Sev.</div><div class="log-sum-val" style="color:var(--red);">{{ $highs }}</div></div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('superadmin.trails.advanced') }}" style="display:contents;">
            <div class="filter-group">
                <span class="filter-label">Search</span>
                <input type="text" name="search" class="filter-input wide"
                       placeholder="Action, user, description…"
                       value="{{ request('search') }}"/>
            </div>
            <div class="filter-group">
                <span class="filter-label">Severity</span>
                <select name="severity" class="filter-select">
                    <option value="">All Severities</option>
                    <option value="low"    @selected(request('severity')=='low')>Low</option>
                    <option value="medium" @selected(request('severity')=='medium')>Medium</option>
                    <option value="high"   @selected(request('severity')=='high')>High</option>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Action Type</span>
                <select name="action" class="filter-select">
                    <option value="">All Actions</option>
                    <option value="created" @selected(request('action')=='created')>Created</option>
                    <option value="updated" @selected(request('action')=='updated')>Updated</option>
                    <option value="deleted" @selected(request('action')=='deleted')>Deleted</option>
                    <option value="login"   @selected(request('action')=='login')>Login</option>
                </select>
            </div>
            <div style="align-self:flex-end; display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search','severity','action']))
                    <a href="{{ route('superadmin.trails.advanced') }}" class="btn btn-cancel btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:130px">Timestamp</th>
                    <th style="width:160px">User</th>
                    <th style="width:140px">Action</th>
                    <th style="width:100px">Severity</th>
                    <th style="width:110px">Category</th>
                    <th>Description</th>
                    <th style="width:110px">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $sevRow = match($log->severity ?? 'low') {
                        'high'   => 'sev-row-high',
                        'medium' => 'sev-row-medium',
                        default  => 'sev-row-low',
                    };
                    $actionClass = match(true) {
                        str_starts_with($log->action, 'created'),
                        str_starts_with($log->action, 'sent'),
                        str_starts_with($log->action, 'restored') => 'trail-create',
                        str_starts_with($log->action, 'updated'),
                        str_starts_with($log->action, 'toggled')  => 'trail-update',
                        str_starts_with($log->action, 'deleted')  => 'trail-delete',
                        str_starts_with($log->action, 'login'),
                        str_starts_with($log->action, 'account')  => 'trail-login',
                        default => 'trail-system',
                    };
                    $sevClass = match($log->severity ?? 'low') {
                        'high'   => 'sev-high',
                        'medium' => 'sev-medium',
                        default  => 'sev-low',
                    };
                @endphp
                <tr class="{{ $sevRow }}">

                    <td data-label="Timestamp">
                        <span class="log-ts-date">{{ $log->created_at->format('M d, Y') }}</span>
                        <span class="log-ts-time">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>

                    <td data-label="User">
                        <div class="log-user">
                            <div class="log-avatar {{ ($log->user_name ?? 'System') === 'System' ? 'sys' : '' }}">
                                {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <div class="log-user-name">{{ $log->user_name ?? 'System' }}</div>
                                @if($log->user)
                                    <div class="log-user-role">{{ ucfirst($log->user->role) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td data-label="Action">
                        <span class="trail-type {{ $actionClass }}">{{ $log->action }}</span>
                    </td>

                    <td data-label="Severity">
                        <span class="badge {{ $sevClass }}">
                            <span class="badge-dot"></span>
                            {{ ucfirst($log->severity ?? 'low') }}
                        </span>
                    </td>

                    <td data-label="Category">
                        <span class="cat-tag">{{ $log->category ?? '—' }}</span>
                    </td>

                    <td data-label="Description" style="font-size:12.5px; color:var(--gray-600);">
                        {{ $log->description }}
                    </td>

                    <td data-label="IP Address">
                        <span class="ip-code">{{ $log->ip_address ?? '—' }}</span>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:48px 20px; text-align:center;">
                        <div style="width:48px;height:48px;background:var(--gray-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--gray-400);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-600);margin-bottom:5px;">No audit log entries found</div>
                        <div style="font-size:12px;color:var(--gray-400);">
                            @if(request()->anyFilled(['search','severity','action']))
                                No entries match your current filters.
                                <a href="{{ route('superadmin.trails.advanced') }}" style="color:var(--blue);font-weight:600;">Clear filters</a>
                            @else
                                System actions will be recorded here automatically.
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="pagination-bar">
        <span class="pagination-info">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries
        </span>
        <div class="pagination-links">{{ $logs->appends(request()->query())->links() }}</div>
    </div>
    @endif

</div>

@endsection