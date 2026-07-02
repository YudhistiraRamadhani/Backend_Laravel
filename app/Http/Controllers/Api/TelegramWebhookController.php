<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DataPelanggan;


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
                $pelanggan = DataPelanggan::where('nama_pelanggan', 'LIKE', "%{$firstName}%")->first();

                if ($pelanggan) {
                    $pelanggan->update([
                        'telegram_chat_id' => $chatId,
                        'telegram_username' => $update['message']['from']['username'] ?? null,
                        'telegram_active' => true,
                    ]);

                    $this->sendTelegramMessage($chatId, "✅ Halo {$firstName}! Akun Telegram Anda berhasil ditautkan dengan sistem <b>JS CELL</b>.");
                } else {
                    $this->sendTelegramMessage($chatId, "👋 Selamat datang di <b>TokoJS Bot</b>!\n\nChat ID Anda adalah: <code>{$chatId}</code>.\nBerikan ID ini ke admin untuk sinkronisasi data.");
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function sendTelegramMessage($chatId, $message)
    {
        Http::post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
