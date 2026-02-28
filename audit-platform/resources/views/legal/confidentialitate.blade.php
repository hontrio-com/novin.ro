@extends('layouts.app')

@section('title', 'Politica de Confidentialitate — NOVIN.RO')
@section('meta_description', 'Politica de confidentialitate NOVIN.RO. Cum colectam, folosim si protejam datele tale personale conform GDPR si legislatiei romane.')

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

.rights-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:20px 0;}
.right-card{background:var(--paper-2);border:1px solid var(--rule);border-radius:10px;padding:18px 20px;}
.right-card h4{font-size:13px;font-weight:700;color:var(--ink);margin-bottom:6px;}
.right-card p{font-size:12px;color:var(--ink-4);line-height:1.6;margin:0;}
@media(max-width:600px){.rights-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
<div class="legal-hero">
    <div class="legal-hero-label">Document legal</div>
    <h1>Politica de Confidentialitate</h1>
    <p class="legal-hero-meta">Ultima actualizare: {{ date('d.m.Y') }} &nbsp;&bull;&nbsp; Versiunea 1.0 &nbsp;&bull;&nbsp; Conform GDPR (UE) 2016/679</p>
</div>

<div class="legal-wrap">

    <div class="legal-highlight" style="margin-bottom:40px;">
        <p><strong>Rezumat:</strong> Colectam doar datele strict necesare pentru functionarea serviciului. Nu vindem datele tale. Nu le impartasim cu terti in scop publicitar. Ai control deplin asupra datelor tale si le poti sterge oricand.</p>
    </div>

    {{-- TOC --}}
    <div class="legal-toc">
        <h2>Cuprins</h2>
        <ol>
            <li><a href="#operator">1. Cine suntem — Operatorul de date</a></li>
            <li><a href="#date-colectate">2. Ce date colectam</a></li>
            <li><a href="#scopuri">3. De ce colectam datele — Scopuri si temeiuri legale</a></li>
            <li><a href="#stocare">4. Cat timp pastram datele</a></li>
            <li><a href="#terti">5. Cu cine impartasim datele</a></li>
            <li><a href="#transfer">6. Transferuri internationale de date</a></li>
            <li><a href="#drepturi">7. Drepturile tale conform GDPR</a></li>
            <li><a href="#securitate">8. Securitatea datelor</a></li>
            <li><a href="#minori">9. Protectia minorilor</a></li>
            <li><a href="#cookies">10. Cookies si tehnologii similare</a></li>
            <li><a href="#modificari">11. Modificarea politicii</a></li>
            <li><a href="#contact-dpo">12. Contact si DPO</a></li>
        </ol>
    </div>

    {{-- 1 --}}
    <div class="legal-section" id="operator">
        <h2>1. Cine suntem — Operatorul de date</h2>
        <p>In conformitate cu Regulamentul (UE) 2016/679 privind protectia datelor cu caracter personal (GDPR), operatorul de date esti:</p>
        <div class="legal-box">
            <p>
                <strong>VOID SFT GAMES SRL</strong><br>
                CUI: 43474393 &nbsp;|&nbsp; J40/12345/2022<br>
                Romania<br><br>
                Email: <a href="mailto:contact@novin.ro">contact@novin.ro</a><br>
                Telefon: <a href="tel:+40750456096">0750 456 096</a><br>
                Website: <a href="https://novin.ro">novin.ro</a>
            </p>
        </div>
        <p>Platforma NOVIN.RO este operata de VOID SFT GAMES SRL in parteneriat cu <a href="https://inovex.ro" target="_blank">Inovex.ro</a>.</p>
        <p>Avem obligatia legala de a prelucra datele tale personale conform GDPR, Legii 190/2018 (legea nationala de implementare GDPR) si altor reglementari aplicabile.</p>
    </div>

    {{-- 2 --}}
    <div class="legal-section" id="date-colectate">
        <h2>2. Ce date colectam</h2>

        <h3>2.1 Date furnizate direct de tine</h3>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Categorie</th>
                    <th>Date colectate</th>
                    <th>Cand</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Cont</strong></td>
                    <td>Adresa de email, parola (stocata criptat)</td>
                    <td>La inregistrare</td>
                </tr>
                <tr>
                    <td><strong>Audit</strong></td>
                    <td>URL-ul website-ului auditat, adresa de email pentru raport</td>
                    <td>La initierea unui audit</td>
                </tr>
                <tr>
                    <td><strong>Plata</strong></td>
                    <td>Email, suma platita, ID tranzactie Stripe (NU stocam datele cardului)</td>
                    <td>La achizitionarea unui serviciu</td>
                </tr>
                <tr>
                    <td><strong>Comunicare</strong></td>
                    <td>Continutul mesajelor trimise catre noi</td>
                    <td>La contactarea suportului</td>
                </tr>
            </tbody>
        </table>

        <h3>2.2 Date colectate automat</h3>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Categorie</th>
                    <th>Date colectate</th>
                    <th>Scop</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Date tehnice</strong></td>
                    <td>Adresa IP, tipul browserului, sistemul de operare, rezolutia ecranului</td>
                    <td>Securitate, diagnosticare erori</td>
                </tr>
                <tr>
                    <td><strong>Date de utilizare</strong></td>
                    <td>Paginile vizitate, timpul petrecut, actiunile efectuate</td>
                    <td>Imbunatatirea serviciului</td>
                </tr>
                <tr>
                    <td><strong>Cookies</strong></td>
                    <td>Cookie-uri sesiune, preferinte, analytics</td>
                    <td>Functionare, analiza trafic</td>
                </tr>
                <tr>
                    <td><strong>Loguri</strong></td>
                    <td>Erori tehnice, timestamp-uri actiuni</td>
                    <td>Depanare tehnica</td>
                </tr>
            </tbody>
        </table>

        <h3>2.3 Date despre website-urile auditate</h3>
        <p>In procesul de audit, Platforma acceseaza si analizeaza continutul public al website-ului furnizat de tine (HTML, meta tags, viteza de incarcare, etc.). <strong>Nu colectam date personale ale vizitatorilor website-ului tau.</strong> Datele tehnice rezultate din audit sunt stocate in Raport si asociate Contului tau.</p>
    </div>

    {{-- 3 --}}
    <div class="legal-section" id="scopuri">
        <h2>3. De ce colectam datele — Scopuri si temeiuri legale</h2>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Scop</th>
                    <th>Datele folosite</th>
                    <th>Temei legal (GDPR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Furnizarea serviciului de audit</td>
                    <td>Email, URL website, date tehnice</td>
                    <td>Art. 6(1)(b) — Executarea contractului</td>
                </tr>
                <tr>
                    <td>Procesarea platilor</td>
                    <td>Email, ID tranzactie</td>
                    <td>Art. 6(1)(b) — Executarea contractului</td>
                </tr>
                <tr>
                    <td>Trimiterea raportului si notificari</td>
                    <td>Email</td>
                    <td>Art. 6(1)(b) — Executarea contractului</td>
                </tr>
                <tr>
                    <td>Obligatii legale (facturare, fiscale)</td>
                    <td>Date tranzactii, email</td>
                    <td>Art. 6(1)(c) — Obligatie legala</td>
                </tr>
                <tr>
                    <td>Securitate si prevenirea fraudei</td>
                    <td>IP, date tehnice, loguri</td>
                    <td>Art. 6(1)(f) — Interes legitim</td>
                </tr>
                <tr>
                    <td>Imbunatatirea serviciului</td>
                    <td>Date anonimizate de utilizare</td>
                    <td>Art. 6(1)(f) — Interes legitim</td>
                </tr>
                <tr>
                    <td>Marketing direct (newsletter)</td>
                    <td>Email</td>
                    <td>Art. 6(1)(a) — Consimtamant explicit</td>
                </tr>
                <tr>
                    <td>Cookies analytics</td>
                    <td>Date comportament</td>
                    <td>Art. 6(1)(a) — Consimtamant explicit</td>
                </tr>
            </tbody>
        </table>
        <div class="legal-highlight">
            <p><strong>Nu folosim datele tale</strong> pentru profilare automatizata cu efecte juridice, publicitate comportamentala catre terti sau vanzarea catre alte companii.</p>
        </div>
    </div>

    {{-- 4 --}}
    <div class="legal-section" id="stocare">
        <h2>4. Cat timp pastram datele</h2>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Categorie date</th>
                    <th>Perioada de retentie</th>
                    <th>Motivul</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Date cont (email, parola)</td>
                    <td>Pe durata existentei contului + 30 zile dupa stergere</td>
                    <td>Furnizarea serviciului</td>
                </tr>
                <tr>
                    <td>Rapoarte de audit</td>
                    <td>2 ani de la generare sau pana la stergerea contului</td>
                    <td>Accesul la istoricul auditurilor</td>
                </tr>
                <tr>
                    <td>Date tranzactii / facturi</td>
                    <td>10 ani</td>
                    <td>Obligatie legala fiscala (Legea 82/1991)</td>
                </tr>
                <tr>
                    <td>Loguri tehnice / securitate</td>
                    <td>90 de zile</td>
                    <td>Depanare si securitate</td>
                </tr>
                <tr>
                    <td>Date marketing (email newsletter)</td>
                    <td>Pana la retragerea consimtamantului</td>
                    <td>Consimtamant</td>
                </tr>
                <tr>
                    <td>Cookies analytics</td>
                    <td>Conform politicii cookies (max. 13 luni)</td>
                    <td>Consimtamant</td>
                </tr>
            </tbody>
        </table>
        <p>La expirarea perioadei de retentie, datele sunt sterse definitiv sau anonimizate in mod ireversibil.</p>
    </div>

    {{-- 5 --}}
    <div class="legal-section" id="terti">
        <h2>5. Cu cine impartasim datele</h2>
        <p>Nu vindem si nu inchiriem datele tale personale. Impartasim date doar in urmatoarele situatii limitate:</p>

        <h3>5.1 Furnizori de servicii (imputerniciti)</h3>
        <table class="legal-table">
            <thead>
                <tr>
                    <th>Furnizor</th>
                    <th>Serviciu</th>
                    <th>Date transmise</th>
                    <th>Locatie</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Stripe Inc.</strong></td>
                    <td>Procesare plati</td>
                    <td>Email, suma, IP</td>
                    <td>SUA (clauze standard UE)</td>
                </tr>
                <tr>
                    <td><strong>Google (PageSpeed API)</strong></td>
                    <td>Analiza viteza website</td>
                    <td>URL-ul auditat</td>
                    <td>SUA (clauze standard UE)</td>
                </tr>
                <tr>
                    <td><strong>Anthropic / AI Provider</strong></td>
                    <td>Analiza AI a continutului</td>
                    <td>Continut public al site-ului auditat</td>
                    <td>SUA (clauze standard UE)</td>
                </tr>
                <tr>
                    <td><strong>Furnizor hosting</strong></td>
                    <td>Infrastructura server</td>
                    <td>Toate datele stocate</td>
                    <td>UE / Romania</td>
                </tr>
                <tr>
                    <td><strong>Serviciu email tranzactional</strong></td>
                    <td>Trimitere email-uri</td>
                    <td>Adresa email, continut email</td>
                    <td>UE</td>
                </tr>
            </tbody>
        </table>
        <p>Toti furnizorii sunt obligati contractual sa respecte GDPR si sa foloseasca datele exclusiv in scopul prestarii serviciului catre noi.</p>

        <h3>5.2 Autoritati legale</h3>
        <p>Putem divulga date catre autoritati publice (DIICOT, Politie, ANAF, instantele judecatoresti) exclusiv in baza unei obligatii legale, hotarari judecatoresti sau cereri legitime ale autoritatilor competente.</p>

        <h3>5.3 Transfer in caz de reorganizare</h3>
        <p>In cazul unei fuziuni, achizitii sau vanzari de active, datele tale pot fi transferate catre noul operator, care va fi obligat sa respecte prezenta Politica de Confidentialitate.</p>
    </div>

    {{-- 6 --}}
    <div class="legal-section" id="transfer">
        <h2>6. Transferuri internationale de date</h2>
        <p>Unii furnizori ai nostri (Stripe, Google, Anthropic) sunt localizati in Statele Unite ale Americii. Transferul datelor catre acestia se realizeaza cu garantii adecvate, conform art. 46 GDPR, prin:</p>
        <ul>
            <li><strong>Clauze contractuale standard (SCC)</strong> adoptate de Comisia Europeana;</li>
            <li><strong>Certificari adecvate</strong> recunoscute de autoritatile europene de protectie a datelor;</li>
            <li><strong>Masuri tehnice suplimentare</strong> (criptare in transit si la repaus).</li>
        </ul>
        <p>Poti obtine mai multe informatii despre mecanismele de transfer utilizate contactandu-ne la <a href="mailto:contact@novin.ro">contact@novin.ro</a>.</p>
    </div>

    {{-- 7 --}}
    <div class="legal-section" id="drepturi">
        <h2>7. Drepturile tale conform GDPR</h2>
        <p>In conformitate cu GDPR (art. 15-22), ai urmatoarele drepturi cu privire la datele tale personale:</p>

        <div class="rights-grid">
            <div class="right-card">
                <h4>Dreptul de acces (art. 15)</h4>
                <p>Poti solicita o copie a tuturor datelor personale pe care le detinem despre tine.</p>
            </div>
            <div class="right-card">
                <h4>Dreptul la rectificare (art. 16)</h4>
                <p>Poti corecta datele incorecte sau incomplete din contul tau.</p>
            </div>
            <div class="right-card">
                <h4>Dreptul la stergere (art. 17)</h4>
                <p>Poti solicita stergerea datelor tale («dreptul de a fi uitat»), sub rezerva obligatiilor legale de retentie.</p>
            </div>
            <div class="right-card">
                <h4>Dreptul la restrictionare (art. 18)</h4>
                <p>Poti solicita restrictionarea prelucrarii datelor tale in anumite circumstante.</p>
            </div>
            <div class="right-card">
                <h4>Dreptul la portabilitate (art. 20)</h4>
                <p>Poti primi datele tale intr-un format structurat, lizibil de masina (JSON/CSV).</p>
            </div>
            <div class="right-card">
                <h4>Dreptul la opozitie (art. 21)</h4>
                <p>Te poti opune prelucrarii datelor in scop de marketing sau in baza interesului legitim.</p>
            </div>
            <div class="right-card">
                <h4>Retragerea consimtamantului</h4>
                <p>Poti retrage oricand consimtamantul pentru prelucrari bazate pe acesta (marketing, cookies analytics).</p>
            </div>
            <div class="right-card">
                <h4>Dreptul de a depune plangere</h4>
                <p>Poti depune plangere la ANSPDCP sau la autoritatea de supraveghere din statul tau de rezidenta.</p>
            </div>
        </div>

        <h3>Cum iti exerciti drepturile</h3>
        <p>Trimite o cerere scrisa la <a href="mailto:contact@novin.ro">contact@novin.ro</a> cu subiectul <strong>„Cerere GDPR"</strong>. Vom raspunde in termen de <strong>30 de zile calendaristice</strong> (cu posibilitatea de prelungire la 60 de zile in cazuri complexe, cu notificarea ta).</p>
        <p>Cererea trebuie sa contina: numele complet, adresa de email asociata contului si descrierea dreptului pe care doresti sa il exerciti. Putem solicita verificarea identitatii pentru a proteja datele tale.</p>

        <h3>Autoritatea Nationala de Supraveghere (ANSPDCP)</h3>
        <div class="legal-box">
            <p>
                <strong>ANSPDCP — Autoritatea Nationala de Supraveghere a Prelucrarii Datelor cu Caracter Personal</strong><br>
                B-dul G-ral. Gheorghe Magheru nr. 28-30, Sector 1, Bucuresti<br>
                Email: <a href="mailto:anspdcp@dataprotection.ro">anspdcp@dataprotection.ro</a><br>
                Telefon: +40 318 059 211<br>
                Website: <a href="https://www.dataprotection.ro" target="_blank">dataprotection.ro</a>
            </p>
        </div>
    </div>

    {{-- 8 --}}
    <div class="legal-section" id="securitate">
        <h2>8. Securitatea datelor</h2>
        <p>Implementam masuri tehnice si organizatorice adecvate pentru protejarea datelor tale, incluzand:</p>
        <ul>
            <li><strong>Criptare SSL/TLS</strong> pentru toate comunicatiile intre browser si server;</li>
            <li><strong>Criptare parole</strong> prin algoritmi bcrypt — nu cunoastem parola ta in text clar;</li>
            <li><strong>Tokenizare plati</strong> — nu stocam datele cardului, acestea sunt gestionate exclusiv de Stripe (certificat PCI-DSS Level 1);</li>
            <li><strong>Acces restrictionat</strong> la date — doar personalul autorizat are acces la datele cu caracter personal;</li>
            <li><strong>Backup-uri regulate</strong> cu stocare securizata;</li>
            <li><strong>Monitorizare securitate</strong> si detectarea accesurilor neautorizate.</li>
        </ul>
        <div class="legal-warn">
            <p><strong>Notificare incidente:</strong> In cazul unui incident de securitate care afecteaza datele tale, te vom notifica in termen de 72 de ore de la descoperire, conform art. 33-34 GDPR, daca incidentul prezinta un risc ridicat pentru drepturile si libertatile tale.</p>
        </div>
    </div>

    {{-- 9 --}}
    <div class="legal-section" id="minori">
        <h2>9. Protectia minorilor</h2>
        <p>Serviciile NOVIN.RO sunt destinate exclusiv persoanelor cu varsta de minimum <strong>18 ani</strong>. Nu colectam intentionat date personale de la minori.</p>
        <p>Daca ai informatii ca un minor a furnizat date personale pe Platforma noastra, te rugam sa ne contactezi la <a href="mailto:contact@novin.ro">contact@novin.ro</a>, iar vom sterge imediat aceste date.</p>
    </div>

    {{-- 10 --}}
    <div class="legal-section" id="cookies">
        <h2>10. Cookies si tehnologii similare</h2>
        <p>Folosim cookies si tehnologii similare pentru functionarea Platformei si analiza utilizarii. Detalii complete gasesti in <a href="/politica-cookies">Politica noastra de Cookies</a>.</p>
        <p>Pe scurt, folosim:</p>
        <ul>
            <li><strong>Cookies esentiale</strong> — necesare pentru autentificare si securitate (nu necesita consimtamant);</li>
            <li><strong>Cookies analytics</strong> — pentru intelegerea modului de utilizare a Platformei (necesita consimtamantul tau);</li>
            <li><strong>Cookies preferinte</strong> — pentru memorarea setarilor tale (necesita consimtamantul tau).</li>
        </ul>
        <p>Iti poti gestiona preferintele de cookies din bannerul afisat la prima vizita sau din setarile browserului tau.</p>
    </div>

    {{-- 11 --}}
    <div class="legal-section" id="modificari">
        <h2>11. Modificarea politicii</h2>
        <p>Putem actualiza aceasta Politica de Confidentialitate periodic, pentru a reflecta modificari ale practicilor noastre sau ale legislatiei aplicabile. In cazul modificarilor substantiale:</p>
        <ul>
            <li>Vom afisa un banner de notificare pe Platforma;</li>
            <li>Vom trimite un email de informare la adresa asociata Contului tau;</li>
            <li>Vom actualiza data „Ultima actualizare" din antetul documentului.</li>
        </ul>
        <p>Te incurajam sa verifici periodic aceasta pagina. Continuarea utilizarii Platformei dupa publicarea modificarilor constituie acceptarea acestora.</p>
    </div>

    {{-- 12 --}}
    <div class="legal-section" id="contact-dpo">
        <h2>12. Contact si DPO</h2>
        <p>Pentru orice intrebari sau solicitari legate de prelucrarea datelor tale personale, ne poti contacta:</p>
        <div class="legal-box">
            <p>
                <strong>Responsabil cu Protectia Datelor (DPO)</strong><br>
                VOID SFT GAMES SRL<br><br>
                Email: <a href="mailto:contact@novin.ro">contact@novin.ro</a> (subiect: „GDPR / Date personale")<br>
                Telefon: <a href="tel:+40750456096">0750 456 096</a><br>
                Adresa: Romania<br><br>
                Program raspuns: Luni — Vineri, 9:00 — 17:00<br>
                Termen maxim de raspuns: 30 de zile calendaristice
            </p>
        </div>
        <p style="margin-top:24px;font-size:13px;color:var(--ink-4);">Aceasta Politica de Confidentialitate a fost redactata in conformitate cu Regulamentul (UE) 2016/679 (GDPR), Legea nr. 190/2018 privind masuri de implementare a GDPR in Romania, si alte reglementari aplicabile privind protectia datelor cu caracter personal.</p>
    </div>

</div>
@endsection