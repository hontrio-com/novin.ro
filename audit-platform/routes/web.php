<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditController;

Route::get('/', function () {
    return view('welcome');
});

// ── PUBLIC ──────────────────────────────────────────────────
Route::get('/', [AuditController::class, 'index'])->name('home');
Route::post('/audit/start', [AuditController::class, 'start'])->name('audit.start');
Route::get('/audit/{audit}/progress', [AuditController::class, 'progress'])->name('audit.progress');
Route::get('/raport/{token}', [AuditController::class, 'report'])->name('audit.report');
Route::get('/raport/{token}/pdf', [AuditController::class, 'downloadPdf'])->name('audit.pdf');

// ── DASHBOARD (necesita autentificare) ───────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/setari', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::patch('/dashboard/profil', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::patch('/dashboard/parola', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
