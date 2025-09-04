<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Santri
 *
 * Represents a Santri model in the application.
 */

class Santri extends Model
{
    //
    use HasFactory;
    protected $table = 'santri';
    protected $fillable = ['user_id', 'nama', 'kamar', 'foto'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
