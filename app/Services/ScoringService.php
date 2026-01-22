<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoringService
{
    /**
     * Mendapatkan nilai konfigurasi dari database dengan caching
     * 
     * @param string $key Config key
     * @param float $default Default value jika tidak ditemukan
     * @return float
     */
    public function getConfig(string $key, float $default): float
    {
        return Cache::remember("scoring_config_{$key}", 3600, function () use ($key, $default) {
            $config = DB::table('scoring_config')
                ->where('config_key', $key)
                ->first();

            return $config ? (float) $config->config_value : $default;
        });
    }

    /**
     * Hitung weighted score change berdasarkan player skill vs question difficulty
     * 
     * @param float $playerCurrentScore Skor pemain saat ini di kategori tertentu (0-100)
     * @param int $questionDifficulty Tingkat kesulitan soal (1=mudah, 2=sedang, 3=sulit)
     * @param float $baseScoreChange Perubahan skor dasar dari database (bisa positif/negatif)
     * @return float Actual score change setelah weighted (rounded)
     */
    public function calculateWeightedScore(
        float $playerCurrentScore,
        int $questionDifficulty,
        float $baseScoreChange
    ): float {
        // Ambil konfigurasi
        $maxScore = $this->getConfig('max_player_score', 100);
        $sensitivity = $this->getConfig('sensitivity_factor', 0.5);
        $minMultiplier = $this->getConfig('min_score_multiplier', 0.3);
        $maxMultiplier = $this->getConfig('max_score_multiplier', 2.0);

        // Normalize difficulty (1-3) ke 0-1
        $difficultyFactor = $questionDifficulty / 3.0;

        // Normalize player score (0-100) ke 0-1
        $playerRatio = $playerCurrentScore / $maxScore;

        // Hitung gap antara kesulitan soal dan kemampuan player
        // Gap positif -> multiplier naik, Gap negatif -> multiplier turun
        $difficultyGap = $difficultyFactor - $playerRatio;

        // Hitung weight multiplier
        $weightMultiplier = 1.0 + ($difficultyGap * $sensitivity);

        // Inverse multiplier untuk penalty ketika player kuat salah di soal mudah
        if ($baseScoreChange < 0 && $difficultyGap < 0) {
            // Semakin tinggi player ratio, semakin besar multiplier
            $progressiveFactor = 1.0 + ($playerRatio * 0.5);
            $weightMultiplier = (1.0 - ($difficultyGap * $sensitivity)) * $progressiveFactor;
        }

        // Clamp multiplier ke range yang diizinkan
        $weightMultiplier = max($minMultiplier, min($maxMultiplier, $weightMultiplier));

        // Apply weight ke base score change
        $actualScoreChange = $baseScoreChange * $weightMultiplier;

        // Round ke integer terdekat
        $result = round($actualScoreChange);

        // Log debugging
        Log::debug('Weighted Scoring Calculation', [
            'player_score' => $playerCurrentScore,
            'difficulty' => $questionDifficulty,
            'base_change' => $baseScoreChange,
            'difficulty_factor' => round($difficultyFactor, 3),
            'player_ratio' => round($playerRatio, 3),
            'difficulty_gap' => round($difficultyGap, 3),
            'multiplier' => round($weightMultiplier, 3),
            'actual_change' => $result,
        ]);

        return $result;
    }

    /**
     * Mendapatkan max score dari konfigurasi
     */
    public function getMaxScore(): float
    {
        return $this->getConfig('max_player_score', 100);
    }

    /**
     * Clear cache untuk config tertentu
     * Berguna setelah update config di admin panel
     */
    public function clearConfigCache(string $key): void
    {
        Cache::forget("scoring_config_{$key}");
    }

    /**
     * Clear semua cache config
     */
    public function clearAllConfigCache(): void
    {
        $keys = ['max_player_score', 'sensitivity_factor', 'min_score_multiplier', 'max_score_multiplier'];
        foreach ($keys as $key) {
            $this->clearConfigCache($key);
        }
    }
}
