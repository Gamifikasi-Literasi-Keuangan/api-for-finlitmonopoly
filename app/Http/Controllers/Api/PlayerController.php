<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * API 32: Mengambil Daftar Semua Pemain
     * Endpoint: GET /players
     */
    public function index()
    {
        // Kita ambil kolom yang penting saja untuk ditampilkan di list
        // Diurutkan dari yang paling baru mendaftar
        $players = Player::select('PlayerId', 'name', 'gamesPlayed', 'createdAt')
            ->orderBy('createdAt', 'desc')
            ->get();

        return response()->json($players);
    }
}