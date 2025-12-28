<?php

use Illuminate\Support\Facades\Route;
use App\Models\ProfilingQuestion;
use App\Repositories\ProfilingRepository;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\LeaderboardController;
use App\Services\ProfilingService;
use App\Services\AI\FuzzyService;

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Login Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

// Leaderboard (alias lama)
Route::get('/leaderboard', function () {
    return view('admin.players.leaderboard');
})->name('leaderboard');

// Admin config routes
Route::get('/admin/config', function () {
    return view('admin.config.index');
})->name('admin.config');

/*
|--------------------------------------------------------------------------
| Protected Routes (auth required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (role:admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        // Config pages
        Route::view('/config', 'admin.config.index')->name('config');
        Route::view('/config/edit', 'admin.config.edit')->name('config.edit');
        Route::view('/config/sync', 'admin.config.sync')->name('config.sync');

        // Content management
        Route::view('/content/scenarios', 'admin.content.scenarios')->name('content.scenarios');
        Route::view('/content/cards', 'admin.content.cards')->name('content.cards');
        Route::view('/content/quiz', 'admin.content.quiz')->name('content.quiz');

        // Players management
        Route::get('/players', [PlayerController::class, 'index'])->name('players');
        Route::get('/players/{id}/profiling', [PlayerController::class, 'profilingView'])->name('players.profiling');

        // Leaderboard
        Route::view('/players/leaderboard', 'admin.players.leaderboard')->name('players.leaderboard');

        // Rekomendasi
        Route::get('/rekomendasi-lanjutan', [PlayerController::class, 'rekomendasiIndex'])->name('rekomendasi.index');

        // Learning Path
        Route::get('/learning-path', [PlayerController::class, 'learningPathIndex'])->name('learning-path.index');
        Route::view('/peer-insight', 'admin.peer_insight.index')->name('peer-insight.index');
    });

    /*
    |--------------------------------------------------------------------------
    | PLAYER ROUTES (role:player)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:player'])->prefix('player')->name('player.')->group(function () {

        // Dashboard player
        Route::view('/dashboard', 'player.dashboard')->name('dashboard');

    });

    /*
    |--------------------------------------------------------------------------
    | JSON / API Routes (tidak butuh role, hanya butuh login)
    |--------------------------------------------------------------------------
    */
    Route::get('/profiling/details', [PlayerController::class, 'profilingDetails'])->name('profiling.details');
    Route::get('/profiling/cluster', [PlayerController::class, 'profilingCluster'])->name('profiling.cluster');
    Route::get('/api/players', [PlayerController::class, 'apiPlayers'])->name('api.players');

    // Recommendation next API
    Route::post('/recommendation/next', [PlayerController::class, 'recommendationNext'])->name('recommendation.next');
});


/*
|--------------------------------------------------------------------------
| Backward compatibility alias: /leaderboard
|--------------------------------------------------------------------------
*/