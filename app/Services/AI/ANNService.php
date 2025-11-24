<?php

namespace App\Services\AI;

class ANNService
{
    private $weights = [
        'hidden' => [
            // Bobot dari 7 input ke neuron hidden
            [0.1, -0.2, 0.5, 0.1, -0.2, 0.5, 0.1],
            [0.2, 0.1, -0.1, 0.2, 0.1, -0.1, 0.2],
            // ... (jumlah baris = jumlah neuron hidden)
        ],
        'output' => [
            'Financial Novice'    => [0.5, 0.2, -0.1],
            'Financial Explorer'  => [0.1, 0.8, 0.2],
            'Foundation Builder'  => [-0.2, 0.3, 0.6],
            'Financial Architect' => [0.3, 0.1, 0.9],
            'Financial Sage'      => [0.6, -0.1, 0.3],
        ]
    ];

    private $biases = [
        'hidden' => [0.1, 0.1],
        'output' => [
            'Financial Novice'    => 0.05,
            'Financial Explorer'  => 0.1,
            'Foundation Builder'  => 0.08,
            'Financial Architect' => 0.12,
            'Financial Sage'      => 0.15,
        ]
    ];

    private const CATEGORY_MAPPINGS = [
        'pendapatan' => [
            'Sangat Rendah' => 0, 'Rendah' => 1, 'Sedang' => 2, 'Tinggi' => 3, 'Sangat Tinggi' => 4
        ],
        'anggaran' => [
            'Tidak Ada' => 0, 'Sangat Rendah' => 1, 'Rendah' => 2, 'Sedang' => 3, 'Tinggi' => 4, 'Sangat Tinggi' => 5
        ],
        'tabungan_dan_dana_darurat' => [
            'Sangat Rendah' => 0, 'Rendah' => 1, 'Sedang' => 2, 'Tinggi' => 3, 'Sangat Tinggi' => 4
        ],
        'utang' => [
            'Sangat Rendah' => 0, 'Rendah' => 1, 'Sedang' => 2, 'Tinggi' => 3, 'Sangat Tinggi' => 4
        ],
        'investasi' => [
            'Tidak Ada' => 0, 'Sangat Rendah' => 1, 'Rendah' => 2, 'Sedang' => 3, 'Tinggi' => 4, 'Sangat Tinggi' => 5
        ],
        'asuransi_dan_proteksi' => [ // Maps to 'Asuransi' di ANNController
            'Tidak Ada' => 0, 'Sangat Rendah' => 1, 'Rendah' => 2, 'Sedang' => 3, 'Tinggi' => 4, 'Sangat Tinggi' => 5
        ],
        'tujuan_jangka_panjang' => [
            'Tidak Ada' => 0, 'Sangat Rendah' => 1, 'Rendah' => 2, 'Sedang' => 3, 'Tinggi' => 4, 'Sangat Tinggi' => 5
        ],
    ];

    private $lastProbabilities = [];

    /**
     * Melakukan prediksi kelas akhir berdasarkan input label fuzzy.
     */
    public function predict(array $fuzzyLabels)
    {
        $inputVector = $this->preprocess($fuzzyLabels);
        $finalScores = $this->feedforward($inputVector);
        $this->lastProbabilities = $finalScores;

        return $this->getFinalClass($finalScores);
    }

    /**
     * Mengubah label teks menjadi vektor numerik yang ternormalisasi (L2 Norm).
     */
    private function preprocess(array $fuzzyLabels)
    {
        // Urutan kunci HARUS konsisten dengan ANNController
        $orderedKeys = [
            'pendapatan',
            'anggaran',
            'tabungan_dan_dana_darurat',
            'utang',
            'investasi',
            'asuransi_dan_proteksi',
            'tujuan_jangka_panjang'
        ];

        $rawVector = [];
        $sumSquares = 0;

        // Tahap 1: Konversi ke Angka (Ordinal Encoding)
        foreach ($orderedKeys as $key) {
            $label = $fuzzyLabels[$key] ?? 'Sangat Rendah';
            $val = self::CATEGORY_MAPPINGS[$key][$label] ?? 0;
            
            $rawVector[] = $val;
            $sumSquares += ($val * $val);
        }

        // Tahap 2: Normalisasi L2 (Euclidean Norm)
        $magnitude = sqrt($sumSquares);
        
        if ($magnitude == 0) {
            return array_fill(0, count($rawVector), 0);
        }

        $normalizedVector = [];
        foreach ($rawVector as $val) {
            $normalizedVector[] = $val / $magnitude;
        }

        return $normalizedVector;
    }

    /**
     * Implementasi manual MLP sederhana.
     */
    private function feedforward(array $input)
    {
        // 1. Hidden Layer
        $hiddenOutputs = [];
        foreach ($this->weights['hidden'] as $neuronIndex => $weights) {
            $sum = 0;
            for ($i = 0; $i < count($input); $i++) {
                $sum += $input[$i] * ($weights[$i] ?? 0);
            }
            $sum += $this->biases['hidden'][$neuronIndex] ?? 0;
            $hiddenOutputs[] = $this->relu($sum);
        }

        // 2. Output Layer
        $finalScores = [];
        foreach ($this->weights['output'] as $className => $weights) {
            $sum = 0;
            for ($i = 0; $i < count($hiddenOutputs); $i++) {
                $sum += $hiddenOutputs[$i] * ($weights[$i] ?? 0);
            }
            $sum += $this->biases['output'][$className] ?? 0;
            $finalScores[$className] = $this->relu($sum);
        }

        return $this->softmax($finalScores);
    }

    /**
     * Mendapatkan kelas akhir berdasarkan skor tertinggi.
     */
    public function getFinalClass(array $scores)
    {
        arsort($scores);
        return array_key_first($scores);
    }

    /**
     * Mendapatkan tingkat kepercayaan dari prediksi terakhir.
     */
    public function getConfidence()
    {
        if (empty($this->lastProbabilities)) {
            return 0.0;
        }
        return max($this->lastProbabilities);
    }

    /**
     * Fungsi aktivasi ReLU.
     */
    private function relu($x)
    {
        return max(0, $x);
    }

    /**
     * Fungsi softmax untuk mengubah skor menjadi probabilitas.
     */
    private function softmax(array $scores)
    {
        $expScores = array_map('exp', $scores);
        $total = array_sum($expScores);
        
        if ($total == 0) {
            return $scores;
        }

        $probabilities = [];
        foreach ($expScores as $class => $val) {
            $probabilities[$class] = $val / $total;
        }
        return $probabilities;
    }
}
