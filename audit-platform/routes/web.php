<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PaymentController;
use App\Models\Audit;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuditController::class, 'index'])->name('home');

// ── PUBLIC ──────────────────────────────────────────────────
Route::post('/audit/start', [AuditController::class, 'start'])->middleware('rate.audit')->name('audit.start');
Route::get('/audit/{audit}/checkout', [PaymentController::class, 'checkout'])->name('audit.checkout');
Route::get('/audit/{audit}/success', [PaymentController::class, 'success'])->name('audit.success');
Route::get('/audit/{audit}/progress', [AuditController::class, 'progress'])->name('audit.progress');
Route::get('/audit/{audit}/status', [AuditController::class, 'status'])->middleware('throttle:60,1')->name('audit.status');
Route::get('/audit/{id}/summary', [AuditController::class, 'summary'])->middleware('throttle:10,1')->name('audit.summary');
Route::get('/raport/{token}', [AuditController::class, 'report'])->name('audit.report');
Route::get('/raport/{token}/pdf', [AuditController::class, 'downloadPdf'])->name('audit.pdf');

// ── TEST BYPASS (doar local, fără Stripe) ────────────────────
if (app()->environment('local')) {
    Route::get('/audit/{audit}/test-bypass', function (Audit $audit) {
        // Simulează plata fără Stripe
        \App\Models\Payment::updateOrCreate(
            ['audit_id' => $audit->id],
            [
                'amount'   => 20000,
                'currency' => 'RON',
                'status'   => 'paid',
                'paid_at'  => now(),
                'stripe_session_id'      => 'test_bypass_' . $audit->id,
                'stripe_payment_intent'  => 'test_pi_' . $audit->id,
            ]
        );
        $audit->update(['status' => 'processing']);
        \App\Jobs\RunAuditJob::dispatch($audit);
        return redirect()->route('audit.progress', $audit)
            ->with('info', '🧪 Test mode: plata simulată, auditul a pornit.');
    })->name('audit.test-bypass');
}


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/setari', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::patch('/dashboard/profil', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::patch('/dashboard/parola', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');
    Route::get('/dashboard/audit/{token}', [DashboardController::class, 'auditDetail'])->name('dashboard.audit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ── SEO ───────────────────────────────────────────────────────────
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ── PAGINI LEGALE ─────────────────────────────────────────────
Route::get('/termeni-si-conditii',           fn() => view('legal.termeni'))->name('legal.termeni');
Route::get('/politica-de-confidentialitate', fn() => view('legal.confidentialitate'))->name('legal.confidentialitate');
Route::get('/politica-cookies',              fn() => view('legal.cookies'))->name('legal.cookies');

// ── ADMIN ────────────────────────────────────────────────────
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                          [AdminController::class, 'index'])->name('index');
    Route::get('/audituri',                  [AdminController::class, 'audits'])->name('audits');
    Route::delete('/audituri/{audit}',       [AdminController::class, 'deleteAudit'])->name('audits.delete');
    Route::get('/useri',                     [AdminController::class, 'users'])->name('users');
    Route::patch('/useri/{user}/toggle-admin',[AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/useri/{user}',           [AdminController::class, 'deleteUser'])->name('users.delete');
});

require __DIR__.'/auth.php';

// ── GOOGLE OAUTH (necesita laravel/socialite configurat) ──────
Route::get('/auth/google', function() {
    // TODO: instalează laravel/socialite și configurează Google OAuth
    // composer require laravel/socialite
    // Adaugă în config/services.php: 'google' => ['client_id'=>env('GOOGLE_CLIENT_ID'), ...]
    return redirect()->route('login')->with('status', 'Conectarea cu Google nu este inca disponibila. Te rugam sa folosesti email si parola.');
})->name('auth.google');

Route::get('/auth/google/callback', function() {
    return redirect()->route('login');
})->name('auth.google.callback');