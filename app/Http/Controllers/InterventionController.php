<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterventionController extends Controller
{
    /**
     * API: GET /intervention/trigger
     * Mengambil pesan intervensi berdasarkan level risiko
     */
    public function trigger(Request $request)
    {
        $playerId = $request->input('player_id');
        $sessionId = $request->input('session_id');

        // Jika tidak ada player_id, return intervention level 0
        if (!$playerId) {
            return response()->json([
                'intervention_id' => null,
                'intervention_level' => 0,
                'title' => '',
                'message' => '',
                'options' => []
            ], 200);
        }

        // Cek error streak untuk tentukan level intervention
        $recentDecisions = DB::table('player_decisions')
            ->where('player_id', $playerId)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Hitung error streak
        $errorStreak = 0;
        $weakArea = null;

        foreach ($recentDecisions as $decision) {
            if (!$decision->is_correct) {
                $errorStreak++;

                // Ambil area lemah dari decision terakhir
                if (!$weakArea && $decision->content_type === 'scenario') {
                    $scenario = DB::table('scenarios')
                        ->where('id', $decision->content_id)
                        ->first();
                    $weakArea = $scenario->category ?? 'Keuangan';
                }
            } else {
                break;
            }
        }

        // Tentukan intervention level berdasarkan error streak
        $interventionLevel = 0;
        if ($errorStreak >= 4) {
            $interventionLevel = 3; // CRITICAL
        } elseif ($errorStreak >= 3) {
            $interventionLevel = 2; // HIGH
        } elseif ($errorStreak >= 2) {
            $interventionLevel = 1; // MEDIUM
        }

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
        $messages = $this->getInterventionMessage($interventionLevel, $errorStreak, $weakArea);

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
    private function getInterventionMessage($level, $errorStreak, $weakArea)
    {
        $area = $weakArea ?? 'Keuangan';

        $messages = [
            1 => [ // MEDIUM
                'title' => 'Perhatian',
                'message' => "💡 Kamu sudah {$errorStreak}x salah berturut-turut di area {$area}. Mungkin perlu review konsep dulu?"
            ],
            2 => [ // HIGH
                'title' => 'Peringatan',
                'message' => "⚠️ Kamu sudah {$errorStreak}x salah di skenario {$area}. Mungkin perlu review konsep bunga majemuk dulu?"
            ],
            3 => [ // CRITICAL
                'title' => 'Peringatan Serius',
                'message' => "🛑 Kamu sudah {$errorStreak}x salah berturut-turut! Sangat disarankan untuk belajar konsep {$area} sebelum melanjutkan."
            ]
        ];

        return $messages[$level] ?? [
            'title' => 'Info',
            'message' => 'Tetap semangat belajar!'
        ];
    }
}