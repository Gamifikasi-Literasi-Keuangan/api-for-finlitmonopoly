<?php

namespace App\Services;

use App\Models\GameSession; 
use App\Models\ParticipatesIn;
use App\Models\Config;
use App\Models\Player;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SessionService
{
    /**
     * Logika Utama Matchmaking
     * POST /matchmaking/join
     */
    public function joinMatchmaking(string $playerId)
    {
        return DB::transaction(function () use ($playerId) {
            $activeSession = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', function ($query) {
                    $query->whereIn('status', ['waiting', 'active']);
                })
                ->first();

            if ($activeSession) {
                return [
                    'ok' => true,
                    'queue_position' => $activeSession->player_order ?? 1
                ];
            }

            $config = Config::first();
            $maxPlayers = $config ? $config->maxPlayers : 4;

            $availableSession = GameSession::where('status', 'waiting')
                ->whereRaw('(SELECT COUNT(*) FROM participatesin WHERE sessionId = game_sessions.sessionId) < ?', [$maxPlayers])
                ->lockForUpdate()
                ->first();

            $myPosition = 0;

            if ($availableSession) {
                $sessionId = $availableSession->sessionId;
                $myPosition = $this->addPlayerToSession($sessionId, $playerId, false);
            } else {
                $sessionId = 'sess_' . Str::random(8);
                $configMaxTurns = $config ? $config->max_turns : 50;

                GameSession::create([
                    'sessionId' => $sessionId,
                    'host_player_id' => $playerId,
                    'max_players' => $maxPlayers,
                    'max_turns' => $configMaxTurns,
                    'status' => 'waiting',
                    'current_turn' => 0,
                    'created_at' => now(),
                ]);

                $myPosition = $this->addPlayerToSession($sessionId, $playerId, true);
            }

            return [
                'ok' => true,
                'queue_position' => $myPosition
            ];
        });
    }

    /**
     * Tambah Pemain ke Sesi
     */
    private function addPlayerToSession($sessionId, $playerId, $isHost = false)
    {
        $currentCount = ParticipatesIn::where('sessionId', $sessionId)->count();
        $newOrder = $currentCount + 1;

        ParticipatesIn::create([
            'sessionId' => $sessionId,
            'playerId' => $playerId,
            'position' => 0,
            'score' => 0,
            'player_order' => $newOrder,
            'connection_status' => 'connected',
            'is_ready' => $isHost ? true : false,
            'joined_at' => now()
        ]);

        return $newOrder;
    }

    public function getSessionData($sessionId, $statusMessage)
    {
        $session = GameSession::with('participants')->find($sessionId);

        // Format data player untuk response
        $playersList = $session->participants->map(function ($p)  use ($session) {
            return [
                'player_id' => $p->playerId,
                'username' => $p->player->name ?? 'Unknown', // Ambil dari relasi player
                'avatar' => $p->player->avatar_url ?? null,
                'is_ready' => (bool) $p->is_ready,
                'is_host' => $p->playerId === $session->host_player_id
            ];
        });

        return [
            'status' => 'success',
            'matchmaking_status' => $statusMessage,
            'session_id' => $session->sessionId,
            'session_status' => $session->status,
            'max_players' => $session->max_players,
            'current_players_count' => $playersList->count(),
            'players' => $playersList
        ];
    }

    /*
    
    */
    public function updatePlayerCharacter(string $playerId, string $characterId) {
        $player = Player::find($playerId);
        if (!$player) {
            throw new (\Exception(message: "Player not found"));
        }
        $player->character_id = $characterId;
        $player->avatar = $this->getAvatarUrlForCharacter($characterId);
        $player->save();

        return [ 'ok' => true ];
    }

    /*

    */
    private function getAvatarUrlForCharacter(int $id) {
        return "https://api.dicebear.com/7.x/adventurer/svg?seed=Char_" . $id;
    }
}
