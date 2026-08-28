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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin_koperasi', 'bidang_pengawasan'])->default('admin_koperasi')->after('password');
            $table->foreignId('koperasi_id')->nullable()->constrained('koperasis')->nullOnDelete()->after('role');
            $table->string('nip')->nullable()->after('koperasi_id');
            $table->string('jabatan')->nullable()->after('nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['koperasi_id']);
            $table->dropColumn(['role', 'koperasi_id', 'nip', 'jabatan']);
        });
    }
};
