<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ── Overview ────────────────────────────────────────────
    public function index()
    {
        $totalAudits     = Audit::count();
        $completedAudits = Audit::where('status','completed')->count();
        $failedAudits    = Audit::where('status','failed')->count();
        $totalUsers      = User::count();
        $avgScore        = round(Audit::where('status','completed')->avg('score_total') ?? 0);

        // ── FINANCIAR ──────────────────────────────────────
        // Venit total
        $totalRevenue = \App\Models\Payment::where('status','paid')->sum('amount') / 100;

        // Venit luna aceasta
        $revenueThisMonth = \App\Models\Payment::where('status','paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount') / 100;

        // Venit luna trecuta
        $revenueLastMonth = \App\Models\Payment::where('status','paid')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount') / 100;

        // Variatie luna aceasta vs luna trecuta
        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : null;

        // Plati luna aceasta
        $paymentsThisMonth = \App\Models\Payment::where('status','paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->count();

        // Venit pe ultimele 6 luni
        $revenueByMonth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $rev = \App\Models\Payment::where('status','paid')
                ->whereMonth('paid_at', $month->month)
                ->whereYear('paid_at', $month->year)
                ->sum('amount') / 100;
            $revenueByMonth->put($month->format('M y'), $rev);
        }

        // Ultimele plati
        $recentPayments = \App\Models\Payment::where('status','paid')
            ->with('audit')
            ->orderBy('paid_at','desc')
            ->take(5)
            ->get();

        // ── AUDITURI ───────────────────────────────────────
        $auditsByDay = Audit::selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day');

        $last30 = collect();
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $last30->put($day, $auditsByDay->get($day, 0));
        }

        $scoreDist = [
            'bun'    => Audit::where('status','completed')->where('score_total','>=',80)->count(),
            'mediu'  => Audit::where('status','completed')->whereBetween('score_total',[50,79])->count(),
            'slab'   => Audit::where('status','completed')->where('score_total','<',50)->count(),
        ];

        $recentAudits = Audit::with(['issues','payment'])
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();

        $newUsers = User::where('created_at','>=',now()->subDays(7))->count();

        return view('admin.index', compact(
            'totalAudits','completedAudits','failedAudits','totalUsers',
            'avgScore','last30','scoreDist','recentAudits','newUsers',
            'totalRevenue','revenueThisMonth','revenueLastMonth','revenueGrowth',
            'paymentsThisMonth','revenueByMonth','recentPayments'
        ));
    }

    // ── Toate auditurile ────────────────────────────────────
    public function audits(Request $request)
    {
        $query = Audit::with(['issues','payment','user'])->orderBy('created_at','desc');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($w) => $w->where('url','like',"%{$q}%")->orWhere('email','like',"%{$q}%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('score')) {
            match($request->score) {
                'bun'   => $query->where('score_total','>=',80),
                'mediu' => $query->whereBetween('score_total',[50,79]),
                'slab'  => $query->where('score_total','<',50),
                default => null,
            };
        }

        $audits = $query->paginate(25)->withQueryString();

        $stats = [
            'total'     => Audit::count(),
            'completed' => Audit::where('status','completed')->count(),
            'pending'   => Audit::whereIn('status',['pending','processing'])->count(),
            'failed'    => Audit::where('status','failed')->count(),
        ];

        return view('admin.audits', compact('audits','stats'));
    }

    // ── Delete audit ─────────────────────────────────────────
    public function deleteAudit(Audit $audit)
    {
        $audit->delete();
        return back()->with('success', 'Auditul a fost șters.');
    }

    // ── Toți userii ──────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::withCount('audits')->orderBy('created_at','desc');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($w) => $w->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%"));
        }
        if ($request->filled('role')) {
            $query->where('is_admin', $request->role === 'admin');
        }

        $users = $query->paginate(25)->withQueryString();

        $stats = [
            'total'  => User::count(),
            'admins' => User::where('is_admin',true)->count(),
            'new'    => User::where('created_at','>=',now()->subDays(7))->count(),
        ];

        return view('admin.users', compact('users','stats'));
    }

    // ── Toggle admin ─────────────────────────────────────────
    public function toggleAdmin(User $user)
    {
        // Nu poți scoate propriul admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nu îți poți modifica propriile drepturi de admin.');
        }
        $user->update(['is_admin' => !$user->is_admin]);
        $label = $user->is_admin ? 'promovat admin' : 'retrogadat utilizator normal';
        return back()->with('success', "{$user->name} a fost {$label}.");
    }

    // ── Delete user ──────────────────────────────────────────
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nu îți poți șterge propriul cont din admin.');
        }
        $user->delete();
        return back()->with('success', 'Utilizatorul a fost șters.');
    }
}