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
        Schema::create('koperasis', function (Blueprint $table) {
            $table->id();
            $table->string('no_badan_hukum')->unique();
            $table->string('nik_koperasi')->nullable();
            $table->string('nama_koperasi');
            $table->enum('jenis_koperasi', ['KSP', 'KSU', 'Produsen', 'Pemasaran', 'Konsumen', 'Jasa']);
            $table->string('kabupaten_kota');
            $table->text('alamat');
            $table->integer('tahun_berdiri');
            $table->enum('status_keaktifan', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->decimal('aset', 18, 2)->default(0);
            $table->decimal('modal_sendiri', 18, 2)->default(0);
            $table->decimal('volume_usaha', 18, 2)->default(0);
            $table->decimal('shu', 18, 2)->default(0);
            $table->decimal('skor_kesehatan_terakhir', 5, 2)->nullable();
            $table->enum('predikat_kesehatan', ['Sehat', 'Cukup Sehat', 'Dalam Pengawasan', 'Pengawasan Khusus'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koperasis');
    }
};
