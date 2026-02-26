<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Inovex Audit powered by AI')</title>
    <meta name="description" content="@yield('meta_description', 'Audit profesional AI pentru website-ul tau. SEO, viteza, GDPR, ANPC, E-E-A-T. Raport PDF in 60 de secunde.')"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
        --ink:#0a0a0a;--ink-2:#1c1c1c;--ink-3:#404040;--ink-4:#737373;--ink-5:#a3a3a3;--ink-6:#d4d4d4;
        --paper:#ffffff;--paper-2:#fafafa;--paper-3:#f5f5f5;
        --rule:#e5e5e5;--rule-2:#d4d4d4;
        --blue:#2D91CE;--blue-bg:rgba(45,145,206,.07);--blue-bd:rgba(45,145,206,.2);--blue-glow:rgba(45,145,206,.28);
        --red:#ef4444;--amber:#f59e0b;--green:#22c55e;
        --font:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
        --mono:'SF Mono','Fira Code',monospace;
        --ease:cubic-bezier(.16,1,.3,1);
    }
    html{scroll-behavior:smooth;}
    body{font-family:var(--font);background:var(--paper);color:var(--ink);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden;}
    ::selection{background:var(--blue);color:white;}
    ::-webkit-scrollbar{width:5px;}::-webkit-scrollbar-track{background:transparent;}::-webkit-scrollbar-thumb{background:var(--rule-2);border-radius:3px;}

    /* ── NAVBAR ── */
    .navbar{position:sticky;top:0;z-index:100;height:56px;background:rgba(255,255,255,.88);backdrop-filter:blur(20px) saturate(180%);-webkit-backdrop-filter:blur(20px) saturate(180%);border-bottom:1px solid var(--rule);display:flex;align-items:center;padding:0 24px;}
    .navbar-inner{width:100%;max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;}

    /* Logo area */
    .nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
    .nav-logo-img{height:28px;width:auto;display:block;}
    .nav-logo-fallback{width:28px;height:28px;background:var(--ink);border-radius:7px;display:flex;align-items:center;justify-content:center;transition:transform .2s var(--ease);flex-shrink:0;}
    .nav-logo:hover .nav-logo-fallback{transform:rotate(-8deg) scale(1.05);}
    .nav-logo-fallback svg{width:13px;height:13px;stroke:white;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
    .nav-logo-name{font-size:13px;font-weight:600;color:var(--ink);letter-spacing:-.3px;line-height:1.1;}
    .nav-logo-sub{font-size:10px;color:var(--ink-5);line-height:1;margin-top:1px;}
    .nav-right{display:flex;align-items:center;gap:6px;}

    /* ── BUTTONS ── */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;font-family:var(--font);font-size:13px;font-weight:500;line-height:1;white-space:nowrap;border:1px solid transparent;border-radius:8px;cursor:pointer;text-decoration:none;transition:background .15s,border-color .15s,color .15s,box-shadow .15s,transform .1s;user-select:none;}
    .btn:active{transform:scale(.975);}
    .btn svg.arrow{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0;transition:transform .2s var(--ease);}
    .btn:hover svg.arrow{transform:translateX(3px);}
    .btn-sm{padding:0 12px;height:32px;font-size:12px;}
    .btn-md{padding:0 16px;height:38px;}
    .btn-lg{padding:0 24px;height:46px;font-size:14px;font-weight:600;}
    .btn-xl{padding:0 32px;height:52px;font-size:15px;font-weight:600;border-radius:10px;}
    .btn-ghost{background:transparent;color:var(--ink-4);}
    .btn-ghost:hover{background:var(--paper-3);color:var(--ink);}
    .btn-outline{background:var(--paper);color:var(--ink-3);border-color:var(--rule);box-shadow:0 1px 2px rgba(0,0,0,.04);}
    .btn-outline:hover{border-color:var(--rule-2);color:var(--ink);}
    .btn-dark{background:var(--ink);color:white;border-color:var(--ink);}
    .btn-dark:hover{background:var(--ink-2);box-shadow:0 4px 14px rgba(0,0,0,.2);}
    .btn-blue{background:var(--blue);color:white;border-color:var(--blue);}
    .btn-blue:hover{filter:brightness(1.08);box-shadow:0 4px 14px var(--blue-glow);}
    .btn-on-dark{background:white;color:var(--ink);border-color:white;font-weight:600;}
    .btn-on-dark:hover{background:#f0f0f0;color:var(--ink);box-shadow:0 4px 14px rgba(0,0,0,.25);}
    .btn-ghost-on-dark{background:transparent;color:rgba(255,255,255,.5);border-color:rgba(255,255,255,.15);}
    .btn-ghost-on-dark:hover{background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);border-color:rgba(255,255,255,.25);}

    /* ── CHIP ── */
    .chip{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;letter-spacing:.3px;padding:3px 10px;border-radius:100px;border:1px solid;}
    .chip-blue{background:var(--blue-bg);border-color:var(--blue-bd);color:var(--blue);}
    .chip-red{background:#fef2f2;border-color:#fecaca;color:#dc2626;}
    .chip-amber{background:#fffbeb;border-color:#fde68a;color:#d97706;}
    .chip-green{background:#f0fdf4;border-color:#bbf7d0;color:#16a34a;}

    /* ── WRAP ── */
    .wrap{max-width:1200px;margin:0 auto;padding:0 24px;}

    /* ── REVEAL ── */
    [data-reveal]{opacity:0;transform:translateY(20px);transition:opacity .65s var(--ease),transform .65s var(--ease);}
    [data-reveal].in{opacity:1;transform:translateY(0);}
    [data-reveal="0.1"]{transition-delay:.1s;}[data-reveal="0.2"]{transition-delay:.2s;}[data-reveal="0.3"]{transition-delay:.3s;}[data-reveal="0.4"]{transition-delay:.4s;}[data-reveal="0.5"]{transition-delay:.5s;}

    /* ── FOOTER ── */
    .footer{margin-top:120px;background:var(--ink);}
    .footer-top{max-width:1200px;margin:0 auto;padding:64px 24px 56px;display:grid;grid-template-columns:1.6fr 1fr 1fr 1fr;gap:48px;}
    .footer-logo{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
    .footer-logo-img{height:24px;width:auto;filter:brightness(0) invert(1);opacity:.7;}
    .footer-logo-mark{width:28px;height:28px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);border-radius:7px;display:flex;align-items:center;justify-content:center;}
    .footer-logo-mark svg{width:13px;height:13px;stroke:white;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
    .footer-logo-name{font-size:13px;font-weight:600;color:rgba(255,255,255,.9);letter-spacing:-.2px;}
    .footer-tagline{font-size:13px;color:rgba(255,255,255,.35);line-height:1.7;max-width:240px;margin-bottom:20px;}
    .footer-contact{display:flex;flex-direction:column;gap:7px;}
    .footer-contact a{font-size:12px;color:rgba(255,255,255,.4);text-decoration:none;transition:color .15s;}
    .footer-contact a:hover{color:rgba(255,255,255,.8);}
    .footer-col-title{font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:rgba(255,255,255,.25);margin-bottom:18px;}
    .footer-col-links{display:flex;flex-direction:column;gap:12px;}
    .footer-col-links a{font-size:13px;color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s;}
    .footer-col-links a:hover{color:rgba(255,255,255,.85);}
    .footer-divider{border:none;border-top:1px solid rgba(255,255,255,.07);}
    .footer-bottom{max-width:1200px;margin:0 auto;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
    .footer-copy{font-size:12px;color:rgba(255,255,255,.22);}
    .footer-legal{display:flex;gap:20px;}
    .footer-legal a{font-size:12px;color:rgba(255,255,255,.22);text-decoration:none;transition:color .15s;}
    .footer-legal a:hover{color:rgba(255,255,255,.6);}

    @media(max-width:900px){.footer-top{grid-template-columns:1fr 1fr;gap:32px;}}
    @media(max-width:600px){.footer-top{grid-template-columns:1fr;}.footer-bottom{flex-direction:column;align-items:flex-start;gap:8px;}}
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="{{ route('home') }}" class="nav-logo">
            {{-- Incearca sa incarce logo-ul din public/images/logo.png, fallback la icon --}}
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Inovex Audit" class="nav-logo-img"/>
            @elseif(file_exists(public_path('images/logo.svg')))
                <img src="{{ asset('images/logo.svg') }}" alt="Inovex Audit" class="nav-logo-img"/>
            @else
                <div class="nav-logo-fallback">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                </div>
            @endif
            <div>
                <div class="nav-logo-name">Inovex Audit</div>
                <div class="nav-logo-sub">powered by AI</div>
            </div>
        </a>
        <div class="nav-right">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">Deconectare</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Conectare
                </a>
                <a href="{{ route('home') }}" class="btn btn-dark btn-sm">
                    Audit nou
                    <svg class="arrow" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            @endauth
        </div>
    </div>
</nav>

<main>@yield('content')</main>

<footer class="footer">
    <div class="footer-top">
        <div>
            <div class="footer-logo">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="Inovex" class="footer-logo-img"/>
                @elseif(file_exists(public_path('images/logo.svg')))
                    <img src="{{ asset('images/logo.svg') }}" alt="Inovex" class="footer-logo-img"/>
                @else
                    <div class="footer-logo-mark">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <span class="footer-logo-name">Inovex Audit</span>
                @endif
            </div>
            <p class="footer-tagline">Audit complet pentru site-ul tau — SEO, viteza, GDPR, ANPC si UX analizate de AI in sub 60 de secunde.</p>
            <div class="footer-contact">
                <a href="mailto:contact@inovex.ro">contact@inovex.ro</a>
                <a href="tel:+40750456096">0750 456 096</a>
                <a href="https://inovex.ro" target="_blank">inovex.ro</a>
            </div>
        </div>
        <div>
            <div class="footer-col-title">Produs</div>
            <div class="footer-col-links">
                <a href="{{ route('home') }}">Audit nou</a>
                <a href="#cum-functioneaza">Cum functioneaza</a>
                <a href="#ce-verificam">Ce verificam</a>
                <a href="#pret">Pret</a>
                <a href="#intrebari">Intrebari frecvente</a>
            </div>
        </div>
        <div>
            <div class="footer-col-title">Inovex</div>
            <div class="footer-col-links">
                <a href="https://inovex.ro" target="_blank">inovex.ro</a>
                <a href="https://inovex.ro/contact" target="_blank">Contact</a>
                <a href="https://inovex.ro/servicii" target="_blank">Servicii web</a>
                <a href="https://inovex.ro/portofoliu" target="_blank">Portofoliu</a>
            </div>
        </div>
        <div>
            <div class="footer-col-title">Legal</div>
            <div class="footer-col-links">
                <a href="#">Termeni si conditii</a>
                <a href="#">Politica de confidentialitate</a>
                <a href="#">Politica cookies</a>
                <a href="https://anpc.ro/ce-este-sal/" target="_blank">SAL (ANPC)</a>
                <a href="https://ec.europa.eu/consumers/odr" target="_blank">SOL (EC)</a>
            </div>
        </div>
    </div>
    <hr class="footer-divider"/>
    <div class="footer-bottom">
        <span class="footer-copy">© {{ date('Y') }} VOID SFT GAMES SRL &nbsp;&bull;&nbsp; CUI 43474393 &nbsp;&bull;&nbsp; Toate drepturile rezervate</span>
        <div class="footer-legal">
            <a href="https://anpc.ro/ce-este-sal/" target="_blank">SAL - ANPC</a>
            <a href="https://ec.europa.eu/consumers/odr" target="_blank">SOL - EC</a>
        </div>
    </div>
</footer>

<script>
(function(){
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);} });
    }, {threshold:.06});
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));
    });
})();
</script>
@stack('scripts')
</body>
</html>