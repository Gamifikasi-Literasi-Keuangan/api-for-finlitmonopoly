<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phpml\Classification\MLPClassifier;
use Phpml\NeuralNetwork\Training\Backpropagation;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\Preprocessing\Normalizer;
use Phpml\ModelManager;

class ANNController extends Controller
{
    // Mapping untuk kategori ordinal
    private $categoryMapping = [
        'Pendapatan' => [
            'Sangat Rendah' => 0,
            'Rendah' => 1,
            'Sedang' => 2,
            'Tinggi' => 3,
            'Sangat Tinggi' => 4
        ],
        'Anggaran' => [
            'Tidak Ada' => 0,
            'Sangat Rendah' => 1,
            'Rendah' => 2,
            'Sedang' => 3,
            'Tinggi' => 4,
            'Sangat Tinggi' => 5
        ],
        'Tabungan & Dana Darurat' => [
            'Sangat Rendah' => 0,
            'Rendah' => 1,
            'Sedang' => 2,
            'Tinggi' => 3,
            'Sangat Tinggi' => 4
        ],
        'Utang' => [
            'Sangat Rendah' => 0,
            'Rendah' => 1,
            'Sedang' => 2,
            'Tinggi' => 3,
            'Sangat Tinggi' => 4
        ],
        'Investasi' => [
            'Tidak Ada' => 0,
            'Sangat Rendah' => 1,
            'Rendah' => 2,
            'Sedang' => 3,
            'Tinggi' => 4,
            'Sangat Tinggi' => 5
        ],
        'Asuransi' => [
            'Tidak Ada' => 0,
            'Sangat Rendah' => 1,
            'Rendah' => 2,
            'Sedang' => 3,
            'Tinggi' => 4,
            'Sangat Tinggi' => 5
        ],
        'Tujuan Jangka Panjang' => [
            'Tidak Ada' => 0,
            'Sangat Rendah' => 1,
            'Rendah' => 2,
            'Sedang' => 3,
            'Tinggi' => 4,
            'Sangat Tinggi' => 5
        ],
        'Kelas Ekonomi (Arsitekip)' => [
            'Financial Novice' => 0,
            'Financial Explorer' => 1,
            'Foundation Builder' => 2,
            'Financial Architect' => 3,
            'Financial Sage' => 4
        ]
    ];

    // Fungsi untuk konversi data kategorikal ke numerik
    private function convertToNumeric($data)
    {
        $numericData = [];
        
        foreach ($data as $row) {
            $numericRow = [];
            
            // Konversi setiap fitur ke nilai numerik
            $numericRow[] = $this->categoryMapping['Pendapatan'][$row['Pendapatan']];
            $numericRow[] = $this->categoryMapping['Anggaran'][$row['Anggaran']];
            $numericRow[] = $this->categoryMapping['Tabungan & Dana Darurat'][$row['Tabungan & Dana Darurat']];
            $numericRow[] = $this->categoryMapping['Utang'][$row['Utang']];
            $numericRow[] = $this->categoryMapping['Investasi'][$row['Investasi']];
            $numericRow[] = $this->categoryMapping['Asuransi'][$row['Asuransi']];
            $numericRow[] = $this->categoryMapping['Tujuan Jangka Panjang'][$row['Tujuan Jangka Panjang']];
            
            $numericData[] = $numericRow;
        }
        
        return $numericData;
    }

    // Fungsi untuk normalisasi data
    private function normalizeData($data)
    {
        $normalizer = new Normalizer(Normalizer::NORM_L2);
        $normalizer->transform($data);
        
        return $data;
    }

