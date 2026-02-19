<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f5f5f5; }
        .header { background: #0038A8; color: white; padding: 20px; text-align: center; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 30px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 20px 40px; background: #28a745; color: white; text-decoration: none; border-radius: 8px; font-size: 20px; font-weight: bold; margin: 10px; }
        .btn:hover { background: #218838; }
        .logout-btn { background: #ffc107; color: black; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, {{ auth()->user()->name }}!</h1>
        <p>Distribution Staff Portal</p>
    </div>

    <div class="container">
        <div class="card">
            <h2 style="color: #0038A8; margin-bottom: 30px;">📱 QR Code Scanner</h2>
            <p style="color: #666; margin-bottom: 30px;">Start scanning household QR codes for ayuda distribution</p>
            
            <a href="{{ route('staff.scan') }}" class="btn">
                📷 Open Scanner
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" style="text-align: center; margin-top: 30px;">
        @csrf
        <button type="submit" class="btn logout-btn">Logout</button>
    </form>
</body>
</html>