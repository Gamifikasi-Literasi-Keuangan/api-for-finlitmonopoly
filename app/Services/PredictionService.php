<?php

namespace App\Services;

use App\Services\AI\FuzzyService;
use App\Services\AI\ANNService;
use App\Models\PlayerProfile;
use App\Models\ProfilingInput;
use App\Models\ParticipatesIn;
use App\Models\PlayerDecision;
use Illuminate\Support\Facades\Log;

class PredictionService
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
     * Re-evaluate player's profile based on current lifetime scores
     * Called after gameplay decisions (scenarios, quizzes, cards)
     */
    public function reevaluatePlayerProfile(string $playerId, bool $updateProfile = true)
    {
        try {
            $profile = PlayerProfile::find($playerId);

            if (!$profile) {
                return ['error' => 'Player profile not found'];
            }

            // Get current lifetime scores
            $features = $profile->lifetime_scores ?? [];
            if (is_string($features)) {
                $features = json_decode($features, true) ?? [];
            }

            // Ensure all required features exist
            $requiredFeatures = [
                'pendapatan', 'anggaran', 'tabungan_dan_dana_darurat',
                'utang', 'investasi', 'asuransi_dan_proteksi', 'tujuan_jangka_panjang'
            ];

            foreach ($requiredFeatures as $feature) {
                if (!isset($features[$feature])) {
                    $features[$feature] = 0;
                }
            }

            // Convert scores to linguistic labels using Fuzzy Logic
            $linguisticLabels = $this->fuzzy->categorize($playerId, $features);

            // Predict new cluster using ANN
            $predictedCluster = $this->ann->predict($linguisticLabels);
            $profileData = self::CLUSTER_PROFILES[$predictedCluster] ?? self::CLUSTER_PROFILES['default'];

            // Calculate weak areas (lowest 3 scores)
            asort($features);
            $lowestScores = array_slice(array_keys($features), 0, 3);

            $weakAreas = array_map(function ($key) {
                return ucwords(str_replace(['_dan_', '_'], [' & ', ' '], $key));
            }, $lowestScores);

            // Get original cluster for comparison
            $originalCluster = $profile->cluster;

            $result = [
                'player_id' => $playerId,
                'original_cluster' => $originalCluster,
                'predicted_cluster' => $profileData['display_name'],
                'predicted_class' => $predictedCluster,
                'cluster_changed' => $originalCluster !== $profileData['display_name'],
                'level' => $profileData['level'],
                'traits' => $profileData['traits'],
                'weak_areas' => $weakAreas,
                'recommended_focus' => $profileData['recommended_focus'],
                'current_scores' => $features,
                'linguistic_labels' => $linguisticLabels
            ];

            // Update profile if requested
            if ($updateProfile) {
                $profile->cluster = $profileData['display_name'];
                $profile->level = $profileData['level'];
                $profile->traits = json_encode($profileData['traits']);
                $profile->weak_areas = json_encode($weakAreas);
                $profile->recommended_focus = $profileData['recommended_focus'][0] ?? null;
                $profile->last_updated = now();
                $profile->save();

                Log::info("Player {$playerId} profile updated. Cluster: {$profileData['display_name']}");
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Prediction error for player {$playerId}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get real-time prediction during gameplay without updating profile
     * Useful for showing player their current trajectory
     */
    public function getCurrentPrediction(string $playerId)
    {
        return $this->reevaluatePlayerProfile($playerId, false);
    }

    /**
     * Analyze player progress when they pause/stop the game
     * Provides comprehensive analysis of gameplay so far
     */
    public function analyzePauseState(string $playerId)
    {
        try {
            $profile = PlayerProfile::find($playerId);

            if (!$profile) {
                return ['error' => 'Player profile not found'];
            }

            // Get current prediction
            $currentPrediction = $this->getCurrentPrediction($playerId);

            // Get active session info
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with('session')
                ->first();

            if (!$participation) {
                return ['error' => 'Player not in active session'];
            }

            $sessionId = $participation->sessionId;
            $turnNumber = $participation->session->current_turn ?? 0;

            // Calculate decision statistics
            $decisions = PlayerDecision::where('player_id', $playerId)
                ->where('session_id', $sessionId)
                ->get();

            $totalDecisions = $decisions->count();
            $correctDecisions = $decisions->where('is_correct', true)->count();
            $totalScoreChange = $decisions->sum('score_change');
            $avgDecisionTime = $decisions->avg('decision_time_seconds');

            // Breakdown by content type
            $scenarioDecisions = $decisions->where('content_type', 'scenario')->count();
            $quizDecisions = $decisions->where('content_type', 'quiz')->count();

            // Calculate improvement rate
            $accuracyRate = $totalDecisions > 0 ? ($correctDecisions / $totalDecisions) * 100 : 0;

            return [
                'player_id' => $playerId,
                'session_id' => $sessionId,
                'current_turn' => $turnNumber,
                'profile_analysis' => $currentPrediction,
                'gameplay_statistics' => [
                    'total_decisions' => $totalDecisions,
                    'correct_decisions' => $correctDecisions,
                    'accuracy_rate' => round($accuracyRate, 2),
                    'total_score_change' => $totalScoreChange,
                    'avg_decision_time_seconds' => round($avgDecisionTime, 2),
                    'scenarios_answered' => $scenarioDecisions,
                    'quizzes_answered' => $quizDecisions
                ],
                'recommendations' => $this->generateRecommendations($currentPrediction, $accuracyRate)
            ];

        } catch (\Exception $e) {
            Log::error("Pause analysis error for player {$playerId}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Final evaluation when session ends
     * Updates profile permanently and provides comprehensive report
     */
    public function finalizeSessionEvaluation(string $playerId, string $sessionId)
    {
        try {
            // Get final prediction and update profile
            $finalPrediction = $this->reevaluatePlayerProfile($playerId, true);

            if (isset($finalPrediction['error'])) {
                return $finalPrediction;
            }

            // Get session statistics
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->where('sessionId', $sessionId)
                ->first();

            if (!$participation) {
                return ['error' => 'Participation record not found'];
            }

            // Get all decisions from this session
            $decisions = PlayerDecision::where('player_id', $playerId)
                ->where('session_id', $sessionId)
                ->get();

            $totalDecisions = $decisions->count();
            $correctDecisions = $decisions->where('is_correct', true)->count();
            $totalScoreChange = $decisions->sum('score_change');
            $accuracyRate = $totalDecisions > 0 ? ($correctDecisions / $totalDecisions) * 100 : 0;

            // Calculate skill improvements
            $skillImprovements = $this->calculateSkillImprovements($playerId, $sessionId);

            // Generate final report
            return [
                'player_id' => $playerId,
                'session_id' => $sessionId,
                'final_profile' => $finalPrediction,
                'session_summary' => [
                    'total_decisions' => $totalDecisions,
                    'correct_decisions' => $correctDecisions,
                    'accuracy_rate' => round($accuracyRate, 2),
                    'total_score_gained' => $totalScoreChange,
                    'final_position' => $participation->position,
                    'final_session_score' => $participation->score
                ],
                'skill_improvements' => $skillImprovements,
                'achievement_message' => $this->generateAchievementMessage($finalPrediction),
                'next_steps' => $finalPrediction['recommended_focus'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error("Session finalization error for player {$playerId}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Calculate which skills improved during the session
     */
    private function calculateSkillImprovements(string $playerId, string $sessionId)
    {
        $profile = PlayerProfile::find($playerId);
        if (!$profile) {
            return [];
        }

        $currentScores = $profile->lifetime_scores ?? [];
        if (is_string($currentScores)) {
            $currentScores = json_decode($currentScores, true) ?? [];
        }

        // Get decisions and group by affected areas
        $decisions = PlayerDecision::where('player_id', $playerId)
            ->where('session_id', $sessionId)
            ->get();

        $improvements = [];
        foreach ($currentScores as $skill => $score) {
            if ($score > 0) {
                $improvements[] = [
                    'skill' => ucwords(str_replace(['_dan_', '_'], [' & ', ' '], $skill)),
                    'current_score' => $score,
                    'status' => $score >= 70 ? 'Strong' : ($score >= 40 ? 'Developing' : 'Needs Focus')
                ];
            }
        }

        return $improvements;
    }

    /**
     * Generate personalized recommendations based on prediction and performance
     */
    private function generateRecommendations(array $prediction, float $accuracyRate)
    {
        $recommendations = [];

        // Accuracy-based recommendations
        if ($accuracyRate < 50) {
            $recommendations[] = "Take your time to read questions carefully";
            $recommendations[] = "Review financial literacy basics";
        } elseif ($accuracyRate < 70) {
            $recommendations[] = "You're on the right track, keep learning!";
            $recommendations[] = "Focus on understanding consequences of decisions";
        } else {
            $recommendations[] = "Excellent decision-making! Keep it up!";
            $recommendations[] = "Challenge yourself with more complex scenarios";
        }

        // Cluster-based recommendations
        if (isset($prediction['weak_areas']) && !empty($prediction['weak_areas'])) {
            $weakestArea = $prediction['weak_areas'][0] ?? 'Financial Planning';
            $recommendations[] = "Strengthen your {$weakestArea} skills";
        }

        return $recommendations;
    }

    /**
     * Generate achievement message based on final profile
     */
    private function generateAchievementMessage(array $finalProfile)
    {
        if (!isset($finalProfile['cluster_changed']) || !$finalProfile['cluster_changed']) {
            return "You maintained your financial profile. Keep building your skills!";
        }

        $originalLevel = $this->getLevelRank($finalProfile['original_cluster'] ?? '');
        $newLevel = $this->getLevelRank($finalProfile['predicted_cluster'] ?? '');

        if ($newLevel > $originalLevel) {
            return "🎉 Congratulations! You've improved from {$finalProfile['original_cluster']} to {$finalProfile['predicted_cluster']}!";
        } elseif ($newLevel < $originalLevel) {
            return "Your profile changed to {$finalProfile['predicted_cluster']}. Focus on better financial decisions!";
        }

        return "Your financial profile has been updated to {$finalProfile['predicted_cluster']}.";
    }

    /**
     * Get numerical rank for cluster comparison
     */
    private function getLevelRank(string $cluster): int
    {
        $ranks = [
            'HIGH RISK PLAYER' => 1,
            'MODERATE RISK PLAYER' => 2,
            'CAUTIOUS PLAYER' => 3,
            'STRATEGIC PLAYER' => 4,
            'SECURE PLAYER' => 5
        ];

        return $ranks[$cluster] ?? 0;
    }
}
