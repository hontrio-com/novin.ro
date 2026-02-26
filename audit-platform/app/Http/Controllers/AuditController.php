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
        $request->validate([
            'url'   => 'required|url|max:255',
            'email' => 'required|email|max:255',
        ]);

        $url = rtrim($request->url, '/');

        $audit = Audit::create([
            'url'    => $url,
            'email'  => $request->email,
            'status' => 'pending',
        ]);

        // Daca utilizatorul e logat, leaga auditul de contul sau
        if (Auth::check()) {
            $audit->update(['user_id' => Auth::id()]);
        }

        return redirect()->route('audit.checkout', $audit);
    }

    public function progress(Audit $audit)
    {
        if (!$audit->payment || !$audit->payment->isPaid()) {
            return redirect()->route('audit.checkout', $audit);
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

        return view('audit.report', compact(
            'audit', 'issuesByCategory', 'critical', 'warnings', 'info'
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