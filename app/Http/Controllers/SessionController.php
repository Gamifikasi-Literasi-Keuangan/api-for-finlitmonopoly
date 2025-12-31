<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SessionService;

class SessionController extends Controller
{
    protected $sessionService;
    public function __construct(SessionService $sessionService)
        {
            $this->sessionService = $sessionService;
        }

    /**
     * Retrieve the current state of the game session for the authenticated player.
     */
    public function state(Request $request){
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->sessionService->getSessionState($user->player->PlayerId);

            if (isset($result['error'])) {
                if (str_contains($result['error'], 'not started')) {
                    return response()->json($result, 409);
                }
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle ping requests to check server status.
     */
    public function ping(Request $request){
        return response()->json([
                'status' => 'ok',
                'server_time' => now()->toIso8601String()
            ]);
    }

    /**
     * Handle starting the player's turn.
     */
    public function startTurn(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->sessionService->startTurn($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle rolling the dice for the player's turn.
     */
    public function roll(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->sessionService->rollDice($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle moving the player based on the last rolled dice.
     */
    public function move(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->sessionService->movePlayer($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieve the current turn information for the authenticated player.
     */
    public function currentTurn(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->sessionService->getCurrentTurn($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 404);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function endTurn(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->sessionService->endTurn($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle leaving/finishing the session
     */
    public function leave(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->sessionService->leaveSession($user->player->PlayerId);

            if (isset($result['error'])) {
                return response()->json($result, 400);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
