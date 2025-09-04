<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'is_read',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
