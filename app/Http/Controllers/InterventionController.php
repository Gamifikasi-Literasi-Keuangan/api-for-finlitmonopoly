<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InterventionService;

class InterventionController extends Controller
{
    protected $interventionService;

    public function __construct(InterventionService $interventionService)
    {
        $this->interventionService = $interventionService;
    }

    /**
     * GET /intervention/trigger
     */
    public function trigger(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->player) {
            return response()->json(['error' => 'Player profile not found'], 404);
        }

        try {
            $result = $this->interventionService->checkInterventionTrigger($user->player->PlayerId);

            // Jika result NULL, artinya tidak ada intervensi (Aman)
            // Kita bisa return 204 No Content atau JSON kosong agar frontend tahu tidak ada popup
            if (!$result) {
                return response()->json(['status' => 'no_intervention'], 200);
            }

            return response()->json($result, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}