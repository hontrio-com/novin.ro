
<?php $__env->startSection('title', 'NOVIN.RO - Audit Website AI | SEO, Viteza, GDPR, ANPC Romania'); ?>
<?php $__env->startSection('meta_description', 'Audit complet al site-ului tau in mai putin de 60 de secunde. Analiza SEO, Core Web Vitals, GDPR, ANPC, securitate si UX. Raport PDF detaliat cu solutii concrete. 200 RON, fara abonament.'); ?>
<?php $__env->startSection('meta_keywords', 'audit website romania, audit seo, audit viteza site, core web vitals romania, gdpr audit, anpc audit, optimizare seo romania, raport website ai, novin.ro'); ?>
<?php $__env->startSection('canonical', url('/')); ?>
<?php $__env->startPush('schema'); ?>
<?php
$_jsonLdService = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    '@id' => url('/') . '/#service',
    'name' => 'Audit Website AI - NOVIN.RO',
    'description' => 'Audit complet al website-ului: SEO tehnic, viteza (Core Web Vitals), conformitate GDPR si ANPC.',
    'url' => url('/'),
    'provider' => ['@type' => 'Organization', 'name' => 'NOVIN.RO', '@id' => url('/') . '/#organization'],
    'serviceType' => 'Website Audit',
    'areaServed' => ['@type' => 'Country', 'name' => 'Romania'],
    'offers' => [
        '@type' => 'Offer',
        'price' => '200',
        'priceCurrency' => 'RON',
        'availability' => 'https://schema.org/InStock',
        'priceValidUntil' => now()->addYear()->toDateString(),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$_jsonLdFaq = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'Cat dureaza un audit de website?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Auditul complet dureaza mai putin de 60 de secunde. Vei primi raportul PDF direct pe email.']],
        ['@type' => 'Question', 'name' => 'Ce include auditul website?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Auditul include: SEO tehnic, Core Web Vitals (LCP, CLS, INP), conformitate GDPR si ANPC, securitate SSL, UX si mobile.']],
        ['@type' => 'Question', 'name' => 'Cat costa auditul website?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Auditul complet costa 200 RON, fara abonament. Primesti raport PDF detaliat cu solutii concrete.']],
        ['@type' => 'Question', 'name' => 'Este auditul conform cu legislatia romana?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Da. Auditam conformitatea cu GDPR, normele ANPC (SAL/SOL) si Legea comertului electronic din Romania.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
<script type="application/ld+json"><?php echo $_jsonLdService; ?></script>
<script type="application/ld+json"><?php echo $_jsonLdFaq; ?></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ═══ HERO ═══ */
.hero {
    padding: 96px 24px 80px;
    position: relative; overflow: hidden;
}
.hero-grid-bg {
    position: absolute; inset: 0; pointer-events: none;
    background-image: linear-gradient(var(--rule) 1px, transparent 1px), linear-gradient(90deg, var(--rule) 1px, transparent 1px);
    background-size: 60px 60px; opacity: .3;
    mask-image: radial-gradient(ellipse 80% 55% at 50% 0%, black 30%, transparent 100%);
}
.hero-radial {
    position: absolute; top: -200px; left: 50%; transform: translateX(-50%);
    width: 800px; height: 600px;
    background: radial-gradient(ellipse at 50% 0%, rgba(45,145,206,.11) 0%, transparent 65%);
    pointer-events: none;
}
.hero-inner {
    position: relative; z-index: 1;
    max-width: 1200px; margin: 0 auto;
    display: grid; grid-template-columns: 1fr 400px; gap: 80px; align-items: center;
}
.hero-eyebrow {
    display: flex; align-items: center; gap: 8px; margin-bottom: 24px;
}
.hero-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--blue); animation: dot-pulse 2s ease-in-out infinite; flex-shrink: 0; }
@keyframes dot-pulse { 0%,100%{box-shadow:0 0 0 0 var(--blue-glow)} 50%{box-shadow:0 0 0 6px transparent} }
.hero-eyebrow-txt { font-size: 12px; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; color: var(--ink-4); }

.hero-h1 {
    font-size: clamp(36px, 5vw, 62px);
    font-weight: 800; letter-spacing: -2.5px; line-height: 1.06;
    color: var(--ink); margin-bottom: 20px;
}
.hero-h1 .blue { color: var(--blue); }
.hero-lead { font-size: 16px; color: var(--ink-4); line-height: 1.75; max-width: 500px; margin-bottom: 36px; }

.hero-cta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

.hero-stats {
    margin-top: 40px; padding-top: 36px;
    border-top: 1px solid var(--rule);
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 0;
}
.hero-stat { padding-right: 20px; }
.hero-stat-n { font-size: 22px; font-weight: 800; letter-spacing: -1px; color: var(--ink); line-height: 1; }
.hero-stat-l { font-size: 11px; color: var(--ink-5); margin-top: 4px; font-weight: 500; }

