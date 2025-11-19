<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerProfile;
// use App\Services\AIEngineService; // (Nanti Anda akan impor AI Engine di sini)

class PlayerService
{
    // protected $aiEngine;
    
    // public function __construct(AIEngineService $aiEngine)
    // {
    //     $this->aiEngine = $aiEngine;
    // }

    /**
     * Ambil data profil lengkap seorang pemain.
     */
    public function getPlayerProfile(string $playerId)
    {
        // Panggil Repository (Eloquent)
        // Muat relasi 'profile' yang sudah kita buat
        return Player::with('profile')->findOrFail($playerId);
    }

    /**
     * Proses jawaban profiling, panggil AI, dan simpan profil.
     */
    public function submitProfiling(string $playerId, array $answers)
    {
        // 1. Panggil AI Engine (Sistem Profiling Cepat dari Paten)
        // (Untuk sekarang, kita buat MOCK/dummy)
        // $cluster = $this->aiEngine->calculateCluster($answers);
        $mockCluster = 'Risk_Averse_Novice'; // HANYA CONTOH MOCK

        // 2. Siapkan data untuk disimpan
        $profileData = [
            'onboarding_answers' => $answers,
            'cluster' => $mockCluster,
            'level' => 'Beginner',
            // Inisialisasi skor JSON agar tidak null
            'lifetime_scores' => [
                'Utang' => 0,
                'Investasi' => 0,
                'Tabungan' => 0,
                // ...kategori lain...
            ]
        ];

        // 3. Panggil Repository (Eloquent)
        // updateOrCreate akan mencari PlayerId, 
        // jika ada di-update, jika tidak ada di-create.
        $profile = PlayerProfile::updateOrCreate(
            ['PlayerId' => $playerId], // Kunci pencarian
            $profileData                 // Data untuk di-update/create
        );

        return $profile;
    }
}