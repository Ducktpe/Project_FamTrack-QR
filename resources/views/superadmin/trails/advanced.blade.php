@extends('superadmin.superadmin')

@section('title', 'Advanced Trail Logs')

@section('content')

<div class="page-header-row">
    <div>
        <div class="breadcrumb">Super Admin &rsaquo; <span>Advanced Trail Logs</span></div>
        <h2 class="page-title">Advanced Trail Logs</h2>
        <p class="page-sub">Full system audit log — all actions across all roles. Live view.</p>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <span class="nav-badge green" style="font-size:11px;padding:4px 10px;">● LIVE</span>
        <div class="today-label">
            <span>{{ now()->format('l') }}</span>
            <strong>{{ now()->format('F j, Y') }}</strong>
        </div>
    </div>
</div>

<div class="panel">
    <div class="filter-bar">
        <form method="GET" action="{{ route('superadmin.trails.advanced') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;width:100%;">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input wide"
                       placeholder="Action, user, description…" value="{{ request('search') }}"/>
            </div>
            <div class="filter-group">
                <label class="filter-label">Severity</label>
                <select name="severity" class="filter-select" style="width:130px;">
                    <option value="">All</option>
                    <option value="low"    @selected(request('severity')=='low')>Low</option>
                    <option value="medium" @selected(request('severity')=='medium')>Medium</option>
                    <option value="high"   @selected(request('severity')=='high')>High</option>
                </select>
            </div>
            <div class="filter-group filter-btn">
                <button type="submit" class="btn btn-primary">🔍 Filter</button>
            </div>
            @if(request()->anyFilled(['search','severity','action']))
            <div class="filter-group filter-btn">
                <a href="{{ route('superadmin.trails.advanced') }}" class="btn btn-ghost">✕ Clear</a>
            </div>
            @endif
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Severity</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size:11px;color:var(--muted);white-space:nowrap;">
                        {{ $log->created_at->format('M d, Y') }}<br/>
                        <strong>{{ $log->created_at->format('H:i:s') }}</strong>
                    </td>
                    <td style="font-size:12px;">
                        <strong>{{ $log->user_name ?? 'System' }}</strong>
                        @if($log->user)
                            <br/><span style="color:var(--muted);font-size:11px;">{{ ucfirst($log->user->role) }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $actionClass = match(true) {
                                str_starts_with($log->action, 'created') => 'trail-create',
                                str_starts_with($log->action, 'updated'),
                                str_starts_with($log->action, 'toggled') => 'trail-update',
                                str_starts_with($log->action, 'deleted') => 'trail-delete',
                                str_starts_with($log->action, 'login')   => 'trail-login',
                                default => 'trail-system',
                            };
                        @endphp
                        <span class="trail-type {{ $actionClass }}">{{ $log->action }}</span>
                    </td>
                    <td>
                        @php
                            $sevClass = match($log->severity) {
                                'high'   => 'badge-inactive',
                                'medium' => 'badge-pending',
                                default  => 'badge-active',
                            };
                        @endphp
                        <span class="badge {{ $sevClass }}">{{ ucfirst($log->severity) }}</span>
                    </td>
                    <td style="font-size:12px;color:var(--muted);">{{ $log->category }}</td>
                    <td style="font-size:12px;max-width:280px;">{{ $log->description }}</td>
                    <td><span class="ip-code">{{ $log->ip_address ?? '—' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="empty-state">No audit log entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="pagination-bar">
        <span class="pagination-info">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} entries
        </span>
        <div class="pagination-links">{{ $logs->links() }}</div>
    </div>
    @endif
</div>

@endsection