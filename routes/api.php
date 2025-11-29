<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ThresholdController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfilingController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\MatchmakingController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\InterventionController;
use Laravel\Sanctum\PersonalAccessToken;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/google', [App\Http\Controllers\AuthController::class, 'google']);
    Route::post('/refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
});

Route::prefix('config')->group(function () {
    Route::get('/game', [ConfigController::class, 'game']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('profiling')->group(function () {
        Route::get('/status', [ProfilingController::class, 'status']);
        Route::post('/submit', [ProfilingController::class, 'submit']);
        Route::get('/cluster', [ProfilingController::class, 'cluster']);
    });
    
    Route::prefix('matchmaking')->group(function () {
        Route::post('/join', [MatchmakingController::class, 'join']);
        Route::post('/character/select', [MatchmakingController::class, 'selectCharacter']);
    });
    
    Route::prefix('recommendation')->group(function () {
        Route::get('/next', [RecommendationController::class, 'next']);
        Route::get('/path', [RecommendationController::class, 'path']);
        Route::get('/peer', [RecommendationController::class, 'peer']);
    });

    Route::get('/scenarios', [ScenarioController::class, 'index']);
    Route::get('/scenario/{scenario}', [ScenarioController::class, 'show']);
    Route::post('/scenario/submit', [ScenarioController::class, 'submit']);
    Route::post('/feedback/intervention', [FeedbackController::class, 'store']);
    Route::get('/intervention/trigger', [InterventionController::class, 'trigger']);
    Route::post('/threshold/update', [ThresholdController::class, 'update']);
    Route::get('/threshold', [ThresholdController::class, 'getThresholds']);
    // Route::get('/tile/{id}', [BoardController::class, 'getTile']);
    Route::get('/card/quiz/{id}', [CardController::class, 'getQuizCard']);
    Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderboard']);
});



Route::get('/debug-sanctum', function (Request $request) {
    $tokenString = $request->bearerToken();
    
    if (!$tokenString) {
        return response()->json(['error' => 'Token tidak terbaca di header'], 400);
    }

    // 1. Cek apakah token ada di database
    // Sanctum menyimpan hash, jadi kita harus cari berdasarkan ID token (angka depan sebelum |)
    [$id, $token] = explode('|', $tokenString, 2);
    $dbToken = PersonalAccessToken::find($id);

    if (!$dbToken) {
        return response()->json(['error' => 'Token ID tidak ditemukan di database personal_access_tokens'], 404);
    }

    // 2. Validasi Hash
    if (!hash_equals($dbToken->token, hash('sha256', $token))) {
        return response()->json(['error' => 'Hash token tidak cocok. Token salah/typo.'], 401);
    }

    // 3. Cek User Pemilik Token
    $user = $dbToken->tokenable; // Ini memanggil model App\Models\User

    return response()->json([
        'status' => 'Token Valid secara Kriptografi',
        'token_id' => $dbToken->id,
        'tokenable_type_in_db' => $dbToken->tokenable_type,
        'tokenable_id' => $dbToken->tokenable_id,
        'user_found' => $user ? 'YA' : 'TIDAK (User mungkin terhapus dari tabel auth_users)',
        'user_data' => $user,
        'config_auth_model' => config('auth.providers.users.model'), // Cek config yang aktif
    ]);
});