<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ortu extends Model
{
    use HasFactory;

    protected $table = 'ortu';
    protected $fillable = ['user_id', 'santri_id', 'nama', 'no_hp', 'foto'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
