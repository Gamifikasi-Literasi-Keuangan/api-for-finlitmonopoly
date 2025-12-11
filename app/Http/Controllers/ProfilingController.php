<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ProfilingSubmitRequest;
use App\Services\ProfilingService;

class ProfilingController extends Controller
{
    protected $profilingService;

    /**
     * Konstruktor untuk ProfilingController.
     */
    public function __construct(ProfilingService $profilingService)
    {
        $this->profilingService = $profilingService;
    }

    /**
     * Mendapatkan status profiling untuk player tertentu.
     */
    public function status(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player data not found'], 404);
        }
        $playerId = $user->player->PlayerId;
        $result = $this->profilingService->getProfilingStatus($playerId);
        return response()->json($result);
    }

    /**
     * Menyimpan jawaban onboarding dari player.
     */
    public function submit(ProfilingSubmitRequest $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player not found'], 404);
        }
        $playerId = $user->player->PlayerId;
        $validatedData = $request->validated();
        $result = $this->profilingService->saveOnboardingAnswers([
            'player_id' => $playerId,
            'answers' => $validatedData['answers'],
            'profiling_done' => $validatedData['profiling_done'] ?? true
        ]);
        return response()->json($result);
    }

    /**
     * Menjalankan proses clustering profiling untuk player tertentu.
     */
    public function cluster(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player not found'], 404);
        }
        $playerId = $user->player->PlayerId;

        $result = $this->profilingService->runProfilingCluster($playerId);
        if (isset($result['error'])) {
            return response()->json($result, 400);
        }
        return response()->json($result, 200);
    }
}