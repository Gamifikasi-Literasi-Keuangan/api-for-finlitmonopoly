<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayerController extends Controller
{
    // Show players list page (Blade)
    public function index()
    {
        return view('admin.players.index');
    }

    // Show profiling page for a single player
    public function profilingView($id)
    {
        return view('admin.players.profiling', ['playerId' => $id]);
    }

    // Return JSON with profiling details (mock/sample data)
    public function profilingDetails(Request $request)
    {
        $playerId = $request->query('player_id', 'unknown');

        // Sample payload — replace with real analytics in future
        $payload = [
            'player_id' => $playerId,
            'current_scores' => [
                'overall' => 78,
                'financial_literacy' => 82,
                'risk_understanding' => 70,
                'decision_making' => 75,
            ],
            'weak_areas' => [
                ['area' => 'risk_understanding', 'score' => 70],
                ['area' => 'decision_making', 'score' => 75],
            ],
            'recommended_focus' => [
                'practice_scenarios' => ['scenario_id' => 12, 'title' => 'Buat rencana anggaran'],
                'micro_quizzes' => true,
            ],
            'behavioral_patterns' => [
                'decision_speed' => 'moderate',
                'risk_tendency' => 'balanced',
                'collaboration' => 'low',
            ],
        ];

        return response()->json($payload);
    }

    // Return JSON with cluster info (mock/sample)
    public function profilingCluster(Request $request)
    {
        $playerId = $request->query('player_id', 'unknown');

        $payload = [
            'player_id' => $playerId,
            'cluster' => [
                'id' => 3,
                'label' => 'Praktis konservatif',
                'centroid' => [75, 60, 80],
            ],
            'nearby_profiles' => [
                ['player_id' => 'p102', 'score' => 76],
                ['player_id' => 'p215', 'score' => 74],
            ],
        ];

        return response()->json($payload);
    }

    // API endpoint returning players list (sample data)
    public function apiPlayers()
    {
        $players = [
            ['id' => 'p001', 'username' => 'ahmad', 'locale' => 'id', 'created_at' => '2025-10-01', 'status' => 'connected'],
            ['id' => 'p002', 'username' => 'siti', 'locale' => 'id', 'created_at' => '2025-09-02', 'status' => 'disconnected'],
            ['id' => 'p003', 'username' => 'budi', 'locale' => 'en', 'created_at' => '2025-10-10', 'status' => 'connected'],
        ];

        return response()->json(['data' => $players]);
    }
}
