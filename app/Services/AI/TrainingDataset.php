<?php

namespace App\Services\AI;

/**
 * Unified Training Dataset for ANN Model
 * 
 * Dataset ini digunakan oleh:
 * - ANNService (untuk production profiling)
 * - ANNController (untuk API testing)
 * 
 * Format: snake_case keys
 * Total: 25 samples
 * Distribution: 
 * - Financial Novice: 5 samples
 * - Financial Explorer: 6 samples
 * - Foundation Builder: 7 samples
 * - Financial Architect: 5 samples
 * - Financial Sage: 2 samples
 */
class TrainingDataset
{
    /**
     * Get unified training dataset
     * 
     * @return array
     */
    public static function get()
    {
        return [
            // Financial Novice (5 samples)
            ['pendapatan' => 'Sangat Rendah', 'anggaran' => 'Sangat Rendah', 'tabungan_dan_dana_darurat' => 'Sangat Rendah', 'utang' => 'Sangat Tinggi', 'investasi' => 'Tidak Ada', 'asuransi_dan_proteksi' => 'Sangat Rendah', 'tujuan_jangka_panjang' => 'Sangat Rendah', 'cluster' => 'Financial Novice'],
            ['pendapatan' => 'Rendah', 'anggaran' => 'Sangat Rendah', 'tabungan_dan_dana_darurat' => 'Sangat Rendah', 'utang' => 'Tinggi', 'investasi' => 'Tidak Ada', 'asuransi_dan_proteksi' => 'Tidak Ada', 'tujuan_jangka_panjang' => 'Sangat Rendah', 'cluster' => 'Financial Novice'],
            ['pendapatan' => 'Sangat Rendah', 'anggaran' => 'Rendah', 'tabungan_dan_dana_darurat' => 'Rendah', 'utang' => 'Sangat Tinggi', 'investasi' => 'Tidak Ada', 'asuransi_dan_proteksi' => 'Rendah', 'tujuan_jangka_panjang' => 'Sangat Rendah', 'cluster' => 'Financial Novice'],
            ['pendapatan' => 'Rendah', 'anggaran' => 'Rendah', 'tabungan_dan_dana_darurat' => 'Sangat Rendah', 'utang' => 'Tinggi', 'investasi' => 'Tidak Ada', 'asuransi_dan_proteksi' => 'Sangat Rendah', 'tujuan_jangka_panjang' => 'Tidak Ada', 'cluster' => 'Financial Novice'],
            ['pendapatan' => 'Rendah', 'anggaran' => 'Sangat Rendah', 'tabungan_dan_dana_darurat' => 'Rendah', 'utang' => 'Sangat Tinggi', 'investasi' => 'Tidak Ada', 'asuransi_dan_proteksi' => 'Sangat Rendah', 'tujuan_jangka_panjang' => 'Sangat Rendah', 'cluster' => 'Financial Novice'],
            
            // Financial Explorer (6 samples)
            ['pendapatan' => 'Rendah', 'anggaran' => 'Sedang', 'tabungan_dan_dana_darurat' => 'Rendah', 'utang' => 'Sedang', 'investasi' => 'Sangat Rendah', 'asuransi_dan_proteksi' => 'Rendah', 'tujuan_jangka_panjang' => 'Rendah', 'cluster' => 'Financial Explorer'],
            ['pendapatan' => 'Sedang', 'anggaran' => 'Rendah', 'tabungan_dan_dana_darurat' => 'Rendah', 'utang' => 'Sedang', 'investasi' => 'Sangat Rendah', 'asuransi_dan_proteksi' => 'Rendah', 'tujuan_jangka_panjang' => 'Rendah', 'cluster' => 'Financial Explorer'],
            ['pendapatan' => 'Rendah', 'anggaran' => 'Sedang', 'tabungan_dan_dana_darurat' => 'Sedang', 'utang' => 'Tinggi', 'investasi' => 'Rendah', 'asuransi_dan_proteksi' => 'Rendah', 'tujuan_jangka_panjang' => 'Rendah', 'cluster' => 'Financial Explorer'],
            ['pendapatan' => 'Sedang', 'anggaran' => 'Sedang', 'tabungan_dan_dana_darurat' => 'Rendah', 'utang' => 'Sedang', 'investasi' => 'Sangat Rendah', 'asuransi_dan_proteksi' => 'Rendah', 'tujuan_jangka_panjang' => 'Sangat Rendah', 'cluster' => 'Financial Explorer'],
            ['pendapatan' => 'Rendah', 'anggaran' => 'Rendah', 'tabungan_dan_dana_darurat' => 'Sedang', 'utang' => 'Sedang', 'investasi' => 'Rendah', 'asuransi_dan_proteksi' => 'Sangat Rendah', 'tujuan_jangka_panjang' => 'Rendah', 'cluster' => 'Financial Explorer'],
            ['pendapatan' => 'Sedang', 'anggaran' => 'Sedang', 'tabungan_dan_dana_darurat' => 'Rendah', 'utang' => 'Tinggi', 'investasi' => 'Sangat Rendah', 'asuransi_dan_proteksi' => 'Rendah', 'tujuan_jangka_panjang' => 'Rendah', 'cluster' => 'Financial Explorer'],
            
            // Foundation Builder (7 samples)
            ['pendapatan' => 'Sedang', 'anggaran' => 'Tinggi', 'tabungan_dan_dana_darurat' => 'Tinggi', 'utang' => 'Rendah', 'investasi' => 'Sedang', 'asuransi_dan_proteksi' => 'Sedang', 'tujuan_jangka_panjang' => 'Sedang', 'cluster' => 'Foundation Builder'],
            ['pendapatan' => 'Tinggi', 'anggaran' => 'Sedang', 'tabungan_dan_dana_darurat' => 'Tinggi', 'utang' => 'Rendah', 'investasi' => 'Sedang', 'asuransi_dan_proteksi' => 'Sedang', 'tujuan_jangka_panjang' => 'Sedang', 'cluster' => 'Foundation Builder'],
            ['pendapatan' => 'Sedang', 'anggaran' => 'Tinggi', 'tabungan_dan_dana_darurat' => 'Tinggi', 'utang' => 'Rendah', 'investasi' => 'Sedang', 'asuransi_dan_proteksi' => 'Sedang', 'tujuan_jangka_panjang' => 'Tinggi', 'cluster' => 'Foundation Builder'],
            ['pendapatan' => 'Tinggi', 'anggaran' => 'Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Sedang', 'asuransi_dan_proteksi' => 'Tinggi', 'tujuan_jangka_panjang' => 'Sedang', 'cluster' => 'Foundation Builder'],
            ['pendapatan' => 'Sedang', 'anggaran' => 'Sedang', 'tabungan_dan_dana_darurat' => 'Tinggi', 'utang' => 'Rendah', 'investasi' => 'Tinggi', 'asuransi_dan_proteksi' => 'Sedang', 'tujuan_jangka_panjang' => 'Sedang', 'cluster' => 'Foundation Builder'],
            ['pendapatan' => 'Tinggi', 'anggaran' => 'Tinggi', 'tabungan_dan_dana_darurat' => 'Tinggi', 'utang' => 'Rendah', 'investasi' => 'Sedang', 'asuransi_dan_proteksi' => 'Sedang', 'tujuan_jangka_panjang' => 'Tinggi', 'cluster' => 'Foundation Builder'],
            ['pendapatan' => 'Sedang', 'anggaran' => 'Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Rendah', 'investasi' => 'Sedang', 'asuransi_dan_proteksi' => 'Sedang', 'tujuan_jangka_panjang' => 'Sedang', 'cluster' => 'Foundation Builder'],
            
            // Financial Architect (5 samples)
            ['pendapatan' => 'Tinggi', 'anggaran' => 'Sangat Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Tinggi', 'asuransi_dan_proteksi' => 'Tinggi', 'tujuan_jangka_panjang' => 'Tinggi', 'cluster' => 'Financial Architect'],
            ['pendapatan' => 'Sangat Tinggi', 'anggaran' => 'Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Tinggi', 'asuransi_dan_proteksi' => 'Tinggi', 'tujuan_jangka_panjang' => 'Sangat Tinggi', 'cluster' => 'Financial Architect'],
            ['pendapatan' => 'Tinggi', 'anggaran' => 'Sangat Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Sangat Tinggi', 'asuransi_dan_proteksi' => 'Tinggi', 'tujuan_jangka_panjang' => 'Sangat Tinggi', 'cluster' => 'Financial Architect'],
            ['pendapatan' => 'Sangat Tinggi', 'anggaran' => 'Sangat Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Tinggi', 'asuransi_dan_proteksi' => 'Sangat Tinggi', 'tujuan_jangka_panjang' => 'Tinggi', 'cluster' => 'Financial Architect'],
            ['pendapatan' => 'Tinggi', 'anggaran' => 'Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Sangat Tinggi', 'asuransi_dan_proteksi' => 'Tinggi', 'tujuan_jangka_panjang' => 'Sangat Tinggi', 'cluster' => 'Financial Architect'],
            
            // Financial Sage (2 samples)
            ['pendapatan' => 'Sangat Tinggi', 'anggaran' => 'Sangat Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Sangat Tinggi', 'asuransi_dan_proteksi' => 'Sangat Tinggi', 'tujuan_jangka_panjang' => 'Sangat Tinggi', 'cluster' => 'Financial Sage'],
            ['pendapatan' => 'Sangat Tinggi', 'anggaran' => 'Sangat Tinggi', 'tabungan_dan_dana_darurat' => 'Sangat Tinggi', 'utang' => 'Sangat Rendah', 'investasi' => 'Sangat Tinggi', 'asuransi_dan_proteksi' => 'Tinggi', 'tujuan_jangka_panjang' => 'Sangat Tinggi', 'cluster' => 'Financial Sage'],
        ];
    }
    
    /**
     * Get dataset with PascalCase keys (for backward compatibility with ANNController)
     * 
     * @return array
     */
    public static function getForController()
    {
        $data = self::get();
        $converted = [];
        
        foreach ($data as $row) {
            $converted[] = [
                'Pendapatan' => $row['pendapatan'],
                'Anggaran' => $row['anggaran'],
                'Tabungan & Dana Darurat' => $row['tabungan_dan_dana_darurat'],
                'Utang' => $row['utang'],
                'Investasi' => $row['investasi'],
                'Asuransi' => $row['asuransi_dan_proteksi'],
                'Tujuan Jangka Panjang' => $row['tujuan_jangka_panjang'],
                'Kelas Ekonomi (Arsitekip)' => $row['cluster']
            ];
        }
        
        return $converted;
    }
}
