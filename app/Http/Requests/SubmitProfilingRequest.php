<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitProfilingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Izinkan semua orang menggunakannya (atur false jika perlu auth)
    }

    public function rules(): array
    {
        return [
            'player_id' => 'required|string|exists:players,PlayerId',
            'answers' => 'required|array|size:3', // Harus array, ukurannya 3
            'answers.*' => 'required|string', // Setiap elemen array harus string
        ];
    }
}