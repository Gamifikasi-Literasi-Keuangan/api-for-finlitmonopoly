<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    protected $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * GET /api/leaderboard
     */
    public function getLeaderboard(Request $request)
    {
        try {
            // Panggil Service
            $result = $this->leaderboardService->getLeaderboard();

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}