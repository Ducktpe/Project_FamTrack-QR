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
        .nav-login { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: var(--blue); color: var(--white); font-family: 'Open Sans', sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; text-decoration: none; border-radius: 4px; transition: background 0.15s, transform 0.15s; }
        .nav-login:hover { background: var(--blue-dark); transform: translateY(-1px); }
        .nav-login svg { width: 14px; height: 14px; }

        /* ─── HERO ─── */
        @keyframes heroGradientShift { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
        @keyframes heroAuroraA { 0%{transform:translate(0%,0%) scale(1);opacity:0.18} 33%{transform:translate(8%,-6%) scale(1.1);opacity:0.26} 66%{transform:translate(-5%,8%) scale(0.95);opacity:0.14} 100%{transform:translate(0%,0%) scale(1);opacity:0.18} }
        @keyframes heroAuroraB { 0%{transform:translate(0%,0%) scale(1);opacity:0.12} 33%{transform:translate(-10%,5%) scale(1.05);opacity:0.20} 66%{transform:translate(6%,-8%) scale(1.1);opacity:0.10} 100%{transform:translate(0%,0%) scale(1);opacity:0.12} }

        .hero { position: relative; background: linear-gradient(135deg,#0A1E3F 0%,#122D5A 25%,#1B3F7A 50%,#0E2348 75%,#0A1E3F 100%); background-size: 300% 300%; animation: heroGradientShift 14s ease infinite; min-height: 92vh; display: flex; align-items: center; overflow: hidden; }
        .hero::after { content: ''; position: absolute; inset: 0; z-index: 0; background: radial-gradient(ellipse 60% 50% at 20% 40%,rgba(36,89,168,0.45) 0%,transparent 70%), radial-gradient(ellipse 50% 60% at 80% 60%,rgba(27,63,122,0.35) 0%,transparent 70%); animation: heroAuroraA 18s ease-in-out infinite; }
        .hero-aurora-b { position: absolute; inset: 0; z-index: 0; pointer-events: none; background: radial-gradient(ellipse 55% 45% at 70% 25%,rgba(245,197,24,0.06) 0%,transparent 65%), radial-gradient(ellipse 40% 55% at 15% 75%,rgba(36,89,168,0.3) 0%,transparent 65%); animation: heroAuroraB 22s ease-in-out infinite; }
        .hero-bg-shapes { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
        .hero::before { content: ''; position: absolute; inset: 0; z-index: 1; background: repeating-linear-gradient(135deg,rgba(255,255,255,0.012) 0px,rgba(255,255,255,0.012) 1px,transparent 1px,transparent 60px); }

        .hero-inner { position: relative; z-index: 3; max-width: 1200px; margin: 0 auto; padding: 80px 48px; display: grid; grid-template-columns: 1fr 420px; gap: 64px; align-items: center; width: 100%; }
        .hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; background: rgba(245,197,24,0.15); border: 1px solid rgba(245,197,24,0.3); color: var(--yellow); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; padding: 6px 14px; border-radius: 2px; margin-bottom: 22px; }
        .hero-eyebrow::before { content: ''; width: 6px; height: 6px; background: var(--yellow); border-radius: 50%; animation: blink 2s infinite; }
        .hero-title { font-family: 'PT Serif', serif; font-size: clamp(32px,4.5vw,54px); font-weight: 700; color: var(--white); line-height: 1.18; margin-bottom: 22px; }
        .hero-title span { color: var(--yellow); font-style: italic; }
        .hero-sub { font-size: 15px; color: rgba(255,255,255,0.65); line-height: 1.75; max-width: 520px; margin-bottom: 36px; font-weight: 400; }
        .hero-cta-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }

        .btn-primary { display: inline-flex; align-items: center; gap: 9px; padding: 14px 28px; background: var(--yellow); color: var(--blue-dark); font-family: 'Open Sans', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; text-decoration: none; border-radius: 4px; transition: background 0.15s, transform 0.15s, box-shadow 0.15s; box-shadow: 0 4px 16px rgba(245,197,24,0.3); }
        .btn-primary:hover { background: var(--yellow-dark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(245,197,24,0.35); }
        .btn-primary svg { width: 15px; height: 15px; }

        .btn-ghost { display: inline-flex; align-items: center; gap: 9px; padding: 14px 24px; background: transparent; color: rgba(255,255,255,0.75); font-family: 'Open Sans', sans-serif; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); transition: border-color 0.15s, color 0.15s, background 0.15s; }
        .btn-ghost:hover { border-color: rgba(255,255,255,0.5); color: var(--white); background: rgba(255,255,255,0.06); }
        .btn-ghost svg { width: 14px; height: 14px; }

        /* ─── SEE EVENTS BUTTON ─── */
        .btn-events {
            display: inline-flex; align-items: center; gap: 9px;
            padding: 14px 24px;
            background: transparent;
            color: rgba(255,255,255,0.85);
            font-family: 'Open Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            text-decoration: none; border-radius: 4px;
            border: 1px solid rgba(245,197,24,0.4);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .btn-events:hover { border-color: var(--yellow); color: var(--yellow); background: rgba(245,197,24,0.08); transform: translateY(-2px); }
        .btn-events svg { width: 15px; height: 15px; }
        .btn-events .event-badge {
            position: absolute;
            top: -8px; right: -8px;
            min-width: 18px; height: 18px;
            background: #E53E3E;
            border-radius: 9px;
            font-size: 10px; font-weight: 700;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 0 5px;
            border: 2px solid #0A1E3F;
            animation: pulseBadge 2s infinite;
        }
        @keyframes pulseBadge { 0%,100%{box-shadow:0 0 0 0 rgba(229,62,62,0.5)} 50%{box-shadow:0 0 0 5px rgba(229,62,62,0)} }

        .hero-stats { display: flex; gap: 32px; margin-top: 48px; padding-top: 32px; padding-left: 120px; border-top: 1px solid rgba(255,255,255,0.1); }
        .hero-stat-num { font-family: 'PT Serif', serif; font-size: 28px; font-weight: 700; color: var(--yellow); line-height: 1; margin-bottom: 4px; }
        .hero-stat-label { font-size: 11px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 1px; }

        .hero-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 40px 32px; text-align: center; backdrop-filter: blur(8px); animation: floatCard 6s ease-in-out infinite; }
        @keyframes floatCard { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .hero-card-logos { display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 24px; }
        .hero-card-logos img { width: 96px; height: 96px; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4)); }
        .hero-card-divider { width: 1px; height: 80px; background: rgba(255,255,255,0.15); }
        .hero-card-title { font-family: 'PT Serif', serif; font-size: 15px; font-weight: 700; color: var(--white); margin-bottom: 6px; }
        .hero-card-sub { font-size: 11px; color: rgba(255,255,255,0.45); letter-spacing: 0.5px; line-height: 1.6; }
        .hero-card-badge { display: inline-flex; align-items: center; gap: 6px; margin-top: 20px; padding: 8px 16px; background: rgba(245,197,24,0.1); border: 1px solid rgba(245,197,24,0.25); border-radius: 3px; font-size: 11px; font-weight: 600; color: var(--yellow); letter-spacing: 0.5px; }

        /* ─── EVENTS MODAL ─── */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 1000;
            background: rgba(10,30,63,0.75);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }

        .modal-box {
            background: var(--white);
            border-radius: 10px;
            width: 100%; max-width: 820px;
            max-height: 88vh;
            overflow: hidden;
            display: flex; flex-direction: column;
            box-shadow: 0 32px 80px rgba(0,0,0,0.35);
            transform: translateY(20px) scale(0.97);
            transition: transform 0.25s cubic-bezier(0.22,1,0.36,1);
        }
        .modal-overlay.open .modal-box { transform: translateY(0) scale(1); }

        .modal-header {
            background: var(--blue-dark);
            padding: 20px 28px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 3px solid var(--yellow);
            flex-shrink: 0;
        }
        .modal-header-left { display: flex; align-items: center; gap: 12px; }
        .modal-header-icon {
            width: 38px; height: 38px;
            background: rgba(245,197,24,0.15);
            border: 1px solid rgba(245,197,24,0.3);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
        }
        .modal-header-icon svg { width: 18px; height: 18px; color: var(--yellow); }
        .modal-title { font-family: 'PT Serif', serif; font-size: 18px; font-weight: 700; color: var(--white); }
        .modal-subtitle { font-size: 11px; color: rgba(255,255,255,0.45); margin-top: 2px; }
        .modal-close {
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: rgba(255,255,255,0.6);
            transition: background 0.15s, color 0.15s;
        }
        .modal-close:hover { background: rgba(229,62,62,0.2); color: #FC8181; border-color: rgba(229,62,62,0.3); }
        .modal-close svg { width: 16px; height: 16px; }

        /* Filter tabs */
        .modal-filters {
            padding: 16px 28px 0;
            display: flex; gap: 8px;
            flex-shrink: 0;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }
        .filter-tab {
            padding: 9px 18px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            border: none; background: transparent;
            color: var(--gray-400); cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: color 0.15s, border-color 0.15s;
        }
        .filter-tab.active { color: var(--blue); border-bottom-color: var(--blue); }
        .filter-tab:hover:not(.active) { color: var(--gray-600); }
        .filter-tab .tab-count {
            display: inline-flex; align-items: center; justify-content: center;
            width: 18px; height: 18px; border-radius: 9px;
            font-size: 10px; margin-left: 6px;
            background: var(--gray-200); color: var(--gray-600);
        }
        .filter-tab.active .tab-count { background: var(--blue); color: #fff; }
        .filter-tab.ongoing-tab.active { color: #16A34A; border-bottom-color: #16A34A; }
        .filter-tab.ongoing-tab.active .tab-count { background: #16A34A; }
        .filter-tab.upcoming-tab.active { color: var(--blue-light); border-bottom-color: var(--blue-light); }
        .filter-tab.upcoming-tab.active .tab-count { background: var(--blue-light); }

        /* Table area */
        .modal-body { overflow-y: auto; flex: 1; padding: 0; }

        /* Empty state */
        .modal-empty {
            text-align: center; padding: 64px 32px;
            color: var(--gray-400);
        }
        .modal-empty svg { width: 48px; height: 48px; color: var(--gray-200); margin-bottom: 12px; }
        .modal-empty-title { font-size: 15px; font-weight: 600; color: var(--gray-600); margin-bottom: 6px; }
        .modal-empty-sub { font-size: 13px; }

        /* Event cards */
        .event-list { padding: 20px 28px; display: flex; flex-direction: column; gap: 14px; }

        .event-card {
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .event-card:hover { box-shadow: 0 6px 20px rgba(27,63,122,0.1); transform: translateY(-2px); }

        .event-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 18px;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }
        .event-card-name { font-size: 14px; font-weight: 700; color: var(--blue-dark); }
        .event-status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
        }
        .event-status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .badge-ongoing { background: #DCFCE7; color: #15803D; }
        .badge-ongoing::before { background: #16A34A; animation: blink 1.5s infinite; }
        .badge-upcoming { background: #DBEAFE; color: #1D4ED8; }
        .badge-upcoming::before { background: #3B82F6; }

        .event-card-body { padding: 14px 18px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px 24px; }
        .event-meta { display: flex; align-items: flex-start; gap: 8px; }
        .event-meta svg { width: 13px; height: 13px; color: var(--gray-400); flex-shrink: 0; margin-top: 2px; }
        .event-meta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--gray-400); margin-bottom: 2px; }
        .event-meta-value { font-size: 12px; color: var(--gray-800); font-weight: 500; line-height: 1.4; }

        .event-card-footer {
            padding: 10px 18px;
            background: var(--blue-pale);
            border-top: 1px solid var(--gray-200);
            display: flex; align-items: center; gap: 8px;
        }
        .event-card-footer svg { width: 12px; height: 12px; color: var(--blue-light); flex-shrink: 0; }
        .event-card-footer span { font-size: 11px; color: var(--blue-light); font-weight: 500; }

        /* Modal footer */
        .modal-footer {
            padding: 14px 28px;
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .modal-footer-note { font-size: 11px; color: var(--gray-400); display: flex; align-items: center; gap: 6px; }
        .modal-footer-note svg { width: 13px; height: 13px; }
        .modal-footer-close {
            padding: 9px 20px;
            background: var(--blue); color: #fff;
            font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
            border: none; border-radius: 4px; cursor: pointer;
            transition: background 0.15s;
        }
        .modal-footer-close:hover { background: var(--blue-dark); }

        /* ─── FEATURES ─── */
        .features-section { background: var(--gray-50); padding: 88px 48px; }
        .section-inner { max-width: 1200px; margin: 0 auto; }
        .section-eyebrow { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--blue-light); margin-bottom: 12px; }
        .section-title { font-family: 'PT Serif', serif; font-size: clamp(24px,3vw,36px); font-weight: 700; color: var(--blue-dark); margin-bottom: 14px; line-height: 1.25; }
        .section-sub { font-size: 14px; color: var(--gray-600); max-width: 560px; line-height: 1.7; margin-bottom: 52px; }
        .features-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
        .feature-card { background: var(--white); border: 1px solid var(--gray-200); border-top: 3px solid var(--blue); padding: 28px 24px; transition: transform 0.2s, box-shadow 0.2s; }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(27,63,122,0.1); }
        .feature-card.green  { border-top-color: #16A34A; }
        .feature-card.yellow { border-top-color: var(--yellow-dark); }
        .feature-card.red    { border-top-color: #C0392B; }
        .feature-card.teal   { border-top-color: #0891B2; }
        .feature-card.orange { border-top-color: #D97706; }
        .feature-icon { width: 44px; height: 44px; background: var(--blue-pale); border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
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
        .how-section { background: var(--white); padding: 88px 48px; }
        .steps-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 0; position: relative; margin-top: 8px; }
        .steps-row::before { content: ''; position: absolute; top: 28px; left: calc(12.5% + 20px); right: calc(12.5% + 20px); height: 2px; background: var(--gray-200); z-index: 0; }
        .step { text-align: center; padding: 0 16px; position: relative; z-index: 1; }
        .step-num { width: 56px; height: 56px; border-radius: 50%; background: var(--blue); color: var(--white); font-family: 'PT Serif', serif; font-size: 20px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; border: 4px solid var(--white); box-shadow: 0 0 0 2px var(--blue), 0 4px 12px rgba(27,63,122,0.2); }
        .step-title { font-size: 13px; font-weight: 700; color: var(--blue-dark); margin-bottom: 8px; }
        .step-desc  { font-size: 12px; color: var(--gray-600); line-height: 1.6; }

        /* ─── ROLES SECTION ─── */
        .roles-section { background: var(--blue-dark); padding: 88px 48px; position: relative; overflow: hidden; }
        .roles-section::before { content: ''; position: absolute; inset: 0; background: repeating-linear-gradient(135deg,rgba(255,255,255,0.015) 0,rgba(255,255,255,0.015) 1px,transparent 1px,transparent 48px); }
        .roles-section .section-title { color: var(--white); }
        .roles-section .section-sub   { color: rgba(255,255,255,0.55); }
        .roles-section .section-eyebrow { color: var(--yellow); }
        .roles-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; position: relative; z-index: 1; }
        .role-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 28px 24px; transition: background 0.2s, transform 0.2s; }
        .role-card:hover { background: rgba(255,255,255,0.09); transform: translateY(-3px); }
        .role-icon { width: 44px; height: 44px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; background: rgba(245,197,24,0.12); }
        .role-icon svg { width: 22px; height: 22px; color: var(--yellow); }
        .role-title { font-size: 15px; font-weight: 700; color: var(--white); margin-bottom: 6px; }
        .role-tag { display: inline-block; padding: 2px 8px; border-radius: 2px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .role-tag.admin   { background: rgba(36,89,168,0.35); color: #93C5FD; }
        .role-tag.staff   { background: rgba(22,163,74,0.3);  color: #86EFAC; }
        .role-tag.auditor { background: rgba(217,119,6,0.3);  color: #FCD34D; }
        .role-desc { font-size: 12px; color: rgba(255,255,255,0.5); line-height: 1.65; }

        /* ─── CTA BANNER ─── */
        .cta-section { position: relative; background: #ffffff; padding: 88px 48px; text-align: center; overflow: hidden; }
        .cta-section .cta-bg { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; }
        .cta-inner { position: relative; z-index: 2; }
        .cta-eyebrow { display: inline-block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--blue); margin-bottom: 14px; padding: 5px 14px; border: 1px solid rgba(27,63,122,0.25); border-radius: 2px; }
        .cta-title { font-family: 'PT Serif', serif; font-size: clamp(22px,3vw,36px); font-weight: 700; color: var(--blue-dark); margin-bottom: 12px; line-height: 1.25; }
        .cta-title span { color: var(--yellow-dark); font-style: italic; }
        .cta-sub { font-size: 14px; color: var(--gray-600); margin-bottom: 36px; max-width: 440px; margin-left: auto; margin-right: auto; line-height: 1.7; }
        .btn-cta { display: inline-flex; align-items: center; gap: 9px; padding: 15px 36px; background: var(--yellow); color: var(--blue-dark); font-family: 'Open Sans', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; text-decoration: none; border-radius: 4px; box-shadow: 0 6px 24px rgba(245,197,24,0.35); transition: background 0.15s, transform 0.15s, box-shadow 0.15s; }
        .btn-cta:hover { background: var(--yellow-dark); transform: translateY(-2px); box-shadow: 0 10px 32px rgba(245,197,24,0.45); }
        .btn-cta svg { width: 15px; height: 15px; }

        /* ─── FOOTER ─── */
        footer { background: var(--blue-dark); border-top: 3px solid var(--yellow); }
        .footer-main { max-width: 1200px; margin: 0 auto; padding: 56px 48px 40px; display: grid; grid-template-columns: 1.6fr 1fr 1fr; gap: 48px; }
        .footer-brand { display: flex; flex-direction: column; gap: 14px; }
        .footer-brand-top { display: flex; align-items: center; gap: 14px; }
        .footer-brand-top img { height: 52px; width: 52px; object-fit: contain; opacity: 0.9; }
        .footer-brand-name { font-family: 'PT Serif', serif; font-size: 15px; font-weight: 700; color: var(--white); line-height: 1.3; }
        .footer-brand-sub { font-size: 11px; color: rgba(255,255,255,0.45); line-height: 1.6; max-width: 280px; }
        .footer-social { display: flex; gap: 10px; margin-top: 4px; }
        .footer-social a { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); border-radius: 4px; color: rgba(255,255,255,0.6); transition: background 0.15s, color 0.15s; }
        .footer-social a:hover { background: var(--yellow); color: var(--blue-dark); border-color: var(--yellow); }
        .footer-social svg { width: 15px; height: 15px; }
        .footer-col-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--yellow); margin-bottom: 16px; }
        .footer-contact-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
        .footer-contact-item svg { width: 14px; height: 14px; color: rgba(255,255,255,0.35); flex-shrink: 0; margin-top: 2px; }
        .footer-contact-item span { font-size: 12px; color: rgba(255,255,255,0.55); line-height: 1.6; }
        .footer-contact-item a { font-size: 12px; color: rgba(255,255,255,0.55); text-decoration: none; transition: color 0.15s; }
        .footer-contact-item a:hover { color: var(--yellow); }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { font-size: 12px; color: rgba(255,255,255,0.5); text-decoration: none; display: flex; align-items: center; gap: 6px; transition: color 0.15s; }
        .footer-links a::before { content: '›'; color: var(--yellow); font-size: 14px; }
        .footer-links a:hover { color: var(--white); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.08); padding: 18px 48px; max-width: 100%; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .footer-bottom-left { font-size: 11px; color: rgba(255,255,255,0.25); }
        .footer-bottom-right { font-size: 10px; color: rgba(255,255,255,0.2); letter-spacing: 0.5px; text-transform: uppercase; }

        /* ─── SCROLL ANIMATIONS ─── */
        .fade-up { opacity: 0; transform: translateY(32px); transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        .fade-left { opacity: 0; transform: translateX(-40px); transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-left.visible { opacity: 1; transform: translateX(0); }
        .fade-right { opacity: 0; transform: translateX(40px); transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1), transform 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-right.visible { opacity: 1; transform: translateX(0); }
        .scale-in { opacity: 0; transform: scale(0.9); transition: opacity 0.65s cubic-bezier(0.22,1,0.36,1), transform 0.65s cubic-bezier(0.22,1,0.36,1); }
        .scale-in.visible { opacity: 1; transform: scale(1); }
        .delay-1 { transition-delay: 0.08s; } .delay-2 { transition-delay: 0.16s; } .delay-3 { transition-delay: 0.24s; } .delay-4 { transition-delay: 0.32s; } .delay-5 { transition-delay: 0.40s; } .delay-6 { transition-delay: 0.48s; }
        .fade-up:nth-child(2){transition-delay:0.10s} .fade-up:nth-child(3){transition-delay:0.20s} .fade-up:nth-child(4){transition-delay:0.30s} .fade-up:nth-child(5){transition-delay:0.40s} .fade-up:nth-child(6){transition-delay:0.50s}
        @keyframes numPop { 0%{transform:scale(0.5) translateY(10px);opacity:0} 70%{transform:scale(1.1) translateY(0)} 100%{transform:scale(1) translateY(0);opacity:1} }
        .num-pop { opacity: 0; }
        .num-pop.visible { animation: numPop 0.55s cubic-bezier(0.22,1,0.36,1) forwards; }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 40px; }
            .hero-card { display: none; }
            .hero-stats { padding-left: 0; gap: 28px; flex-wrap: wrap; }
            .features-grid { grid-template-columns: repeat(2,1fr); gap: 16px; }
            .roles-grid { grid-template-columns: repeat(2,1fr); gap: 16px; }
            .steps-row { grid-template-columns: repeat(2,1fr); gap: 40px 32px; }
            .steps-row::before { display: none; }
            .footer-main { grid-template-columns: 1fr 1fr; gap: 36px; padding: 48px 40px 32px; }
            .footer-brand { grid-column: 1 / -1; }
        }
        @media (max-width: 768px) {
            .topbar { padding: 0 20px; height: 30px; } .topbar-left { display: none; } .clock-date { display: none; }
            nav { padding: 0 20px; height: 64px; }
            .nav-brand img { height: 40px; width: 40px; } .nav-text-org { display: none; } .nav-text-title { font-size: 14px; } .nav-divider { height: 32px; } .nav-login { padding: 9px 16px; font-size: 11px; }
            .hero-inner { padding: 60px 24px; gap: 32px; }
            .hero-cta-row { gap: 10px; }
            .btn-primary, .btn-ghost, .btn-events { padding: 13px 18px; font-size: 12px; }
            .hero-stats { gap: 20px; margin-top: 32px; padding-top: 24px; }
            .features-section, .how-section, .roles-section, .cta-section { padding: 64px 24px; }
            .features-grid { grid-template-columns: 1fr; gap: 14px; }
            .steps-row { grid-template-columns: 1fr; gap: 28px; }
            .step { padding: 0; display: grid; grid-template-columns: 56px 1fr; gap: 0 18px; text-align: left; align-items: start; }
            .step-num { margin: 0; grid-row: 1 / 3; }
            .step-title { font-size: 14px; padding-top: 6px; }
            .step-desc { grid-column: 2; }
            .roles-grid { grid-template-columns: 1fr; gap: 14px; }
            .footer-main { grid-template-columns: 1fr; gap: 28px; padding: 40px 24px 24px; }
            .footer-brand { grid-column: auto; }
            .footer-bottom { padding: 16px 24px; flex-direction: column; gap: 6px; text-align: center; }
            .modal-box { max-height: 95vh; }
            .event-card-body { grid-template-columns: 1fr; gap: 8px; }
            .fade-left, .fade-right { transform: translateY(24px); }
            .fade-left.visible, .fade-right.visible { transform: translateY(0); }
        }
        @media (max-width: 480px) {
            .topbar { height: 28px; } .status-dot { display: none; } .clock { font-size: 11px; }
            nav { height: 58px; padding: 0 16px; }
            .nav-brand { gap: 10px; } .nav-brand img { height: 34px; width: 34px; } .nav-divider { display: none; } .nav-text-title { font-size: 13px; }
            .nav-login span { display: none; } .nav-login { padding: 9px 12px; gap: 0; border-radius: 50%; width: 38px; height: 38px; justify-content: center; }
            .hero-inner { padding: 48px 16px 40px; }
            .btn-primary, .btn-ghost, .btn-events { width: 100%; justify-content: center; }
            .hero-cta-row { flex-direction: column; gap: 10px; }
            .hero-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; text-align: center; }
            .modal-filters { padding: 12px 16px 0; gap: 4px; }
            .filter-tab { padding: 8px 12px; font-size: 10px; }
            .event-list { padding: 16px; }
            .modal-header { padding: 16px 20px; }
            .modal-footer { padding: 12px 20px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
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
            <span>Login to System</span>
        </a>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-aurora-b"></div>
        <svg class="hero-bg-shapes" viewBox="0 0 1440 800" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="yellowShimTR" cx="90%" cy="10%" r="70%" gradientUnits="objectBoundingBox">
                    <stop offset="0%"   stop-color="#FFD94A"/>
                    <stop offset="50%"  stop-color="#F5C518"/>
                    <stop offset="100%" stop-color="#B8900A"/>
                </radialGradient>
                <radialGradient id="yellowShimBL" cx="10%" cy="90%" r="70%" gradientUnits="objectBoundingBox">
                    <stop offset="0%"   stop-color="#FFD94A"/>
                    <stop offset="50%"  stop-color="#F5C518"/>
                    <stop offset="100%" stop-color="#B8900A"/>
                </radialGradient>
            </defs>
            <polygon points="1440,0 1440,360 1060,0" fill="url(#yellowShimTR)"/>
            <polygon points="1060,0 1440,360 1440,480 880,0" fill="#0E2348"/>
            <polygon points="880,0 1440,480 1440,570 760,0" fill="#2E4A6E" opacity="0.8"/>
            <polygon points="760,0 1440,570 1440,620 700,0" fill="rgba(255,255,255,0.05)"/>
            <polygon points="0,800 260,800 0,560" fill="url(#yellowShimBL)"/>
            <polygon points="0,560 260,800 400,800 0,460" fill="#0E2348"/>
            <polygon points="0,460 400,800 490,800 0,390" fill="#2E4A6E" opacity="0.8"/>
            <polygon points="0,390 490,800 520,800 0,368" fill="rgba(255,255,255,0.05)"/>
        </svg>

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

                    {{-- SEE EVENTS BUTTON --}}
                    <button class="btn-events" onclick="openEventsModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        See Events
                        @php
                            $totalEvents = $events->count();
                        @endphp
                        @if($totalEvents > 0)
                            <span class="event-badge">{{ $totalEvents }}</span>
                        @endif
                    </button>

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

    <!-- ═══════════════════════════════════════════ -->
    <!-- EVENTS MODAL                                -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="modal-overlay" id="eventsModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-box">

            <!-- Header -->
            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="modal-title" id="modalTitle">Distribution Events</div>
                        <div class="modal-subtitle">Ongoing &amp; Upcoming relief distribution schedules</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeEventsModal()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <!-- Filter Tabs -->
            <div class="modal-filters">
                @php
                    $ongoingEvents  = $events->where('status', 'ongoing');
                    $upcomingEvents = $events->where('status', 'upcoming');
                @endphp
                <button class="filter-tab active" id="tab-all" onclick="filterEvents('all')">
                    All <span class="tab-count">{{ $events->count() }}</span>
                </button>
                <button class="filter-tab ongoing-tab" id="tab-ongoing" onclick="filterEvents('ongoing')">
                    Ongoing <span class="tab-count">{{ $ongoingEvents->count() }}</span>
                </button>
                <button class="filter-tab upcoming-tab" id="tab-upcoming" onclick="filterEvents('upcoming')">
                    Upcoming <span class="tab-count">{{ $upcomingEvents->count() }}</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                @if($events->isEmpty())
                    <div class="modal-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <div class="modal-empty-title">No Active Events</div>
                        <div class="modal-empty-sub">There are no ongoing or upcoming distribution events at this time.</div>
                    </div>
                @else
                    <div class="event-list" id="eventList">
                        @foreach($events as $event)
                            @php
                                $barangays = [];
                                if ($event->target_barangay) {
                                    $raw = $event->target_barangay;
                                    if (is_array($raw)) {
                                        $barangays = $raw;
                                    } else {
                                        $decoded = json_decode($raw, true);
                                        $barangays = is_array($decoded) ? $decoded : [$raw];
                                    }
                                }
                                $barangayText = !empty($barangays) ? implode(', ', $barangays) : 'All Barangays';

                                $reliefItems = [];
                                if ($event->relief_items) {
                                    $raw = $event->relief_items;
                                    if (is_array($raw)) {
                                        $reliefItems = $raw;
                                    } else {
                                        $decoded = json_decode($raw, true);
                                        $reliefItems = is_array($decoded) ? $decoded : [$raw];
                                    }
                                }
                                $reliefText = !empty($reliefItems)
                                ? implode(', ', array_map(fn($i) => is_array($i) ? implode(', ', $i) : $i, $reliefItems))
                                : ($event->relief_type ?? '—');
                            @endphp
                            <div class="event-card" data-status="{{ $event->status }}">
                                <div class="event-card-header">
                                    <div class="event-card-name">{{ $event->event_name }}</div>
                                    <span class="event-status-badge badge-{{ $event->status }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </div>

                                <div class="event-card-body">
                                    {{-- Event Date --}}
                                    <div class="event-meta">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        <div>
                                            <div class="event-meta-label">Event Date</div>
                                            <div class="event-meta-value">
                                                {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('F j, Y') : '—' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Relief Type --}}
                                    <div class="event-meta">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                                            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                                        </svg>
                                        <div>
                                            <div class="event-meta-label">Relief Type</div>
                                            <div class="event-meta-value">{{ $event->relief_type ?? '—' }}</div>
                                        </div>
                                    </div>

                                    {{-- Target Barangay --}}
                                    <div class="event-meta">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        <div>
                                            <div class="event-meta-label">Target Barangay</div>
                                            <div class="event-meta-value">{{ $barangayText }}</div>
                                        </div>
                                    </div>

                                    {{-- Started At / Scheduled --}}
                                    <div class="event-meta">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/>
                                        </svg>
                                        <div>
                                            @if($event->status === 'ongoing' && $event->started_at)
                                                <div class="event-meta-label">Started</div>
                                                <div class="event-meta-value">{{ \Carbon\Carbon::parse($event->started_at)->format('M j, Y g:i A') }}</div>
                                            @else
                                                <div class="event-meta-label">Status</div>
                                                <div class="event-meta-value">Scheduled</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($event->distribution_location)
                                    <div class="event-card-footer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        <span>{{ $event->distribution_location }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <div class="modal-footer-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Use QRcode scanning during distribution events to track relief delivery in real time. Each scan logs the recipient household, timestamp, and staff member responsible for distribution.
                </div>
                <button class="modal-footer-close" onclick="closeEventsModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- SECTION DIVIDER -->
    <div style="display:block;width:100%;height:6px;background:var(--gray-50);margin-top:-1px;"></div>

    <!-- FEATURES -->
    <section class="features-section" id="features">
        <div class="section-inner">
            <div class="section-eyebrow fade-left">System Capabilities</div>
            <h2 class="section-title fade-left delay-1">Everything MDRRMO needs<br>in one platform</h2>
            <p class="section-sub fade-left delay-2">From household enrollment to real-time QR scanning during relief operations — the system covers the full cycle of disaster preparedness and response.</p>
            <div class="features-grid">
                <div class="feature-card fade-up">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg></div>
                    <div class="feature-title">Household Profiling</div>
                    <div class="feature-desc">Comprehensive household registration capturing family composition, sector flags (PWD, senior, student), address, and socioeconomic data for all barangays in Naic.</div>
                </div>
                <div class="feature-card green fade-up">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 7 23 1 17 1"/><polyline points="1 17 1 23 7 23"/><polyline points="23 17 23 23 17 23"/><polyline points="1 7 1 1 7 1"/><rect x="8" y="8" width="8" height="8" rx="1"/></svg></div>
                    <div class="feature-title">QR Code Generation</div>
                    <div class="feature-desc">Each approved household is automatically assigned a unique serial code and QR sticker — enabling fast, error-free scanning during distribution events.</div>
                </div>
                <div class="feature-card yellow fade-up">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                    <div class="feature-title">Distribution Events</div>
                    <div class="feature-desc">Create and manage ayuda distribution events with start/end tracking, barangay targeting, relief type logging, and real-time status monitoring.</div>
                </div>
                <div class="feature-card red fade-up">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                    <div class="feature-title">Role-Based Access</div>
                    <div class="feature-desc">Separate portals for Admin, Staff, and Auditor roles — each with tailored permissions ensuring data integrity and accountability across all operations.</div>
                </div>
                <div class="feature-card teal fade-up">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                    <div class="feature-title">Live Scan Tracking</div>
                    <div class="feature-desc">Staff scan household QR codes on-site during distribution. The system instantly logs who received relief, when, and by which staff member — in real time.</div>
                </div>
                <div class="feature-card orange fade-up">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg></div>
                    <div class="feature-title">Audit Trail & Reports</div>
                    <div class="feature-desc">Full audit logging of all system actions, exportable distribution reports in Excel and PDF, and read-only auditor access for transparency and accountability.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how-section" id="how">
        <div class="section-inner">
            <div class="section-eyebrow fade-left">Process Overview</div>
            <h2 class="section-title fade-left delay-1">How the system works</h2>
            <p class="section-sub fade-left delay-2">A simple four-step cycle from household enrollment to verified relief distribution.</p>
            <div class="steps-row">
                <div class="step fade-up"><div class="step-num num-pop">1</div><div class="step-title">Register Household</div><div class="step-desc">Staff encodes household head info, family members, barangay, and sector flags into the profiling system.</div></div>
                <div class="step fade-up delay-2"><div class="step-num num-pop">2</div><div class="step-title">Admin Approval & QR</div><div class="step-desc">Admin reviews and approves the household record. A unique QR serial code is automatically generated.</div></div>
                <div class="step fade-up delay-3"><div class="step-num num-pop">3</div><div class="step-title">Distribution Event</div><div class="step-desc">Admin creates a distribution event, sets target barangays, and activates it when relief operations begin.</div></div>
                <div class="step fade-up delay-4"><div class="step-num num-pop">4</div><div class="step-title">Scan & Record</div><div class="step-desc">Staff scans each household's QR code on-site. Distribution is logged instantly with timestamp and staff details.</div></div>
            </div>
        </div>
    </section>

    <!-- ROLES -->
    <section class="roles-section" id="roles">
        <div class="section-inner">
            <div class="section-eyebrow fade-left">User Roles</div>
            <h2 class="section-title fade-left delay-1">Three levels of access</h2>
            <p class="section-sub fade-left delay-2">Each account type has a specific role in the system — ensuring the right people have the right tools.</p>
            <div class="roles-grid">
                <div class="role-card scale-in">
                    <div class="role-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                    <div class="role-title">Administrator</div>
                    <div class="role-tag admin">Full Access</div>
                    <div class="role-desc">Manages household approvals, QR code generation, distribution events, user accounts, and has full visibility into all system data and audit logs.</div>
                </div>
                <div class="role-card scale-in delay-2">
                    <div class="role-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/></svg></div>
                    <div class="role-title">Staff / Encoder</div>
                    <div class="role-tag staff">Field Operations</div>
                    <div class="role-desc">Encodes household profiles, operates the QR scanner during active distribution events, and views their personal scan history and statistics.</div>
                </div>
                <div class="role-card scale-in delay-4">
                    <div class="role-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
                    <div class="role-title">Auditor</div>
                    <div class="role-tag auditor">Read Only</div>
                    <div class="role-desc">Has read-only access to household records, distribution logs, and audit trails. Cannot modify any data — designed for oversight and compliance review.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <svg class="cta-bg" viewBox="0 0 1440 280" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <polygon points="0,0 220,0 0,140" fill="#122D5A"/>
            <polygon points="0,0 140,0 0,90" fill="#1B3F7A"/>
            <polygon points="220,0 300,0 0,180 0,140" fill="#0E2348"/>
            <polygon points="300,0 340,0 0,220 0,180" fill="#EAF0FA"/>
            <polygon points="1440,280 1220,280 1440,140" fill="#122D5A"/>
            <polygon points="1440,280 1300,280 1440,190" fill="#1B3F7A"/>
            <polygon points="1220,280 1140,280 1440,100 1440,140" fill="#0E2348"/>
            <polygon points="1140,280 1100,280 1440,60 1440,100" fill="#EAF0FA"/>
        </svg>
        <div class="cta-inner">
            <div class="cta-eyebrow fade-up">Get Started</div>
            <h2 class="cta-title fade-up delay-1">Ready to access the <span>system?</span></h2>
            <p class="cta-sub fade-up delay-2">Login with your authorized MDRRMO account to get started with household profiling and relief distribution.</p>
            <a href="{{ route('login') }}" class="btn-cta fade-up delay-3">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                </svg>
                Login to System
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-main">
            <div class="footer-brand">
                <div class="footer-brand-top">
                    <img src="{{ asset('images/mdrrmo-logo.png') }}" alt="MDRRMO">
                    <div class="footer-brand-name">MDRRMO Naic<br>Cavite</div>
                </div>
                <p class="footer-brand-sub">Office of the Municipal Disaster Risk Reduction and Management Officer — Municipality of Naic, Province of Cavite, Republic of the Philippines.</p>
                <div class="footer-social">
                    <a href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener" title="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="mailto:mdrrmo.naic@gmail.com" title="Email">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <div class="footer-col-title">Contact Us</div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.68A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
                    <span>0976-317-4852<br>0917-812-8187<br>410-5725</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
                    <a href="mailto:mdrrmo.naic@gmail.com"><span>mdrrmo.naic@gmail.com</span></a>
                </div>
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
                    <li><a href="{{ route('login') }}">Login to System</a></li>
                    <li><a href="#features">System Features</a></li>
                    <li><a href="#how">How It Works</a></li>
                    <li><a href="#roles">User Roles</a></li>
                </ul>
                <div class="footer-col-title" style="margin-top:20px;">Office Head</div>
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
            const now = new Date();
            const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            const shortM = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            document.getElementById('top-time').textContent = pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
            document.getElementById('top-date').textContent = days[now.getDay()]+', '+pad(now.getDate())+' '+shortM[now.getMonth()]+' '+now.getFullYear();
        }
        updateClock(); setInterval(updateClock, 1000);
        document.getElementById('footer-year').textContent = new Date().getFullYear();

        // ─── Events Modal ───
        const modal = document.getElementById('eventsModal');

        function openEventsModal() {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            // Reset to "all" tab on open
            filterEvents('all');
        }

        function closeEventsModal() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close on overlay click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeEventsModal();
        });

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('open')) closeEventsModal();
        });

        // ─── Filter tabs ───
        function filterEvents(status) {
            // Update tabs
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-' + status).classList.add('active');

            // Filter cards
            const cards = document.querySelectorAll('.event-card');
            cards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Show empty state if no visible cards
            const list = document.getElementById('eventList');
            if (list) {
                const visible = [...cards].filter(c => c.style.display !== 'none');
                const existingEmpty = list.querySelector('.filter-empty');
                if (existingEmpty) existingEmpty.remove();
                if (visible.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'modal-empty filter-empty';
                    empty.innerHTML = `
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <div class="modal-empty-title">No ${status.charAt(0).toUpperCase()+status.slice(1)} Events</div>
                        <div class="modal-empty-sub">There are no ${status} distribution events at this time.</div>`;
                    list.appendChild(empty);
                }
            }
        }

        // ─── Scroll Animations ───
        const animClasses = ['.fade-up','.fade-left','.fade-right','.scale-in','.num-pop'];
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
            });
        }, { threshold: 0.08 });

        animClasses.forEach(sel => {
            document.querySelectorAll(sel).forEach(el => {
                const rect = el.getBoundingClientRect();
                if (rect.top < window.innerHeight) { el.classList.add('visible'); }
                else { observer.observe(el); }
            });
        });
    </script>
</body>
</html>