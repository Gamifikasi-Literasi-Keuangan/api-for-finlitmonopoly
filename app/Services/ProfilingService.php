<?php

namespace App\Services;

use App\Services\AI\FuzzyService;
use App\Services\AI\ANNService;
use App\Services\AI\CosineSimilarityService;
use App\Models\PlayerProfile;
use App\Models\ProfilingInput;
use App\Models\ProfilingResult;

class ProfilingService
{
    protected $fuzzy;
    protected $ann;
    protected $cosine;

    private const CLUSTER_PROFILES = [
        'Financial Novice' => [
            'level' => 'critical',
            'traits' => ['unaware', 'vulnerable', 'reactive'],
            'weak_areas' => ['pendapatan', 'anggaran', 'utang', 'tabungan_dan_dana_darurat'],
            'recommended_focus' => ['basic_budgeting', 'debt_avoidance']
        ],
        'Financial Explorer' => [
            'level' => 'high_risk',
            'traits' => ['impulsive', 'fomo_driven', 'experimental'],
            'weak_areas' => ['utang', 'anggaran', 'tujuan_jangka_panjang'],
            'recommended_focus' => ['debt_management', 'risk_awareness']
        ],
        'Foundation Builder' => [
            'level' => 'moderate',
            'traits' => ['saver', 'cautious', 'planner'],
            'weak_areas' => ['investasi', 'asuransi_dan_proteksi'],
            'recommended_focus' => ['intro_to_investing', 'emergency_fund']
        ],
        'Financial Architect' => [
            'level' => 'stable',
            'traits' => ['proactive', 'investor', 'optimizer'],
            'weak_areas' => ['investment_optimization', 'tax_efficiency'],
            'recommended_focus' => ['advanced_investing', 'portfolio_diversification']
        ],
        'Financial Sage' => [
            'level' => 'secure',
            'traits' => ['strategic', 'disciplined', 'legacy_focused'],
            'weak_areas' => ['estate_planning', 'wealth_transfer'],
            'recommended_focus' => ['estate_planning', 'advanced_wealth_strategy']
        ],
        'default' => [
            'level' => 'unknown',
            'traits' => [],
            'weak_areas' => [],
            'recommended_focus' => []
        ]
    ];
    
    public function __construct(
        FuzzyService $fuzzy,
        ANNService $ann,
        CosineSimilarityService $cosine
    ) {
        $this->fuzzy = $fuzzy;
        $this->ann = $ann;
        $this->cosine = $cosine;
    }
    public function saveOnboardingAnswers(array $input)
    {
        $player = PlayerProfile::updateOrCreate(
            ['PlayerId' => $input['player_id']],
            [
                'onboarding_answers' => json_encode($input['answers']),
                'locale' => $input['locale'],
                'last_updated' => now(),
            ]
        );

        return [
            'player_id' => $input['player_id'],
            'onboarding_answers' => $input['answers'],
            'platform' => $input['platform'],
            'status' => 'recorded',
            'session_id' => $input['session_id'],
        ];
    }

    public function runProfilingCluster(string $playerId)
    {
        $input = ProfilingInput::where('player_id', $playerId)->latest()->first();
        if (!$input) {
            return ['error' => 'No profiling input found'];
        }

        $features = json_decode($input->feature, true);
        $linguisticLabels = $this->fuzzy->categorize($features);
        $finalClass = $this->ann->getFinalClass($linguisticLabels);
        $profileData = self::CLUSTER_PROFILES[$finalClass] ?? self::CLUSTER_PROFILES['default'];

        PlayerProfile::where('PlayerId', $playerId)->update([
            'cluster' => $finalClass,
            'level' => $profileData['level'],
            'traits' => json_encode($profileData['traits']),
            'weak_areas' => json_encode($profileData['weak_areas']),
            'recommended_fokus' => $profileData['recommended_focus'][0] ?? null, 
            'lifetime_scores' => json_encode($features),
            'last_updated' => now(),
        ]);

        return [
            'player_id' => $playerId,
            'cluster' => $finalClass,
            'level' => $profileData['level'],
            'traits' => $profileData['traits'],
            'weak_areas' => $profileData['weak_areas'], 
            'recommended_focus' => $profileData['recommended_fokus'],
            'confidence_level' => $this->ann->getConfidence(),
            'profiling_version' => 2,
            'generated_at' => now()->toISOString(),
        ];
    }
}

