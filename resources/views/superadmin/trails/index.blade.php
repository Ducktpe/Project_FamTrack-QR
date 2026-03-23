@extends('superadmin.superadmin')

@section('title', 'Trail Logs')

@section('content')

<div class="page-header-row">
    <div>
        <div class="breadcrumb">Super Admin &rsaquo; <span>Trail Logs</span></div>
        <h2 class="page-title">Trail Logs</h2>
        <p class="page-sub">Recent system activity log.</p>
    </div>
    <div class="today-label">
        <span>{{ now()->format('l') }}</span>
        <strong>{{ now()->format('F j, Y') }}</strong>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size:11px;color:var(--muted);white-space:nowrap;">
                        {{ $log->created_at->format('M d, Y H:i:s') }}
                    </td>
                    <td style="font-size:12px;font-weight:600;">{{ $log->user_name ?? 'System' }}</td>
                    <td>
                        @php
                            $cls = match(true) {
                                str_starts_with($log->action, 'created') => 'trail-create',
                                str_starts_with($log->action, 'updated'),
                                str_starts_with($log->action, 'toggled') => 'trail-update',
                                str_starts_with($log->action, 'deleted') => 'trail-delete',
                                str_starts_with($log->action, 'login')   => 'trail-login',
                                default => 'trail-system',
                            };
                        @endphp
                        <span class="trail-type {{ $cls }}">{{ $log->action }}</span>
                    </td>
                    <td style="font-size:12px;max-width:340px;">{{ $log->description }}</td>
                    <td><span class="ip-code">{{ $log->ip_address ?? '—' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state">No log entries found.</td></tr>
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