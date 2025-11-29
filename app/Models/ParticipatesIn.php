<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipatesIn extends Model
{
    use HasFactory;

    // Beri tahu Eloquent nama tabel yang benar
    protected $table = 'participatesin';
    protected $fillable = ['sessionId', 'playerId', 'position', 'score', 'connection_status', 'is_ready', 'joined_at'];
    /**
     * Relasi (Langkah 6 di diagram): Baris ini milik SATU Player.
     * Ini adalah kunci untuk JOIN kita!
     */
    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId', 'PlayerId');
    }
    public function session()
    {
        return $this->belongsTo(GameSession::class, 'sessionId', 'sessionId');
    }   
}
