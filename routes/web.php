<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PlayerController;

// Main routes
Route::get('/', function () {
    return view('admin.dashboard');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Admin config routes
Route::get('/admin/config', function () {
    return view('admin.config.index');
})->name('admin.config');

Route::get('/admin/config/edit', function () {
    return view('admin.config.edit');
})->name('admin.config.edit');

Route::get('/admin/config/sync', function () {
    return view('admin.config.sync');
})->name('admin.config.sync');

// Admin content management routes
Route::get('/admin/players/leaderboard', function () {
    return view('admin.players.leaderboard');
})->name('admin.players.leaderboard');

// Alias route for backward compatibility: /leaderboard -> admin.players.leaderboard
Route::get('/leaderboard', function () {
    return view('admin.players.leaderboard');
})->name('leaderboard');

Route::get('/admin/content/scenarios', function () {
    return view('admin.content.scenarios');
})->name('admin.content.scenarios');

Route::get('/admin/content/cards', function () {
    return view('admin.content.cards');
})->name('admin.content.cards');

Route::get('/admin/content/quiz', function () {
    return view('admin.content.quiz');
})->name('admin.content.quiz');

// Player management (simple pages and API endpoints for profiling)
Route::get('/admin/players', [PlayerController::class, 'index'])->name('admin.players');
Route::get('/admin/players/{id}/profiling', [PlayerController::class, 'profilingView'])->name('admin.players.profiling');

// Lightweight profiling data endpoints (return JSON)
Route::get('/profiling/details', [PlayerController::class, 'profilingDetails'])->name('profiling.details');
Route::get('/profiling/cluster', [PlayerController::class, 'profilingCluster'])->name('profiling.cluster');
Route::get('/api/players', [PlayerController::class, 'apiPlayers'])->name('api.players');

// Recommendation API endpoint
Route::post('recommendation/next', [PlayerController::class, 'recommendationNext'])->name('recommendation.next');

// Rekomendasi routes (grouped for better organization)
Route::prefix('admin/rekomendasi')->name('admin.rekomendasi.')->group(function () {
    Route::get('/', [PlayerController::class, 'rekomendasiIndex'])->name('index');
    Route::get('/learning-path', [PlayerController::class, 'learningPathIndex'])->name('learning-path.index');
    Route::get('/peer-insight', [PlayerController::class, 'peerInsightIndex'])->name('peer-insight.index');
});

// Backward compatibility route (optional)
Route::get('/admin/rekomendasi-lanjutan', [PlayerController::class, 'rekomendasiIndex'])->name('admin.rekomendasi-lanjutan');