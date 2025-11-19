<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'sessions';
    protected $primaryKey = 'sessionId';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = []; // Izinkan mass assignment


    // 1. Aktifkan timestamps agar Laravel otomatis mengisi 'created_at'
    public $timestamps = true;

    // 2. Matikan 'updated_at' karena kolom ini TIDAK ADA di tabel Anda
    const UPDATED_AT = null;

    // 3. Pastikan nama kolom created_at sesuai (defaultnya memang 'created_at', tapi kita tegaskan)
    const CREATED_AT = 'started_at';

    // Relasi ke pemain yang sedang giliran
    public function currentPlayer() {
        return $this->belongsTo(Player::class, 'current_player_id', 'PlayerId');
    }

    // Relasi ke semua pemain di sesi ini (via tabel pivot)
    public function players() {
        return $this->belongsToMany(Player::class, 'ParticipatesIn', 'sessionId', 'playerId')
                    ->withPivot('score', 'position', 'color', 'status');
    }

    // Relasi ke semua giliran (turns) dalam sesi ini
    public function turns() {
        return $this->hasMany(Turn::class, 'session_id', 'sessionId');
    }
}