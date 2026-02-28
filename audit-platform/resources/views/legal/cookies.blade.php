@extends('layouts.app')

@section('title', 'Politica Cookies — NOVIN.RO')
@section('meta_description', 'Politica de cookies NOVIN.RO. Informatii despre tipurile de cookies folosite, scopul lor si cum iti poti gestiona preferintele.')

@push('styles')
<style>
.legal-hero{background:var(--ink);padding:72px 24px 56px;text-align:center;}
.legal-hero-label{font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--blue);margin-bottom:16px;}
.legal-hero h1{font-size:clamp(28px,5vw,42px);font-weight:800;color:#fff;letter-spacing:-.5px;margin-bottom:12px;}
.legal-hero-meta{font-size:13px;color:rgba(255,255,255,.35);}

.legal-wrap{max-width:820px;margin:0 auto;padding:64px 24px 100px;}
.legal-toc{background:var(--paper-2);border:1px solid var(--rule);border-radius:12px;padding:28px 32px;margin-bottom:56px;}
.legal-toc h2{font-size:13px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;color:var(--ink-4);margin-bottom:16px;}
.legal-toc ol{padding-left:20px;display:flex;flex-direction:column;gap:8px;}
.legal-toc ol a{font-size:14px;color:var(--blue);text-decoration:none;}
.legal-toc ol a:hover{text-decoration:underline;}

.legal-section{margin-bottom:52px;scroll-margin-top:80px;}
.legal-section h2{font-size:20px;font-weight:700;color:var(--ink);letter-spacing:-.3px;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--rule);}
.legal-section h3{font-size:15px;font-weight:600;color:var(--ink);margin:24px 0 10px;}
.legal-section p{font-size:14px;color:var(--ink-3);line-height:1.8;margin-bottom:14px;}
.legal-section ul,.legal-section ol{padding-left:22px;margin-bottom:14px;display:flex;flex-direction:column;gap:8px;}
.legal-section li{font-size:14px;color:var(--ink-3);line-height:1.7;}
.legal-section strong{color:var(--ink);font-weight:600;}
.legal-section a{color:var(--blue);text-decoration:none;}
.legal-section a:hover{text-decoration:underline;}

.legal-box{background:var(--paper-2);border:1px solid var(--rule);border-radius:8px;padding:18px 22px;margin:16px 0;}
.legal-box p{margin:0;font-size:13px;color:var(--ink-3);line-height:1.7;}

.legal-highlight{background:rgba(45,145,206,.06);border-left:3px solid var(--blue);border-radius:0 8px 8px 0;padding:14px 18px;margin:16px 0;}
.legal-highlight p{margin:0;font-size:13px;color:var(--ink-2);line-height:1.7;}

