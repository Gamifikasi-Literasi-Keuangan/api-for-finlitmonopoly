<?php

namespace App\Http\Controllers;

use App\Services\SessionService;
use App\Http\Requests\StartTurnRequest;
use App\Http\Requests\MovePlayerRequest;
use App\Http\Requests\EndTurnRequest;
use App\Http\Requests\EndSessionRequest; // (Jika Anda buat, atau validasi manual)
use Illuminate\Http\Request;

class SessionController extends Controller
{
    // Suntikkan (inject) Service
    public function __construct(protected SessionService $sessionService)
    {
    }

    public function startTurn(StartTurnRequest $request)
    {
        $data = $request->validated();
        $turn = $this->sessionService->startTurn($data['sessionId'], $data['playerId']);
        return response()->json($turn, 201); // 201 Created
    }

    public function movePlayer(MovePlayerRequest $request)
    {
        $data = $request->validated();
        $result = $this->sessionService->movePlayer(
            $data['sessionId'], $data['playerId'], $data['from_tile'], $data['steps']
        );
        return response()->json($result);
    }

    public function endTurn(EndTurnRequest $request)
    {
        $data = $request->validated();
        $result = $this->sessionService->endTurn(
            $data['sessionId'], $data['playerId'], $data['turn_id'], $data['actions'] ?? []
        );
        return response()->json($result);
    }

    public function endSession(Request $request, $sessionId) // Contoh jika ID dari URL
    {
        // $data = $request->validate(['sessionId' => 'required|...']);
        // $result = $this->sessionService->endSession($data['sessionId']);
        $result = $this->sessionService->endSession($sessionId);
        return response()->json($result);
    }
}