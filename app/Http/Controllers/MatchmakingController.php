<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SessionService;

class MatchmakingController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * POST /matchmaking/join
     * Mencari atau membuat sesi permainan.
     */
    public function join(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }
        $playerId = $user->player->PlayerId;

        try {
            $result = $this->sessionService->joinMatchmaking($playerId);
            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Matchmaking failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function selectCharacter(Request $request) {
        $request->validate([
            'character_id' => 'required|integer'
        ]);
        
        $user = $request->user();
        if (!$user || $user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }
        try {
        $result = $this->sessionService->updatePlayerCharacter(
            $user->player->PlayerId,
            $request->input('character_id')
        );

        return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
