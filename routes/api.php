<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilingController;

Route::post('/profiling/submit', [ProfilingController::class, 'submit']);
Route::get('/profiling/cluster/{playerId}', [ProfilingController::class, 'cluster']);