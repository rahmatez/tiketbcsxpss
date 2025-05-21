<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\NotificationService;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan pengingat pertandingan setiap hari pada pukul 8 pagi
        // Akan mengirim notifikasi untuk pertandingan yang akan terjadi 3 hari lagi
        $schedule->command('app:create-match-reminders 3')->dailyAt('08:00');
        
        // Kirim juga pengingat untuk pertandingan besok (1 hari sebelumnya)
        $schedule->command('app:create-match-reminders 1')->dailyAt('18:00');
        
        // Proses notifikasi yang sudah dijadwalkan 
        // (untuk notifikasi email, push notifications, dll)
        $schedule->call(function() {
            app(NotificationService::class)->processScheduledNotifications();
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
