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
        Schema::table('pengawasans', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->enum('status_persetujuan_koperasi', ['pending', 'approved', 'rejected'])->default('pending')->after('status_verifikasi');
            $table->text('tanggapan_koperasi')->nullable()->after('status_persetujuan_koperasi');
            $table->string('file_bukti_tindak_lanjut_path')->nullable()->after('tanggapan_koperasi');
            $table->integer('skor_transparansi')->default(100)->after('file_bukti_tindak_lanjut_path');
            $table->foreignId('approved_by')->nullable()->after('skor_transparansi')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        Schema::table('temuans', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->enum('status_persetujuan_koperasi', ['pending', 'approved', 'rejected'])->default('pending')->after('status_tindak_lanjut');
            $table->string('file_bukti_tindak_lanjut_path')->nullable()->after('tanggapan_koperasi');
            $table->timestamp('approved_at')->nullable()->after('file_bukti_tindak_lanjut_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengawasans', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'created_by',
                'status_persetujuan_koperasi',
                'tanggapan_koperasi',
                'file_bukti_tindak_lanjut_path',
                'skor_transparansi',
                'approved_by',
                'approved_at',
            ]);
        });

        Schema::table('temuans', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'created_by',
                'status_persetujuan_koperasi',
                'file_bukti_tindak_lanjut_path',
                'approved_at',
            ]);
        });
    }
};
