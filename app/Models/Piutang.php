<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    use HasFactory;
    protected $table = 'piutang';
public $timestamps = false;

    protected $fillable = [
        'nama_pelanggan',
        'jumlah_hutang',
        'nama_barang',
        'harga',
        'status',
        'pesanpenagihan',
        'no_whatsapp',
        'date',
    ];
}
