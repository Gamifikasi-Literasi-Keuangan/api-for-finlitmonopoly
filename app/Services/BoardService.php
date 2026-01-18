<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\ParticipatesIn;
use App\Models\BoardTile;
use App\Models\Scenario;
use App\Models\Card;
use App\Models\QuizCard;

class BoardService
{
    /**
     * GET /tile/{tile_id} (index)
     * Mengambil data tile dan memicu event.
     */
    public function resolveTileEvent(string $playerId, int $tileIndex)
    {
        return DB::transaction(function () use ($playerId, $tileIndex) {
            // 1. Cari Sesi Aktif
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with('session')
                ->lockForUpdate()
                ->first();

            if (!$participation) {
                // Mock Bypass for Dummy Player
                if ($playerId === 'player_dummy_profiling_infinite') {
                    $tile = BoardTile::where('position_index', $tileIndex)->first();
                    if (!$tile)
                        return ['error' => 'Tile not found'];

                    // Mock content determination (simplified)
                    $mockResultId = $this->determineContentId($tile);

                    return [
                        'title' => $tile->name,
                        'category' => $tile->category ?? 'General',
                        'type' => $tile->type,
                        'result_id' => $mockResultId,
                        'turn_phase' => 'resolving_event (MOCK)'
                    ];
                }

                return ['error' => 'Player is not in an active session'];
            }

            $session = $participation->session;

            // 2. Validasi Giliran & Posisi
            if ($session->current_player_id !== $playerId) {
                return ['error' => 'It is not your turn'];
            }

            // Validasi: Apakah pemain benar-benar ada di kotak yang diminta?
            // (Opsional, tapi bagus untuk keamanan)
            if ($participation->position != $tileIndex) {
                return ['error' => "Player is at index {$participation->position}, not {$tileIndex}"];
            }

            // 3. Ambil Data Tile
            $tile = BoardTile::where('position_index', $tileIndex)->first();
            if (!$tile) {
                return ['error' => 'Tile not found'];
            }

            // 4. Tentukan Result ID (Konten)
            // Logika: Cek tipe tile, lalu ambil konten yang sesuai
            $resultId = $this->determineContentId($tile);

            // 5. Update Game State -> resolving_event
            $gameState = json_decode($session->game_state, true) ?? [];
            $gameState['turn_phase'] = 'resolving_event';

            // Simpan event yang sedang aktif agar bisa divalidasi saat submit jawaban
            $gameState['active_event'] = [
                'type' => $tile->type,
                'id' => $resultId
            ];

            $session->game_state = json_encode($gameState);
            $session->save();

            // 6. Return Response
            return [
                'title' => $tile->name,
                'category' => $tile->category ?? 'General',
                'type' => $tile->type, // scenario | risk | chance | quiz | special
                'result_id' => $resultId,
                'turn_phase' => 'resolving_event'
            ];
        });
    }

    /**
     * Helper: Memilih konten berdasarkan tipe tile
     */
    private function determineContentId($tile)
    {
        // 1. Cek apakah ada konten statis (linked_content) di tile
        if (!empty($tile->linked_content)) {
            $linked = json_decode($tile->linked_content, true);
            
            // Untuk special tiles, return special_id
            if ($tile->type === 'special' && isset($linked['special_id'])) {
                return $linked['special_id'];
            }
            
            if (!empty($linked['id'])) {
                return $linked['id'];
            }
        }

        // 2. Jika dinamis, pilih acak dari tabel terkait
        switch ($tile->type) {
            case 'scenario':
                // Ambil scenario acak berdasarkan nama tile (exact match dengan kategori scenario)
                $query = Scenario::query();
                if ($tile->name) {
                    $query->where('category', $tile->name);
                }
                return $query->inRandomOrder()->value('id') ?? 'sc_default';

            case 'risk':
                // Ambil kartu risk acak (uppercase untuk match database)
                $card = Card::where('type', 'RISK')->inRandomOrder()->first();
                return $card ? $card->id : 'card_risk_default';

            case 'chance':
                // Ambil kartu chance acak (uppercase untuk match database)
                $card = Card::where('type', 'CHANCE')->inRandomOrder()->first();
                return $card ? $card->id : 'card_chance_default';

            case 'quiz':
                // Ambil kuis acak
                $quiz = QuizCard::inRandomOrder()->first();
                return $quiz ? $quiz->id : 'quiz_default';

            default:
                // Untuk kotak Start, Parkir Bebas, dll.
                return $tile->tile_id;
        }
    }

