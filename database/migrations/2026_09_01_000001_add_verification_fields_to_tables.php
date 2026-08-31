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
        $tables = ['koperasis', 'rats', 'pengawasans', 'temuans'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->default('pending')->after('created_at');
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('status_verifikasi');
                $table->timestamp('verified_at')->nullable()->after('verified_by');
                $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
                $table->text('alasan_penolakan')->nullable()->after('rejected_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['koperasis', 'rats', 'pengawasans', 'temuans'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign([$tableName . '_verified_by_foreign']);
                $table->dropForeign([$tableName . '_rejected_by_foreign']);
                $table->dropColumn([
                    'status_verifikasi',
                    'verified_by',
                    'verified_at',
                    'rejected_by',
                    'rejected_at',
                    'alasan_penolakan',
                ]);
            });
        }
    }
};
