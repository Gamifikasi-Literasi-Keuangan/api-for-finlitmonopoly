<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipatesIn extends Model
{
    use HasFactory;

    protected $table = 'participatesin';
    protected $fillable = ['sessionId', 'playerId', 'position', 'score', 'rank', 'player_order', 'connection_status', 'is_ready', 'joined_at', 'on_break', 'last_break_end_at'];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerId', 'PlayerId');
    }
    public function session()
    {
        return $this->belongsTo(GameSession::class, 'sessionId', 'sessionId');
    }
}
