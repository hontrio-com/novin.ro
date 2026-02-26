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

        $stats = [
            'total'    => $audits->count(),
            'avg_score'=> $audits->where('status', 'completed')->avg('score_total') ?? 0,
            'critical' => $audits->sum(fn($a) => $a->issues->where('severity', 'critical')->count()),
            'last_audit'=> $audits->first()?->created_at?->diffForHumans() ?? 'Niciodata',
        ];

        return view('dashboard.index', compact('user', 'audits', 'stats'));
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