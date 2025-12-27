<?php

namespace App\Services;

use App\Services\AI\FuzzyService;
use App\Services\AI\ANNService;
use App\Models\PlayerProfile;
use App\Models\ProfilingInput;

class ProfilingService
{
    protected $fuzzy;
    protected $ann;

    private const CLUSTER_PROFILES = [
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
    ) {
        $this->fuzzy = $fuzzy;
        $this->ann = $ann;
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
     * Menyimpan jawaban onboarding pemain dan, bila diminta,
     * memicu proses profiling (clustering) secara otomatis.
     */
    public function saveOnboardingAnswers(array $input)
    {
        PlayerProfile::updateOrCreate(
            ['PlayerId' => $input['player_id']],
            [
                'onboarding_answers' => json_encode($input['answers']),
                'last_updated' => now(),
            ]
        );
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
            $calculatedFeatures = [
                'pendapatan' => 50,
                'anggaran' => 60,
                'tabungan_dan_dana_darurat' => 40,
                'utang' => 20,
                'investasi' => 10,
                'asuransi_dan_proteksi' => 30,
                'tujuan_jangka_panjang' => 50
            ];
        }
        $profilingInput = ProfilingInput::create([
            'player_id' => $input['player_id'],
            'feature' => json_encode($calculatedFeatures),
            'created_at' => now(),
        ]);

        $profilingResult = null;
        if (!empty($input['profiling_done']) && $input['profiling_done'] === true) {
            try {
                $profilingResult = $this->runProfilingCluster($input['player_id'], $profilingInput);
            } catch (\Exception $e) {
                \Log::error("Profiling calculation failed for {$input['player_id']}: " . $e->getMessage());
                return ['ok' => false, 'error' => $e->getMessage()];
            }
        }
        return ['ok' => true, 'profiling_result' => $profilingResult];
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
        $linguisticLabels = $this->fuzzy->categorize($features);
        
        // Prediksi menggunakan ANN dengan PHP-ML
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
            'cluster' => $finalClass,
            'level' => $profileData['level'],
            'traits' => $profileData['traits'],
            'weak_areas' => $dynamicWeakAreas,
            'recommended_focus' => $profileData['recommended_focus'],
        ];
    }
}

