<?php

namespace App\Services;

use App\Models\PlayerDecision;
use App\Models\ParticipatesIn;
use App\Models\InterventionTemplate;
use App\Models\BoardTile;
use App\Repositories\InterventionRepository;

class FeedbackService
{
    protected $repo;

    public function __construct(InterventionRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Menyimpan respon pemain terhadap intervensi
     */
    public function storeInterventionFeedback(string $playerId, array $data)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with('session')
            ->first();

        $sessionId = $participation ? $participation->sessionId : null;
        $turnNumber = $participation ? ($participation->session->current_turn ?? 0) : 0;

        // Catat sebagai keputusan (decision) tipe 'intervention_log'
        $interventionType = $data['intervention_type'] ?? null;
        if (!$interventionType && stripos($data['intervention_id'], 'intv_l4_') === 0) {
            $interventionType = 'break';
        }

        PlayerDecision::create([
            'player_id' => $playerId,
            'session_id' => $sessionId,
            'turn_number' => $turnNumber,
            'content_id' => $data['scenario_id'],
            'intervention_id' => $data['intervention_id'],
            'intervention_type' => $interventionType,
            'content_type' => 'intervention_log',
            'player_response' => $data['player_response'],
            'is_correct' => false,
            'score_change' => 0,
            'created_at' => now()
        ]);

        $heedMessage = null;

        // Cek Apakah Heeded
        if ($data['player_response'] !== 'ignored') {

            // Checks untuk Level 4
            if ($interventionType === 'break') {
                $heedMessage = "Selamat beristirahat! Kami akan tunggu.";
                if ($participation) {
                    $participation->update(['on_break' => true]);
                }
            } else {
                $contentId = $data['scenario_id'] ?? null;
                $category = null;

                if ($contentId) {
                    $category = $this->repo->getCategoryFromContentId($contentId);
                }

                if (!$category && $participation) {
                    $currentTile = BoardTile::where('position_index', $participation->position)->first();
                    if ($currentTile) {
                        $category = $currentTile->category;
                    }
                }

                // Mengambil level dari Parsing ID
                $level = $data['intervention_level'] ?? null;

                if (!$level && isset($data['intervention_id'])) {
                    if (preg_match('/intv_L(\d+)_/', $data['intervention_id'], $matches)) {
                        $level = (int) $matches[1];
                    }
                }

                $query = InterventionTemplate::query();

                if ($level) {
                    $query->where('level', $level);

                    // If Level 1, it is generic (category is null)
                    if ($level == 1) {
                        $query->whereNull('category');
                    }
                    // If Level 2 or 3, it should match the category
                    elseif ($category) {
                        $query->where('category', $category);
                    }
                } else {
                    // Fallback
                    if ($category) {
                        $query->whereIn('level', [2, 3])->where('category', $category);
                    }
                }

                $template = $query->first();

                if ($template && $template->heed_message) {
                    $heedMessage = $template->heed_message;
                }
            }
        }

        return [
            'ok' => true,
            'heed_message' => $heedMessage
        ];
    }
}
