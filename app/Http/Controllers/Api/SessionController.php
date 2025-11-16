<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session; // <-- Impor Model Session
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Mengambil semua sesi yang sudah selesai
     * Ini adalah API baru untuk mengisi daftar "List Game"
     */
    public function getCompletedSessions(Request $request)
    {
        $sessions = Session::where('status', 'completed')
            ->orderBy('ended_at', 'DESC')
            ->select('sessionId', 'ended_at', 'host_player_id') // Ambil data yang relevan
            ->get();
            
        return response()->json($sessions);
    }
}