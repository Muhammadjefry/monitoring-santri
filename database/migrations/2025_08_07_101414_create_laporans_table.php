<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('jadwal_id');
            $table->string('bukti_laporan')->nullable();
            $table->timestamps();

            $table->foreign('santri_id')->references('id')->on('santri')->onDelete('cascade');
            $table->foreign('jadwal_id')->references('id')->on('jadwal_kegiatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
