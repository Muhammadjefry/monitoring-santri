<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';
    protected $fillable = [
        'santri_id',
        'jadwal_id',
        'bukti_laporan',
        'keterangan',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalKegiatan::class, 'jadwal_id');
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }
}
