<?php

namespace App\Services;

use App\Models\Session;
use App\Models\ParticipatesIn;
use App\Models\BoardTile;
use App\Models\Turn;
use App\Models\Player;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Untuk transaksi

class SessionService
{
    /**
     * Implementasi Sequence Diagram: /session/turn/start
     */
    public function startTurn(string $sessionId, string $playerId)
    {
        $session = Session::findOrFail($sessionId);

        // Validasi Logika Bisnis
        if ($session->current_player_id !== $playerId) {
            throw new \Exception("Bukan giliran pemain ini.", 403);
        }
        if ($session->status === 'turn_started') {
            throw new \Exception("Giliran sedang berjalan.", 409); // 409 Conflict
        }

        // Buat record giliran baru
        $turn = Turn::create([
            'turn_id' => 'turn_' . Str::uuid(),
            'session_id' => $sessionId,
            'player_id' => $playerId,
            'turn_number' => $session->turn_index + 1,
        ]);

        // Update status sesi
        $session->status = 'turn_started';
        $session->save();

        return $turn; // Kembalikan data giliran
    }

    /**
     * Implementasi Sequence Diagram: /session/player/move
     */
    public function movePlayer(string $sessionId, string $playerId, int $fromTile, int $steps): array
    {
        // 1. Logika Bisnis: Hitung posisi baru
        $newPosition = ($fromTile + $steps) % 40; // Asumsi 40 petak

        // 2. Panggil Repository (Eloquent) -> Update DB `ParticipatesIn`
        ParticipatesIn::where('sessionId', $sessionId)
            ->where('playerId', $playerId)
            ->update(['position' => $newPosition]);

        // 3. Panggil Repository (Eloquent) -> Baca DB `board_tiles`
        $tile = BoardTile::where('position', $newPosition)->firstOrFail();

        // 4. Kembalikan data sesuai spesifikasi
        return [
            'from_tile' => $fromTile,
            'to_tile' => $newPosition,
            'tile_type' => $tile->type, 
            'next_action' => ['related_id' => $tile->related_id] // (ID skenario/kuis/dll)
        ];
    }

    /**
     * Implementasi Sequence Diagram: /session/turn/end
     */
    public function endTurn(string $sessionId, string $playerId, string $turnId, array $actions = [])
    {
        // Gunakan Transaksi Database untuk memastikan semua update berhasil
        return DB::transaction(function () use ($sessionId, $playerId, $turnId, $actions) {
            // 1. Update data giliran (Turn)
            $turn = Turn::findOrFail($turnId);
            $turn->end_time = now();
            $turn->save();

            // 2. (Opsional) Catat 'actions' ke tabel Telemetry
            // ... (logika untuk menyimpan 'actions' ke tabel 'telemetry') ...

            // 3. Tentukan pemain selanjutnya
            $session = Session::with('players')->findOrFail($sessionId); // Muat relasi players
            $playerCount = $session->players->count();
            
            $nextTurnIndex = ($session->turn_index + 1) % $playerCount;
            $nextPlayer = $session->players[$nextTurnIndex];
            
            // 4. Update Sesi
            $session->turn_index = $nextTurnIndex;
            $session->current_player_id = $nextPlayer->PlayerId;
            $session->status = 'turn_pending'; // Menunggu pemain selanjutnya
            $session->save();

            return [
                'next_player_id' => $nextPlayer->PlayerId,
                'next_turn_index' => $nextTurnIndex
            ];
        });
    }
    
    /**
     * Implementasi Sequence Diagram: /session/end
     */
    public function endSession(string $sessionId)
    {
        return DB::transaction(function () use ($sessionId) {
            // 1. Update status sesi
            $session = Session::findOrFail($sessionId);
            $session->status = 'finished';
            $session->ended_at = now();
            $session->save();

            // 2. Ambil ranking akhir
            $rankings = ParticipatesIn::where('sessionId', $sessionId)
                            ->orderBy('score', 'desc')
                            ->get();
            
            // 3. Update 'gamesPlayed' di tabel 'players'
            $playerIds = $rankings->pluck('playerId');
            Player::whereIn('PlayerId', $playerIds)->increment('gamesPlayed');

            return [
                'sessionId' => $sessionId,
                'status' => 'finished',
                'rankings' => $rankings
            ];
        });
    }
}