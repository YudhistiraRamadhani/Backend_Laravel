<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TelegramWebhookController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

 Route::post('/', [TelegramWebhookController::class, 'handle']);
Route::post('/index.php', [TelegramWebhookController::class, 'handle']);
Route::any('/index.php', [TelegramWebhookController::class, 'handle']);
