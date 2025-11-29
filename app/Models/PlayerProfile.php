<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerProfile extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang benar
    protected $table = 'playerprofile';

    // Beri tahu Eloquent bahwa Primary Key BUKAN 'id'
    protected $primaryKey = 'PlayerId';

    // Beri tahu Eloquent bahwa PK BUKAN auto-incrementing
    public $incrementing = false;

    // Beri tahu Eloquent bahwa PK adalah string
    protected $keyType = 'string';

    // Atur timestamp (kita punya 'last_updated')
    public $timestamps = true;
    const CREATED_AT = null; // Tidak ada 'created_at'
    const UPDATED_AT = 'last_updated';
    
    protected $fillable = [
        'PlayerId',
        'onboarding_answers',
        'cluster',
        'level',
        'traits',
        'weak_areas',
        'recommended_focus',
        'fuzzy_scores',
        'lifetime_scores',
        'decision_history',
        'behavior_pattern',
        'confidence_level',
        'fuzzy_scores',
        'ann_probabilities',
        'last_recommendation',
        'last_updated',
        'thresholds',
        'created_at',
        'updated_at'
    ];
    
    /**
     * Otomatis cast kolom JSON (MySQL)
     */
    protected $casts = [
        'thresholds' => 'array',
        'lifetime_scores' => 'array',
        'onboarding_answers' => 'array',
        'traits' => 'array',
        'weak_areas' => 'array',
    ];

    /**
     * Relasi: Profil ini milik SATU Player
     */
    public function player()
    {
        return $this->belongsTo(Player::class, 'PlayerId', 'PlayerId');
    }
}
