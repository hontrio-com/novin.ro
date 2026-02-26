<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/audit/{id}/status', [AuditController::class, 'status']);
Route::get('/audit/{id}/summary', [AuditController::class, 'summary']);
