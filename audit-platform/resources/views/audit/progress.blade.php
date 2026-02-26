@extends('layouts.app')
@section('title', 'Se analizeaza — Inovex Audit')

@push('styles')
<style>
.progress-page {
    min-height: calc(100vh - 56px);
    display: grid; grid-template-columns: 1fr 1fr;
}

/* LEFT */
.pp-left {
    display: flex; align-items: center; justify-content: center;
    padding: 80px 56px; border-right: 1px solid var(--rule);
}
.pp-left-inner { width: 100%; max-width: 400px; }
.pp-live { display: flex; align-items: center; gap: 8px; margin-bottom: 28px; }
.pp-live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--blue); animation: live-pulse 1.4s ease-in-out infinite; }
@keyframes live-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.55;transform:scale(.8)} }
.pp-live-txt { font-size: 11px; font-weight: 700; letter-spacing: .7px; text-transform: uppercase; color: var(--ink-5); }
.pp-title { font-size: 30px; font-weight: 800; letter-spacing: -1.5px; color: var(--ink); line-height: 1.08; margin-bottom: 10px; }
.pp-url-badge {
    display: inline-block; font-size: 12px; font-weight: 500; color: var(--ink-4);
    font-family: var(--mono); background: var(--paper-3);
    border: 1px solid var(--rule); padding: 5px 12px; border-radius: 6px;
    max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    margin-bottom: 36px;
}
.pp-bar-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px; }
.pp-bar-lbl { font-size: 11px; font-weight: 600; color: var(--ink-5); }
.pp-bar-pct { font-size: 12px; font-weight: 700; color: var(--ink-2); }
.pp-track { height: 3px; background: var(--rule); border-radius: 2px; overflow: hidden; margin-bottom: 28px; }
.pp-fill { height: 100%; background: var(--blue); border-radius: 2px; width: 0; transition: width .8s var(--ease); }
.pp-steps { display: flex; flex-direction: column; gap: 2px; }
.pp-step { display: flex; align-items: center; gap: 13px; padding: 11px 13px; border-radius: 8px; transition: background .25s; }
.pp-step.active { background: var(--blue-bg); }
.pp-step-ico { width: 18px; height: 18px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.ico-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--rule-2); }
.ico-spin { width: 16px; height: 16px; border: 2px solid var(--blue-bd); border-top-color: var(--blue); border-radius: 50%; animation: step-spin .8s linear infinite; display: none; }
@keyframes step-spin { to { transform: rotate(360deg); } }
.ico-check { display: none; width: 16px; height: 16px; stroke: var(--green); fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
.pp-step.active .ico-dot { display: none; }
.pp-step.active .ico-spin { display: block; }
.pp-step.done .ico-dot { display: none; }
.pp-step.done .ico-spin { display: none; }
.pp-step.done .ico-check { display: block; }
.pp-step-name { font-size: 13px; color: var(--ink-5); font-weight: 500; transition: color .25s; }
.pp-step.active .pp-step-name { color: var(--ink); }
.pp-step.done .pp-step-name { color: var(--ink-4); }

/* RIGHT */
.pp-right { background: var(--paper-2); display: flex; align-items: center; justify-content: center; padding: 80px 56px; position: relative; overflow: hidden; }
.pp-right::before { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 400px; height: 400px; background: radial-gradient(circle, var(--blue-bg) 0%, transparent 70%); pointer-events: none; }
.pp-right-inner { position: relative; z-index: 1; width: 100%; max-width: 320px; }

/* Orbit */
.orbit-wrap { width: 220px; height: 220px; margin: 0 auto 32px; position: relative; display: flex; align-items: center; justify-content: center; }
.orbit { position: absolute; border-radius: 50%; border: 1px solid; }
.orbit-1 { width: 64px; height: 64px; border-color: var(--blue-bd); }
.orbit-2 { width: 112px; height: 112px; border-color: var(--rule); animation: o-spin 4s linear infinite; }
.orbit-3 { width: 166px; height: 166px; border-color: var(--rule); animation: o-spin 8s linear infinite reverse; }
.orbit-4 { width: 220px; height: 220px; border-color: var(--rule); animation: o-spin 14s linear infinite; }
@keyframes o-spin { to { transform: rotate(360deg); } }
.orbit-2::after,.orbit-3::after,.orbit-4::after { content: ''; position: absolute; top: -3px; left: 50%; transform: translateX(-50%); border-radius: 50%; }
.orbit-2::after { width: 6px; height: 6px; background: var(--blue); }
.orbit-3::after { width: 5px; height: 5px; background: var(--blue); opacity: .4; }
.orbit-4::after { width: 4px; height: 4px; background: var(--ink-6); }
.orbit-center {
    position: relative; z-index: 1;
    width: 56px; height: 56px; border-radius: 50%;
    background: var(--paper); border: 1px solid var(--blue-bd);
    box-shadow: 0 0 20px var(--blue-glow);
    display: flex; align-items: center; justify-content: center;
}
.orbit-center svg { width: 22px; height: 22px; stroke: var(--blue); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

/* Fact card */
.fact-card { background: var(--paper); border: 1px solid var(--rule); border-radius: 12px; padding: 18px 20px; }
.fact-label { font-size: 10px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: var(--ink-5); margin-bottom: 8px; }
.fact-text { font-size: 12px; color: var(--ink-4); line-height: 1.7; }

/* MOBILE: stacked layout */
@media (max-width: 900px) {
    .progress-page { grid-template-columns: 1fr; }
    .pp-left { border-right: none; border-bottom: 1px solid var(--rule); padding: 56px 20px 40px; }
    .pp-right { display: flex; padding: 32px 20px 40px; }
    .orbit-wrap { width: 140px; height: 140px; margin-bottom: 20px; }
    .orbit-1 { width: 44px; height: 44px; }
    .orbit-2 { width: 76px; height: 76px; }
    .orbit-3 { width: 110px; height: 110px; }
    .orbit-4 { width: 140px; height: 140px; }
    .orbit-center { width: 40px; height: 40px; }
    .orbit-center svg { width: 16px; height: 16px; }
    .pp-right-inner { display: flex; gap: 20px; align-items: center; max-width: 100%; }
    .fact-card { flex: 1; }
}
@media (max-width: 480px) {
    .pp-left { padding: 40px 16px 32px; }
    .pp-right { padding: 24px 16px 32px; }
    .pp-right-inner { flex-direction: column; align-items: flex-start; }
    .orbit-wrap { display: none; }
}
</style>
@endpush

@section('content')
<div class="progress-page">
    <div class="pp-left">
        <div class="pp-left-inner">
            <div class="pp-live"><div class="pp-live-dot"></div><span class="pp-live-txt">Analiza in curs</span></div>
            <h2 class="pp-title">Analizam<br>site-ul tau</h2>
            <div class="pp-url-badge">{{ $audit->url }}</div>
            <div class="pp-bar-row"><span class="pp-bar-lbl">Progres</span><span class="pp-bar-pct" id="pctLbl">0%</span></div>
            <div class="pp-track"><div class="pp-fill" id="ppFill"></div></div>
            <div class="pp-steps" id="ppSteps">
                <div class="pp-step" id="s1"><div class="pp-step-ico"><div class="ico-dot"></div><div class="ico-spin"></div><svg class="ico-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><span class="pp-step-name">Scanare pagini si structura</span></div>
                <div class="pp-step" id="s2"><div class="pp-step-ico"><div class="ico-dot"></div><div class="ico-spin"></div><svg class="ico-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><span class="pp-step-name">Verificare viteza si performanta</span></div>
                <div class="pp-step" id="s3"><div class="pp-step-ico"><div class="ico-dot"></div><div class="ico-spin"></div><svg class="ico-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><span class="pp-step-name">Analiza SEO si conformitate legala</span></div>
                <div class="pp-step" id="s4"><div class="pp-step-ico"><div class="ico-dot"></div><div class="ico-spin"></div><svg class="ico-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><span class="pp-step-name">Analiza continut prin AI</span></div>
                <div class="pp-step" id="s5"><div class="pp-step-ico"><div class="ico-dot"></div><div class="ico-spin"></div><svg class="ico-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><span class="pp-step-name">Calculare scoruri si generare raport</span></div>
            </div>
        </div>
    </div>

    <div class="pp-right">
        <div class="pp-right-inner">
            <div class="orbit-wrap">
                <div class="orbit orbit-1"></div>
                <div class="orbit orbit-2"></div>
                <div class="orbit orbit-3"></div>
                <div class="orbit orbit-4"></div>
                <div class="orbit-center">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
                </div>
            </div>
            <div class="fact-card">
                <div class="fact-label">Stiai ca?</div>
                <div class="fact-text" id="factTxt">Site-urile cu scor PageSpeed sub 50 pe mobil pierd in medie 53% din vizitatori inainte ca pagina sa se incarce.</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const steps = ['s1','s2','s3','s4','s5'];
    const pcts  = [15,35,55,80,95];
    const facts = [
        "Site-urile cu scor PageSpeed sub 50 pe mobil pierd in medie 53% din vizitatori inainte ca pagina sa se incarce.",
        "Lipsa logo-ului ANPC pe site-urile comerciale din Romania poate duce la amenzi de pana la 10.000 RON.",
        "Google penalizeaza site-urile fara HTTPS de la 2018. Un certificat SSL influenteaza direct pozitia in cautari.",
        "75% din utilizatori judeca credibilitatea unei companii pe baza designului site-ului, conform studiilor de UX.",
        "Site-urile cu meta description optimizata primesc cu 5.8% mai multe click-uri din rezultatele Google."
    ];
    let cur = 0;
    function setStep(i) {
        if (i > 0) { const p = document.getElementById(steps[i-1]); p.classList.remove('active'); p.classList.add('done'); }
        if (i < steps.length) {
            document.getElementById(steps[i]).classList.add('active');
            document.getElementById('ppFill').style.width = pcts[i] + '%';
            document.getElementById('pctLbl').textContent = pcts[i] + '%';
            document.getElementById('factTxt').textContent = facts[i];
            cur = i + 1;
        }
    }
    setStep(0);
    const timer = setInterval(() => { if (cur < steps.length) setStep(cur); else clearInterval(timer); }, 9000);

    const poll = setInterval(async () => {
        try {
            const r = await fetch('/api/audit/{{ $audit->id }}/status');
            const d = await r.json();
            if (d.status === 'completed' && d.redirect) {
                clearInterval(poll); clearInterval(timer);
                steps.forEach(id => { const el = document.getElementById(id); el.classList.remove('active'); el.classList.add('done'); });
                document.getElementById('ppFill').style.width = '100%';
                document.getElementById('pctLbl').textContent = '100%';
                setTimeout(() => window.location.href = d.redirect, 500);
            }
            if (d.status === 'failed') {
                clearInterval(poll); clearInterval(timer);
                document.querySelector('.pp-title').textContent = 'A aparut o eroare';
                document.querySelector('.pp-url-badge').textContent = 'Contacteaza-ne: contact@inovex.ro';
                document.querySelector('.pp-live-dot').style.background = 'var(--red)';
            }
        } catch(e) {}
    }, 4000);
})();
</script>
@endpush