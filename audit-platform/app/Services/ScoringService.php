<?php
namespace App\Services;

use App\Models\Audit;

class ScoringService
{
    public function calculate(Audit $audit): array
    {
        $issues = $audit->issues;

        // Calculează scorul per categorie
        $scores = [
            'technical' => $this->scoreCategory($issues, 'technical'),
            'seo'       => $this->scoreCategory($issues, 'seo'),
            'legal'     => $this->scoreCategory($issues, 'legal'),
            'eeeat'     => $this->scoreCategory($issues, 'eeeat'),
            'content'   => $this->scoreCategory($issues, 'content'),
            'ux'        => $this->scoreCategory($issues, 'ux'),
        ];

        // Scor total = media ponderată
        $scores['total'] = (int)(
            $scores['technical'] * 0.20 +
            $scores['seo']       * 0.25 +
            $scores['legal']     * 0.20 +
            $scores['eeeat']     * 0.15 +
            $scores['content']   * 0.15 +
            $scores['ux']        * 0.05
        );

        return $scores;
    }

    private function scoreCategory($issues, string $category): int
    {
        $categoryIssues = $issues->where('category', $category);

        if ($categoryIssues->isEmpty()) {
            return 95; // Nicio problemă = scor aproape perfect
        }

        $score = 100;

        foreach ($categoryIssues as $issue) {
            $score -= match($issue->severity) {
                'critical' => 25,
                'warning'  => 12,
                'info'     => 5,
                default    => 0,
            };
        }

        return max(0, min(100, $score));
    }
}