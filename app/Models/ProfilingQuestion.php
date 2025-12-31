<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilingQuestion extends Model
{
    protected $table = 'profiling_questions';

    protected $fillable = [
        'question_code',
        'question_text',
        'max_score',
        'is_active',
    ];

    public function options()
    {
        return $this->hasMany(ProfilingQuestionOption::class, 'question_id');
    }

    public function aspects()
    {
        return $this->belongsToMany(
            FinancialAspect::class,
            'profiling_question_aspects',
            'question_id',
            'aspect_id'
        );
    }
}
