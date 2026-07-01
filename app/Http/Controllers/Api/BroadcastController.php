<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DataPelanggan;

class BroadcastController extends Controller
{
    /**
     * Kirim pesan ke satu nomor WhatsApp
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBroadcast(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'target' => 'required|string', // Format: 08123456789
            'message' => 'required|string',
            'nama' => 'nullable|string', // Opsional
        ]);

        $token = env('FONNTE_TOKEN', 'ork2zNR3YAQgdTznHbiM');

        // Ambil data
        $number = $request->target;
        $message = $request->message;
        $nama = $request->nama ?? 'Pelanggan';

        // Format nomor
        $formattedTarget = $this->formatNumber($number);

        // Personalisasi pesan (ganti {nama} dengan nama pelanggan)
        $finalMessage = str_replace('{nama}', $nama, $message);

        try {
            // Kirim satu pesan
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $formattedTarget,
                'message' => $finalMessage,
                'delay' => '5-10', // Delay kecil untuk 1 pesan
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pesan berhasil dikirim ke ' . $formattedTarget,
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim pesan',
                'error' => $response->body(),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kirim pesan ke pelanggan berdasarkan ID
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendToPelanggan(Request $request, $id)
    {
        // Cari pelanggan
        $pelanggan = DataPelanggan::find($id);
        
        if (!$pelanggan) {
            return response()->json([
                'status' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        // Validasi pesan
        $request->validate([
            'message' => 'required|string',
        ]);

        // Gunakan pesan dari request atau dari database
        $message = $request->message ?? $pelanggan->pesannotifikasi ?? '';

        if (empty($message)) {
            return response()->json([
                'status' => false,
                'message' => 'Pesan tidak boleh kosong',
            ], 400);
        }

        // Kirim ke Fonnte
        $token = env('FONNTE_TOKEN', 'ork2zNR3YAQgdTznHbiM');
        $formattedTarget = $this->formatNumber($pelanggan->no_whatsapp);
        $finalMessage = str_replace('{nama}', $pelanggan->nama_pelanggan, $message);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $formattedTarget,
                'message' => $finalMessage,
                'delay' => '5-10',
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pesan berhasil dikirim ke ' . $pelanggan->nama_pelanggan,
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim pesan',
                'error' => $response->body(),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format nomor WhatsApp ke format 62
     * 
     * @param string $number
     * @return string
     */
    private function formatNumber($number)
    {
        $number = trim($number);
        $number = preg_replace('/[^0-9]/', '', $number);
        
        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }
        
        if (str_starts_with($number, '62')) {
            return $number;
        }
        
        return $number;
    }

    /**
     * Kirim pesan ke Telegram (selain WhatsApp)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTelegram(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $token = env('TELEGRAM_BOT_TOKEN');

        if (empty($token)) {
            return response()->json([
                'status' => false,
                'message' => 'TELEGRAM_BOT_TOKEN tidak diatur',
            ], 500);
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $request->chat_id,
                'text' => $request->message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pesan Telegram berhasil dikirim',
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim pesan Telegram',
                'error' => $response->body(),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}