.legal-warn{background:rgba(239,68,68,.05);border-left:3px solid #ef4444;border-radius:0 8px 8px 0;padding:14px 18px;margin:16px 0;}
.legal-warn p{margin:0;font-size:13px;color:var(--ink-2);line-height:1.7;}

.legal-table{width:100%;border-collapse:collapse;margin:16px 0;font-size:13px;}
.legal-table th{background:var(--paper-2);border:1px solid var(--rule);padding:10px 14px;text-align:left;font-weight:600;color:var(--ink);font-size:12px;text-transform:uppercase;letter-spacing:.4px;}
.legal-table td{border:1px solid var(--rule);padding:10px 14px;color:var(--ink-3);vertical-align:top;line-height:1.6;}
.legal-table tr:hover td{background:var(--paper-2);}

/* Cookie type cards */
.cookie-cards{display:flex;flex-direction:column;gap:16px;margin:24px 0;}
.cookie-card{border:1px solid var(--rule);border-radius:12px;overflow:hidden;}
.cookie-card-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--paper-2);}
.cookie-card-title{display:flex;align-items:center;gap:10px;}
.cookie-card-title strong{font-size:14px;font-weight:700;color:var(--ink);}
.cookie-badge{font-size:10px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;padding:3px 8px;border-radius:20px;}
.badge-required{background:rgba(34,197,94,.12);color:#16a34a;}
.badge-optional{background:rgba(245,158,11,.12);color:#d97706;}
.cookie-card-body{padding:16px 20px;}
.cookie-card-body p{font-size:13px;color:var(--ink-3);line-height:1.7;margin:0 0 12px;}
.cookie-card-body p:last-child{margin:0;}

/* Browser guides */
.browser-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:16px 0;}
.browser-item{background:var(--paper-2);border:1px solid var(--rule);border-radius:8px;padding:14px 16px;text-align:center;}
.browser-item strong{display:block;font-size:13px;color:var(--ink);margin-bottom:6px;}
.browser-item a{font-size:12px;color:var(--blue);}
@media(max-width:600px){.browser-grid{grid-template-columns:1fr 1fr;}.cookie-card-header{flex-direction:column;align-items:flex-start;gap:8px;}}
</style>
@endpush

@section('content')
<div class="legal-hero">
    <div class="legal-hero-label">Document legal</div>
    <h1>Politica Cookies</h1>
    <p class="legal-hero-meta">Ultima actualizare: {{ date('d.m.Y') }} &nbsp;&bull;&nbsp; Versiunea 1.0 &nbsp;&bull;&nbsp; Conform Directivei ePrivacy si GDPR</p>
</div>

<div class="legal-wrap">

    <div class="legal-highlight" style="margin-bottom:40px;">
        <p><strong>Pe scurt:</strong> Folosim cookies esentiale pentru functionarea site-ului (fara acestea nu poti folosi Platforma) si cookies optionale de analytics pentru a intelege cum este utilizat serviciul. Ai control deplin — poti accepta sau refuza cookies optionale oricand.</p>
    </div>

    {{-- TOC --}}
    <div class="legal-toc">
        <h2>Cuprins</h2>
        <ol>
            <li><a href="#ce-sunt">1. Ce sunt cookies</a></li>
            <li><a href="#legislatie">2. Cadrul legal aplicabil</a></li>
            <li><a href="#tipuri">3. Tipurile de cookies folosite</a></li>
            <li><a href="#lista">4. Lista detaliata a cookies</a></li>
            <li><a href="#terti">5. Cookies ale tertilor</a></li>
            <li><a href="#consimtamant">6. Consimtamantul tau</a></li>
            <li><a href="#gestionare">7. Cum iti gestionezi cookies</a></li>
            <li><a href="#consecinte">8. Consecintele dezactivarii cookies</a></li>
            <li><a href="#modificari">9. Modificarea politicii</a></li>
            <li><a href="#contact">10. Contact</a></li>
        </ol>
    </div>

    {{-- 1 --}}
    <div class="legal-section" id="ce-sunt">
        <h2>1. Ce sunt cookies</h2>
        <p>Cookie-urile sunt fisiere text de mici dimensiuni care sunt stocate pe dispozitivul tau (calculator, telefon, tableta) atunci cand vizitezi un website. Ele permit site-ului sa iti memoreze actiunile si preferintele pe o anumita perioada de timp, astfel incat sa nu fie nevoie sa le re-introduci de fiecare data cand revii pe site sau navighezi de pe o pagina pe alta.</p>
        <p>Pe langa cookies, folosim si tehnologii similare precum:</p>
        <ul>
            <li><strong>Local Storage</strong> — stocarea datelor direct in browser, fara expirare automata;</li>
            <li><strong>Session Storage</strong> — date stocate temporar, sterse la inchiderea tab-ului;</li>
            <li><strong>Pixeli de tracking</strong> — imagini de 1x1 pixel folosite pentru masurarea interactiunilor (daca sunt utilizate de terti).</li>
        </ul>
        <p>In aceasta politica, termenul „cookies" se refera la toate aceste tehnologii.</p>
    </div>

    {{-- 2 --}}
    <div class="legal-section" id="legislatie">
        <h2>2. Cadrul legal aplicabil</h2>
        <p>Utilizarea cookies pe Platforma noastra este reglementata de:</p>
        <ul>
            <li><strong>Directiva 2002/58/CE</strong> (Directiva ePrivacy), astfel cum a fost modificata prin Directiva 2009/136/CE;</li>
            <li><strong>Legea nr. 506/2004</strong> privind prelucrarea datelor cu caracter personal si protectia vietii private in sectorul comunicatiilor electronice;</li>
            <li><strong>Regulamentul (UE) 2016/679</strong> (GDPR) — pentru cookies care implica prelucrarea de date personale;</li>
            <li><strong>Ghidul ANSPDCP</strong> privind utilizarea cookies si tehnologiilor similare.</li>
        </ul>
        <div class="legal-highlight">
            <p>Conform legii, cookies esentiale (strict necesare) pot fi plasate fara consimtamantul tau. Toate celelalte categorii de cookies necesita consimtamantul tau <strong>explicit, prealabil si informat</strong>, acordat prin bannerul de cookies.</p>
        </div>
    </div>

    {{-- 3 --}}
    <div class="legal-section" id="tipuri">
        <h2>3. Tipurile de cookies folosite</h2>

        <div class="cookie-cards">

            {{-- Esentiale --}}
            <div class="cookie-card">
                <div class="cookie-card-header">
                    <div class="cookie-card-title">
                        <strong>Cookies Esentiale (Strict Necesare)</strong>
                    </div>
                    <span class="cookie-badge badge-required">Obligatorii — fara consimtamant</span>
                </div>
                <div class="cookie-card-body">
                    <p>Aceste cookies sunt absolut necesare pentru functionarea corecta a Platformei. Fara ele, serviciile de baza (autentificare, securitate, sesiune) nu pot functiona. Nu pot fi dezactivate din bannerul de cookies.</p>
                    <p><strong>Includ:</strong> cookie-ul de sesiune (te mentine autentificat), token CSRF (protejeaza impotriva atacurilor cross-site), preferinte cookie banner (memoreaza alegerea ta).</p>
                    <p><strong>Durata:</strong> Sesiune (se sterg la inchiderea browserului) sau pana la 1 an pentru preferinte.</p>
                </div>
            </div>

            {{-- Functionale --}}
            <div class="cookie-card">
                <div class="cookie-card-header">
                    <div class="cookie-card-title">
                        <strong>Cookies Functionale (Preferinte)</strong>
                    </div>
                    <span class="cookie-badge badge-optional">Optionale — necesita consimtamant</span>
                </div>
                <div class="cookie-card-body">
                    <p>Aceste cookies permit Platformei sa isi aminteasca alegerile pe care le-ai facut si sa ofere functionalitati imbunatatite si mai personalizate. Nu sunt strict necesare, dar imbunatatesc experienta de utilizare.</p>
                    <p><strong>Includ:</strong> preferinte de limba, tema interfetei (light/dark), setari de afisare, ultimele actiuni efectuate.</p>
                    <p><strong>Durata:</strong> Pana la 1 an.</p>
                </div>
            </div>

            {{-- Analytics --}}
            <div class="cookie-card">
                <div class="cookie-card-header">
                    <div class="cookie-card-title">
                        <strong>Cookies de Analiza (Analytics)</strong>
                    </div>
                    <span class="cookie-badge badge-optional">Optionale — necesita consimtamant</span>
                </div>
                <div class="cookie-card-body">
                    <p>Aceste cookies ne ajuta sa intelegem cum interactioneaza vizitatorii cu Platforma, colectand informatii in mod anonim sau pseudonim. Datele sunt folosite exclusiv pentru imbunatatirea serviciului.</p>
                    <p><strong>Includ:</strong> Google Analytics 4 (GA4) — masoara numarul de vizitatori, paginile vizitate, durata sesiunii, sursa traficului. Datele sunt anonimizate prin IP masking.</p>
                    <p><strong>Durata:</strong> Pana la 13 luni (conform limitei impuse de Google pentru GA4).</p>
                    <p><strong>Optare in afara:</strong> Poti instala <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics Opt-out Browser Add-on</a> pentru a bloca GA4 in toate site-urile.</p>
                </div>
            </div>

            {{-- Marketing --}}
            <div class="cookie-card">
                <div class="cookie-card-header">
                    <div class="cookie-card-title">
                        <strong>Cookies de Marketing</strong>
                    </div>
                    <span class="cookie-badge badge-optional">Optionale — necesita consimtamant</span>
                </div>
                <div class="cookie-card-body">
                    <p>In prezent, <strong>nu folosim cookies de marketing sau publicitate comportamentala</strong>. Nu afisam reclame si nu urmarim comportamentul tau in scop publicitar pe alte site-uri.</p>
                    <p>In cazul in care vom introduce astfel de cookies in viitor, aceasta politica va fi actualizata si vei fi notificat.</p>
                </div>
            </div>

        </div>
    </div>

    {{-- 4 --}}
    <div class="legal-section" id="lista">
        <h2>4. Lista detaliata a cookies</h2>

        <h3>4.1 Cookies proprii (first-party)</h3>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Nume cookie</th>
                    <th>Categorie</th>
                    <th>Scop</th>
                    <th>Durata</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>audit-ai-session</code></td>
                    <td>Esential</td>
                    <td>Sesiunea utilizatorului autentificat — mentine starea de login</td>
                    <td>2 ore (sesiune)</td>
                </tr>
                <tr>
                    <td><code>XSRF-TOKEN</code></td>
                    <td>Esential</td>
                    <td>Protectie CSRF — previne atacurile cross-site request forgery</td>
                    <td>Sesiune</td>
                </tr>
                <tr>
                    <td><code>cookie_consent</code></td>
                    <td>Esential</td>
                    <td>Memoreaza preferintele tale privind cookies</td>
                    <td>1 an</td>
                </tr>
                <tr>
                    <td><code>remember_token</code></td>
                    <td>Esential</td>
                    <td>Cookie „Tine-ma minte" — mentine autentificarea pe durata extinsa</td>
                    <td>30 zile</td>
                </tr>
            </tbody>
        </table>

        <h3>4.2 Cookies terti (third-party)</h3>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Furnizor</th>
                    <th>Cookie</th>
                    <th>Categorie</th>
                    <th>Scop</th>
                    <th>Durata</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Google Analytics</strong></td>
                    <td><code>_ga</code></td>
                    <td>Analytics</td>
                    <td>Distinge utilizatorii unici</td>
                    <td>13 luni</td>
                </tr>
                <tr>
                    <td><strong>Google Analytics</strong></td>
                    <td><code>_ga_XXXXX</code></td>
                    <td>Analytics</td>
                    <td>Mentine starea sesiunii GA4</td>
                    <td>13 luni</td>
                </tr>
                <tr>
                    <td><strong>Google Analytics</strong></td>
                    <td><code>_gid</code></td>
                    <td>Analytics</td>
                    <td>Distinge utilizatorii (sesiune)</td>
                    <td>24 ore</td>
                </tr>
                <tr>
                    <td><strong>Stripe</strong></td>
                    <td><code>__stripe_mid</code></td>
                    <td>Esential</td>
                    <td>Prevenire frauda la plati (necesar pentru procesarea platii)</td>
                    <td>1 an</td>
                </tr>
                <tr>
                    <td><strong>Stripe</strong></td>
                    <td><code>__stripe_sid</code></td>
                    <td>Esential</td>
                    <td>Sesiunea de plata securizata</td>
                    <td>30 minute</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- 5 --}}
    <div class="legal-section" id="terti">
        <h2>5. Cookies ale tertilor</h2>
        <p>Unele functionalitati ale Platformei implica servicii ale tertilor care pot plasa propriile cookies. Nu avem control direct asupra acestor cookies si te incurajam sa consulti politicile de confidentialitate ale furnizorilor respectivi:</p>
        <ul>
            <li>
                <strong>Google Analytics</strong> — <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">policies.google.com/privacy</a><br>
                <span style="font-size:12px;color:var(--ink-4);">Optare in afara: <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">tools.google.com/dlpage/gaoptout</a></span>
            </li>
            <li>
                <strong>Stripe (plati)</strong> — <a href="https://stripe.com/privacy" target="_blank" rel="noopener">stripe.com/privacy</a><br>
                <span style="font-size:12px;color:var(--ink-4);">Cookies Stripe sunt esentiale pentru procesarea platilor si nu pot fi dezactivate daca doresti sa efectuezi o plata.</span>
            </li>
            <li>
                <strong>Google Fonts</strong> — <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">policies.google.com/privacy</a><br>
                <span style="font-size:12px;color:var(--ink-4);">Fonturile sunt incarcate de pe serverele Google. Aceasta implica transmiterea adresei IP catre Google.</span>
            </li>
        </ul>
    </div>

    {{-- 6 --}}
    <div class="legal-section" id="consimtamant">
        <h2>6. Consimtamantul tau</h2>
        <p>La prima ta vizita pe Platforma, afisam un banner de cookies care iti permite sa:</p>
        <ul>
            <li><strong>Accepti toate cookies</strong> — inclusiv analytics si functionale;</li>
            <li><strong>Refuzi cookies optionale</strong> — vor fi plasate doar cookies esentiale;</li>
            <li><strong>Personalizezi alegerile</strong> — selectezi individual categoriile de cookies pe care le accepti.</li>
        </ul>
        <div class="legal-highlight">
            <p><strong>Consimtamantul tau este:</strong> liber, specific, informat si neambiguu. Nu conditionam accesul la Platforma de acceptarea cookies optionale. Poti reveni oricand asupra alegerii tale.</p>
        </div>
        <p>Consimtamantul acordat este stocat in cookie-ul <code>cookie_consent</code> pe o perioada de 1 an, dupa care vei fi intrebat din nou.</p>

        <h3>Retragerea consimtamantului</h3>
        <p>Iti poti retrage consimtamantul oricand, prin urmatoarele metode:</p>
        <ul>
            <li>Accesand bannerul de cookies (disponibil in footer-ul site-ului, butonul „Setari cookies");</li>
            <li>Stergand cookies din setarile browserului tau;</li>
            <li>Contactandu-ne la <a href="mailto:contact@novin.ro">contact@novin.ro</a>.</li>
        </ul>
        <p>Retragerea consimtamantului nu afecteaza legalitatea prelucrarii efectuate pe baza consimtamantului anterior retragerii.</p>
    </div>

    {{-- 7 --}}
    <div class="legal-section" id="gestionare">
        <h2>7. Cum iti gestionezi cookies</h2>
        <p>Pe langa bannerul nostru de cookies, poti gestiona sau sterge cookies direct din setarile browserului tau. Iata ghiduri rapide pentru cele mai populare browsere:</p>

        <div class="browser-grid">
            <div class="browser-item">
                <strong>Google Chrome</strong>
                <a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener">Ghid Chrome</a>
            </div>
            <div class="browser-item">
                <strong>Mozilla Firefox</strong>
                <a href="https://support.mozilla.org/kb/enhanced-tracking-protection-firefox-desktop" target="_blank" rel="noopener">Ghid Firefox</a>
            </div>
            <div class="browser-item">
                <strong>Safari</strong>
                <a href="https://support.apple.com/guide/safari/manage-cookies-sfri11471/mac" target="_blank" rel="noopener">Ghid Safari</a>
            </div>
            <div class="browser-item">
                <strong>Microsoft Edge</strong>
                <a href="https://support.microsoft.com/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener">Ghid Edge</a>
            </div>
            <div class="browser-item">
                <strong>Opera</strong>
                <a href="https://help.opera.com/latest/web-preferences/" target="_blank" rel="noopener">Ghid Opera</a>
            </div>
            <div class="browser-item">
                <strong>iOS Safari</strong>
                <a href="https://support.apple.com/HT201265" target="_blank" rel="noopener">Ghid iOS</a>
            </div>
        </div>

        <p>Poti de asemenea folosi <a href="https://www.youronlinechoices.eu/" target="_blank" rel="noopener">youronlinechoices.eu</a> pentru a gestiona preferintele de publicitate comportamentala ale companiilor membre IAB Europe.</p>

        <div class="legal-warn">
            <p><strong>Atentie:</strong> Stergerea sau blocarea tuturor cookies poate afecta functionarea Platformei. In special, blocarea cookies esentiale va impiedica autentificarea si utilizarea serviciilor.</p>
        </div>
    </div>

    {{-- 8 --}}
    <div class="legal-section" id="consecinte">
        <h2>8. Consecintele dezactivarii cookies</h2>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Categorie dezactivata</th>
                    <th>Impact asupra utilizarii</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Cookies esentiale</strong></td>
                    <td>Nu te poti autentifica. Platforma nu functioneaza corect. Platile nu pot fi procesate. <strong>Nu recomandam dezactivarea.</strong></td>
                </tr>
                <tr>
                    <td><strong>Cookies functionale</strong></td>
                    <td>Preferintele tale nu sunt salvate. Va trebui sa resetezi setarile la fiecare vizita. Impact minor asupra functionalitatii.</td>
                </tr>
                <tr>
                    <td><strong>Cookies analytics</strong></td>
                    <td>Nu putem masura utilizarea Platformei. Nu exista impact direct asupra experientei tale. Serviciul functioneaza normal.</td>
                </tr>
                <tr>
                    <td><strong>Cookies marketing</strong></td>
                    <td>Nu aplicabil — nu folosim cookies de marketing in prezent.</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- 9 --}}
    <div class="legal-section" id="modificari">
        <h2>9. Modificarea politicii</h2>
        <p>Putem actualiza aceasta Politica de Cookies pentru a reflecta modificari ale tehnologiilor utilizate, ale legislatiei sau ale practicilor noastre. In cazul modificarilor substantiale:</p>
        <ul>
            <li>Vom actualiza data „Ultima actualizare" din antetul paginii;</li>
            <li>Vom afisa un banner de notificare pe Platforma;</li>
            <li>Te vom solicita sa iti reconfirmi preferintele de cookies, daca este cazul.</li>
        </ul>
        <p>Te incurajam sa verifici periodic aceasta pagina pentru a fi la curent cu eventualele modificari.</p>
    </div>

    {{-- 10 --}}
    <div class="legal-section" id="contact">
        <h2>10. Contact</h2>
        <p>Pentru orice intrebari legate de utilizarea cookies pe Platforma noastra sau pentru exercitarea drepturilor tale privind datele personale colectate prin cookies, ne poti contacta:</p>
        <div class="legal-box">
            <p>
                <strong>VOID SFT GAMES SRL — NOVIN.RO</strong><br><br>
                Email: <a href="mailto:contact@novin.ro">contact@novin.ro</a><br>
                Telefon: <a href="tel:+40750456096">0750 456 096</a><br>
                Program: Luni — Vineri, 9:00 — 17:00<br><br>
                Pentru drepturi GDPR legate de cookies, consulta si <a href="/politica-de-confidentialitate">Politica de Confidentialitate</a>.
            </p>
        </div>
        <p style="margin-top:24px;font-size:13px;color:var(--ink-4);">Aceasta Politica de Cookies a fost redactata in conformitate cu Directiva 2002/58/CE (ePrivacy), Legea nr. 506/2004 si Regulamentul (UE) 2016/679 (GDPR), cu respectarea ghidurilor emise de ANSPDCP.</p>
    </div>

</div>
@endsection