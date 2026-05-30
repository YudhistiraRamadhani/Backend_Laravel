<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';


    protected $fillable = [
        'Nama_Barang', 'nama_barang', // Daftarkan dua-duanya agar aman
        'Harga', 'harga',
        'Jumlah', 'jumlah',
        'Tanggal', 'tanggal',
        'jenis_transaksi',
        'jenis_barang',
        'nama_supplier',
        'deskripsi',
    ];

    public function getNamaBarangAttribute()
    {
        return $this->attributes['Nama_Barang'] ?? null;
    }

    public function getHargaAttribute()
    {
        return $this->attributes['Harga'] ?? null;
    }

    public function getJumlahAttribute()
    {
        return $this->attributes['Jumlah'] ?? null;
    }

    public function getTanggalAttribute()
    {
        return $this->attributes['Tanggal'] ?? null;
    }
    // Fungsi Boot untuk menangkap data dari Flutter sebelum disimpan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Jika Flutter kirim 'nama_barang', masukkan ke 'Nama_Barang'
            if (isset($model->nama_barang)) $model->Nama_Barang = $model->nama_barang;
            if (isset($model->harga)) $model->Harga = $model->harga;
            if (isset($model->jumlah)) $model->Jumlah = $model->jumlah;
            if (isset($model->tanggal)) $model->Tanggal = $model->tanggal;
        });
    }

    public function scopeVoucher($query) {
        return $query->where('jenis_barang', 'Voucher');
    }

    public function scopeBarang($query) {
        return $query->where('jenis_barang', 'Barang');
    }
}
