<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartTurnRequest extends FormRequest
{
    public function rules(): array {
    return [
        'sessionId' => 'required|string|exists:sessions,sessionId',
        'playerId' => 'required|string|exists:players,PlayerId',
    ];
    }
}
