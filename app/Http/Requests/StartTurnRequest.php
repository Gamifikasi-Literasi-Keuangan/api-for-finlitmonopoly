<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartTurnRequest extends FormRequest
{
    public function rules(): array {
    return [
        'playerId' => 'required|string|exists:players,PlayerId'
    ];
    }
}
