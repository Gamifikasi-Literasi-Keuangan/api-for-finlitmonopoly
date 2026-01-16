<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $table = 'game_sessions';
    protected $primaryKey = 'sessionId';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $fillable = ['sessionId', 'host_player_id',  'max_players', 'max_turns', 'status', 'current_turn', 'created_at'];
    public $timestamps = false;

    public function currentPlayer() {
        return $this->belongsTo(Player::class, 'current_player_id', 'PlayerId');
    }

    public function participants() {
        return $this->hasMany(ParticipatesIn::class, 'sessionId', 'sessionId');
    }

    public function turns() {
        return $this->hasMany(Turn::class, 'session_id', 'sessionId');
    }
}