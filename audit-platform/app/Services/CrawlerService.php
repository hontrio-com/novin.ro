<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlerService
{
    private string $url;

    public function analyze(string $url): array
    {
        $this->url = $url;
        Log::info("CrawlerService starting for: {$url}");

        $homepageHtml    = '';
        $homepageHeaders = [];
        try {
            $resp = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AuditBot/1.0)'])
                ->get($this->url);
            $homepageHtml    = $this->toUtf8($resp->body());
            $homepageHeaders = $resp->headers();
        } catch (\Exception $e) {
            Log::error("Homepage fetch failed: " . $e->getMessage());
        }

        return [
            'pages'            => $this->scrapePages($homepageHtml),
            'pagespeed'        => $this->checkPageSpeed(),
            'ssl'              => $this->checkSsl(),
            'broken_links'     => $this->checkBrokenLinks($homepageHtml),
            'seo'              => $this->checkSeo($homepageHtml),
            'legal'            => $this->checkLegal($homepageHtml),
            'ux'               => $this->checkUx($homepageHtml),
            'structured_data'  => $this->checkStructuredData($homepageHtml),
            'open_graph'       => $this->checkOpenGraph($homepageHtml),
            'security_headers' => $this->checkSecurityHeaders($homepageHeaders),
            'tracking'         => $this->checkTracking($homepageHtml),
        ];
    }

    private function scrapePages(string $homepageHtml): array
    {
        $pages = [];
        try {
            $start    = microtime(true);
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AuditBot/1.0)'])
                ->get($this->url);
            $loadTime = (int)((microtime(true) - $start) * 1000);
            $pages[]  = $this->parsePage($this->url, $this->toUtf8($response->body()), $response->status(), $loadTime);

            $internalLinks = $this->extractInternalLinks($homepageHtml, $this->url);
            foreach (array_slice($internalLinks, 0, 4) as $link) {
                try {
                    $start    = microtime(true);
                    $resp     = Http::timeout(10)
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AuditBot/1.0)'])
                        ->get($link);
                    $loadTime = (int)((microtime(true) - $start) * 1000);
                    $pages[]  = $this->parsePage($link, $this->toUtf8($resp->body()), $resp->status(), $loadTime);
                } catch (\Exception $e) {
                    Log::warning("Could not scrape page: {$link}");
                }
            }
        } catch (\Exception $e) {
            Log::error("scrapePages failed: " . $e->getMessage());
        }
        return $pages;
    }

    private function parsePage(string $url, string $html, int $statusCode, int $loadTime): array
    {
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $titleMatch);
        $title = isset($titleMatch[1]) ? trim(strip_tags($titleMatch[1])) : null;

        preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']/is', $html, $metaMatch);
        $metaDesc = isset($metaMatch[1]) ? trim($metaMatch[1]) : null;

        preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $h1Match);
        $h1 = isset($h1Match[1]) ? trim(strip_tags($h1Match[1])) : null;

        preg_match_all('/<img[^>]+>/i', $html, $imgMatches);
        $totalImages = count($imgMatches[0]);

        $missingAlt = 0;
        foreach ($imgMatches[0] as $img) {
            if (!preg_match('/alt=["\'][^"\']+["\']/i', $img)) {
                $missingAlt++;
            }
        }

        $pageType    = $this->classifyPageType($url, $html);
        $visibleText = $this->extractVisibleText($html);

        return [
            'url'                => $url,
            'status_code'        => $statusCode,
            'load_time_ms'       => $loadTime,
            'title'              => $title,
            'meta_description'   => $metaDesc,
            'h1'                 => $h1,
            'images_total'       => $totalImages,
            'images_missing_alt' => $missingAlt,
            'broken_links_count' => 0,
            'page_type'          => $pageType,
            'visible_text'       => substr($visibleText, 0, 3000),
            'raw_html'           => substr($html, 0, 50000),
        ];
    }

    private function classifyPageType(string $url, string $html): string
    {
        $path = strtolower(parse_url($url, PHP_URL_PATH) ?? '/');
        $patterns = [
            'checkout' => ['/checkout', '/cos', '/cart', '/comanda', '/order'],
            'product'  => ['/produs', '/product', '/p/', '/shop/'],
            'category' => ['/categorie', '/category', '/cat/'],
            'blog'     => ['/blog', '/articol', '/article', '/post', '/stire', '/news'],
            'contact'  => ['/contact', '/contactati'],
            'about'    => ['/despre', '/about', '/cine-suntem', '/echipa'],
            'services' => ['/servicii', '/services', '/oferta'],
            'faq'      => ['/faq', '/intrebari', '/questions'],
            'legal'    => ['/termeni', '/terms', '/privacy', '/confidentialitate', '/gdpr', '/cookies'],
        ];
        foreach ($patterns as $type => $slugs) {
            foreach ($slugs as $slug) {
                if (str_contains($path, $slug)) return $type;
            }
        }
        if ($path === '/' || $path === '') return 'home';
        return 'other';
    }

    private function extractVisibleText(string $html): string
    {
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
        $text = strip_tags($html);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function extractInternalLinks(string $html, string $baseUrl): array
    {
        $parsed = parse_url($baseUrl);
        $base   = $parsed['scheme'] . '://' . $parsed['host'];
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $html, $matches);
        $links = [];
        foreach ($matches[1] as $href) {
            if (str_starts_with($href, $base)) {
                $links[] = strtok($href, '?#');
            } elseif (str_starts_with($href, '/') && !str_starts_with($href, '//')) {
                $links[] = $base . strtok($href, '?#');
            }
        }
        $links = array_unique($links);
        $links = array_filter($links, fn($l) => rtrim($l, '/') !== rtrim($baseUrl, '/'));
        return array_values($links);
    }

    private function checkPageSpeed(): array
    {
        $result     = ['mobile' => ['score' => 50], 'desktop' => ['score' => 50]];
        $apiKey     = env('GOOGLE_PAGESPEED_KEY', '');
        $encodedUrl = urlencode($this->url);

        $opportunityMap = [
            'uses-optimized-images'      => 'Imagini neoptimizate',
            'uses-webp-images'           => 'Imagini neconvertite în WebP/AVIF',
            'offscreen-images'           => 'Imagini încărcate fără lazy-load',
            'render-blocking-resources'  => 'Resurse care blochează randarea',
            'unused-css-rules'           => 'CSS nefolosit care încetinește pagina',
            'unused-javascript'          => 'JavaScript nefolosit care încetinește pagina',
            'uses-text-compression'      => 'Lipsă compresie text (Gzip/Brotli)',
            'uses-long-cache-ttl'        => 'Cache static slab configurat',
            'efficient-animated-content' => 'GIF-uri care pot fi înlocuite cu video',
            'uses-responsive-images'     => 'Imagini neresponsive (prea mari)',
            'total-byte-weight'          => 'Pagina are dimensiune totală prea mare',
            'dom-size'                   => 'DOM prea mare (prea multe elemente HTML)',
            'third-party-summary'        => 'Scripturi terțe care încetinesc pagina',
            'bootup-time'                => 'JavaScript cu timp de execuție mare',
            'mainthread-work-breakdown'  => 'Thread principal supraîncărcat',
            'font-display'               => 'Fonturi care blochează textul vizibil',
            'no-document-write'          => 'Folosire document.write() care blochează',
            'redirects'                  => 'Redirecționări inutile care adaugă latență',
            'server-response-time'       => 'TTFB mare — serverul răspunde lent',
        ];

        foreach (['mobile', 'desktop'] as $strategy) {
            try {
                $apiUrl   = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$encodedUrl}&strategy={$strategy}" . ($apiKey ? "&key={$apiKey}" : '');
                $response = Http::timeout(60)->get($apiUrl);
                if (!$response->successful()) continue;

                $data   = $response->json();
                $lhr    = $data['lighthouseResult'] ?? [];
                $audits = $lhr['audits'] ?? [];
                $cats   = $lhr['categories'] ?? [];
                $score  = (int)(($cats['performance']['score'] ?? 0.5) * 100);

                $cwv = [
                    'score'         => $score,
                    'lcp'           => $this->metricValue($audits, 'largest-contentful-paint'),
                    'lcp_ms'        => $this->metricNumeric($audits, 'largest-contentful-paint'),
                    'cls'           => $this->metricValue($audits, 'cumulative-layout-shift'),
                    'cls_raw'       => $this->metricNumeric($audits, 'cumulative-layout-shift'),
                    'inp'           => $this->metricValue($audits, 'interaction-to-next-paint'),
                    'inp_ms'        => $this->metricNumeric($audits, 'interaction-to-next-paint'),
                    'fcp'           => $this->metricValue($audits, 'first-contentful-paint'),
                    'fcp_ms'        => $this->metricNumeric($audits, 'first-contentful-paint'),
                    'ttfb'          => $this->metricValue($audits, 'server-response-time'),
                    'ttfb_ms'       => $this->metricNumeric($audits, 'server-response-time'),
                    'tbt'           => $this->metricValue($audits, 'total-blocking-time'),
                    'tbt_ms'        => $this->metricNumeric($audits, 'total-blocking-time'),
                    'speed_index'   => $this->metricValue($audits, 'speed-index'),
                    'si_ms'         => $this->metricNumeric($audits, 'speed-index'),
                    'opportunities' => [],
                    'passed'        => [],
                ];

                foreach ($opportunityMap as $auditId => $label) {
                    if (!isset($audits[$auditId])) continue;
                    $audit      = $audits[$auditId];
                    $auditScore = $audit['score'] ?? 1;
                    if ($auditScore === null || $auditScore >= 0.9) { $cwv['passed'][] = $label; continue; }
                    $savingsMs    = $audit['details']['overallSavingsMs'] ?? $audit['numericValue'] ?? null;
                    $savingsBytes = $audit['details']['overallSavingsBytes'] ?? null;
                    $savingLabel  = null;
                    if ($savingsMs && $savingsMs > 100)        $savingLabel = round($savingsMs / 1000, 1) . 's economisiți';
                    elseif ($savingsBytes && $savingsBytes > 1024) $savingLabel = round($savingsBytes / 1024) . ' KB economisiți';
                    $cwv['opportunities'][] = [
                        'id'       => $auditId, 'label'    => $label,
                        'display'  => $audit['displayValue'] ?? null,
                        'savings'  => $savingLabel,
                        'severity' => $auditScore <= 0.49 ? 'critical' : ($auditScore <= 0.74 ? 'warning' : 'info'),
                        'score'    => $auditScore,
                    ];
                }
                $result[$strategy] = $cwv;
            } catch (\Exception $e) {
                Log::warning("PageSpeed check failed for {$strategy}: " . $e->getMessage());
            }
        }
        return $result;
    }

    private function metricValue(array $audits, string $id): ?string  { return $audits[$id]['displayValue'] ?? null; }
    private function metricNumeric(array $audits, string $id): ?float { $v = $audits[$id]['numericValue'] ?? null; return $v !== null ? round((float)$v, 2) : null; }

    private function checkSsl(): array
    {
        $parsed = parse_url($this->url);
        $host   = $parsed['host'] ?? '';
        $result = ['valid' => false, 'days_left' => 0, 'issuer' => null, 'https_redirect' => false];
        if (!$host) return $result;
        try {
            $httpResp = Http::timeout(8)->withoutRedirecting()->get('http://' . $host);
            $location = $httpResp->header('Location') ?? '';
            $result['https_redirect'] = str_starts_with($location, 'https://');
        } catch (\Exception $e) {}
        try {
            $ctx    = stream_context_create(['ssl' => ['capture_peer_cert' => true, 'verify_peer' => false, 'verify_peer_name' => false]]);
            $socket = @stream_socket_client("ssl://{$host}:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
            if ($socket) {
                $params   = stream_context_get_params($socket);
                $cert     = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                $validTo  = $cert['validTo_time_t'] ?? 0;
                $daysLeft = (int)(($validTo - time()) / 86400);
                $result   = array_merge($result, ['valid' => $daysLeft > 0, 'days_left' => max(0, $daysLeft), 'issuer' => $cert['issuer']['O'] ?? null]);
                fclose($socket);
            }
        } catch (\Exception $e) { Log::warning("SSL check failed: " . $e->getMessage()); }
        return $result;
    }

    private function checkBrokenLinks(string $html): array
    {
        $broken = []; $checked = [];
        try {
            $parsed = parse_url($this->url);
            $base   = $parsed['scheme'] . '://' . $parsed['host'];
            preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $html, $matches);
            foreach (array_slice($matches[1], 0, 30) as $href) {
                $checkUrl = str_starts_with($href, 'http') ? strtok($href, '#') : (str_starts_with($href, '/') ? $base . strtok($href, '#') : null);
                if (!$checkUrl || in_array($checkUrl, $checked)) continue;
                $checked[] = $checkUrl;
                try { $resp = Http::timeout(8)->head($checkUrl); if ($resp->status() === 404) $broken[] = $checkUrl; } catch (\Exception $e) {}
            }
        } catch (\Exception $e) { Log::warning("checkBrokenLinks failed: " . $e->getMessage()); }
        return $broken;
    }

    private function checkSeo(string $html): array
    {
        $result = ['missing_meta_description' => [], 'missing_h1' => [], 'duplicate_h1' => [], 'has_sitemap' => false, 'has_robots' => false, 'has_canonical' => false, 'has_noindex' => false, 'title_length' => 0, 'meta_desc_length' => 0];
        try {
            if (!preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']\S/i', $html)) $result['missing_meta_description'][] = $this->url;
            if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']/is', $html, $m)) $result['meta_desc_length'] = mb_strlen(trim($m[1]));
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) $result['title_length'] = mb_strlen(trim(strip_tags($m[1])));
            preg_match_all('/<h1[^>]*>/i', $html, $h1M);
            $h1c = count($h1M[0]);
            if ($h1c === 0) $result['missing_h1'][] = $this->url; elseif ($h1c > 1) $result['duplicate_h1'][] = $this->url;
            $result['has_canonical'] = (bool)preg_match('/<link[^>]+rel=["\']canonical["\']/i', $html);
            $result['has_noindex']   = (bool)preg_match('/<meta[^>]+name=["\']robots["\'][^>]+content=["\'][^"\']*noindex/i', $html);
            $base    = parse_url($this->url, PHP_URL_SCHEME) . '://' . parse_url($this->url, PHP_URL_HOST);
            $sitemap = Http::timeout(8)->get($base . '/sitemap.xml');
            $result['has_sitemap'] = $sitemap->successful() && str_contains($sitemap->body(), '<url');
            $robots = Http::timeout(8)->get($base . '/robots.txt');
            $result['has_robots'] = $robots->successful() && str_contains($robots->body(), 'User-agent');
        } catch (\Exception $e) { Log::warning("checkSeo failed: " . $e->getMessage()); }
        return $result;
    }

    private function checkLegal(string $html): array
    {
        $result = ['has_anpc' => false, 'has_company_details' => false, 'has_gdpr_policy' => false, 'has_cookie_banner' => false, 'has_terms' => false];
        try {
            $l = strtolower($html);
            $result['has_anpc']            = str_contains($l, 'anpc.ro') || str_contains($l, 'sal.anpc') || (bool)preg_match('/href=["\'][^"\']*anpc/i', $html);
            $result['has_company_details'] = (bool)preg_match('/cui\s*:?\s*\d{6,10}/i', $html) || (bool)preg_match('/j\d{2}\/\d+\/\d{4}/i', $html);
            $result['has_gdpr_policy']     = str_contains($l, 'confidentialitate') || str_contains($l, 'gdpr') || str_contains($l, 'privacy policy');
            $result['has_cookie_banner']   = str_contains($l, 'cookie') && (str_contains($l, 'accept') || str_contains($l, 'acept'));
            $result['has_terms']           = str_contains($l, 'termeni si conditii') || str_contains($l, 'termeni și condiții') || (bool)preg_match('/href=["\'][^"\']*termin/i', $html);
        } catch (\Exception $e) { Log::warning("checkLegal failed: " . $e->getMessage()); }
        return $result;
    }

    private function checkUx(string $html): array
    {
        $result = ['is_mobile_friendly' => false, 'has_phone' => false, 'has_whatsapp' => false, 'has_search' => false, 'has_breadcrumb' => false];
        try {
            $l = strtolower($html);
            $result['is_mobile_friendly'] = (bool)preg_match('/<meta[^>]+name=["\']viewport["\']/i', $html);
            $result['has_phone']          = (bool)preg_match('/href=["\']tel:/i', $html) || (bool)preg_match('/(\+40|0[237]\d{8}|07\d{8})/', $html);
            $result['has_whatsapp']       = str_contains($l, 'wa.me') || str_contains($l, 'whatsapp.com/send');
            $result['has_search']         = (bool)preg_match('/<input[^>]+type=["\']search["\']/i', $html) || str_contains($l, 'class="search') || str_contains($l, "class='search");
            $result['has_breadcrumb']     = str_contains($l, 'breadcrumb') || (bool)preg_match('/itemtype=["\']https?:\/\/schema\.org\/BreadcrumbList["\']/i', $html);
        } catch (\Exception $e) { Log::warning("checkUx failed: " . $e->getMessage()); }
        return $result;
    }

    private function checkStructuredData(string $html): array
    {
        $result = ['has_json_ld' => false, 'has_microdata' => false, 'schemas' => [], 'errors' => []];
        try {
            preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches);
            if (!empty($matches[1])) {
                $result['has_json_ld'] = true;
                foreach ($matches[1] as $raw) {
                    $decoded = json_decode(trim($raw), true);
                    if (json_last_error() !== JSON_ERROR_NONE) { $result['errors'][] = 'JSON-LD invalid: ' . json_last_error_msg(); continue; }
                    $items = isset($decoded['@graph']) ? $decoded['@graph'] : [$decoded];
                    foreach ($items as $item) {
                        $type = $item['@type'] ?? 'Unknown';
                        if (is_array($type)) $type = implode(', ', $type);
                        if (!in_array($type, $result['schemas'])) $result['schemas'][] = $type;
                    }
                }
            }
            $result['has_microdata'] = (bool)preg_match('/itemscope/i', $html);
        } catch (\Exception $e) { Log::warning("checkStructuredData failed: " . $e->getMessage()); }
        return $result;
    }

    private function checkOpenGraph(string $html): array
    {
        $result = ['has_og_title' => false, 'has_og_description' => false, 'has_og_image' => false, 'has_og_url' => false, 'has_og_type' => false, 'has_twitter_card' => false, 'og_image_url' => null, 'og_title' => null];
        try {
            $result['has_og_title']       = (bool)preg_match('/<meta[^>]+property=["\']og:title["\']/i', $html);
            $result['has_og_description'] = (bool)preg_match('/<meta[^>]+property=["\']og:description["\']/i', $html);
            $result['has_og_image']       = (bool)preg_match('/<meta[^>]+property=["\']og:image["\']/i', $html);
            $result['has_og_url']         = (bool)preg_match('/<meta[^>]+property=["\']og:url["\']/i', $html);
            $result['has_og_type']        = (bool)preg_match('/<meta[^>]+property=["\']og:type["\']/i', $html);
            $result['has_twitter_card']   = (bool)preg_match('/<meta[^>]+(name|property)=["\']twitter:card["\']/i', $html);
            if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\'](.*?)["\']/i', $html, $m)) $result['og_image_url'] = trim($m[1]);
            if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\'](.*?)["\']/i', $html, $m)) $result['og_title'] = trim($m[1]);
        } catch (\Exception $e) { Log::warning("checkOpenGraph failed: " . $e->getMessage()); }
        return $result;
    }

    private function checkSecurityHeaders(array $headers): array
    {
        $h = [];
        foreach ($headers as $key => $val) $h[strtolower($key)] = is_array($val) ? ($val[0] ?? '') : $val;
        $hsts     = $h['strict-transport-security'] ?? null;
        $csp      = $h['content-security-policy'] ?? null;
        $xframe   = $h['x-frame-options'] ?? null;
        $xcontent = $h['x-content-type-options'] ?? null;
        $referrer = $h['referrer-policy'] ?? null;
        return [
            'hsts'               => $hsts !== null,   'hsts_value'    => $hsts,
            'csp'                => $csp  !== null,   'csp_value'     => $csp ? substr($csp, 0, 120) : null,
            'x_frame_options'    => $xframe !== null, 'x_frame_value' => $xframe,
            'x_content_type'     => $xcontent !== null && strtolower((string)$xcontent) === 'nosniff',
            'referrer_policy'    => $referrer !== null,
            'score' => ($hsts?1:0) + ($csp?1:0) + ($xframe?1:0) + (($xcontent && strtolower((string)$xcontent)==='nosniff')?1:0) + ($referrer?1:0),
        ];
    }

    private function checkTracking(string $html): array
    {
        $result = ['has_ga4' => false, 'has_ua' => false, 'has_gtm' => false, 'has_meta_pixel' => false, 'has_tiktok_pixel' => false, 'has_hotjar' => false, 'double_ga4' => false, 'ga4_ids' => [], 'gtm_ids' => []];
        try {
            $l = strtolower($html);
            preg_match_all('/[\'"]?(G-[A-Z0-9]{6,12})[\'"]?/i', $html, $ga4M);
            $ga4Ids = array_unique($ga4M[1] ?? []);
            $result['has_ga4'] = !empty($ga4Ids); $result['ga4_ids'] = $ga4Ids; $result['double_ga4'] = count($ga4Ids) > 1;
            $result['has_ua']  = (bool)preg_match('/UA-\d{4,}-\d+/i', $html);
            preg_match_all('/GTM-[A-Z0-9]{4,8}/i', $html, $gtmM);
            $gtmIds = array_unique($gtmM[0] ?? []);
            $result['has_gtm'] = !empty($gtmIds); $result['gtm_ids'] = $gtmIds;
            $result['has_meta_pixel']   = str_contains($l, 'fbevents.js') || (bool)preg_match('/fbq\s*\(\s*[\'"]init[\'"]/i', $html);
            $result['has_tiktok_pixel'] = str_contains($l, 'analytics.tiktok.com') || (bool)preg_match('/ttq\s*\.\s*load\s*\(/i', $html);
            $result['has_hotjar']       = str_contains($l, 'static.hotjar.com') || (bool)preg_match('/hjid\s*[=:]/i', $html);
        } catch (\Exception $e) { Log::warning("checkTracking failed: " . $e->getMessage()); }
        return $result;
    }

    /**
     * Convertește un string la UTF-8 valid.
     * Gestionează site-uri cu encoding Windows-1252, ISO-8859-1 sau mixt.
     */
    private function toUtf8(string $html): string
    {
        // Detectează charset din meta tag
        if (preg_match('/charset=["\']?\s*([\w-]+)/i', $html, $m)) {
            $declared = strtolower(trim($m[1]));
            if (!in_array($declared, ['utf-8', 'utf8'])) {
                $converted = @mb_convert_encoding($html, 'UTF-8', $declared);
                if ($converted !== false) {
                    return $converted;
                }
            }
        }

        // Dacă nu e UTF-8 valid, încearcă Windows-1252
        if (!mb_detect_encoding($html, 'UTF-8', true)) {
            $html = @mb_convert_encoding($html, 'UTF-8', 'Windows-1252') ?: $html;
        }

        // Curăță orice byte invalid rămas
        return mb_convert_encoding($html, 'UTF-8', 'UTF-8');
    }
}