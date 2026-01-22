<?php

namespace App\Services;

use App\Models\Scenario;
use App\Services\InterventionService;
use App\Services\PredictionService;
use App\Services\ScoringService;
use App\Models\ScenarioOption;
use App\Models\PlayerProfile;
use App\Models\PlayerDecision;
use App\Models\ParticipatesIn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScenarioService
{
    protected $interventionService;
    protected $predictionService;
    protected $scoringService;
    protected $sessionService;


    public function __construct(
        InterventionService $interventionService,
        PredictionService $predictionService,
        ScoringService $scoringService,
        SessionService $sessionService
    ) {
        $this->interventionService = $interventionService;
        $this->predictionService = $predictionService;
        $this->scoringService = $scoringService;
        $this->sessionService = $sessionService;
    }

    /**
     * Mengambil detail satu skenario lengkap dengan daftar opsi
     * dan menambahkan flag apakah pemain sedang memicu intervensi.
     */
    public function getScenarioDetail(string $playerId, string $scenarioId)
    {
        $scenario = Scenario::with([
            'options' => function ($q) {
                $q->orderBy('optionId');
            }
        ])->find($scenarioId);

        if (!$scenario) {
            return ['error' => 'Scenario not found'];
        }

        $interventionCheck = $this->interventionService->checkInterventionTrigger($playerId);

        $hasIntervention = !empty($interventionCheck);

        return [
            'category' => $scenario->category,
            'title' => $scenario->title,
            'question' => $scenario->question,
            'options' => $scenario->options->map(function ($opt) {
                return [
                    'id' => $opt->optionId,
                    'text' => $opt->text
                ];
            }),
            'intervention' => $hasIntervention
        ];
    }

    /**
     * Memproses jawaban skenario dari pemain,
     * memperbarui skor lifetime berdasarkan opsi yang dipilih,
     * dan mencatat keputusan untuk analisis lebih lanjut.
     */
    public function submitAnswer(string $playerId, array $data)
    {
        return DB::transaction(function () use ($playerId, $data) {
            $scenarioId = $data['scenario_id'];
            $selectedOptionId = $data['selected_option'];

            $option = ScenarioOption::where('scenarioId', $scenarioId)
                ->where('optionId', $selectedOptionId)
                ->first();

            if (!$option) {
                return ['error' => 'Invalid option selected'];
            }

            $profile = PlayerProfile::find($playerId);
            if (!$profile) {
                return ['error' => 'Player profile not found'];
            }

            $scoreChanges = $option->scoreChange ?? [];
            if (!is_array($scoreChanges)) {
                $scoreChanges = [];
            }
            $currentScores = $profile->lifetime_scores ?? [];

            $primaryAffected = 'general';
            $maxChangeVal = 0;
            $totalChange = 0;
            $allScoreChanges = []; // Track semua perubahan kategori

            $scenario = Scenario::find($scenarioId);
            $scenarioDifficulty = $scenario->difficulty ?? 2;

            foreach ($scoreChanges as $category => $baseChange) {

                $oldVal = $currentScores[$category] ?? 0;

                // Menerapkan Perhitungan Pembobotan
                $actualChange = $this->scoringService->calculateWeightedScore(
                    $oldVal,
                    $scenarioDifficulty,
                    $baseChange
                );

                // Clamp ke 0-max range
                $maxScore = $this->scoringService->getMaxScore();
                $newVal = max(0, min($maxScore, $oldVal + $actualChange));
                $currentScores[$category] = $newVal;

                // Track semua perubahan kategori
                $allScoreChanges[$category] = [
                    'base' => $baseChange,
                    'actual' => $actualChange,
                    'old' => $oldVal,
                    'new' => $newVal
                ];

                // Tentukan affected utama
                if (abs($actualChange) >= abs($maxChangeVal)) {
                    $maxChangeVal = $actualChange;
                    $primaryAffected = $category;
                }

                $totalChange += $actualChange;
            }

            $profile->lifetime_scores = $currentScores;
            $profile->save();


            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with('session')
                ->first();

            if ($participation && $participation->session) {
                $session = $participation->session;
                $gameState = json_decode($session->game_state, true) ?? [];

                // Ubah phase agar client tahu event sudah selesai
                $gameState['turn_phase'] = 'event_completed';
                // Hapus active_event agar bersih
                unset($gameState['active_event']);

                $session->game_state = json_encode($gameState);
                $session->save();

                // Accumulate score changes ke session score
                $participation->score = ($participation->score ?? 0) + $totalChange;
                $participation->save();

                // Update ranks untuk semua pemain dalam session
                $this->sessionService->updateSessionRanks($participation->sessionId);
            }

            $sessionId = $participation ? $participation->sessionId : null;
            $turnNumber = $participation ? ($participation->session->current_turn ?? 0) : 0;

            PlayerDecision::create([
                'player_id' => $playerId,
                'session_id' => $sessionId,
                'turn_number' => $turnNumber,
                'content_id' => $scenarioId,
                'content_type' => 'scenario',
                'selected_option' => $selectedOptionId,
                'is_correct' => $option->is_correct ?? false,
                'score_change' => $totalChange,
                'decision_time_seconds' => $data['decision_time_seconds'] ?? 0,
                'created_at' => now()
            ]);

            // Get real-time prediction after decision (without updating profile)
            $prediction = null;
            try {
                $prediction = $this->predictionService->getCurrentPrediction($playerId);
                // Remove error key if present for cleaner response
                if (isset($prediction['error'])) {
                    $prediction = null;
                }
            } catch (\Exception $e) {
                Log::warning("Prediction failed after scenario answer: " . $e->getMessage());
            }

            $response = [
                'correct' => (bool) $option->is_correct,
                'score_change' => $maxChangeVal,
                'affected_score' => $primaryAffected,
                'new_score_value' => $currentScores[$primaryAffected] ?? 0,
                // 'all_score_changes' => $allScoreChanges, // Detail semua kategori
                'response' => $option->response
            ];

            // Add prediction data if available
            if ($prediction) {
                $response['prediction'] = [
                    'current_cluster' => $prediction['predicted_cluster'] ?? null,
                    'cluster_changed' => $prediction['cluster_changed'] ?? false,
                    'weak_areas' => $prediction['weak_areas'] ?? [],
                    'level' => $prediction['level'] ?? null
                ];
            }

            return $response;
        });
    }
}