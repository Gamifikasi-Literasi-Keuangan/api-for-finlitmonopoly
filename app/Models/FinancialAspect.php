<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialAspect extends Model
{
    protected $table = 'financial_aspects';

    public $timestamps = false;

    protected $fillable = [
        'aspect_key',
        'display_name',
    ];

    public function questions()
    {
        return $this->belongsToMany(
            ProfilingQuestion::class,
            'profiling_question_aspects',
            'aspect_id',
            'question_id'
        );
    }
}