    // Fungsi untuk melatih model ANN
    public function train()
    {
        // Data training dari unified dataset
        $trainingData = \App\Services\AI\TrainingDataset::getForController();
        
        /* Original hardcoded data diganti dengan unified dataset
        $trainingData = [
            [
                'Pendapatan' => 'Sangat Rendah',
                'Anggaran' => 'Sangat Rendah',
                'Tabungan & Dana Darurat' => 'Sangat Rendah',
                'Utang' => 'Sangat Tinggi',
                'Investasi' => 'Tidak Ada',
                'Asuransi' => 'Sangat Rendah',
                'Tujuan Jangka Panjang' => 'Sangat Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Novice'
            ],
            [
                'Pendapatan' => 'Rendah',
                'Anggaran' => 'Sangat Rendah',
                'Tabungan & Dana Darurat' => 'Sangat Rendah',
                'Utang' => 'Tinggi',
                'Investasi' => 'Tidak Ada',
                'Asuransi' => 'Tidak Ada',
                'Tujuan Jangka Panjang' => 'Sangat Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Novice'
            ],
            [
                'Pendapatan' => 'Sangat Rendah',
                'Anggaran' => 'Rendah',
                'Tabungan & Dana Darurat' => 'Rendah',
                'Utang' => 'Sangat Tinggi',
                'Investasi' => 'Tidak Ada',
                'Asuransi' => 'Rendah',
                'Tujuan Jangka Panjang' => 'Sangat Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Novice'
            ],
            [
                'Pendapatan' => 'Rendah',
                'Anggaran' => 'Rendah',
                'Tabungan & Dana Darurat' => 'Sangat Rendah',
                'Utang' => 'Tinggi',
                'Investasi' => 'Tidak Ada',
                'Asuransi' => 'Sangat Rendah',
                'Tujuan Jangka Panjang' => 'Tidak Ada',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Novice'
            ],
            [
                'Pendapatan' => 'Rendah',
                'Anggaran' => 'Sangat Rendah',
                'Tabungan & Dana Darurat' => 'Rendah',
                'Utang' => 'Sangat Tinggi',
                'Investasi' => 'Tidak Ada',
                'Asuransi' => 'Sangat Rendah',
                'Tujuan Jangka Panjang' => 'Sangat Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Novice'
            ],
            [
                'Pendapatan' => 'Rendah',
                'Anggaran' => 'Sedang',
                'Tabungan & Dana Darurat' => 'Rendah',
                'Utang' => 'Sedang',
                'Investasi' => 'Sangat Rendah',
                'Asuransi' => 'Rendah',
                'Tujuan Jangka Panjang' => 'Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Explorer'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Rendah',
                'Tabungan & Dana Darurat' => 'Rendah',
                'Utang' => 'Sedang',
                'Investasi' => 'Sangat Rendah',
                'Asuransi' => 'Rendah',
                'Tujuan Jangka Panjang' => 'Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Explorer'
            ],
            [
                'Pendapatan' => 'Rendah',
                'Anggaran' => 'Sedang',
                'Tabungan & Dana Darurat' => 'Sedang',
                'Utang' => 'Tinggi',
                'Investasi' => 'Rendah',
                'Asuransi' => 'Rendah',
                'Tujuan Jangka Panjang' => 'Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Explorer'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Sedang',
                'Tabungan & Dana Darurat' => 'Rendah',
                'Utang' => 'Sedang',
                'Investasi' => 'Sangat Rendah',
                'Asuransi' => 'Rendah',
                'Tujuan Jangka Panjang' => 'Sangat Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Explorer'
            ],
            [
                'Pendapatan' => 'Rendah',
                'Anggaran' => 'Rendah',
                'Tabungan & Dana Darurat' => 'Sedang',
                'Utang' => 'Sedang',
                'Investasi' => 'Rendah',
                'Asuransi' => 'Sangat Rendah',
                'Tujuan Jangka Panjang' => 'Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Explorer'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Sedang',
                'Tabungan & Dana Darurat' => 'Rendah',
                'Utang' => 'Tinggi',
                'Investasi' => 'Sangat Rendah',
                'Asuransi' => 'Rendah',
                'Tujuan Jangka Panjang' => 'Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Explorer'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Tinggi',
                'Utang' => 'Rendah',
                'Investasi' => 'Sedang',
                'Asuransi' => 'Sedang',
                'Tujuan Jangka Panjang' => 'Sedang',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Tinggi',
                'Anggaran' => 'Sedang',
                'Tabungan & Dana Darurat' => 'Tinggi',
                'Utang' => 'Rendah',
                'Investasi' => 'Sedang',
                'Asuransi' => 'Sedang',
                'Tujuan Jangka Panjang' => 'Sedang',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Tinggi',
                'Utang' => 'Rendah',
                'Investasi' => 'Sedang',
                'Asuransi' => 'Sedang',
                'Tujuan Jangka Panjang' => 'Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Tinggi',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Sedang',
                'Asuransi' => 'Tinggi',
                'Tujuan Jangka Panjang' => 'Sedang',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Sedang',
                'Tabungan & Dana Darurat' => 'Tinggi',
                'Utang' => 'Rendah',
                'Investasi' => 'Tinggi',
                'Asuransi' => 'Sedang',
                'Tujuan Jangka Panjang' => 'Sedang',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Tinggi',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Tinggi',
                'Utang' => 'Rendah',
                'Investasi' => 'Sedang',
                'Asuransi' => 'Sedang',
                'Tujuan Jangka Panjang' => 'Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Rendah',
                'Investasi' => 'Sedang',
                'Asuransi' => 'Sedang',
                'Tujuan Jangka Panjang' => 'Sedang',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Tinggi',
                'Anggaran' => 'Sangat Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Tinggi',
                'Asuransi' => 'Tinggi',
                'Tujuan Jangka Panjang' => 'Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Architect'
            ],
            [
                'Pendapatan' => 'Sangat Tinggi',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Tinggi',
                'Asuransi' => 'Tinggi',
                'Tujuan Jangka Panjang' => 'Sangat Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Architect'
            ],
            [
                'Pendapatan' => 'Tinggi',
                'Anggaran' => 'Sangat Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Sangat Tinggi',
                'Asuransi' => 'Tinggi',
                'Tujuan Jangka Panjang' => 'Sangat Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Architect'
            ],
            [
                'Pendapatan' => 'Sangat Tinggi',
                'Anggaran' => 'Sangat Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Tinggi',
                'Asuransi' => 'Sangat Tinggi',
                'Tujuan Jangka Panjang' => 'Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Architect'
            ],
            [
                'Pendapatan' => 'Tinggi',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Sangat Tinggi',
                'Asuransi' => 'Tinggi',
                'Tujuan Jangka Panjang' => 'Sangat Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Architect'
            ],
            [
                'Pendapatan' => 'Sangat Tinggi',
                'Anggaran' => 'Sangat Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Sangat Tinggi',
                'Asuransi' => 'Sangat Tinggi',
                'Tujuan Jangka Panjang' => 'Sangat Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Sage'
            ],
            [
                'Pendapatan' => 'Sangat Tinggi',
                'Anggaran' => 'Sangat Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Sangat Tinggi',
                'Asuransi' => 'Tinggi',
                'Tujuan Jangka Panjang' => 'Sangat Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Sage'
            ]
        ]; */

        // Pisahkan fitur dan label
        $features = [];
        $labels = [];
        
        foreach ($trainingData as $row) {
            $labels[] = $row['Kelas Ekonomi (Arsitekip)'];
            unset($row['Kelas Ekonomi (Arsitekip)']);
            $features[] = $row;
        }

        // Konversi fitur ke nilai numerik
        $numericFeatures = $this->convertToNumeric($features);
        
        // Normalisasi fitur
        $normalizedFeatures = $this->normalizeData($numericFeatures);
        
        // Buat classifier MLP
        $mlp = new MLPClassifier(
            7, // Jumlah fitur input
            [10, 10], // Hidden layers
            array_keys($this->categoryMapping['Kelas Ekonomi (Arsitekip)']), // Kelas output
            1000 // Max iterations
        );
        
        // Latih model
        $mlp->train($normalizedFeatures, $labels);
        
        // Simpan model
        $modelManager = new ModelManager();
        $modelManager->saveToFile($mlp, storage_path('app/financial_ann_model.phpml'));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Model ANN berhasil dilatih dan disimpan!'
        ]);
    }

    // Fungsi untuk testing model
    public function test(Request $request)
    {
        // Validasi input
        $request->validate([
            'pendapatan' => 'required|string',
            'anggaran' => 'required|string',
            'tabungan_dana_darurat' => 'required|string',
            'utang' => 'required|string',
            'investasi' => 'required|string',
            'asuransi' => 'required|string',
            'tujuan_jangka_panjang' => 'required|string'
        ]);
        
        // Load model yang sudah dilatih
        $modelManager = new ModelManager();
        $mlp = $modelManager->restoreFromFile(storage_path('app/financial_ann_model.phpml'));
        
        // Siapkan data input
        $inputData = [
            [
                'Pendapatan' => $request->pendapatan,
                'Anggaran' => $request->anggaran,
                'Tabungan & Dana Darurat' => $request->tabungan_dana_darurat,
                'Utang' => $request->utang,
                'Investasi' => $request->investasi,
                'Asuransi' => $request->asuransi,
                'Tujuan Jangka Panjang' => $request->tujuan_jangka_panjang
            ]
        ];
        
        // Konversi ke numerik
        $numericInput = $this->convertToNumeric($inputData);
        
        // Normalisasi
        $normalizedInput = $this->normalizeData($numericInput);
        
        // Prediksi
        $prediction = $mlp->predict($normalizedInput);
        
        return response()->json([
            'status' => 'success',
            'prediction' => $prediction[0],
            'input_data' => $request->all()
        ]);
    }

    // Fungsi untuk evaluasi model
    public function evaluate()
    {
        // Load model yang sudah dilatih
        $modelManager = new ModelManager();
        $mlp = $modelManager->restoreFromFile(storage_path('app/financial_ann_model.phpml'));
        
        // Data testing (sebagian dari data training untuk contoh)
        $testData = [
            [
                'Pendapatan' => 'Sangat Rendah',
                'Anggaran' => 'Sangat Rendah',
                'Tabungan & Dana Darurat' => 'Sangat Rendah',
                'Utang' => 'Sangat Tinggi',
                'Investasi' => 'Tidak Ada',
                'Asuransi' => 'Sangat Rendah',
                'Tujuan Jangka Panjang' => 'Sangat Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Novice'
            ],
            [
                'Pendapatan' => 'Sedang',
                'Anggaran' => 'Sedang',
                'Tabungan & Dana Darurat' => 'Rendah',
                'Utang' => 'Sedang',
                'Investasi' => 'Sangat Rendah',
                'Asuransi' => 'Rendah',
                'Tujuan Jangka Panjang' => 'Sangat Rendah',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Explorer'
            ],
            [
                'Pendapatan' => 'Tinggi',
                'Anggaran' => 'Tinggi',
                'Tabungan & Dana Darurat' => 'Tinggi',
                'Utang' => 'Rendah',
                'Investasi' => 'Sedang',
                'Asuransi' => 'Sedang',
                'Tujuan Jangka Panjang' => 'Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Foundation Builder'
            ],
            [
                'Pendapatan' => 'Sangat Tinggi',
                'Anggaran' => 'Sangat Tinggi',
                'Tabungan & Dana Darurat' => 'Sangat Tinggi',
                'Utang' => 'Sangat Rendah',
                'Investasi' => 'Sangat Tinggi',
                'Asuransi' => 'Sangat Tinggi',
                'Tujuan Jangka Panjang' => 'Sangat Tinggi',
                'Kelas Ekonomi (Arsitekip)' => 'Financial Sage'
            ]
        ];
        
        // Pisahkan fitur dan label
        $testFeatures = [];
        $testLabels = [];
        
        foreach ($testData as $row) {
            $testFeatures[] = [
                $row['Pendapatan'],
                $row['Anggaran'],
                $row['Tabungan & Dana Darurat'],
                $row['Utang'],
                $row['Investasi'],
                $row['Asuransi'],
                $row['Tujuan Jangka Panjang']
            ];
            $testLabels[] = $row['Kelas Ekonomi (Arsitekip)'];
        }
        
        // Konversi ke numerik
        $numericTestFeatures = $this->convertToNumeric($testFeatures);
        
        // Normalisasi
        $normalizedTestFeatures = $this->normalizeData($numericTestFeatures);
        
        // Prediksi
        $predictions = $mlp->predict($normalizedTestFeatures);
        
        // Hitung akurasi
        $correct = 0;
        $total = count($testLabels);
        
        for ($i = 0; $i < $total; $i++) {
            if ($predictions[$i] === $testLabels[$i]) {
                $correct++;
            }
        }
        
        $accuracy = ($correct / $total) * 100;
        
        return response()->json([
            'status' => 'success',
            'accuracy' => round($accuracy, 2) . '%',
            'predictions' => $predictions,
            'actual_labels' => $testLabels
        ]);
    }
}