<?php

namespace App\Http\Controllers;

use App\Services\CardService; // <-- Impor Service
use Illuminate\Http\Request;

class CardController extends Controller
{
    // Suntikkan (inject) CardService lewat constructor
    public function __construct(protected CardService $cardService)
    {
    }

    /**
     * Implementasi Diagram No. 17: GET /card/quiz/{id}
     */
    public function getQuizCard($id)
    {
        // 1. Delegasikan ke Service
        $card = $this->cardService->getQuizCard($id);

        // 2. Kembalikan View (JSON)
        // Respons JSON akan otomatis menyertakan array 'options'
        return response()->json($card);
    }
}