<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovePlayerRequest extends FormRequest
{
    public function rules(): array {
    return [
        'sessionId' => 'required|string|exists:sessions,sessionId',
        'playerId' => 'required|string|exists:players,PlayerId',
        'from_tile' => 'required|integer|min:0|max:39',
        'steps' => 'required|integer|min:1|max:12',
    ];
    }
}