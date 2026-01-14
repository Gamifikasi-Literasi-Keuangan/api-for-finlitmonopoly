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
     * Mapping kategori Tile (umum) ke kategori Scenario (spesifik)
     * Digunakan untuk random selection scenario berdasarkan kategori tile
     */
    private const CATEGORY_MAPPING = [
        'pendapatan' => ['Uang Bulanan', 'Pendapatan', 'Beasiswa'],
        'anggaran' => ['Makan', 'Transport', 'Nongkrong'],
        'tabungan_dan_dana_darurat' => ['Tabungan & Dana Darurat'],
        'utang' => ['Pinjaman Teman', 'Pinjol', 'Utang'],
        'investasi' => ['Reksadana', 'Saham', 'Cryptoocurrency'],
        'asuransi' => ['Asuransi Kesehatan', 'Asuransi Kendaraan', 'Asuransi Barang/Harta'],
        'tujuan_jangka_panjang' => ['Pendidikan', 'Pengalaman', 'Aset Produktif']
    ];

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
                'type' => $tile->type, // scenario | risk | chance | quiz
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
            if (!empty($linked['id'])) {
                return $linked['id'];
            }
        }

        // 2. Jika dinamis, pilih acak dari tabel terkait
        switch ($tile->type) {
            case 'scenario':
                // Ambil scenario acak dengan mapping kategori
                $query = Scenario::query();
                if ($tile->category && isset(self::CATEGORY_MAPPING[$tile->category])) {
                    // Gunakan mapping untuk mencari scenario dengan sub-kategori yang sesuai
                    $query->whereIn('category', self::CATEGORY_MAPPING[$tile->category]);
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
}
