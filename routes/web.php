<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoperasiController;
use App\Http\Controllers\PengawasanController;
use App\Http\Controllers\RatController;
use App\Http\Controllers\TemuanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Master Data Koperasi
    Route::resource('koperasi', KoperasiController::class);
    Route::put('/koperasi/{koperasi}/verifikasi', [KoperasiController::class, 'verifikasi'])->name('koperasi.verifikasi');
    Route::put('/koperasi/{koperasi}/tolak', [KoperasiController::class, 'tolak'])->name('koperasi.tolak');

    // Modul Pelaporan & Monitoring RAT
    Route::resource('rat', RatController::class);
    Route::put('/rat/{rat}/verifikasi', [RatController::class, 'verifikasi'])->name('rat.verifikasi');
    Route::put('/rat/{rat}/tolak', [RatController::class, 'tolak'])->name('rat.tolak');

    // Modul Pemeriksaan & Skor Kesehatan Koperasi
    Route::resource('pengawasan', PengawasanController::class);
    Route::put('/pengawasan/{pengawasan}/verifikasi', [PengawasanController::class, 'verifikasi'])->name('pengawasan.verifikasi');
    Route::put('/pengawasan/{pengawasan}/tolak', [PengawasanController::class, 'tolak'])->name('pengawasan.tolak');

    // Modul Matriks Temuan & Tindak Lanjut
    Route::get('/temuan', [TemuanController::class, 'index'])->name('temuan.index');
    Route::put('/temuan/{temuan}/tindak-lanjut', [TemuanController::class, 'updateTindakLanjut'])->name('temuan.update-tindak-lanjut');
    Route::put('/temuan/{temuan}/verifikasi', [TemuanController::class, 'verifikasi'])->name('temuan.verifikasi');
    Route::put('/temuan/{temuan}/tolak', [TemuanController::class, 'tolak'])->name('temuan.tolak');
    Route::delete('/temuan/{temuan}', [TemuanController::class, 'destroy'])->name('temuan.destroy');

    // Modul Audit Log Verifikasi Pengawas
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // Modul Cetak Laporan Resmi Diskop Sumut
    Route::get('/cetak', [CetakController::class, 'index'])->name('cetak.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
