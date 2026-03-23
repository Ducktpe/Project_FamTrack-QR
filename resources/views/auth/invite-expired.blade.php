<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invite Expired — MDRRMO Naic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #111d35 0%, #1a2a4a 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center; padding: 24px;
        }
        .card {
            background: #fff; border-radius: 14px;
            width: 100%; max-width: 440px;
            box-shadow: 0 24px 60px rgba(0,0,0,.35);
            overflow: hidden; text-align: center;
        }
        .card-top { background: #1a2a4a; border-bottom: 3px solid #e6a817; padding: 24px 32px; }
        .card-top h1 { font-family: 'Rajdhani', sans-serif; font-size: 20px; font-weight: 700; color: #fff; }
        .card-top p { font-size: 11px; color: #6b7a99; margin-top: 4px; }
        .card-body { padding: 36px 32px; }
        .icon { font-size: 52px; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: 700; color: #1a2a4a; margin-bottom: 10px; }
        .sub { font-size: 13px; color: #6b7a99; line-height: 1.6; margin-bottom: 24px; }
        .btn {
            display: inline-block; background: #1a2a4a; color: #fff;
            text-decoration: none; padding: 12px 28px;
            border-radius: 8px; font-size: 13px; font-weight: 700;
            transition: background .15s;
        }
        .btn:hover { background: #2e6ddd; }
        footer { margin-top: 20px; font-size: 11px; color: rgba(255,255,255,.3); text-align: center; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-top">
        <h1>MDRRMO – Naic, Cavite</h1>
        <p>Municipal Disaster Risk Reduction and Management Office</p>
    </div>
    <div class="card-body">
        <div class="icon">⏰</div>
        <div class="title">Invite Link Expired</div>
        <div class="sub">
            This account setup link has expired or is no longer valid.<br/>
            Invite links are only valid for <strong>24 hours</strong>.<br/><br/>
            Please contact the <strong>Super Administrator</strong> to send you a new invite.
        </div>
        <a href="{{ route('login') }}" class="btn">← Back to Login</a>
    </div>
</div>
<footer>© {{ date('Y') }} MDRRMO Naic, Cavite</footer>
</body>
</html>