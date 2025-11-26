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
use App\Http\Controllers\InterventionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/sessions', [SessionController::class, 'index']);


//API 6
Route::get('/profiling/details', [ProfilingController::class, 'details']);
// Rute untuk API GET Scenarios (Publik untuk tes)
Route::get('/scenarios', [ScenarioController::class, 'index']);

// Rute untuk API 31 (Publik untuk tes)
// API 19 (BARU)
// Nama '{scenario}' harus cocok dengan variabel $scenario di Controller
Route::get('/scenario/{scenario}', [ScenarioController::class, 'show']);
//API 20
Route::post('/scenario/submit', [ScenarioController::class, 'submit']);
//API 30 - Leaderboard
Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderboard']);
//Tambah daftar sesi selesai
Route::get('/sessions/completed', [SessionController::class, 'getCompletedSessions']);

// API 28: Kirim Feedback (Trigger Log & Learning)
Route::post('/feedback/intervention', [FeedbackController::class, 'store']);

// API: GET Intervention Trigger
Route::get('/intervention/trigger', [InterventionController::class, 'trigger']);

// API 30: Update Threshold Manual (Opsional, biasanya internal)
Route::post('/threshold/update', [ThresholdController::class, 'update']);

// API 29: Get Threshold (Sudah ada sebelumnya)
Route::get('/threshold', [ThresholdController::class, 'getThresholds']);

// api untuk daftar list player
// TELAH DIPERBAIKI: Menggunakan apiIndex() untuk API list pemain
Route::get('/players', [PlayerController::class, 'apiIndex']); 

// api untuk detail basic player
Route::get('/players/{id}', [PlayerController::class, 'show']);

Route::get('/tile/{id}', [BoardController::class, 'getTile']);
Route::get('/card/quiz/{id}', [CardController::class, 'getQuizCard']);
Route::get('/player/{id}/profile', [PlayerController::class, 'getProfile']);
Route::post('/profiling/submit', [PlayerController::class, 'submitProfiling']);
Route::post('/session/turn/start', [SessionController::class, 'startTurn']);
Route::post('/session/player/move', [SessionController::class, 'movePlayer']);
Route::post('/session/turn/end', [SessionController::class, 'endTurn']);
Route::post('/session/end/{sessionId}', [SessionController::class, 'endSession']); 