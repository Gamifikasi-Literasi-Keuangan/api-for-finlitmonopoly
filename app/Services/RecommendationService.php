<?php

namespace App\Services;

use App\Models\PlayerProfile;
use App\Services\AI\CosineSimilarityService;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    protected $cosine;

    public function __construct(CosineSimilarityService $cosine)
    {
        $this->cosine = $cosine;
    }

    /*
     * Memberikan rekomendasi skenario pertanyaan berikutnya untuk pemain:
     * mengambil profil dan skor lifetime, menentukan kategori terlemah,
     * mencari daftar skenario yang relevan, menghitung kemiripan (cosine similarity)
     * antara kemampuan pemain dan tingkat kesulitan skenario, lalu memilih
     * pertanyaan terbaik yang dapat meningkatkan skor pemain.
     */
    public function recommendNextQuestion(string $playerId)
    {
        $profile = PlayerProfile::find($playerId);
        if (!$profile || empty($profile->lifetime_scores)) {
            return ['error' => 'Player profile or scores not found'];
        }

        $userScores = $profile->lifetime_scores;

        if (!is_array($userScores)) {
            return ['error' => 'Invalid score format'];
        }

        $weakestCategory = $this->findWeakestCategory($userScores);
        $userWeakestScore = $userScores[$weakestCategory] ?? 0;
        
        // Konversi user score (0-100) ke difficulty level (1-3)
        $userLevel = $this->convertScoreToLevel($userWeakestScore);

        $questions = DB::table('scenarios')
            ->where('category', $weakestCategory)
            ->get();

        if ($questions->isEmpty()) {
            return ['error' => 'No questions found for category: ' . $weakestCategory];
        }

        $bestQuestion = null;
        $maxSimilarity = -1;
        $userVector = $this->prepareVector($userScores);

        foreach ($questions as $question) {
            // Bandingkan difficulty skenario dengan level pemain (keduanya skala 1-3)
            if ($question->difficulty <= $userLevel) {
                continue;
            }

            $questionVector = $this->createQuestionVector($question->category, $question->difficulty, array_keys($userScores));
            $similarity = $this->cosine->calculate($userVector, $questionVector);

            if ($similarity > $maxSimilarity) {
                $maxSimilarity = $similarity;
                $bestQuestion = $question;
            }
        }

        if (!$bestQuestion && $questions->isNotEmpty()) {
            $bestQuestion = $questions->sortBy('difficulty')->first();
        }

        if (!$bestQuestion) {
            return ['error' => 'Belum ada konten skenario yang tersedia di database.'];
        }

        $categoryName = ucwords(str_replace(['_dan_', '_'], [' & ', ' '], $weakestCategory));

        return [
            'scenario_id' => $bestQuestion->id,
            'title' => $bestQuestion->title,
            'reason' => "Fokus pada area lemah: $categoryName (skor {$userWeakestScore}/100)",
            'expected_benefit' => "+{$bestQuestion->expected_benefit} points jika diselesaikan dengan benar",
            'peer_insight' => $this->generatePeerInsight($weakestCategory)
        ];
    }

    /*
     * Menghasilkan jalur rekomendasi peningkatan skor untuk pemain:
     * membaca profil dan skor saat ini, menentukan target skor berikutnya,
     * memetakan area kelemahan, lalu membuat estimasi langkah, waktu, dan potensi peningkatan.
     */
    public function getRecommendationPath(string $playerId)
    {
        $profile = null;

        // Special handling for dummy infinite profile
        if ($playerId === 'player_dummy_profiling_infinite') {
            // Try to find if exists, otherwise mock
            $profile = PlayerProfile::find($playerId);

            if (!$profile) {
                $profile = new PlayerProfile();
                $profile->PlayerId = $playerId;
            }
            // Inject dummy data for logic processing
            // Target: Overall 58. Weak areas: Tabungan & Utang.
            $profile->lifetime_scores = [
                'pendapatan' => 65,
                'anggaran' => 65,
                'tabungan_dan_dana_darurat' => 50,
                'utang' => 45,
                'investasi' => 60,
                'asuransi_dan_proteksi' => 60,
                'tujuan_jangka_panjang' => 60
            ];
            $profile->weak_areas = ['Dana Darurat & Tabungan', 'Utang & Paylater'];
            $profile->confidence_level = 0.78;
        } else {
            $profile = PlayerProfile::find($playerId);
        }


        if (!$profile)
            return null;

        $userScores = $profile->lifetime_scores ?? [];
        if (is_string($userScores)) {
            $userScores = json_decode($userScores, true) ?? [];
        }

        $currentScore = $this->calculateOverall($userScores);
        $currentScore = round($currentScore);

        $targetScore = 80;
        if ($currentScore >= 80)
            $targetScore = 90;
        if ($currentScore >= 90)
            $targetScore = 100;

        $weakAreas = $profile->weak_areas ?? ['literasi_dasar'];
        if (is_string($weakAreas)) {
            $weakAreas = json_decode($weakAreas, true) ?? ['literasi_dasar'];
        }

        $steps = [];
        $phase = 1;
        $totalMinSessions = 0;
        $totalMaxSessions = 0;
        $totalGain = 0;

        foreach ($weakAreas as $area) {
            $focusName = ucwords(str_replace(['_dan_', '_'], [' & ', ' '], $area));

            // Logic range: 2 to 3 sessions
            $minSessions = 2;
            $maxSessions = rand(2, 3);
            $gain = rand(10, 15);

            $timeString = ($minSessions == $maxSessions) ? "{$minSessions} sesi" : "{$minSessions}-{$maxSessions} sesi";

            $steps[] = [
                'phase' => $phase++,
                'focus' => $focusName,
                'estimated_time' => $timeString,
                'estimated_gain' => "+{$gain} poin"
            ];

            $totalMinSessions += $minSessions;
            $totalMaxSessions += $maxSessions;
            $totalGain += $gain;
        }

        $totalTimeString = ($totalMinSessions == $totalMaxSessions) ? "{$totalMinSessions} sesi" : "{$totalMinSessions}-{$totalMaxSessions} sesi";

        return [
            'title' => "Path Optimal ke Skor {$targetScore}+",
            'current_score' => $currentScore,
            'target_score' => $targetScore,
            'steps' => $steps,
            'total_estimated_time' => $totalTimeString,
            'success_probability' => number_format($profile->confidence_level * 100, 0) . "% berdasarkan pemain serupa"
        ];
    }

    /**
     * Menghasilkan perbandingan performa pemain terhadap seluruh pemain lain
     * Mengambil profil pemain dan menghitung skor keseluruhan saat ini, mengumpulkan skor keseluruhan dari semua pemain sebagai basis komparasi, 
     * menghitung rata-rata, persentil, serta ambang masuk Top 10%, menyusun insight naratif berdasarkan posisi pemain dan area kelemahan.
     */
    public function getPeerComparison(string $playerId)
    {
        $currentPlayer = PlayerProfile::find($playerId);
        if (!$currentPlayer || empty($currentPlayer->lifetime_scores)) {
            return null;
        }

        $currentScoresRaw = $currentPlayer->lifetime_scores;
        if (is_string($currentScoresRaw)) {
            $currentScoresRaw = json_decode($currentScoresRaw, true) ?? [];
        }
        $playerScore = $this->calculateOverall($currentScoresRaw);

        $allProfiles = PlayerProfile::whereNotNull('lifetime_scores')->get();

        $allOverallScores = [];
        foreach ($allProfiles as $p) {
            $scores = $p->lifetime_scores;
            if (is_string($scores)) {
                $scores = json_decode($scores, true) ?? [];
            }
            if (is_array($scores)) {
                $allOverallScores[] = $this->calculateOverall($scores);
            }
        }

        if (empty($allOverallScores)) {
            $allOverallScores = [0];
        }

        $count = count($allOverallScores);
        $average = array_sum($allOverallScores) / $count;

        sort($allOverallScores);

        $rank = 0;
        foreach ($allOverallScores as $s) {
            if ($s < $playerScore)
                $rank++;
        }
        $percentile = ($count > 1) ? ($rank / ($count - 1)) * 100 : 100;

        $top10Index = floor(0.9 * $count);
        $top10Index = min($top10Index, $count - 1);
        $top10Threshold = $allOverallScores[$top10Index];

        $weakAreas = $currentPlayer->weak_areas ?? [];

        $insights = [];
        if ($playerScore >= $average) {
            $insights[] = "Skor kamu di atas rata-rata pemain lain! 👍";
        } else {
            $insights[] = "Skor kamu sedikit di bawah rata-rata, yuk kejar ketertinggalan!";
        }

        if ($playerScore < $top10Threshold) {
            $gap = round($top10Threshold - $playerScore);
            $insights[] = "Butuh +{$gap} poin lagi untuk masuk jajaran Top 10% Elite.";
        } else {
            $insights[] = "Luar biasa! Kamu termasuk dalam Top 10% pemain terbaik.";
        }

        if (!empty($weakAreas)) {
            $weakest = ucwords(str_replace(['_dan_', '_'], [' & ', ' '], $weakAreas[0]));
            $insights[] = "Pemain yang fokus memperbaiki '$weakest' biasanya naik level 45% lebih cepat.";
        }

        return [
            'player_score' => round($playerScore),
            'average' => round($average),
            'percentile' => round($percentile),
            'top_10_threshold' => round($top10Threshold),
            'insights' => $insights
        ];
    }

    /**
     * Menghitung rata-rata dari array skor kategori
     */
    private function calculateOverall(array $scores): float
    {
        // Filter hanya nilai numerik untuk keamanan
        $numericScores = array_filter($scores, 'is_numeric');

        if (empty($numericScores))
            return 0.0;

        return array_sum($numericScores) / count($numericScores);
    }

    /**
     * Mencari kategori dengan skor terendah
     */
    private function findWeakestCategory(array $scores): string
    {
        asort($scores);
        return array_key_first($scores);
    }

    /**
     * Mempersiapkan vektor dari skor kategori untuk perhitungan cosine similarity
     */
    private function prepareVector(array $scores): array
    {
        $categories = ['pendapatan', 'anggaran', 'tabungan_dan_dana_darurat', 'utang', 'investasi', 'asuransi_dan_proteksi', 'tujuan_jangka_panjang'];
        $vector = [];
        foreach ($categories as $cat) {
            $vector[] = $scores[$cat] ?? 0;
        }
        return $vector;
    }

    /**
     * Membuat vektor pertanyaan berdasarkan kategori dan tingkat kesulitan
     */
    private function createQuestionVector(string $category, float $difficulty, array $allCategories): array
    {
        $normalizedCategory = strtolower(str_replace([' ', '&'], ['_', 'dan'], $category));

        $vectorTemplate = array_fill_keys(['pendapatan', 'anggaran', 'tabungan_dan_dana_darurat', 'utang', 'investasi', 'asuransi_dan_proteksi', 'tujuan_jangka_panjang'], 0);

        if (array_key_exists($normalizedCategory, $vectorTemplate)) {
            $vectorTemplate[$normalizedCategory] = $difficulty;
        }

        return array_values($vectorTemplate);
    }
    /**
     * Menghasilkan insight rekan sebaya berdasarkan kategori
     */
    private function generatePeerInsight(string $category): string
    {
        $categoryName = ucwords(str_replace(['_dan_', '_'], [' & ', ' '], $category));
        return "Insight: Mayoritas pemain berhasil meningkatkan skor $categoryName mereka dalam 3 sesi latihan.";
    }

    /**
     * Konversi skor lifetime (0-100) ke level difficulty (1-3)
     * 
     * @param float $score Skor pemain dalam rentang 0-100
     * @return int Level difficulty (1=Pemula, 2=Intermediate, 3=Advanced)
     */
    private function convertScoreToLevel(float $score): int
    {
        if ($score < 34) {
            return 1; // Pemula (0-33)
        }
        
        if ($score < 67) {
            return 2; // Intermediate (34-66)
        }
        
        return 3; // Advanced (67-100)
    }
}
