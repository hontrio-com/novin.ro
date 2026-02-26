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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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

            // 2. Salvează datele brute
            $this->audit->update(['raw_data' => $crawlData]);

            // 3. Salvează datele per pagină
            foreach ($crawlData['pages'] ?? [] as $page) {
                PageData::create([
                    'audit_id'            => $this->audit->id,
                    'url'                 => $page['url'],
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
                ]);
            }

            // 7. Calculează scorurile
            $scoring = new ScoringService();
            $scores = $scoring->calculate($this->audit->fresh()->load('issues'));

            // 8. Marchează ca finalizat
            $this->audit->update([
                'status'           => 'completed',
                'public_token'     => Str::random(32),
                'score_total'      => $scores['total'],
                'score_technical'  => $scores['technical'],
                'score_seo'        => $scores['seo'],
                'score_legal'      => $scores['legal'],
                'score_eeeat'      => $scores['eeeat'],
                'score_content'    => $scores['content'],
                'score_ux'         => $scores['ux'],
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

        // ── TEHNIC ──────────────────────────────────────────

        // Viteză mobile
        $mobileScore = $data['pagespeed']['mobile']['score'] ?? 100;
        if ($mobileScore < 50) {
            $issues[] = [
                'category'    => 'technical',
                'severity'    => 'critical',
                'title'       => 'Viteză mobilă foarte slabă',
                'description' => "Scorul PageSpeed pe mobile este {$mobileScore}/100. Peste 60% din trafic vine de pe telefon.",
                'suggestion'  => 'Optimizează imaginile (convertește în WebP), activează lazy loading și minimizează JavaScript.',
            ];
        } elseif ($mobileScore < 75) {
            $issues[] = [
                'category'    => 'technical',
                'severity'    => 'warning',
                'title'       => 'Viteză mobilă sub standard',
                'description' => "Scorul PageSpeed pe mobile este {$mobileScore}/100. Google recomandă minim 75.",
                'suggestion'  => 'Verifică imaginile neoptimizate și resursele care blochează randarea.',
            ];
        }

        // Viteză desktop
        $desktopScore = $data['pagespeed']['desktop']['score'] ?? 100;
        if ($desktopScore < 50) {
            $issues[] = [
                'category'   => 'technical',
                'severity'   => 'warning',
                'title'      => 'Viteză desktop slabă',
                'description' => "Scorul PageSpeed desktop este {$desktopScore}/100.",
                'suggestion' => 'Activează compresia Gzip/Brotli pe server și folosește un CDN.',
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
                'suggestion' => 'Implementează un design responsive care să funcționeze pe toate dimensiunile de ecran.',
            ];
        }

        return $issues;
    }
}