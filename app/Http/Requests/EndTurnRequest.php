<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndTurnRequest extends FormRequest
{
    public function rules(): array {
    return [
        'sessionId' => 'required|string|exists:sessions,sessionId',
        'playerId' => 'required|string|exists:players,PlayerId',
        'turn_id' => 'required|string|exists:turns,turn_id',
        'actions' => 'sometimes|array', // 'actions' opsional, tapi jika ada harus array
    ];
    }
}