<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BroadcastController extends Controller
{
    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'target' => 'required',
            'message' => 'required|string',
        ]);

        $token = "SnmjjjeviWdCJmgP7ncV";

        // Pecah target menjadi array
        $targetRaw = str_replace(' ', '', $request->target);
        $targets = explode(',', $targetRaw);

        $successCount = 0;

        foreach ($targets as $number) {
            $number = trim($number);

            // Format nomor ke format internasional
            if (str_starts_with($number, '0')) {
                $formattedTarget = '62' . substr($number, 1);
            } elseif (str_starts_with($number, '+')) {
                $formattedTarget = substr($number, 1);
            } else {
                $formattedTarget = $number;
            }

            // KIRIM SATU PER SATU KE FONNTE
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $formattedTarget,
                'message' => $request->message,
                'delay' => '5-10', // Jeda internal Fonnte
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $successCount++;
            }

            // JEDA TAMBAHAN (RANDOM 10-20 DETIK)
            // Ini membuat server seolah-olah mengirim pesan dengan jeda manusia
            sleep(rand(10, 20));
        }

        return response()->json([
            'status' => true,
            'message' => 'Proses broadcast selesai',
            'total_sent' => $successCount,
            'total_targets' => count($targets)
        ]);
    }
}
