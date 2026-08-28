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
        Schema::create('temuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengawasan_id')->constrained('pengawasans')->cascadeOnDelete();
            $table->foreignId('koperasi_id')->constrained('koperasis')->cascadeOnDelete();
            $table->enum('aspek_temuan', ['Kelembagaan', 'Keuangan', 'Operasional', 'Usaha']);
            $table->text('deskripsi_temuan');
            $table->text('rekomendasi');
            $table->date('batas_waktu');
            $table->enum('tingkat_risiko', ['Rendah', 'Sedang', 'Tinggi', 'Kritis']);
            $table->enum('status_tindak_lanjut', ['Belum Ditindaklanjuti', 'Dalam Proses', 'Selesai'])->default('Belum Ditindaklanjuti');
            $table->text('tanggapan_koperasi')->nullable();
            $table->text('catatan_verifikasi_pengawas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temuans');
    }
};
