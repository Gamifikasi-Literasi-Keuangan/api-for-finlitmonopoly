<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telemetry extends Model
{
    use HasFactory;

    protected $table = 'telemetry';
    public $timestamps = false;

    protected $fillable = [
        'sessionId',
        'playerId',
        'turn_id', 
        'tile_id',  // <--- TAMBAHKAN INI
        'action',
        'details',
        'metadata',
        'created_at'
    ];

    protected $casts = [
        'details' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];
}