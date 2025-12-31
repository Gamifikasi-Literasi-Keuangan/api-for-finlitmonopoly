<?php

namespace App\Services;

use App\Services\AI\FuzzyService;
use App\Services\AI\ANNService;
use App\Models\PlayerProfile;
use App\Models\ProfilingInput;
use App\Repositories\ProfilingRepository;
use Illuminate\Support\Facades\Log;

class ProfilingService
{
    protected $fuzzy;
    protected $ann;
    protected $profilingRepository;


    public const CLUSTER_PROFILES = [
        'Financial Novice' => [
            'display_name' => 'HIGH RISK PLAYER',
            'level' => 'Critical',
            'traits' => ['Impulsive', 'FOMO-driven'],
            'recommended_focus' => ['Basic Budgeting', 'Debt Management']
        ],
        'Financial Explorer' => [
            'display_name' => 'MODERATE RISK PLAYER',
            'level' => 'High',
            'traits' => ['Experimental', 'Overconfident'],
            'recommended_focus' => ['Risk Awareness', 'Diversification']
        ],
        'Foundation Builder' => [
            'display_name' => 'CAUTIOUS PLAYER',
            'level' => 'Medium',
            'traits' => ['Conservative', 'Saver'],
            'recommended_focus' => ['Investment Basics', 'Inflation Protection']
        ],
        'Financial Architect' => [
            'display_name' => 'STRATEGIC PLAYER',
            'level' => 'Low',
            'traits' => ['Planner', 'Optimizer'],
            'recommended_focus' => ['Advanced Portfolio', 'Tax Efficiency']
        ],
        'Financial Sage' => [
            'display_name' => 'SECURE PLAYER',
            'level' => 'Safe',
            'traits' => ['Mentor', 'Philanthropist'],
            'recommended_focus' => ['Legacy Planning', 'Wealth Transfer']
        ],
        'default' => [
            'display_name' => 'UNKNOWN PLAYER',
            'level' => 'Unknown',
            'traits' => [],
            'recommended_focus' => []
        ]
    ];

    public function __construct(
        FuzzyService $fuzzy,
        ANNService $ann,
        ProfilingRepository $profilingRepository
    ) {
        $this->fuzzy = $fuzzy;
        $this->ann = $ann;
        $this->profilingRepository = $profilingRepository;
    }

    /**
     * Mengecek status profiling pemain berdasarkan data yang tersimpan.
     */
    public function getProfilingStatus(string $playerId)
    {
        $profile = PlayerProfile::find($playerId);

        if (!$profile) {
            return [
                'player_id' => $playerId,
                'status' => 'needed',
                'profiling_done' => false,
                'cluster' => null
            ];
        }

        if (!empty($profile->cluster)) {
            return [
                'player_id' => $playerId,
                'status' => 'completed',
                'profiling_done' => true,
                'cluster' => $profile->cluster,
                'level' => $profile->level,
                'last_updated' => $profile->last_updated
            ];
        }

        if (!empty($profile->onboarding_answers)) {
            return [
                'player_id' => $playerId,
                'status' => 'processing',
                'profiling_done' => false,
                'message' => 'Answers received, waiting for calculation.'
            ];
        }

        return [
            'profiling_done' => false,
            'cluster' => null
        ];
    }

     /**
     * Mengambil daftar pertanyaan profiling yang aktif
     */
    public function getActiveProfilingQuestions(): array
    {
        $questions = $this->profilingRepository->getProfilingQuestions();
        return $questions->map(function ($question) {
            return [
                'question_code' => $question->question_code,
                'text' => $question->question_text,
                'options' => $question->options
                    ->map(function ($option) {
                        return [
                            'option_token' => $option->option_token,
                            'text' => $option->option_text,
                        ];
                    })
                    ->shuffle()
                    ->values(),
            ];
        })->values()->toArray();
    }

