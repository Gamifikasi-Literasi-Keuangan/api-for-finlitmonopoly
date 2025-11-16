<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    // Tentukan nama tabelnya (Laravel mungkin mengira 'sessions')
    protected $table = 'sessions';

    // Beri tahu Eloquent bahwa Primary Key BUKAN 'id'
    protected $primaryKey = 'sessionId';

    // Beri tahu Eloquent bahwa PK BUKAN auto-incrementing
    public $incrementing = false;

    // Beri tahu Eloquent bahwa PK adalah string
    protected $keyType = 'string';

    /**
     * Skema V5 kita memiliki 'created_at', 'started_at', 'ended_at'
     * Eloquent akan mengelola 'created_at' secara otomatis.
     * Kita matikan 'updated_at' bawaan Laravel.
     */
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; // Tidak ada 'updated_at'

    /**
     * Relasi: Satu Sesi memiliki BANYAK partisipan.
     */
    public function participants()
    {
        return $this->hasMany(ParticipatesIn::class, 'sessionId', 'sessionId');
    }

    /**
     * Relasi: Sesi ini dibuat oleh SATU host (pemain).
     */
    public function host()
    {
        return $this->belongsTo(Player::class, 'host_player_id', 'PlayerId');
    }
}