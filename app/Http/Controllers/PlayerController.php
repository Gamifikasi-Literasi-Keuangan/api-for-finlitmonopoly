<?php

namespace App\Http\Controllers;

use App\Services\PlayerService; // <-- Impor Service
use App\Http\Requests\SubmitProfilingRequest; // <-- Impor Validator
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    // Suntikkan (inject) Service
    public function __construct(protected PlayerService $playerService)
    {
    }

    /**
     * Implementasi: GET /player/{id}/profile
     * (Sesuai PL-2)
     */
    public function getProfile($id)
    {
        // 1. Delegasikan ke Service
        $playerWithProfile = $this->playerService->getPlayerProfile($id);

        // 2. Kembalikan View (JSON)
        return response()->json($playerWithProfile);
    }

    /**
     * Implementasi: POST /profiling/submit
     * (Sesuai API Profiling)
     */
    public function submitProfiling(SubmitProfilingRequest $request)
    {
        // 1. Validasi otomatis oleh SubmitProfilingRequest
        $validatedData = $request->validated();

        // 2. Delegasikan ke Service
        $profile = $this->playerService->submitProfiling(
            $validatedData['player_id'],
            $validatedData['answers']
        );

        // 3. Kembalikan View (JSON)
        return response()->json([
            'status' => 'success',
            'cluster' => $profile->cluster,
            'profile' => $profile
        ], 201); // 201 Created/Updated
    }
}