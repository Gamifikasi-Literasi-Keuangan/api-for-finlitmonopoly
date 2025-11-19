<?php

namespace App\Services;

// Impor model yang kita butuhkan
use App\Models\QuizCard;

class CardService
{
    /**
     * Ambil satu kartu kuis DENGAN opsi-opsinya.
     */
    public function getQuizCard(int $id)
    {
        // Panggil Repository (Eloquent) dan muat relasi 'options'
        // 'with('options')' akan otomatis menjalankan relasi yang kita buat
        // findOrFail() akan otomatis melempar error 404 jika tidak ditemukan
        return QuizCard::with('options')->findOrFail($id);
    }
}