<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardTile extends Model
{
    protected $table = 'boardtiles';
    protected $primaryKey = 'tile_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'tile_id',
        'position_index',
        'type',
        'name',
        'category',
        'linked_content'
    ];
}