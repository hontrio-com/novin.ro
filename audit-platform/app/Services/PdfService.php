<?php

namespace App\Services;

use App\Models\Audit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    protected AuditSummaryService $summaryService;

    public function __construct(AuditSummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    public function generate(Audit $audit): string|false
    {
        try {
            $issues  = $audit->issues()->get();
            $pages   = $audit->pageData()->get();
            $summary = $audit->ai_summary ?? $this->summaryService->generate($audit);

            // Build issue data
            $issuesData = $issues->map(fn($i) => [
                'id'           => $i->id,
                'category'     => $i->category,
                'severity'     => $i->severity,
                'title'        => $i->title,
                'description'  => $i->description,
                'suggestion'   => $i->suggestion ?? '',
                'affected_url' => $i->affected_url ?? '',
                'impact'       => $i->impact ?? '',
            ])->values()->toArray();

            // Build pages data
            $pagesData = $pages->map(fn($p) => [
                'url'                => $p->url,
                'page_type'          => $p->page_type ?? 'other',
                'status_code'        => $p->status_code,
                'load_time_ms'       => $p->load_time_ms,
                'title'              => $p->title,
                'images_total'       => $p->images_total ?? 0,
                'images_missing_alt' => $p->images_missing_alt ?? 0,
            ])->values()->toArray();

            // Build Quick Wins (same logic as AuditController)
            $effortScore = function($issue): int {
                $t = mb_strtolower($issue->title);
                if (str_contains($t, 'meta description'))  return 1;
                if (str_contains($t, 'canonical'))         return 1;
                if (str_contains($t, 'twitter'))           return 1;
                if (str_contains($t, 'x-frame'))           return 1;
                if (str_contains($t, 'x-content'))         return 1;
                if (str_contains($t, 'imagini fără'))      return 1;
                if (str_contains($t, 'title prea'))        return 1;
                if (str_contains($t, 'robots.txt'))        return 1;
                if (str_contains($t, 'număr de telefon'))  return 1;
                if (str_contains($t, 'open graph'))        return 2;
                if (str_contains($t, 'sitemap'))           return 2;
                if (str_contains($t, 'cookie'))            return 2;
                if (str_contains($t, 'google analytics'))  return 2;
                if (str_contains($t, 'compresie'))         return 2;
                if (str_contains($t, 'cache'))             return 2;
                if (str_contains($t, 'webp'))              return 2;
                if (str_contains($t, 'lcp'))               return 4;
                if (str_contains($t, 'cls'))               return 4;
                if (str_contains($t, 'inp'))               return 4;
                if (str_contains($t, 'ttfb'))              return 4;
                if (str_contains($t, 'mobile'))            return 4;
                return 3;
            };
            $impactScore = function($issue): int {
                $base = match($issue->severity) { 'critical' => 3, 'warning' => 2, default => 1 };
                $imp  = mb_strtolower($issue->impact ?? '');
                if (str_contains($imp, 'seo') && str_contains($imp, 'conversie')) return $base + 1;
                if (str_contains($imp, 'seo')) return $base + 1;
                return $base;
            };

            $quickWins = $issues
                ->filter(fn($i) => $i->severity !== 'info' || $effortScore($i) <= 1)
                ->map(fn($i) => [
                    'issue'  => $i,
                    'effort' => $effortScore($i),
                    'impact' => $impactScore($i),
                    'score'  => round($impactScore($i) / $effortScore($i), 2),
                ])
                ->sortByDesc('score')
                ->take(3)
                ->map(fn($qw) => [
                    'title'       => $qw['issue']->title,
                    'description' => $qw['issue']->description,
                    'severity'    => $qw['issue']->severity,
                    'impact'      => $qw['issue']->impact ?? '',
                    'effort'      => $qw['effort'],
                    'suggestion'  => $qw['issue']->suggestion ?? '',
                ])
                ->values()
                ->toArray();

            // CWV data
            $rawData   = $audit->raw_data ?? [];
            $psData    = $rawData['pagespeed'] ?? [];
            $psMobile  = $psData['mobile']  ?? [];
            $psDesktop = $psData['desktop'] ?? [];

            $cwvData = [
                'mobile_score'  => $psMobile['score']   ?? null,
                'desktop_score' => $psDesktop['score']  ?? null,
                'lcp'           => $psMobile['lcp']     ?? null,
                'lcp_ms'        => $psMobile['lcp_ms']  ?? null,
                'cls'           => $psMobile['cls']     ?? null,
                'cls_raw'       => $psMobile['cls_raw'] ?? null,
                'inp'           => $psMobile['inp']     ?? null,
                'inp_ms'        => $psMobile['inp_ms']  ?? null,
                'fcp'           => $psMobile['fcp']     ?? null,
                'ttfb'          => $psMobile['ttfb']    ?? null,
                'tbt'           => $psMobile['tbt']     ?? null,
            ];

            $data = [
                'audit' => [
                    'url'              => $audit->url,
                    'email'            => $audit->email,
                    'completed_at'     => $audit->completed_at
                        ? $audit->completed_at->format('d.m.Y, H:i')
                        : now()->format('d.m.Y, H:i'),
                    'score_total'      => $audit->score_total ?? 0,
                    'score_technical'  => $audit->score_technical ?? 0,
                    'score_seo'        => $audit->score_seo ?? 0,
                    'score_legal'      => $audit->score_legal ?? 0,
                    'score_eeeat'      => $audit->score_eeeat ?? 0,
                    'score_content'    => $audit->score_content ?? 0,
                    'score_ux'         => $audit->score_ux ?? 0,
                ],
                'issues'     => $issuesData,
                'pages'      => $pagesData,
                'quick_wins' => $quickWins,
                'cwv'        => $cwvData,
                'summary'    => $summary,
            ];

            $tmpJson = sys_get_temp_dir() . '/audit_' . $audit->id . '_' . time() . '.json';
            $tmpPdf  = sys_get_temp_dir() . '/audit_' . $audit->id . '_' . time() . '.pdf';
            file_put_contents($tmpJson, json_encode($data, JSON_UNESCAPED_UNICODE));

            $scriptPath = base_path('scripts/generate_pdf.py');
            $cmd        = escapeshellcmd("python3 {$scriptPath} {$tmpJson} {$tmpPdf}");
            $output     = shell_exec($cmd . ' 2>&1');

            if (!file_exists($tmpPdf) || filesize($tmpPdf) < 100) {
                Log::error("PDF generation failed. Output: {$output}");
                @unlink($tmpJson);
                return false;
            }

            $filename = 'pdf/audit_' . $audit->public_token . '.pdf';
            Storage::disk('public')->put($filename, file_get_contents($tmpPdf));
            @unlink($tmpJson);
            @unlink($tmpPdf);

            return Storage::disk('public')->url($filename);

        } catch (\Exception $e) {
            Log::error('PdfService::generate failed: ' . $e->getMessage());
            return false;
        }
    }
}