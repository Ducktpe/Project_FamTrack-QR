<!DOCTYPE html>
<html lang="en">
<head>
    <title>Distribution Event — {{ $event->event_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background: #f3f5f7; color: #273142; font-size: 14px; }
        .container { max-width: 1100px; margin: 28px auto; padding: 0 20px 40px; }

        .page-header { background: #fff; border: 1px solid #e6e9ee; border-top: 3px solid #1B3F7A; padding: 20px 24px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
        .event-title { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: #122D5A; margin-bottom: 6px; }
        .event-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .meta-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-badge.status-ongoing  { background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; }
        .meta-badge.status-upcoming { background: #EAF0FA; color: #1B3F7A; border: 1px solid #C7D9F5; }
        .meta-badge.status-completed{ background: #F0F2F5; color: #5A6372; border: 1px solid #DEE2E8; }
        .meta-badge.mode-household  { background: #EAF0FA; color: #1B3F7A; border: 1px solid #C7D9F5; }
        .meta-badge.mode-head       { background: #F5F3FF; color: #6D28D9; border: 1px solid #DDD6FE; }

        .btn-group { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 4px; font-size: 12px; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.15s; white-space: nowrap; }
        .btn-primary { background: #1B3F7A; color: #fff; }
        .btn-primary:hover { background: #122D5A; }
        .btn-secondary { background: #F0F2F5; color: #5A6372; border: 1px solid #DEE2E8; }
        .btn-secondary:hover { background: #DEE2E8; }
        .btn svg { width: 13px; height: 13px; }

        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-card { background: #fff; border: 1px solid #e6e9ee; border-top: 3px solid #1B3F7A; padding: 16px 18px; }
        .stat-card.green { border-top-color: #16A34A; }
        .stat-card.purple { border-top-color: #7C3AED; }
        .stat-card.orange { border-top-color: #D97706; }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9AA3B0; margin-bottom: 6px; }
        .stat-value { font-size: 28px; font-weight: 700; color: #122D5A; line-height: 1; }
        .stat-card.green .stat-value { color: #16A34A; }
        .stat-card.purple .stat-value { color: #7C3AED; }
        .stat-card.orange .stat-value { color: #D97706; }
        .stat-meta { font-size: 11px; color: #9AA3B0; margin-top: 4px; }

        .section-card { background: #fff; border: 1px solid #e6e9ee; margin-bottom: 16px; }
        .section-header { padding: 13px 20px; border-bottom: 1px solid #f0f3f7; background: #fafbfc; display: flex; align-items: center; gap: 10px; }
        .section-title { font-size: 13px; font-weight: 600; color: #122D5A; }
        .section-header-actions { margin-left: auto; display: flex; gap: 8px; }

        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9AA3B0; background: #fafbfc; border-bottom: 2px solid #e6e9ee; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #f0f3f7; transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #EAF0FA; }
        tbody td { padding: 10px 14px; font-size: 12.5px; color: #2C3340; vertical-align: middle; }
        .td-serial { font-family: 'Courier New', monospace; font-size: 11px; background: #EAF0FA; color: #1B3F7A; padding: 2px 6px; border-radius: 3px; white-space: nowrap; }
        .td-muted { color: #9AA3B0; font-style: italic; font-size: 11px; }

        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #1B3F7A; text-decoration: none; margin-bottom: 16px; }
        .back-link:hover { text-decoration: underline; }
        .back-link svg { width: 14px; height: 14px; }

        .empty-state { padding: 48px 24px; text-align: center; color: #9AA3B0; }
        .empty-state svg { width: 40px; height: 40px; margin: 0 auto 12px; display: block; opacity: 0.3; }
        .empty-state p { font-size: 13px; }

        @media (max-width: 640px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .page-header { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="container">

    <a href="{{ route('admin.distribution.logs') }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Distribution Logs
    </a>

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div>
            <div class="event-title">{{ $event->event_name }}</div>
            <div class="event-meta">
                {{-- Status badge --}}
                <span class="meta-badge status-{{ $event->status }}">{{ ucfirst($event->status) }}</span>

                {{-- Scan Mode badge --}}
                @if($event->scan_mode === 'family_head')
                    <span class="meta-badge mode-head">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg>
                        Family Head QR Mode
                    </span>
                @else
                    <span class="meta-badge mode-household">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>
                        Household QR Mode
                    </span>
                @endif

                <span style="font-size:11px;color:#9AA3B0;">
                    {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '—' }}
                </span>
            </div>
            @if($event->description)
                <p style="font-size:12px;color:#5A6372;margin-top:8px;max-width:600px;">{{ $event->description }}</p>
            @endif
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.distribution.events.export.csv',  $event) }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </a>
            <a href="{{ route('admin.distribution.events.export.xlsx', $event) }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                XLSX
            </a>
            <a href="{{ route('admin.distribution.events.export.pdf',  $event) }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                PDF
            </a>
        </div>
    </div>

    {{-- STATS --}}
    <div class="stats-row">
        <div class="stat-card green">
            <div class="stat-label">Total Released</div>
            <div class="stat-value">{{ $totalReleased }}</div>
            <div class="stat-meta">distribution records</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Unique Households</div>
            <div class="stat-value">{{ $uniqueHouseholds }}</div>
            <div class="stat-meta">households served</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Scan Mode</div>
            <div class="stat-value" style="font-size:14px;padding-top:6px;">
                @if(($event->scan_mode ?? 'household') === 'family_head')
                    👤 Per Family Head
                @else
                    🏠 Per Household
                @endif
            </div>
            <div class="stat-meta">QR type accepted</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-label">Duplicates Blocked</div>
            <div class="stat-value">{{ $event->scanAttempts?->where('result', 'duplicate')->count() ?? '—' }}</div>
            <div class="stat-meta">blocked re-releases</div>
        </div>
    </div>

    {{-- DISTRIBUTION LOGS TABLE --}}
    <div class="section-card">
        <div class="section-header">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9AA3B0" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="21" y2="14"/><line x1="14" y1="14" x2="14" y2="21"/></svg>
            <div class="section-title">Distribution Logs — {{ $totalReleased }} records</div>
        </div>

        @if($event->logs->isEmpty())
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                <p>No distribution records yet for this event.</p>
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Serial Code</th>
                    <th>Household Head</th>
                    @if(($event->scan_mode ?? 'household') === 'family_head')
                        <th>Family Head (Scanned)</th>
                    @endif
                    <th>Barangay</th>
                    <th>Distributed By</th>
                    <th>Distributed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->logs as $i => $log)
                <tr>
                    <td style="color:#9AA3B0;font-size:12px;">{{ $i + 1 }}</td>
                    <td><span class="td-serial">{{ $log->serial_code }}</span></td>
                    <td style="font-weight:600;">{{ $log->household?->household_head_name ?? '—' }}</td>
                    @if(($event->scan_mode ?? 'household') === 'family_head')
                        <td>{{ $log->familyMember?->full_name ?? '—' }}</td>
                    @endif
                    <td>{{ $log->household?->barangay ?? '—' }}</td>
                    <td>{{ $log->staff?->name ?? '—' }}</td>
                    <td style="font-size:12px;color:#5A6372;">{{ $log->distributed_at?->format('M d, Y g:i A') ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
</body>
</html>