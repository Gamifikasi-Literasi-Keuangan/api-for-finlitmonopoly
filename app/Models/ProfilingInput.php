<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilingInput extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['player_id', 'feature', 'created_at'];
}
