<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasilokal extends Model
{
    use HasFactory;
       protected $table = 'notifikasilokals';
        protected $fillable = [
        'judul',
        'pesan',
        'waktu'
    ];
}
