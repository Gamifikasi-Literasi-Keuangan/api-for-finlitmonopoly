<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ProfilingController;
use App\Http\Controllers\MatchmakingController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PredictionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/google', [AuthController::class, 'google']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::prefix('config')->group(function () {
    Route::get('/game', [ConfigController::class, 'game']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('profiling')->group(function () {
        Route::get('/status', [ProfilingController::class, 'status']);
        Route::get('/questions', [ProfilingController::class, 'questions']);
        Route::post('/submit', [ProfilingController::class, 'submit']);
        Route::get('/cluster', [ProfilingController::class, 'cluster']);
        // Route::get('/fuzzy', [ProfilingController::class, 'fuzzy']);
    });
    
    Route::prefix('matchmaking')->group(function () {
        Route::post('/join', [MatchmakingController::class, 'join']);
        Route::post('/character/select', [MatchmakingController::class, 'selectCharacter']);
        Route::get('/status', [MatchmakingController::class, 'status']);
        Route::post('/ready', [MatchmakingController::class, 'ready']);
    });
    
    Route::prefix('session')->group(function () {
        Route::get('/state', [SessionController::class, 'state']);
        Route::post('/ping', [SessionController::class, 'ping']);
        Route::prefix('turn')->group(function () {
            Route::post('/start', [SessionController::class, 'startTurn']);
            Route::post('/roll', [SessionController::class, 'roll']);
            Route::get('/current', [SessionController::class, 'currentTurn']);
            Route::post('/end', [SessionController::class, 'endTurn']);
        });
        Route::post('/player/move', [SessionController::class, 'move']);
        Route::post('/leave', [SessionController::class, 'leave']);
        
        // Prediction & Analysis Endpoints
        Route::get('/predict/current', [PredictionController::class, 'getCurrentPrediction']);
        Route::get('/analysis/pause', [PredictionController::class, 'analyzePause']);
        Route::post('/finish', [PredictionController::class, 'finishSession']);
    });

    Route::get('/tile/{id}', [BoardController::class, 'getTile']);
    
    Route::prefix('scenario')->group(function () {
        Route::get('/{scenario_id}', [ScenarioController::class, 'show']);
        Route::post('/submit', [ScenarioController::class, 'submit']);
    });
    Route::prefix('card')->group(function () {
        Route::get('/risk/{risk_id}', [CardController::class, 'getRiskCard']);
        Route::get('/chance/{chance_id}', [CardController::class, 'getChanceCard']);
        Route::get('/quiz/{quiz_id}', [CardController::class, 'getQuizCard']);
        Route::post('/quiz/submit', [CardController::class, 'submitQuiz']);
    });
    
    Route::prefix('recommendation')->group(function () {
        Route::get('/next', [RecommendationController::class, 'next']);
        Route::get('/path', [RecommendationController::class, 'path']);
        Route::get('/peer', [RecommendationController::class, 'peer']);
    });
    
    Route::post('/feedback/intervention', [FeedbackController::class, 'store']);
    Route::get('/intervention/trigger', [InterventionController::class, 'trigger']);
    Route::get('/performance/scores', [PerformanceController::class, 'scores']);
    Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderboard']);
});
