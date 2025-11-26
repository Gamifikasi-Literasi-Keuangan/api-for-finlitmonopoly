<?php

namespace App\Repositories;

use App\Models\Telemetry;

class FeedbackRepository
{
    /**
     * Mencatat log feedback ke tabel telemetry
     * Hanya log jika sessionId tersedia (karena kolom NOT NULL)
     */
    public function logIntervention(array $data)
    {
        // Skip logging jika tidak ada session_id (karena kolom NOT NULL)
        if (empty($data['session_id'])) {
            return null;
        }

        return Telemetry::create([
            'playerId' => $data['player_id'] ?? 'unknown',
            'sessionId' => $data['session_id'],
            'turn_id' => $data['turn_id'] ?? null,
            'tile_id' => $data['tile_id'] ?? null,
            'action' => 'intervention_feedback',
            'details' => json_encode([
                'intervention_id' => $data['intervention_id'],
                'scenario_id' => $data['scenario_id'],
                'player_response' => $data['player_response']
            ]),
            'metadata' => json_encode($data),
            'created_at' => now()
        ]);
    }
}