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
     * Cek apakah intervensi perlu ditrigger berdasarkan performa player
     *
     * @param string $playerId
     * @param string|null $scenarioId Contextual Scenario ID (e.g. SCN_UTANG_01)
     * @return array|null
     */
    public function checkInterventionTrigger(string $playerId, ?string $scenarioId = null): ?array
    {
        // Intervensi Level 4 (Contextual Care)
        // Cek durasi bermain. Menghitung selisih dari joined_at atau last_break_end_at
        $participation = \App\Models\ParticipatesIn::where('playerId', $playerId)
            ->whereHas('session', fn($q) => $q->where('status', 'active'))
            ->with('session')
            ->first();

        if ($participation) {
            // Gunakan last_break_end_at jika ada, jika tidak gunakan joined_at/started_at
            $startTime = $participation->last_break_end_at
                ? \Carbon\Carbon::parse($participation->last_break_end_at)
                : \Carbon\Carbon::parse($participation->joined_at ?? $participation->session->started_at);

            $now = now();
            $durationMinutes = $startTime->diffInMinutes($now);

            // Trigger jika > 45 menit DAN belum break (on_break = false)
            if ($durationMinutes > 45 && !$participation->on_break) {
                // Check for SPAM:
                // Apakah user sudah pernah menerima intervensi 'break' dalam segmen waktu ini?
                // Kita cari PlayerDecision tipe 'break' yang dibuat SETELAH startTime.
                // DAN response-nya adalah 'ignored' (User memilih lanjut main).
                // Jika sudah pernah ignore, JANGAN trigger lagi (request user).

                $alreadyIgnored = \App\Models\PlayerDecision::where('player_id', $playerId)
                    ->where('intervention_type', 'break')
                    ->where('player_response', 'ignored')
                    ->where('created_at', '>', $startTime)
                    ->exists();

                if ($alreadyIgnored) {
                    // Skip trigger untuk menghindari spam
                } else {
                    return [
                        'intervention_id' => 'intv_l4_' . Str::random(8),
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
            }
        }

        // Intervensi Level 1-3 (Performance Check)

        // Ambil data keputusan pemain (15 data terakhir)
        $decisions = $this->repository->getRecentDecisions($playerId, 15);

        // Analisis kesalahan tiap kategori yang ditemukan
        $analysis = $this->repository->analyzeStreaks($decisions);

        $consecutiveErrors = $analysis['global_errors'];
        $categoryStreaks = $analysis['category_streaks'];

        // Early return jika tidak ada kesalahan signifikan
        if ($consecutiveErrors < 2 && empty($categoryStreaks)) {
            return null;
        }

        // Tentukan Level Intervensi
        $triggerLevel = 0;
        $targetCategory = null;

        // Resolve Category Context from Scenario ID
        $currentCategory = null;
        if ($scenarioId) {
            $currentCategory = $this->repository->getCategoryFromContentId($scenarioId);
        }

        // Prioritas 1: Level 3 (Blocking)
        // HANYA jika kategori scenario saat ini SAMA dengan kategori yang streak salah 4x
        if ($currentCategory && isset($categoryStreaks[$currentCategory]) && $categoryStreaks[$currentCategory] >= 4) {
            $triggerLevel = 3;
            $targetCategory = $currentCategory;
        }
        // Fallback: Jika tidak ada scenario context, cek kategori terburuk (Legacy/Random behavior)
        // User request: "berkaitan dengan scenario hanya dua (LV 2 & 3)"
        // Jadi jika ScenarioID NULL, kita TIDAK trigger level 2/3 (Contextual).

        // Prioritas 2: Level 2 (Warning)
        // HANYA jika kategori scenario saat ini SAMA dengan kategori yang streak salah 3x
        if ($triggerLevel === 0 && $currentCategory && isset($categoryStreaks[$currentCategory]) && $categoryStreaks[$currentCategory] >= 3) {
            $triggerLevel = 2;
            $targetCategory = $currentCategory;
        }

        // Prioritas 3: Level 1 (Reminder/Global)
        // GLOBAL CHECK: 2x salah berturut-turut (apapun kategorinya)
        // Level 1 category-agnostic
        if ($triggerLevel === 0 && $consecutiveErrors >= 2) {
            // Probabilitas 60%
            if (rand(1, 100) <= 60) {
                $triggerLevel = 1;
            }
        }

        if ($triggerLevel === 0) {
            return null;
        }

        // 4. Ambil Template
        $query = InterventionTemplate::where('level', $triggerLevel);

        if ($targetCategory) {
            $query->where('category', $targetCategory);
        } else {
            $query->whereNull('category');
        }

        $template = $query->inRandomOrder()->first();

        // Fallback ke general jika spesifik tidak ada
        if (!$template && $targetCategory) {
            $template = InterventionTemplate::where('level', $triggerLevel)
                ->whereNull('category')
                ->inRandomOrder()
                ->first();
        }

        if (!$template) {
            return null;
        }

        // 5. Format Response
        $message = $template->message_template;
        $title = $template->title_template;

        // Replace placeholders if any
        if ($targetCategory) {
            $catName = ucwords(str_replace(['_'], [' '], $targetCategory));
            $message = str_replace('{category}', $catName, $message);
        }

        return [
            'intervention_id' => 'intv_' . Str::random(8),
            'intervention_level' => $template->level,
            'title' => $title,
            'message' => $message,
            'is_mandatory' => (bool) $template->is_mandatory,
            'options' => $template->actions_template,
        ];
    }
}