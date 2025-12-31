<?php

namespace App\Services\AI;

class FuzzyRule
{
    /**
     * Definisi aturan Fuzzy (Bisa ditambahkan)
     */
    private const RULES = [
        'Financial Novice' => [
            [
                'pendapatan' => 'Sangat Rendah',
                'utang'      => 'Tinggi',
                'investasi'  => 'Tidak Ada',
            ],
            [
                'pendapatan' => 'Rendah',
                'utang'      => 'Tinggi',
                'investasi'  => 'Rendah',
            ],
            [
                'pendapatan' => 'Sangat Rendah',
                'utang'      => 'Tinggi',
                'anggaran'   => 'Sangat Rendah',
            ],
            [
                'tabungan_dan_dana_darurat' => 'Sangat Rendah',
                'investasi'                => 'Tidak Ada',
                'tujuan_jangka_panjang'    => 'Sangat Rendah',
            ],
            [
                'utang'   => 'Tinggi',
                'investasi' => 'Tidak Ada',
                'asuransi_dan_proteksi' => 'Sangat Rendah',
            ],
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Tinggi',
                'anggaran'   => 'Rendah',
            ],
            [
                'tabungan_dan_dana_darurat' => 'Rendah',
                'asuransi_dan_proteksi'     => 'Sangat Rendah',
                'investasi'                 => 'Tidak Ada',
            ],
        ],

        'Financial Explorer' => [
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Sedang',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Rendah',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan' => 'Rendah',
                'utang'      => 'Sedang',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Rendah',
                'investasi'  => 'Rendah',
            ],
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Sedang',
                'investasi'  => 'Rendah',
            ],
            [
                'anggaran' => 'Sedang',
                'tabungan_dan_dana_darurat' => 'Sedang',
                'investasi' => 'Sedang',
            ],
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Rendah',
                'asuransi_dan_proteksi' => 'Sedang',
            ],
            [
                'pendapatan' => 'Rendah',
                'anggaran'   => 'Sedang',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan'                => 'Sedang',
                'utang'                     => 'Sedang',
                'anggaran'                  => 'Sedang',
                'tabungan_dan_dana_darurat' => 'Sedang', 
            ],
            [
                'asuransi_dan_proteksi' => 'Sedang',
                'tujuan_jangka_panjang' => 'Sedang',
                'investasi'             => 'Sedang',
            ],
            [
                'utang'     => 'Rendah',
                'investasi' => 'Rendah',
                'anggaran'  => 'Sedang',
            ],
        ],

        'Foundation Builder' => [
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Rendah',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan' => 'Tinggi',
                'utang'      => 'Sedang',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan' => 'Tinggi',
                'utang'      => 'Rendah',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan' => 'Sedang',
                'utang'      => 'Rendah',
                'tabungan_dan_dana_darurat' => 'Sedang',
            ],
            [
                'anggaran' => 'Tinggi',
                'tabungan_dan_dana_darurat' => 'Sedang',
                'investasi' => 'Sedang',
            ],
            [
                'pendapatan' => 'Tinggi',
                'utang'      => 'Sedang',
                'asuransi_dan_proteksi' => 'Sedang',
            ],
            [
            'tabungan_dan_dana_darurat' => 'Tinggi',
            'asuransi_dan_proteksi'     => 'Sedang',
            'utang'                     => 'Rendah',
            ],
            [
                'anggaran'              => 'Tinggi',
                'tujuan_jangka_panjang' => 'Sedang',
                'investasi'             => 'Sedang',
            ],
        ],

        'Financial Architect' => [
            [
                'pendapatan' => 'Tinggi',
                'utang'      => 'Rendah',
                'investasi'  => 'Sedang',
            ],
            [
                'pendapatan' => 'Tinggi',
                'utang'      => 'Rendah',
                'investasi'  => 'Tinggi',
            ],
            [
                'pendapatan' => 'Tinggi',
                'utang'      => 'Rendah',
                'anggaran'   => 'Tinggi',
            ],
            [
                'tabungan_dan_dana_darurat' => 'Tinggi',
                'asuransi_dan_proteksi'     => 'Tinggi',
                'investasi'                => 'Sedang',
            ],
            [
                'tujuan_jangka_panjang' => 'Tinggi',
                'investasi'             => 'Tinggi',
                'utang'                 => 'Rendah',
            ],
            [
                'tujuan_jangka_panjang' => 'Tinggi',
                'asuransi_dan_proteksi' => 'Tinggi',
                'anggaran'              => 'Tinggi',
            ],
            [
                'pendapatan' => 'Sangat Tinggi',
                'investasi'  => 'Sedang', 
                'utang'      => 'Rendah',
            ],
        ],

        'Financial Sage' => [
            [
                'pendapatan' => 'Sangat Tinggi',
                'utang'      => 'Rendah',
                'investasi'  => 'Tinggi',
            ],
            [
                'pendapatan' => 'Sangat Tinggi',
                'utang'      => 'Rendah',
                'investasi'  => 'Sangat Tinggi',
            ],
            [
                'pendapatan' => 'Sangat Tinggi',
                'utang'      => 'Rendah',
                'investasi'  => 'Sangat Tinggi',
            ],
            [
                'tabungan_dan_dana_darurat' => 'Sangat Tinggi',
                'asuransi_dan_proteksi'     => 'Tinggi',
                'tujuan_jangka_panjang'     => 'Sangat Tinggi',
            ],
            [
                'investasi'                 => 'Sangat Tinggi',
                'tujuan_jangka_panjang'     => 'Tinggi',
                'tabungan_dan_dana_darurat' => 'Tinggi',
            ],
            [
                'asuransi_dan_proteksi' => 'Sangat Tinggi',
                'investasi'             => 'Sangat Tinggi',
                'utang'                 => 'Rendah',
            ],
        ],
    ];

    /**
     * Evaluasi aturan fuzzy
     */
    public function evaluate(array $fuzzyFeatures): array
    {
        $classStrengths  = [];

        foreach (self::RULES as $class => $rules) {
            $ruleStrengths = [];

            foreach ($rules as $rule) {
                $degrees = [];

                foreach ($rule as $feature => $label) {
                    $degrees[] = $fuzzyFeatures[$feature]['degrees'][$label] ?? 0.0;
                }

                // AND fuzzy = MIN
                $ruleStrengths[] = min($degrees);
            }

            // OR fuzzy = MAX
            $classStrengths[$class] = empty($ruleStrengths)
                ? 0.0
                : max($ruleStrengths);
        }

        return $classStrengths;
    }

    public function evaluateWithDebug(array $fuzzyFeatures): array
    {
        $classStrengths = [];
        $ruleDebug = [];

        foreach (self::RULES as $class => $rules) {
            $ruleStrengths = [];
            $ruleDebug[$class] = [];

            foreach ($rules as $rule) {
                $degrees = [];

                foreach ($rule as $feature => $label) {
                    $degrees[$feature] =
                        $fuzzyFeatures[$feature]['degrees'][$label] ?? 0.0;
                }

                $strength = min($degrees);
                $ruleStrengths[] = $strength;

                $ruleDebug[$class][] = [
                    'rule' => $rule,
                    'degrees' => $degrees,
                    'strength' => $strength
                ];
            }

            $classStrengths[$class] = empty($ruleStrengths)
                ? 0.0
                : max($ruleStrengths);
        }

        return [
            'class_strengths' => $classStrengths,
            'rule_debug' => $ruleDebug
        ];
    } 
}
