<?php
// app/Http/Controllers/TelegramWebhookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DataPelanggan;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $update = $request->all();
            
            // Log untuk debugging
            Log::info('Telegram Webhook received:', $update);
            
            // Cek apakah ada pesan masuk
            if (isset($update['message'])) {
                $message = $update['message'];
                $chatId = $message['chat']['id'];
                $text = $message['text'] ?? '';
                $username = $message['chat']['username'] ?? '';
                $firstName = $message['chat']['first_name'] ?? '';

                // Jika pengguna mengirim /start
                if ($text == '/start') {
                    // Simpan chat_id ke database
                    $this->registerChatId($chatId, $username, $firstName);
                    
                    // Balas pesan selamat datang
                    $token = env('TELEGRAM_BOT_TOKEN');
                    Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => "✅ Selamat! Anda telah terdaftar untuk menerima notifikasi promo dan penagihan.\n\n"
                            . "Anda akan menerima pesan promo dan pengingat tagihan dari TokoJS.\n"
                            . "Untuk berhenti, kirim /stop",
                        'parse_mode' => 'HTML'
                    ]);
                }
                // Jika pengguna mengirim /stop
                elseif ($text == '/stop') {
                    $this->unregisterChatId($chatId);
                    
                    $token = env('TELEGRAM_BOT_TOKEN');
                    Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => "❌ Anda telah berhenti menerima notifikasi. Kirim /start untuk aktif kembali.",
                        'parse_mode' => 'HTML'
                    ]);
                }
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Telegram Webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function registerChatId($chatId, $username, $firstName)
    {
        try {
            // Cari pelanggan berdasarkan username atau nama
            $pelanggan = DataPelanggan::where('telegram_chat_id', $chatId)->first();
            
            if (!$pelanggan) {
                // Jika belum ada, cari berdasarkan username
                $pelanggan = DataPelanggan::where('telegram_username', $username)->first();
            }
            
            if ($pelanggan) {
                $pelanggan->telegram_chat_id = $chatId;
                $pelanggan->telegram_username = $username;
                $pelanggan->telegram_active = true;
                $pelanggan->save();
                
                Log::info("Chat ID registered: {$chatId} for {$pelanggan->nama_pelanggan}");
            } else {
                // Buat pelanggan baru jika belum ada
                $pelanggan = DataPelanggan::create([
                    'nama_pelanggan' => $firstName ?? 'Unknown',
                    'no_whatsapp' => '',
                    'telegram_chat_id' => $chatId,
                    'telegram_username' => $username,
                    'telegram_active' => true,
                ]);
                
                Log::info("New pelanggan created from Telegram: {$firstName}");
            }
        } catch (\Exception $e) {
            Log::error('Register chat ID error: ' . $e->getMessage());
        }
    }

    private function unregisterChatId($chatId)
    {
        try {
            DataPelanggan::where('telegram_chat_id', $chatId)->update([
                'telegram_chat_id' => null,
                'telegram_active' => false
            ]);
            
            Log::info("Chat ID unregistered: {$chatId}");
        } catch (\Exception $e) {
            Log::error('Unregister chat ID error: ' . $e->getMessage());
        }
    }
}