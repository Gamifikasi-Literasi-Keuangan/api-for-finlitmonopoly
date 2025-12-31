<?php

namespace App\Services\AI;

class FuzzyService
{
    private const CATEGORIES_WITH_ZERO = [
        'anggaran',
        'investasi',
        'asuransi',
        'asuransi_dan_proteksi',
        'tujuan_jangka_panjang'
    ];

    /**
     * Mengkategorikan input berdasarkan logika fuzzy dan mengembalikan hasilnya
     */
    public function categorize(string $playerId, array $numericFeatures, bool $debug=false): array
    {
        $fuzzyFeatures = [];
        foreach ($numericFeatures as $feature => $value) {
            $normalized = strtolower($feature);
            $isExtended = in_array($normalized, self::CATEGORIES_WITH_ZERO);

            $fuzzyFeatures[$feature] = $this->fuzzifyFeature($value,$isExtended);
        }

        $fuzzyCategories = $this->extractFinalLabels($fuzzyFeatures);

        $ruleResults = app(FuzzyRule::class);

        // $classStrengths = $ruleResults->evaluate($fuzzyFeatures);
        // $fuzzyResult = $this->defuzzifyClass($classStrengths);

        if ($debug) {
            $ruleEval = $ruleResults->evaluateWithDebug($fuzzyFeatures);

            $classStrengths = $ruleEval['class_strengths'];
            $ruleDebug = $ruleEval['rule_debug'];

            $fuzzyResult = $this->defuzzifyClass($classStrengths);

            return [
                'player_id' => $playerId,
                'input' => [
                    'numeric_features' => $numericFeatures
                ],
                'fuzzification' => $fuzzyFeatures,
                'rule_evaluation' => [
                    'rules_per_class' => $ruleDebug,
                    'class_strengths' => $classStrengths
                ],
                'defuzzification' => $fuzzyResult,
                'for_ann' => [
                    'fuzzy_categories' => $fuzzyCategories
                ],
            ];
        }
        else {
            $classStrengths = $ruleResults->evaluate($fuzzyFeatures);
            $fuzzyResult = $this->defuzzifyClass($classStrengths);

            return [
                'player_id'        => $playerId,
                'numeric_features' => $numericFeatures,
                'fuzzy_categories' => $fuzzyCategories,
                'final_result'     => $fuzzyResult,
            ];
        }
    }


    /**
     * Proses Fuzzifikasi untuk satu fitur
     */
    protected function fuzzifyFeature(float $value, bool $isExtended): array
    {
        $value = max(0, min(100, $value));
        $degrees = [];

        if ($isExtended) {
            $degrees['Tidak Ada'] = $value < 1 ? 1.0 : 0.0;
        }

        // Membership Functions untuk kategori fuzzy
        $ranges = [
            'Sangat Rendah' => [0, 0, 20, 40],
            'Rendah'        => [20, 35, 50, 65],
            'Sedang'        => [50, 65, 80, 90],
            'Tinggi'        => [80, 90, 95, 100],
            'Sangat Tinggi' => [90, 95, 100, 105],

        ];

        foreach ($ranges as $label => [$a, $b, $c, $d]) {
            $degrees[$label] = $this->trapezoidal($value, $a, $b, $c, $d);
        }

        return [
            'input_value' => $value,
            'is_extended' => $isExtended,
            'degrees' => $degrees,
            'final_label' => $this->resolveFinalClass($degrees)
        ];
    }

    /**
     * Fungsi trapesium untuk menghitung derajat keanggotaan
     */
    private function trapezoidal(float $x, float $a, float $b, float $c, float $d): float
    {
        if ($x <= $a || $x > $d) {
            return 0.0;
        } elseif ($x >= $b && $x <= $c) {
            return 1.0;
        } elseif ($x > $a && $x < $b) {
            return ($x - $a) / ($b - $a);
        } elseif ($x > $c && $x < $d) {
            return ($d - $x) / ($d - $c);
        }

        return 0.0;
    }

    private function extractFinalLabels(array $fuzzyFeatures): array
    {
        $labels = [];

        foreach ($fuzzyFeatures as $feature => $data) {
            $labels[$feature] = $data['final_label'];
        }

        return $labels;
    }


    /**
     * Menentukan kelas akhir berdasarkan derajat keanggotaan
     */
    private function resolveFinalClass(array $degrees): string
    {
        arsort($degrees);
        return array_key_first($degrees);
    }

    /**
     * Defuzzifikasi
     */
    private function defuzzifyClass(array $classStrengths): array
    {
        arsort($classStrengths);
        $class = array_key_first($classStrengths);

        return [
            'class'        => $class,
            'confidence'   => round($classStrengths[$class], 2),
        ];
    }
}
