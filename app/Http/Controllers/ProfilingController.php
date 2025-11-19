<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProfilingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfilingController extends Controller
{
    protected $service;

    public function __construct(ProfilingService $service)
    {
        $this->service = $service;
    }

    /**
     * IMPLEMENTASI API 6: GET /profiling/details
     * Mengambil detail lengkap profil pemain
     */
    public function details(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'player_id' => 'required|string|exists:players,PlayerId'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Panggil Service
        $profile = $this->service->getProfileDetails($request->input('player_id'));

        if (!$profile) {
            return response()->json([
                'message' => 'Profile not found (Maybe user hasn\'t done onboarding?)'
            ], 404);
        }

        // 3. Kirim Response
        return response()->json($profile);
    }
    
    // Nanti API 5 (Cluster) juga bisa ditaruh di sini
}