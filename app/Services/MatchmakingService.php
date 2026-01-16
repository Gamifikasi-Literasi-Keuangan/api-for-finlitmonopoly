<?php

namespace App\Services;

use App\Models\GameSession;
use App\Models\ParticipatesIn;
use App\Models\Config;
use App\Models\Player;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MatchmakingService
{
    /**
     * Memasukkan pemain ke dalam antrean matchmaking berbasis sesi.
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
                ->has('participants', '<', $maxPlayers)
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
                    'current_player_id' => $playerId,
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
     * Menambah pemain ke sebuah sesi permainan
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

    /**
     * Mengambil dan merakit data lengkap sebuah sesi permainan untuk dikirim ke client.
     */
    public function getSessionData($sessionId, $statusMessage)
    {
        $session = GameSession::with('participants')->find($sessionId);

        // Format data player untuk response
        $playersList = $session->participants->map(function ($p) use ($session) {
            return [
                'player_id' => $p->playerId,
                'username' => $p->player->name ?? 'Unknown',
                'avatar_url' => $p->player->avatar_url ?? null,
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

    /**
     * Memperbarui karakter yang dipilih pemain beserta avatar visualnya.
     */
    public function updatePlayerCharacter(string $playerId, string $characterId)
    {
        $player = Player::find($playerId);
        if (!$player) {
            throw new (\Exception(message: "Player not found"));
        }
        $player->character_id = $characterId;
        $player->avatar_url = $this->getAvatarUrlForCharacter($characterId);
        $player->save();

        return ['ok' => true];
    }

    /**
     * Mendapatkan URL Avatar berdasarkan ID Karakter
     */
    private function getAvatarUrlForCharacter(int $id)
    {
        return "https://api.dicebear.com/7.x/adventurer/svg?seed=Char_" . $id;
    }

    /**
     * Mengambil status terbaru matchmaking/lobi untuk seorang pemain.
     */
    public function getMatchmakingStatus(string $playerId)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', function ($query) {
                $query->whereIn('status', ['waiting', 'active', 'finished']);
            })
            ->first();

        if (!$participation) {
            return ['error' => 'Player is not in any active lobby/session'];
        }

        $session = GameSession::with('participants')->find($participation->sessionId);

        $totalPlayers = $session->participants->count();
        $readyCount = $session->participants->where('is_ready', true)->count();
        $maxPlayers = $session->max_players;

        $lobbyStatus = 'waiting_for_players';

        if ($session->status === 'active') {
            $lobbyStatus = 'session_assigned';
        } elseif ($totalPlayers >= $maxPlayers) {
            if ($readyCount >= $totalPlayers) {
                $lobbyStatus = 'preparing_session';
            } else {
                $lobbyStatus = 'waiting_for_ready';
            }
        } else {
            $lobbyStatus;
        }

        $playersList = $session->participants->map(function ($p) use ($session) {
            return [
                'player_id' => $p->playerId,
                'username' => $p->player->name ?? 'Unknown Player',
                'character_id' => $p->player->character_id ?? 1,
                'connected' => $p->connection_status === 'connected',
                'is_ready' => (bool) $p->is_ready
            ];
        });

        return [
            'status' => $lobbyStatus,
            'session_id' => $session->sessionId,
            'ready_count' => $readyCount,
            'total_players' => $totalPlayers,
            'max_players' => $maxPlayers,
            'players' => $playersList
        ];
    }

    /**
     * Menandai status kesiapan (ready / not ready) seorang pemain di sebuah session yang masih dalam status 'waiting'.
     */
    public function setPlayerReady(string $playerId, bool $isReady)
    {
        $participation = ParticipatesIn::where('playerId', $playerId)
            ->WhereHas('session', function ($query) {
                $query->where('status', 'waiting');
            })
            ->first();

        if (!$participation) {
            throw new \Exception("Player is not in a waiting session");
        }
        $participation->is_ready = $isReady;
        $participation->save();

        // Cek apakah semua pemain sudah ready
        $session = $participation->session; // Eager load
        $participants = $session->participants; // Relasi HasMany

        $total = $participants->count();
        $readyCount = $participants->where('is_ready', true)->count();

        // Jika sudah penuh dan semua ready, start session
        if ($total >= $session->max_players && $readyCount == $total) {
            $session->status = 'active';
            $session->started_at = now();
            
            // Set giliran pertama ke player dengan order 1 (player pertama yang join)
            $firstPlayer = $participants->sortBy('player_order')->first();
            $session->current_player_id = $firstPlayer ? $firstPlayer->playerId : $session->host_player_id;
            
            // set current_turn = 1
            $session->current_turn = 1;

            // Inisialisasi game_state dengan turn_phase
            $gameState = [
                'turn_phase' => 'waiting',
                'last_dice' => 0,
                'prev_position' => 0
            ];
            $session->game_state = json_encode($gameState);

            $session->save();
        }

        return ['ok' => true];
    }
}
