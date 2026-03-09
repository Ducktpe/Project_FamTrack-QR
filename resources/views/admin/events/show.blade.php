<!DOCTYPE html>
<html lang="en">
<head>
    <title>Distribution Event — {{ $event->event_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        /* Reuse admin dashboard minimal styles for consistency */
        body{font-family:'Open Sans',sans-serif;background:#f3f5f7;color:#273142}
        .container{max-width:1100px;margin:28px auto;padding:20px;background:#fff;border:1px solid #e6e9ee}
        .header{display:flex;justify-content:space-between;align-items:center}
        .btn{padding:8px 12px;background:#1b3f7a;color:#fff;border-radius:4px;text-decoration:none}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{padding:10px;border-bottom:1px solid #f0f3f7;text-align:left}
        th{background:#fafbfc;font-weight:700}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h2>{{ $event->event_name }}</h2>
            <div style="color:#6b7280">Date: {{ $event->event_date }} — Status: {{ $event->status }}</div>
        </div>
        <div class="actions">
            <a href="{{ route('admin.distribution.events.export.csv', $event) }}" class="btn">Export CSV</a>
            <a href="{{ route('admin.distribution.events.export.xlsx', $event) }}" class="btn">Export XLSX</a>
            <a href="{{ route('admin.distribution.events.export.pdf', $event) }}" class="btn">Export PDF</a>
        </div>
    </div>

    <p style="margin-top:12px">{{ $event->description }}</p>

    <h3>Distribution Logs ({{ $totalReleased }} records — {{ $uniqueHouseholds }} unique households)</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Serial</th>
                <th>Household Head</th>
                <th>Barangay</th>
                <th>Distributed By</th>
                <th>Distributed At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->serial_code }}</td>
                    <td>{{ $log->household?->household_head_name }}</td>
                    <td>{{ $log->household?->barangay }}</td>
                    <td>{{ $log->staff?->name }}</td>
                    <td>{{ $log->distributed_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:14px">
        <a href="{{ route('admin.distribution.logs') }}">Back to Distribution Logs</a>
    </div>
</div>
</body>
</html>
