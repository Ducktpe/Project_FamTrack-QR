<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDRRMO Naic — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --blue:      #1B3F7A;
            --blue-dark: #122D5A;
            --yellow:    #F5C518;
            --white:     #ffffff;
            --gray-50:   #F7F8FA;
            --gray-100:  #F0F2F5;
            --gray-200:  #DEE2E8;
            --gray-400:  #9AA3B0;
            --gray-600:  #5A6372;
            --gray-800:  #2C3340;
        }

        html, body {
            height: 100%;
            font-family: 'Open Sans', sans-serif;
            background: var(--gray-100);
        }

        .page-wrap {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOP BAR ── */
        .topbar {
            background: #0d1f3c;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.4); }
        .topbar-right { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.35); }
        .topbar-dot { width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 4px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* ── BODY CENTER ── */
        .login-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        /* ── CARD ── */
        .login-card {
            width: 100%;
            max-width: 840px;
            display: grid;
            grid-template-columns: 300px 1fr;
            box-shadow: 0 8px 40px rgba(0,0,0,0.45);
        }

        /* ── LEFT PANEL ── */
        .login-left {
            background: var(--blue);
            border-left: 5px solid var(--yellow);
            padding: 40px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .login-logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 22px;
        }
        .login-logos img { width: 66px; height: 66px; object-fit: contain; }
        .logos-sep { width: 1px; height: 50px; background: rgba(255,255,255,0.2); }

        .left-eyebrow {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            color: var(--yellow); margin-bottom: 7px;
        }
        .left-title {
            font-family: 'PT Serif', serif;
            font-size: 22px; font-weight: 700;
            color: var(--white); line-height: 1.3; margin-bottom: 5px;
        }
        .left-title span { color: var(--yellow); }
        .left-sub {
            font-size: 11px; color: rgba(255,255,255,0.5);
            line-height: 1.6; margin-bottom: 26px;
        }
        .left-rule {
            width: 36px; height: 2px;
            background: var(--yellow);
            margin: 0 auto 22px; opacity: 0.6;
        }
        .left-features {
            width: 100%; display: flex;
            flex-direction: column; gap: 8px; text-align: left;
        }
        .left-feature {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 11px;
            background: rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .left-feature-icon {
            width: 26px; height: 26px; flex-shrink: 0;
            background: rgba(245,197,24,0.12);
            border: 1px solid rgba(245,197,24,0.25);
            display: flex; align-items: center; justify-content: center;
        }
        .left-feature-icon svg { width: 13px; height: 13px; color: var(--yellow); }
        .left-feature-text strong {
            display: block; font-size: 11.5px; font-weight: 700;
            color: rgba(255,255,255,0.85); margin-bottom: 1px;
        }
        .left-feature-text span { font-size: 10.5px; color: rgba(255,255,255,0.4); }

        /* ── RIGHT PANEL ── */
        .login-right {
            background: var(--white);
            padding: 40px 36px;
            display: flex; flex-direction: column; justify-content: center;
        }

        .right-eyebrow {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            color: var(--yellow); margin-bottom: 6px;
            display: flex; align-items: center; gap: 8px;
        }
        .right-eyebrow::before { content: ''; width: 18px; height: 2px; background: var(--yellow); }
        .right-title {
            font-family: 'PT Serif', serif;
            font-size: 22px; font-weight: 700;
            color: var(--blue-dark); margin-bottom: 4px; line-height: 1.25;
        }
        .right-sub { font-size: 12px; color: var(--gray-600); margin-bottom: 22px; }

        /* ── FIELDS ── */
        .field { margin-bottom: 16px; }
        .field-label {
            display: block; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--gray-800); margin-bottom: 6px;
        }
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
            width: 15px; height: 15px; color: var(--gray-400); pointer-events: none;
        }
        .field-input {
            width: 100%;
            border: 1.5px solid var(--gray-200);
            padding: 10px 12px 10px 34px;
            font-size: 13px; color: var(--gray-800);
            background: var(--gray-50);
            font-family: 'Open Sans', sans-serif;
            outline: none; border-radius: 0;
            transition: border-color 0.15s, background 0.15s;
        }
        .field-input:focus {
            border-color: var(--blue);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(27,63,122,0.08);
        }
        .field-error { font-size: 11px; color: #C0392B; margin-top: 4px; }

        /* ── REMEMBER ── */
        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
        .remember-row input[type="checkbox"] { width: 14px; height: 14px; accent-color: var(--blue); cursor: pointer; }
        .remember-row span { font-size: 12px; color: var(--gray-600); }

        .form-divider { border: none; border-top: 1px solid var(--gray-100); margin: 16px 0; }

        /* ── ACTIONS ── */
        .form-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .forgot-link {
            font-size: 12px; color: var(--blue);
            text-decoration: underline; text-underline-offset: 2px; font-weight: 500;
        }
        .forgot-link:hover { color: var(--yellow); }

        .submit-btn {
            background: var(--blue);
            color: var(--white);
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            padding: 11px 28px;
            border: none; cursor: pointer; border-radius: 0;
            display: flex; align-items: center; gap: 8px;
            transition: background 0.15s; white-space: nowrap;
        }
        .submit-btn:hover { background: var(--blue-dark); }
        .submit-btn svg { width: 14px; height: 14px; }

        /* ── SECURITY NOTE ── */
        .security-note {
            margin-top: 18px;
            display: flex; align-items: flex-start; gap: 9px;
            padding: 10px 13px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-left: 3px solid var(--blue);
        }
        .security-note svg { width: 14px; height: 14px; color: var(--blue); flex-shrink: 0; margin-top: 1px; }
        .security-note p { font-size: 11px; color: var(--gray-600); line-height: 1.5; }
        .security-note strong { color: var(--blue-dark); }

        /* ── FOOTER ── */
        .footer {
            background: #0d1f3c;
            height: 46px;
            border-top: 3px solid var(--yellow);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; flex-shrink: 0;
        }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.35); }
        .footer-left strong { color: rgba(255,255,255,0.6); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.18); text-transform: uppercase; letter-spacing: 1px; }
        .footer-fb {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: rgba(255,255,255,0.35);
            text-decoration: none; transition: color 0.15s; white-space: nowrap;
        }
        .footer-fb:hover { color: var(--yellow); }
        .footer-fb svg { width: 13px; height: 13px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 700px) {
            .login-card { grid-template-columns: 1fr; max-width: 420px; }
            .login-left { padding: 28px 24px; }
            .left-features { display: none; }
            .login-right { padding: 28px 24px; }
            .footer-center { display: none; }
            .topbar-left { display: none; }
        }
        @media (max-width: 400px) {
            .login-body { padding: 20px 12px; }
            .login-right { padding: 24px 18px; }
        }
    </style>
