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

        return [
            'pages'        => $this->scrapePages(),
            'pagespeed'    => $this->checkPageSpeed(),
            'ssl'          => $this->checkSsl(),
            'broken_links' => $this->checkBrokenLinks(),
            'seo'          => $this->checkSeo(),
            'legal'        => $this->checkLegal(),
            'ux'           => $this->checkUx(),
        ];
    }

    private function scrapePages(): array
    {
        $pages = [];
        try {
            $start    = microtime(true);
            $response = Http::timeout(15)->get($this->url);
            $loadTime = (int)((microtime(true) - $start) * 1000);
            $html     = $response->body();
            $pages[]  = $this->parsePage($this->url, $html, $response->status(), $loadTime);

            $internalLinks = $this->extractInternalLinks($html, $this->url);
            foreach (array_slice($internalLinks, 0, 4) as $link) {
                try {
                    $start    = microtime(true);
                    $resp     = Http::timeout(10)->get($link);
                    $loadTime = (int)((microtime(true) - $start) * 1000);
                    $pages[]  = $this->parsePage($link, $resp->body(), $resp->status(), $loadTime);
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
            'visible_text'       => substr($visibleText, 0, 3000),
            'raw_html'           => substr($html, 0, 50000),
        ];
    }

    private function extractVisibleText(string $html): string
    {
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
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
        $result    = ['mobile' => ['score' => 50], 'desktop' => ['score' => 50]];
        $apiKey    = env('GOOGLE_PAGESPEED_KEY', '');
        $encodedUrl = urlencode($this->url);

        foreach (['mobile', 'desktop'] as $strategy) {
            try {
                $apiUrl   = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$encodedUrl}&strategy={$strategy}" . ($apiKey ? "&key={$apiKey}" : '');
                $response = Http::timeout(30)->get($apiUrl);

                if ($response->successful()) {
                    $data  = $response->json();
                    $score = (int)(($data['lighthouseResult']['categories']['performance']['score'] ?? 0.5) * 100);
                    $result[$strategy] = [
                        'score' => $score,
                        'fcp'   => $data['lighthouseResult']['audits']['first-contentful-paint']['displayValue'] ?? null,
                        'lcp'   => $data['lighthouseResult']['audits']['largest-contentful-paint']['displayValue'] ?? null,
                        'cls'   => $data['lighthouseResult']['audits']['cumulative-layout-shift']['displayValue'] ?? null,
                        'tbt'   => $data['lighthouseResult']['audits']['total-blocking-time']['displayValue'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("PageSpeed check failed for {$strategy}: " . $e->getMessage());
            }
        }
        return $result;
    }

    private function checkSsl(): array
    {
        $parsed = parse_url($this->url);
        $host   = $parsed['host'] ?? '';
        $result = ['valid' => false, 'days_left' => 0, 'issuer' => null];

        if (!$host) return $result;

        try {
            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true, 'verify_peer' => false, 'verify_peer_name' => false]]);
            $socket  = @stream_socket_client("ssl://{$host}:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);

            if ($socket) {
                $params   = stream_context_get_params($socket);
                $cert     = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                $validTo  = $cert['validTo_time_t'] ?? 0;
                $daysLeft = (int)(($validTo - time()) / 86400);
                $result   = ['valid' => $daysLeft > 0, 'days_left' => max(0, $daysLeft), 'issuer' => $cert['issuer']['O'] ?? null];
                fclose($socket);
            }
        } catch (\Exception $e) {
            Log::warning("SSL check failed: " . $e->getMessage());
        }
        return $result;
    }

    private function checkBrokenLinks(): array
    {
        $broken = [];
        try {
            $response = Http::timeout(15)->get($this->url);
            $html     = $response->body();
            preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $html, $matches);

            $parsed  = parse_url($this->url);
            $base    = $parsed['scheme'] . '://' . $parsed['host'];
            $checked = [];

            foreach (array_slice($matches[1], 0, 30) as $href) {
                if (str_starts_with($href, 'http')) {
                    $checkUrl = strtok($href, '#');
                } elseif (str_starts_with($href, '/')) {
                    $checkUrl = $base . strtok($href, '#');
                } else {
                    continue;
                }

                if (in_array($checkUrl, $checked)) continue;
                $checked[] = $checkUrl;

                try {
                    $resp = Http::timeout(8)->head($checkUrl);
                    if ($resp->status() === 404) $broken[] = $checkUrl;
                } catch (\Exception $e) {}
            }
        } catch (\Exception $e) {
            Log::warning("checkBrokenLinks failed: " . $e->getMessage());
        }
        return $broken;
    }

    private function checkSeo(): array
    {
        $result = ['missing_meta_description' => [], 'missing_h1' => [], 'duplicate_h1' => [], 'has_sitemap' => false, 'has_robots' => false];

        try {
            $response = Http::timeout(15)->get($this->url);
            $html     = $response->body();

            if (!preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']\S/i', $html)) {
                $result['missing_meta_description'][] = $this->url;
            }

            preg_match_all('/<h1[^>]*>/i', $html, $h1Matches);
            $h1Count = count($h1Matches[0]);
            if ($h1Count === 0) $result['missing_h1'][] = $this->url;
            elseif ($h1Count > 1) $result['duplicate_h1'][] = $this->url;

            $parsed  = parse_url($this->url);
            $base    = $parsed['scheme'] . '://' . $parsed['host'];
            $sitemap = Http::timeout(8)->get($base . '/sitemap.xml');
            $result['has_sitemap'] = $sitemap->successful();

            $robots = Http::timeout(8)->get($base . '/robots.txt');
            $result['has_robots'] = $robots->successful() && str_contains($robots->body(), 'User-agent');
        } catch (\Exception $e) {
            Log::warning("checkSeo failed: " . $e->getMessage());
        }
        return $result;
    }

   private function checkLegal(): array
{
    $result = ['has_anpc' => false, 'has_company_details' => false, 'has_gdpr_policy' => false, 'has_cookie_banner' => false];

    try {
        $response = Http::timeout(15)->get($this->url);
        $html     = $response->body();
        $htmlLower = strtolower($html);

        // ANPC — caută link-uri sau imagini cu referință spre anpc.ro
        $result['has_anpc'] = str_contains($htmlLower, 'anpc.ro') ||
                              str_contains($htmlLower, 'sal.anpc') ||
                              preg_match('/href=["\'][^"\']*anpc/i', $html) ||
                              preg_match('/src=["\'][^"\']*anpc/i', $html) ||
                              preg_match('/alt=["\'][^"\']*anpc/i', $html);

        $result['has_company_details'] = preg_match('/cui\s*:?\s*\d{6,10}/i', $html) || 
                                         preg_match('/j\d{2}\/\d+\/\d{4}/i', $html);
        
        $result['has_gdpr_policy']   = str_contains($htmlLower, 'confidentialitate') || 
                                        str_contains($htmlLower, 'gdpr') || 
                                        str_contains($htmlLower, 'privacy');
        
        $result['has_cookie_banner'] = str_contains($htmlLower, 'cookie') && 
                                        (str_contains($htmlLower, 'accept') || str_contains($htmlLower, 'acept'));
    } catch (\Exception $e) {
        Log::warning("checkLegal failed: " . $e->getMessage());
    }
    return $result;
}

    private function checkUx(): array
    {
        $result = ['is_mobile_friendly' => true];
        try {
            $response = Http::timeout(15)->get($this->url);
            $html     = $response->body();
            $result['is_mobile_friendly'] = (bool)preg_match('/<meta[^>]+name=["\']viewport["\']/i', $html);
        } catch (\Exception $e) {
            Log::warning("checkUx failed: " . $e->getMessage());
        }
        return $result;
    }
}