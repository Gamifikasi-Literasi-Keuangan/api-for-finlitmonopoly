<?php

namespace App\Services\AI;

class FuzzyService
{
    private const LABELS = [
        'Sangat Rendah',
        'Rendah',
        'Sedang',
        'Tinggi',
        'Sangat Tinggi'
    ];

    public function categorize(array $input): array
    {
        $categorizedOutput = [];
        foreach ($input as $category => $score) {
            $categorizedOutput[$category] = $this->mapScoreToLabel($score);
        }
        return $categorizedOutput;
    }

    private function mapScoreToLabel($score): string
    {
        $score = max(0, min(100, $score));

        if ($score <= 20) {
            return self::LABELS[0];
        } elseif ($score <= 40) {
            return self::LABELS[1];
        } elseif ($score <= 60) {
            return self::LABELS[2];
        } elseif ($score <= 80) {
            return self::LABELS[3];
        } else {
            return self::LABELS[4];
        }
    }
}