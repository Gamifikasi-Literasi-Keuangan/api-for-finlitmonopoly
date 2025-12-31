<?php

namespace App\Services\AI;

use Phpml\Classification\MLPClassifier;
use Phpml\Preprocessing\Normalizer;
use Phpml\ModelManager;
use Illuminate\Support\Facades\Log;

class ANNService
{
    private $model = null;
    private $modelPath;
    private $lastProbabilities = [];

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
        'asuransi_dan_proteksi' => [
            'Tidak Ada' => 0, 'Sangat Rendah' => 1, 'Rendah' => 2, 'Sedang' => 3, 'Tinggi' => 4, 'Sangat Tinggi' => 5
        ],
        'tujuan_jangka_panjang' => [
            'Tidak Ada' => 0, 'Sangat Rendah' => 1, 'Rendah' => 2, 'Sedang' => 3, 'Tinggi' => 4, 'Sangat Tinggi' => 5
        ],
    ];

    public function __construct()
    {
        $this->modelPath = storage_path('app/financial_ann_model.phpml');
        $this->loadOrTrainModel();
    }

    /**
     * Load model jika ada, atau latih model baru jika belum ada
     */
    private function loadOrTrainModel()
    {
        try {
            if (file_exists($this->modelPath)) {
                $modelManager = new ModelManager();
                $this->model = $modelManager->restoreFromFile($this->modelPath);
                Log::info('ANN Model loaded from file');
            } else {
                Log::info('ANN Model not found, training new model...');
                $this->trainModel();
            }
        } catch (\Exception $e) {
            Log::error('Failed to load ANN model: ' . $e->getMessage());
            // Fallback: train new model
            $this->trainModel();
        }
    }

    /**
     * Melatih model dengan dataset training
     */
    public function trainModel()
    {
        $trainingData = $this->getTrainingData();
        
        $features = [];
        $labels = [];
        
        foreach ($trainingData as $row) {
            $features[] = [
                $row['pendapatan'],
                $row['anggaran'],
                $row['tabungan_dan_dana_darurat'],
                $row['utang'],
                $row['investasi'],
                $row['asuransi_dan_proteksi'],
                $row['tujuan_jangka_panjang']
            ];
            $labels[] = $row['cluster'];
        }

        // Konversi ke numerik
        $numericFeatures = $this->convertToNumeric($features);
        
        // Normalisasi
        $normalizedFeatures = $this->normalizeData($numericFeatures);
        
        // Buat dan latih classifier
        // MLPClassifier($inputLayerFeatures, $hiddenLayers, $classes, $iterations, $activationFunction)
        $this->model = new MLPClassifier(
            7,              // Jumlah fitur input
            [10, 10],       // Hidden layers (2 layers dengan 10 neurons each)
            ['Financial Novice', 'Financial Explorer', 'Foundation Builder', 'Financial Architect', 'Financial Sage'],
            1000            // Max iterations
        );
        
        $this->model->train($normalizedFeatures, $labels);
        
        // Simpan model
        $modelManager = new ModelManager();
        $modelManager->saveToFile($this->model, $this->modelPath);
        
        Log::info('ANN Model trained and saved successfully');
    }

    /**
     * Dataset training unified (25 samples)
     */
    private function getTrainingData()
    {
        return TrainingDataset::get();
    }

    /**
     * Melakukan prediksi kelas akhir berdasarkan input label fuzzy
     */
    public function predict(array $fuzzyLabels)
    {
        if (!$this->model) {
            throw new \Exception('ANN Model not loaded');
        }

        // Preprocess input
        $inputVector = $this->preprocessInput($fuzzyLabels);
        
        // Normalisasi
        $normalizedInput = $this->normalizeData([$inputVector]);
        
        // Prediksi
        $prediction = $this->model->predict($normalizedInput);
        
        return $prediction[0];
    }

    /**
     * Preprocessing input dari fuzzy labels ke numeric vector
     */
    private function preprocessInput(array $fuzzyLabels)
    {
        $orderedKeys = [
            'pendapatan',
            'anggaran',
            'tabungan_dan_dana_darurat',
            'utang',
            'investasi',
            'asuransi_dan_proteksi',
            'tujuan_jangka_panjang'
        ];

        $numericVector = [];
        foreach ($orderedKeys as $key) {
            $label = $fuzzyLabels[$key] ?? 'Sangat Rendah';
            $numericVector[] = self::CATEGORY_MAPPINGS[$key][$label] ?? 0;
        }

        return $numericVector;
    }

    /**
     * Konversi data kategorikal ke numerik
     */
    private function convertToNumeric($data)
    {
        $numericData = [];
        
        foreach ($data as $row) {
            $numericRow = [];
            foreach ($row as $idx => $label) {
                $key = ['pendapatan', 'anggaran', 'tabungan_dan_dana_darurat', 'utang', 'investasi', 'asuransi_dan_proteksi', 'tujuan_jangka_panjang'][$idx];
                $numericRow[] = self::CATEGORY_MAPPINGS[$key][$label] ?? 0;
            }
            $numericData[] = $numericRow;
        }
        
        return $numericData;
    }

    /**
     * Normalisasi data dengan L2 norm
     */
    private function normalizeData($data)
    {
        $normalizer = new Normalizer(Normalizer::NORM_L2);
        $normalizer->transform($data);
        
        return $data;
    }

    /**
     * Mendapatkan kelas akhir (backward compatibility)
     */
    public function getFinalClass($prediction)
    {
        // Jika prediction sudah string, return as is
        if (is_string($prediction)) {
            return $prediction;
        }
        
        // Jika array, ambil yang tertinggi
        if (is_array($prediction)) {
            arsort($prediction);
            return array_key_first($prediction);
        }
        
        return $prediction;
    }

    /**
     * Mendapatkan tingkat kepercayaan (untuk backward compatibility)
     */
    public function getConfidence()
    {
        // PHP-ML tidak expose probability langsung untuk MLP
        // Return placeholder
        return 0.85;
    }
}
