<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Set Up Your Account — MDRRMO Naic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #111d35 0%, #1a2a4a 50%, #0e1e38 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .gov-bar {
            position: fixed; top: 0; left: 0; right: 0;
            background: #0a1322; color: #4a5a7a;
            font-size: 10px; padding: 0 24px; height: 28px;
            display: flex; align-items: center; justify-content: space-between;
            letter-spacing: .8px; text-transform: uppercase; z-index: 10;
        }

        .card {
            background: #fff;
            border-radius: 14px;
            width: 100%; max-width: 480px;
            box-shadow: 0 24px 60px rgba(0,0,0,.35);
            overflow: hidden;
            margin-top: 28px;
        }

        .card-header {
            background: #1a2a4a;
            border-bottom: 3px solid #e6a817;
            padding: 24px 32px;
        }
        .card-header .label { font-size: 10px; color: #8fa0c2; text-transform: uppercase; letter-spacing: 1px; }
        .card-header h1 { font-family: 'Rajdhani', sans-serif; font-size: 22px; font-weight: 700; color: #fff; margin: 4px 0 2px; }
        .card-header .sub { font-size: 11px; color: #6b7a99; }

        .card-body { padding: 28px 32px 32px; }

        /* Login email display */
        .login-email-box {
            background: #f0f3f9;
            border: 1px solid #dde3ef;
            border-left: 4px solid #2e6ddd;
            border-radius: 0 8px 8px 0;
            padding: 14px 16px;
            margin-bottom: 24px;
        }
        .login-email-box .lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7a99; margin-bottom: 4px; }
        .login-email-box .email { font-size: 15px; font-weight: 700; color: #1a2a4a; font-family: 'Courier New', monospace; letter-spacing: .5px; }
        .login-email-box .hint { font-size: 11px; color: #6b7a99; margin-top: 4px; }

        /* Role badge */
        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #ede9fe; color: #5b21b6;
            font-size: 11px; font-weight: 700;
            padding: 4px 12px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 20px;
        }
        .role-dot { width: 6px; height: 6px; border-radius: 50%; background: #7c3aed; }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 11px; font-weight: 700;
            color: #6b7a99; text-transform: uppercase; letter-spacing: .7px;
            margin-bottom: 6px;
        }
        .form-group .req { color: #e03c3c; }
        .form-group input {
            width: 100%; height: 42px;
            border: 1px solid #dde3ef; border-radius: 8px;
            padding: 0 14px; font-size: 14px;
            font-family: 'Inter', sans-serif; color: #1e2d4d;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-group input:focus {
            outline: none; border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124,58,237,.1);
        }
        .field-error { font-size: 11px; color: #e03c3c; margin-top: 4px; display: block; }

        /* Password strength */
        .pw-hint { font-size: 11px; color: #6b7a99; margin-top: 5px; }

        /* Submit */
        .btn-submit {
            width: 100%; height: 46px;
            background: #7c3aed; color: #fff;
            border: none; border-radius: 8px;
            font-size: 14px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer; transition: background .15s, transform .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 24px;
        }
        .btn-submit:hover { background: #6d28d9; transform: translateY(-1px); }

        /* Alert */
        .alert-success {
            background: #ecfdf5; border: 1px solid #6ee7b7;
            border-left: 4px solid #22a86a; border-radius: 0 8px 8px 0;
            padding: 12px 16px; font-size: 13px; color: #065f46;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #fff1f2; border: 1px solid #fca5a5;
            border-left: 4px solid #e03c3c; border-radius: 0 8px 8px 0;
            padding: 12px 16px; font-size: 13px; color: #991b1b;
            margin-bottom: 20px;
        }

        footer {
            margin-top: 20px; font-size: 11px; color: rgba(255,255,255,.3);
            text-align: center; line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="gov-bar">
    <span>Republic of the Philippines &nbsp;·&nbsp; Province of Cavite &nbsp;·&nbsp; Municipality of Naic</span>
    <span>MDRRMO Naic Official System</span>
</div>

<div class="card">

    <!-- Header -->
    <div class="card-header">
        <div class="label">Account Setup</div>
        <h1>Set Up Your Account</h1>
        <div class="sub">MDRRMO Naic — RBI System</div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                ⛔ <strong>Please fix the following:</strong>
                <ul style="margin:6px 0 0 16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Role badge -->
        <div class="role-badge">
            <span class="role-dot"></span>
            {{ $roleLabel }}
        </div>

        <!-- Login email display -->
        <div class="login-email-box">
            <div class="lbl">🔑 Your System Login Email</div>
            <div class="email">{{ $loginEmail }}</div>
            <div class="hint">Save this — you'll use it every time you log in.</div>
        </div>

        <p style="font-size:13px;color:#4a5a7a;margin-bottom:20px;line-height:1.6;">
            Please enter your full name and create a secure password to complete your account setup.
        </p>

        <!-- Setup Form -->
        <form method="POST" action="{{ route('account.setup.store', ['id' => $user->id, 'token' => $token]) }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <label for="name">Full Name <span class="req">*</span></label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       placeholder="e.g. Juan dela Cruz"
                       autocomplete="name" required/>
                @error('name') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Create Password <span class="req">*</span></label>
                <input type="password" id="password" name="password"
                       placeholder="At least 8 characters" required/>
                <span class="pw-hint">Must be at least 8 characters with uppercase, lowercase, and a number.</span>
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span class="req">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Re-enter your password" required/>
            </div>

            <button type="submit" class="btn-submit">
                ✅ Complete Account Setup
            </button>
        </form>

    </div>
</div>

<footer>
    © {{ date('Y') }} MDRRMO Naic, Cavite &nbsp;·&nbsp; Municipal Disaster Risk Reduction and Management Office
</footer>

</body>
</html>