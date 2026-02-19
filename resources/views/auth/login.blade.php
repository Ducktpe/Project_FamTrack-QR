<x-guest-layout>
    <style>
        .login-page {
            min-height: 50vh;
            background: #F0F2F5;
            display: flex;
            flex-direction: column;
            font-family: 'Open Sans', sans-serif;
        }

        /* ── Top utility bar ── */
        .log-topbar {
            background: #122D5A;
            height: 36px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        /* ── Header ── */
        .log-header {
            background: #ffffff;
            border-bottom: 3px solid #F5C518;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            padding: 0 32px;
            height: 76px;
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }
        .log-header img {
            height: 52px;
            width: 52px;
            object-fit: contain;
        }
        .log-header-divider {
            width: 1px;
            height: 44px;
            background: #DEE2E8;
        }
        .log-header-org {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9AA3B0;
        }
        .log-header-title {
            font-size: 17px;
            font-weight: 700;
            color: #122D5A;
            font-family: Georgia, serif;
        }
        .log-header-sub {
            font-size: 11px;
            color: #5A6372;
        }

        /* ── Body ── */
    

        .log-card {
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 440px;
        }

        .log-card-header {
            background: #1B3F7A;
            border-left: 5px solid #F5C518;
            padding: 18px 28px;
        }
        .log-card-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 3px;
        }
        .log-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            font-family: Georgia, serif;
        }
        .log-card-title em {
            color: #F5C518;
            font-style: normal;
        }

        .log-card-body {
            padding: 28px 28px 24px;
        }

        /* Session status */
        .log-status {
            margin-bottom: 16px;
        }

        /* Field */
        .log-field {
            margin-bottom: 18px;
        }
        .log-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #2C3340;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .log-input {
            width: 100% !important;
            border: 1px solid #DEE2E8 !important;
            border-radius: 3px !important;
            padding: 10px 13px !important;
            font-size: 13.5px !important;
            color: #2C3340 !important;
            background: #F7F8FA !important;
            transition: border-color 0.15s, background 0.15s !important;
            font-family: 'Open Sans', sans-serif !important;
            outline: none !important;
            box-shadow: none !important;
        }
        .log-input:focus {
            border-color: #1B3F7A !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(27,63,122,0.08) !important;
        }

        /* Remember me */
        .log-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .log-remember input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #1B3F7A;
            cursor: pointer;
        }
        .log-remember span {
            font-size: 12px;
            color: #5A6372;
        }

        .log-divider {
            border: none;
            border-top: 1px solid #F0F2F5;
            margin: 20px 0;
        }

        /* Actions */
        .log-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .log-forgot-link {
            font-size: 12px;
            color: #1B3F7A;
            text-decoration: underline;
            text-underline-offset: 2px;
            font-weight: 500;
            transition: color 0.15s;
        }
        .log-forgot-link:hover { color: #F5C518; }

        .log-submit-btn {
            background: #1B3F7A !important;
            color: #ffffff !important;
            font-family: 'Open Sans', sans-serif !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            padding: 10px 28px !important;
            border: none !important;
            border-radius: 3px !important;
            cursor: pointer !important;
            transition: background 0.15s !important;
        }
        .log-submit-btn:hover { background: #122D5A !important; }

        /* ── Footer ── */
        .log-footer {
            background: #122D5A;
            border-top: 3px solid #F5C518;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            flex-shrink: 0;
        }
        .log-footer-left {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
        }
        .log-footer-left strong { color: rgba(255,255,255,0.7); }
        .log-footer-center {
            font-size: 10px;
            color: rgba(255,255,255,0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .log-fb-link {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            transition: color 0.15s;
        }
        .log-fb-link:hover { color: #F5C518; }
        .log-fb-link svg { width: 13px; height: 13px; }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <div class="login-page">

        {{-- BODY --}}
        <div class="log-body">
            <div class="log-card">

                <div class="log-card-header">
                    <div class="log-card-label">Secure Portal Access</div>
                    <div class="log-card-title">Admin <em>Login</em></div>
                </div>

                <div class="log-card-body">

                    {{-- Session Status --}}
                    <div class="log-status">
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="log-field">
                            <x-input-label for="email" :value="__('Email Address')" class="log-label" />
                            <x-text-input
                                id="email"
                                class="log-input"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required autofocus autocomplete="username"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Password --}}
                        <div class="log-field">
                            <x-input-label for="password" :value="__('Password')" class="log-label" />
                            <x-text-input
                                id="password"
                                class="log-input"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Remember Me --}}
                        <div class="log-remember">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>{{ __('Remember me') }}</span>
                        </div>

                        <hr class="log-divider">

                        <div class="log-actions">
                            @if (Route::has('password.request'))
                                <a class="log-forgot-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif

                            <x-primary-button class="log-submit-btn">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>