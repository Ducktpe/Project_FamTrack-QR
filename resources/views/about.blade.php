<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us — MDRRMO Naic</title>
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
            --green:       #16A34A;
            --green-pale:  #DCFCE7;
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
        body { font-family: 'Open Sans', sans-serif; color: var(--gray-800); background: var(--white); overflow-x: hidden; }

        /* ─── TOPBAR ─── */
        .topbar { background: var(--blue-dark); padding: 0 32px; height: 34px; display: flex; align-items: center; justify-content: space-between; }
        .topbar-left { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.3px; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .clock { font-size: 12px; font-weight: 600; color: var(--yellow); letter-spacing: 1px; font-variant-numeric: tabular-nums; }
        .clock-date { font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.45); }
        .status-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #4CAF50; box-shadow: 0 0 6px #4CAF50; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.35} }

        /* ─── NAVBAR ─── */
        nav { background: var(--white); border-bottom: 3px solid var(--yellow); box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 0 32px; height: 72px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
        .nav-brand { display: flex; align-items: center; gap: 14px; }
        .nav-brand img { height: 50px; width: 50px; object-fit: contain; }
        .nav-divider { width: 1px; height: 40px; background: var(--gray-200); }
        .nav-text-org { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); margin-bottom: 2px; }
        .nav-text-title { font-family: 'PT Serif', serif; font-size: 17px; font-weight: 700; color: var(--blue-dark); line-height: 1.2; }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .nav-back { display: inline-flex; align-items: center; gap: 7px; padding: 10px 18px; background: transparent; color: var(--blue); font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; text-decoration: none; border-radius: 4px; border: 1px solid var(--gray-200); transition: background 0.15s, border-color 0.15s; }
        .nav-back:hover { background: var(--blue-pale); border-color: var(--blue); }
        .nav-login { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: var(--blue); color: var(--white); font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; text-decoration: none; border-radius: 4px; transition: background 0.15s, transform 0.15s; }
        .nav-login:hover { background: var(--blue-dark); transform: translateY(-1px); }
        .nav-login svg, .nav-back svg { width: 14px; height: 14px; }

        /* ─── PAGE HERO ─── */
        .page-hero {
            position: relative;
            background: linear-gradient(135deg, #0A1E3F 0%, #122D5A 40%, #1B3F7A 100%);
            padding: 72px 48px 80px;
            overflow: hidden;
        }
        .page-hero::before {
            content: '';
            position: absolute; inset: 0;
            background: repeating-linear-gradient(135deg, rgba(255,255,255,0.012) 0px, rgba(255,255,255,0.012) 1px, transparent 1px, transparent 60px);
        }
        .page-hero-accent {
            position: absolute; top: 0; right: 0;
            width: 480px; height: 100%;
            background: linear-gradient(135deg, transparent 40%, rgba(245,197,24,0.07) 100%);
            pointer-events: none;
        }
        .page-hero-inner { position: relative; z-index: 2; max-width: 1100px; margin: 0 auto; }
        .page-hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; background: rgba(245,197,24,0.15); border: 1px solid rgba(245,197,24,0.3); color: var(--yellow); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; padding: 6px 14px; border-radius: 2px; margin-bottom: 20px; }
        .page-hero-eyebrow::before { content: ''; width: 6px; height: 6px; background: var(--yellow); border-radius: 50%; }
        .page-hero-title { font-family: 'PT Serif', serif; font-size: clamp(28px, 4vw, 48px); font-weight: 700; color: var(--white); line-height: 1.2; margin-bottom: 16px; }
        .page-hero-title span { color: var(--yellow); font-style: italic; }
        .page-hero-sub { font-size: 15px; color: rgba(255,255,255,0.6); max-width: 580px; line-height: 1.75; }

        /* ─── SECTION SHARED ─── */
        .section-wrap { max-width: 1100px; margin: 0 auto; padding: 0 48px; }
        .section-eyebrow { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--blue-light); margin-bottom: 10px; }
        .section-title { font-family: 'PT Serif', serif; font-size: clamp(22px, 3vw, 34px); font-weight: 700; color: var(--blue-dark); margin-bottom: 14px; line-height: 1.25; }
        .section-sub { font-size: 14px; color: var(--gray-600); line-height: 1.75; max-width: 580px; }

        /* ─── MISSION & VISION ─── */
        .mv-section { padding: 88px 0; background: var(--white); }
        .mv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-top: 52px; }
        .mv-card { padding: 40px 36px; border: 1px solid var(--gray-200); position: relative; overflow: hidden; }
        .mv-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
        .mv-card.mission::before { background: var(--blue); }
        .mv-card.vision::before  { background: var(--yellow); }
        .mv-card-icon { width: 52px; height: 52px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 22px; }
        .mv-card.mission .mv-card-icon { background: var(--blue-pale); }
        .mv-card.vision  .mv-card-icon { background: #FFFBEB; }
        .mv-card-icon svg { width: 24px; height: 24px; }
        .mv-card.mission .mv-card-icon svg { color: var(--blue); }
        .mv-card.vision  .mv-card-icon svg { color: var(--yellow-dark); }
        .mv-card-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; }
        .mv-card.mission .mv-card-label { color: var(--blue); }
        .mv-card.vision  .mv-card-label { color: var(--yellow-dark); }
        .mv-card-title { font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; color: var(--blue-dark); margin-bottom: 14px; line-height: 1.3; }
        .mv-card-text { font-size: 13.5px; color: var(--gray-600); line-height: 1.8; }

        /* ─── MANDATE ─── */
        .mandate-section { padding: 88px 0; background: var(--gray-50); border-top: 1px solid var(--gray-200); border-bottom: 1px solid var(--gray-200); }
        .mandate-layout { display: grid; grid-template-columns: 1fr 1.4fr; gap: 64px; align-items: start; margin-top: 48px; }
        .mandate-text { font-size: 14px; color: var(--gray-600); line-height: 1.85; }
        .mandate-text p { margin-bottom: 16px; }
        .mandate-text p:last-child { margin-bottom: 0; }
        .mandate-list { list-style: none; display: flex; flex-direction: column; gap: 14px; }
        .mandate-list li { display: flex; align-items: flex-start; gap: 12px; font-size: 13px; color: var(--gray-600); line-height: 1.6; }
        .mandate-list li::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--yellow); flex-shrink: 0; margin-top: 6px; border: 2px solid var(--yellow-dark); }

        /* ─── TEAM / PERSONNEL ─── */
        .team-section { padding: 88px 0; background: var(--white); }
        .team-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 52px; }
        .team-card { border: 1px solid var(--gray-200); background: var(--white); padding: 32px 24px; text-align: center; transition: box-shadow 0.2s, transform 0.2s; }
        .team-card:hover { box-shadow: 0 8px 32px rgba(27,63,122,0.1); transform: translateY(-3px); }
        .team-card.featured { border-top: 4px solid var(--yellow); grid-column: span 3; display: grid; grid-template-columns: auto 1fr; gap: 32px; text-align: left; align-items: center; }
        .team-avatar { width: 72px; height: 72px; border-radius: 50%; background: var(--blue); color: var(--white); font-family: 'PT Serif', serif; font-size: 26px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; flex-shrink: 0; }
        .team-card.featured .team-avatar { width: 88px; height: 88px; font-size: 32px; margin: 0; background: linear-gradient(135deg, var(--blue) 0%, var(--blue-light) 100%); box-shadow: 0 6px 24px rgba(27,63,122,0.25); }
        .team-name { font-family: 'PT Serif', serif; font-size: 16px; font-weight: 700; color: var(--blue-dark); margin-bottom: 4px; }
        .team-card.featured .team-name { font-size: 20px; }
        .team-role { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--blue-light); margin-bottom: 8px; }
        .team-designation { font-size: 11px; color: var(--gray-400); margin-bottom: 12px; }
        .team-card.featured .team-designation { font-size: 12px; }
        .team-desc { font-size: 12px; color: var(--gray-600); line-height: 1.65; }
        .team-badge { display: inline-block; padding: 3px 10px; border-radius: 2px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(245,197,24,0.15); color: var(--yellow-dark); border: 1px solid rgba(245,197,24,0.3); margin-bottom: 14px; }

        /* ─── SYSTEM SECTION ─── */
        .system-section { padding: 88px 0; background: var(--blue-dark); position: relative; overflow: hidden; }
        .system-section::before { content: ''; position: absolute; inset: 0; background: repeating-linear-gradient(135deg, rgba(255,255,255,0.015) 0, rgba(255,255,255,0.015) 1px, transparent 1px, transparent 48px); }
        .system-section .section-eyebrow { color: var(--yellow); }
        .system-section .section-title { color: var(--white); }
        .system-section .section-sub { color: rgba(255,255,255,0.55); }
        .system-inner { position: relative; z-index: 1; }
        .system-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 48px; }
        .system-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 28px 26px; border-radius: 4px; transition: background 0.2s; }
        .system-card:hover { background: rgba(255,255,255,0.09); }
        .system-card-icon { width: 42px; height: 42px; border-radius: 6px; background: rgba(245,197,24,0.12); border: 1px solid rgba(245,197,24,0.2); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .system-card-icon svg { width: 20px; height: 20px; color: var(--yellow); }
        .system-card-title { font-size: 14px; font-weight: 700; color: var(--white); margin-bottom: 8px; }
        .system-card-text { font-size: 12px; color: rgba(255,255,255,0.5); line-height: 1.7; }

        /* ─── CONTACT STRIP ─── */
        .contact-section { padding: 88px 0; background: var(--white); }
        .contact-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 48px; }
        .contact-card { padding: 28px 24px; border: 1px solid var(--gray-200); display: flex; flex-direction: column; gap: 10px; }
        .contact-card-icon { width: 40px; height: 40px; background: var(--blue-pale); border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .contact-card-icon svg { width: 18px; height: 18px; color: var(--blue); }
        .contact-card-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--gray-400); }
        .contact-card-value { font-size: 13px; color: var(--gray-800); font-weight: 500; line-height: 1.55; }
        .contact-card-value a { color: var(--blue); text-decoration: none; }
        .contact-card-value a:hover { color: var(--blue-light); text-decoration: underline; }

        /* ─── FOOTER ─── */
        footer { background: var(--blue-dark); border-top: 3px solid var(--yellow); }
        .footer-main { max-width: 1100px; margin: 0 auto; padding: 56px 48px 40px; display: grid; grid-template-columns: 1.6fr 1fr 1fr; gap: 48px; }
        .footer-brand-top { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
        .footer-brand-top img { height: 52px; width: 52px; object-fit: contain; opacity: 0.9; }
        .footer-brand-name { font-family: 'PT Serif', serif; font-size: 15px; font-weight: 700; color: var(--white); line-height: 1.3; }
        .footer-brand-sub { font-size: 11px; color: rgba(255,255,255,0.45); line-height: 1.6; max-width: 280px; }
        .footer-col-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--yellow); margin-bottom: 16px; }
        .footer-contact-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
        .footer-contact-item svg { width: 14px; height: 14px; color: rgba(255,255,255,0.35); flex-shrink: 0; margin-top: 2px; }
        .footer-contact-item span, .footer-contact-item a { font-size: 12px; color: rgba(255,255,255,0.55); line-height: 1.6; text-decoration: none; transition: color 0.15s; }
        .footer-contact-item a:hover { color: var(--yellow); }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { font-size: 12px; color: rgba(255,255,255,0.5); text-decoration: none; display: flex; align-items: center; gap: 6px; transition: color 0.15s; }
        .footer-links a::before { content: '›'; color: var(--yellow); font-size: 14px; }
        .footer-links a:hover { color: var(--white); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.08); padding: 18px 48px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .footer-bottom-left { font-size: 11px; color: rgba(255,255,255,0.25); }
        .footer-bottom-right { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 0.5px; text-transform: uppercase; }

        /* ─── SCROLL ANIMATIONS ─── */
        .fade-up { opacity: 0; transform: translateY(28px); transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        .fade-left { opacity: 0; transform: translateX(-36px); transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-left.visible { opacity: 1; transform: translateX(0); }
        .fade-right { opacity: 0; transform: translateX(36px); transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-right.visible { opacity: 1; transform: translateX(0); }
        .delay-1 { transition-delay: 0.08s; } .delay-2 { transition-delay: 0.16s; } .delay-3 { transition-delay: 0.24s; }
        .fade-up:nth-child(2){transition-delay:0.10s} .fade-up:nth-child(3){transition-delay:0.20s} .fade-up:nth-child(4){transition-delay:0.30s}

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .section-wrap { padding: 0 32px; }
            .mv-grid { grid-template-columns: 1fr; }
            .mandate-layout { grid-template-columns: 1fr; gap: 36px; }
            .team-grid { grid-template-columns: 1fr 1fr; }
            .team-card.featured { grid-column: span 2; }
            .system-grid { grid-template-columns: 1fr; }
            .contact-grid { grid-template-columns: 1fr; }
            .footer-main { grid-template-columns: 1fr 1fr; gap: 32px; }
            .page-hero { padding: 56px 32px 64px; }
        }
        @media (max-width: 640px) {
            .section-wrap { padding: 0 20px; }
            .topbar { padding: 0 16px; }
            nav { padding: 0 16px; height: 60px; }
            .nav-divider { display: none; }
            .nav-text-org { display: none; }
            .nav-text-title { font-size: 13px; }
            .nav-back span { display: none; } .nav-back { padding: 9px 10px; border-radius: 50%; width: 38px; height: 38px; justify-content: center; gap: 0; }
            .team-grid { grid-template-columns: 1fr; }
            .team-card.featured { grid-column: span 1; grid-template-columns: 1fr; text-align: center; }
            .team-card.featured .team-avatar { margin: 0 auto; }
            .footer-main { grid-template-columns: 1fr; padding: 40px 20px 32px; }
            .footer-bottom { padding: 16px 20px; }
            .page-hero { padding: 48px 20px 56px; }
        }
        @media (max-width: 480px) {
            .topbar { height: 28px; } .status-dot { display: none; }
            .mv-section, .mandate-section, .team-section, .system-section, .contact-section { padding: 64px 0; }
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
        <div class="nav-actions">
            <a href="{{ route('home') }}" class="nav-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                <span>Back to Home</span>
            </a>
            <a href="{{ route('login') }}" class="nav-login">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                </svg>
                <span>Login to System</span>
            </a>
        </div>
    </nav>

    <!-- PAGE HERO -->
    <section class="page-hero">
        <div class="page-hero-accent"></div>
        <div class="page-hero-inner">
            <div class="page-hero-eyebrow">About the Office</div>
            <h1 class="page-hero-title">
                Municipal Disaster Risk<br>
                Reduction and <span>Management Office</span>
            </h1>
            <p class="page-hero-sub">
                Serving the 30 barangays of Naic, Cavite — the MDRRMO leads disaster preparedness,
                response, and recovery operations, and manages the equitable distribution of relief
                aid to families in need.
            </p>
        </div>
    </section>

    <!-- MISSION & VISION -->
    <section class="mv-section">
        <div class="section-wrap">
            <div class="fade-up">
                <div class="section-eyebrow">Our Foundation</div>
                <h2 class="section-title">Mission & Vision</h2>
                <p class="section-sub">The guiding principles that shape every decision, program, and response operation of the MDRRMO Naic.</p>
            </div>
            <div class="mv-grid">
                <div class="mv-card mission fade-up delay-1">
                    <div class="mv-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="16"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                    </div>
                    <div class="mv-card-label">Mission</div>
                    <div class="mv-card-title">Disaster Resilient, Climate Change Adaptive and Safe Naic Community</div>
                    <p class="mv-card-text">
                        Disaster resilient, climate change adaptive and safe Naic community with a strong spirit
                        of stakeholder commitment guided by effective local governance ensuring social protection,
                        economic security and socially-inclusive disaster management towards sustainable development.
                    </p>
                </div>
                <div class="mv-card vision fade-up delay-2">
                    <div class="mv-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <div class="mv-card-label">Vision</div>
                    <div class="mv-card-title">Safer, Adaptive and Disaster-Resilient Filipino Communities</div>
                    <p class="mv-card-text">
                        Safer, adaptive and disaster-resilient Filipino communities towards sustainable development.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- MANDATE -->
    <section class="mandate-section">
        <div class="section-wrap">
            <div class="fade-left">
                <div class="section-eyebrow">Who We Are</div>
                <h2 class="section-title">Office of the Municipal Disaster Risk Reduction &amp; Management Officer</h2>
            </div>
            <div class="mandate-layout">
                <div class="mandate-text fade-left">
                    <p>
                        The OMDRRMO of Naic provides systematic and quality public safety services within its area of
                        responsibility in the form of promoting, conducting and establishing programs, plans and activities
                        in accordance with the four thematic areas of DRRM namely: Disaster Prevention &amp; Mitigation,
                        Disaster Preparedness, Disaster Response and Disaster Rehabilitation &amp; Recovery as is mandated
                        by the Philippine Disaster Risk Reduction and Management Act of 2010 otherwise known as
                        <strong>Republic Act 10121</strong>.
                    </p>
                    <p>
                        Our office coordinates with barangay-level DRRMCs, partner agencies such as the Philippine
                        National Police, Bureau of Fire Protection, and Philippine Coast Guard, to deliver rapid and
                        efficient emergency response to the communities of Naic, Cavite.
                    </p>
                    <p>
                        The Local Disaster Risk Reduction and Management Office of the Municipality of Naic was
                        established in 2014 in accordance with the Republic Act No. 10121, or the Philippine Disaster
                        Risk Reduction and Management Act of 2010 which is <strong><em>AN ACT STRENGTHENING THE PHILIPPINE
                        DISASTER RISK REDUCTION AND MANAGEMENT SYSTEM, PROVIDING FOR THE NATIONAL DISASTER RISK REDUCTION
                        AND MANAGEMENT FRAMEWORK AND INSTITUTIONALIZING THE NATIONAL DISASTER RISK REDUCTION AND MANAGEMENT
                        PLAN, APPROPRIATING FUNDS THEREFORE AND FOR OTHER PURPOSES.</em></strong>
                    </p>
                    <p>
                        Section 12 <strong>There shall be an established LDRRMO</strong> in every province, city and
                        Municipality and a Barangay Disaster Risk Reduction and Management Committee in every barangay
                        <strong>which shall be responsible for setting the direction, development, implementation and
                        coordination of disaster risk management programs within their territorial jurisdiction.</strong>
                    </p>
                </div>
                <div class="fade-right">
                    <ul class="mandate-list">
                        <li>Disaster Prevention &amp; Mitigation — reducing risks and vulnerabilities of communities before disasters occur</li>
                        <li>Disaster Preparedness — equipping communities, responders, and institutions with the knowledge and tools to anticipate and respond effectively</li>
                        <li>Disaster Response — coordinating and mobilizing resources and personnel to protect and assist affected communities during emergencies</li>
                        <li>Disaster Rehabilitation &amp; Recovery — restoring and improving facilities, livelihoods, and living conditions of disaster-affected communities</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- PERSONNEL / TEAM -->
    <section class="team-section">
        <div class="section-wrap">
            <div class="fade-up">
                <div class="section-eyebrow">Our People</div>
                <h2 class="section-title">Office Personnel</h2>
                <p class="section-sub">Dedicated public servants committed to the safety and welfare of every Naiqueño household.</p>
            </div>
            <div class="team-grid" style="margin-top:52px;">

                <!-- Office Head (featured) -->
                <div class="team-card featured fade-up">
                    <div class="team-avatar">AG</div>
                    <div>
                        <div class="team-badge">Department Head</div>
                        <div class="team-name">Augusto A. Gonzales</div>
                        <div class="team-role">Local DRRMO</div>
                        <div class="team-designation">MGDH-1, LDRRMO</div>
                        <p class="team-desc">
                            Leads the overall disaster risk reduction and management operations of the
                            municipality, overseeing all preparedness, response, and recovery programs
                            across the 30 barangays of Naic, Cavite.
                        </p>
                    </div>
                </div>

                <!-- Staff cards -->
                <div class="team-card fade-up delay-1">
                    <div class="team-avatar" style="background:var(--blue-light);">OP</div>
                    <div class="team-name">Operations Section</div>
                    <div class="team-role">Emergency Response</div>
                    <div class="team-designation">Field Operations & ERT Coordination</div>
                    <p class="team-desc">Handles on-the-ground emergency response, ERT deployment, and field logistics during disaster operations.</p>
                </div>
                <div class="team-card fade-up delay-2">
                    <div class="team-avatar" style="background:#0891B2;">PP</div>
                    <div class="team-name">Planning Section</div>
                    <div class="team-role">DRRM Planning</div>
                    <div class="team-designation">Risk Assessment & Preparedness</div>
                    <p class="team-desc">Prepares the Municipal DRRM Plan, conducts risk assessments, and maintains the hazard and vulnerability database.</p>
                </div>
                <div class="team-card fade-up delay-3">
                    <div class="team-avatar" style="background:#16A34A;">AD</div>
                    <div class="team-name">Admin & Finance Section</div>
                    <div class="team-role">Administration</div>
                    <div class="team-designation">LDRRMF Management & Logistics</div>
                    <p class="team-desc">Manages LDRRMF utilization, relief goods procurement, stockpiling, and administrative records of all DRRM activities.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ABOUT THE SYSTEM -->
    <section class="system-section">
        <div class="section-wrap system-inner">
            <div class="fade-up">
                <div class="section-eyebrow">Digital Transformation</div>
                <h2 class="section-title">About This System</h2>
                <p class="section-sub">The Household Profiling & Relief Distribution Management System was developed to modernize and bring transparency to ayuda distribution operations in Naic.</p>
            </div>
            <div class="system-grid">
                <div class="system-card fade-up delay-1">
                    <div class="system-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
                        </svg>
                    </div>
                    <div class="system-card-title">Household Profiling</div>
                    <p class="system-card-text">Every household across all 30 barangays of Naic is registered and profiled in the system, complete with family composition, program flags (4Ps, PWD, Senior), and unique QR-coded resident cards.</p>
                </div>
                <div class="system-card fade-up delay-2">
                    <div class="system-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </div>
                    <div class="system-card-title">QR Code Distribution</div>
                    <p class="system-card-text">Households receive unique QR-coded cards that staff scan during relief operations, eliminating paper-based lists and preventing duplicate or fraudulent claims.</p>
                </div>
                <div class="system-card fade-up delay-1">
                    <div class="system-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="system-card-title">Live Distribution Tracking</div>
                    <p class="system-card-text">Real-time dashboards give administrators and auditors a live view of distribution progress — how many households have received aid, which barangays are pending, and staff activity logs.</p>
                </div>
                <div class="system-card fade-up delay-2">
                    <div class="system-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="system-card-title">Accountability & Audit</div>
                    <p class="system-card-text">Every release is timestamped, geotagged, and photo-documented. Auditors have read-only access to verify the integrity of all distribution records at any time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section class="contact-section">
        <div class="section-wrap">
            <div class="fade-up">
                <div class="section-eyebrow">Get in Touch</div>
                <h2 class="section-title">Contact the Office</h2>
                <p class="section-sub">Reach out to the MDRRMO Naic for inquiries about disaster preparedness, relief operations, or household registration.</p>
            </div>
            <div class="contact-grid">
                <div class="contact-card fade-up delay-1">
                    <div class="contact-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="contact-card-label">Office Address</div>
                    <div class="contact-card-value">
                        Antero Soriano Highway, Naic,<br>
                        Cavite, Philippines
                    </div>
                </div>
                <div class="contact-card fade-up delay-2">
                    <div class="contact-card-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                        </svg>
                    </div>
                    <div class="contact-card-label">Facebook Page</div>
                    <div class="contact-card-value">
                        <a href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
                            facebook.com/naicmdrrmo
                        </a>
                    </div>
                </div>
                <div class="contact-card fade-up delay-3">
                    <div class="contact-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                        </svg>
                    </div>
                    <div class="contact-card-label">Jurisdiction</div>
                    <div class="contact-card-value">
                        Municipality of Naic<br>
                        Province of Cavite — 30 Barangays
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-main">
            <div>
                <div class="footer-brand-top">
                    <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO Logo">
                    <div class="footer-brand-name">MDRRMO<br>Naic, Cavite</div>
                </div>
                <p class="footer-brand-sub">Municipal Disaster Risk Reduction and Management Office — dedicated to protecting every household in Naic.</p>
            </div>
            <div>
                <div class="footer-col-title">Contact</div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Antero Soriano Hwy, Naic,<br>Cavite, Philippines</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    <a href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">facebook.com/naicmdrrmo</a>
                </div>
            </div>
            <div>
                <div class="footer-col-title">Quick Links</div>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('login') }}">Login to System</a></li>
                </ul>
                <div class="footer-col-title" style="margin-top:20px;">Department Head</div>
                <p style="font-size:12px;color:rgba(255,255,255,0.55);line-height:1.6;">
                    Augusto A. Gonzales<br>
                    <span style="color:rgba(255,255,255,0.35);font-size:11px;">MGDH-1, LDRRMO</span>
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-left">&copy; <span id="footer-year"></span> MDRRMO Naic, Cavite. All rights reserved.</div>
            <div class="footer-bottom-right">Republic of the Philippines &nbsp;·&nbsp; Province of Cavite &nbsp;·&nbsp; Municipality of Naic</div>
        </div>
    </footer>

    <script>
        // ─── Clock ───
        function pad(n){ return String(n).padStart(2,'0'); }
        function updateClock() {
            const now    = new Date();
            const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
            document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        }
        updateClock(); setInterval(updateClock, 1000);
        document.getElementById('footer-year').textContent = new Date().getFullYear();

        // ─── Scroll Animations ───
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
            });
        }, { threshold: 0.08 });
        document.querySelectorAll('.fade-up, .fade-left, .fade-right').forEach(el => observer.observe(el));
    </script>
</body>
</html>