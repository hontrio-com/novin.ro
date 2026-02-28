<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>@yield('title','Admin') — NOVIN.RO</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
    --ink:#0a0a0a;--ink-2:#1c1c1c;--ink-3:#404040;--ink-4:#737373;--ink-5:#a3a3a3;--ink-6:#d4d4d4;
    --paper:#ffffff;--paper-2:#fafafa;--paper-3:#f5f5f5;--rule:#e5e5e5;--rule-2:#d4d4d4;
    --blue:#2D91CE;--blue-bg:rgba(45,145,206,.07);--blue-bd:rgba(45,145,206,.2);
    --red:#ef4444;--red-bg:#fef2f2;--red-bd:#fecaca;
    --amber:#d97706;--amber-bg:#fffbeb;--amber-bd:#fde68a;
    --green:#16a34a;--green-bg:#f0fdf4;--green-bd:#bbf7d0;
    --sb-w:220px;--topbar-h:52px;
    --font:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
}
html{height:100%;}
body{font-family:var(--font);background:var(--paper-2);color:var(--ink);-webkit-font-smoothing:antialiased;min-height:100vh;display:flex;overflow-x:hidden;}

/* ── SIDEBAR ── */
.sb{width:var(--sb-w);background:var(--ink);position:fixed;top:0;left:0;height:100vh;display:flex;flex-direction:column;z-index:50;overflow-y:auto;}
.sb-logo{padding:18px 16px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:10px;text-decoration:none;}
.sb-logo-badge{width:28px;height:28px;background:var(--red);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:white;flex-shrink:0;}
.sb-logo-text{font-size:13px;font-weight:700;color:white;letter-spacing:-.2px;}
.sb-logo-sub{font-size:10px;color:rgba(255,255,255,.35);font-weight:500;}

.sb-section{padding:12px 10px 4px;font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:rgba(255,255,255,.25);}
.sb-nav{padding:0 8px;}
.sb-link{display:flex;align-items:center;gap:9px;padding:8px 10px;border-radius:8px;text-decoration:none;color:rgba(255,255,255,.55);font-size:13px;font-weight:500;transition:all .15s;margin-bottom:2px;}
.sb-link:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.9);}
.sb-link.active{background:rgba(255,255,255,.1);color:white;font-weight:600;}
.sb-link svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0;}

.sb-footer{margin-top:auto;padding:12px 8px;border-top:1px solid rgba(255,255,255,.06);}
.sb-user{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;}
.sb-user-av{width:28px;height:28px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:white;flex-shrink:0;}
.sb-user-name{font-size:12px;font-weight:600;color:white;letter-spacing:-.2px;}
.sb-user-role{font-size:10px;color:rgba(255,255,255,.35);}

/* ── MAIN ── */
.main{margin-left:var(--sb-w);flex:1;display:flex;flex-direction:column;min-height:100vh;min-width:0;overflow-x:hidden;}
.topbar{height:var(--topbar-h);background:var(--paper);border-bottom:1px solid var(--rule);display:flex;align-items:center;padding:0 24px;position:sticky;top:0;z-index:40;gap:12px;}
.topbar-title{font-size:14px;font-weight:700;color:var(--ink);letter-spacing:-.2px;}
.topbar-breadcrumb{font-size:12px;color:var(--ink-5);margin-left:auto;display:flex;align-items:center;gap:6px;}
.topbar-breadcrumb a{color:var(--ink-5);text-decoration:none;}
.topbar-breadcrumb a:hover{color:var(--ink);}
.topbar-sep{color:var(--ink-6);}
.content{padding:24px;flex:1;}

/* ── HAMBURGER ── */
.hamburger{display:none;width:36px;height:36px;background:transparent;border:1px solid var(--rule);border-radius:8px;cursor:pointer;align-items:center;justify-content:center;flex-shrink:0;}
.hamburger svg{width:16px;height:16px;stroke:var(--ink-3);fill:none;stroke-width:2;stroke-linecap:round;}

/* ── OVERLAY ── */
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:49;backdrop-filter:blur(2px);}
.sb-overlay.open{display:block;}

/* ── RESPONSIVE ── */
@media(max-width:768px){
    .sb{transform:translateX(-100%);transition:transform .25s cubic-bezier(.16,1,.3,1);}
    .sb.open{transform:translateX(0);}
    .main{margin-left:0;}
    .hamburger{display:inline-flex;}
    .content{padding:16px;}
    .topbar{padding:0 16px;}
}

/* Flash messages */
.flash{padding:10px 16px;border-radius:8px;font-size:13px;font-weight:500;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.flash.success{background:var(--green-bg);color:var(--green);border:1px solid var(--green-bd);}
.flash.error  {background:var(--red-bg);  color:var(--red);  border:1px solid var(--red-bd);}
.flash svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0;}
</style>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
@stack('styles')
</head>
<body>

<!-- Overlay mobil -->
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside class="sb">
    <a href="{{ route('admin.index') }}" class="sb-logo">
        <div class="sb-logo-badge">A</div>
        <div>
            <div class="sb-logo-text">Admin Panel</div>
            <div class="sb-logo-sub">NOVIN.RO</div>
        </div>
    </a>

    <div style="padding:8px 0;">
        <div class="sb-section">Navigare</div>
        <nav class="sb-nav">
            <a href="{{ route('admin.index') }}" class="sb-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Overview
            </a>
            <a href="{{ route('admin.audits') }}" class="sb-link {{ request()->routeIs('admin.audits*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Audituri
            </a>
            <a href="{{ route('admin.users') }}" class="sb-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Utilizatori
            </a>
        </nav>

        <div class="sb-section" style="margin-top:8px;">Aplicație</div>
        <nav class="sb-nav">
            <a href="{{ route('dashboard') }}" class="sb-link">
                <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard client
            </a>
            <a href="{{ route('home') }}" class="sb-link">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                Audit nou
            </a>
        </nav>
    </div>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-user-av">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="sb-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="sb-user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<!-- Main -->
<div class="main">
    <header class="topbar">
        <button class="hamburger" onclick="openSidebar()" aria-label="Meniu">
            <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="topbar-title">@yield('page_title','Admin')</div>
        <div class="topbar-breadcrumb">
            <a href="{{ route('admin.index') }}">Admin</a>
            @hasSection('breadcrumb')
            <span class="topbar-sep">/</span>
            @yield('breadcrumb')
            @endif
        </div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="flash success">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
function openSidebar(){
    document.querySelector('.sb').classList.add('open');
    document.getElementById('sbOverlay').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeSidebar(){
    document.querySelector('.sb').classList.remove('open');
    document.getElementById('sbOverlay').classList.remove('open');
    document.body.style.overflow='';
}
// Inchide la resize desktop
window.addEventListener('resize',function(){if(window.innerWidth>768)closeSidebar();});
</script>
</body>
</html>