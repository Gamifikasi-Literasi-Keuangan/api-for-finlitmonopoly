<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'players';
    protected $primaryKey = 'PlayerId'; // Sesuai SQL
    public $incrementing = false;     // Karena PlayerId adalah VARCHAR/string
    protected $keyType = 'string';    // Sesuai SQL

    /**
     * (WAJIB DITAMBAHKAN)
     * Mendefinisikan relasi ke profil AI pemain.
     */
    public function profile()
    {
        // 'PlayerId' adalah foreign key di tabel 'PlayerProfile'
        // dan 'PlayerId' adalah local key di tabel 'players'
        return $this->hasOne(PlayerProfile::class, 'PlayerId', 'PlayerId');
    }

        // ... di dalam class Player ...
    public function gameSessions() {
        return $this->belongsToMany(Session::class, 'ParticipatesIn', 'playerId', 'sessionId')
                    ->withPivot('score', 'position', 'color', 'status');
    }
}
