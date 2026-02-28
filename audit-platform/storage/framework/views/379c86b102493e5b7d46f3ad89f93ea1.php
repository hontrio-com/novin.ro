<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Creeaza cont — NOVIN.RO</title>
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
    --rule: #e5e5e5;
    --blue: #2D91CE;
    --blue-glow: rgba(45,145,206,.18);
    --red: #ef4444;
    --font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --ease: cubic-bezier(.16,1,.3,1);
}
html { height: 100%; }
body { font-family: var(--font); background: var(--paper); color: var(--ink); line-height: 1.6; -webkit-font-smoothing: antialiased; min-height: 100vh; display: flex; }
::selection { background: var(--blue); color: white; }

.wrap { display: flex; width: 100%; min-height: 100vh; }

/* LEFT */
.left {
    width: 46%; flex-shrink: 0;
    background: #050505;
    position: relative;
    display: flex; flex-direction: column; justify-content: space-between;
    padding: 48px; overflow: hidden;
}
.left::after {
    content: ''; position: absolute;
    width: 500px; height: 500px; border-radius: 50%;
    background: radial-gradient(circle, rgba(45,145,206,.12) 0%, transparent 70%);
    top: -150px; left: -150px; pointer-events: none;
}
.left > * { position: relative; z-index: 1; }

