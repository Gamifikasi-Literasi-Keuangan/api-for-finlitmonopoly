<?php

namespace App\Services;

use App\Models\BoardTile;

class BoardService
{
    /**
     * Ambil detail satu petak (tile) berdasarkan ID-nya.
     * Ini akan menangani 'Risk', 'Chance', 'Property', 'Scenario', dll.
     */
    public function getTileDetails(int $id)
    {
        // Panggil Repository (Eloquent) -> Panggil DB
        // findOrFail() akan otomatis melempar error 404
        $tile = BoardTile::findOrFail($id);
        
        // 'details' di SQL Anda adalah JSON, Laravel otomatis
        // akan meng-decode nya menjadi array/objek.
        return $tile;
    }
}
