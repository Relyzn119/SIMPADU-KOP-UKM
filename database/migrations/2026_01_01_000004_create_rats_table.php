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
        Schema::create('rats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_id')->constrained('koperasis')->cascadeOnDelete();
            $table->integer('tahun_buku');
            $table->date('tanggal_rat');
            $table->string('tempat_pelaksanaan');
            $table->integer('jumlah_anggota_hadir');
            $table->boolean('kuorum')->default(true);
            $table->enum('status_rat', ['Sudah RAT Tepat Waktu', 'Sudah RAT Terlambat', 'Belum RAT'])->default('Sudah RAT Tepat Waktu');
            $table->string('file_lpj_path')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rats');
    }
};
