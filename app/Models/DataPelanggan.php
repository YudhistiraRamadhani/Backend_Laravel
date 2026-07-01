<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPelanggan extends Model
{
    use HasFactory;
   protected $fillable = [
    'nama_pelanggan',
    'no_whatsapp',
    'pesannotifikasi',
   'telegram_chat_id',
    'telegram_username',
    'telegram_active',
];
}
