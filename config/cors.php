<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Di sini Anda bisa menentukan origin (domain) mana yang diizinkan
    | untuk membuat request ke API Anda. Anda bisa menggunakan '*' untuk
    | mengizinkan semua, tapi lebih aman mendaftarkannya satu per satu.
    |
    */

    'allowed_origins' => [
        'http://localhost:3000', // <-- Untuk development React/Vue
        'http://127.0.0.1:5500', // <-- Untuk development file HTML biasa
        'https://project-finlitmon-demo-api.netlify.app' // <-- Nanti saat sudah production
    ],

    // ATAU (jika ingin gampang untuk tes, tapi tidak aman):
    // 'allowed_origins' => ['*'], 

    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,

];