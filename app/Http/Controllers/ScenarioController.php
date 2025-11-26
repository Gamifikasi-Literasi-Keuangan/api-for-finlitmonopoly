<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Services\ScenarioService; // Service logika utama
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScenarioController extends Controller
{
    protected $service;

    // Inject ScenarioService ke dalam Controller
    public function __construct(ScenarioService $service)
    {
        $this->service = $service;
    }


    public function index(Request $request)
    {
        // Ambil kolom penting saja untuk list view
        $scenarios = Scenario::select(
            'id',
            'title',
            'category',
            'difficulty',
            'created_at'
        )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($scenarios);
    }

    /**
     * API 19: GET /scenario/{scenario_id}
     * Mengambil data skenario untuk ditampilkan di game
     *
     * intervention = true jika player punya error streak >= 2
     */
    public function show(Request $request, Scenario $scenario)
    {
        // Muat relasi 'options'
        $scenario->load('options');

        // Cek apakah perlu intervention (berdasarkan player_id dan session_id)
        $intervention = $this->shouldShowIntervention(
            $request->input('player_id'),
            $request->input('session_id')
        );

        // Format response sesuai spesifikasi V3
        return response()->json([
            'category' => $scenario->category,
            'title' => $scenario->title,
            'question' => $scenario->question,
            'options' => $scenario->options->map(function ($option) {
                return [
                    'id' => $option->optionId,
                    'text' => $option->text
                ];
            }),
            'intervention' => $intervention
        ], 200);
    }

    private function shouldShowIntervention($playerId, $sessionId)
    {
        // Jika tidak ada player_id, tidak perlu intervention
        if (!$playerId) {
            return false;
        }

        // Cek 5 decision terakhir player di session ini
        $recentDecisions = \App\Models\PlayerDecision::where('player_id', $playerId)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Hitung error streak (consecutive errors)
        $errorStreak = 0;
        foreach ($recentDecisions as $decision) {
            if (!$decision->is_correct) {
                $errorStreak++;
            } else {
                break; // Stop jika ketemu jawaban benar
            }
        }

        // Intervention = true jika error streak >= 2
        return $errorStreak >= 3;
    }


    /**
     * API: POST /scenario/submit
     * Menyimpan pilihan player terhadap skenario dan memperoleh nilai score baru
     */
    public function submit(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'scenario_id' => 'required|string|exists:scenarios,id',
            'selected_option' => 'required|string',
            'decision_time_seconds' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            // 2. Panggil Service untuk memproses jawaban
            $result = $this->service->processSubmission($validator->validated());

            // 3. Kembalikan hasil sesuai spesifikasi V3
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Processing failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}