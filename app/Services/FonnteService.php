<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public static function sendMessage($target, $message, $schedule = null)
    {
        $token = env('FONNTE_TOKEN');

        // ✅ PAYLOAD AMAN
        $payload = [
            'target'  => $target,
            'message' => $message,
        ];

        // ✅ HANYA TAMBAH SCHEDULE JIKA ADA
        if (!empty($schedule)) {
            $payload['schedule'] = $schedule;
        }

        // ✅ DEBUG LOG
        Log::info('PAYLOAD FONNTE:', $payload);

        // ✅ KIRIM KE FONNTE (JSON)
        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type'  => 'application/json',
        ])->post('https://api.fonnte.com/send', $payload);

        // ✅ LOG RESPONSE
        Log::error('RESPONSE FONNTE:', [
            'status' => $response->status(),
            'body'   => $response->json()
        ]);

        return $response->json();
    }
}
