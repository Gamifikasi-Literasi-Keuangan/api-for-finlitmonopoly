<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndSessionRequest extends FormRequest
{
    public function rules(): array {
    return [
        'sessionId' => 'required|string|exists:sessions,sessionId',
        // (Asumsi di-call oleh admin, jadi perlu validasi auth)
    ];
    }
}
