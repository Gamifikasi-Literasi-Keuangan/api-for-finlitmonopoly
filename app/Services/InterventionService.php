<?php

namespace App\Services;

use App\Models\InterventionTemplate;
use App\Repositories\InterventionRepository;
use Illuminate\Support\Str;

class InterventionService
{
    protected $repository;

    public function __construct(InterventionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Main Entry Point: Cek apakah intervensi perlu ditrigger
     * Automatically determines logic based on Turn Phase.
     *
     * @param string $playerId
     * @return array|null
     */
    public function checkInterventionTrigger(string $playerId): ?array
    {
        // Ambil Partisipasi Player untuk cek Session & Turn Phase
        $participation = \App\Models\ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with(['session'])
            ->first();

        if (!$participation || !$participation->session) {
            return null;
        }

        $session = $participation->session;
        // Parse Game State untuk dapat Turn Phase
        $gameState = is_string($session->game_state) ? json_decode($session->game_state, true) : $session->game_state;
        $turnPhase = $gameState['turn_phase'] ?? 'unknown';

        // Routing Logic berdasarkan Phase
        if ($turnPhase === 'waiting') {
            // Awal Giliran -> Cek Level 4 (Time-based / Contextual Care)
            return $this->checkLevel4Intervention($participation);
        } elseif (in_array($turnPhase, ['rolling', 'moving', 'resolving_event', 'completed'])) {
            // Akhir Giliran -> Cek Level 1-3 (Performance-based)
            return $this->checkLevel1to3Intervention($playerId, $participation);
        }

        return null;
    }

    /**
     * Cek Intervensi Level 4 (Time-based / Contextual Care)
     * Dipanggil saat phase 'waiting' (Start of Turn).
     */
    protected function checkLevel4Intervention($participation): ?array
    {
        // Gunakan last_break_end_at jika ada, jika tidak gunakan joined_at/started_at
        $startTime = $participation->last_break_end_at
            ? \Carbon\Carbon::parse($participation->last_break_end_at)
            : \Carbon\Carbon::parse($participation->joined_at ?? $participation->session->started_at);

        $now = now();
        $durationMinutes = $startTime->diffInMinutes($now);

        // Trigger jika > 45 menit DAN belum break
        if ($durationMinutes > 45 && !$participation->on_break) {
            // Check for SPAM
            $alreadyIgnored = \App\Models\PlayerDecision::where('player_id', $participation->playerId)
                ->where('intervention_type', 'break')
                ->where('player_response', 'ignored')
                ->where('created_at', '>', $startTime)
                ->exists();

            if ($alreadyIgnored) {
                return null;
            }

            return [
                'intervention_id' => 'intv_L4_' . Str::random(8),
                'intervention_level' => 4,
                'intervention_type' => 'break',
                'title' => 'Istirahat Sejenak?',
                'message' => 'Kamu sudah main fokus 45 menit lebih. Riset menunjukkan performa turun saat lelah.',
                'is_mandatory' => true,
                'options' => [
                    ['id' => 'heeded', 'text' => 'Istirahat Dulu'],
                    ['id' => 'ignored', 'text' => 'Masih Kuat Lanjut']
                ],
                'heed_message' => 'Selamat beristirahat! Kami akan tunggu.',
                'on_break_suggestion' => true
            ];
        }

        return null;
    }

    /**
     * Cek Intervensi Level 1-3 (Performance-based)
     * Dipanggil saat phase 'moving', 'resolving_event', dll.
     */
    protected function checkLevel1to3Intervention(string $playerId, $participation): ?array
    {
        // Menentukan kategori aktif dari papan aktif
        // participatesin.position -> boardtiles.position_index
        $currentTile = \App\Models\BoardTile::where('position_index', $participation->position)->first();

        // Safe fallback
        if (!$currentTile) {
            return null;
        }

        $activeCategory = $currentTile->category;

        // Skip jika kategori 'special', 'card', 'quiz', atau lainnya yang bukan scenario
        $ignoredCategories = ['special', 'card', 'chance', 'risk', 'quiz'];

        // Cek juga 'type', karena boardtiles punya kolom 'type' dan 'category'.
        // Jika type != 'scenario', mungkin tidak perlu intervensi skill.
        if (in_array($activeCategory, $ignoredCategories) || $currentTile->type !== 'scenario') {
            return null;
        }

        // Ambil 15 Keputusan Terakhir
        $decisions = $this->repository->getRecentDecisions($playerId, 15);

        // Analisis Pola Kesalahan
        $analysis = $this->repository->analyzeStreaks($decisions);
        $categoryStreaks = $analysis['category_streaks'];

        // Ambil streak untuk kategori AKTIF saaat ini
        $currentCategoryStreak = $categoryStreaks[$activeCategory] ?? 0;

        $triggerLevel = 0;

        // Prioritas Logic:
        // Cek kesalahan beruntun PADA KATEGORI YANG SAMA DENGAN POSISI SAAT INI.

        // Level 3: Critical (>= 3 errors berturut-turut di kategori ini)
        if ($currentCategoryStreak >= 3) {
            $triggerLevel = 3;
        }
        // Level 2: Warning (>= 2 errors berturut-turut di kategori ini)
        elseif ($currentCategoryStreak >= 2) {
            $triggerLevel = 2;
        }
        // Level 1: Reminder/General (Global errors?)
        // Global >= 2
        elseif ($analysis['global_errors'] >= 2) {
            // Opsional: Probabilitas 60%
            if (rand(1, 100) <= 60) {
                $triggerLevel = 1;
            }
        }

        if ($triggerLevel === 0) {
            return null;
        }

        // Fetch Template Intervensi
        // Cari template yang match Level & Kategori Aktif (jika ada spesifik)
        // Jika tidak ada spesifik kategori, cari yang general (category IS NULL)

        // Coba cari Spesifik dulu
        $template = InterventionTemplate::where('level', $triggerLevel)
            ->where('category', $activeCategory)
            ->inRandomOrder()
            ->first();

        // Jika tidak ada, cari General (fallback)
        if (!$template) {
            $template = InterventionTemplate::where('level', $triggerLevel)
                ->whereNull('category')
                ->inRandomOrder()
                ->first();
        }

        if (!$template) {
            return null;
        }

        // Format Response
        $message = $template->message_template;
        $title = $template->title_template;

        // Replace placeholder {category} dengan nama yang bisa dibaca
        $friendlyCategoryName = ucwords(str_replace(['_'], [' '], $activeCategory));
        $message = str_replace('{category}', $friendlyCategoryName, $message);
        $title = str_replace('{category}', $friendlyCategoryName, $title);

        return [
            'intervention_id' => 'intv_L' . $template->level . '_' . Str::random(8),
            'intervention_level' => (int) $template->level,
            'title' => $title,
            'message' => $message,
            'is_mandatory' => (bool) $template->is_mandatory,
            'options' => $template->actions_template ?: [],
        ];
    }
}