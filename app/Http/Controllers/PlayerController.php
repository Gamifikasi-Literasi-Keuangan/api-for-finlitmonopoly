<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlayerController extends Controller
{
    // Show players list page (Blade)
    public function index()
    {
        return view('admin.players.index');
    }

  public function rekomendasiIndex()
{
    $players = collect();

    if (Schema::hasTable('users')) {
        // cek kolom apa yang ada
        $columns = Schema::getColumnListing('users');
        
        $query = DB::table('users')->select('id', 'name');
        
        // tambah kolom jika ada
        if (in_array('locale', $columns)) {
            $query->addSelect('locale');
        } else {
            $query->addSelect(DB::raw("'id' as locale"));
        }
        
        if (in_array('created_at', $columns)) {
            $query->addSelect('created_at');
        } else {
            $query->addSelect(DB::raw("'2025-01-01' as created_at"));
        }
        
        // status fallback: selalu 'connected'
        $query->addSelect(DB::raw("'connected' as status"));
        
        $players = $query->orderBy('name')->get();
    }

    return view('admin.rekomendasi.index', compact('players'));
}

    // API endpoint rekomendasi (sample data)
    public function recommendationNext(Request $request)
    {
        $playerId = $request->input('player_id');

        $recommendations = [
            [
                'title' => 'Mengelola Pinjol dengan Bijak',
                'reason' => 'Top weak area: utang',
                'peer_insight' => ['peer_success_rate' => 0.64],
                'expected_benefit' => 'Mengurangi risiko keterlambatan pembayaran',
                'scores' => ['content' => 75, 'collaborative' => 40, 'performance' => 62],
                'scenario_id' => 123
            ],
            [
                'title' => 'Menyusun Anggaran Rumah Tangga',
                'reason' => 'Top weak area: budgeting',
                'peer_insight' => ['peer_success_rate' => 0.72],
                'expected_benefit' => 'Meningkatkan kemampuan perencanaan keuangan',
                'scores' => ['content' => 82, 'collaborative' => 50, 'performance' => 70],
                'scenario_id' => 124
            ]
        ];

        return response()->json([
            'player_id' => $playerId,
            'recommendations' => $recommendations
        ]);
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

    // Show learning path page
    public function learningPathIndex()
    {
        $players = [
            ['id' => 'p001', 'name' => 'Ahmad'],
            ['id' => 'p002', 'name' => 'Siti'],
            ['id' => 'p003', 'name' => 'Budi'],
        ];
        return view('admin.rekomendasi.learning_path.index', compact('players'));
    }
    public function peerInsightIndex()
{
    $players = collect();

    if (Schema::hasTable('players')) {
        $players = DB::table('players')
            ->join('auth_users', 'players.user_id', '=', 'auth_users.id')
            ->select(
                'players.PlayerId as id',
                'players.name',
                'players.locale',
                'players.createdAt as created_at',
                DB::raw("'connected' as status")
            )
            ->orderBy('players.name')
            ->get();
    }

    return view('admin.rekomendasi.peer_insight.index', compact('players'));
}
}