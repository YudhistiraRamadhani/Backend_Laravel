<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataPelangganController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\DatapiutangController;
use App\Http\Controllers\Api\LaporankeuanganController;
use App\Http\Controllers\Api\NotifikasilocalController;
use App\Http\Controllers\Api\FonnteNotificationController;
use App\Http\Controllers\Api\FonntePenagihanNotificationController;
use App\Http\Controllers\Api\BroadcastController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\AuthController;

// --- ROUTE TANPA LOGIN (Agar Flutter tidak loading/401) ---
// Route::get('/cron-trigger', function () {
//     Artisan::call('schedule:run');
//     return 'Scheduler executed successfully!';
// });
// --- TEMPORARY ROUTE CLEANER ---
Route::post('/login', [AuthController::class, 'login']);

// Protected Route (Harus menyertakan token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route test untuk cek user profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
Route::get('/cron-trigger', function () {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return 'Cache di server Render berhasil dihancurkan secara paksa!';
});

// Route Master Data
Route::apiResource('pelanggan', DataPelangganController::class);
Route::apiResource('produks', ProdukController::class);
Route::get('/produks-list', [TransaksiController::class, 'getProdukList']);
Route::get('/dashboard', [DashboardController::class, 'getSummary']);
// Route Transaksi & Stok
Route::apiResource('transaksi', TransaksiController::class);
Route::apiResource('piutang', DatapiutangController::class);

// Route Laporan & Voucher
Route::apiResource('laporankeuangan', LaporankeuanganController::class);
// PASTIKAN ini mengarah ke storeVoucher jika kamu membedakan logikanya
Route::post('/transaksi-voucher', [TransaksiController::class, 'storeVoucher']);

// Route Notifikasi & WA Gateway (Fonnte)
Route::apiResource('notifikasi', NotifikasilocalController::class);
Route::post('/promo', [FonnteNotificationController::class, 'sendNotification']);
Route::post('/penagihan', [FonntePenagihanNotificationController::class, 'store']);
Route::post('/broadcast-promo', [BroadcastController::class, 'sendBroadcast']);
// Route::get('/get-pelanggan', [DatapiutangController::class, 'getPelanggan'])
Route::get('/get-pelanggan', [DatapiutangController::class, 'getPelanggan']);
Route::get('/get-produk', [DatapiutangController::class, 'getProduk']);
// routes/api.php


// Route::post('/', [TelegramWebhookController::class, 'handle']);
// Route::post('/index.php', [TelegramWebhookController::class, 'handle'])

// Daftarkan jalur resmi di dalam routes/api.php
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);
Route::post('/broadcast-telegram', [BroadcastController::class, 'sendTelegram']);
Route::post('/send-telegram', [BroadcastController::class, 'sendTelegram']);
Route::post('/send-batch-telegram', [BroadcastController::class, 'sendBatchTelegram']);
Route::get('/get-chat-id/{phoneNumber}', [BroadcastController::class, 'getChatIdByPhone']);
Route::post('/send-to-pelanggan/{id}', [BroadcastController::class, 'sendToPelanggan']);
Route::get('/get-pelanggan', [DatapiutangController::class, 'getPelanggan']);
Route::get('/get-produk', [DatapiutangController::class, 'getProduk']);
Route::post('/piutang/{id}/lunas', [DatapiutangController::class, 'updateStatusLunas']);
Route::post('/piutang/sinkron-telegram', [DatapiutangController::class, 'sinkronTelegram']);

Route::post('/sinkron-telegram', [DatapiutangController::class, 'sinkronTelegram']);
// atau jika mau menggunakan prefix piutang
Route::post('/piutang/sinkron-telegram', [DatapiutangController::class, 'sinkronTelegram']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sinkron-telegram/{id}', [DatapiutangController::class, 'sinkronTelegramSingle']);
