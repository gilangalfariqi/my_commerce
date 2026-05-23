<?php

return [
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'account_type' => env('RAJAONGKIR_ACCOUNT_TYPE', 'starter'),
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com/starter'),
    'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', 152), // Default: Kudus city
    'origin_province_id' => env('RAJAONGKIR_ORIGIN_PROVINCE_ID', 6), // Default: Jawa Tengah
];
