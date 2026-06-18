<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Provider Configuration
    |--------------------------------------------------------------------------
    | Configure the active WhatsApp provider for sending reminders.
    | Supported: "log", "fonnte", "wablas", "meta"
    */

    'provider' => env('WHATSAPP_PROVIDER', 'log'),

    'providers' => [

        'log' => [
            'driver' => 'log',
        ],

        'fonnte' => [
            'driver' => 'fonnte',
            'token' => env('WHATSAPP_FONNTE_TOKEN', ''),
            'url' => env('WHATSAPP_FONNTE_URL', 'https://api.fonnte.com/send'),
            'timeout' => 30,
        ],

        'wablas' => [
            'driver' => 'wablas',
            'token' => env('WHATSAPP_WABLAS_TOKEN', ''),
            'url' => env('WHATSAPP_WABLAS_URL', 'https://pati.wablas.com/api/send-message'),
            'timeout' => 30,
        ],

        'meta' => [
            'driver' => 'meta',
            'token' => env('WHATSAPP_META_TOKEN', ''),
            'phone_number_id' => env('WHATSAPP_META_PHONE_ID', ''),
            'url' => env('WHATSAPP_META_URL', 'https://graph.facebook.com/v18.0'),
            'timeout' => 30,
        ],

        'kirimdev' => [
            'driver' => 'kirimdev',
            'api_key' => env('WHATSAPP_KIRIMDEV_API_KEY', ''),
            'phone_id' => env('WHATSAPP_KIRIMDEV_PHONE_ID', ''),
            'url' => env('WHATSAPP_KIRIMDEV_URL', 'https://api.kirimdev.com/v1'),
            'timeout' => 30,
        ],

        'selfhosted' => [
            'driver' => 'selfhosted',
            'url' => env('WHATSAPP_SELFHOSTED_URL', 'http://localhost:3000'),
            'timeout' => 30,
        ],

    ],
];
