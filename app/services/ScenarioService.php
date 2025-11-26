<?php

namespace App\Services;

use App\Repositories\PlayerDecisionRepository;
use App\Repositories\PlayerProfileRepository;
use App\Models\ScenarioOption;
use App\Models\Scenario;

class ScenarioService
{
    protected $decisionRepo;
    protected $profileRepo;

    public function __construct(
        PlayerDecisionRepository $decisionRepo,
        PlayerProfileRepository $profileRepo
    ) {
        $this->decisionRepo = $decisionRepo;
        $this->profileRepo = $profileRepo;
    }

    public function processSubmission(array $data)
    {
        // 1. Ambil Kunci Jawaban dari Database
        $option = ScenarioOption::where('scenarioId', $data['scenario_id'])
            ->where('optionId', $data['selected_option'])
            ->first();

        if (!$option) {
            throw new \Exception("Opsi jawaban tidak ditemukan.");
        }

        // 2. Hitung Score Change
        $scoreChangeArray = $option->scoreChange; // JSON dari DB: {"pendapatan": -3, "overall": -3}

        // Ambil kategori score yang terpengaruh (key pertama)
        $affectedScore = array_key_first($scoreChangeArray);
        $scoreChange = $scoreChangeArray[$affectedScore] ?? 0;

        // 4. Hitung nilai baru (mock - nanti ambil dari player profile saat auth sudah ada)
        $newScoreValue = 12; // Mock value

        // 5. Response message berdasarkan benar/salah
        $responseMessage = $option->is_correct
            ? ($option->feedback ?? "Pilihan yang tepat!")
            : ($option->feedback ?? "Hati-hati, keputusan ini berdampak negatif.");

        // 6. Return response sesuai spesifikasi V3
        return [
            'correct' => $option->is_correct,
            'score_change' => $scoreChange,
            'affected_score' => $affectedScore,
            'new_score_value' => $newScoreValue,
            'response' => $responseMessage
        ];
    }
}