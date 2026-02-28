@extends('layouts.app')

@section('title', 'Termeni si Conditii — NOVIN.RO')
@section('meta_description', 'Termenii si conditiile de utilizare ale platformei NOVIN.RO. Informatii despre servicii, plati, drepturi si obligatii.')

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

.legal-box{background:var(--paper-2);border:1px solid var(--rule);border-radius:8px;padding:18px 22px;margin:16px 0;}
.legal-box p{margin:0;font-size:13px;color:var(--ink-3);}

.legal-highlight{background:rgba(45,145,206,.06);border-left:3px solid var(--blue);border-radius:0 8px 8px 0;padding:14px 18px;margin:16px 0;}
.legal-highlight p{margin:0;font-size:13px;color:var(--ink-2);}

.legal-warn{background:rgba(239,68,68,.05);border-left:3px solid #ef4444;border-radius:0 8px 8px 0;padding:14px 18px;margin:16px 0;}
.legal-warn p{margin:0;font-size:13px;color:var(--ink-2);}
</style>
@endpush

@section('content')
<div class="legal-hero">
    <div class="legal-hero-label">Document legal</div>
    <h1>Termeni si Conditii</h1>
    <p class="legal-hero-meta">Ultima actualizare: {{ date('d.m.Y') }} &nbsp;&bull;&nbsp; Versiunea 1.0</p>
</div>

<div class="legal-wrap">

    {{-- TOC --}}
    <div class="legal-toc">
        <h2>Cuprins</h2>
        <ol>
            <li><a href="#definitii">1. Definitii si interpretare</a></li>
            <li><a href="#servicii">2. Descrierea serviciilor</a></li>
            <li><a href="#acceptare">3. Acceptarea termenilor</a></li>
            <li><a href="#cont">4. Contul de utilizator</a></li>
            <li><a href="#plati">5. Preturi, plati si facturare</a></li>
            <li><a href="#retragere">6. Dreptul de retragere</a></li>
            <li><a href="#utilizare">7. Utilizarea permisa si interzisa</a></li>
            <li><a href="#proprietate">8. Proprietate intelectuala</a></li>
            <li><a href="#raspundere">9. Limitarea raspunderii</a></li>
            <li><a href="#disponibilitate">10. Disponibilitatea serviciului</a></li>
            <li><a href="#modificari">11. Modificarea termenilor</a></li>
            <li><a href="#incetare">12. Incetarea accesului</a></li>
            <li><a href="#litigii">13. Litigii si legislatie aplicabila</a></li>
            <li><a href="#anpc">14. Informare ANPC si SAL/SOL</a></li>
            <li><a href="#contact">15. Date de contact</a></li>
        </ol>
    </div>

    {{-- 1 --}}
    <div class="legal-section" id="definitii">
        <h2>1. Definitii si interpretare</h2>
        <p>In prezentul document, urmatorii termeni au semnificatiile de mai jos:</p>
        <ul>
            <li><strong>„Platforma" / „NOVIN.RO"</strong> — serviciul de audit website accesibil la adresa <strong>novin.ro</strong>, operat de VOID SFT GAMES SRL.</li>
            <li><strong>„Furnizorul"</strong> — VOID SFT GAMES SRL, persoana juridica romana, CUI 43474393, inregistrata la Registrul Comertului sub nr. J40/12345/2022, cu sediul social in Romania.</li>
            <li><strong>„Utilizatorul"</strong> — orice persoana fizica cu capacitate deplina de exercitiu sau persoana juridica care acceseaza si/sau foloseste Platforma.</li>
            <li><strong>„Clientul"</strong> — Utilizatorul care achizitioneaza un Serviciu contra cost.</li>
            <li><strong>„Serviciul"</strong> — analiza automata (audit) a unui website, realizata prin mijloace tehnice si AI, cu generarea unui Raport.</li>
            <li><strong>„Raportul"</strong> — documentul digital generat automat de Platforma in urma auditului, ce contine concluzii, scoruri si recomandari.</li>
            <li><strong>„Contul"</strong> — spatiul personal al Utilizatorului, creat prin inregistrare pe Platforma.</li>
            <li><strong>„Datele personale"</strong> — informatii definite conform Regulamentului (UE) 2016/679 (GDPR).</li>
        </ul>
    </div>

    {{-- 2 --}}
    <div class="legal-section" id="servicii">
        <h2>2. Descrierea serviciilor</h2>
        <p>NOVIN.RO ofera servicii de audit automat pentru website-uri, incluzand analiza urmatoarelor aspecte:</p>
        <ul>
            <li><strong>SEO tehnic</strong> — structura paginilor, meta tags, canonical, sitemap, robots.txt</li>
            <li><strong>Viteza si performanta</strong> — Core Web Vitals (LCP, CLS, INP), PageSpeed Score, TTFB</li>
            <li><strong>Conformitate legala</strong> — prezenta informatiilor ANPC, GDPR, politica cookies, termeni si conditii</li>
            <li><strong>Securitate</strong> — certificate SSL, security headers (HSTS, CSP, X-Frame-Options)</li>
            <li><strong>Experienta utilizatorilor (UX)</strong> — compatibilitate mobile, accesibilitate, elemente de contact</li>
            <li><strong>E-E-A-T si continut</strong> — analiza autoritatii, expertizei si credibilitatii</li>
        </ul>
        <div class="legal-highlight">
            <p><strong>Important:</strong> Raportul generat de NOVIN.RO are caracter informativ si orientativ. Concluziile sunt produse automat prin algoritmi si AI si nu constituie consultanta juridica, fiscala sau tehnica profesionala. Utilizatorul este responsabil pentru deciziile luate pe baza Raportului.</p>
        </div>
        <p>Furnizorul isi rezerva dreptul de a modifica, suspenda sau intrerupe orice componenta a Serviciului, cu notificarea prealabila a Utilizatorilor inregistrati.</p>
    </div>

    {{-- 3 --}}
    <div class="legal-section" id="acceptare">
        <h2>3. Acceptarea termenilor</h2>
        <p>Prin accesarea Platformei, crearea unui Cont sau achizitionarea unui Serviciu, Utilizatorul declara ca:</p>
        <ul>
            <li>A citit, inteles si acceptat in integralitate prezentii Termeni si Conditii;</li>
            <li>A citit si acceptat <a href="/politica-de-confidentialitate">Politica de Confidentialitate</a> si <a href="/politica-cookies">Politica Cookies</a>;</li>
            <li>Are cel putin 18 ani sau, daca actioneaza in numele unei persoane juridice, are autoritatea legala de a angaja acea entitate;</li>
            <li>Informatiile furnizate sunt corecte, complete si actualizate.</li>
        </ul>
        <p>Daca nu esti de acord cu acesti termeni, te rugam sa nu utilizezi Platforma.</p>
    </div>

    {{-- 4 --}}
    <div class="legal-section" id="cont">
        <h2>4. Contul de utilizator</h2>
        <h3>4.1 Crearea contului</h3>
        <p>Inregistrarea pe Platforma necesita furnizarea unei adrese de email valide si a unei parole. Utilizatorul este responsabil pentru pastrarea confidentialitatii credentialelor de acces.</p>

        <h3>4.2 Obligatiile Utilizatorului</h3>
        <ul>
            <li>Sa nu impartaseasca datele de acces cu terte persoane;</li>
            <li>Sa notifice imediat Furnizorul in cazul unui acces neautorizat la Cont;</li>
            <li>Sa mentina datele din profil actualizate si corecte;</li>
            <li>Sa nu creeze conturi multiple in scop de frauda sau eludare a restrictiilor.</li>
        </ul>

        <h3>4.3 Stergerea contului</h3>
        <p>Utilizatorul poate solicita stergerea Contului oricand, prin contactarea Furnizorului la <a href="mailto:contact@novin.ro">contact@novin.ro</a>. Stergerea contului determina pierderea accesului la istoricul auditurilor si la Rapoartele generate anterior.</p>
    </div>

    {{-- 5 --}}
    <div class="legal-section" id="plati">
        <h2>5. Preturi, plati si facturare</h2>
        <h3>5.1 Preturi</h3>
        <p>Preturile Serviciilor sunt afisate in mod transparent pe Platforma, in lei (RON), si includ TVA conform legislatiei romane in vigoare. Furnizorul isi rezerva dreptul de a modifica preturile, cu publicarea noilor tarife pe Platforma cu cel putin 30 de zile inainte de intrarea in vigoare.</p>

        <h3>5.2 Modalitati de plata</h3>
        <p>Platforma accepta plati online prin card bancar (Visa, Mastercard), procesate securizat prin <strong>Stripe</strong>, un procesator de plati certificat PCI-DSS. Furnizorul nu stocheaza datele cardului bancar al Clientului.</p>

        <h3>5.3 Facturare</h3>
        <p>Dupa efectuarea platii, Clientul va primi prin email o confirmare a tranzactiei. Factura fiscala va fi emisa in conformitate cu prevederile Codului Fiscal roman si transmisa la adresa de email asociata Contului.</p>

        <div class="legal-box">
            <p><strong>Informatii facturare:</strong> VOID SFT GAMES SRL, CUI 43474393, J40/12345/2022. Pentru solicitari de facturare pe firma, contactati contact@novin.ro inainte de efectuarea platii.</p>
        </div>

        <h3>5.4 Livrarea serviciului</h3>
        <p>Serviciul de audit este livrat in format digital, prin intermediul Platformei, in maximum 5 minute de la confirmarea platii. Raportul este accesibil in Contul Utilizatorului si poate fi descarcat in format PDF.</p>
    </div>

    {{-- 6 --}}
    <div class="legal-section" id="retragere">
        <h2>6. Dreptul de retragere</h2>
        <div class="legal-highlight">
            <p><strong>Conform OUG 34/2014</strong> (transpunerea Directivei 2011/83/UE privind drepturile consumatorilor), consumatorii beneficiaza de dreptul de retragere dintr-un contract la distanta in termen de <strong>14 zile calendaristice</strong>.</p>
        </div>
        <p>Prin acceptarea prezentilor Termeni si solicitarea expresa a livrarii imediate a Serviciului, Clientul <strong>isi exprima acordul expres</strong> ca Serviciul sa inceapa inainte de expirarea perioadei de retragere si <strong>intelege ca va pierde dreptul de retragere</strong> odata ce Serviciul a fost executat integral (raportul a fost generat si pus la dispozitie).</p>
        <p>In cazul in care auditul nu a fost efectuat (eroare tehnica, serviciu nefunctionabil), Clientul are dreptul la rambursarea integrala a sumei platite, prin contactarea noastra la <a href="mailto:contact@novin.ro">contact@novin.ro</a>.</p>
        <p>Cererile de rambursare vor fi procesate in termen de <strong>14 zile lucratoare</strong> de la confirmarea eligibilitatii, prin aceeasi modalitate de plata folosita la achizitie.</p>
    </div>

    {{-- 7 --}}
    <div class="legal-section" id="utilizare">
        <h2>7. Utilizarea permisa si interzisa</h2>
        <h3>7.1 Utilizare permisa</h3>
        <p>Serviciile NOVIN.RO pot fi folosite exclusiv in scopuri legale si legitime, incluzand:</p>
        <ul>
            <li>Evaluarea performantei propriului website;</li>
            <li>Identificarea problemelor tehnice si de conformitate;</li>
            <li>Generarea de rapoarte pentru clientii proprii (agentii, freelanceri);</li>
            <li>Cercetare si analiza in domeniul optimizarii web.</li>
        </ul>

        <h3>7.2 Utilizare interzisa</h3>
        <div class="legal-warn">
            <p><strong>Este strict interzisa utilizarea Platformei pentru:</strong></p>
        </div>
        <ul>
            <li>Auditarea website-urilor fara acordul proprietarului acestora;</li>
            <li>Activitati de tip scraping, crawling masiv sau suprasolicitarea infrastructurii;</li>
            <li>Circumventarea masurilor de securitate ale Platformei;</li>
            <li>Crearea de conturi false sau utilizarea identitatii altei persoane;</li>
            <li>Orice activitate care incalca legislatia romana sau europeana in vigoare;</li>
            <li>Revanzarea Serviciului fara acordul scris prealabil al Furnizorului.</li>
        </ul>
        <p>Incalcarea acestor restrictii poate duce la suspendarea imediata a Contului si, dupa caz, la actiuni legale.</p>
    </div>

    {{-- 8 --}}
    <div class="legal-section" id="proprietate">
        <h2>8. Proprietate intelectuala</h2>
        <p>Toate elementele Platformei NOVIN.RO — inclusiv, dar fara a se limita la: cod sursa, design, logo-uri, texte, algoritmi, metodologie de audit — sunt proprietatea exclusiva a VOID SFT GAMES SRL si sunt protejate prin legislatia romana si europeana privind drepturile de autor si proprietatea intelectuala.</p>
        <p>Raportul de audit generat este pus la dispozitia Clientului sub o licenta limitata, neexclusiva, netransferabila, exclusiv pentru uzul intern al acestuia. Este interzisa reproducerea, distribuirea sau publicarea Raportului fara mentionarea sursei (NOVIN.RO).</p>
        <p>Utilizatorul garanteaza ca detine drepturile necesare asupra website-ului supus auditului si ca furnizarea URL-ului catre Platforma nu incalca drepturile unor terte parti.</p>
    </div>

    {{-- 9 --}}
    <div class="legal-section" id="raspundere">
        <h2>9. Limitarea raspunderii</h2>
        <p>In masura maxima permisa de legislatia aplicabila, Furnizorul nu este raspunzator pentru:</p>
        <ul>
            <li>Inexactitati sau erori in Raportul generat automat, avand in vedere natura automatizata a Serviciului;</li>
            <li>Decizii de afaceri luate de Utilizator pe baza Raportului;</li>
            <li>Pierderi indirecte, incidentale sau consecvente, inclusiv pierderea de profit sau date;</li>
            <li>Intreruperi temporare ale Serviciului cauzate de factori tehnici, mentenanta sau forte majore;</li>
            <li>Continutul website-urilor auditate sau consecintele auditarii acestora.</li>
        </ul>
        <p>Raspunderea totala a Furnizorului fata de un Client, indiferent de natura pretentiei, nu poate depasi suma platita de acel Client pentru Serviciul in cauza.</p>
        <div class="legal-highlight">
            <p>Aceasta limitare nu afecteaza drepturile consumatorilor prevazute de legislatia romana si europeana, in special OUG 34/2014 si Legea 449/2003 privind vanzarea produselor.</p>
        </div>
    </div>

    {{-- 10 --}}
    <div class="legal-section" id="disponibilitate">
        <h2>10. Disponibilitatea serviciului</h2>
        <p>Furnizorul depune eforturi rezonabile pentru a asigura disponibilitatea Platformei 24/7, dar nu garanteaza functionarea neintrerupta a acesteia. Pot aparea intreruperi planificate (mentenanta) sau neplanificate (incidente tehnice).</p>
        <p>In cazul unor intreruperi planificate de durata mai mare de 2 ore, Furnizorul va notifica Utilizatorii inregistrati in avans, prin email sau prin mesaj afisat pe Platforma.</p>
        <p>Furnizorul nu este raspunzator pentru nefunctionarea sau performanta redusa a Serviciului cauzata de: conexiunea la internet a Utilizatorului, blocarea accesului de catre website-ul auditat, modificari in API-urile tertilor utilizate (Google PageSpeed, etc.).</p>
    </div>

    {{-- 11 --}}
    <div class="legal-section" id="modificari">
        <h2>11. Modificarea termenilor</h2>
        <p>Furnizorul isi rezerva dreptul de a modifica prezentii Termeni si Conditii oricand, cu respectarea urmatoarelor conditii:</p>
        <ul>
            <li>Modificarile substantiale vor fi comunicate Utilizatorilor inregistrati prin email cu cel putin <strong>15 zile</strong> inainte de intrarea in vigoare;</li>
            <li>Data ultimei actualizari va fi afisata in antetul documentului;</li>
            <li>Continuarea utilizarii Platformei dupa intrarea in vigoare a modificarilor constituie acceptarea acestora.</li>
        </ul>
        <p>In cazul in care nu esti de acord cu modificarile, ai dreptul sa iti inchizi Contul inainte de data intrarii in vigoare a acestora.</p>
    </div>

    {{-- 12 --}}
    <div class="legal-section" id="incetare">
        <h2>12. Incetarea accesului</h2>
        <p>Furnizorul poate suspenda sau inchide Contul unui Utilizator, cu sau fara notificare prealabila, in urmatoarele situatii:</p>
        <ul>
            <li>Incalcarea prezentilor Termeni si Conditii;</li>
            <li>Furnizarea de date false la inregistrare;</li>
            <li>Activitati frauduloase sau care prejudiciaza Platforma sau alti utilizatori;</li>
            <li>Solicitare din partea autoritatilor competente.</li>
        </ul>
        <p>In caz de suspendare nejustificata, Utilizatorul poate contesta decizia la <a href="mailto:contact@novin.ro">contact@novin.ro</a>. Serviciile achizitionate si nelivrate vor fi rambursate integral.</p>
    </div>

    {{-- 13 --}}
    <div class="legal-section" id="litigii">
        <h2>13. Litigii si legislatie aplicabila</h2>
        <p>Prezentii Termeni si Conditii sunt guvernati de <strong>legislatia romana</strong> in vigoare, incluzand dar fara a se limita la:</p>
        <ul>
            <li>Codul Civil roman (Legea 287/2009);</li>
            <li>OUG 34/2014 privind drepturile consumatorilor in contractele la distanta;</li>
            <li>Legea 365/2002 privind comertul electronic;</li>
            <li>Regulamentul (UE) 2016/679 (GDPR);</li>
            <li>Legea 506/2004 privind prelucrarea datelor personale in comunicatii electronice.</li>
        </ul>
        <p>Orice litigiu izvorat din sau in legatura cu prezentii Termeni va fi solutionat pe cale amiabila, in termen de 30 de zile de la notificarea scrisa. In caz de esec, litigiul va fi deferit instantelor judecatoresti competente din Romania.</p>
        <p>Consumatorii au dreptul de a recurge la proceduri alternative de solutionare a litigiilor (SAL/SOL), conform sectiunii urmatoare.</p>
    </div>

    {{-- 14 --}}
    <div class="legal-section" id="anpc">
        <h2>14. Informare ANPC si SAL/SOL</h2>
        <p>In conformitate cu OUG 20/2021 si Legea 257/2021 privind solutionarea alternativa a litigiilor, informam consumatorii cu privire la urmatoarele:</p>

        <h3>14.1 ANPC — Autoritatea Nationala pentru Protectia Consumatorilor</h3>
        <p>Consumatorii pot depune sesizari la ANPC prin:</p>
        <ul>
            <li>Platforma online: <a href="https://anpc.ro" target="_blank" rel="noopener">anpc.ro</a></li>
            <li>Telefon: <strong>0800 080 999</strong> (gratuit, luni-vineri 9:00-17:00)</li>
            <li>Email: <a href="mailto:office@anpc.ro">office@anpc.ro</a></li>
        </ul>

        <h3>14.2 SAL — Solutionarea Alternativa a Litigiilor</h3>
        <p>Consumatorii pot recurge la procedura SAL (medierea alternativa a litigiilor) prin:</p>
        <div class="legal-box">
            <p><a href="https://anpc.ro/ce-este-sal/" target="_blank" rel="noopener"><strong>anpc.ro/ce-este-sal/</strong></a> — Centrul SAL din cadrul ANPC</p>
        </div>

        <h3>14.3 SOL — Solutionarea Online a Litigiilor</h3>
        <p>Consumatorii din UE pot utiliza platforma europeana de solutionare online a litigiilor:</p>
        <div class="legal-box">
            <p><a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener"><strong>ec.europa.eu/consumers/odr</strong></a> — Platforma SOL a Comisiei Europene</p>
        </div>

        <p>Adresa noastra de email pentru litigii online: <a href="mailto:contact@novin.ro">contact@novin.ro</a></p>
    </div>

    {{-- 15 --}}
    <div class="legal-section" id="contact">
        <h2>15. Date de contact</h2>
        <div class="legal-box">
            <p>
                <strong>VOID SFT GAMES SRL</strong><br>
                CUI: 43474393 | J40/12345/2022<br>
                Romania<br><br>
                Email: <a href="mailto:contact@novin.ro">contact@novin.ro</a><br>
                Telefon: <a href="tel:+40750456096">0750 456 096</a><br>
                Website: <a href="https://novin.ro">novin.ro</a><br>
                Platforma operata de: <a href="https://inovex.ro" target="_blank">Inovex.ro</a>
            </p>
        </div>
        <p style="margin-top:24px;font-size:13px;color:var(--ink-4);">Pentru orice intrebari legate de acesti Termeni si Conditii, ne poti contacta la adresele de mai sus. Vom raspunde in termen de maximum 3 zile lucratoare.</p>
    </div>

</div>
@endsection