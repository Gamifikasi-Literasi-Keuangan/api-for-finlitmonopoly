<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizCard extends Model
{
    use HasFactory;

    // Tentukan tabel jika namanya beda (sesuai glk_db_final.sql)
    protected $table = 'quiz_cards'; 

    protected $primaryKey = 'id';

    /**
     * (WAJIB DITAMBAHKAN)
     * Mendefinisikan relasi ke opsi-opsi kuis.
     */
    public function options()
    {
        // 'quizId' adalah foreign key di tabel 'quiz_options'
        return $this->hasMany(QuizOption::class, 'quizId');
    }
}