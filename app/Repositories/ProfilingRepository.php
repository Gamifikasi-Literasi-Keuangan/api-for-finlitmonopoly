<?php

namespace App\Repositories;

use App\Models\ProfilingAnswer;
use App\Models\ProfilingResult;
use App\Models\PlayerProfile;
use App\Models\ProfilingQuestion;
use App\Models\ProfilingQuestionOption;

class ProfilingRepository
{
    public function getProfilingQuestions()
    {
        return ProfilingQuestion::where('is_active', 1)
            ->with(['options', 'aspects'])
            ->orderBy('id')
            ->get();
    }

    public function getQuestionByCode($questionCode)
    {
        return ProfilingQuestion::where('question_code', $questionCode)
            ->with(['options', 'aspects'])
            ->first();
    }   

    
    public function getOptionByToken(string $questionCode, string $optionToken)
    {
        return ProfilingQuestionOption::where('option_token', $optionToken)
            ->whereHas('question', function ($q) use ($questionCode) {
                $q->where('question_code', $questionCode);
            })
            ->first();
    }

    public function getOptionScore($questionId, $optionCode)
    {
        $option = ProfilingQuestionOption::where('question_id', $questionId)
            ->where('option_code', $optionCode)
            ->first();

        return $option ? $option->score : null;
    }

    public function saveAnswer($playerId, $questionId, $answer)
    {
        return ProfilingAnswer::updateOrCreate(
            [
                'player_id' => $playerId,
                'question_id' => $questionId,
            ],
            [
                'answer' => $answer,
            ]
        );
    }

    public function getAnswersByPlayerId($playerId)
    {
        return ProfilingAnswer::where('player_id', $playerId)
            ->with('question.options')
            ->get();
    }

    public function saveResult($playerId, $fuzzy, $ann, $finalClass, $recommendation)
    {
        return ProfilingResult::create([
            'player_id' => $playerId,
            'fuzzy_output' => json_encode($fuzzy),
            'ann_output' => json_encode($ann),
            'final_class' => $finalClass,
            'recommended_focus' => json_encode($recommendation),
        ]);
    }

    public function updateProfile($playerId, $fuzzy, $ann, $finalClass)
    {
        return PlayerProfile::updateOrCreate(
            ['PlayerId' => $playerId],
            [
                'cluster' => $finalClass,
                'fuzzy_scores' => json_encode($fuzzy),
                'ann_probabilities' => json_encode($ann),
                'last_updated' => now(),
            ]
        );
    }
}
