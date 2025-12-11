<?php

namespace App\Services;

use App\Models\PlayerDecision;
use App\Models\ParticipatesIn;

class FeedbackService
{
    /**
     * Menyimpan respon pemain terhadap intervensi (misal: heed/ignore)
     */
    public function storeInterventionFeedback(string $playerId, array $data)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with('session')
            ->first();

        $sessionId = $participation ? $participation->sessionId : 'unknown_session';
        $turnNumber = $participation ? ($participation->session->current_turn ?? 0) : 0;

        // Catat sebagai keputusan (decision) tipe 'intervention_response'
        PlayerDecision::create([
            'player_id' => $playerId,
            'session_id' => $sessionId,
            'turn_number' => $turnNumber,
            'content_id' => $data['intervention_id'],
            'content_type' => 'intervention_response',
            'selected_option' => $data['player_response'],
            'is_correct' => false,
            'score_change' => 0,
            'decision_time_seconds' => 0,
            'created_at' => now()
        ]);

        return [
            'status' => 'success',
            'message' => 'Feedback recorded',
            'data' => [
                'intervention_id' => $data['intervention_id'],
                'response' => $data['player_response']
            ]
        ];
    }
}
