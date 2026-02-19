<!DOCTYPE html>
<html>
<head>
    <title>Create Distribution Event</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        h1 { color: #0038A8; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box; }
        .btn { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #218838; }
    </style>
</head>
<body>
    <h1>Quick Create Distribution Event</h1>
    
    <form method="POST" action="{{ route('admin.events.quick-store') }}">
        @csrf
        
        <div class="form-group">
            <label>Event Name *</label>
            <input type="text" name="event_name" required placeholder="e.g. Relief Distribution - Feb 2026">
        </div>
        
        <div class="form-group">
            <label>Relief Type *</label>
            <input type="text" name="relief_type" required placeholder="e.g. Food Pack">
        </div>
        
        <button type="submit" class="btn">✓ Create & Set to ONGOING</button>
    </form>
    
    <p style="margin-top: 20px; color: #666;">
        <a href="{{ route('admin.dashboard') }}">← Back to Dashboard</a>
    </p>
</body>
</html>