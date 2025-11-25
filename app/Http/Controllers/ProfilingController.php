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
        $playerId = $request->query('player_id');
        
        if (!$playerId) {
            return response()->json([
                'error' => 'player_id is required'
            ], 400);
        }

        $result = $this->profilingService->getProfilingStatus($playerId);

        return response()->json($result);
    }

    /**
     * Menyimpan jawaban onboarding dari player.
     */
    public function submit(ProfilingSubmitRequest $request)
    {
        $result = $this->profilingService->saveOnboardingAnswers($request->validated());
        return response()->json($result);
    }

    /**
     * Menjalankan proses clustering profiling untuk player tertentu.
     */
    public function cluster($playerId)
    {
        $result = $this->profilingService->runProfilingCluster($playerId);
        return response()->json($result);
    }

}