/* FORM CARD */
.form-card {
    background: var(--paper);
    border: 1px solid var(--rule);
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 6px rgba(0,0,0,.04), 0 10px 40px rgba(0,0,0,.07);
}
.form-card-title { font-size: 15px; font-weight: 700; color: var(--ink); letter-spacing: -.3px; margin-bottom: 4px; }
.form-card-sub { font-size: 12px; color: var(--ink-5); margin-bottom: 20px; }
.form-group { margin-bottom: 10px; }
.form-label { display: block; font-size: 11px; font-weight: 600; color: var(--ink-4); letter-spacing: .2px; margin-bottom: 6px; text-transform: uppercase; }
.input-wrap { position: relative; }
.input-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; stroke: var(--ink-6); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; pointer-events: none; transition: stroke .2s; }
.form-input {
    width: 100%; padding: 0 14px 0 36px; height: 42px;
    font-family: var(--font); font-size: 13px; color: var(--ink);
    background: var(--paper-2); border: 1px solid var(--rule); border-radius: 8px;
    outline: none; transition: all .2s;
}
.form-input::placeholder { color: var(--ink-6); }
.form-input:hover { border-color: var(--rule-2); }
.form-input:focus { background: white; border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-bg); }
.form-input:focus ~ svg, .input-wrap:focus-within svg { stroke: var(--blue); }
.form-error { font-size: 11px; color: var(--red); margin-top: 4px; }
.btn-submit {
    width: 100%; height: 46px; margin-top: 6px;
    background: var(--ink); color: white; border: none; border-radius: 8px;
    font-family: var(--font); font-size: 14px; font-weight: 600; letter-spacing: -.2px;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s var(--ease); position: relative; overflow: hidden;
}
.btn-submit:hover { background: var(--ink-2); box-shadow: 0 6px 20px rgba(0,0,0,.18); transform: translateY(-1px); }
.btn-submit:active { transform: translateY(0); box-shadow: none; }
.btn-submit svg { width: 15px; height: 15px; stroke: white; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; transition: transform .2s var(--ease); flex-shrink: 0; }
.btn-submit:hover svg { transform: translateX(3px); }
.form-note { text-align: center; font-size: 11px; color: var(--ink-5); margin-top: 10px; }
.form-note strong { color: var(--ink-4); font-weight: 600; }
.trust-row { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 12px; }
.trust-item { display: flex; align-items: center; gap: 7px; padding: 9px 10px; background: var(--paper-2); border: 1px solid var(--rule); border-radius: 7px; }
.trust-item svg { width: 13px; height: 13px; stroke: var(--ink-4); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
.trust-item span { font-size: 11px; color: var(--ink-4); font-weight: 500; line-height: 1.3; }

/* ═══ PROOF STRIP ═══ */
.proof-strip { border-top: 1px solid var(--rule); border-bottom: 1px solid var(--rule); }
.proof-strip-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; display: flex; overflow-x: auto; }
.proof-stat { padding: 22px 32px; border-right: 1px solid var(--rule); display: flex; align-items: center; gap: 14px; flex-shrink: 0; }
.proof-stat:first-child { padding-left: 0; }
.proof-stat:last-child { border-right: none; }
.proof-stat-n { font-size: 22px; font-weight: 800; letter-spacing: -1px; color: var(--ink); }
.proof-stat-l { font-size: 11px; color: var(--ink-5); font-weight: 500; max-width: 80px; line-height: 1.3; }

/* ═══ SECTIONS ═══ */
.section { padding: 96px 24px; }
.section-alt { background: var(--paper-2); }
.section-kicker { display: block; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--blue); margin-bottom: 12px; }
.section-h2 { font-size: clamp(28px, 4vw, 44px); font-weight: 800; letter-spacing: -2px; color: var(--ink); line-height: 1.07; margin-bottom: 14px; }
.section-lead { font-size: 15px; color: var(--ink-4); line-height: 1.75; max-width: 520px; }
.section-header { margin-bottom: 56px; }

