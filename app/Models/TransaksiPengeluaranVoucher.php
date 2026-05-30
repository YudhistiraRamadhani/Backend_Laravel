<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPengeluaranVoucher extends Model
{
    use HasFactory;
    protected $table = 'transaksi_pengeluaran_vouchers';
    protected $fillable = [
        'image',
        'nama_barang',
        'nama_supplier',
   
        'tanggal_transaksi',

        'jumlah',
        'harga',
    ];
}
