<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    protected $table = 'PlayerProfile';
    protected $primaryKey = 'PlayerId'; // Sesuai SQL
    public $incrementing = false;
    protected $keyType = 'string';

    // Izinkan Laravel mengisi kolom-kolom ini
    protected $guarded = []; 

    // Beri tahu Laravel bahwa kolom ini adalah JSON
    protected $casts = [
        'onboarding_answers' => 'array',
        'traits' => 'array',
        'weak_areas' => 'array',
        'lifetime_scores' => 'array',
    ];

    /**
     * (Opsional tapi bagus) Relasi balik ke Player
     */
    public function player()
    {
        return $this->belongsTo(Player::class, 'PlayerId', 'PlayerId');
    }
}