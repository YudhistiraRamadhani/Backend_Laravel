<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';

    protected $fillable = [
        'kode_produk',
        'Nama_Barang',
        'Harga',
        'Stok',
        'image',
        'jenis_barang',
    ];

    /**
     * Accessor: Menghubungkan 'Nama_Barang' di DB ke properti model
     */
    public function getNamaBarangAttribute()
    {
        return $this->attributes['Nama_Barang'] ?? null;
    }

    /**
     * Accessor: Menghubungkan 'Harga' di DB ke properti model
     */
    public function getHargaAttribute()
    {
        return $this->attributes['Harga'] ?? null;
    }

    /**
     * Accessor: Menghubungkan 'Stok' di DB ke properti model
     */
    public function getStokAttribute()
    {
        return $this->attributes['Stok'] ?? null;
    }
}
