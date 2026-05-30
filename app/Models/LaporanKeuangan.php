<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeuangan extends Model
{
    use HasFactory;
    protected $table = 'laporan_keuangan';

    // Kolom yang diizinkan untuk diisi secara massal
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'pendapatan',
        'pengeluaran',
        'nama_barang',
        'harga',
        'jumlah',
        'tanggal',
        'jenis_barang',
        // 'jenis_transaksi',
        'nama_supplier',
    ];

    // Mengubah format tanggal otomatis saat diakses
    protected $casts = [
        'tanggal' => 'date',
        'pendapatan' => 'integer',
        'pengeluaran' => 'integer',
        'harga' => 'integer',
        'jenis_barang' => 'string',
    ];
}
