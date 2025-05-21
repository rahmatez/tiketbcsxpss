<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials and configurations for Midtrans.
    | 
    */

    // Untuk Sandbox/Development
    'sandbox' => env('MIDTRANS_SANDBOX', true),
    
    // Kunci API Midtrans
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    
    // Pengaturan untuk halaman pembayaran Snap
    'snap_redirect' => env('MIDTRANS_SNAP_REDIRECT', false),
    
    // Sanitasi input dan keamanan 3D Secure
    'sanitize' => env('MIDTRANS_SANITIZE', true),
    'enable_3d_secure' => env('MIDTRANS_3D_SECURE', true),
    
    // URL callback untuk notifikasi pembayaran dari Midtrans
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', ''),
    
    // URL redirect setelah pembayaran selesai
    'finish_url' => env('MIDTRANS_FINISH_URL', ''),
    'unfinish_url' => env('MIDTRANS_UNFINISH_URL', ''),
    'error_url' => env('MIDTRANS_ERROR_URL', ''),
    
    // Konfigurasi Default
    'enable_3d_secure' => env('MIDTRANS_3D_SECURE', true),
    'sanitize' => true,
    
    // Pengaturan lainnya
    'append_notif_url' => true,
    'overrideNotifUrl' => true,
    'enable_payments' => [],
    'bank_transfer_options' => [],
];
