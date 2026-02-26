<?php

namespace App\Services;

use App\Models\Audit;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AuditSummaryService
{
    public function generate(Audit $audit): string
    {
        try {
            $issues = $audit->issues()->get();

            if ($issues->isEmpty()) {
                return 'Nu au fost identificate probleme semnificative. Site-ul are o stare generala buna.';
            }

            // Build issues list for prompt
            $issuesList = '';
            foreach ($issues as $issue) {
                $sevLabel = match($issue->severity) {
                    'critical' => '[CRITIC]',
                    'warning'  => '[AVERTISMENT]',
                    default    => '[INFO]',
                };
                $issuesList .= "{$sevLabel} {$issue->title}: {$issue->description}\n";
                if ($issue->suggestion) {
                    $issuesList .= "  Solutie sugerata: {$issue->suggestion}\n";
                }
            }

            $score    = $audit->score_total ?? 0;
            $critical = $issues->where('severity', 'critical')->count();
            $warnings = $issues->where('severity', 'warning')->count();

            $prompt = <<<PROMPT
Esti un consultant expert in optimizarea site-urilor web pentru piata romaneasca.

Analizeaza urmatoarele probleme identificate pe site-ul {$audit->url} (scor general: {$score}/100, {$critical} probleme critice, {$warnings} avertismente) si genereaza:

1. Un rezumat executiv (2-3 propozitii) al starii generale a site-ului
2. Top 3 prioritati imediate pe care proprietarul trebuie sa le rezolve urgent
3. Un plan de actiune pe termen scurt (1-4 saptamani) cu pasi concreti

PROBLEME IDENTIFICATE:
{$issuesList}

INSTRUCTIUNI:
- Scrie in romana clara si profesionala, fara jargon tehnic excesiv
- Fii specific si practic, nu generic
- Mentioneaza impactul real al problemelor (pe vanzari, pe Google, pe clienti)
- Nu folosi bullet points, scrie in paragrafe clare
- Maxim 300 de cuvinte total
- NU mentiona GPT, AI sau modele lingvistice in text
PROMPT;

            $response = OpenAI::chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.4,
                'max_tokens'  => 600,
            ]);

            return $response->choices[0]->message->content ?? '';

        } catch (\Exception $e) {
            Log::error('AuditSummaryService failed: ' . $e->getMessage());
            return '';
        }
    }

    public function generateWithSteps(Audit $audit): array
    {
        try {
            $issues = $audit->issues()->get();

            if ($issues->isEmpty()) {
                return [];
            }

            // Build issues for step generation
            $issuesList = [];
            foreach ($issues as $issue) {
                $issuesList[] = [
                    'id'       => $issue->id,
                    'title'    => $issue->title,
                    'category' => $issue->category,
                    'severity' => $issue->severity,
                    'description' => $issue->description,
                    'suggestion'  => $issue->suggestion,
                ];
            }

            $issuesJson = json_encode($issuesList, JSON_UNESCAPED_UNICODE);

            $prompt = <<<PROMPT
Esti un specialist tehnic web cu experienta in Romania.

Pentru fiecare problema din lista de mai jos, genereaza 3-5 pasi concreti de implementare pe care un webmaster sau programator sa ii urmeze exact.

PROBLEME:
{$issuesJson}

Returneaza DOAR un JSON valid cu urmatoarea structura:
{"steps": {"<issue_id>": ["Pasul 1 detaliat", "Pasul 2 detaliat", "Pasul 3 detaliat"]}}

Fiecare pas trebuie sa fie:
- Concret si actionabil, nu vag
- Scris in romana
- Specific tehnic (nu generic)
- Maxim 100 de caractere
PROMPT;

            $response = OpenAI::chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.2,
                'max_tokens'  => 1500,
            ]);

            $content = $response->choices[0]->message->content ?? '{}';
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $decoded = json_decode(trim($content), true);

            return $decoded['steps'] ?? [];

        } catch (\Exception $e) {
            Log::error('AuditSummaryService::generateWithSteps failed: ' . $e->getMessage());
            return [];
        }
    }
}