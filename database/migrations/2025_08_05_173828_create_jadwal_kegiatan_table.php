<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jadwal_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('judul_kegiatan');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kegiatan');
    }
};