</head>
<body>
<div class="page-wrap">

    {{-- TOP BAR --}}
    <div class="topbar">
        <div class="topbar-left">Republic of the Philippines &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic</div>
    </div>

    {{-- BODY --}}
    <div class="login-body">
        <div class="login-card">

            {{-- LEFT --}}
            <div class="login-left">
                <div class="login-logos">
                    <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
                    <div class="logos-sep"></div>
                    <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal">
                </div>
                <div class="left-eyebrow">Municipal DRRMO</div>
                <div class="left-title">NAIC MDRRMO<br><span>RBI System</span></div>
                <div class="left-sub">Barangay Family Track &amp;<br>Relief Distribution Portal</div>
                <div class="left-rule"></div>
                <div class="left-features">
                    <div class="left-feature">
                        <div class="left-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                                <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                                <rect x="8" y="8" width="8" height="8" rx="1"/>
                            </svg>
                        </div>
                        <div class="left-feature-text">
                            <strong>QR Distribution</strong>
                            <span>Household scanning &amp; release logging</span>
                        </div>
                    </div>
                    <div class="left-feature">
                        <div class="left-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="left-feature-text">
                            <strong>Family Registry</strong>
                            <span>Beneficiary profiling &amp; records</span>
                        </div>
                    </div>
                    <div class="left-feature">
                        <div class="left-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                                <line x1="9" y1="12" x2="15" y2="12"/>
                                <line x1="9" y1="16" x2="13" y2="16"/>
                            </svg>
                        </div>
                        <div class="left-feature-text">
                            <strong>Audit &amp; Reports</strong>
                            <span>Distribution logs &amp; exports</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="login-right">
                <div class="right-eyebrow">Secure Portal Access</div>
                <div class="right-title">Sign in to your account</div>
                <div class="right-sub">Enter your credentials to access the MDRRMO portal.</div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field">
                        <label class="field-label" for="email">Email Address</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <input id="email" class="field-input" type="email" name="email"
                                value="{{ old('email') }}" required autofocus autocomplete="username"
                                placeholder="you@example.com" />
                        </div>
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label class="field-label" for="password">Password</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            <input id="password" class="field-input" type="password" name="password"
                                required autocomplete="current-password" placeholder="••••••••" />
                        </div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="remember-row">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Keep me signed in</span>
                    </div>

                    <hr class="form-divider">

                    <div class="form-actions">
                        @if (Route::has('password.request'))
                            <a class="forgot-link" href="{{ route('password.request') }}">Forgot your password?</a>
                        @endif
                        <button type="submit" class="submit-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                                <polyline points="10 17 15 12 10 7"/>
                                <line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                            Log In
                        </button>
                    </div>

                </form>

                <div class="security-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <p><strong>Authorized personnel only.</strong> All login attempts are logged and monitored.</p>
                </div>
            </div>

        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">
            &copy; {{ date('Y') }} <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office
        </div>
        <div class="footer-center">Republic of the Philippines</div>
        <a class="footer-fb" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
            </svg>
            facebook.com/naicmdrrmo
        </a>
    </div>

</div>
</body>
</html>