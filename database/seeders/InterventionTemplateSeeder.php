<?php

namespace Database\Seeders;

use App\Models\InterventionTemplate;
use Illuminate\Database\Seeder;

class InterventionTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InterventionTemplate::truncate();

        // ==========================================
        // LEVEL 1: GENTLE REMINDER (General)
        // ==========================================
        InterventionTemplate::create([
            'level' => 1,
            'risk_level' => 'low',
            'category' => null,
            'title_template' => 'Sedikit Reminder',
            'message_template' => 'Ingat: Dana darurat itu prioritas #1 sebelum investasi ya!',
            'heed_message' => 'Sip! Lanjut main dengan bijak ya.',
            'actions_template' => [
                ['id' => 'ignored', 'text' => 'Oke, Mengerti']
            ],
            'is_mandatory' => false
        ]);

        // Categories List
        $categories = [
            'pendapatan' => [
                'name' => 'Pendapatan',
                'l2_msg' => 'Kamu tampaknya kesulitan mengelola sumber pendapatan. Ingat untuk diversifikasi!',
                'l2_heed' => 'Pendapatan aktif dan pasif harus seimbang untuk keamanan finansial.',
                'l3_msg' => 'Pola keputusanmu berisiko mengganggu arus kas jangka panjang.',
                'l3_heed' => 'Cashflow adalah raja. Pastikan pemasukan lebih besar dari pengeluaran, bukan sebaliknya.'
            ],
            'anggaran' => [
                'name' => 'Anggaran',
                'l2_msg' => 'Pengeluaranmu mulai tidak terkontrol. Coba review budget plan kamu.',
                'l2_heed' => 'Gunakan metode 50/30/20: 50% Kebutuhan, 30% Keinginan, 20% Tabungan/Investasi.',
                'l3_msg' => 'Kamu berkali-kali over-budget. Ini bisa menyebabkan defisit serius.',
                'l3_heed' => 'Disiplin anggaran adalah fondasi kekayaan. Selalu catat dan evaluasi pengeluaran harianmu.'
            ],
            'tabungan_dan_dana_darurat' => [
                'name' => 'Tabungan & Dana Darurat',
                'l2_msg' => 'Saldo tabunganmu menipis. Jangan lupa sisihkan di awal, bukan sisa belanja.',
                'l2_heed' => 'Idealnya dana darurat adalah 6-12 kali pengeluaran bulanan.',
                'l3_msg' => 'Kamu mengabaikan pentingnya dana darurat. Ini sangat berisiko saat ada musibah.',
                'l3_heed' => 'Tanpa dana darurat, satu kejadian tak terduga bisa memaksamu berutang. Prioritaskan ini!'
            ],
            'utang' => [
                'name' => 'Utang',
                'l2_msg' => 'Rasio utangmu mulai mengkhawatirkan. Hati-hati dengan bunga berbunga.',
                'l2_heed' => 'Rumus aman: Cicilan utang maksimal 30% dari penghasilan bulanan.',
                'l3_msg' => 'Terlalu banyak mengambil utang konsumtif. Ini jalan cepat menuju kebangkrutan.',
                'l3_heed' => 'Utang produktif bisa menambah aset, tapi utang konsumtif hanya menggerogoti kekayaan masa depanmu.'
            ],
            'investasi' => [
                'name' => 'Investasi',
                'l2_msg' => 'Investasi tanpa analisis itu spekulasi. Kamu yakin dengan pilihan instrumenmu?',
                'l2_heed' => 'Pahami profil risikomu. Jangan letakkan semua telur dalam satu keranjang (Diversifikasi).',
                'l3_msg' => 'Keputusan investasimu terlalu impulsif dan berisiko tinggi.',
                'l3_heed' => 'High risk, high return, tapi juga high potential loss. Pelajari fundamental aset sebelum membeli.'
            ],
            'asuransi_dan_proteksi' => [
                'name' => 'Asuransi & Proteksi',
                'l2_msg' => 'Kamu belum terlindungi dengan baik. Risiko kesehatan atau aset bisa menguras hartamu.',
                'l2_heed' => 'Asuransi adalah pengaman kekayaan, bukan investasi. Fokus pada proteksi risiko terbesar.',
                'l3_msg' => 'Mengabaikan asuransi berarti kamu "self-insure" dengan seluruh tabunganmu.',
                'l3_heed' => 'Satu tagihan rumah sakit besar bisa menghabiskan tabungan bertahun-tahun. Proteksi diri sekarang.'
            ],
            'tujuan_jangka_panjang' => [
                'name' => 'Tujuan Jangka Panjang',
                'l2_msg' => 'Fokus jangka pendekmu mengorbankan tujuan masa depan. Pikirkan pendidikan/pensiun.',
                'l2_heed' => 'Kekuatan bunga majemuk butuh waktu. Mulai menabung untuk pensiun sedini mungkin.',
                'l3_msg' => 'Kamu terus menunda persiapan masa depan demi kesenangan saat ini.',
                'l3_heed' => 'Masa depan tidak akan membiayai dirinya sendiri. Inflasi akan menggerus daya beli jika tidak dilawan dengan investasi jangka panjang.'
            ]
        ];

        foreach ($categories as $key => $data) {
            // ==========================================
            // LEVEL 2: MODERATE WARNING
            // ==========================================
            InterventionTemplate::create([
                'level' => 2,
                'risk_level' => 'moderate',
                'category' => $key,
                'title_template' => "Peringatan Risiko: {$data['name']}",
                'message_template' => $data['l2_msg'],
                'heed_message' => $data['l2_heed'],
                'actions_template' => [
                    ['id' => 'heeded', 'text' => 'Lihat Penjelasan Singkat'],
                    ['id' => 'ignored', 'text' => 'Lanjut Tanpa Hint']
                ],
                'is_mandatory' => false
            ]);

            // ==========================================
            // LEVEL 3: BLOCKING INTERVENTION
            // ==========================================
            InterventionTemplate::create([
                'level' => 3,
                'risk_level' => 'high',
                'category' => $key,
                'title_template' => "PAUSE! Risiko {$data['name']} Tinggi",
                'message_template' => "{$data['l3_msg']} Sebelum lanjut, pahami konsep ini.",
                'heed_message' => $data['l3_heed'],
                'actions_template' => [
                    ['id' => 'heeded', 'text' => 'Lihat Penjelasan Lengkap'],
                    ['id' => 'ignored', 'text' => 'Saya Sudah Yakin, Lanjut']
                ],
                'is_mandatory' => true
            ]);
        }
    }
}
