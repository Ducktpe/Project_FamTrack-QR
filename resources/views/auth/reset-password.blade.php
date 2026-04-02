<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDRRMO Naic — Reset Password</title>
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
            --red:       #C0392B;
        }

        html, body { height: 100%; font-family: 'Open Sans', sans-serif; background: var(--gray-100); overflow-x: hidden; }
        .page-wrap { min-height: 100vh; display: flex; flex-direction: column; }

        /* ─── TOPBAR ─── */
        .topbar { background: var(--blue-dark); padding: 0 32px; height: 34px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 6px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.35} }

        /* ─── NAVBAR ─── */
        nav { background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 0 32px; height: 72px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .nav-brand { display: flex; align-items: center; gap: 14px; }
        .nav-brand img { height: 50px; width: 50px; object-fit: contain; }
        .nav-divider { width: 1px; height: 40px; background: var(--gray-200); }
        .nav-text-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .nav-text-title { font-family: 'PT Serif', serif; font-size: 17px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .nav-home { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: var(--blue); color: var(--white); border: none; font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; text-decoration: none; border-radius: 4px; transition: background 0.15s, transform 0.15s; white-space: nowrap; }
        .nav-home:hover { background: var(--blue-dark); transform: translateY(-1px); }
        .nav-home svg { width: 14px; height: 14px; flex-shrink: 0; }

        /* ── BODY ── */
        .login-body { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 24px; }

        /* ── CARD ── */
        .login-card { width: 100%; max-width: 840px; display: grid; grid-template-columns: 300px 1fr; box-shadow: 0 8px 40px rgba(0,0,0,0.45); border-radius: 4px; overflow: hidden; }

        /* ── LEFT PANEL ── */
        .login-left { background: var(--blue); border-left: 5px solid var(--yellow); padding: 40px 28px; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .login-logos { display: flex; align-items: center; justify-content: center; gap: 14px; margin-bottom: 22px; }
        .login-logos img { width: 66px; height: 66px; object-fit: contain; }
        .logos-sep { width: 1px; height: 50px; background: rgba(255,255,255,0.2); }
        .left-eyebrow { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--yellow); margin-bottom: 7px; }
        .left-title { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--white); line-height: 1.3; margin-bottom: 5px; }
        .left-title span { color: var(--yellow); }
        .left-sub { font-size: 11px; color: rgba(255,255,255,0.5); line-height: 1.6; margin-bottom: 26px; }
        .left-rule { width: 36px; height: 2px; background: var(--yellow); margin: 0 auto 22px; opacity: 0.6; }

        /* Password tips */
        .left-tips { width: 100%; display: flex; flex-direction: column; gap: 8px; text-align: left; }
        .left-tip { display: flex; align-items: flex-start; gap: 10px; padding: 9px 11px; background: rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.08); }
        .tip-icon { width: 20px; height: 20px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .tip-icon svg { width: 13px; height: 13px; color: var(--yellow); }
        .tip-text { font-size: 11px; color: rgba(255,255,255,0.65); line-height: 1.4; }

        /* ── RIGHT PANEL ── */
        .login-right { background: var(--white); padding: 40px 36px; display: flex; flex-direction: column; justify-content: center; }
        .right-eyebrow { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--yellow); margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }
        .right-eyebrow::before { content: ''; width: 18px; height: 2px; background: var(--yellow); flex-shrink: 0; }
        .right-title { font-family: 'PT Serif', serif; font-size: 22px; font-weight: 700; color: var(--blue-dark); margin-bottom: 4px; line-height: 1.25; }
        .right-sub { font-size: 12px; color: var(--gray-600); margin-bottom: 22px; line-height: 1.6; }

        /* Login email display */
        .system-email-box { background: var(--gray-50); border: 1.5px solid var(--gray-200); border-left: 3px solid var(--blue); padding: 10px 14px; margin-bottom: 18px; font-size: 12px; color: var(--gray-600); line-height: 1.5; }
        .system-email-box strong { display: block; font-size: 13px; color: var(--blue-dark); font-family: 'Courier New', monospace; margin-top: 2px; letter-spacing: .5px; }

        /* ── FIELDS ── */
        .field { margin-bottom: 16px; }
        .field-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-800); margin-bottom: 6px; }
        .field-wrap { position: relative; }
        .field-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--gray-400); pointer-events: none; }
        .field-input { width: 100%; border: 1.5px solid var(--gray-200); padding: 11px 12px 11px 34px; font-size: 14px; color: var(--gray-800); background: var(--gray-50); font-family: 'Open Sans', sans-serif; outline: none; border-radius: 0; transition: border-color 0.15s, background 0.15s; }
        .field-input:focus { border-color: var(--blue); background: var(--white); box-shadow: 0 0 0 3px rgba(27,63,122,0.08); }
        .field-error { font-size: 11px; color: var(--red); margin-top: 4px; }
        .field-hint { font-size: 11px; color: var(--gray-400); margin-top: 4px; }

        /* Password strength indicator */
        .pw-strength { margin-top: 6px; display: flex; gap: 4px; }
        .pw-bar { flex: 1; height: 3px; background: var(--gray-200); border-radius: 2px; transition: background 0.2s; }
        .pw-bar.weak   { background: #e53e3e; }
        .pw-bar.fair   { background: #ed8936; }
        .pw-bar.good   { background: #ecc94b; }
        .pw-bar.strong { background: #48bb78; }
        .pw-label { font-size: 10px; color: var(--gray-400); margin-top: 4px; text-align: right; }

        /* ── ACTIONS ── */
        .form-divider { border: none; border-top: 1px solid var(--gray-100); margin: 16px 0; }
        .form-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .back-link { font-size: 12px; color: var(--blue); text-decoration: underline; text-underline-offset: 2px; font-weight: 500; }
        .back-link:hover { color: var(--yellow); }
        .submit-btn { background: var(--blue); color: var(--white); font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 28px; border: none; cursor: pointer; border-radius: 0; display: flex; align-items: center; gap: 8px; transition: background 0.15s; white-space: nowrap; }
        .submit-btn:hover { background: var(--blue-dark); }
        .submit-btn svg { width: 14px; height: 14px; }

        /* ── SECURITY NOTE ── */
        .security-note { margin-top: 18px; display: flex; align-items: flex-start; gap: 9px; padding: 10px 13px; background: var(--gray-50); border: 1px solid var(--gray-200); border-left: 3px solid var(--blue); }
        .security-note svg { width: 14px; height: 14px; color: var(--blue); flex-shrink: 0; margin-top: 1px; }
        .security-note p { font-size: 11px; color: var(--gray-600); line-height: 1.5; }
        .security-note strong { color: var(--blue-dark); }

        /* ── FOOTER ── */
        .footer { background: #0d1f3c; min-height: 46px; border-top: 3px solid var(--yellow); display: flex; align-items: center; justify-content: space-between; padding: 10px 28px; flex-shrink: 0; flex-wrap: wrap; gap: 6px; }
        .footer-left { font-size: 11px; color: rgba(255,255,255,0.35); }
        .footer-left strong { color: rgba(255,255,255,0.6); }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.18); text-transform: uppercase; letter-spacing: 1px; }
        .footer-fb { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.35); text-decoration: none; transition: color 0.15s; }
        .footer-fb:hover { color: var(--yellow); }
        .footer-fb svg { width: 13px; height: 13px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .topbar { padding: 0 20px; height: 30px; }
            .topbar-left { display: none; }
            .clock-date { display: none; }
            nav { padding: 0 20px; height: 62px; }
            .nav-brand img { height: 40px; width: 40px; }
            .nav-text-org { display: none; }
            .nav-text-title { font-size: 14px; }
            .login-card { grid-template-columns: 1fr; max-width: 480px; }
            .login-left { padding: 28px 24px 22px; border-left: none; border-top: 5px solid var(--yellow); }
            .left-rule { display: none; }
            .left-tips { display: none; }
            .login-right { padding: 28px 24px; }
            .login-body { padding: 28px 16px; align-items: flex-start; padding-top: 32px; }
            .footer { padding: 10px 20px; }
            .footer-center { display: none; }
        }
        @media (max-width: 480px) {
            .topbar { padding: 0 14px; }
            .status-dot { display: none; }
            nav { padding: 0 14px; height: 56px; }
            .nav-brand img { height: 34px; width: 34px; }
            .nav-divider { display: none; }
            .nav-text-title { font-size: 13px; }
            .nav-home span { display: none; }
            .nav-home { padding: 9px 10px; gap: 0; width: 38px; height: 38px; justify-content: center; border-radius: 50%; }
            .login-card { max-width: 100%; border-radius: 2px; }
            .login-right { padding: 24px 18px; }
            .right-title { font-size: 19px; }
            .field-input { font-size: 16px; }
            .form-actions { flex-direction: column; align-items: stretch; gap: 10px; }
            .submit-btn { width: 100%; justify-content: center; padding: 13px; }
            .back-link { text-align: center; }
            .login-body { padding: 16px 12px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
<div class="page-wrap">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">Republic of the Philippines &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic</div>
        <div class="topbar-right">
            <span class="clock-date" id="top-date">—</span>
            <span class="clock" id="top-time">00:00:00</span>
            <span class="status-dot">System Online</span>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav>
        <div class="nav-brand">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
            <div class="nav-divider"></div>
            <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal">
            <div style="margin-left:6px;">
                <div class="nav-text-org">Office of the Municipal DRRMO</div>
                <div class="nav-text-title">MDRRMO — Naic, Cavite</div>
            </div>
        </div>
        <a href="{{ route('home') }}" class="nav-home">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
            </svg>
            <span>Homepage</span>
        </a>
    </nav>

    <!-- BODY -->
    <div class="login-body">
        <div class="login-card">

            <!-- LEFT -->
            <div class="login-left">
                <div class="login-logos">
                    <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
                    <div class="logos-sep"></div>
                    <img src="{{ asset('images/naic-seal.png') }}" alt="Naic Seal">
                </div>
                <div class="left-eyebrow">Set New Password</div>
                <div class="left-title">NAIC MDRRMO<br><span>RBI System</span></div>
                <div class="left-sub">Create a strong password to secure your account.</div>
                <div class="left-rule"></div>
                <div class="left-tips">
                    <div class="left-tip">
                        <div class="tip-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="tip-text">At least 8 characters long</div>
                    </div>
                    <div class="left-tip">
                        <div class="tip-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="tip-text">Mix of uppercase and lowercase letters</div>
                    </div>
                    <div class="left-tip">
                        <div class="tip-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="tip-text">Include at least one number</div>
                    </div>
                    <div class="left-tip">
                        <div class="tip-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="tip-text">Do not reuse old passwords</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="login-right">
                <div class="right-eyebrow">Password Reset</div>
                <div class="right-title">Set a new password</div>
                <div class="right-sub">Enter and confirm your new password below. After resetting, log in using your system email address.</div>

                {{-- System email reminder --}}
                @php
                    $systemEmail = request('email') ?? old('email');
                @endphp
                @if($systemEmail)
                <div class="system-email-box">
                    Your system login email:
                    <strong>{{ $systemEmail }}</strong>
                </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Hidden token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Hidden system email -->
                    <input type="hidden" name="email" value="{{ $systemEmail }}">

                    <!-- New Password -->
                    <div class="field">
                        <label class="field-label" for="password">New Password</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            <input id="password" class="field-input" type="password" name="password"
                                   required autocomplete="new-password" placeholder="Min. 8 characters"
                                   oninput="checkStrength(this.value)"/>
                        </div>
                        <div class="pw-strength">
                            <div class="pw-bar" id="bar1"></div>
                            <div class="pw-bar" id="bar2"></div>
                            <div class="pw-bar" id="bar3"></div>
                            <div class="pw-bar" id="bar4"></div>
                        </div>
                        <div class="pw-label" id="pw-label"></div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="field">
                        <label class="field-label" for="password_confirmation">Confirm New Password</label>
                        <div class="field-wrap">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            <input id="password_confirmation" class="field-input" type="password"
                                   name="password_confirmation" required autocomplete="new-password"
                                   placeholder="Re-enter new password"/>
                        </div>
                        @error('password_confirmation')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <hr class="form-divider">

                    <div class="form-actions">
                        <a class="back-link" href="{{ route('login') }}">← Back to Login</a>
                        <button type="submit" class="submit-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            Reset Password
                        </button>
                    </div>
                </form>

                <div class="security-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <p><strong>Authorized personnel only.</strong> All password changes are logged and monitored for security.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-left">&copy; {{ date('Y') }} <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office</div>
        <div class="footer-center">Republic of the Philippines</div>
        <a class="footer-fb" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            <span>facebook.com/naicmdrrmo</span>
        </a>
    </div>

</div>
<script>
    function pad(n){ return String(n).padStart(2,'0'); }
    function updateClock() {
        const now = new Date();
        const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
        document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+months[now.getMonth()]+' '+now.getFullYear();
    }
    updateClock(); setInterval(updateClock, 1000);

    // Password strength meter
    function checkStrength(val) {
        const bars  = [1,2,3,4].map(i => document.getElementById('bar' + i));
        const label = document.getElementById('pw-label');
        bars.forEach(b => b.className = 'pw-bar');

        if (!val) { label.textContent = ''; return; }

        let score = 0;
        if (val.length >= 8)  score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = ['', 'weak', 'fair', 'good', 'strong'];
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];

        for (let i = 0; i < score; i++) {
            bars[i].classList.add(levels[score]);
        }
        label.textContent = labels[score] || '';
        label.style.color = score <= 1 ? '#e53e3e' : score === 2 ? '#ed8936' : score === 3 ? '#ecc94b' : '#48bb78';
    }
</script>
</body>
</html>