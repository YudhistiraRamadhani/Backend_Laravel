<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationFonntepromo extends Model
{
    use HasFactory;
     protected $table = 'notification_fonntepromos';

     protected $fillable = [
        'nama',
        'nomorwa',
        'pesannotifikasi',
        'namabarang',
        'tanggal_kirim',
    ];

}
