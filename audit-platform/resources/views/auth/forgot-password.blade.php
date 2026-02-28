<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Recuperare parola — NOVIN.RO</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --ink: #0a0a0a; --ink-2: #1c1c1c; --ink-3: #404040; --ink-4: #737373; --ink-5: #a3a3a3; --ink-6: #d4d4d4;
    --paper: #ffffff; --paper-2: #fafafa; --rule: #e5e5e5;
    --blue: #2D91CE; --blue-glow: rgba(45,145,206,.18); --red: #ef4444;
    --font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --ease: cubic-bezier(.16,1,.3,1);
}
html { height: 100%; }
body { font-family: var(--font); background: var(--paper); color: var(--ink); line-height: 1.6; -webkit-font-smoothing: antialiased; min-height: 100vh; display: flex; }
::selection { background: var(--blue); color: white; }
.wrap { display: flex; width: 100%; min-height: 100vh; }
.left {
    width: 46%; flex-shrink: 0; background: var(--ink);
    position: relative; display: flex; flex-direction: column; justify-content: space-between;
    padding: 48px; overflow: hidden;
}
.left::after {
    content: ''; position: absolute; width: 420px; height: 420px; border-radius: 50%;
    background: radial-gradient(circle, rgba(45,145,206,.15) 0%, transparent 70%);
    bottom: -120px; right: -120px; pointer-events: none;
}
.left > * { position: relative; z-index: 1; }
.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.logo-mark { width: 34px; height: 34px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.1); border-radius: 9px; display: flex; align-items: center; justify-content: center; }
.logo-mark svg { width: 15px; height: 15px; stroke: white; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.logo-name { font-size: 14px; font-weight: 700; color: white; letter-spacing: -.3px; }
.logo-sub { font-size: 10px; color: rgba(255,255,255,.28); margin-top: 1px; }
.left-body { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 40px 0; }
.tagline { font-size: 36px; font-weight: 800; color: white; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 18px; }
.tagline em { color: rgba(255,255,255,.22); font-style: normal; }
.tagline-desc { font-size: 14px; color: rgba(255,255,255,.36); line-height: 1.75; max-width: 300px; margin-bottom: 40px; }
.tip-box { background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08); border-radius: 12px; padding: 20px; max-width: 300px; }
.tip-title { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; }
.tip-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: rgba(255,255,255,.35); margin-bottom: 8px; line-height: 1.5; }
.tip-item:last-child { margin-bottom: 0; }
.tip-item::before { content: '→'; color: var(--blue); flex-shrink: 0; margin-top: 1px; }
.left-foot { font-size: 11px; color: rgba(255,255,255,.16); }
.right { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px; }
.box { width: 100%; max-width: 380px; animation: fadeUp .45s var(--ease) both; }
.back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--ink-5); text-decoration: none; margin-bottom: 28px; transition: color .15s; }
.back-link:hover { color: var(--ink-3); }
.back-link svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.icon-wrap { width: 48px; height: 48px; background: var(--paper-2); border: 1px solid var(--rule); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
.icon-wrap svg { width: 22px; height: 22px; stroke: var(--ink-3); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.box-title { font-size: 24px; font-weight: 800; color: var(--ink); letter-spacing: -.8px; margin-bottom: 5px; }
.box-sub { font-size: 14px; color: var(--ink-4); margin-bottom: 28px; line-height: 1.6; }
.box-sub a { color: var(--blue); text-decoration: none; font-weight: 500; }
.alert { display: flex; align-items: flex-start; gap: 9px; padding: 11px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
.alert.ok { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
.alert.err { background: #fef2f2; border: 1px solid #fecaca; color: var(--red); }
.alert svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; flex-shrink: 0; margin-top: 1px; }
.field { margin-bottom: 20px; }
.lbl { display: block; font-size: 11px; font-weight: 600; color: var(--ink-4); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px; }
.inp { width: 100%; height: 44px; padding: 0 14px; font-family: var(--font); font-size: 14px; color: var(--ink); background: var(--paper-2); border: 1.5px solid var(--rule); border-radius: 10px; outline: none; transition: border-color .15s, box-shadow .15s, background .15s; }
.inp:focus { border-color: var(--blue); background: var(--paper); box-shadow: 0 0 0 4px var(--blue-glow); }
.inp::placeholder { color: var(--ink-6); }
.inp.has-err { border-color: var(--red); }
.ferr { font-size: 11px; color: var(--red); margin-top: 4px; }
.submit { width: 100%; height: 46px; background: var(--ink); color: white; font-family: var(--font); font-size: 14px; font-weight: 600; border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background .15s, transform .15s, box-shadow .15s; }
.submit:hover { background: var(--ink-2); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.18); }
.submit:active { transform: translateY(0); box-shadow: none; }
.submit svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.foot { text-align: center; margin-top: 20px; font-size: 13px; color: var(--ink-5); }
.foot a { color: var(--ink-3); text-decoration: none; font-weight: 500; }
.foot a:hover { color: var(--ink); }
@keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
.mobile-logo { display: none; }
@media (max-width: 900px) {
    .left { display: none; }
    .right { padding: 40px 24px 32px; flex-direction: column; align-items: center; justify-content: flex-start; }
    .mobile-logo { display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 6px; margin-bottom: 36px; text-align: center; width: 100%; }
    .mobile-logo-name { font-size: 18px; font-weight: 800; color: var(--ink); letter-spacing: -.5px; margin-top: 4px; }
    .mobile-logo-sub { font-size: 11px; color: var(--ink-5); }
}
@media (max-width: 480px) { .right { padding: 36px 20px 28px; } }
</style>
</head>
<body>
<div class="wrap">
    <!-- LEFT -->
    <div class="left">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-mark">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="NOVIN.RO" style="height:36px;width:auto;filter:brightness(0) invert(1);opacity:.9;">
                @else
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                @endif
            </div>
            <div>
                <div class="logo-name">NOVIN.RO</div>
                <div class="logo-sub">powered by Inovex.ro</div>
            </div>
        </a>
        <div class="left-body">
            <div class="tagline">Recupereaza<br/><em>accesul</em><br/>la contul tau.</div>
            <div class="tagline-desc">Iti trimitem un link de resetare pe email. Procesul dureaza mai putin de 2 minute.</div>
            <div class="tip-box">
                <div class="tip-title">Ce urmeaza</div>
                <div class="tip-item">Primesti un email cu link de resetare</div>
                <div class="tip-item">Link-ul este valabil 60 de minute</div>
                <div class="tip-item">Alegi o noua parola si te conectezi</div>
            </div>
        </div>
        <div class="left-foot">© {{ date('Y') }} NOVIN.RO — Toate drepturile rezervate</div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="mobile-logo">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="NOVIN.RO" style="height:40px;">
            @endif
            <div class="mobile-logo-name">NOVIN.RO</div>
            <div class="mobile-logo-sub">powered by Inovex.ro</div>
        </div>

        <div class="box">
            <a href="{{ route('login') }}" class="back-link">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Inapoi la conectare
            </a>

            <div class="icon-wrap">
                <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>

            <div class="box-title">Ai uitat parola?</div>
            <div class="box-sub">Introdu adresa de email asociata contului tau si iti trimitem un link de resetare a parolei.</div>

            @if(session('status'))
                <div class="alert ok">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert err">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="field">
                    <label class="lbl" for="email">Adresa de email</label>
                    <input class="inp {{ $errors->has('email') ? 'has-err' : '' }}"
                           type="email" id="email" name="email"
                           value="{{ old('email') }}" placeholder="tu@@exemplu.ro"
                           autocomplete="email" required autofocus/>
                    @error('email')<div class="ferr">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="submit">
                    Trimite link de resetare
                    <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>

            <div class="foot">
                Nu ai cont? <a href="{{ route('register') }}">Creeaza unul gratuit</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>