<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MDRRMO Naic — Household Profiling & Relief Distribution System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue:        #1B3F7A;
            --blue-dark:   #122D5A;
            --blue-light:  #2459A8;
            --blue-pale:   #EAF0FA;
            --yellow:      #F5C518;
            --yellow-dark: #D4A800;
            --white:       #FFFFFF;
            --gray-50:     #F7F8FA;
            --gray-100:    #F0F2F5;
            --gray-200:    #DEE2E8;
            --gray-400:    #9AA3B0;
            --gray-600:    #5A6372;
            --gray-800:    #2C3340;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--gray-800);
            background: var(--white);
            overflow-x: hidden;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            background: var(--blue-dark);
            padding: 0 32px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 6px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.35} }

        /* ─── NAVBAR ─── */
        nav {
            background: var(--white);
            border-bottom: 3px solid var(--yellow);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 0 32px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-brand { display: flex; align-items: center; gap: 14px; }
        .nav-brand img { height: 50px; width: 50px; object-fit: contain; }
        .nav-divider { width: 1px; height: 40px; background: var(--gray-200); }
        .nav-text-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .nav-text-title { font-family: 'PT Serif', serif; font-size: 17px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .nav-login {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px;
            background: var(--blue);
            color: var(--white);
            font-family: 'Open Sans', sans-serif;
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.15s, transform 0.15s;
        }
        .nav-login:hover { background: var(--blue-dark); transform: translateY(-1px); }
        .nav-login svg { width: 14px; height: 14px; }

        /* ─── HERO ─── */
        .hero {
            position: relative;
            background: var(--blue-dark);
            min-height: 92vh;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        /* Diagonal background pattern */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                repeating-linear-gradient(
                    135deg,
                    rgba(255,255,255,0.018) 0px,
                    rgba(255,255,255,0.018) 1px,
                    transparent 1px,
                    transparent 48px
                );
        }

        /* Blue glow orb */
        .hero::after {
            content: '';
            position: absolute;
            top: -120px; right: -80px;
            width: 700px; height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(36,89,168,0.45) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 48px;
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 64px;
            align-items: center;
            width: 100%;
        }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(245,197,24,0.15);
            border: 1px solid rgba(245,197,24,0.3);
            color: var(--yellow);
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            padding: 6px 14px; border-radius: 2px;
            margin-bottom: 22px;
        }
        .hero-eyebrow::before { content: ''; width: 6px; height: 6px; background: var(--yellow); border-radius: 50%; animation: blink 2s infinite; }

        .hero-title {
            font-family: 'PT Serif', serif;
            font-size: clamp(32px, 4.5vw, 54px);
            font-weight: 700;
            color: var(--white);
            line-height: 1.18;
            margin-bottom: 22px;
        }
        .hero-title span {
            color: var(--yellow);
            font-style: italic;
        }

        .hero-sub {
            font-size: 15px;
            color: rgba(255,255,255,0.65);
            line-height: 1.75;
            max-width: 520px;
            margin-bottom: 36px;
            font-weight: 400;
        }

        .hero-cta-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 9px;
            padding: 14px 28px;
            background: var(--yellow);
            color: var(--blue-dark);
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            text-decoration: none; border-radius: 4px;
            transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(245,197,24,0.3);
        }
        .btn-primary:hover { background: var(--yellow-dark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(245,197,24,0.35); }
        .btn-primary svg { width: 15px; height: 15px; }

        .btn-ghost {
            display: inline-flex; align-items: center; gap: 9px;
            padding: 14px 24px;
            background: transparent;
            color: rgba(255,255,255,0.75);
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; font-weight: 600;
            text-decoration: none; border-radius: 4px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: border-color 0.15s, color 0.15s, background 0.15s;
        }
        .btn-ghost:hover { border-color: rgba(255,255,255,0.5); color: var(--white); background: rgba(255,255,255,0.06); }
        .btn-ghost svg { width: 14px; height: 14px; }

        /* Hero stats strip */
        .hero-stats {
            display: flex; gap: 32px;
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .hero-stat-num {
            font-family: 'PT Serif', serif;
            font-size: 28px; font-weight: 700;
            color: var(--yellow);
            line-height: 1;
            margin-bottom: 4px;
        }
        .hero-stat-label {
            font-size: 11px; color: rgba(255,255,255,0.45);
            text-transform: uppercase; letter-spacing: 1px;
        }

        /* Hero right — logos card */
        .hero-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 40px 32px;
            text-align: center;
            backdrop-filter: blur(8px);
            animation: floatCard 6s ease-in-out infinite;
        }
        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }
        .hero-card-logos { display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 24px; }
        .hero-card-logos img { width: 96px; height: 96px; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4)); }
        .hero-card-divider { width: 1px; height: 80px; background: rgba(255,255,255,0.15); }
        .hero-card-title {
            font-family: 'PT Serif', serif;
            font-size: 15px; font-weight: 700;
            color: var(--white);
            margin-bottom: 6px;
        }
        .hero-card-sub { font-size: 11px; color: rgba(255,255,255,0.45); letter-spacing: 0.5px; line-height: 1.6; }
        .hero-card-badge {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 20px;
            padding: 8px 16px;
            background: rgba(245,197,24,0.1);
            border: 1px solid rgba(245,197,24,0.25);
            border-radius: 3px;
            font-size: 11px; font-weight: 600;
            color: var(--yellow);
            letter-spacing: 0.5px;
        }

        /* ─── WAVE DIVIDER ─── */
        .wave-divider { display: block; width: 100%; overflow: hidden; line-height: 0; }
        .wave-divider svg { display: block; width: 100%; }

        /* ─── FEATURES ─── */
        .features-section {
            background: var(--gray-50);
            padding: 88px 48px;
        }
        .section-inner { max-width: 1200px; margin: 0 auto; }

        .section-eyebrow {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            color: var(--blue-light);
            margin-bottom: 12px;
        }
        .section-title {
            font-family: 'PT Serif', serif;
            font-size: clamp(24px, 3vw, 36px);
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 14px;
            line-height: 1.25;
        }
        .section-sub {
            font-size: 14px; color: var(--gray-600);
            max-width: 560px; line-height: 1.7;
            margin-bottom: 52px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-top: 3px solid var(--blue);
            padding: 28px 24px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(27,63,122,0.1); }
        .feature-card.green  { border-top-color: #16A34A; }
        .feature-card.yellow { border-top-color: var(--yellow-dark); }
        .feature-card.red    { border-top-color: #C0392B; }
        .feature-card.teal   { border-top-color: #0891B2; }
        .feature-card.orange { border-top-color: #D97706; }

        .feature-icon {
            width: 44px; height: 44px;
            background: var(--blue-pale);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .feature-card.green  .feature-icon { background: #DCFCE7; }
        .feature-card.yellow .feature-icon { background: #FFFBEB; }
        .feature-card.red    .feature-icon { background: #FEF2F2; }
        .feature-card.teal   .feature-icon { background: #E0F2FE; }
        .feature-card.orange .feature-icon { background: #FFF7ED; }

        .feature-icon svg { width: 22px; height: 22px; }
        .feature-card .feature-icon svg { color: var(--blue); }
        .feature-card.green  .feature-icon svg { color: #16A34A; }
        .feature-card.yellow .feature-icon svg { color: var(--yellow-dark); }
        .feature-card.red    .feature-icon svg { color: #C0392B; }
        .feature-card.teal   .feature-icon svg { color: #0891B2; }
        .feature-card.orange .feature-icon svg { color: #D97706; }

        .feature-title { font-size: 14px; font-weight: 700; color: var(--blue-dark); margin-bottom: 8px; }
        .feature-desc  { font-size: 13px; color: var(--gray-600); line-height: 1.65; }

        /* ─── HOW IT WORKS ─── */
        .how-section {
            background: var(--white);
            padding: 88px 48px;
        }
        .steps-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            position: relative;
            margin-top: 8px;
        }
        .steps-row::before {
            content: '';
            position: absolute;
            top: 28px; left: calc(12.5% + 20px); right: calc(12.5% + 20px);
            height: 2px;
            background: var(--gray-200);
            z-index: 0;
        }
        .step { text-align: center; padding: 0 16px; position: relative; z-index: 1; }
        .step-num {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: var(--blue);
            color: var(--white);
            font-family: 'PT Serif', serif;
            font-size: 20px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            border: 4px solid var(--white);
            box-shadow: 0 0 0 2px var(--blue), 0 4px 12px rgba(27,63,122,0.2);
        }
        .step-title { font-size: 13px; font-weight: 700; color: var(--blue-dark); margin-bottom: 8px; }
        .step-desc  { font-size: 12px; color: var(--gray-600); line-height: 1.6; }

        /* ─── ROLES SECTION ─── */
        .roles-section {
            background: var(--blue-dark);
            padding: 88px 48px;
            position: relative;
            overflow: hidden;
        }
        .roles-section::before {
            content: '';
            position: absolute; inset: 0;
            background: repeating-linear-gradient(
                135deg,
                rgba(255,255,255,0.015) 0, rgba(255,255,255,0.015) 1px,
                transparent 1px, transparent 48px
            );
        }
        .roles-section .section-title { color: var(--white); }
        .roles-section .section-sub   { color: rgba(255,255,255,0.55); }
        .roles-section .section-eyebrow { color: var(--yellow); }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            position: relative; z-index: 1;
        }
        .role-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            padding: 28px 24px;
            transition: background 0.2s, transform 0.2s;
        }
        .role-card:hover { background: rgba(255,255,255,0.09); transform: translateY(-3px); }
        .role-icon {
            width: 44px; height: 44px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
            background: rgba(245,197,24,0.12);
        }
        .role-icon svg { width: 22px; height: 22px; color: var(--yellow); }
        .role-title { font-size: 15px; font-weight: 700; color: var(--white); margin-bottom: 6px; }
        .role-tag {
            display: inline-block;
            padding: 2px 8px; border-radius: 2px;
            font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 12px;
        }
        .role-tag.admin  { background: rgba(36,89,168,0.35); color: #93C5FD; }
        .role-tag.staff  { background: rgba(22,163,74,0.3);  color: #86EFAC; }
        .role-tag.auditor{ background: rgba(217,119,6,0.3);  color: #FCD34D; }
        .role-desc { font-size: 12px; color: rgba(255,255,255,0.5); line-height: 1.65; }

        /* ─── CTA BANNER ─── */
        .cta-section {
            background: var(--yellow);
            padding: 64px 48px;
            text-align: center;
        }
        .cta-title {
            font-family: 'PT Serif', serif;
            font-size: clamp(22px, 3vw, 34px);
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 12px;
        }
        .cta-sub { font-size: 14px; color: var(--blue); opacity: 0.75; margin-bottom: 28px; }
        .btn-cta {
            display: inline-flex; align-items: center; gap: 9px;
            padding: 14px 32px;
            background: var(--blue-dark);
            color: var(--white);
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            text-decoration: none; border-radius: 4px;
            transition: background 0.15s, transform 0.15s;
        }
        .btn-cta:hover { background: var(--blue); transform: translateY(-2px); }
        .btn-cta svg { width: 15px; height: 15px; }

        /* ─── FOOTER ─── */
        footer {
            background: var(--blue-dark);
            border-top: 3px solid var(--yellow);
            padding: 32px 48px;
            display: flex; align-items: center;
            justify-content: space-between; gap: 16px;
            flex-wrap: wrap;
        }
        .footer-brand { display: flex; align-items: center; gap: 12px; }
        .footer-brand img { height: 40px; width: 40px; object-fit: contain; opacity: 0.9; }
        .footer-brand-text { font-size: 12px; color: rgba(255,255,255,0.5); }
        .footer-brand-text strong { display: block; color: rgba(255,255,255,0.8); font-size: 13px; margin-bottom: 2px; }
        .footer-center { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 1px; text-transform: uppercase; }
        .footer-right { font-size: 11px; color: rgba(255,255,255,0.35); }

        /* ─── ENTRANCE ANIMATIONS ─── */
        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.65s ease, transform 0.65s ease;
        }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        .fade-up:nth-child(2) { transition-delay: 0.1s; }
        .fade-up:nth-child(3) { transition-delay: 0.2s; }
        .fade-up:nth-child(4) { transition-delay: 0.3s; }
        .fade-up:nth-child(5) { transition-delay: 0.4s; }
        .fade-up:nth-child(6) { transition-delay: 0.5s; }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 48px; }
            .hero-card { display: none; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .roles-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-row { grid-template-columns: repeat(2, 1fr); gap: 32px; }
            .steps-row::before { display: none; }
        }
        @media (max-width: 640px) {
            .topbar { padding: 0 16px; }
            .topbar-left, .clock-date, .status-dot { display: none; }
            nav { padding: 0 16px; }
            .hero-inner { padding: 56px 20px; }
            .features-section, .how-section, .roles-section, .cta-section { padding: 56px 20px; }
            footer { padding: 24px 20px; flex-direction: column; text-align: center; }
            .features-grid, .roles-grid { grid-template-columns: 1fr; }
            .steps-row { grid-template-columns: 1fr; }
            .hero-stats { gap: 20px; flex-wrap: wrap; }
            .nav-text-org { display: none; }
        }
    </style>
</head>
<body>

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
        <a href="{{ route('login') }}" class="nav-login">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
            </svg>
            Login to System
        </a>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-inner">
            <div>
                <div class="hero-eyebrow">Official System &mdash; Municipality of Naic</div>
                <h1 class="hero-title">
                    Household Profiling &<br>
                    <span>Relief Distribution</span><br>
                    Management System
                </h1>
                <p class="hero-sub">
                    A centralized digital platform for the MDRRMO Naic to manage household profiles
                    across all barangays, generate QR-coded resident IDs, and efficiently track
                    ayuda distribution during disaster relief operations.
                </p>
                <div class="hero-cta-row">
                    <a href="{{ route('login') }}" class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                        </svg>
                        Access System
                    </a>
                    <a href="#features" class="btn-ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/>
                        </svg>
                        Learn More
                    </a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-num">30</div>
                        <div class="hero-stat-label">Barangays Covered</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">QR</div>
                        <div class="hero-stat-label">Coded Households</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">Live</div>
                        <div class="hero-stat-label">Distribution Tracking</div>
                    </div>
                </div>
            </div>

            <!-- Floating card (desktop only) -->
            <div class="hero-card">
                <div class="hero-card-logos">
                    <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO">
                    <div class="hero-card-divider"></div>
                    <img src="{{ asset('images/naic-seal.png') }}" alt="Naic">
                </div>
                <div class="hero-card-title">MDRRMO Naic, Cavite</div>
                <div class="hero-card-sub">
                    Municipal Disaster Risk Reduction<br>
                    and Management Office<br>
                    Republic of the Philippines
                </div>
                <div class="hero-card-badge">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Official Government System
                </div>
            </div>
        </div>
    </section>

    <!-- WAVE -->
    <div class="wave-divider" style="background:var(--blue-dark);">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,30 C240,60 480,0 720,30 C960,60 1200,0 1440,30 L1440,60 L0,60 Z" fill="#F7F8FA"/>
        </svg>
    </div>

    <!-- FEATURES -->
    <section class="features-section" id="features">
        <div class="section-inner">
            <div class="section-eyebrow">System Capabilities</div>
            <h2 class="section-title">Everything MDRRMO needs<br>in one platform</h2>
            <p class="section-sub">From household enrollment to real-time QR scanning during relief operations — the system covers the full cycle of disaster preparedness and response.</p>

            <div class="features-grid">
                <div class="feature-card fade-up">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
                        </svg>
                    </div>
                    <div class="feature-title">Household Profiling</div>
                    <div class="feature-desc">Comprehensive household registration capturing family composition, sector flags (PWD, senior, student), address, and socioeconomic data for all barangays in Naic.</div>
                </div>
                <div class="feature-card green fade-up">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/>
                            <polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/>
                            <rect x="8" y="8" width="8" height="8" rx="1"/>
                        </svg>
                    </div>
                    <div class="feature-title">QR Code Generation</div>
                    <div class="feature-desc">Each approved household is automatically assigned a unique serial code and QR sticker — enabling fast, error-free scanning during distribution events.</div>
                </div>
                <div class="feature-card yellow fade-up">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="feature-title">Distribution Events</div>
                    <div class="feature-desc">Create and manage ayuda distribution events with start/end tracking, barangay targeting, relief type logging, and real-time status monitoring.</div>
                </div>
                <div class="feature-card red fade-up">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="feature-title">Role-Based Access</div>
                    <div class="feature-desc">Separate portals for Admin, Staff, and Auditor roles — each with tailored permissions ensuring data integrity and accountability across all operations.</div>
                </div>
                <div class="feature-card teal fade-up">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="feature-title">Live Scan Tracking</div>
                    <div class="feature-desc">Staff scan household QR codes on-site during distribution. The system instantly logs who received relief, when, and by which staff member — in real time.</div>
                </div>
                <div class="feature-card orange fade-up">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                            <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
                        </svg>
                    </div>
                    <div class="feature-title">Audit Trail & Reports</div>
                    <div class="feature-desc">Full audit logging of all system actions, exportable distribution reports in Excel and PDF, and read-only auditor access for transparency and accountability.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how-section" id="how">
        <div class="section-inner">
            <div class="section-eyebrow">Process Overview</div>
            <h2 class="section-title">How the system works</h2>
            <p class="section-sub">A simple four-step cycle from household enrollment to verified relief distribution.</p>

            <div class="steps-row">
                <div class="step fade-up">
                    <div class="step-num">1</div>
                    <div class="step-title">Register Household</div>
                    <div class="step-desc">Staff encodes household head info, family members, barangay, and sector flags into the profiling system.</div>
                </div>
                <div class="step fade-up">
                    <div class="step-num">2</div>
                    <div class="step-title">Admin Approval & QR</div>
                    <div class="step-desc">Admin reviews and approves the household record. A unique QR serial code is automatically generated.</div>
                </div>
                <div class="step fade-up">
                    <div class="step-num">3</div>
                    <div class="step-title">Distribution Event</div>
                    <div class="step-desc">Admin creates a distribution event, sets target barangays, and activates it when relief operations begin.</div>
                </div>
                <div class="step fade-up">
                    <div class="step-num">4</div>
                    <div class="step-title">Scan & Record</div>
                    <div class="step-desc">Staff scans each household's QR code on-site. Distribution is logged instantly with timestamp and staff details.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ROLES -->
    <section class="roles-section" id="roles">
        <div class="section-inner">
            <div class="section-eyebrow">User Roles</div>
            <h2 class="section-title">Three levels of access</h2>
            <p class="section-sub">Each account type has a specific role in the system — ensuring the right people have the right tools.</p>

            <div class="roles-grid">
                <div class="role-card">
                    <div class="role-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="role-title">Administrator</div>
                    <div class="role-tag admin">Full Access</div>
                    <div class="role-desc">Manages household approvals, QR code generation, distribution events, user accounts, and has full visibility into all system data and audit logs.</div>
                </div>
                <div class="role-card">
                    <div class="role-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
                        </svg>
                    </div>
                    <div class="role-title">Staff / Encoder</div>
                    <div class="role-tag staff">Field Operations</div>
                    <div class="role-desc">Encodes household profiles, operates the QR scanner during active distribution events, and views their personal scan history and statistics.</div>
                </div>
                <div class="role-card">
                    <div class="role-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <div class="role-title">Auditor</div>
                    <div class="role-tag auditor">Read Only</div>
                    <div class="role-desc">Has read-only access to household records, distribution logs, and audit trails. Cannot modify any data — designed for oversight and compliance review.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <h2 class="cta-title">Ready to access the system?</h2>
        <p class="cta-sub">Login with your authorized MDRRMO account to get started.</p>
        <a href="{{ route('login') }}" class="btn-cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
            </svg>
            Login to System
        </a>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-brand">
            <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO">
            <div class="footer-brand-text">
                <strong>MDRRMO Naic, Cavite</strong>
                Municipal Disaster Risk Reduction and Management Office
            </div>
        </div>
        <div class="footer-center">Republic of the Philippines</div>
        <div class="footer-right">&copy; <span id="footer-year"></span> MDRRMO Naic. All rights reserved.</div>
    </footer>

    <script>
        // Clock
        function pad(n){ return String(n).padStart(2,'0'); }
        function updateClock() {
            const now = new Date();
            const days  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
            document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        }
        updateClock(); setInterval(updateClock, 1000);
        document.getElementById('footer-year').textContent = new Date().getFullYear();

        // Scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.12 });
        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    </script>
</body>
</html>