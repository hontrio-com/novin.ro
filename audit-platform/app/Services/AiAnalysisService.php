<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AiAnalysisService
{
    public function analyze(array $crawlData): array
    {
        $issues = [];

        try {
            $mainPage = $crawlData['pages'][0] ?? null;
            if (!$mainPage || empty($mainPage['visible_text'])) {
                return $issues;
            }

            $url  = $mainPage['url'];
            $text = substr($mainPage['visible_text'], 0, 2500);
            $title = $mainPage['title'] ?? 'Nedefinit';
            $h1    = $mainPage['h1'] ?? 'Lipsă';
            $meta  = $mainPage['meta_description'] ?? 'Lipsă';

            $prompt = <<<PROMPT
Ești un expert în SEO, UX și copywriting pentru piața românească.
Analizează conținutul acestui website și identifică problemele concrete.

URL: {$url}
Title: {$title}
H1: {$h1}
Meta Description: {$meta}

Text vizibil de pe pagina principală:
{$text}

Returnează DOAR un JSON valid cu array-ul "issues". Fiecare issue are:
- category: "content" sau "eeeat"
- severity: "critical", "warning" sau "info"
- title: titlu scurt în română (max 60 caractere)
- description: explicație clară în română (max 200 caractere)
- suggestion: ce trebuie făcut concret în română (max 200 caractere)

Exemplu format răspuns:
{"issues":[{"category":"content","severity":"warning","title":"CTA-uri generice","description":"Butoanele de acțiune sunt vagi.","suggestion":"Înlocuiește cu CTA-uri specifice."}]}
PROMPT;

            $response = OpenAI::chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.3,
                'max_tokens'  => 1500,
            ]);

            $content = $response->choices[0]->message->content ?? '';
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $content = trim($content);
            $decoded = json_decode($content, true);

            if (isset($decoded['issues']) && is_array($decoded['issues'])) {
                foreach ($decoded['issues'] as $issue) {
                    if (isset($issue['category'], $issue['severity'], $issue['title'], $issue['description'])) {
                        $issues[] = [
                            'category'    => in_array($issue['category'], ['content', 'eeeat']) ? $issue['category'] : 'content',
                            'severity'    => in_array($issue['severity'], ['critical', 'warning', 'info']) ? $issue['severity'] : 'warning',
                            'title'       => substr($issue['title'], 0, 100),
                            'description' => substr($issue['description'], 0, 500),
                            'suggestion'  => substr($issue['suggestion'] ?? '', 0, 500),
                        ];
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error("AiAnalysisService failed: " . $e->getMessage());
        }

        return $issues;
    }
}