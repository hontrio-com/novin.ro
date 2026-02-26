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
            $summary = $audit->ai_summary ?? $this->summaryService->generate($audit);

            // Generate per-issue steps
            $stepsMap = $this->summaryService->generateWithSteps($audit);

            // Build issue data with steps
            $issuesData = [];
            foreach ($issues as $issue) {
                $issuesData[] = [
                    'id'           => $issue->id,
                    'category'     => $issue->category,
                    'severity'     => $issue->severity,
                    'title'        => $issue->title,
                    'description'  => $issue->description,
                    'suggestion'   => $issue->suggestion ?? '',
                    'affected_url' => $issue->affected_url ?? '',
                    'steps'        => $stepsMap[(string)$issue->id] ?? [],
                ];
            }

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
                'issues'  => $issuesData,
                'summary' => $summary,
            ];

            // Write JSON to temp file
            $tmpJson = sys_get_temp_dir() . '/audit_' . $audit->id . '_' . time() . '.json';
            $tmpPdf  = sys_get_temp_dir() . '/audit_' . $audit->id . '_' . time() . '.pdf';
            file_put_contents($tmpJson, json_encode($data, JSON_UNESCAPED_UNICODE));

            // Python script path
            $scriptPath = base_path('scripts/generate_pdf.py');

            $cmd    = escapeshellcmd("python3 {$scriptPath} {$tmpJson} {$tmpPdf}");
            $output = shell_exec($cmd . ' 2>&1');

            if (!file_exists($tmpPdf) || filesize($tmpPdf) < 100) {
                Log::error("PDF generation failed. Output: {$output}");
                @unlink($tmpJson);
                return false;
            }

            // Store PDF
            $filename = 'pdf/audit_' . $audit->token . '.pdf';
            Storage::disk('public')->put($filename, file_get_contents($tmpPdf));

            // Cleanup
            @unlink($tmpJson);
            @unlink($tmpPdf);

            return Storage::disk('public')->url($filename);

        } catch (\Exception $e) {
            Log::error('PdfService::generate failed: ' . $e->getMessage());
            return false;
        }
    }
}