<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BroadcastController extends Controller
{
    public function sendBroadcast(Request $request)
    {
        // 1. Validasi Input
        // Pastikan Anda mengirim array of objects atau format yang mendukung nama
        // Contoh request: target = [{"nomor": "0812...", "nama": "Budi"}, ...]
        $request->validate([
            'target' => 'required', // Diharapkan format JSON string dari Flutter
            'message' => 'required|string',
        ]);

        $token = "SnmjjjeviWdCJmgP7ncV";

        // Decode data target menjadi array
        $targets = json_decode($request->target, true);

        // 2. BATASI MAKSIMAL 20 TARGET PER REQUEST (BATCHING)
        $batchTargets = array_slice($targets, 0, 20);

        $successCount = 0;

        foreach ($batchTargets as $item) {
            $number = $item['nomor'] ?? '';
            $nama = $item['nama'] ?? 'Pelanggan';

            $formattedTarget = $this->formatNumber($number);

            // 3. PERSONALISASI PESAN
            // Ganti {nama} di pesan dengan nama asli pelanggan agar tidak dianggap spam
            $finalMessage = str_replace('{nama}', $nama, $request->message);

            // 4. KIRIM SATU PER SATU
            $response = Http::withHeaders(['Authorization' => $token])
                ->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $formattedTarget,
                    'message' => $finalMessage,
                    'delay' => '15-30', // Delay internal Fonnte
                ]);

            if ($response->successful()) {
                $successCount++;
            }

            // 5. JEDA AMAN (20-40 detik antar pesan)
            sleep(rand(20, 40));
        }

        return response()->json([
            'status' => true,
            'message' => 'Batch selesai. Berhasil terkirim: ' . $successCount,
            'remaining' => count($targets) - count($batchTargets)
        ]);
    }

    // Fungsi pembantu format nomor agar selalu 62
    private function formatNumber($number) {
        $number = trim($number);
        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        } elseif (str_starts_with($number, '+')) {
            return substr($number, 1);
        }
        return $number;
    }
}
