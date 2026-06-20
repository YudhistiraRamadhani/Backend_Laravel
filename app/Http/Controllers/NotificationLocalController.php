<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationLocalController extends Controller
{
    public function getNotification()
    {
        // Tentukan jam dan menit yang Anda inginkan (contoh: Jam 22:38 / 10:38 PM)
        $targetHour = 21;
        $targetMinute = 26;

        // Buat objek Carbon berdasarkan waktu sekarang, lalu atur jam dan menitnya
        $scheduledTime = Carbon::now()->setTime($targetHour, $targetMinute, 0);

        // Validasi: Jika waktu yang ditentukan sudah lewat untuk hari ini,
        // maka jadwalkan untuk hari esok.
        if ($scheduledTime->isPast()) {
            $scheduledTime->addDay();
        }

        $data = [
            'title' => 'Pengingat Jadwal',
            'message' => 'Jangan lupa untuk melakukan pencatatan pada Aplikasi dan cek laporan hari ini.',
            'scheduled_at' => $scheduledTime->format('Y-m-d H:i:s'),
        ];

        return response()->json($data, 200);
    }
}
