<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilingQuestionOption extends Model
{
    protected $table = 'profiling_question_options';

    protected $fillable = [
        'question_id',
        'option_code',
        'label',
        'score',
    ];

    protected $guarded = [
        'option_token',
    ];

    public function question()
    {
        return $this->belongsTo(ProfilingQuestion::class, 'question_id');
    }

    // Membuat UUID otomatis saat membuat opsi baru
    protected static function booted()
    {
        static::creating(function ($option) {
            if (empty($option->option_token)) {
                $option->option_token = (string) Str::uuid();
            }
        });
    }
}
