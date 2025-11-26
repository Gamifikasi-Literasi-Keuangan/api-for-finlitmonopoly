<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterventionController extends Controller
{
    /**
     * API: GET /intervention/trigger
     * Mengambil pesan intervensi berdasarkan level risiko
     * Tidak memerlukan parameter - sistem otomatis generate
     */
    public function trigger(Request $request)
    {
        // Untuk demo/testing, generate intervention level secara random
        // Di production, ini bisa diambil dari session atau context player yang sedang aktif

        $interventionLevel = rand(0, 3);

        // Jika tidak perlu intervensi
        if ($interventionLevel === 0) {
            return response()->json([
                'intervention_id' => null,
                'intervention_level' => 0,
                'title' => '',
                'message' => '',
                'options' => []
            ], 200);
        }

        // Generate intervention ID
        $interventionId = 'intv_' . uniqid();

        // Generate message berdasarkan level
        $messages = $this->getInterventionMessage($interventionLevel);

        return response()->json([
            'intervention_id' => $interventionId,
            'intervention_level' => $interventionLevel,
            'title' => $messages['title'],
            'message' => $messages['message'],
            'options' => [
                [
                    'id' => 'heed',
                    'text' => 'Lihat Penjelasan Singkat'
                ],
                [
                    'id' => 'ignore',
                    'text' => 'Lanjut Tanpa Hint'
                ]
            ]
        ], 200);
    }

    /**
     * Generate intervention message berdasarkan level
     */
    private function getInterventionMessage($level)
    {
        $messages = [
            1 => [ // MEDIUM
                'title' => 'Perhatian',
                'message' => '💡 Kamu sudah beberapa kali salah berturut-turut. Mungkin perlu review konsep dulu?'
            ],
            2 => [ // HIGH
                'title' => 'Peringatan',
                'message' => '⚠️ Kamu sudah sering salah di skenario ini. Mungkin perlu review konsep bunga majemuk dulu?'
            ],
            3 => [ // CRITICAL
                'title' => 'Peringatan Serius',
                'message' => '🛑 Kamu sudah banyak salah berturut-turut! Sangat disarankan untuk belajar konsep dasar sebelum melanjutkan.'
            ]
        ];

        return $messages[$level] ?? [
            'title' => 'Info',
            'message' => 'Tetap semangat belajar!'
        ];
    }
}