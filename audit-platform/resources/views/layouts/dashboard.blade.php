<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>@yield('title','Dashboard') — NOVIN.RO</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --ink: #0a0a0a;
    --ink-2: #1c1c1c;
    --ink-3: #404040;
    --ink-4: #737373;
    --ink-5: #a3a3a3;
    --ink-6: #d4d4d4;
    --paper: #ffffff;
    --paper-2: #fafafa;
    --paper-3: #f5f5f5;
    --rule: #e5e5e5;
    --rule-2: #d4d4d4;
    --blue: #2D91CE;
    --blue-bg: rgba(45,145,206,.07);
    --blue-bd: rgba(45,145,206,.2);
    --blue-glow: rgba(45,145,206,.15);
    --red: #ef4444;
    --red-bg: #fef2f2;
    --red-bd: #fecaca;
    --amber: #d97706;
    --amber-bg: #fffbeb;
    --amber-bd: #fde68a;
    --green: #16a34a;
    --green-bg: #f0fdf4;
    --green-bd: #bbf7d0;
    --sb-w: 248px;
    --topbar-h: 54px;
    --font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --ease: cubic-bezier(.16,1,.3,1);
    --shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 16px 48px rgba(0,0,0,.16), 0 4px 14px rgba(0,0,0,.08);
}
html { height: 100%; scroll-behavior: smooth; }
body { font-family: var(--font); background: var(--paper-2); color: var(--ink); line-height: 1.55; -webkit-font-smoothing: antialiased; min-height: 100vh; overflow-x: hidden; }
::selection { background: var(--blue); color: white; }
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-thumb { background: var(--rule-2); border-radius: 3px; }

/* ══ OVERLAY ══ */
.overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 98;
    opacity: 0;
    transition: opacity .25s;
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}

/* ══ SIDEBAR ══ */
.sidebar {
    width: var(--sb-w);
    position: fixed; top: 0; left: 0; height: 100vh;
    background: var(--ink);
    display: flex; flex-direction: column;
    z-index: 99;
    overflow-y: auto; overflow-x: hidden;
    transition: transform .3s var(--ease);
    will-change: transform;
}

.sb-logo {
    padding: 20px 18px 16px;
    border-bottom: 1px solid rgba(255,255,255,.06);
    display: flex; align-items: center; gap: 11px;
    text-decoration: none; flex-shrink: 0;
    transition: background .15s;
}
.sb-logo:hover { background: rgba(255,255,255,.03); }
.sb-logo-icon {
    width: 32px; height: 32px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: transform .2s var(--ease);
}
.sb-logo:hover .sb-logo-icon { transform: rotate(-8deg) scale(1.05); }
.sb-logo-icon svg { width: 15px; height: 15px; stroke: white; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.sb-logo-name { font-size: 13px; font-weight: 700; color: rgba(255,255,255,.9); letter-spacing: -.3px; line-height: 1.1; }
.sb-logo-sub  { font-size: 10px; color: rgba(255,255,255,.28); line-height: 1; margin-top: 2px; }

.sb-nav { padding: 12px 10px; flex: 1; }
.sb-sec { font-size: 10px; font-weight: 600; letter-spacing: .9px; text-transform: uppercase; color: rgba(255,255,255,.2); padding: 10px 10px 5px; }

.sb-link {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 10px; border-radius: 8px; margin-bottom: 1px;
    font-size: 13px; font-weight: 500;
    color: rgba(255,255,255,.45);
    text-decoration: none;
    transition: background .12s, color .12s;
    border: none; background: none; cursor: pointer;
    width: 100%; font-family: var(--font); text-align: left;
}
.sb-link:hover  { background: rgba(255,255,255,.06); color: rgba(255,255,255,.82); }
.sb-link.active { background: rgba(255,255,255,.1);  color: rgba(255,255,255,.95); }
.sb-link svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
.sb-ext { margin-left: auto; width: 10px; height: 10px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; opacity: .3; }

.sb-footer { padding: 10px 10px 16px; border-top: 1px solid rgba(255,255,255,.06); flex-shrink: 0; }
.sb-user { display: flex; align-items: center; gap: 10px; padding: 9px 10px; }
.sb-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--blue);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: white; flex-shrink: 0;
}
.sb-uname  { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.78); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
.sb-uemail { font-size: 11px; color: rgba(255,255,255,.28); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
.sb-logout {
    display: flex; align-items: center; gap: 9px;
    padding: 8px 10px; border-radius: 8px;
    font-size: 12px; color: rgba(255,255,255,.28);
    background: none; border: none; cursor: pointer;
    width: 100%; font-family: var(--font);
    transition: all .12s; margin-top: 2px;
}
.sb-logout:hover { color: rgba(255,255,255,.65); background: rgba(255,255,255,.05); }
.sb-logout svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

/* ══ MAIN ══ */
.main { margin-left: var(--sb-w); display: flex; flex-direction: column; min-height: 100vh; }

/* ══ TOPBAR ══ */
.topbar {
    height: var(--topbar-h);
    background: rgba(255,255,255,.9);
    backdrop-filter: blur(16px) saturate(160%);
    -webkit-backdrop-filter: blur(16px) saturate(160%);
    border-bottom: 1px solid var(--rule);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 26px;
    position: sticky; top: 0; z-index: 40; flex-shrink: 0;
}
.topbar-l { display: flex; align-items: center; gap: 12px; }
.hamburger {
    display: none;
    align-items: center; justify-content: center;
    width: 36px; height: 36px;
    border-radius: 9px; border: 1px solid var(--rule);
    background: var(--paper); cursor: pointer; color: var(--ink-4);
    transition: all .15s; flex-shrink: 0;
}
.hamburger:hover { border-color: var(--rule-2); color: var(--ink); }
.hamburger svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; }
.topbar-title { font-size: 14px; font-weight: 700; color: var(--ink); letter-spacing: -.3px; }
.topbar-r { display: flex; align-items: center; gap: 8px; }

