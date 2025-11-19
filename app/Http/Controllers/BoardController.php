<?php

namespace App\Http\Controllers;

use App\Services\BoardService; // <-- Impor Service
use Illuminate\Http\Request;

class BoardController extends Controller
{
    // Suntikkan (inject) Service
    public function __construct(protected BoardService $boardService)
    {
    }

    /**
     * Implementasi: GET /tile/{id}
     */
    public function getTile($id)
    {
        // 1. Delegasikan ke Service
        $tileData = $this->boardService->getTileDetails($id);
        
        // 2. Kembalikan View (JSON)
        return response()->json($tileData);
    }
}
