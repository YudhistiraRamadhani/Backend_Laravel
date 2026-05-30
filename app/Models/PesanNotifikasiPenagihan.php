<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanNotifikasiPenagihan extends Model
{
    use HasFactory;
    protected $table = 'pesan_notifikasi_penagihans';

    // Kolom yang boleh diisi
    protected $fillable = [
        'nama_pelanggan',
        'nama_barang',
        'nomorwa',
        'pesan_notifikasi_penagihan',
    ];
}
