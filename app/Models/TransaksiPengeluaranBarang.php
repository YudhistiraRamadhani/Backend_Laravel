<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPengeluaranBarang extends Model
{
    use HasFactory;
    protected $table = 'transaksi_pengeluaran_barangs';
    protected $fillable = [
        'nama_barang',
        'deskripsi',
        'harga',
        'jumlah',
        'tanggal_transaksi',
    ];
}
