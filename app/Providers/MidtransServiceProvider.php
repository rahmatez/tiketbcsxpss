<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Midtrans\Config;

class MidtransServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */    public function boot(): void
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = !config('midtrans.sandbox');
        
        // Pastikan konfigurasi opsional memiliki nilai default
        Config::$isSanitized = config('midtrans.sanitize', true);
        Config::$is3ds = config('midtrans.enable_3d_secure', true);
        
        // Gunakan null sebagai default untuk URL override
        Config::$appendNotifUrl = null;
        Config::$overrideNotifUrl = config('midtrans.notification_url', null);
    }
}
