<?php

namespace App\Repositories;

use App\Models\PlayerDecision;
use App\Models\Scenario;
use Illuminate\Support\Collection;

class InterventionRepository
{
    /**
     * Mengambil keputusan terakhir pemain
     */
    public function getRecentDecisions(string $playerId, int $limit = 15): Collection
    {
        return PlayerDecision::where('player_id', $playerId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Menganalisis pola kesalahan pemain (Global & Per Kategori)
     * 
     * @param Collection $decisions
     * @return array ['global_errors' => int, 'category_streaks' => array]
     */
    public function analyzeStreaks(Collection $decisions): array
    {
        if ($decisions->isEmpty()) {
            return [
                'global_errors' => 0,
                'category_streaks' => []
            ];
        }

        $globalConsecutiveErrors = 0;
        $globalChainBroken = false;

        $categoryStreaks = []; // 'category_name' => streak_count
        $categoryBroken = [];  // 'category_name' => boolean

        foreach ($decisions as $decision) {
            if ($decision->content_type !== 'scenario') {
                continue;
            }

            // Global Streak
            if (!$globalChainBroken) {
                if (!$decision->is_correct) {
                    $globalConsecutiveErrors++;
                } else {
                    $globalChainBroken = true;
                }
            }

            // Category Streak
            $category = $this->getCategoryFromDecision($decision);

            if ($category) {
                // Init data jika baru ketemu kategori ini
                if (!isset($categoryBroken[$category])) {
                    $categoryBroken[$category] = false;
                    $categoryStreaks[$category] = 0;
                }

                if (!$categoryBroken[$category]) {
                    if (!$decision->is_correct) {
                        $categoryStreaks[$category]++;
                    } else {
                        $categoryBroken[$category] = true;
                    }
                }
            }
        }

        return [
            'global_errors' => $globalConsecutiveErrors,
            'category_streaks' => $categoryStreaks
        ];
    }

    /**
     * Mendapatkan kategori dari keputusan (via DB)
     */
    public function getCategoryFromDecision($decision): ?string
    {
        if ($decision->content_id) {
            return $this->getCategoryFromContentId($decision->content_id);
        }

        return null;
    }

    /**
     * Parse kategori dari Content ID menggunakan ID (SCN_KATEGORI_NOMOR)
     */
    public function getCategoryFromContentId(string $contentId): ?string
    {
        // Parsing Manual (SCN_KATEGORI_NOMOR)
        $parts = explode('_', $contentId);

        if (count($parts) >= 2) {
            // Mapping kategori ID dengan boardtile
            $mapping = [
                'PENDAPATAN' => 'pendapatan',
                'ANGGARAN' => 'anggaran',
                'TABUNGAN' => 'tabungan_dan_dana_darurat',
                'UTANG' => 'utang',
                'INVESTASI' => 'investasi',
                'ASURANSI' => 'asuransi',
                'TUJUAN' => 'tujuan_jangka_panjang',

                // Legacy mappings
                'SAVING' => 'tabungan_dan_dana_darurat',
                'DEBT' => 'utang',
                'INSURANCE' => 'asuransi',
                'GOAL' => 'tujuan_jangka_panjang'
            ];

            // Ambil bagian tengah sebagai kategori (index 1)
            $categoryCode = strtoupper($parts[1]);

            if (isset($mapping[$categoryCode])) {
                return $mapping[$categoryCode];
            }

            // Fallback: Loop untuk partial match keys
            foreach ($mapping as $key => $val) {
                if (str_contains(strtoupper($contentId), $key)) {
                    return $val;
                }
            }
        }

        // Cek Tabel Scenarios (Fallback jika parsing gagal)
        $scenario = Scenario::where('id', $contentId)->first();
        if ($scenario && $scenario->category) {
            // Normalisasi sederhana jika perlu
            $cat = strtolower($scenario->category);
            // Example mapping manual fix if needed based on DB content
            if (str_contains($cat, 'tabungan'))
                return 'tabungan_dan_dana_darurat';
            if (str_contains($cat, 'utang'))
                return 'utang';

            return $cat;
        }

        return null;
    }
}
