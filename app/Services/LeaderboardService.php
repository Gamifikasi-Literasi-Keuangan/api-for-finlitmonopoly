<?php

namespace App\Services;

use App\Models\PlayerProfile;
use Illuminate\Support\Collection;

class LeaderboardService
{
    /**
     * GET /leaderboard
     * Mengambil daftar peringkat pemain berdasarkan skor overall.
     */
    public function getLeaderboard()
    {
        // 1. Ambil semua profil beserta data pemain (nama/username)
        // Menggunakan 'with' untuk Eager Loading agar performa query efisien
        $profiles = PlayerProfile::with('player')->get();

        // 2. Map data ke format yang butuhkan & Hitung Overall Score
        $rankedPlayers = $profiles->map(function ($profile) {
            $scores = $profile->lifetime_scores ?? []; // Sudah array karena casts
            
            // Hitung Overall Score
            // Jika sudah ada key 'overall' di JSON, pakai itu. Jika tidak, hitung rata-rata.
            if (isset($scores['overall'])) {
                $overall = $scores['overall'];
            } else {
                // Filter nilai non-numerik
                $numericScores = array_filter($scores, 'is_numeric');
                $count = count($numericScores);
                $overall = $count > 0 ? array_sum($numericScores) / $count : 0;
            }

            return [
                'player_id' => $profile->PlayerId,
                'username'  => $profile->player->name ?? 'Unknown Player',
                'avatar'    => $profile->player->avatar_url ?? null, // Opsional: untuk UI
                'overall'   => round($overall) // Bulatkan skor
            ];
        });

        // 3. Urutkan berdasarkan Overall Score (Descending - Tertinggi ke Terendah)
        // values() digunakan untuk mereset index array setelah sorting
        $sortedPlayers = $rankedPlayers->sortByDesc('overall')->values();

        // 4. Berikan Nomor Peringkat (Rank)
        $finalRankings = $sortedPlayers->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // 5. Return Format JSON
        return [
            'rankings' => $finalRankings,
            'generated_at' => now()->toIso8601String()
        ];
    }
}