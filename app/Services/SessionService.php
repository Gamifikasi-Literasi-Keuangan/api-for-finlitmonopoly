<?php

namespace App\Services;

use App\Models\GameSession;
use App\Models\ParticipatesIn;
use App\Models\BoardTile;
use App\Models\Config;
use App\Models\Player;
use App\Models\PlayerProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SessionService
{
    /*
     * Mengambil status sesi permainan yang sedang diikuti pemain:
     * mengecek apakah pemain berada di sesi aktif atau masih menunggu,
     * memuat state permainan (giliran, fase, skor, posisi), lalu
     * mengembalikan ringkasan lengkap status sesi dalam bentuk array.
     */
    public function getSessionState(string $playerId)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', function ($query) {
                $query->whereIn('status', ['active', 'waiting', 'ended']);
            })
            ->with('session.participants')
            ->first();

        if (!$participation) {
            $lobby = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'waiting'))
                ->first();

            if ($lobby) {
                return ['error' => 'Game has not started yet. Please use /matchmaking/status'];
            }

            return ['error' => 'Player is not in an active session'];
        }

        $session = $participation->session;
        $gameState = json_decode($session->game_state, true) ?? [];
        $turnPhase = $gameState['turn_phase'] ?? 'waiting';

        $playersData = [];
        $scoresData = [];
        $positionsData = [];

        $tiles = BoardTile::pluck('name', 'position_index');

        foreach ($session->participants as $p) {
            $playersData[] = [
                'player_id' => $p->playerId,
                'username' => $p->player->name ?? 'Unknown',
                'character_id' => $p->player->character_id ?? 1,
                'connected' => $p->connection_status === 'connected',
                'is_ready' => (bool) $p->is_ready
            ];

            $latestProfile = PlayerProfile::find($p->playerId);
            $latestScores = $latestProfile ? ($latestProfile->lifetime_scores ?? []) : [];

            // Decode jika masih string
            if (is_string($latestScores)) {
                $latestScores = json_decode($latestScores, true) ?? [];
            }

            $pScore = [
                "pendapatan" => $latestScores['pendapatan'] ?? 0,
                "anggaran" => $latestScores['anggaran'] ?? 0,
                "tabungan_dan_dana_darurat" => $latestScores['tabungan_dan_dana_darurat'] ?? 0,
                "utang" => $latestScores['utang'] ?? 0,
                "investasi" => $latestScores['investasi'] ?? 0,
                "asuransi_dan_proteksi" => $latestScores['asuransi_dan_proteksi'] ?? 0,
                "tujuan_jangka_panjang" => $latestScores['tujuan_jangka_panjang'] ?? 0,
                "overall" => $latestProfile->level ?? 0
            ];
            $scoresData[] = $pScore;

            $tileName = $tiles[$p->position] ?? 'Start';
            $positionsData[] = [
                'tile_id' => $p->position,
                'tile_name' => $tileName
            ];
        }

        $currentPlayerName = 'None';
        if ($session->current_player_id) {
            $currentPlayer = $session->participants->firstWhere('playerId', $session->current_player_id);
            $currentPlayerName = $currentPlayer ? $currentPlayer->player->name : 'Unknown';
        }

        return [
            "session_id" => $session->sessionId,
            "status" => $session->status,
            "current_turn_player_id" => $session->current_player_id,
            "current_turn_player_name" => $currentPlayerName,
            "turn_phase" => $turnPhase,
            "turn_number" => $session->current_turn,
            "players" => $playersData,
            "scores" => $scoresData,
            "positions" => $positionsData
        ];
    }

    /**
     * Memulai giliran pemain dalam sesi aktif: memastikan pemain berada
     * di sesi yang benar, mengecek apakah memang gilirannya, lalu
     * mengatur fase giliran ke 'waiting' dan mengembalikan status giliran.
     */
    public function startTurn(string $playerId)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', function ($query) {
                $query->where('status', 'active');
            })
            ->first();

        if (!$participation) {
            return ['error' => 'Player is not in an active session'];
        }

        $session = $participation->session;

        if ($session->current_player_id !== $playerId) {
            return ['error' => 'It is not your turn yet'];
        }

        $gameState = json_decode($session->game_state, true) ?? [];
        $gameState['turn_phase'] = 'waiting';

        $session->game_state = json_encode($gameState);
        $session->save();

        return [
            'turn_phase' => 'waiting',
            'turn_number' => $session->current_turn
        ];
    }

    /**
     * Mengocok dadu untuk pemain dalam sesi aktif:
     * memverifikasi giliran dan fase, menghasilkan nilai dadu acak,
     * memperbarui fase giliran ke 'rolling', lalu mengembalikan hasilnya.
     */
    public function rollDice(string $playerId)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->first();

        if (!$participation) {
            return ['error' => 'Player is not in an active session'];
        }

        $session = $participation->session;

        if ($session->current_player_id !== $playerId) {
            return ['error' => 'It is not your turn'];
        }

        $gameState = json_decode($session->game_state, true) ?? [];
        $currentPhase = $gameState['turn_phase'] ?? 'waiting';

        if ($currentPhase !== 'waiting') {
            return ['error' => "Cannot roll dice in '$currentPhase' phase. Please wait or check state."];
        }

        $diceValue = rand(1, 6);

        $gameState['turn_phase'] = 'rolling';
        $gameState['last_dice'] = $diceValue;

        $session->game_state = json_encode($gameState);
        $session->save();

        return [
            'turn_phase' => 'rolling',
            'dice_value' => $diceValue
        ];
    }

    /**
     * Memindahkan pemain berdasarkan nilai dadu terakhir:
     * memverifikasi giliran dan fase, menghitung posisi baru,
     * memperbarui posisi pemain dan fase giliran, lalu mengembalikan detail pergerakan.
     */
    public function movePlayer(string $playerId)
    {
        return DB::transaction(function () use ($playerId) {
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with('session')
                ->lockForUpdate()
                ->first();

            if (!$participation) {
                return ['error' => 'Player is not in an active session'];
            }

            $session = $participation->session;

            if ($session->current_player_id !== $playerId) {
                return ['error' => 'It is not your turn'];
            }

            $gameState = json_decode($session->game_state, true) ?? [];
            $currentPhase = $gameState['turn_phase'] ?? 'waiting';

            if ($currentPhase !== 'rolling') {
                return ['error' => "Cannot move in '$currentPhase' phase. You need to roll dice first."];
            }

            $diceValue = $gameState['last_dice'] ?? 0;
            if ($diceValue == 0) {
                return ['error' => 'Dice value invalid. Please roll again.'];
            }

            $currentPosition = $participation->position;

            $totalTiles = BoardTile::count();
            if ($totalTiles == 0)
                $totalTiles = 20;

            $newPosition = ($currentPosition + $diceValue) % $totalTiles;

            if ($newPosition < $currentPosition) {
                // Logika "Pass Go" (Dapat uang) bisa ditaruh di sini
                // $participation->score += 200;

            }

            $gameState['prev_position'] = $currentPosition;
            $participation->position = $newPosition;
            $participation->save();

            $gameState['turn_phase'] = 'moving';
            $session->game_state = json_encode($gameState);
            $session->save();

            return [
                'turn_phase' => 'moving',
                'from_tile' => $currentPosition,
                'to_tile' => $newPosition
            ];
        });
    }

    /**
     * Mengambil informasi giliran saat ini untuk pemain yang diautentikasi:
     * memverifikasi partisipasi di sesi aktif, memuat state permainan,
     * lalu mengembalikan detail giliran termasuk pemain saat ini dan aksi terakhir.
     */
    public function getCurrentTurn(string $playerId)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with(['session.participants'])
            ->first();

        if (!$participation) {
            return ['error' => 'Player is not in an active session'];
        }

        $session = $participation->session;
        $gameState = json_decode($session->game_state, true) ?? [];

        $currentPlayerId = $session->current_player_id;
        $currentPlayerName = 'Unknown';
        $currentParticipant = $session->participants->firstWhere('playerId', $currentPlayerId);

        if ($currentParticipant) {
            $currentPlayerName = $currentParticipant->player->name;
            $currentPos = $currentParticipant->position;
        } else {
            $currentPos = 0;
        }

        $tile = BoardTile::where('position_index', $currentPos)->first();

        $eventType = 'none';
        $eventId = null;

        if ($tile) {
            $eventType = $tile->type;
            $linkedContent = json_decode($tile->linked_content, true);
            $eventId = $linkedContent['id'] ?? $tile->tile_id;
        }

        $actionData = [
            'dice_value' => $gameState['last_dice'] ?? 0,
            'from_tile' => $gameState['prev_position'] ?? ($currentPos - ($gameState['last_dice'] ?? 0)),
            'to_tile' => $currentPos,
            'landed_event_type' => $eventType,
            'landed_event_id' => $eventId
        ];

        if ($actionData['from_tile'] < 0) {
            $totalTiles = BoardTile::count() ?: 20;
            $actionData['from_tile'] += $totalTiles;
        }

        return [
            'turn_number' => $session->current_turn,
            'turn_phase' => $gameState['turn_phase'] ?? 'waiting',
            'current_turn_player_name' => $currentPlayerName,
            'current_turn_player_id' => $currentPlayerId,
            'current_turn_action' => $actionData
        ];
    }

    /**
     * Mengakhiri giliran pemain dalam sesi aktif:
     * memverifikasi giliran dan fase, menentukan pemain berikutnya,
     * memperbarui giliran dan fase, lalu mengembalikan detail giliran berikutnya.
     * Jika turn_number mencapai max_turns, sesi otomatis berakhir (status = 'ended').
     */
    public function endTurn(string $playerId)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with(['session.participants'])
            ->first();

        if (!$participation) {
            return ['error' => 'Player is not in an active session'];
        }

        $session = $participation->session;

        if ($session->current_player_id !== $playerId) {
            return ['error' => 'It is not your turn to end'];
        }

        $participants = $session->participants->sortBy('player_order')->values();

        $currentIndex = $participants->search(function ($p) use ($playerId) {
            return $p->playerId === $playerId;
        });

        if ($currentIndex === false) {
            return ['error' => 'Player participation data error'];
        }

        $nextIndex = ($currentIndex + 1) % $participants->count();
        $nextPlayer = $participants[$nextIndex];

        $session->current_player_id = $nextPlayer->playerId;
        $session->current_turn += 1;

        $gameState = json_decode($session->game_state, true) ?? [];
        $gameState['turn_phase'] = 'waiting';
        $gameState['last_dice'] = 0;

        $session->game_state = json_encode($gameState);
        
        // Check if game should end (turn_number >= max_turns)
        if ($session->current_turn >= $session->max_turns) {
            $session->status = 'ended';
        }
        
        $session->save();

        $response = [
            'turn_phase' => 'completed',
            'next_turn_player_id' => $nextPlayer->playerId,
            'turn_number' => $session->current_turn
        ];

        // Add session_ended flag if game ended
        if ($session->status === 'ended') {
            $response['session_ended'] = true;
            $response['message'] = 'Game session has ended. Maximum turns reached.';
        }

        return $response;
    }

    /**
     * Finalize and leave session
     * Updates player profile with final ANN evaluation
     */
    public function leaveSession(string $playerId)
    {
        return DB::transaction(function () use ($playerId) {
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->whereIn('status', ['active', 'waiting']))
                ->first();

            if (!$participation) {
                return ['error' => 'Player is not in any session'];
            }

            $sessionId = $participation->sessionId;
            $session = $participation->session;

            // Mark player as disconnected
            $participation->connection_status = 'disconnected';
            $participation->save();

            // Check if all players left
            $remainingPlayers = ParticipatesIn::where('sessionId', $sessionId)
                ->where('connection_status', 'connected')
                ->count();

            if ($remainingPlayers === 0) {
                $session->status = 'completed';
                $session->save();
            }

            return [
                'message' => 'Successfully left session',
                'session_id' => $sessionId
            ];
        });
    }
}
