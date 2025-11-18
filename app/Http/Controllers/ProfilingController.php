<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProfilingSubmitRequest;
use App\Services\ProfilingService;

class ProfilingController extends Controller
{
    protected $profilingService;
    
    public function __construct(ProfilingService $profilingService)
    {
        $this->profilingService = $profilingService;
    }

    public function submit(ProfilingSubmitRequest $request)
    {
        $result = $this->profilingService->saveOnboardingAnswers($request->validated());
        return response()->json($result);
    }

    public function cluster($playerId)
    {
        $result = $this->profilingService->runProfilingCluster($playerId);
        return response()->json($result);
    }

}
