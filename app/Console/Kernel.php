<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\ScheduledNotification;
use App\Jobs\SendFcmNotificationJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 1. Mengecek antrean dinamis dari database setiap menit
        $schedule->command('notification:check')->everyMinute();

        // 2. Pengingat Otomatis Jam 9 Malam (Otomatis dibuat oleh server setiap hari)
        $schedule->call(function () {
            $notification = ScheduledNotification::create([
                'title' => 'Pengingat Transaksi 📝',
                'body' => 'Sudah jam 9 malam nih, jangan lupa catat seluruh transaksi Anda hari ini ya!',
                'topic' => 'all_users', // Dikirim ke semua user yang subscribe topik ini
                'scheduled_at' => now('Asia/Jakarta'),
                'status' => 'pending'
            ]);

            // Jalankan pengiriman langsung ke antrean
            dispatch(new SendFcmNotificationJob($notification));
        })->dailyAt('21:00')->timezone('Asia/Jakarta');
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
