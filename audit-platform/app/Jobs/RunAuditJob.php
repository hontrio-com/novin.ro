<?php
namespace App\Jobs;

use App\Models\Audit;
use App\Models\AuditIssue;
use App\Models\PageData;
use App\Services\CrawlerService;
use App\Services\AiAnalysisService;
use App\Services\ScoringService;
use App\Mail\AuditCompletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RunAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 2;

    public function __construct(public Audit $audit) {}

    public function handle(): void
    {
        try {
            Log::info("Audit started for: {$this->audit->url}");

            // 1. Crawlează site-ul
            $crawler = new CrawlerService();
            $crawlData = $crawler->analyze($this->audit->url);

            // 2. Salvează datele brute — forțăm JSON valid ignorând bytes invalizi
            $rawJson = json_encode($crawlData, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
            if ($rawJson === false) {
                // Fallback extrem: înlocuim tot ce nu e valid
                $crawlData = $this->sanitizeUtf8($crawlData);
                $rawJson = json_encode($crawlData, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
            }
            \Illuminate\Support\Facades\DB::table('audits')
                ->where('id', $this->audit->id)
                ->update(['raw_data' => $rawJson]);
            $this->audit->setRawAttributes(array_merge($this->audit->getAttributes(), ['raw_data' => $rawJson]));

            // 3. Salvează datele per pagină
            foreach ($crawlData['pages'] ?? [] as $page) {
                PageData::create([
                    'audit_id'            => $this->audit->id,
                    'url'                 => $page['url'],
                    'page_type'           => $page['page_type'] ?? 'other',
                    'status_code'         => $page['status_code'] ?? null,
                    'load_time_ms'        => $page['load_time_ms'] ?? null,
                    'title'               => $page['title'] ?? null,
                    'meta_description'    => $page['meta_description'] ?? null,
                    'h1'                  => $page['h1'] ?? null,
                    'images_total'        => $page['images_total'] ?? 0,
                    'images_missing_alt'  => $page['images_missing_alt'] ?? 0,
                    'broken_links_count'  => $page['broken_links_count'] ?? 0,
                ]);
            }

            // 4. Generează problemele din datele crawlate
            $issues = $this->generateIssues($crawlData);

            // 5. Analiză AI pe texte
            $aiService = new AiAnalysisService();
            $aiIssues = $aiService->analyze($crawlData);
            $issues = array_merge($issues, $aiIssues);

            // 6. Salvează toate problemele
            foreach ($issues as $issue) {
                AuditIssue::create([
                    'audit_id'     => $this->audit->id,
                    'category'     => $issue['category'],
                    'severity'     => $issue['severity'],
                    'title'        => $issue['title'],
                    'description'  => $issue['description'],
                    'suggestion'   => $issue['suggestion'] ?? null,
                    'affected_url' => $issue['affected_url'] ?? null,
                    'impact'       => $issue['impact'] ?? null,
                ]);
            }

            // 7. Calculează scorurile
            $scoring = new ScoringService();
            $scores = $scoring->calculate($this->audit->fresh()->load('issues'));

            // 8. Marchează ca finalizat — DB::table evită JSON cast pe raw_data
            $token = Str::random(32);
            \Illuminate\Support\Facades\DB::table('audits')
                ->where('id', $this->audit->id)
                ->update([
                    'status'          => 'completed',
                    'public_token'    => $token,
                    'score_total'     => $scores['total'],
                    'score_technical' => $scores['technical'],
                    'score_seo'       => $scores['seo'],
                    'score_legal'     => $scores['legal'],
                    'score_eeeat'     => $scores['eeeat'],
                    'score_content'   => $scores['content'],
                    'score_ux'        => $scores['ux'],
                    'completed_at'    => now(),
                ]);

            // 9. Trimite email
            Mail::to($this->audit->email)
                ->send(new AuditCompletedMail($this->audit));

            Log::info("Audit completed for: {$this->audit->url}");

        } catch (\Exception $e) {
            Log::error("Audit failed for {$this->audit->url}: " . $e->getMessage());
            $this->audit->update(['status' => 'failed']);
            throw $e;
        }
    }

    private function generateIssues(array $data): array
    {
        $issues = [];

        // ── TEHNIC — PageSpeed & Core Web Vitals ────────────────

        $mobile  = $data['pagespeed']['mobile']  ?? [];
        $desktop = $data['pagespeed']['desktop'] ?? [];
        $mobileScore  = $mobile['score']  ?? 100;
        $desktopScore = $desktop['score'] ?? 100;

        // Scor general mobile
        if ($mobileScore < 50) {
            $issues[] = [
                'category'    => 'technical',
                'severity'    => 'critical',
                'title'       => 'Viteză mobilă foarte slabă',
                'description' => "Scorul PageSpeed pe mobile este {$mobileScore}/100. Peste 60% din trafic vine de pe telefon.",
                'suggestion'  => 'Optimizează imaginile (convertește în WebP), activează lazy loading și minimizează JavaScript.',
                'impact'      => 'SEO,UX,Conversie',
            ];
        } elseif ($mobileScore < 75) {
            $issues[] = [
                'category'    => 'technical',
                'severity'    => 'warning',
                'title'       => 'Viteză mobilă sub standard',
                'description' => "Scorul PageSpeed pe mobile este {$mobileScore}/100. Google recomandă minim 75.",
                'suggestion'  => 'Verifică imaginile neoptimizate și resursele care blochează randarea.',
                'impact'      => 'SEO,UX',
            ];
        }

        // Scor general desktop
        if ($desktopScore < 50) {
            $issues[] = [
                'category'    => 'technical',
                'severity'    => 'warning',
                'title'       => 'Viteză desktop slabă',
                'description' => "Scorul PageSpeed desktop este {$desktopScore}/100.",
                'suggestion'  => 'Activează compresia Gzip/Brotli pe server și folosește un CDN.',
                'impact'      => 'UX,Conversie',
            ];
        }

        // ── Core Web Vitals — LCP ────────────────────────────
        // Threshold: bun < 2500ms, necesită îmbunătățiri < 4000ms, slab >= 4000ms
        $lcpMs = $mobile['lcp_ms'] ?? null;
        if ($lcpMs !== null) {
            if ($lcpMs >= 4000) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'critical',
                    'title'       => 'LCP critic: ' . ($mobile['lcp'] ?? round($lcpMs/1000,1).'s'),
                    'description' => "Largest Contentful Paint pe mobile este {$mobile['lcp']} (trebuie sub 2.5s). Google penalizează direct în ranking.",
                    'suggestion'  => 'Optimizează imaginea/elementul principal vizibil: preload, dimensiune corectă, format WebP, server mai rapid.',
                    'impact'      => 'SEO,UX',
                ];
            } elseif ($lcpMs >= 2500) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'warning',
                    'title'       => 'LCP necesită îmbunătățiri: ' . ($mobile['lcp'] ?? round($lcpMs/1000,1).'s'),
                    'description' => "Largest Contentful Paint pe mobile este {$mobile['lcp']} (ideal sub 2.5s).",
                    'suggestion'  => 'Preload resursa principală vizibilă (hero image/text), elimină render-blocking resources.',
                    'impact'      => 'SEO,UX',
                ];
            }
        }

        // ── Core Web Vitals — CLS ────────────────────────────
        // Threshold: bun < 0.1, necesită îmbunătățiri < 0.25, slab >= 0.25
        $clsRaw = $mobile['cls_raw'] ?? null;
        if ($clsRaw !== null) {
            if ($clsRaw >= 0.25) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'critical',
                    'title'       => 'CLS critic: ' . ($mobile['cls'] ?? $clsRaw),
                    'description' => "Cumulative Layout Shift pe mobile este {$mobile['cls']} (trebuie sub 0.1). Elementele sar pe ecran când pagina se încarcă.",
                    'suggestion'  => 'Specifică dimensiuni width/height pentru imagini și embeds, evită inserarea de conținut deasupra celui existent.',
                    'impact'      => 'UX,SEO',
                ];
            } elseif ($clsRaw >= 0.1) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'warning',
                    'title'       => 'CLS necesită îmbunătățiri: ' . ($mobile['cls'] ?? $clsRaw),
                    'description' => "Cumulative Layout Shift pe mobile este {$mobile['cls']} (ideal sub 0.1). Utilizatorii pot face click greșit din cauza elementelor care se mișcă.",
                    'suggestion'  => 'Adaugă dimensiuni explicite imaginilor și rezervă spațiu pentru fonturi/ads.',
                    'impact'      => 'UX',
                ];
            }
        }

        // ── Core Web Vitals — INP ────────────────────────────
        // Threshold: bun < 200ms, necesită îmbunătățiri < 500ms, slab >= 500ms
        $inpMs = $mobile['inp_ms'] ?? null;
        if ($inpMs !== null) {
            if ($inpMs >= 500) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'critical',
                    'title'       => 'INP critic: ' . ($mobile['inp'] ?? $inpMs.'ms'),
                    'description' => "Interaction to Next Paint pe mobile este {$mobile['inp']} (trebuie sub 200ms). Site-ul pare blocat când utilizatorul apasă butoane.",
                    'suggestion'  => 'Reduce JavaScript executat pe main thread, folosește web workers pentru operații grele.',
                    'impact'      => 'UX,Conversie',
                ];
            } elseif ($inpMs >= 200) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'warning',
                    'title'       => 'INP necesită îmbunătățiri: ' . ($mobile['inp'] ?? $inpMs.'ms'),
                    'description' => "Interaction to Next Paint pe mobile este {$mobile['inp']} (ideal sub 200ms).",
                    'suggestion'  => 'Optimizează event handlers și divide task-urile JavaScript lungi.',
                    'impact'      => 'UX',
                ];
            }
        }

        // ── TTFB ─────────────────────────────────────────────
        // Threshold: bun < 800ms, slab >= 1800ms
        $ttfbMs = $mobile['ttfb_ms'] ?? null;
        if ($ttfbMs !== null) {
            if ($ttfbMs >= 1800) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'critical',
                    'title'       => 'TTFB critic — serverul răspunde lent: ' . ($mobile['ttfb'] ?? round($ttfbMs/1000,1).'s'),
                    'description' => "Time to First Byte este {$mobile['ttfb']} (trebuie sub 800ms). Serverul durează prea mult să trimită primul byte de HTML.",
                    'suggestion'  => 'Verifică hosting-ul (upgrade plan sau schimbă provider), activează caching la nivel server (Redis/Memcached), folosește CDN.',
                    'impact'      => 'SEO,UX',
                ];
            } elseif ($ttfbMs >= 800) {
                $issues[] = [
                    'category'    => 'technical',
                    'severity'    => 'warning',
                    'title'       => 'TTFB ridicat: ' . ($mobile['ttfb'] ?? round($ttfbMs/1000,1).'s'),
                    'description' => "Time to First Byte este {$mobile['ttfb']} (ideal sub 800ms). Serverul răspunde mai lent decât standard.",
                    'suggestion'  => 'Activează caching de pagini, optimizează query-urile din baza de date, verifică plugins lenți (WordPress).',
                    'impact'      => 'SEO,UX',
                ];
            }
        }

        // ── Opportunities Lighthouse (deduplicat mobile vs desktop) ──
        // Folosim mobile ca sursă principală, completăm cu desktop dacă lipsesc
        $mobileOpps  = $mobile['opportunities']  ?? [];
        $desktopOpps = $desktop['opportunities'] ?? [];

        // Index după ID ca să deduplicăm
        $allOpps = [];
        foreach ($mobileOpps as $opp) {
            $allOpps[$opp['id']] = $opp;
        }
        foreach ($desktopOpps as $opp) {
            if (!isset($allOpps[$opp['id']])) {
                $allOpps[$opp['id']] = $opp;
            }
        }

        // Mapping ID → sugestie concretă
        $suggestionMap = [
            'uses-webp-images'          => 'Convertește imaginile în format WebP sau AVIF folosind un plugin (Shortpixel, Imagify) sau manual cu squoosh.app.',
            'uses-optimized-images'     => 'Compresează imaginile înainte de upload. Folosește tinypng.com sau un plugin de optimizare automată.',
            'offscreen-images'          => 'Adaugă atributul loading="lazy" pe toate imaginile care nu sunt vizibile imediat la încărcare.',
            'render-blocking-resources' => 'Mută CSS-ul non-critic în footer sau folosește async/defer pentru scripturi JavaScript.',
            'unused-css-rules'          => 'Elimină CSS-ul neutilizat. În WordPress: dezactivează stilurile plugin-urilor inactive.',
            'unused-javascript'         => 'Elimină sau amână JavaScript-ul care nu e necesar la încărcarea inițială a paginii.',
            'uses-text-compression'     => 'Activează Gzip sau Brotli în cPanel → Apache Configuration sau prin fișierul .htaccess.',
            'uses-long-cache-ttl'       => 'Setează Cache-Control headers pentru fișierele statice (CSS, JS, imagini) la minim 1 an.',
            'efficient-animated-content'=> 'Înlocuiește GIF-urile animate cu video MP4/WebM — sunt de 10x mai mici.',
            'uses-responsive-images'    => 'Folosește atributul srcset pentru a servi dimensiuni diferite în funcție de dispozitiv.',
            'total-byte-weight'         => 'Pagina depășește greutatea recomandată. Compresează imagini, minifică CSS/JS, elimină resurse inutile.',
            'dom-size'                  => 'Pagina are prea multe elemente HTML. Simplifică structura, evită widgets inutile.',
            'third-party-summary'       => 'Scripturile terțe (chat, analytics, ads) încetinesc pagina. Încarcă-le async sau elimină cele neutilizate.',
            'bootup-time'               => 'JavaScript-ul durează prea mult să se execute. Audit și elimină librăriile JavaScript nefolosite.',
            'mainthread-work-breakdown' => 'Browserul este blocat pe main thread. Divide task-urile lungi și amână JS-ul non-critic.',
            'font-display'              => 'Adaugă font-display: swap în CSS pentru a afișa textul imediat cu un font fallback.',
            'no-document-write'         => 'Înlocuiește document.write() cu metode DOM moderne (appendChild, insertAdjacentHTML).',
            'redirects'                 => 'Elimină redirecționările inutile. Actualizează linkurile să pointeze direct la URL-ul final.',
            'server-response-time'      => 'Serverul răspunde lent. Consideră upgrade hosting, activare caching sau migrare pe server dedicat.',
        ];

        foreach ($allOpps as $opp) {
            $savings = $opp['savings'] ? " ({$opp['savings']})" : '';
            $issues[] = [
                'category'    => 'technical',
                'severity'    => $opp['severity'],
                'title'       => $opp['label'] . ($opp['display'] ? ' — ' . $opp['display'] : ''),
                'description' => "PageSpeed Lighthouse: {$opp['label']}{$savings}. Afectează direct viteza de încărcare și scorul Core Web Vitals.",
                'suggestion'  => $suggestionMap[$opp['id']] ?? $opp['label'] . ': urmărește recomandarea din Google PageSpeed Insights.',
                'impact'      => 'SEO,UX',
            ];
        }

        // SSL
        if (!($data['ssl']['valid'] ?? true)) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'critical',
                'title'      => 'Certificat SSL invalid sau expirat',
                'description' => 'Site-ul nu are HTTPS activ sau certificatul a expirat. Google penalizează site-urile fără SSL.',
                'suggestion' => 'Instalează un certificat SSL gratuit (Let\'s Encrypt) prin cPanel.',
            ];
        }

        // SSL expiră curând
        $daysLeft = $data['ssl']['days_left'] ?? 999;
        if ($daysLeft > 0 && $daysLeft < 30) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'warning',
                'title'      => "Certificatul SSL expiră în {$daysLeft} zile",
                'description' => 'Dacă expiră, browserele vor afișa eroarea "Not Secure" și vei pierde tot traficul.',
                'suggestion' => 'Reînnoiește certificatul SSL cât mai curând.',
            ];
        }

        // Broken links
        $brokenLinks = $data['broken_links'] ?? [];
        if (count($brokenLinks) > 0) {
            $issues[] = [
                'category'    => 'technical',
                'severity'    => count($brokenLinks) > 5 ? 'critical' : 'warning',
                'title'       => count($brokenLinks) . ' link-uri rupte (eroare 404)',
                'description' => 'Link-urile rupte afectează experiența utilizatorilor și scorul SEO.',
                'suggestion'  => 'Redirecționează sau șterge link-urile: ' . implode(', ', array_slice($brokenLinks, 0, 3)),
            ];
        }

        // ── SEO ──────────────────────────────────────────────

        // Meta description
        $missingMeta = $data['seo']['missing_meta_description'] ?? [];
        if (count($missingMeta) > 0) {
            $issues[] = [
                'category'    => 'seo',
                'severity'    => 'critical',
                'title'       => count($missingMeta) . ' pagini fără meta description',
                'description' => 'Meta description lipsă înseamnă că Google generează automat un rezumat aleatoriu în rezultatele de căutare.',
                'suggestion'  => 'Scrie descrieri de 150-160 caractere pentru fiecare pagină, incluzând cuvântul cheie principal.',
                'affected_url' => implode(', ', array_slice($missingMeta, 0, 2)),
            ];
        }

        // H1
        $missingH1 = $data['seo']['missing_h1'] ?? [];
        if (count($missingH1) > 0) {
            $issues[] = [
                'category'    => 'seo',
                'severity'    => 'critical',
                'title'       => count($missingH1) . ' pagini fără tag H1',
                'description' => 'H1 este cel mai important element de titlu pentru Google. Fără el, pagina nu are un titlu principal definit.',
                'suggestion'  => 'Adaugă câte un singur tag H1 pe fiecare pagină, care să conțină cuvântul cheie principal.',
                'affected_url' => implode(', ', array_slice($missingH1, 0, 2)),
            ];
        }

        // Duplicate H1
        $duplicateH1 = $data['seo']['duplicate_h1'] ?? [];
        if (count($duplicateH1) > 0) {
            $issues[] = [
                'category'    => 'seo',
                'severity'    => 'warning',
                'title'       => count($duplicateH1) . ' pagini cu H1 duplicat',
                'description' => 'Paginile au mai mult de un tag H1. Google nu știe care este titlul principal.',
                'suggestion'  => 'Păstrează un singur H1 per pagină și transformă restul în H2.',
                'affected_url' => implode(', ', array_slice($duplicateH1, 0, 2)),
            ];
        }

        // Alt text imagini
        $totalMissingAlt = collect($data['pages'] ?? [])->sum('images_missing_alt');
        if ($totalMissingAlt > 0) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => $totalMissingAlt > 5 ? 'critical' : 'warning',
                'title'      => "{$totalMissingAlt} imagini fără atribut alt text",
                'description' => 'Google nu poate "citi" imaginile fără alt text. Pierzi trafic din căutările de imagini.',
                'suggestion' => 'Adaugă descrieri relevante în atributul alt pentru fiecare imagine.',
            ];
        }

        // Sitemap
        if (!($data['seo']['has_sitemap'] ?? false)) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'warning',
                'title'      => 'Sitemap.xml lipsă',
                'description' => 'Fără sitemap, Google poate să nu descopere toate paginile site-ului tău.',
                'suggestion' => 'Generează un sitemap.xml și trimite-l în Google Search Console.',
            ];
        }

        // Robots.txt
        if (!($data['seo']['has_robots'] ?? false)) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'warning',
                'title'      => 'Robots.txt lipsă',
                'description' => 'Fără robots.txt, Google nu are instrucțiuni despre ce pagini să indexeze.',
                'suggestion' => 'Creează un fișier robots.txt în rădăcina site-ului.',
            ];
        }

        // ── LEGAL ─────────────────────────────────────────────

        if (!($data['legal']['has_anpc'] ?? false)) {
            $issues[] = [
                'category'   => 'legal',
                'severity'   => 'critical',
                'title'      => 'Lipsesc imaginile obligatorii ANPC',
                'description' => 'Comercianții online din România sunt obligați prin lege să afișeze logo-urile SAL și ANPC cu linkuri către site-urile oficiale.',
                'suggestion' => 'Adaugă în footer imaginile SAL (anpc.ro/ce-este-sal) și ANPC (ec.europa.eu/consumers/odr).',
            ];
        }

        if (!($data['legal']['has_company_details'] ?? false)) {
            $issues[] = [
                'category'   => 'legal',
                'severity'   => 'critical',
                'title'      => 'Date firmă incomplete în footer',
                'description' => 'Legea obligă afișarea CUI, numărului de înregistrare (J) și sediului social.',
                'suggestion' => 'Adaugă în footer: denumire firmă, CUI, nr. Registrul Comerțului și adresa sediului social.',
            ];
        }

        if (!($data['legal']['has_gdpr_policy'] ?? false)) {
            $issues[] = [
                'category'   => 'legal',
                'severity'   => 'critical',
                'title'      => 'Politică de confidențialitate lipsă sau incompletă',
                'description' => 'GDPR obligă toate site-urile care colectează date să aibă o politică de confidențialitate completă.',
                'suggestion' => 'Creează o pagină dedicată cu: ce date colectezi, scopul, durata păstrării și drepturile utilizatorilor.',
            ];
        }

        if (!($data['legal']['has_cookie_banner'] ?? false)) {
            $issues[] = [
                'category'   => 'legal',
                'severity'   => 'warning',
                'title'      => 'Banner cookies lipsă sau neconform',
                'description' => 'Directiva ePrivacy impune informarea utilizatorilor despre cookies și posibilitatea de a refuza.',
                'suggestion' => 'Implementează un banner de cookies cu opțiunea de Accept și Refuz separat.',
            ];
        }

        // ── UX ───────────────────────────────────────────────

        if (!($data['ux']['is_mobile_friendly'] ?? true)) {
            $issues[] = [
                'category'   => 'ux',
                'severity'   => 'critical',
                'title'      => 'Site-ul nu este optimizat pentru mobile',
                'description' => 'Google folosește Mobile-First Indexing — dacă site-ul nu funcționează pe telefon, pierzi poziții în căutări.',
                'suggestion' => 'Implementează un design responsive care să funcționează pe toate dimensiunile de ecran.',
                'impact'     => 'SEO,UX',
            ];
        }

        if (!($data['ux']['has_phone'] ?? false)) {
            $issues[] = [
                'category'   => 'ux',
                'severity'   => 'warning',
                'title'      => 'Număr de telefon lipsă sau nedetectabil',
                'description' => 'Nu am găsit un număr de telefon vizibil sau un link tel: în pagină. Clienții nu te pot contacta ușor.',
                'suggestion' => 'Adaugă numărul de telefon în header sau footer cu un link href="tel:07XXXXXXXX".',
                'impact'     => 'Conversie,UX',
            ];
        }

        // ── STRUCTURED DATA ──────────────────────────────────

        $sd = $data['structured_data'] ?? [];
        if (!($sd['has_json_ld'] ?? false) && !($sd['has_microdata'] ?? false)) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'warning',
                'title'      => 'Lipsesc datele structurate (Schema.org)',
                'description' => 'Fără JSON-LD sau microdata, Google nu poate afișa rich snippets (stele, prețuri, FAQ) în rezultatele de căutare.',
                'suggestion'  => 'Adaugă JSON-LD cu schema Organization, WebSite și LocalBusiness (dacă e aplicabil). Folosește schema.org sau un plugin WordPress (RankMath, Yoast).',
                'impact'      => 'SEO',
            ];
        }

        if (!empty($sd['errors'] ?? [])) {
            $issues[] = [
                'category'    => 'seo',
                'severity'    => 'warning',
                'title'       => 'Erori în datele structurate JSON-LD',
                'description' => 'JSON-LD-ul găsit pe pagină conține erori de sintaxă. Google nu îl va procesa corect.',
                'suggestion'  => 'Validează JSON-LD-ul cu instrumentul Google Rich Results Test (search.google.com/test/rich-results).',
                'impact'      => 'SEO',
            ];
        }

        // ── OPEN GRAPH ────────────────────────────────────────

        $og = $data['open_graph'] ?? [];
        $ogMissing = [];
        if (!($og['has_og_title'] ?? false))       $ogMissing[] = 'og:title';
        if (!($og['has_og_description'] ?? false)) $ogMissing[] = 'og:description';
        if (!($og['has_og_image'] ?? false))       $ogMissing[] = 'og:image';

        if (!empty($ogMissing)) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'warning',
                'title'      => 'Tag-uri Open Graph lipsă: ' . implode(', ', $ogMissing),
                'description' => 'Fără Open Graph complet, când site-ul e distribuit pe Facebook, LinkedIn sau WhatsApp apare fără imagine și cu titlu greșit.',
                'suggestion'  => 'Adaugă în <head>: og:title, og:description, og:image (min. 1200x630px) și og:url.',
                'impact'      => 'SEO,Conversie',
            ];
        }

        if (!($og['has_twitter_card'] ?? false)) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'info',
                'title'      => 'Twitter/X Card lipsă',
                'description' => 'Fără meta twitter:card, postările pe X/Twitter nu vor afișa preview-ul corect al site-ului.',
                'suggestion'  => 'Adaugă <meta name="twitter:card" content="summary_large_image"> în <head>.',
                'impact'      => 'SEO',
            ];
        }

        // ── SECURITY HEADERS ─────────────────────────────────

        $sec = $data['security_headers'] ?? [];
        $secScore = $sec['score'] ?? 0;

        if (!($sec['hsts'] ?? false)) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'warning',
                'title'      => 'Header HSTS lipsă (Strict-Transport-Security)',
                'description' => 'Fără HSTS, browserele nu forțează HTTPS automat. Utilizatorii pot fi vulnerabili la atacuri man-in-the-middle.',
                'suggestion'  => 'Adaugă în configurația serverului: Strict-Transport-Security: max-age=31536000; includeSubDomains',
                'impact'      => 'Security',
            ];
        }

        if (!($sec['x_frame_options'] ?? false)) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'warning',
                'title'      => 'Header X-Frame-Options lipsă (protecție clickjacking)',
                'description' => 'Fără acest header, site-ul poate fi încorporat în iframe-uri malițioase pentru a păcăli utilizatorii.',
                'suggestion'  => 'Adaugă: X-Frame-Options: SAMEORIGIN în configurația serverului sau .htaccess.',
                'impact'      => 'Security',
            ];
        }

        if (!($sec['x_content_type'] ?? false)) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'info',
                'title'      => 'Header X-Content-Type-Options lipsă',
                'description' => 'Fără nosniff, browserele pot interpreta greșit tipul fișierelor, creând vulnerabilități de securitate.',
                'suggestion'  => 'Adaugă: X-Content-Type-Options: nosniff în configurația serverului.',
                'impact'      => 'Security',
            ];
        }

        if (!($sec['csp'] ?? false)) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'info',
                'title'      => 'Content Security Policy (CSP) lipsă',
                'description' => 'Fără CSP, site-ul este mai vulnerabil la atacuri XSS (injectare de scripturi malițioase).',
                'suggestion'  => 'Definește o politică CSP care să specifice sursele permise pentru scripturi, imagini și stiluri.',
                'impact'      => 'Security',
            ];
        }

        // ── TRACKING & ANALYTICS ─────────────────────────────

        $tracking = $data['tracking'] ?? [];

        if (!($tracking['has_ga4'] ?? false) && !($tracking['has_gtm'] ?? false)) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'critical',
                'title'      => 'Google Analytics 4 lipsă — nu măsori traficul',
                'description' => 'Nu am detectat GA4 sau Google Tag Manager. Fără analytics nu știi câți vizitatori ai, de unde vin și ce fac pe site.',
                'suggestion'  => 'Instalează GA4 prin Google Tag Manager. Creează cont pe analytics.google.com și conectează-l cu Search Console.',
                'impact'      => 'Conversie,SEO',
            ];
        }

        if ($tracking['has_ua'] ?? false) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'critical',
                'title'      => 'Universal Analytics (UA) detectat — deprecated din iulie 2023',
                'description' => 'Universal Analytics nu mai colectează date din iulie 2023. Dacă mai e prezent în cod, e cod mort care ocupă resurse.',
                'suggestion'  => 'Elimină complet codul UA-XXXXXX și înlocuiește cu GA4 (G-XXXXXXXX).',
                'impact'      => 'Conversie',
            ];
        }

        if ($tracking['double_ga4'] ?? false) {
            $ga4List = implode(', ', $tracking['ga4_ids'] ?? []);
            $issues[] = [
                'category'    => 'technical',
                'severity'    => 'warning',
                'title'       => 'GA4 instalat de mai multe ori — date duplicate',
                'description' => "Am detectat multiple ID-uri GA4 sau instanțe: {$ga4List}. Datele din Analytics sunt incorecte (sesiunile se numără dublu).",
                'suggestion'  => 'Păstrează un singur tag GA4. Dacă folosești GTM, elimină tagurile GA4 hardcodate din HTML.',
                'impact'      => 'Conversie',
            ];
        }

        // Termeni și condiții lipsă
        if (!($data['legal']['has_terms'] ?? false)) {
            $issues[] = [
                'category'   => 'legal',
                'severity'   => 'warning',
                'title'      => 'Pagină Termeni și Condiții lipsă',
                'description' => 'Orice site de business sau eCommerce are obligația legală de a afișa termenii contractuali.',
                'suggestion'  => 'Creează o pagină /termeni-si-conditii cu: obiectul contractului, prețuri, livrare, returnare, răspundere.',
                'impact'      => 'Legal',
            ];
        }

        // SEO: lipsă canonical
        if (!($data['seo']['has_canonical'] ?? false)) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'info',
                'title'      => 'Tag canonical lipsă',
                'description' => 'Fără canonical, Google nu știe care e versiunea preferată a paginii (cu/fără www, cu/fără slash).',
                'suggestion'  => 'Adaugă <link rel="canonical" href="URL_COMPLET"> în <head> pe fiecare pagină.',
                'impact'      => 'SEO',
            ];
        }

        // SEO: title prea scurt sau prea lung
        $titleLen = $data['seo']['title_length'] ?? 0;
        if ($titleLen > 0 && $titleLen > 70) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'warning',
                'title'      => "Title prea lung ({$titleLen} caractere) — trunchiat în Google",
                'description' => 'Google afișează maxim ~60-65 de caractere din title. Restul e tăiat cu „...".',
                'suggestion'  => 'Scurtează titlul paginii principale la maxim 60 de caractere, păstrând cuvântul cheie la început.',
                'impact'      => 'SEO',
            ];
        } elseif ($titleLen > 0 && $titleLen < 30) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'info',
                'title'      => "Title prea scurt ({$titleLen} caractere) — oportunitate SEO ratată",
                'description' => 'Un title sub 30 de caractere nu folosește tot spațiul disponibil pentru a include cuvinte cheie relevante.',
                'suggestion'  => 'Extinde titlul la 50-60 caractere, incluzând cuvântul cheie principal și brandul.',
                'impact'      => 'SEO',
            ];
        }

        // Meta description prea lungă
        $metaLen = $data['seo']['meta_desc_length'] ?? 0;
        if ($metaLen > 160) {
            $issues[] = [
                'category'   => 'seo',
                'severity'   => 'info',
                'title'      => "Meta description prea lungă ({$metaLen} caractere)",
                'description' => 'Google trunchiază meta description-urile peste ~155-160 caractere. Mesajul important poate fi tăiat.',
                'suggestion'  => 'Scurtează meta description la 140-155 caractere, asigurând că CTA sau cuvântul cheie e la început.',
                'impact'      => 'SEO',
            ];
        }

        return $issues;
    }

    /**
     * Sanitizează recursiv toate string-urile dintr-un array la UTF-8 valid.
     * Necesitar pentru site-uri cu encoding Windows-1252 / ISO-8859-1.
     */
    private function sanitizeUtf8(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn($v) => $this->sanitizeUtf8($v), $value);
        }
        if (is_string($value)) {
            // Dacă nu e UTF-8 valid, convertim forțat
            if (!mb_detect_encoding($value, 'UTF-8', true)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
            }
            // Eliminăm orice caracter care tot nu e valid UTF-8
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
        return $value;
    }
}