<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PredictionService;

class PredictionController extends Controller
{
    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Get current real-time prediction without updating profile
     * GET /session/predict/current
     */
    public function getCurrentPrediction(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->predictionService->getCurrentPrediction($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Analyze player state when they pause/stop the game
     * GET /session/analysis/pause
     */
    public function analyzePause(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->predictionService->analyzePauseState($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Finalize session evaluation (called when session ends)
     * POST /session/finish
     */
    public function finishSession(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $result = $this->predictionService->finalizeSessionEvaluation(
                $user->player->PlayerId,
                $request->session_id
            );

            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
