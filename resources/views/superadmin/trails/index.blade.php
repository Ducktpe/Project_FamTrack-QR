@extends('superadmin.superadmin')

@section('title', 'Trail Logs')

@push('styles')
<style>
/* ── Log timestamp cell ── */
.log-ts { line-height: 1.5; }
.log-ts-date { font-size: 11px; color: var(--gray-400); }
.log-ts-time { font-size: 12px; font-weight: 700; color: var(--gray-700); display: block; }

/* ── User cell ── */
.log-user { display: flex; align-items: center; gap: 9px; }
.log-avatar {
    width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800; color: var(--white);
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-light) 100%);
}
.log-avatar.system { background: var(--gray-200); color: var(--gray-500); }
.log-user-name  { font-size: 12.5px; font-weight: 600; color: var(--gray-700); line-height: 1.2; }
.log-user-role  { font-size: 10.5px; color: var(--gray-400); }

/* ── Summary stat strip ── */
.log-summary {
    display: grid; grid-template-columns: repeat(4, 1fr);
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
}
.log-sum-item {
    padding: 14px 18px; border-right: 1px solid var(--gray-200);
    display: flex; align-items: center; gap: 12px;
}
.log-sum-item:last-child { border-right: none; }
.log-sum-icon {
    width: 34px; height: 34px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.log-sum-icon svg { width: 16px; height: 16px; }
.log-sum-label { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); }
.log-sum-val   { font-size: 20px; font-weight: 800; color: var(--gray-800); line-height: 1; margin-top: 1px; font-variant-numeric: tabular-nums; }

/* ── Mobile ── */
@media (max-width: 900px) {
    .log-summary { grid-template-columns: repeat(2, 1fr); }
    .log-sum-item { border-right: none !important; border-bottom: 1px solid var(--gray-200); padding: 12px 14px; }
    .log-sum-item:nth-child(odd) { border-right: 1px solid var(--gray-200) !important; }
    .log-sum-item:nth-last-child(-n+2) { border-bottom: none; }
}
@media (max-width: 640px) {
    .log-sum-val { font-size: 16px; }
    .log-sum-icon { width: 28px; height: 28px; }
}
</style>
@endpush

@section('content')

{{-- ══ PAGE TITLE ══ --}}
<div class="page-titlebar">
    <div>
        <div class="page-breadcrumb">
            Super Admin <span class="bc-sep">›</span>
            <span class="bc-link">Trail Logs</span>
        </div>
        <div class="page-h1">Trail Logs</div>
        <div class="page-sub">Recent system activity — all tracked actions across the platform.</div>
    </div>
    <div class="page-date-badge">
        <span class="day">{{ now()->format('l') }}</span>
        <span class="full-date">{{ now()->format('F j, Y') }}</span>
    </div>
</div>

{{-- ══ TABLE PANEL ══ --}}
<div class="panel">

    {{-- Panel header --}}
    <div class="panel-header">
        <div class="panel-title">
            <span class="panel-dot blue"></span>
            Activity Log
            @if($logs->total() > 0)
                <small>({{ number_format($logs->total()) }} entries)</small>
            @endif
        </div>
        <a href="{{ route('superadmin.trails.advanced') }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Advanced Logs
        </a>
    </div>

    {{-- Summary strip ── computed from current page ── --}}
    @php
        $creates  = $logs->getCollection()->filter(fn($l) => str_starts_with($l->action, 'created'))->count();
        $updates  = $logs->getCollection()->filter(fn($l) => str_starts_with($l->action, 'updated') || str_starts_with($l->action, 'toggled'))->count();
        $deletes  = $logs->getCollection()->filter(fn($l) => str_starts_with($l->action, 'deleted'))->count();
        $logins   = $logs->getCollection()->filter(fn($l) => str_starts_with($l->action, 'login'))->count();
    @endphp
    <div class="log-summary">
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--green-pale); color:var(--green);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="log-sum-label">Created</div>
                <div class="log-sum-val">{{ $creates }}</div>
            </div>
        </div>
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--blue-pale); color:var(--blue);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <div class="log-sum-label">Updated</div>
                <div class="log-sum-val">{{ $updates }}</div>
            </div>
        </div>
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--red-pale); color:var(--red);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <div class="log-sum-label">Deleted</div>
                <div class="log-sum-val">{{ $deletes }}</div>
            </div>
        </div>
        <div class="log-sum-item">
            <div class="log-sum-icon" style="background:var(--super-pale2); color:var(--super-mid);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <div>
                <div class="log-sum-label">Logins</div>
                <div class="log-sum-val">{{ $logins }}</div>
            </div>
        </div>
    </div>

    {{-- Table ── --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:130px">Timestamp</th>
                    <th style="width:160px">User</th>
                    <th style="width:140px">Action</th>
                    <th>Description</th>
                    <th style="width:110px">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td data-label="Timestamp">
                        <div class="log-ts">
                            <span class="log-ts-date">{{ $log->created_at->format('M d, Y') }}</span>
                            <span class="log-ts-time">{{ $log->created_at->format('H:i:s') }}</span>
                        </div>
                    </td>

                    <td data-label="User">
                        <div class="log-user">
                            <div class="log-avatar {{ ($log->user_name ?? 'System') === 'System' ? 'system' : '' }}">
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
                        @php
                            $cls = match(true) {
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
                        @endphp
                        <span class="trail-type {{ $cls }}">{{ $log->action }}</span>
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
                    <td colspan="5" style="padding:48px 20px; text-align:center;">
                        <div style="width:48px;height:48px;background:var(--gray-100);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--gray-400);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-600);margin-bottom:5px;">No log entries found</div>
                        <div style="font-size:12px;color:var(--gray-400);">System activity will appear here as actions are performed.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination ── --}}
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