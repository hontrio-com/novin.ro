<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $audits = Audit::where('user_id', $user->id)
            ->with(['issues', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $completed = $audits->where('status', 'completed');

        $stats = [
            'total'      => $audits->count(),
            'avg_score'  => round($completed->avg('score_total') ?? 0),
            'critical'   => $audits->sum(fn($a) => $a->issues->where('severity', 'critical')->count()),
            'last_audit' => $audits->first()?->created_at?->diffForHumans() ?? 'Niciodată',
            'best_score' => $completed->max('score_total') ?? 0,
            'improved'   => $completed->count() >= 2
                ? ($completed->first()->score_total - $completed->skip(1)->first()->score_total)
                : null,
        ];

        // Evoluție scoruri — ultimele 8 audituri completate (cronologic)
        $trend = $completed
            ->sortBy('created_at')
            ->values()
            ->slice(-8)
            ->map(fn($a) => [
                'date'  => $a->created_at->format('d.m'),
                'score' => $a->score_total ?? 0,
                'url'   => $a->url,
                'token' => $a->public_token,
            ])
            ->values();

        // Comparație ultimele 2 audituri completate
        $latest  = $completed->first();
        $prev    = $completed->skip(1)->first();
        $compare = null;
        if ($latest && $prev) {
            $cats = ['technical','seo','legal','eeeat','content','ux'];
            $labels = ['technical'=>'Tehnic','seo'=>'SEO','legal'=>'Legal','eeeat'=>'E-E-A-T','content'=>'Conținut','ux'=>'UX'];
            $compare = collect($cats)->map(fn($k) => [
                'key'    => $k,
                'label'  => $labels[$k],
                'latest' => $latest->{"score_{$k}"} ?? 0,
                'prev'   => $prev->{"score_{$k}"}   ?? 0,
                'delta'  => ($latest->{"score_{$k}"} ?? 0) - ($prev->{"score_{$k}"} ?? 0),
            ])->values();
        }

        // Quick Wins din cel mai recent audit
        $quickWins = collect();
        if ($latest) {
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
                if (str_contains($t, 'google analytics'))  return 2;
                if (str_contains($t, 'lcp'))               return 4;
                if (str_contains($t, 'mobile'))            return 4;
                return 3;
            };
            $quickWins = $latest->issues
                ->filter(fn($i) => $i->severity !== 'info')
                ->map(fn($i) => [
                    'issue' => $i,
                    'score' => round(
                        match($i->severity) {'critical'=>3,'warning'=>2,default=>1}
                        / $effortScore($i), 2
                    ),
                ])
                ->sortByDesc('score')
                ->take(3)
                ->values();
        }

        return view('dashboard.index', compact(
            'user', 'audits', 'stats', 'trend', 'compare', 'latest', 'prev', 'quickWins'
        ));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('dashboard.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        Auth::user()->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profilul a fost actualizat.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'  => 'required',
            'password'          => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Parola curenta este incorecta.']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Parola a fost schimbata cu succes.');
    }

    public function auditDetail(string $token)
    {
        $audit = Audit::where('public_token', $token)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with(['issues', 'pageData', 'payment'])
            ->firstOrFail();

        $issuesByCategory = $audit->issues->groupBy('category');
        $critical = $audit->issues->where('severity', 'critical')->count();
        $warnings = $audit->issues->where('severity', 'warning')->count();
        $info     = $audit->issues->where('severity', 'info')->count();

        return view('audit.report', compact('audit', 'issuesByCategory', 'critical', 'warnings', 'info'));
    }
}