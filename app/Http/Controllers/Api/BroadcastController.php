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
        $request->validate([
            'target' => 'required|string',
            'message' => 'required|string',
            'nama' => 'nullable|string',
        ]);

        $token = env('FONNTE_TOKEN', 'ork2zNR3YAQgdTznHbiM');

        $number = $request->target;
        $message = $request->message;
        $nama = $request->nama ?? 'Pelanggan';

        $formattedTarget = $this->formatNumber($number);
        $finalMessage = str_replace('{nama}', $nama, $message);

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
        $pelanggan = DataPelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'status' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->message ?? $pelanggan->pesannotifikasi ?? '';

        if (empty($message)) {
            return response()->json([
                'status' => false,
                'message' => 'Pesan tidak boleh kosong',
            ], 400);
        }

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

        $token = config('services.telegram.bot_token');

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

    /**
     * Kirim pesan Telegram ke banyak target sekaligus.
     * Format targets: [{ "chat_id": "123", "nama": "Budi", "message": "..." }, ...]
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBatchTelegram(Request $request)
    {
        $request->validate([
            'targets' => 'required|array|min:1',
            'targets.*.chat_id' => 'required|string',
        ]);

        $token = config('services.telegram.bot_token');

        if (empty($token)) {
            return response()->json([
                'status' => false,
                'message' => 'TELEGRAM_BOT_TOKEN tidak diatur',
            ], 500);
        }

        $success = 0;
        $failed = 0;

        foreach ($request->targets as $target) {
            $message = $target['message'] ?? $request->message ?? '';
            $message = str_replace('{nama}', $target['nama'] ?? 'Pelanggan', $message);

            if (empty($target['chat_id']) || empty($message)) {
                $failed++;
                continue;
            }

            try {
                $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $target['chat_id'],
                    'text' => $message,
                ]);

                $response->successful() ? $success++ : $failed++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return response()->json([
            'status' => true,
            'success' => $success,
            'failed' => $failed,
            'total' => count($request->targets),
        ]);
    }

    /**
     * Cek apakah nomor WhatsApp tertentu sudah punya chat_id Telegram
     * (sudah pernah kirim /start ke bot dan berhasil ditautkan).
     *
     * @param string $phoneNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatIdByPhone($phoneNumber)
    {
        $normalized = $this->formatNumber($phoneNumber);

        $pelanggan = DataPelanggan::where('no_whatsapp', $phoneNumber)
            ->orWhere('no_whatsapp', $normalized)
            ->first();

        if (!$pelanggan || empty($pelanggan->telegram_chat_id)) {
            return response()->json([
                'success' => true,
                'has_chat_id' => false,
                'chat_id' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'has_chat_id' => true,
            'chat_id' => $pelanggan->telegram_chat_id,
        ]);
    }
}
