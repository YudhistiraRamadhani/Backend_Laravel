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
// --- ROUTE TANPA LOGIN (Agar Flutter tidak loading/401) ---
Route::get('/cron-trigger', function () {
    Artisan::call('schedule:run');
    return 'Scheduler executed successfully!';
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


Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);