    /**
     * Handle special tile events (Start, Terjerat Utang, Dana Darurat Aman, Bangkrut)
     */
    public function handleSpecialTile(string $playerId, string $specialId)
    {
        return DB::transaction(function () use ($playerId, $specialId) {
            $participation = ParticipatesIn::where('playerId', $playerId)
                ->whereHas('session', fn($q) => $q->where('status', 'active'))
                ->with(['session', 'player.profile'])
                ->lockForUpdate()
                ->first();

            if (!$participation) {
                return ['error' => 'Player is not in an active session'];
            }

            $profile = $participation->player->profile;
            if (!$profile) {
                return ['error' => 'Player profile not found'];
            }

            $lifetimeScores = is_string($profile->lifetime_scores) 
                ? json_decode($profile->lifetime_scores, true) 
                : ($profile->lifetime_scores ?? []);

            $affectedScores = [];
            $newScoreValues = [];
            $message = '';

            switch ($specialId) {
                case 'start':
                    // Kotak Start - tidak mengubah score, hanya informasi
                    $message = 'Kembali ke titik awal! Terus semangat mengelola keuangan!';
                    break;

                case 'terjerat_utang':
                    // Terjerat Utang - meningkatkan score utang (semakin tinggi = semakin buruk)
                    $currentDebt = $lifetimeScores['utang'] ?? 0;
                    $newDebt = max(0, min(100, $currentDebt + 15)); // +15 poin utang
                    
                    $affectedScores = ['utang'];
                    $newScoreValues = [$newDebt];
                    $lifetimeScores['utang'] = $newDebt;
                    
                    $message = 'Kamu terjerat utang! Hati-hati dalam mengelola pinjaman. Score utang meningkat.';
                    break;

                case 'dana_darurat_aman':
                    // Dana Darurat Aman - meningkatkan score tabungan & dana darurat
                    $currentSavings = $lifetimeScores['tabungan_dan_dana_darurat'] ?? 0;
                    $newSavings = max(0, min(100, $currentSavings + 20)); // +20 poin
                    
                    $affectedScores = ['tabungan_dan_dana_darurat'];
                    $newScoreValues = [$newSavings];
                    $lifetimeScores['tabungan_dan_dana_darurat'] = $newSavings;
                    
                    $message = 'Dana daruratmu aman! Keuanganmu terlindungi dengan baik. Score tabungan & dana darurat meningkat.';
                    break;

                case 'bangkrut':
                    // Bangkrut - mengurangi beberapa score sekaligus
                    $currentSavings = $lifetimeScores['tabungan_dan_dana_darurat'] ?? 0;
                    $currentInvestment = $lifetimeScores['investasi'] ?? 0;
                    $currentDebt = $lifetimeScores['utang'] ?? 0;
                    
                    $newSavings = max(0, $currentSavings - 30);
                    $newInvestment = max(0, $currentInvestment - 20);
                    $newDebt = max(0, min(100, $currentDebt + 25));
                    
                    $affectedScores = ['tabungan_dan_dana_darurat', 'investasi', 'utang'];
                    $newScoreValues = [$newSavings, $newInvestment, $newDebt];
                    
                    $lifetimeScores['tabungan_dan_dana_darurat'] = $newSavings;
                    $lifetimeScores['investasi'] = $newInvestment;
                    $lifetimeScores['utang'] = $newDebt;
                    
                    $message = 'Kamu bangkrut! Tabungan dan investasi berkurang drastis, sementara utang meningkat. Bangkit lagi!';
                    break;

                default:
                    return ['error' => 'Invalid special tile ID'];
            }

            // Update player profile jika ada perubahan score
            if (!empty($affectedScores)) {
                $profile->lifetime_scores = $lifetimeScores;
                $profile->save();
            }

            return [
                'affected_scores' => $affectedScores,
                'new_score_values' => $newScoreValues,
                'message' => $message
            ];
        });
    }
}