/* ══ PAGE ══ */
.page { padding: 28px; flex: 1; }

/* ══ BUTTONS ══ */
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-family: var(--font); font-weight: 500; line-height: 1; white-space: nowrap; border: 1px solid transparent; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all .15s; -webkit-appearance: none; }
.btn:active { transform: scale(.975); }
.btn svg { stroke: currentColor; fill: none; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
.btn-sm  { padding: 0 12px; height: 32px; font-size: 12px; }
.btn-sm svg  { width: 13px; height: 13px; stroke-width: 2; }
.btn-md  { padding: 0 16px; height: 38px; font-size: 13px; }
.btn-md svg  { width: 14px; height: 14px; stroke-width: 2; }
.btn-lg  { padding: 0 20px; height: 44px; font-size: 14px; font-weight: 600; }
.btn-lg svg  { width: 15px; height: 15px; stroke-width: 2; transition: transform .2s var(--ease); }
.btn-lg:hover svg { transform: translateX(3px); }
.btn-dark    { background: var(--ink); color: white; border-color: var(--ink); }
.btn-dark:hover { background: var(--ink-2); box-shadow: var(--shadow-md); }
.btn-outline { background: var(--paper); color: var(--ink-3); border-color: var(--rule); box-shadow: var(--shadow-sm); }
.btn-outline:hover { border-color: var(--rule-2); color: var(--ink); }

/* ══ CARD ══ */
.card { background: var(--paper); border: 1px solid var(--rule); border-radius: 14px; box-shadow: var(--shadow-sm); }
.card-body   { padding: 22px 24px; }
.card-header { padding: 18px 22px; border-bottom: 1px solid var(--rule); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.card-title  { font-size: 14px; font-weight: 700; color: var(--ink); letter-spacing: -.3px; }
.card-desc   { font-size: 12px; color: var(--ink-5); margin-top: 2px; }

/* ══ STATS ══ */
.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 22px; }
.stat { padding: 20px 22px; }
.stat-label { font-size: 11px; font-weight: 600; color: var(--ink-5); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; }
.stat-val   { font-size: 30px; font-weight: 800; color: var(--ink); line-height: 1; letter-spacing: -1.5px; }
.stat-unit  { font-size: 14px; font-weight: 400; color: var(--ink-5); letter-spacing: 0; }
.stat-sub   { font-size: 11px; color: var(--ink-5); margin-top: 5px; }
.stat-val.red   { color: var(--red); }
.stat-val.green { color: var(--green); }

/* ══ TABLE ══ */
.tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.tbl { width: 100%; border-collapse: collapse; min-width: 560px; }
.tbl th { font-size: 11px; font-weight: 600; color: var(--ink-5); text-transform: uppercase; letter-spacing: .5px; padding: 10px 18px; text-align: left; border-bottom: 1px solid var(--rule); white-space: nowrap; }
.tbl td { padding: 13px 18px; font-size: 13px; color: var(--ink-3); border-bottom: 1px solid var(--paper-3); vertical-align: middle; }
.tbl tr:last-child td { border-bottom: none; }
.tbl tbody tr { transition: background .1s; }
.tbl tbody tr:hover td { background: var(--paper-2); }
.tbl-url   { font-weight: 600; color: var(--ink); font-size: 13px; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tbl-email { font-size: 11px; color: var(--ink-5); margin-top: 2px; }

/* ══ SCORE / BADGE / CHIP ══ */
.score { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 22px; border-radius: 6px; font-size: 12px; font-weight: 800; }
.score.g { background: var(--green-bg); color: var(--green); }
.score.a { background: var(--amber-bg); color: var(--amber); }
.score.r { background: var(--red-bg);   color: var(--red); }

.badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; white-space: nowrap; border: 1px solid; }
.badge.g { background: var(--green-bg); border-color: var(--green-bd); color: var(--green); }
.badge.a { background: var(--amber-bg); border-color: var(--amber-bd); color: var(--amber); }
.badge.r { background: var(--red-bg);   border-color: var(--red-bd);   color: var(--red); }

.chip { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 2px 7px; border-radius: 100px; border: 1px solid; }
.chip.r { background: var(--red-bg);   border-color: var(--red-bd);   color: var(--red); }
.chip.a { background: var(--amber-bg); border-color: var(--amber-bd); color: var(--amber); }
.chip.g { background: var(--green-bg); border-color: var(--green-bd); color: var(--green); }
.chip.b { background: var(--blue-bg);  border-color: var(--blue-bd);  color: var(--blue); }
.chips  { display: flex; flex-wrap: wrap; gap: 4px; }

.tbl-action { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; color: var(--blue); text-decoration: none; padding: 5px 10px; border-radius: 7px; border: 1px solid var(--blue-bd); background: var(--blue-bg); transition: all .15s; white-space: nowrap; }
.tbl-action:hover { background: var(--blue); color: white; border-color: var(--blue); }
.tbl-action svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

/* ══ EMPTY STATE ══ */
.empty { text-align: center; padding: 56px 24px; }
.empty-icon { width: 48px; height: 48px; background: var(--paper-3); border: 1px solid var(--rule); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
.empty-icon svg { width: 22px; height: 22px; stroke: var(--ink-5); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.empty-title { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
.empty-desc  { font-size: 13px; color: var(--ink-5); margin-bottom: 20px; max-width: 280px; margin-left: auto; margin-right: auto; }

/* ══ ALERTS ══ */
.alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; display: flex; align-items: flex-start; gap: 9px; border: 1px solid; }
.alert svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; margin-top: 1px; }
.alert.success { background: var(--green-bg); border-color: var(--green-bd); color: var(--green); }
.alert.error   { background: var(--red-bg);   border-color: var(--red-bd);   color: var(--red); }

/* ══ FORMS ══ */
.field { margin-bottom: 14px; }
.field-label { display: block; font-size: 11px; font-weight: 600; color: var(--ink-4); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px; }
.field-input { width: 100%; height: 40px; padding: 0 12px; font-family: var(--font); font-size: 13px; color: var(--ink); background: var(--paper-2); border: 1.5px solid var(--rule); border-radius: 9px; outline: none; -webkit-appearance: none; transition: border-color .15s, box-shadow .15s, background .15s; }
.field-input:focus { border-color: var(--blue); background: var(--paper); box-shadow: 0 0 0 3px var(--blue-glow); }
.field-hint  { font-size: 11px; color: var(--ink-5); margin-top: 4px; }
.field-error { font-size: 11px; color: var(--red); margin-top: 4px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-sec { padding-bottom: 24px; margin-bottom: 24px; border-bottom: 1px solid var(--rule); }
.form-sec:last-child { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
.form-sec-title { font-size: 14px; font-weight: 700; color: var(--ink); letter-spacing: -.3px; margin-bottom: 3px; }
.form-sec-desc  { font-size: 12px; color: var(--ink-5); margin-bottom: 16px; }

/* Info rows */
.info-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding: 10px 0; border-bottom: 1px solid var(--paper-3); }
.info-row:last-child { border-bottom: none; }
.info-lbl { color: var(--ink-4); }
.info-val { font-weight: 600; color: var(--ink); }

/* ══ RESPONSIVE ══ */
@media (max-width: 1200px) {
    .stats-grid { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 768px) {
    .overlay { display: block; pointer-events: none; }
    .sidebar { transform: translateX(-100%); }
    .main { margin-left: 0; }
    .hamburger { display: flex; }
    .topbar { padding: 0 16px; }
    .page { padding: 16px; }
    .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
    .stat-val { font-size: 24px; }
    .form-grid { grid-template-columns: 1fr; }
    .topbar-r .btn-label { display: none; }
}
@media (max-width: 420px) {
    .stat { padding: 14px 16px; }
    .stat-val { font-size: 20px; }
}

/* ══ OPEN STATE ══ */
body.sb-open .overlay { opacity: 1; pointer-events: all; }
body.sb-open .sidebar { transform: translateX(0); box-shadow: var(--shadow-lg); }
</style>
@stack('styles')
</head>
<body>

<div class="overlay" id="overlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sb-logo">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" alt="NOVIN.RO" style="height:32px;width:auto;display:block;filter:brightness(0) invert(1);opacity:.9;">
        @elseif(file_exists(public_path('images/logo.svg')))
            <img src="{{ asset('images/logo.svg') }}" alt="NOVIN.RO" style="height:32px;width:auto;display:block;filter:brightness(0) invert(1);opacity:.9;">
        @endif
        <div>
            <div class="sb-logo-name">NOVIN.RO</div>
            <div class="sb-logo-sub">powered by Inovex.ro</div>
        </div>
        @if(false)
            <div class="sb-logo-icon">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
            </div>
            <div>
                <div class="sb-logo-name">NOVIN.RO</div>
                <div class="sb-logo-sub">powered by Inovex.ro</div>
            </div>
        @endif
    </a>

    <nav class="sb-nav">
        <div class="sb-sec">Principal</div>
        <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
            Dashboard
        </a>
        <a href="{{ route('home') }}" class="sb-link" target="_blank">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Audit nou
            <svg class="sb-ext" viewBox="0 0 24 24"><path d="M7 17L17 7M17 7H7M17 7v10"/></svg>
        </a>
        <div class="sb-sec" style="margin-top:6px">Cont</div>
        <a href="{{ route('dashboard.settings') }}" class="sb-link {{ request()->routeIs('dashboard.settings') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Setari cont
        </a>

        @if(Auth::user()->is_admin)
        <div class="sb-sec" style="margin-top:6px">Administrare</div>
        <a href="{{ route('admin.index') }}" class="sb-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
           style="{{ request()->routeIs('admin.*') ? '' : 'color:rgba(255,180,0,.75)' }}">
            <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Admin Panel
            @if(!request()->routeIs('admin.*'))
            <span style="margin-left:auto;font-size:9px;font-weight:700;background:rgba(255,180,0,.15);color:rgba(255,180,0,.9);border:1px solid rgba(255,180,0,.2);padding:1px 5px;border-radius:3px;">ADMIN</span>
            @endif
        </a>
        @endif
    </nav>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div style="min-width:0;flex:1">
                <div class="sb-uname">{{ Auth::user()->name }}</div>
                <div class="sb-uemail">{{ Auth::user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout">
                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Deconectare
            </button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <header class="topbar">
        <div class="topbar-l">
            <button class="hamburger" id="menuBtn" aria-label="Meniu" aria-expanded="false">
                <svg id="menuIco" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="topbar-title">@yield('page_title','Dashboard')</span>
        </div>
        <div class="topbar-r">
            <a href="{{ route('home') }}" class="btn btn-dark btn-sm">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span class="btn-label">Audit nou</span>
            </a>
        </div>
    </header>

    <div class="page">
        @if(session('success'))
            <div class="alert success">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
        @endif
        @yield('content')
    </div>
</div>

<script>
(function() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    var menuBtn = document.getElementById('menuBtn');
    var menuIco = document.getElementById('menuIco');

    var ICO_OPEN  = '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>';
    var ICO_CLOSE = '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>';

    function open() {
        document.body.classList.add('sb-open');
        menuBtn.setAttribute('aria-expanded', 'true');
        menuIco.innerHTML = ICO_CLOSE;
        document.body.style.overflow = 'hidden';
    }
    function close() {
        document.body.classList.remove('sb-open');
        menuBtn.setAttribute('aria-expanded', 'false');
        menuIco.innerHTML = ICO_OPEN;
        document.body.style.overflow = '';
    }

    menuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        document.body.classList.contains('sb-open') ? close() : open();
    });

    overlay.addEventListener('click', close);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') close();
    });

    // Inchide la click pe link in sidebar (mobile)
    sidebar.querySelectorAll('a').forEach(function(a) {
        a.addEventListener('click', function() {
            if (window.innerWidth <= 768) close();
        });
    });
})();
</script>
@stack('scripts')
</body>
</html>