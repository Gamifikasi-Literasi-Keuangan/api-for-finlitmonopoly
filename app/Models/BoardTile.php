<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardTile extends Model
{
    protected $table = 'boardtiles';
    protected $primaryKey = 'tileId';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'tileId',
        'sessionId',
        'position',
        'type',
        'ownerPlayerId',
        'created_at',
        'updated_at'
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
}