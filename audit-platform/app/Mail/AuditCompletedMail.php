<?php
namespace App\Mail;

use App\Models\Audit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuditCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Audit $audit) {}

    public function envelope(): Envelope
    {
        $score = $this->audit->score_total ?? 0;
        $emoji = $score >= 80 ? '✅' : ($score >= 50 ? '⚠️' : '🔴');
        return new Envelope(
            subject: "{$emoji} Raportul tău audit este gata — {$this->audit->url}",
        );
    }

    public function content(): Content
    {
        $audit    = $this->audit;
        $issues   = $audit->issues;
        $score    = $audit->score_total ?? 0;
        $critical = $issues->where('severity', 'critical')->count();
        $warnings = $issues->where('severity', 'warning')->count();

        // Quick Wins — aceeași logică ca în controller
        $effortScore = function($issue): int {
            $t = mb_strtolower($issue->title);
            if (str_contains($t, 'meta description'))  return 1;
            if (str_contains($t, 'canonical'))         return 1;
            if (str_contains($t, 'twitter'))           return 1;
            if (str_contains($t, 'x-frame'))           return 1;
            if (str_contains($t, 'imagini fără'))      return 1;
            if (str_contains($t, 'title prea'))        return 1;
            if (str_contains($t, 'robots.txt'))        return 1;
            if (str_contains($t, 'open graph'))        return 2;
            if (str_contains($t, 'sitemap'))           return 2;
            if (str_contains($t, 'cookie'))            return 2;
            if (str_contains($t, 'google analytics'))  return 2;
            if (str_contains($t, 'lcp'))               return 4;
            if (str_contains($t, 'cls'))               return 4;
            if (str_contains($t, 'mobile'))            return 4;
            return 3;
        };

        $quickWins = $issues
            ->filter(fn($i) => $i->severity !== 'info')
            ->map(fn($i) => [
                'issue' => $i,
                'score' => round(
                    match($i->severity) { 'critical'=>3, 'warning'=>2, default=>1 }
                    / $effortScore($i),
                    2
                ),
            ])
            ->sortByDesc('score')
            ->take(3)
            ->values();

        // CWV
        $rawData  = $audit->raw_data ?? [];
        $psMobile = $rawData['pagespeed']['mobile'] ?? [];

        return new Content(
            view: 'emails.audit-completed-html',
            with: [
                'audit'       => $audit,
                'reportUrl'   => route('audit.report', $audit->public_token),
                'score'       => $score,
                'critical'    => $critical,
                'warnings'    => $warnings,
                'quickWins'   => $quickWins,
                'categories'  => [
                    'Tehnic'   => $audit->score_technical ?? 0,
                    'SEO'      => $audit->score_seo ?? 0,
                    'Legal'    => $audit->score_legal ?? 0,
                    'E-E-A-T'  => $audit->score_eeeat ?? 0,
                    'Continut' => $audit->score_content ?? 0,
                    'UX'       => $audit->score_ux ?? 0,
                ],
                'mobilePerfScore' => $psMobile['score'] ?? null,
                'lcp'             => $psMobile['lcp']   ?? null,
                'cls'             => $psMobile['cls']   ?? null,
            ]
        );
    }
}