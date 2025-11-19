<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $table = 'turns';
    protected $primaryKey = 'turn_id';
    public $incrementing = false; 
    protected $keyType = 'string';
    protected $guarded = [];
    
    // 1. Aktifkan timestamps agar Laravel otomatis mengisi 'created_at'
    public $timestamps = true;

    // 2. Matikan 'updated_at' karena kolom ini TIDAK ADA di tabel Anda
    const UPDATED_AT = null;

    // 3. Pastikan nama kolom created_at sesuai (defaultnya memang 'created_at', tapi kita tegaskan)
    const CREATED_AT = 'started_at';

    // ---------------------------------------

    public function player() {
        return $this->belongsTo(Player::class, 'player_id', 'PlayerId');
    }
    
    public function session() {
        return $this->belongsTo(Session::class, 'session_id', 'sessionId');
    }
}