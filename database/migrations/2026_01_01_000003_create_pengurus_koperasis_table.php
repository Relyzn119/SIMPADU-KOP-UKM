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
        Schema::create('pengurus_koperasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_id')->constrained('koperasis')->cascadeOnDelete();
            $table->string('ketua');
            $table->string('sekretaris');
            $table->string('bendahara');
            $table->string('ketua_pengawas');
            $table->string('manajer')->nullable();
            $table->integer('jumlah_anggota_pria')->default(0);
            $table->integer('jumlah_anggota_wanita')->default(0);
            $table->string('periode_jabatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus_koperasis');
    }
};
