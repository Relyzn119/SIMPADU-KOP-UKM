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
        Schema::create('pengawasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_id')->constrained('koperasis')->cascadeOnDelete();
            $table->string('no_surat_tugas');
            $table->date('tanggal_pemeriksaan');
            $table->string('nama_tim_pengawas');
            $table->decimal('skor_tata_kelola', 5, 2)->default(0);
            $table->decimal('skor_profil_risiko', 5, 2)->default(0);
            $table->decimal('skor_kinerja_keuangan', 5, 2)->default(0);
            $table->decimal('skor_permodalan', 5, 2)->default(0);
            $table->decimal('skor_total', 5, 2)->default(0);
            $table->enum('predikat_kesehatan', ['Sehat', 'Cukup Sehat', 'Dalam Pengawasan', 'Pengawasan Khusus']);
            $table->text('kesimpulan_pengawasan')->nullable();
            $table->string('file_berita_acara_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengawasans');
    }
};
