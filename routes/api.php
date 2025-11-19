<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PlayerController;use App\Http\Controllers\SessionController;

Route::get('/tile/{id}', [BoardController::class, 'getTile']);
Route::get('/card/quiz/{id}', [CardController::class, 'getQuizCard']);
Route::get('/player/{id}/profile', [PlayerController::class, 'getProfile']);
Route::post('/profiling/submit', [PlayerController::class, 'submitProfiling']);
Route::post('/session/turn/start', [SessionController::class, 'startTurn']);
Route::post('/session/player/move', [SessionController::class, 'movePlayer']);
Route::post('/session/turn/end', [SessionController::class, 'endTurn']);
Route::post('/session/end/{sessionId}', [SessionController::class, 'endSession']); // Contoh rute