<?php

namespace App\Services\AI;

class FuzzyService
{
    private const STANDARD_LABELS = [
        'Sangat Rendah',
        'Rendah',
        'Sedang',
        'Tinggi',
        'Sangat Tinggi'
    ];
    private const EXTENDED_LABELS = [
        'Tidak Ada',
        'Sangat Rendah',
        'Rendah',
        'Sedang',
        'Tinggi',
        'Sangat Tinggi'
    ];

    private const CATEGORIES_WITH_ZERO = [
        'anggaran',
        'investasi',
        'asuransi',
        'asuransi_dan_proteksi',
        'tujuan_jangka_panjang'
    ];

    /**
     * Mengategorikan input berdasarkan logika fuzzy dan mengembalikan hasilnya
     */
    public function categorize(array $input): array
    {
        $categorizedOutput = [];
        foreach ($input as $category => $score) {
            $normalizedKey = strtolower(str_replace(' ', '_', $category));
            if (in_array($normalizedKey, self::CATEGORIES_WITH_ZERO)) {
                $categorizedOutput[$category] = $this->mapScoreToExtendedLabel($score);
            } else {
                $categorizedOutput[$category] = $this->mapScoreToStandardLabel($score);
            }
        }
        return $categorizedOutput;
    }

    /**
     * Mengubah skor menjadi label standar berdasarkan ambang batas yang telah ditentukan
     */
    private function mapScoreToStandardLabel($score): string
    {
        $score = max(0, min(100, $score));
        $thresholds = [20, 40, 60, 80, 100];
        $label = self::STANDARD_LABELS[count(self::STANDARD_LABELS) - 1];

        foreach ($thresholds as $i => $t) {
            if ($score <= $t) {
                $label = self::STANDARD_LABELS[$i];
                break;
            }
        }

        return $label;
    }

/**
     * Mapping untuk kategori extended (0 adalah 'Tidak Ada')
     */
    private function mapScoreToExtendedLabel($score): string
    {
        $score = max(0, min(100, $score));
        if ($score < 1) {
            return self::EXTENDED_LABELS[0];
        }

        $thresholds = [20, 40, 60, 80, 100];
        $label = self::EXTENDED_LABELS[count(self::EXTENDED_LABELS) - 1];

        foreach ($thresholds as $i => $t) {
            if ($score <= $t) {
                $label = self::EXTENDED_LABELS[$i + 1];
                break;
            }
        }

        return $label;
    }
}
