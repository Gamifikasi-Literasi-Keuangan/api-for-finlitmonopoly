<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendationService;

class RecommendationController extends Controller
{
    protected $recommendationService;

    // Inject RecommendationService
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Mendapatkan rekomendasi berdasarkan player_id
     */
    public function next(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->recommendationService->recommendNextQuestion($user->player->PlayerId);
            
            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mendapatkan jalur rekomendasi berdasarkan player_id
     */
    public function path(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) return response()->json(['error' => 'Player not found'], 404);

        $result = $this->recommendationService->getRecommendationPath($user->player->PlayerId);
        return response()->json($result);
    }

    /**
     * Mendapatkan perbandingan peer berdasarkan player_id
     */
    public function peer(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) return response()->json(['error' => 'Player not found'], 404);

        $result = $this->recommendationService->getPeerComparison($user->player->PlayerId);
        return response()->json($result);
    }
}
