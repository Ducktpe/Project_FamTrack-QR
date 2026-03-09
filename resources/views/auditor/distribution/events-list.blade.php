<!DOCTYPE html>
<html lang="en">
<head>
    <title>Distribution Events — Auditor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple: #7B2E8C;
            --purple-dark: #5A1F69;
            --gray-50: #F7F8FA;
            --gray-100: #F0F2F5;
            --gray-200: #DEE2E8;
            --gray-600: #5A6372;
            --gray-800: #2C3340;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Open Sans', sans-serif; background: var(--gray-100); color: var(--gray-800); font-size: 14px; }
        .main-content { padding: 28px 32px; max-width: 1400px; margin: 0 auto; }
        .page-titlebar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200); }
        .page-h1 { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--purple-dark); }
        .btn { padding: 8px 14px; background: var(--purple); color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn:hover { background: var(--purple-dark); }
        .events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
        .event-card { background: #fff; border: 1px solid var(--gray-200); border-radius: 6px; padding: 20px; cursor: pointer; transition: all 0.2s; }
        .event-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); transform: translateY(-2px); border-color: var(--purple); }
        .event-name { font-size: 16px; font-weight: 700; color: var(--purple-dark); margin-bottom: 8px; }
        .event-date { font-size: 12px; color: var(--gray-600); margin-bottom: 12px; }
        .event-stats { display: flex; gap: 16px; }
        .stat { flex: 1; }
        .stat-number { font-size: 18px; font-weight: 700; color: var(--purple); }
        .stat-label { font-size: 11px; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }
        .event-action { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--gray-200); }
        .view-btn { font-size: 12px; color: var(--purple); text-decoration: none; font-weight: 600; }
        .view-btn:hover { text-decoration: underline; }
        .badge-readonly { display: inline-block; padding: 2px 6px; background: #E8D4F0; color: var(--purple-dark); border-radius: 3px; font-size: 10px; font-weight: 600; margin-left: 8px; }
    </style>
</head>
<body>
<main class="main-content">
    <div class="page-titlebar">
        <div>
            <h1 class="page-h1">Distribution Events <span class="badge-readonly">VIEW ONLY</span></h1>
        </div>
        <a href="javascript:history.back()" class="btn">← Back</a>
    </div>

    @if($events->isEmpty())
        <div style="text-align: center; padding: 40px; background: #fff; border-radius: 6px;">
            <p style="color: var(--gray-600); margin-bottom: 16px;">No distribution events found.</p>
        </div>
    @else
        <div class="events-grid">
            @foreach($events as $event)
                <div class="event-card">
                    <div class="event-name">{{ $event->event_name }}</div>
                    <div class="event-date">📅 {{ $event->event_date }} — <strong>{{ $event->status }}</strong></div>
                    <div class="event-stats">
                        <div class="stat">
                            <div class="stat-number">{{ $event->total_distributed }}</div>
                            <div class="stat-label">Total Distributed</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">{{ $event->unique_households }}</div>
                            <div class="stat-label">Households</div>
                        </div>
                    </div>
                    <div class="event-action">
                        <a href="{{ route('auditor.distribution.events.households', $event) }}" class="view-btn">View Households ↓</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</main>
</body>
</html>