    /**
     * Menyimpan jawaban onboarding pemain dan, bila diminta,
     * memicu proses profiling (clustering) secara otomatis.
     */
    public function saveOnboardingAnswers(array $input)
    {
        $playerId = $input['player_id'];
        $answers  = $input['answers'];

        PlayerProfile::updateOrCreate(
            ['PlayerId' => $input['player_id']],
            [
                'onboarding_answers' => json_encode($input['answers']),
                'last_updated' => now(),
            ]
        );

        // Menyimpan jawaban satu per satu  
        foreach ($answers as $answer) {
            $questionCode = $answer['question_code'];
            $optionToken  = $answer['option_token'];

            $option = $this->profilingRepository
                ->getOptionByToken($questionCode, $optionToken);

            if (!$option) {
                throw new \Exception(
                    "Invalid option token for question {$questionCode}"
                );
            }

            $this->profilingRepository->saveAnswer(
                $playerId,
                $option->question_id,
                $option->option_code 
            );
        }

        if ($input['player_id'] === 'player_dummy_profiling_infinite') {
            $calculatedFeatures = [
                'pendapatan' => 30,
                'anggaran' => 30,
                'tabungan_dan_dana_darurat' => 20,
                'utang' => 10,
                'investasi' => 10,
                'asuransi_dan_proteksi' => 20,
                'tujuan_jangka_panjang' => 30
            ];
        } else {
            $calculatedFeatures = $this->calculateFeaturesFromAnswers($playerId);
        }

        $profilingInput = ProfilingInput::create([
            'player_id' => $playerId,
            'feature' => json_encode($calculatedFeatures),
            'created_at' => now(),
        ]);

        // return ['ok' => true];

        $profilingResult = null;
        if (!empty($input['profiling_done']) && $input['profiling_done'] === true) {
            try {
                $profilingResult = $this->runProfilingCluster($input['player_id'], $profilingInput);
            } catch (\Exception $e) {
                Log::error("Profiling calculation failed for {$input['player_id']}: " . $e->getMessage());
                return ['ok' => false, 'error' => $e->getMessage()];
            }
        }
        return ['ok' => true, 'profiling_result' => $profilingResult];
    }

    /**
     * Menghitung fitur dari jawaban onboarding pemain.
     * Menggunakan sistem poin yang telah ditentukan.
     */
    public function calculateFeaturesFromAnswers(string $playerId): array
    {
        // Mengambil semua pertanyaan aktif
        $questions = $this->profilingRepository->getProfilingQuestions();
        
        // Mengambil semua jawaban pemain
        $answers = $this->profilingRepository->getAnswersByPlayerId($playerId)
            ->keyBy('question_id');
        
        $scores = [];

        foreach ($questions as $question) {
            foreach ($question->aspects as $aspect) {
                $scores[$aspect->aspect_key] ??= 0;
            }
        }

        // Menghitung Skor Pemain berdasarkan sistem poin
        foreach ($questions as $question) {

            if (!isset($answers[$question->id])) {
                continue;
            }

            $answer = $answers[$question->id];
            $optionScore = $this->profilingRepository
                ->getOptionScore($question->id, $answer->answer);

            if ($optionScore === null) {
                continue;
            }

            // Normalisasi (0-100)
            $normalizedScore = ($optionScore / $question->max_score) * 100;

            foreach ($question->aspects as $aspect) {
                if ($aspect->aspect_key === 'utang') {
                    $scores['utang'] = 100 - $normalizedScore;
                } else {
                    $scores[$aspect->aspect_key] = $normalizedScore;
                }
            }
        }

        return $scores;
    }

    /**
     * Menjalankan proses profiling (clustering) untuk seorang pemain.
     */
    public function runProfilingCluster(string $playerId, $directInput = null)
    {
        $input = $directInput ?? ProfilingInput::where('player_id', $playerId)->latest()->first();

        // 1b. Mock Input for Dummy Player if missing
        if (!$input && $playerId === 'player_dummy_profiling_infinite') {
            $input = new \stdClass();
            $input->feature = json_encode([
                'pendapatan' => 30,
                'anggaran' => 30,
                'tabungan_dan_dana_darurat' => 20,
                'utang' => 10,
                'investasi' => 10,
                'asuransi_dan_proteksi' => 20,
                'tujuan_jangka_panjang' => 30
            ]);
        }

        if (!$input) {
            return ['error' => 'No profiling input found'];
        }

        $features = json_decode($input->feature, true);
        
        #Profiling dengan Fuzzy Logic
        $fuzzyOutput = $this->fuzzy->categorize($playerId, $features);
        $linguisticLabels = $fuzzyOutput['fuzzy_categories'];
        
        #Profiling dengan ANN dengan PHP-ML
        $finalClass = $this->ann->predict($linguisticLabels);
        $profileData = self::CLUSTER_PROFILES[$finalClass] ?? self::CLUSTER_PROFILES['default'];

        asort($features);
        $lowestScores = array_slice(array_keys($features), 0, 3);

        $dynamicWeakAreas = array_map(function ($key) {
            return ucwords(str_replace(['_dan_', '_'], [' & ', ' '], $key));
        }, $lowestScores);

        PlayerProfile::where('PlayerId', $playerId)->update([
            'cluster' => $profileData['display_name'],
            'level' => $profileData['level'],
            'traits' => json_encode($profileData['traits']),
            'weak_areas' => json_encode($dynamicWeakAreas),
            'recommended_focus' => $profileData['recommended_focus'][0] ?? null,
            'lifetime_scores' => json_encode($features),
            'last_updated' => now(),
        ]);

        return [
            'cluster_id' => $finalClass,
            'cluster_name' => $profileData['display_name'],
            'level' => $profileData['level'],
            'traits' => $profileData['traits'],
            'weak_areas' => $dynamicWeakAreas,
            'recommended_focus' => $profileData['recommended_focus'],
        ];
    }
}

