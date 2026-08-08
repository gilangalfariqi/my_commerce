<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | Nomor WhatsApp toko untuk fitur direct chat & checkout.
    | Format: kode negara + nomor tanpa tanda + (contoh: 6281234567890).
    | Prioritas utama diambil dari DB Settings (store_whatsapp key).
    | Config ini sebagai fallback jika DB belum dikonfigurasi.
    |
    */
    'whatsapp' => [
        'number'           => env('WHATSAPP_NUMBER', ''),
        'checkout_number'  => env('WHATSAPP_CHECKOUT_NUMBER', env('WHATSAPP_NUMBER', '')),
    ],

];
