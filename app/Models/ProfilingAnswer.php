<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilingAnswer extends Model
{
    protected $table = 'profiling_answers';

    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'question_id',
        'answer',
    ];

    public function question()
    {
        return $this->belongsTo(
            ProfilingQuestion::class,
            'question_id',
            'id'
        );
    }
}