.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.logo-mark { width: 34px; height: 34px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.1); border-radius: 9px; display: flex; align-items: center; justify-content: center; }
.logo-mark svg { width: 15px; height: 15px; stroke: white; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.logo-name { font-size: 14px; font-weight: 700; color: white; letter-spacing: -.3px; }
.logo-sub { font-size: 10px; color: rgba(255,255,255,.28); margin-top: 1px; }

.left-body { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 40px 0; }
.tagline { font-size: 36px; font-weight: 800; color: white; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 36px; }
.tagline em { color: rgba(255,255,255,.2); font-style: normal; }

/* Steps */
.steps { display: flex; flex-direction: column; gap: 0; }
.step { display: flex; gap: 16px; padding: 16px 0; position: relative; }
.step:not(:last-child)::after { content: ''; position: absolute; left: 14px; top: 44px; width: 1px; height: calc(100% - 10px); background: rgba(255,255,255,.07); }
.step-num { width: 28px; height: 28px; border-radius: 50%; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: rgba(255,255,255,.35); flex-shrink: 0; margin-top: 2px; }
.step-title { font-size: 14px; font-weight: 600; color: rgba(255,255,255,.65); margin-bottom: 3px; }
.step-desc { font-size: 12px; color: rgba(255,255,255,.28); line-height: 1.6; }

.left-foot { font-size: 11px; color: rgba(255,255,255,.16); }

/* RIGHT */
.right { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px; }
.box { width: 100%; max-width: 380px; animation: fadeUp .45s var(--ease) both; }

.box-title { font-size: 24px; font-weight: 800; color: var(--ink); letter-spacing: -.8px; margin-bottom: 5px; }
.box-sub { font-size: 14px; color: var(--ink-4); margin-bottom: 28px; }
.box-sub a { color: var(--blue); text-decoration: none; font-weight: 500; }
.box-sub a:hover { text-decoration: underline; }

/* Alert */
.alert { display: flex; align-items: flex-start; gap: 9px; padding: 11px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
.alert.err { background: #fef2f2; border: 1px solid #fecaca; color: var(--red); }
.alert svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; flex-shrink: 0; margin-top: 1px; }

/* Field */
.field { margin-bottom: 13px; }
.lbl { display: block; font-size: 11px; font-weight: 600; color: var(--ink-4); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px; }
.inp { width: 100%; height: 44px; padding: 0 14px; font-family: var(--font); font-size: 14px; color: var(--ink); background: var(--paper-2); border: 1.5px solid var(--rule); border-radius: 10px; outline: none; -webkit-appearance: none; transition: border-color .15s, box-shadow .15s, background .15s; }
.inp:focus { border-color: var(--blue); background: var(--paper); box-shadow: 0 0 0 4px var(--blue-glow); }
.inp::placeholder { color: var(--ink-6); }
.inp.has-err { border-color: var(--red); }
.ferr { font-size: 11px; color: var(--red); margin-top: 4px; }

/* Password */
.pw-wrap { position: relative; }
.pw-wrap .inp { padding-right: 44px; }
.pw-eye { position: absolute; right: 0; top: 0; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: var(--ink-5); }
.pw-eye:hover { color: var(--ink-3); }
.pw-eye svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

/* Password strength */
.pw-strength { display: flex; align-items: center; gap: 4px; margin-top: 6px; }
.pw-bar { height: 3px; flex: 1; border-radius: 2px; background: var(--rule); transition: background .3s; }
.pw-bar.weak { background: #ef4444; }
.pw-bar.med  { background: #f59e0b; }
.pw-bar.str  { background: #22c55e; }
.pw-lbl { font-size: 10px; color: var(--ink-5); margin-left: 4px; min-width: 44px; }

/* Terms */
.terms { display: flex; align-items: flex-start; gap: 9px; font-size: 13px; color: var(--ink-4); line-height: 1.5; margin-bottom: 18px; }
.terms input { width: 15px; height: 15px; accent-color: var(--blue); cursor: pointer; flex-shrink: 0; margin-top: 2px; }
.terms a { color: var(--blue); text-decoration: none; }
.terms a:hover { text-decoration: underline; }

/* Submit */
.submit { width: 100%; height: 46px; background: var(--ink); color: white; font-family: var(--font); font-size: 14px; font-weight: 600; letter-spacing: -.2px; border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background .15s, transform .15s, box-shadow .15s; }
.submit:hover { background: var(--ink-2); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.18); }
.submit:active { transform: translateY(0); box-shadow: none; }
.submit svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; transition: transform .2s var(--ease); }
.submit:hover svg { transform: translateX(3px); }

.foot { text-align: center; margin-top: 20px; font-size: 13px; color: var(--ink-5); }
.foot a { color: var(--ink-3); text-decoration: none; font-weight: 500; }
.foot a:hover { color: var(--ink); }
.btn-google { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; height: 44px; background: var(--paper); border: 1.5px solid var(--rule); border-radius: 10px; font-size: 14px; font-weight: 500; color: var(--ink-3); text-decoration: none; transition: border-color .15s, background .15s, box-shadow .15s; margin-bottom: 16px; }
.btn-google:hover { border-color: var(--ink-6); background: var(--paper-2); box-shadow: 0 2px 8px rgba(0,0,0,.06); color: var(--ink); }
.divider { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--rule); }
.divider span { font-size: 11px; color: var(--ink-5); font-weight: 500; white-space: nowrap; }

@keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

.mobile-logo { display: none; }
@media (max-width: 900px) {
    .left { display: none; }
    .right { padding: 40px 24px 32px; flex-direction: column; align-items: center; justify-content: flex-start; }
    .mobile-logo {
        display: flex; align-items: center; justify-content: center;
        flex-direction: column; gap: 6px;
        margin-bottom: 36px; text-align: center; width: 100%;
    }
    .mobile-logo img { height: 48px; width: auto; display: block; }
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
        <div class="logo">
            <div class="logo-mark" style="background:none;border:none;padding:0;width:auto;">
                <?php if(file_exists(public_path('images/logo.png'))): ?>
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="NOVIN.RO" style="height:36px;width:auto;display:block;filter:brightness(0) invert(1);opacity:.9;">
                <?php elseif(file_exists(public_path('images/logo.svg'))): ?>
                    <img src="<?php echo e(asset('images/logo.svg')); ?>" alt="NOVIN.RO" style="height:36px;width:auto;display:block;filter:brightness(0) invert(1);opacity:.9;">
                <?php else: ?>
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:white;fill:none;stroke-width:2;"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                <?php endif; ?>
            </div>
            <div>
                <div class="logo-name">NOVIN.RO</div>
                <div class="logo-sub">powered by Inovex.ro</div>
            </div>
        </div>

        <div class="left-body">
            <div class="tagline">Primul pas<br/><em>spre un site</em><br/>mai bun.</div>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div>
                        <div class="step-title">Creeaza contul</div>
                        <div class="step-desc">Inregistrare rapida cu email si parola. Fara card bancar necesar.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div>
                        <div class="step-title">Porneste auditul</div>
                        <div class="step-desc">Introduce URL-ul site-ului tau si AI-ul analizeaza totul in sub 60 de secunde.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div>
                        <div class="step-title">Primesti raportul</div>
                        <div class="step-desc">Raport PDF detaliat cu probleme identificate, solutii clare si plan de actiune prioritizat.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="left-foot">© <?php echo e(date('Y')); ?> NOVIN.RO — Toate drepturile rezervate</div>
    </div>

    <!-- RIGHT -->
    <div class="right">
            <div class="mobile-logo">
                <?php if(file_exists(public_path('images/logo.png'))): ?>
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="NOVIN.RO">
                <?php elseif(file_exists(public_path('images/logo.svg'))): ?>
                    <img src="<?php echo e(asset('images/logo.svg')); ?>" alt="NOVIN.RO">
                <?php endif; ?>
                <div class="mobile-logo-name">NOVIN.RO</div>
                <div class="mobile-logo-sub">powered by Inovex.ro</div>
            </div>
        <div class="box">
            <div class="box-title">Creeaza cont</div>
            <div class="box-sub">Ai deja cont? <a href="<?php echo e(route('login')); ?>">Conecteaza-te</a></div>

            <?php if($errors->any()): ?>
                <div class="alert err">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?php echo e($errors->first()); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('register')); ?>">
                <?php echo csrf_field(); ?>

                <a href="<?php echo e(route('auth.google')); ?>" class="btn-google">
                    <svg viewBox="0 0 24 24" width="18" height="18"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Continua cu Google
                </a>
                <div class="divider"><span>sau</span></div>

                <div class="field">
                    <label class="lbl" for="name">Numele tau</label>
                    <input class="inp <?php echo e($errors->has('name') ? 'has-err' : ''); ?>"
                           type="text" id="name" name="name"
                           value="<?php echo e(old('name')); ?>" placeholder="Ion Popescu"
                           autocomplete="name" required autofocus/>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ferr"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="field">
                    <label class="lbl" for="email">Adresa de email</label>
                    <input class="inp <?php echo e($errors->has('email') ? 'has-err' : ''); ?>"
                           type="email" id="email" name="email"
                           value="<?php echo e(old('email')); ?>" placeholder="tu@exemplu.ro"
                           autocomplete="email" required/>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ferr"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="field">
                    <label class="lbl" for="password">Parola</label>
                    <div class="pw-wrap">
                        <input class="inp <?php echo e($errors->has('password') ? 'has-err' : ''); ?>"
                               type="password" id="password" name="password"
                               placeholder="Minim 8 caractere"
                               autocomplete="new-password" required
                               oninput="checkStr(this.value)"/>
                        <button type="button" class="pw-eye" onclick="togglePw('password')" tabindex="-1">
                            <svg id="eye-password" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-bar" id="b1"></div>
                        <div class="pw-bar" id="b2"></div>
                        <div class="pw-bar" id="b3"></div>
                        <div class="pw-bar" id="b4"></div>
                        <span class="pw-lbl" id="plbl"></span>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="ferr"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="field">
                    <label class="lbl" for="password_confirmation">Confirma parola</label>
                    <div class="pw-wrap">
                        <input class="inp"
                               type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Repeta parola" autocomplete="new-password" required/>
                        <button type="button" class="pw-eye" onclick="togglePw('password_confirmation')" tabindex="-1">
                            <svg id="eye-password_confirmation" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required/>
                    <label for="terms" style="cursor:pointer;">Sunt de acord cu <a href="/termeni-si-conditii" target="_blank">Termenii si Conditiile</a> si <a href="/politica-de-confidentialitate" target="_blank">Politica de Confidentialitate</a></label>
                </div>

                <button type="submit" class="submit">
                    Creeaza contul
                    <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>

            <div class="foot">
                <a href="<?php echo e(route('home')); ?>">← Inapoi la pagina principala</a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePw(id) {
    var el = document.getElementById(id);
    var ic = document.getElementById('eye-' + id);
    if (el.type === 'password') {
        el.type = 'text';
        ic.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        el.type = 'password';
        ic.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}
function checkStr(v) {
    var bars = ['b1','b2','b3','b4'].map(function(i){ return document.getElementById(i); });
    var lbl  = document.getElementById('plbl');
    var s = 0;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    bars.forEach(function(b){ b.className = 'pw-bar'; });
    if (!v.length) { lbl.textContent = ''; return; }
    var cls = s <= 1 ? 'weak' : s === 2 ? 'med' : 'str';
    var txt = s <= 1 ? 'Slaba' : s === 2 ? 'Medie' : s === 3 ? 'Buna' : 'Puternica';
    var col = s <= 1 ? '#ef4444' : s === 2 ? '#f59e0b' : '#22c55e';
    for (var i = 0; i < s; i++) bars[i].classList.add(cls);
    lbl.textContent = txt; lbl.style.color = col;
}
</script>
</body>
</html><?php /**PATH C:\Users\iorda\Desktop\audit_inovex\audit-platform\resources\views/auth/register.blade.php ENDPATH**/ ?>