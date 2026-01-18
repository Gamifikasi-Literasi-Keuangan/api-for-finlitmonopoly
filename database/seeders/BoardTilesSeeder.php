<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardTilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiles = [
            // ID 0 - Start
            [
                'tile_id' => 0,
                'position_index' => 0,
                'type' => 'special',
                'name' => 'Mulai',
                'category' => 'special',
                'linked_content' => json_encode(['description' => 'Titik awal permainan'])
            ],
            
            // ID 1 - Reksadana
            [
                'tile_id' => 1,
                'position_index' => 1,
                'type' => 'scenario',
                'name' => 'Reksadana',
                'category' => 'investasi',
                'linked_content' => json_encode(['type' => 'investment', 'risk_level' => 'medium'])
            ],
            
            // ID 2 - Pinjaman Teman
            [
                'tile_id' => 2,
                'position_index' => 2,
                'type' => 'scenario',
                'name' => 'Pinjaman Teman',
                'category' => 'utang',
                'linked_content' => json_encode(['scenario_category' => 'debt_management'])
            ],
            
            // ID 3 - Tabungan & Dana Darurat
            [
                'tile_id' => 3,
                'position_index' => 3,
                'type' => 'scenario',
                'name' => 'Tabungan',
                'category' => 'tabungan_dan_dana_darurat',
                'linked_content' => json_encode(['type' => 'savings', 'interest_rate' => 2.5])
            ],
            
            // ID 4 - Risiko
            [
                'tile_id' => 4,
                'position_index' => 4,
                'type' => 'risk',
                'name' => 'Risiko',
                'category' => 'card',
                'linked_content' => json_encode(['card_type' => 'risk'])
            ],
            
            // ID 5 - Makan
            [
                'tile_id' => 5,
                'position_index' => 5,
                'type' => 'scenario',
                'name' => 'Makan',
                'category' => 'anggaran',
                'linked_content' => json_encode(['scenario_category' => 'budgeting'])
            ],
            
            // ID 6 - Uang Bulanan
            [
                'tile_id' => 6,
                'position_index' => 6,
                'type' => 'scenario',
                'name' => 'Uang Bulanan',
                'category' => 'pendapatan',
                'linked_content' => json_encode(['scenario_category' => 'budgeting'])
            ],
            
            // ID 7 - Terjerat Utang
            [
                'tile_id' => 7,
                'position_index' => 7,
                'type' => 'special',
                'name' => 'Terjerat Utang',
                'category' => 'special',
                'linked_content' => json_encode(['card_type' => 'risk', 'severity' => 'high'])
            ],
            
            // ID 8 - Cryptocurrency
            [
                'tile_id' => 8,
                'position_index' => 8,
                'type' => 'scenario',
                'name' => 'Cryptocurrency',
                'category' => 'investasi',
                'linked_content' => json_encode(['type' => 'investment', 'risk_level' => 'high'])
            ],
            
            // ID 9 - Nongkrong
            [
                'tile_id' => 9,
                'position_index' => 9,
                'type' => 'scenario',
                'name' => 'Nongkrong',
                'category' => 'anggaran',
                'linked_content' => json_encode(['scenario_category' => 'lifestyle'])
            ],
            
            // ID 10 - Beasiswa
            [
                'tile_id' => 10,
                'position_index' => 10,
                'type' => 'scenario',
                'name' => 'Beasiswa',
                'category' => 'pendapatan',
                'linked_content' => json_encode(['card_type' => 'chance', 'benefit' => 'education'])
            ],
            
            // ID 11 - Aset Produktif
            [
                'tile_id' => 11,
                'position_index' => 11,
                'type' => 'scenario',
                'name' => 'Aset Produktif',
                'category' => 'tujuan_jangka_panjang',
                'linked_content' => json_encode(['type' => 'asset', 'generates_income' => true])
            ],
            
            // ID 12 - Paylater
            [
                'tile_id' => 12,
                'position_index' => 12,
                'type' => 'scenario',
                'name' => 'Paylater',
                'category' => 'utang',
                'linked_content' => json_encode(['scenario_category' => 'debt_management'])
            ],
            
            // ID 13 - Asuransi Barang/Harta
            [
                'tile_id' => 13,
                'position_index' => 13,
                'type' => 'scenario',
                'name' => 'Asuransi Barang/Harta',
                'category' => 'asuransi',
                'linked_content' => json_encode(['type' => 'insurance', 'coverage' => 'property'])
            ],
            
            // ID 14 - Dana Darurat Aman
            [
                'tile_id' => 14,
                'position_index' => 14,
                'type' => 'special',
                'name' => 'Dana Darurat Aman',
                'category' => 'special',
                'linked_content' => json_encode(['type' => 'emergency_fund', 'status' => 'secured'])
            ],
            
            // ID 15 - Deposito
            [
                'tile_id' => 15,
                'position_index' => 15,
                'type' => 'scenario',
                'name' => 'Deposito',
                'category' => 'tabungan_dan_dana_darurat',
                'linked_content' => json_encode(['type' => 'savings', 'interest_rate' => 5.0, 'term' => 12])
            ],
            
            // ID 16 - Pengalaman
            [
                'tile_id' => 16,
                'position_index' => 16,
                'type' => 'scenario',
                'name' => 'Pengalaman',
                'category' => 'tujuan_jangka_panjang',
                'linked_content' => json_encode(['scenario_category' => 'life_experience'])
            ],
            
            // ID 17 - Saham
            [
                'tile_id' => 17,
                'position_index' => 17,
                'type' => 'scenario',
                'name' => 'Saham',
                'category' => 'investasi',
                'linked_content' => json_encode(['type' => 'investment', 'risk_level' => 'high'])
            ],
            
            // ID 18 - Kuis Literasi
            [
                'tile_id' => 18,
                'position_index' => 18,
                'type' => 'quiz',
                'name' => 'Kuis Literasi',
                'category' => 'quiz',
                'linked_content' => json_encode(['card_type' => 'quiz'])
            ],
            
            // ID 19 - Asuransi Kendaraan
            [
                'tile_id' => 19,
                'position_index' => 19,
                'type' => 'scenario',
                'name' => 'Asuransi Kendaraan',
                'category' => 'asuransi',
                'linked_content' => json_encode(['type' => 'insurance', 'coverage' => 'vehicle'])
            ],
            
            // ID 20 - Freelance
            [
                'tile_id' => 20,
                'position_index' => 20,
                'type' => 'scenario',
                'name' => 'Freelance',
                'category' => 'pendapatan',
                'linked_content' => json_encode(['card_type' => 'chance', 'income_type' => 'side_hustle'])
            ],
            
            // ID 21 - Bangkrut
            [
                'tile_id' => 21,
                'position_index' => 21,
                'type' => 'special',
                'name' => 'Bangkrut',
                'category' => 'special',
                'linked_content' => json_encode(['card_type' => 'risk', 'severity' => 'critical'])
            ],
            
            // ID 22 - Pinjol
            [
                'tile_id' => 22,
                'position_index' => 22,
                'type' => 'scenario',
                'name' => 'Pinjol',
                'category' => 'utang',
                'linked_content' => json_encode(['card_type' => 'risk', 'severity' => 'high'])
            ],
            
            // ID 23 - Dana Darurat
            [
                'tile_id' => 23,
                'position_index' => 23,
                'type' => 'scenario',
                'name' => 'Dana Darurat',
                'category' => 'tabungan_dan_dana_darurat',
                'linked_content' => json_encode(['type' => 'emergency_fund'])
            ],
            
            // ID 24 - Transport
            [
                'tile_id' => 24,
                'position_index' => 24,
                'type' => 'scenario',
                'name' => 'Transport',
                'category' => 'anggaran',
                'linked_content' => json_encode(['scenario_category' => 'transportation'])
            ],
            
            // ID 25 - Kesempatan
            [
                'tile_id' => 25,
                'position_index' => 25,
                'type' => 'chance',
                'name' => 'Kesempatan',
                'category' => 'card',
                'linked_content' => json_encode(['card_type' => 'chance'])
            ],
            
            // ID 26 - Pendidikan
            [
                'tile_id' => 26,
                'position_index' => 26,
                'type' => 'scenario',
                'name' => 'Pendidikan',
                'category' => 'tujuan_jangka_panjang',
                'linked_content' => json_encode(['type' => 'education_investment'])
            ],
            
            // ID 27 - Asuransi Kesehatan
            [
                'tile_id' => 27,
                'position_index' => 27,
                'type' => 'scenario',
                'name' => 'Asuransi Kesehatan',
                'category' => 'asuransi',
                'linked_content' => json_encode(['type' => 'insurance', 'coverage' => 'health'])
            ],
        ];

        foreach ($tiles as $tile) {
            DB::table('boardtiles')->updateOrInsert(
                ['tile_id' => $tile['tile_id']],
                $tile
            );
        }

        $this->command->info('✅ Board tiles seeded successfully!');
        $this->command->info('📊 Total tiles: ' . count($tiles));
        $this->command->info('   - Scenarios: 19');
        $this->command->info('   - Risk: 4');
        $this->command->info('   - Chance: 3');
        $this->command->info('   - Quiz: 1');
    }
}
