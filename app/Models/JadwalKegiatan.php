<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kegiatan';

    protected $fillable = [
        'tanggal',
        'judul_kegiatan',
        'waktu_mulai',
        'waktu_selesai',
    ];
}
