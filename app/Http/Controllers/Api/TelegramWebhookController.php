<?php

namespace App\Http\Controllers\Api;

// TAMBAHKAN baris di bawah ini agar class Controller utama terbaca dengan benar
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Pelanggan;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();

        // Log data untuk monitoring di dashboard Render nanti
        Log::info('Telegram Webhook Data:', $update);

        if (isset($update['message'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'] ?? '';
            $firstName = $update['message']['from']['first_name'] ?? '';

            // Jika pelanggan mengetik /start ke TokoJs_bot
            if (str_contains($text, '/start')) {
                // Coba cocokkan dengan data pelanggan di database berdasarkan nama depan telegram
                $pelanggan = Pelanggan::where('nama', 'LIKE', "%{$firstName}%")->first();

                if ($pelanggan) {
                    $pelanggan->update([
                        'chat_id_telegram' => $chatId
                    ]);

                    $this->sendTelegramMessage($chatId, "✅ Halo {$firstName}! Akun Telegram Anda berhasil ditautkan dengan sistem **JS CELL**.");
                } else {
                    $this->sendTelegramMessage($chatId, "👋 Selamat datang di **TokoJS Bot**!\n\nChat ID Anda adalah: `{$chatId}`.\nBerikan ID ini ke admin untuk sinkronisasi data.");
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function sendTelegramMessage($chatId, $message)
    {
        Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