/* ═══ CATEGORIES GRID ═══ */
.cats-grid {
    display: grid; grid-template-columns: repeat(3,1fr);
    border: 1px solid var(--rule); border-radius: 16px; overflow: hidden;
}
.cat-card { padding: 32px 28px; border-right: 1px solid var(--rule); border-bottom: 1px solid var(--rule); transition: background .2s; }
.cat-card:hover { background: var(--paper-2); }
.cat-card:nth-child(3n) { border-right: none; }
.cat-card:nth-last-child(-n+3) { border-bottom: none; }
.cat-num { font-size: 11px; font-weight: 700; letter-spacing: .8px; color: var(--ink-6); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
.cat-num::after { content: ''; flex: 1; max-width: 28px; height: 1px; background: var(--rule); }
.cat-icon { width: 36px; height: 36px; background: var(--paper-3); border: 1px solid var(--rule); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; transition: all .2s; }
.cat-card:hover .cat-icon { background: var(--blue-bg); border-color: var(--blue-bd); }
.cat-icon svg { width: 16px; height: 16px; stroke: var(--ink-4); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; transition: stroke .2s; }
.cat-card:hover .cat-icon svg { stroke: var(--blue); }
.cat-title { font-size: 14px; font-weight: 700; letter-spacing: -.2px; color: var(--ink); margin-bottom: 8px; }
.cat-desc { font-size: 12px; color: var(--ink-5); line-height: 1.65; margin-bottom: 14px; }
.cat-items { display: flex; flex-direction: column; gap: 5px; }
.cat-item { display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: var(--ink-4); line-height: 1.4; }
.cat-item::before { content: ''; width: 4px; height: 4px; border-radius: 50%; background: var(--rule-2); flex-shrink: 0; margin-top: 5px; transition: background .2s; }
.cat-card:hover .cat-item::before { background: var(--blue); }

/* ═══ HOW IT WORKS ═══ */
.how-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
.how-steps { display: flex; flex-direction: column; }
.how-step { display: flex; gap: 20px; padding: 28px 0; border-bottom: 1px solid var(--rule); }
.how-step:first-child { padding-top: 0; }
.how-step:last-child { border-bottom: none; padding-bottom: 0; }
.how-step-n { font-size: 11px; font-weight: 700; letter-spacing: .5px; color: var(--ink-6); flex-shrink: 0; width: 28px; padding-top: 3px; transition: color .2s; }
.how-step:hover .how-step-n { color: var(--blue); }
.how-step-title { font-size: 16px; font-weight: 700; letter-spacing: -.3px; color: var(--ink); margin-bottom: 7px; }
.how-step-desc { font-size: 13px; color: var(--ink-5); line-height: 1.7; }

/* Demo preview card */
.demo-card { background: var(--paper); border: 1px solid var(--rule); border-radius: 16px; padding: 28px; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
.demo-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--rule); }
.demo-url { font-size: 12px; font-weight: 500; color: var(--ink-4); font-family: var(--mono); }
.demo-score-wrap { text-align: center; }
.demo-score-n { font-size: 24px; font-weight: 800; letter-spacing: -1px; color: #16a34a; line-height: 1; }
.demo-score-d { font-size: 10px; color: var(--ink-5); }
.demo-bars { display: flex; flex-direction: column; gap: 10px; }
.demo-bar-row { display: flex; align-items: center; gap: 10px; }
.demo-bar-lbl { font-size: 11px; color: var(--ink-4); font-weight: 500; width: 90px; flex-shrink: 0; }
.demo-bar-track { flex: 1; height: 4px; background: var(--paper-3); border-radius: 2px; overflow: hidden; }
.demo-bar-fill { height: 100%; border-radius: 2px; width: 0; transition: width 1.1s var(--ease); }
.demo-bar-val { font-size: 11px; font-weight: 600; color: var(--ink-3); width: 24px; text-align: right; flex-shrink: 0; }

/* ═══ DARK REPORT PREVIEW ═══ */
.dark-section { background: var(--ink); padding: 96px 24px; overflow: hidden; position: relative; }
.dark-section::before {
    content: ''; position: absolute; top: -250px; right: -150px;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(45,145,206,.12) 0%, transparent 65%);
    pointer-events: none;
}
.dark-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; position: relative; z-index: 1; }
.dark-section .section-kicker { color: rgba(45,145,206,.85); }
.dark-section .section-h2 { color: white; }
.dark-section .section-lead { color: rgba(255,255,255,.45); }
.dark-features { margin-top: 36px; display: flex; flex-direction: column; gap: 0; }
.dark-feature { display: flex; gap: 14px; padding: 20px 0; border-bottom: 1px solid rgba(255,255,255,.06); }
.dark-feature:last-child { border-bottom: none; }
.dark-feature-icon { width: 30px; height: 30px; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08); border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.dark-feature-icon svg { width: 13px; height: 13px; stroke: rgba(255,255,255,.6); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.dark-feature-title { font-size: 13px; font-weight: 600; color: rgba(255,255,255,.85); margin-bottom: 3px; }
.dark-feature-desc { font-size: 12px; color: rgba(255,255,255,.35); line-height: 1.65; }
.dark-mock { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 16px; padding: 28px; }
.dark-mock-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 22px; padding-bottom: 18px; border-bottom: 1px solid rgba(255,255,255,.07); }
.dark-mock-title { font-size: 14px; font-weight: 700; color: rgba(255,255,255,.8); }
.dark-mock-score { font-size: 26px; font-weight: 800; letter-spacing: -1px; color: #4ade80; }
.dark-mock-cats { display: flex; flex-direction: column; gap: 11px; }
.dark-mock-row { display: flex; align-items: center; gap: 10px; }
.dark-mock-lbl { font-size: 11px; color: rgba(255,255,255,.4); width: 100px; flex-shrink: 0; }
.dark-mock-track { flex: 1; height: 4px; background: rgba(255,255,255,.07); border-radius: 2px; overflow: hidden; }
.dark-mock-fill { height: 100%; border-radius: 2px; }
.dark-mock-val { font-size: 11px; font-weight: 600; color: rgba(255,255,255,.5); width: 22px; text-align: right; flex-shrink: 0; }

/* ═══ FAQ ═══ */
.faq-wrap { max-width: 680px; margin: 56px auto 0; }
.faq-item { border-bottom: 1px solid var(--rule); }
.faq-btn {
    width: 100%; background: none; border: none; cursor: pointer;
    display: flex; justify-content: space-between; align-items: center; gap: 16px;
    padding: 20px 0; text-align: left;
    font-family: var(--font); font-size: 14px; font-weight: 600; color: var(--ink);
    transition: color .15s;
}
.faq-btn:hover { color: var(--blue); }
.faq-chevron { width: 16px; height: 16px; stroke: var(--ink-5); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; transition: transform .3s var(--ease), stroke .15s; }
.faq-item.open .faq-chevron { transform: rotate(180deg); stroke: var(--blue); }
.faq-ans { font-size: 13px; color: var(--ink-4); line-height: 1.75; max-height: 0; overflow: hidden; transition: max-height .4s var(--ease), padding .3s; }
.faq-item.open .faq-ans { max-height: 300px; padding-bottom: 20px; }

/* ═══ FINAL CTA ═══ */
.final-cta {
    margin: 0 24px 80px;
    background: var(--ink); border-radius: 24px;
    padding: 72px 56px; text-align: center;
    position: relative; overflow: hidden;
}
.final-cta::before {
    content: ''; position: absolute; top: -150px; left: 50%; transform: translateX(-50%);
    width: 600px; height: 400px;
    background: radial-gradient(ellipse at 50% 0%, rgba(45,145,206,.18) 0%, transparent 65%);
    pointer-events: none;
}
.final-cta-inner { position: relative; z-index: 1; }
.final-cta h2 { font-size: clamp(26px, 4vw, 46px); font-weight: 800; letter-spacing: -2.5px; color: white; line-height: 1.07; margin-bottom: 12px; }
.final-cta p { font-size: 14px; color: rgba(255,255,255,.4); margin-bottom: 36px; }
.final-cta-btns { display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap; }

/* ═══ RESPONSIVE ═══ */
@media (max-width: 1024px) {
    .hero-inner { grid-template-columns: 1fr; gap: 48px; max-width: 600px; }
    .hero-stats { grid-template-columns: repeat(2,1fr); gap: 16px; }
    .how-grid { grid-template-columns: 1fr; gap: 40px; }
    .dark-inner { grid-template-columns: 1fr; gap: 48px; }
    .cats-grid { grid-template-columns: 1fr 1fr; }
    .cat-card:nth-child(3n) { border-right: 1px solid var(--rule); }
    .cat-card:nth-child(2n) { border-right: none; }
}
@media (max-width: 768px) {
    .hero { padding: 64px 16px 56px; }
    .section { padding: 72px 16px; }
    .cats-grid { grid-template-columns: 1fr; border-radius: 12px; }
    .cat-card:nth-child(n) { border-right: none; }
    .final-cta { margin: 0 12px 60px; padding: 48px 20px; }
    .proof-strip-inner { gap: 0; }
}
@media (max-width: 480px) {
    .hero-stats { grid-template-columns: 1fr 1fr; }
    .trust-row { grid-template-columns: 1fr; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<section class="hero">
    <div class="hero-grid-bg"></div>
    <div class="hero-radial"></div>
    <div class="hero-inner">
        <div data-reveal>
            <div class="hero-eyebrow">
                <div class="hero-dot"></div>
                <span class="hero-eyebrow-txt">Audit AI pentru site-uri din Romania</span>
            </div>
            <h1 class="hero-h1">
                Afla exact de ce<br>
                site-ul tau nu<br>
                <span class="blue">vinde destul</span>
            </h1>
            <p class="hero-lead">
                Scanam complet site-ul tau in 60 de secunde si iti livram un raport de audit cu probleme detaliate si solutii concrete, SEO, viteza, GDPR, ANPC, E-E-A-T si UX.
            </p>
            <div class="hero-cta">
                <a href="#form" class="btn btn-dark btn-lg">
                    Incepe auditul acum
                    <svg class="arrow" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#ce-verificam" class="btn btn-outline btn-lg">Ce verificam</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><div class="hero-stat-n">23+</div><div class="hero-stat-l">Puncte verificate</div></div>
                <div class="hero-stat"><div class="hero-stat-n">&lt;60s</div><div class="hero-stat-l">Timp de analiza</div></div>
                <div class="hero-stat"><div class="hero-stat-n">200 RON</div><div class="hero-stat-l">O singura data</div></div>
                <div class="hero-stat"><div class="hero-stat-n">100%</div><div class="hero-stat-l">In limba romana</div></div>
            </div>
        </div>

        <div id="form" data-reveal data-reveal="0.2">
            <div class="form-card">
                <div class="form-card-title">Auditeaza site-ul tau</div>
                <div class="form-card-sub">Introduceti URL-ul si adresa de email pentru a incepe</div>
                <form action="<?php echo e(route('audit.start')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label class="form-label" for="url">URL site</label>
                        <div class="input-wrap">
                            <input type="url" id="url" name="url" class="form-input" placeholder="https://www.site-ul-tau.ro" required value="<?php echo e(old('url')); ?>"/>
                            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <?php $__errorArgs = ['url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Adresa email</label>
                        <div class="input-wrap">
                            <input type="email" id="email" name="email" class="form-input" placeholder="tu@@firma-ta.ro" required value="<?php echo e(old('email')); ?>"/>
                            <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="btn-submit">
                        Porneste auditul · 200 RON
                        <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                    <p class="form-note">Plata securizata prin <strong>Stripe</strong>. Fara abonament.</p>
                </form>
                <div class="trust-row">
                    <div class="trust-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><span>Plata securizata SSL</span></div>
                    <div class="trust-item"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><span>Raport PDF inclus</span></div>
                    <div class="trust-item"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>Rezultate in sub 60s</span></div>
                    <div class="trust-item"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg><span>Fara abonament</span></div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="proof-strip">
    <div class="proof-strip-inner">
        <div class="proof-stat"><div class="proof-stat-n">200+</div><div class="proof-stat-l">site-uri auditate</div></div>
        <div class="proof-stat"><div class="proof-stat-n">4.9/5</div><div class="proof-stat-l">rating mediu clienti</div></div>
        <div class="proof-stat"><div class="proof-stat-n">6 ani</div><div class="proof-stat-l">experienta Inovex.ro</div></div>
        <div class="proof-stat"><div class="proof-stat-n">98%</div><div class="proof-stat-l">clienti multumiti</div></div>
        <div class="proof-stat"><div class="proof-stat-n">24h</div><div class="proof-stat-l">suport disponibil</div></div>
    </div>
</div>


<section class="section" id="ce-verificam">
    <div class="wrap">
        <div class="section-header">
            <span class="section-kicker" data-reveal>Ce verificam</span>
            <h2 class="section-h2" data-reveal data-reveal="0.1">Audit complet in 6 categorii</h2>
            <p class="section-lead" data-reveal data-reveal="0.2">Fiecare categorie este analizata automat. Nu estimari, date reale extrase direct din site-ul tau.</p>
        </div>
        <div class="cats-grid" data-reveal data-reveal="0.2">
            <div class="cat-card">
                <div class="cat-num">01</div>
                <div class="cat-icon"><svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
                <div class="cat-title">Tehnic si Viteza</div>
                <div class="cat-desc">Performanta reala a site-ului, nu estimari. Datele vin direct de la Google PageSpeed Insights.</div>
                <div class="cat-items">
                    <div class="cat-item">PageSpeed scor mobil si desktop</div>
                    <div class="cat-item">Core Web Vitals (LCP, CLS, FCP, TTFB)</div>
                    <div class="cat-item">Certificat SSL si HTTPS</div>
                    <div class="cat-item">Detectare link-uri rupte si erori 404</div>
                </div>
            </div>
            <div class="cat-card">
                <div class="cat-num">02</div>
                <div class="cat-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg></div>
                <div class="cat-title">SEO On-Page</div>
                <div class="cat-desc">Verificam toti factorii tehnici de optimizare care influenteaza pozitia in Google.</div>
                <div class="cat-items">
                    <div class="cat-item">Meta title si meta description</div>
                    <div class="cat-item">Structura headings H1–H6</div>
                    <div class="cat-item">Sitemap XML si robots.txt</div>
                    <div class="cat-item">Alt text imagini si canonical tags</div>
                </div>
            </div>
            <div class="cat-card">
                <div class="cat-num">03</div>
                <div class="cat-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
                <div class="cat-title">Legal si GDPR</div>
                <div class="cat-desc">Legislatia romaneasca impune cerinte clare pentru site-urile comerciale. Verificam conformitatea cu OPC, ANSPDCP si directivele UE.</div>
                <div class="cat-items">
                    <div class="cat-item">Logo-uri si linkuri obligatorii ANPC</div>
                    <div class="cat-item">Date firma: CUI, Nr. Registru Comertului</div>
                    <div class="cat-item">Politica de confidentialitate GDPR</div>
                    <div class="cat-item">Banner cookies conform Directivei ePrivacy</div>
                </div>
            </div>
            <div class="cat-card">
                <div class="cat-num">04</div>
                <div class="cat-icon"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div>
                <div class="cat-title">E-E-A-T</div>
                <div class="cat-desc">Google evalueaza Experienta, Expertiza, Autoritatea si Credibilitatea site-ului tau.</div>
                <div class="cat-items">
                    <div class="cat-item">Dovezi de expertiza si credentiale</div>
                    <div class="cat-item">Testimoniale si recenzii verificabile</div>
                    <div class="cat-item">Pagina "Despre noi" completa</div>
                    <div class="cat-item">Semnale de autoritate in domeniu</div>
                </div>
            </div>
            <div class="cat-card">
                <div class="cat-num">05</div>
                <div class="cat-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                <div class="cat-title">Continut AI</div>
                <div class="cat-desc">AI-ul nostru analizeaza calitatea si relevanta continutului din perspectiva unui potential client.</div>
                <div class="cat-items">
                    <div class="cat-item">Claritate propunere de valoare principala</div>
                    <div class="cat-item">Calitate si relevanta CTA-uri</div>
                    <div class="cat-item">Ton profesional si coeziune brand</div>
                    <div class="cat-item">Optimizare cuvinte cheie in continut</div>
                </div>
            </div>
            <div class="cat-card">
                <div class="cat-num">06</div>
                <div class="cat-icon"><svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></div>
                <div class="cat-title">UX si Design</div>
                <div class="cat-desc">Experienta utilizatorului pe mobil si desktop si factorii care influenteaza rata de conversie.</div>
                <div class="cat-items">
                    <div class="cat-item">Responsive design si viewport corect</div>
                    <div class="cat-item">Accesibilitate (contrast, font size)</div>
                    <div class="cat-item">Structura navigatiei si ierarhia informatiei</div>
                    <div class="cat-item">Elemente de conversie si trust</div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section section-alt" id="cum-functioneaza">
    <div class="wrap">
        <div class="how-grid">
            <div>
                <span class="section-kicker" data-reveal>Cum functioneaza</span>
                <h2 class="section-h2" data-reveal data-reveal="0.1">Simplu, rapid, fara batai de cap</h2>
                <div class="how-steps">
                    <div class="how-step" data-reveal data-reveal="0.2">
                        <div class="how-step-n">01</div>
                        <div><div class="how-step-title">Introduci URL-ul si emailul</div><div class="how-step-desc">Completezi formularul cu adresa site-ului tau. De exemplu, www.exemplu.ro. Nu avem nevoie de acces la cont, parole sau date sensibile.</div></div>
                    </div>
                    <div class="how-step" data-reveal data-reveal="0.3">
                        <div class="how-step-n">02</div>
                        <div><div class="how-step-title">Platesti securizat prin Stripe</div><div class="how-step-desc">200 RON, o singura data, fara abonament. Plata se proceseaza in cateva secunde prin Stripe, acelasi sistem folosit de Amazon si Google.</div></div>
                    </div>
                    <div class="how-step" data-reveal data-reveal="0.4">
                        <div class="how-step-n">03</div>
                        <div><div class="how-step-title">AI-ul analizeaza site-ul tau</div><div class="how-step-desc">Crawlerul nostru scaneaza paginile principale, testeaza viteza, verifica conformitatea legala si trimite totul prin AI pentru analiza de continut.</div></div>
                    </div>
                    <div class="how-step" data-reveal data-reveal="0.5">
                        <div class="how-step-n">04</div>
                        <div><div class="how-step-title">Primesti raportul complet</div><div class="how-step-desc">Raportul apare direct in browser in mai putin de 60 de secunde si primesti un PDF complet pe email cu pasi concreti de rezolvare. Disponibil 30 de zile.</div></div>
                    </div>
                </div>
            </div>
            <div data-reveal data-reveal="0.2">
                <div class="demo-card">
                    <div class="demo-header">
                        <span class="demo-url">www.exemplu.ro</span>
                        <div class="demo-score-wrap"><div class="demo-score-n">76</div><div class="demo-score-d">/100</div></div>
                    </div>
                    <div class="demo-bars" id="demoBars">
                        <div class="demo-bar-row"><span class="demo-bar-lbl">Tehnic</span><div class="demo-bar-track"><div class="demo-bar-fill" style="background:#22c55e" data-w="88"></div></div><span class="demo-bar-val">88</span></div>
                        <div class="demo-bar-row"><span class="demo-bar-lbl">SEO</span><div class="demo-bar-track"><div class="demo-bar-fill" style="background:#f59e0b" data-w="72"></div></div><span class="demo-bar-val">72</span></div>
                        <div class="demo-bar-row"><span class="demo-bar-lbl">Legal & GDPR</span><div class="demo-bar-track"><div class="demo-bar-fill" style="background:#ef4444" data-w="38"></div></div><span class="demo-bar-val">38</span></div>
                        <div class="demo-bar-row"><span class="demo-bar-lbl">E-E-A-T</span><div class="demo-bar-track"><div class="demo-bar-fill" style="background:#22c55e" data-w="85"></div></div><span class="demo-bar-val">85</span></div>
                        <div class="demo-bar-row"><span class="demo-bar-lbl">Continut AI</span><div class="demo-bar-track"><div class="demo-bar-fill" style="background:#f59e0b" data-w="60"></div></div><span class="demo-bar-val">60</span></div>
                        <div class="demo-bar-row"><span class="demo-bar-lbl">UX & Design</span><div class="demo-bar-track"><div class="demo-bar-fill" style="background:#22c55e" data-w="95"></div></div><span class="demo-bar-val">95</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="dark-section">
    <div class="dark-inner wrap">
        <div>
            <span class="section-kicker" data-reveal>Ce primesti</span>
            <h2 class="section-h2" data-reveal data-reveal="0.1">Raport detaliat, nu o lista generica</h2>
            <p class="section-lead" data-reveal data-reveal="0.2">Fiecare problema identificata vine cu descriere clara, context si o sugestie concreta de rezolvare.</p>
            <div class="dark-features" data-reveal data-reveal="0.3">
                <div class="dark-feature">
                    <div class="dark-feature-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                    <div><div class="dark-feature-title">Scor per categorie si scor general</div><div class="dark-feature-desc">Fiecare categorie primeste un scor de la 0 la 100 calibrat pentru piata romaneasca.</div></div>
                </div>
                <div class="dark-feature">
                    <div class="dark-feature-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div><div class="dark-feature-title">Probleme prioritizate dupa severitate</div><div class="dark-feature-desc">Critic, avertisment sau informatie: stii exact ce sa rezolvi primul pentru impact maxim.</div></div>
                </div>
                <div class="dark-feature">
                    <div class="dark-feature-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                    <div><div class="dark-feature-title">Rezumat AI cu pasi de rezolvare</div><div class="dark-feature-desc">AI-ul genereaza un rezumat complet cu toate solutiile si pasii concreti pentru fiecare problema gasita.</div></div>
                </div>
                <div class="dark-feature">
                    <div class="dark-feature-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div><div class="dark-feature-title">Raport PDF complet pe email</div><div class="dark-feature-desc">Primesti raportul in format PDF profesional, cu toate detaliile si pasii de implementare.</div></div>
                </div>
            </div>
        </div>
        <div data-reveal data-reveal="0.2">
            <div class="dark-mock">
                <div class="dark-mock-header">
                    <div class="dark-mock-title">www.exemplu.ro</div>
                    <div class="dark-mock-score">76<span style="font-size:13px;opacity:.35">/100</span></div>
                </div>
                <div class="dark-mock-cats">
                    <div class="dark-mock-row"><span class="dark-mock-lbl">Tehnic</span><div class="dark-mock-track"><div class="dark-mock-fill" style="width:88%;background:#4ade80"></div></div><span class="dark-mock-val">88</span></div>
                    <div class="dark-mock-row"><span class="dark-mock-lbl">SEO On-Page</span><div class="dark-mock-track"><div class="dark-mock-fill" style="width:72%;background:#fbbf24"></div></div><span class="dark-mock-val">72</span></div>
                    <div class="dark-mock-row"><span class="dark-mock-lbl">Legal & GDPR</span><div class="dark-mock-track"><div class="dark-mock-fill" style="width:38%;background:#f87171"></div></div><span class="dark-mock-val">38</span></div>
                    <div class="dark-mock-row"><span class="dark-mock-lbl">E-E-A-T</span><div class="dark-mock-track"><div class="dark-mock-fill" style="width:85%;background:#4ade80"></div></div><span class="dark-mock-val">85</span></div>
                    <div class="dark-mock-row"><span class="dark-mock-lbl">Continut AI</span><div class="dark-mock-track"><div class="dark-mock-fill" style="width:60%;background:#fbbf24"></div></div><span class="dark-mock-val">60</span></div>
                    <div class="dark-mock-row"><span class="dark-mock-lbl">UX & Design</span><div class="dark-mock-track"><div class="dark-mock-fill" style="width:95%;background:#4ade80"></div></div><span class="dark-mock-val">95</span></div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section" id="pret" style="text-align:center">
    <div class="wrap">
        <span class="section-kicker" data-reveal>Pret</span>
        <h2 class="section-h2" data-reveal data-reveal="0.1" style="max-width:400px;margin:0 auto 12px">Simplu si transparent</h2>
        <p class="section-lead" data-reveal data-reveal="0.2" style="margin:0 auto 56px">Fara abonamente, fara costuri ascunse. Platesti o data si primesti raportul complet.</p>

        <div data-reveal data-reveal="0.2" style="max-width:420px;margin:0 auto">
            <div style="border:1px solid var(--rule);border-radius:20px;padding:40px;background:var(--paper);text-align:left">
                <div style="font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--ink-5);margin-bottom:20px">Audit Standard</div>
                <div style="font-size:52px;font-weight:800;letter-spacing:-3px;color:var(--ink);line-height:1;margin-bottom:6px">200 <span style="font-size:22px;font-weight:600;letter-spacing:-1px">RON</span></div>
                <div style="font-size:13px;color:var(--ink-5);margin-bottom:28px">o singura plata, fara abonament</div>
                <div style="display:flex;flex-direction:column;gap:11px;margin-bottom:28px">
                    <?php $__currentLoopData = ['Raport complet in browser in 60 secunde','23+ puncte verificate automat','Analiza continut prin AI','Rezumat AI cu pasi de rezolvare','PDF profesional pe email','Disponibil 30 de zile']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--ink-3)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <?php echo e($feat); ?>

                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button onclick="document.getElementById('form').scrollIntoView({behavior:'smooth'})" class="btn btn-dark btn-lg" style="width:100%;justify-content:center">
                    Porneste auditul acum
                    <svg class="arrow" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>


<section class="section section-alt" id="intrebari">
    <div class="wrap" style="text-align:center">
        <span class="section-kicker" data-reveal>Intrebari frecvente</span>
        <h2 class="section-h2" data-reveal data-reveal="0.1" style="max-width:400px;margin:0 auto">Raspunsuri la intrebarile tale</h2>
        <div class="faq-wrap" data-reveal data-reveal="0.2">
            <div class="faq-item">
                <button class="faq-btn">Cat dureaza auditul?<svg class="faq-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
                <div class="faq-ans">De obicei mai putin de 60 de secunde. Timpul depinde de viteza site-ului tau si de numarul de pagini scanate. Poti urmari progresul in timp real pe pagina de analiza.</div>
            </div>
            <div class="faq-item">
                <button class="faq-btn">Am nevoie sa dau acces la site sau CMS?<svg class="faq-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
                <div class="faq-ans">Nu. Auditul se face exclusiv din exterior, exact cum ar analiza Google sau un utilizator site-ul tau. Nu ai nevoie sa oferi parole, acces FTP sau orice date sensibile.</div>
            </div>
            <div class="faq-item">
                <button class="faq-btn">Ce se intampla daca nu sunt multumit de raport?<svg class="faq-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
                <div class="faq-ans">Daca raportul nu contine informatii utile pentru site-ul tau, contacteaza-ne la contact@@inovex.ro si analizam situatia. Fiecare audit este verificat manual inainte de livrare.</div>
            </div>
            <div class="faq-item">
                <button class="faq-btn">Cat timp este disponibil raportul?<svg class="faq-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
                <div class="faq-ans">Linkul catre raportul online este activ 30 de zile de la generare. PDF-ul primit pe email il ai permanent si il poti descarca si arhiva oricand.</div>
            </div>
            <div class="faq-item">
                <button class="faq-btn">Functioneaza pentru orice tip de site?<svg class="faq-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
                <div class="faq-ans">Da, WordPress, Wix, Shopify, site-uri custom, magazine online, pagini de prezentare. Orice site accesibil public poate fi auditat. Nu functioneaza pentru site-uri cu autentificare obligatorie.</div>
            </div>
            <div class="faq-item">
                <button class="faq-btn">Plata este securizata?<svg class="faq-chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
                <div class="faq-ans">Procesam platile exclusiv prin Stripe. Nu stocam niciun detaliu de card. Tranzactiile sunt criptate SSL end-to-end.</div>
            </div>
        </div>
    </div>
</section>


<div class="final-cta">
    <div class="final-cta-inner" data-reveal>
        <h2>Gata sa afli adevarul<br>despre site-ul tau?</h2>
        <p>Raport complet in 60 de secunde. 200 RON, fara abonament.</p>
        <div class="final-cta-btns">
            <button onclick="document.getElementById('form').scrollIntoView({behavior:'smooth'})" class="btn btn-on-dark btn-xl">
                Porneste auditul acum
                <svg class="arrow" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
            <a href="https://inovex.ro/contact" target="_blank" class="btn btn-ghost-on-dark btn-xl">Vorbeste cu un expert</a>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Demo bars animate on scroll
(function(){
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                document.querySelectorAll('#demoBars .demo-bar-fill').forEach(b => { b.style.width = b.dataset.w + '%'; });
                io.disconnect();
            }
        });
    }, { threshold: 0.3 });
    const el = document.getElementById('demoBars');
    if (el) io.observe(el);
})();

// FAQ accordion
document.querySelectorAll('.faq-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const item = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\iorda\Desktop\audit_inovex\audit-platform\resources\views/home.blade.php ENDPATH**/ ?>