<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    protected $table = 'playerprofile';
    protected $primaryKey = 'PlayerId';
    protected $fillable = [
        'PlayerId',
        'onboarding_answers',
        'cluster',
        'level',
        'traits',
        'weak_areas',
        'recommended_focus',
        'fuzzy_scores',
        'lifetime_scores',
        'decision_history',
        'behavior_pattern',
        'confidence_level',
        'fuzzy_scores',
        'ann_probabilities',
        'last_recommendation',
        'last_updated',
        'thresholds',
        'created_at',
        'updated_at'
    ];
}
