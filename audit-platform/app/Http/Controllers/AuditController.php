<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Jobs\RunAuditJob;
use App\Services\AccountService;
use App\Services\AuditSummaryService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuditController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function start(Request $request)
    {
        // Auto-prepend https:// BEFORE validation so bare domains pass the 'url' rule
        if ($request->filled('url')) {
            $raw = trim($request->input('url'));
            if (!preg_match('#^https?://#i', $raw)) {
                $request->merge(['url' => 'https://' . $raw]);
            }
        }

        $request->validate([
            'url'   => ['required', 'url', 'max:255',
                // Blochează URL-uri interne / localhost / IP-uri private
                function ($attribute, $value, $fail) {
                    $host = parse_url($value, PHP_URL_HOST);
                    if (!$host) { $fail('URL invalid.'); return; }
                    // Blochează localhost și IP-uri private
                    $blocked = ['localhost', '127.0.0.1', '0.0.0.0', '::1'];
                    if (in_array(strtolower($host), $blocked)) {
                        $fail('Acest URL nu este permis.');
                        return;
                    }
                    // Blochează IP-uri private (10.x, 172.16-31.x, 192.168.x)
                    if (filter_var($host, FILTER_VALIDATE_IP)) {
                        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                            $fail('Adresele IP private nu sunt permise.');
                        }
                    }
                }
            ],
            'email' => 'required|email:rfc,dns|max:255',
        ]);

        $url = rtrim(strtolower(trim($request->url)), '/');

        $audit = Audit::create([
            'url'    => $url,
            'email'  => $request->email,
            'status' => 'pending',
        ]);

        // Daca utilizatorul e logat, leaga auditul de contul sau
        if (Auth::check()) {
            $audit->update(['user_id' => Auth::id()]);
        }

        // În local — sari peste Stripe, pornește auditul direct
        if (app()->environment('local')) {
            \App\Models\Payment::create([
                'audit_id'              => $audit->id,
                'amount'                => 20000,
                'currency'              => 'RON',
                'status'                => 'paid',
                'paid_at'               => now(),
                'stripe_session_id'     => 'test_bypass_' . $audit->id,
                'stripe_payment_intent' => 'test_pi_' . $audit->id,
            ]);
            $audit->update(['status' => 'processing']);
            \App\Jobs\RunAuditJob::dispatch($audit);
            return redirect()->route('audit.progress', $audit);
        }

        return redirect()->route('audit.checkout', $audit);
    }

    public function progress(Audit $audit)
    {
        // În local — permite accesul direct fără verificare plată
        if (!app()->environment('local')) {
            if (!$audit->payment || !$audit->payment->isPaid()) {
                return redirect()->route('audit.checkout', $audit);
            }
        }

        if ($audit->status === 'completed') {
            return redirect()->route('audit.report', $audit->public_token);
        }

        return view('audit.progress', compact('audit'));
    }

    public function status(int $id)
    {
        $audit = Audit::find($id);

        if (!$audit) {
            return response()->json(['status' => 'not_found', 'redirect' => null]);
        }

        // Cand auditul e completat, creeaza cont automat pentru client
        if ($audit->status === 'completed' && !$audit->user_id) {
            try {
                $accountService = app(AccountService::class);
                $accountService->createOrAttach($audit);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Auto account creation failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'   => $audit->status,
            'redirect' => $audit->status === 'completed' && $audit->public_token
                ? route('audit.report', $audit->public_token)
                : null,
        ]);
    }

    public function summary(int $id)
    {
        $audit = Audit::find($id);

        if (!$audit) {
            return response()->json(['summary' => '']);
        }

        // Daca exista deja, returneaza din cache
        if ($audit->ai_summary && $audit->ai_summary_generated_at) {
            return response()->json(['summary' => $audit->ai_summary]);
        }

        // Genereaza si salveaza
        $summaryService = app(AuditSummaryService::class);
        $summary = $summaryService->generate($audit);

        if ($summary) {
            $audit->update([
                'ai_summary'              => $summary,
                'ai_summary_generated_at' => now(),
            ]);
        }

        return response()->json(['summary' => $summary]);
    }

    public function report(string $token)
    {
        $audit = Audit::where('public_token', $token)
            ->where('status', 'completed')
            ->with(['issues', 'pageData', 'payment'])
            ->firstOrFail();

        $issuesByCategory = $audit->issues->groupBy('category');
        $critical = $audit->issues->where('severity', 'critical')->count();
        $warnings = $audit->issues->where('severity', 'warning')->count();
        $info     = $audit->issues->where('severity', 'info')->count();

        // ── Quick Wins ────────────────────────────────────────
        // Scor efort: cât de ușor e de rezolvat (mic = ușor)
        // Scor impact: cât de mult afectează (mare = important)
        // Quick Win = impact mare + efort mic
        $effortScore = function($issue): int {
            $title = mb_strtolower($issue->title);
            // Ușor de fix: meta tags, ALT, canonical, twitter card, headers HTTP
            if (str_contains($title, 'meta description'))  return 1;
            if (str_contains($title, 'canonical'))         return 1;
            if (str_contains($title, 'twitter'))           return 1;
            if (str_contains($title, 'x-frame'))           return 1;
            if (str_contains($title, 'x-content'))         return 1;
            if (str_contains($title, 'imagini fără'))      return 1;
            if (str_contains($title, 'title prea'))        return 1;
            if (str_contains($title, 'robots.txt'))        return 1;
            if (str_contains($title, 'număr de telefon'))  return 1;
            if (str_contains($title, 'open graph'))        return 2;
            if (str_contains($title, 'sitemap'))           return 2;
            if (str_contains($title, 'cookie'))            return 2;
            if (str_contains($title, 'google analytics'))  return 2;
            if (str_contains($title, 'compresie'))         return 2;
            if (str_contains($title, 'cache'))             return 2;
            if (str_contains($title, 'lazy'))              return 2;
            if (str_contains($title, 'webp'))              return 2;
            // Mediu: structured data, ssl, broken links
            if (str_contains($title, 'structured'))        return 3;
            if (str_contains($title, 'ssl'))               return 3;
            if (str_contains($title, 'link-uri rupte'))    return 3;
            // Dificil: CWV, mobile, TTFB, JS
            if (str_contains($title, 'lcp'))               return 4;
            if (str_contains($title, 'cls'))               return 4;
            if (str_contains($title, 'inp'))               return 4;
            if (str_contains($title, 'ttfb'))              return 4;
            if (str_contains($title, 'mobile'))            return 4;
            return 3;
        };

        $impactScore = function($issue): int {
            // Critical = impact 3, warning = 2, info = 1
            $base = match($issue->severity) {
                'critical' => 3,
                'warning'  => 2,
                default    => 1,
            };
            // Bonus pentru impact SEO + Conversie simultan
            $impacts = mb_strtolower($issue->impact ?? '');
            if (str_contains($impacts, 'seo') && str_contains($impacts, 'conversie')) return $base + 1;
            if (str_contains($impacts, 'seo')) return $base + 1;
            return $base;
        };

        // Calculează scorul Quick Win = impact / efort
        $quickWins = $audit->issues
            ->filter(fn($i) => $i->severity !== 'info' || $effortScore($i) <= 1)
            ->map(function($issue) use ($effortScore, $impactScore) {
                $effort = $effortScore($issue);
                $impact = $impactScore($issue);
                return [
                    'issue'  => $issue,
                    'effort' => $effort,
                    'impact' => $impact,
                    'score'  => round($impact / $effort, 2),
                ];
            })
            ->sortByDesc('score')
            ->take(3)
            ->values();

        return view('audit.report', compact(
            'audit', 'issuesByCategory', 'critical', 'warnings', 'info', 'quickWins'
        ));
    }

    public function downloadPdf(string $token)
    {
        $audit = Audit::where('public_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        $filename = 'pdf/audit_' . $token . '.pdf';
        if (Storage::disk('public')->exists($filename)) {
            return redirect(Storage::disk('public')->url($filename));
        }

        $pdfService = app(PdfService::class);
        $url = $pdfService->generate($audit);

        if ($url) {
            return redirect($url);
        }

        abort(500, 'PDF generation failed');
    }
}