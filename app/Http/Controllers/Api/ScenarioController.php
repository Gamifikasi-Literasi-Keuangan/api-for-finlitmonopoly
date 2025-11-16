<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario; // <-- Impor Model
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    /**
     * FUNGSI BARU: Mengambil DAFTAR SEMUA skenario.
     * Ini akan menjadi endpoint: GET /scenarios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Untuk "List View", kita tidak perlu semua data (seperti 'question' atau 'cluster_relevance')
        // Kita hanya ambil kolom yang penting untuk ditampilkan di tabel admin.
        $scenarios = Scenario::select(
            'id', 
            'title', 
            'category', 
            'difficulty', 
            'created_at'
        )
        ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
        ->get();

        // Kembalikan sebagai koleksi (array) JSON
        return response()->json($scenarios);
    }

    /**
     * FUNGSI LAMA (API 19): Mengambil SATU skenario.
     * Ini adalah endpoint: GET /scenario/{scenario}
     *
     * @param  \App\Models\Scenario  $scenario
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Scenario $scenario)
    {
        // Kita load relasi 'options'
        $scenario->load('options');

        // Kembalikan satu objek JSON
        return response()->json($scenario);
    }
}