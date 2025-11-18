<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilingResult extends Model
{
    protected $fillable = ['player_id', 'fuzzy_output', 'ann_output', 'final_class', 'recommended_focus'];
}