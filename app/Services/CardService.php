<?php

namespace App\Services;

use App\Models\Card;
use App\Models\QuizCard;
use App\Models\PlayerProfile;
use App\Models\PlayerDecision;
use App\Models\ParticipatesIn;
use App\Models\BoardTile;
use App\Services\InterventionService;
use App\Services\PredictionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CardService
{
    protected $interventionService;
    protected $predictionService;
    protected $scoringService;
    protected $sessionService;

    // Inject InterventionService, PredictionService, ScoringService, and SessionService via Constructor
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
     * Mengambil kartu risiko dan mengaplikasikan dampaknya.
     */
    public function drawRiskCard(string $playerId, string $cardId)
    {
        return DB::transaction(function () use ($playerId, $cardId) {
            // 1. Cari Kartu Risiko
            $card = Card::where('id', $cardId)->where('type', 'risk')->first();

            if (!$card) {
                return ['error' => 'Risk card not found'];
            }

            // 2. Ambil Profil Pemain
            $profile = PlayerProfile::find($playerId);
            if (!$profile) {
                return ['error' => 'Player profile not found'];
            }

            // 3. Tentukan Kategori Terpengaruh
            // Ambil kategori pertama dari array categories di kartu (misal: ["Anggaran"])
            $categories = $card->categories ?? ['General'];
            $affectedCategoryKey = strtolower($categories[0] ?? 'pendapatan');

            $scoreKeyMap = [
                'anggaran' => 'anggaran',
                'pendapatan' => 'pendapatan',
                'tabungan' => 'tabungan_dan_dana_darurat',
                'utang' => 'utang',
                'investasi' => 'investasi',
                'asuransi' => 'asuransi_dan_proteksi',
                'risiko' => 'tabungan_dan_dana_darurat',
                'pendidikan' => 'tujuan_jangka_panjang'
            ];
            $targetScoreKey = $scoreKeyMap[$affectedCategoryKey] ?? $affectedCategoryKey;

            $currentScores = $profile->lifetime_scores ?? [];
            if (is_string($currentScores)) {
                $currentScores = json_decode($currentScores, true) ?? [];
            }
            $oldValue = $currentScores[$targetScoreKey] ?? 0;
            $baseChange = $card->scoreChange;

            // Apply weighted scoring
            $actualScoreChange = $this->scoringService->calculateWeightedScore(
                $oldValue,
                $card->difficulty ?? 2, // Default to medium difficulty if not set
                $baseChange
            );

            // Apply score change and clamp to 0-max range
            $maxScore = $this->scoringService->getMaxScore();
            $newValue = max(0, min($maxScore, $oldValue + $actualScoreChange));

            $dicePreroll = null;
            $possibleTiles = null;
            $targetPosition = null; // Position to move player to

            // Jika kartu memiliki target_tile, siapkan data untuk client
            if (!is_null($card->target_tile)) {
                $targetTile = BoardTile::where('position_index', $card->target_tile)->first();
                $possibleTiles = [$targetTile ? $targetTile->name : 'Unknown'];
                $dicePreroll = 0; // Client akan ambil possible_tiles[0]
                $targetPosition = $card->target_tile;
            } elseif ($card->action === 'roll_loss' || $card->action === 'random_choice') {
                // Jika action membutuhkan random choice
                $tileOptions = BoardTile::whereIn('name', ["Makan", "Transport", "Nongkrong"])->get();
                $possibleTiles = $tileOptions->pluck('name')->toArray();
                $dicePreroll = rand(0, count($possibleTiles) - 1); // Server tentukan hasil random

                // Get target position from selected tile
                if (isset($tileOptions[$dicePreroll])) {
                    $targetPosition = $tileOptions[$dicePreroll]->position_index;
                }
            }

            // 6. Simpan Perubahan ke Database
            $currentScores[$targetScoreKey] = $newValue;
            $profile->lifetime_scores = $currentScores;
            $profile->save();

            // UPDATE SESSION STATE
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with('session')
                ->first();

            if ($participation && $participation->session) {
                $session = $participation->session;
                $gameState = json_decode($session->game_state, true) ?? [];

                // Jika kartu memiliki target_tile atau random_choice dengan target position
                if (!is_null($targetPosition)) {
                    $oldPosition = $participation->position;
                    $newPosition = $targetPosition;

                    // Update posisi pemain
                    $participation->position = $newPosition;
                    $participation->save();

                    // Simpan detail pergerakan di game_state untuk referensi game client
                    $gameState['current_turn_action'] = [
                        'dice_value' => null, // Pergerakan dari kartu, bukan dadu
                        'from_tile' => $oldPosition,
                        'to_tile' => $newPosition,
                        'landed_event_type' => 'card_movement',
                        'landed_event_id' => $cardId
                    ];

                    // Set phase ke resolving_event agar client tahu harus tampilkan kartu dulu
                    $gameState['turn_phase'] = 'resolving_event';
                } else {
                    // Kartu tanpa pergerakan, langsung selesai
                    $gameState['turn_phase'] = 'event_completed';
                }

                unset($gameState['active_event']);

                $session->game_state = json_encode($gameState);
                $session->save();

                // Accumulate score changes ke session score
                $participation->score = ($participation->score ?? 0) + $actualScoreChange;
                $participation->save();

                // Update ranks untuk semua pemain dalam session
                $this->sessionService->updateSessionRanks($participation->sessionId);
            }

            // (Opsional) Catat ke tabel player_decisions sebagai history
            $this->logCardHistory($playerId, $cardId, $actualScoreChange);

            // 7. Format Response
            return [
                'card_category' => ucfirst($affectedCategoryKey),
                'title' => $card->title,
                'narration' => $card->narration,
                // 'real_score' => $baseChange,
                'score_change' => $actualScoreChange,
                'affected_score' => $targetScoreKey,
                'new_score_value' => $newValue,
                'dice_preroll_result' => $dicePreroll, // null jika tidak ada dadu
                'possible_tiles' => $possibleTiles     // null jika tidak ada dadu
            ];
        });
    }

    private function logCardHistory($playerId, $cardId, $scoreChange, $contentType = 'risk_card')
    {
        // Cari sesi aktif untuk konteks log
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->first();

        if ($participation && $participation->sessionId !== 'unknown') {
            PlayerDecision::create([
                'player_id' => $playerId,
                'session_id' => $participation->sessionId,
                'turn_number' => $participation->session->current_turn ?? 0,
                'content_id' => $cardId,
                'content_type' => $contentType,
                'is_correct' => 0, // Kartu tidak ada benar/salah
                'score_change' => $scoreChange,
                'created_at' => now()
            ]);
        }
    }

    public function drawChanceCard(string $playerId, string $cardId)
    {
        return DB::transaction(function () use ($playerId, $cardId) {
            // 1. Cari Kartu Kesempatan
            $card = Card::where('id', $cardId)->where('type', 'chance')->first();

            if (!$card) {
                return ['error' => 'Chance card not found'];
            }

            // 2. Ambil Profil Pemain
            $profile = PlayerProfile::find($playerId);
            if (!$profile) {
                return ['error' => 'Player profile not found'];
            }

            // 3. Tentukan Kategori Terpengaruh
            $categories = $card->categories ?? ['General'];
            $affectedCategoryKey = strtolower($categories[0] ?? 'pendapatan');

            // Mapping key skor (sama seperti risk)
            $scoreKeyMap = [
                'anggaran' => 'anggaran',
                'pendapatan' => 'pendapatan',
                'tabungan' => 'tabungan',
                'utang' => 'utang',
                'investasi' => 'investasi',
                'asuransi' => 'asuransi_dan_proteksi',
                'pendidikan' => 'tujuan_jangka_panjang' // Contoh mapping baru
            ];
            $targetScoreKey = $scoreKeyMap[$affectedCategoryKey] ?? $affectedCategoryKey;

            // 4. Hitung Perubahan Skor (Biasanya Positif)
            $currentScores = $profile->lifetime_scores ?? [];
            if (is_string($currentScores)) {
                $currentScores = json_decode($currentScores, true) ?? [];
            }
            $oldValue = $currentScores[$targetScoreKey] ?? 0;
            $baseChange = $card->scoreChange; // Nilai dari DB (misal: +5)

            // Apply weighted scoring
            $actualScoreChange = $this->scoringService->calculateWeightedScore(
                $oldValue,
                $card->difficulty ?? 2, // Default to medium difficulty if not set
                $baseChange
            );

            // Apply score change and clamp to 0-max range
            $maxScore = $this->scoringService->getMaxScore();
            $newValue = max(0, min($maxScore, $oldValue + $actualScoreChange));

            // 5. Logika Preroll / Possible Tiles
            $dicePreroll = null;
            $possibleTiles = null;
            $targetPosition = null; // Position to move player to

            // Jika kartu memiliki target_tile, siapkan data untuk client
            if (!is_null($card->target_tile)) {
                $targetTile = BoardTile::where('position_index', $card->target_tile)->first();
                $possibleTiles = [$targetTile ? $targetTile->name : 'Unknown'];
                $dicePreroll = 0; // Client akan ambil possible_tiles[0]
                $targetPosition = $card->target_tile;
            } elseif ($card->action === 'roll_gain' || $card->action === 'random_choice') {
                // Jika action membutuhkan random choice
                $tileOptions = BoardTile::whereIn('name', ["Makan", "Transport"])->get();
                $possibleTiles = $tileOptions->pluck('name')->toArray();
                $dicePreroll = rand(0, count($possibleTiles) - 1); // Server tentukan hasil random

                // Get target position from selected tile
                if (isset($tileOptions[$dicePreroll])) {
                    $targetPosition = $tileOptions[$dicePreroll]->position_index;
                }
            }

            // 6. Simpan Perubahan
            $currentScores[$targetScoreKey] = $newValue;
            $profile->lifetime_scores = $currentScores;
            $profile->save();

            // UPDATE SESSION STATE
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with('session')
                ->first();

            if ($participation && $participation->session) {
                $session = $participation->session;
                $gameState = json_decode($session->game_state, true) ?? [];

                // Jika kartu memiliki target_tile atau random_choice dengan target position
                if (!is_null($targetPosition)) {
                    $oldPosition = $participation->position;
                    $newPosition = $targetPosition;

                    // Update posisi pemain
                    $participation->position = $newPosition;
                    $participation->save();

                    // Simpan detail pergerakan di game_state untuk referensi game client
                    $gameState['current_turn_action'] = [
                        'dice_value' => null, // Pergerakan dari kartu, bukan dadu
                        'from_tile' => $oldPosition,
                        'to_tile' => $newPosition,
                        'landed_event_type' => 'card_movement',
                        'landed_event_id' => $cardId
                    ];

                    // Set phase ke resolving_event agar client tahu harus tampilkan kartu dulu
                    $gameState['turn_phase'] = 'resolving_event';
                } else {
                    // Kartu tanpa pergerakan, langsung selesai
                    $gameState['turn_phase'] = 'event_completed';
                }

                unset($gameState['active_event']);

                $session->game_state = json_encode($gameState);
                $session->save();

                // Accumulate score changes ke session score
                $participation->score = ($participation->score ?? 0) + $actualScoreChange;
                $participation->save();

                // Update ranks untuk semua pemain dalam session
                $this->sessionService->updateSessionRanks($participation->sessionId);
            }

            // Catat History
            $this->logCardHistory($playerId, $cardId, $actualScoreChange, 'chance_card');

            // 7. Format Response
            return [
                'card_category' => ucfirst($affectedCategoryKey),
                'title' => $card->title,
                'narration' => $card->narration,
                // 'real_score' => $baseChange,
                'score_change' => $actualScoreChange,
                'affected_score' => $targetScoreKey,
                'new_score_value' => $newValue,
                'dice_preroll_result' => $dicePreroll,
                'possible_tiles' => $possibleTiles
            ];
        });
    }

    public function getQuizCardDetail(string $playerId, string $quizId)
    {
        // 1. Ambil Data Kuis & Opsi
        $quiz = QuizCard::with([
            'options' => function ($q) {
                $q->orderBy('optionId');
            }
        ])->find($quizId);

        if (!$quiz) {
            return ['error' => 'Quiz card not found'];
        }

        // 2. Cek Intervensi
        // Apakah pemain ini sedang dalam kondisi "berisiko" (salah terus)?
        $interventionCheck = $this->interventionService->checkInterventionTrigger($playerId);
        $hasIntervention = !empty($interventionCheck);

        // 3. Tentukan Kategori (Ambil dari tags pertama atau default)
        $rawCategory = 'General';
        if (!empty($quiz->tags) && is_array($quiz->tags)) {
            $rawCategory = $quiz->tags[0];
        }

        // Mapping ke Readable Name - STRICT 7 CATEGORIES
        $categoryMap = [
            'pendapatan' => 'Pendapatan',
            'anggaran' => 'Anggaran',
            'tabungan_dan_dana_darurat' => 'Tabungan & Dana Darurat',
            'utang' => 'Utang',
            'investasi' => 'Investasi',
            'asuransi_dan_proteksi' => 'Asuransi & Proteksi',
            'tujuan_jangka_panjang' => 'Tujuan Jangka Panjang',
            // Mapping alias/legacy
            'literasi_dasar' => 'Tujuan Jangka Panjang', // Mapping logical untuk Quiz
            'risiko' => 'Asuransi & Proteksi'
        ];
        $readableCategory = $categoryMap[$rawCategory] ?? 'Tujuan Jangka Panjang'; // Default fallback safe

        // 4. Format Response
        return [
            'card_category' => $readableCategory,
            'question' => $quiz->question,
            'options' => $quiz->options->map(function ($opt) {
                return [
                    'id' => $opt->optionId,
                    'text' => $opt->text
                ];
            }),
            'intervention' => $hasIntervention
        ];
    }

    public function submitQuizAnswer(string $playerId, array $data)
    {
        return DB::transaction(function () use ($playerId, $data) {
            $quizId = $data['quiz_id'];
            $selectedOption = $data['selected_option'];

            // 1. Ambil Data Kuis
            $quiz = QuizCard::find($quizId);
            if (!$quiz) {
                return ['error' => 'Quiz card not found'];
            }

            // 2. Cek Kebenaran Jawaban
            // Di DB, correctOption menyimpan ID opsi yang benar (misal "B")
            $isCorrect = ($quiz->correctOption === $selectedOption);

            // Tentukan perubahan skor
            $scoreChange = $isCorrect ? $quiz->correctScore : $quiz->incorrectScore;

            // 3. Ambil Profil Pemain
            $profile = PlayerProfile::find($playerId);
            if (!$profile)
                return ['error' => 'Player profile not found'];

            $categoryLabel = $quiz->tags[0] ?? 'pendapatan';

            // Update Skor dengan Weighted Scoring
            $currentScores = $profile->lifetime_scores ?? [];
            $oldVal = $currentScores[$categoryLabel] ?? 0;

            $actualScoreChange = $this->scoringService->calculateWeightedScore(
                $oldVal,
                $quiz->difficulty ?? 2, // Default Medium (2)
                $scoreChange
            );

            // Apply score change and clamp to 0-max range
            $maxScore = $this->scoringService->getMaxScore();
            $newVal = max(0, min($maxScore, $oldVal + $actualScoreChange));

            $currentScores[$categoryLabel] = $newVal;
            $profile->lifetime_scores = $currentScores;
            $profile->save();

            // UPDATE SESSION STATE
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with('session')
                ->first();

            if ($participation && $participation->session) {
                $session = $participation->session;
                $gameState = json_decode($session->game_state, true) ?? [];

                $gameState['turn_phase'] = 'event_completed';
                unset($gameState['active_event']);

                $session->game_state = json_encode($gameState);
                $session->save();

                // Accumulate score changes ke session score
                $participation->score = ($participation->score ?? 0) + $actualScoreChange;
                $participation->save();

                // Update ranks untuk semua pemain dalam session
                $this->sessionService->updateSessionRanks($participation->sessionId);
            }

            // 6. Catat History Keputusan
            $this->logQuizDecision($playerId, $quizId, $selectedOption, $isCorrect, $actualScoreChange, $data['decision_time_seconds'] ?? 0);

            // 7. Get real-time prediction after quiz answer
            $prediction = null;
            try {
                $prediction = $this->predictionService->getCurrentPrediction($playerId);
                if (isset($prediction['error'])) {
                    $prediction = null;
                }
            } catch (\Exception $e) {
                Log::warning("Prediction failed after quiz answer: " . $e->getMessage());
            }

            // 8. Format Response
            $response = [
                'correct' => $isCorrect,
                // 'real_score' => $scoreChange,
                'score_change' => $actualScoreChange,
                'affected_score' => $categoryLabel,
                'new_score_value' => $newVal
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

    /**
     * Helper: Log ke tabel player_decisions
     */
    private function logQuizDecision($playerId, $contentId, $selection, $isCorrect, $change, $time)
    {
        // Cari sesi aktif
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with('session')
            ->first();

        $sessionId = $participation ? $participation->sessionId : 'unknown';
        $turnNum = $participation ? ($participation->session->current_turn ?? 0) : 0;

        // Validation: Don't log if session is unknown (prevents SQL error)
        if ($sessionId !== 'unknown') {
            PlayerDecision::create([
                'player_id' => $playerId,
                'session_id' => $sessionId,
                'turn_number' => $turnNum,
                'content_id' => $contentId,
                'content_type' => 'quiz',
                'selected_option' => $selection,
                'is_correct' => $isCorrect,
                'score_change' => $change,
                'decision_time_seconds' => $time,
                'created_at' => now()
            ]);
        }
    }
}