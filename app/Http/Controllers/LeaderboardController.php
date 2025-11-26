<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParticipatesIn; // <-- Impor Model Anda
use Illuminate\Support\Facades\Validator; // <-- Impor Validator

class LeaderboardController extends Controller
{
    /**
     * API 31: GET /leaderboard
     * Mengambil peringkat untuk papan peringkat
     *
     * Mode 1: Dengan session_id → Leaderboard per session
     * Mode 2: Tanpa session_id → Leaderboard global (top players dari semua session)
     */
    public function getLeaderboard(Request $request)
    {
        // === Validasi ===
        $validator = Validator::make($request->all(), [
            'session_id' => 'nullable|string|exists:sessions,sessionId', // Opsional
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $sessionId = $request->input('session_id');
        $limit = $request->input('limit', 50); // Default 50

        // === Query: Conditional berdasarkan session_id ===
        $query = ParticipatesIn::with('player:PlayerId,name')
            ->orderBy('score', 'DESC');

        // Jika ada session_id, filter per session
        if ($sessionId) {
            $query->where('sessionId', $sessionId);
        }

        $rankings = $query->limit($limit)->get();

        // === Format Response ===
        $formattedRankings = $rankings->map(function ($participation, $key) {
            return [
                'player_id' => $participation->player->PlayerId,
                'username' => $participation->player->name,
                'overall' => $participation->score,
                'rank' => $key + 1
            ];
        });

        // === Response ===
        $response = [
            'success' => true,
            'rankings' => $formattedRankings,
            'generated_at' => now()->toIso8601String()
        ];

        // Tambahkan session_id jika ada
        if ($sessionId) {
            $response['session_id'] = $sessionId;
        }

        return response()->json($response, 200);
    }
}