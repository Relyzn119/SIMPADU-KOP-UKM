<?php

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

    // Modul Pelaporan & Monitoring RAT
    Route::resource('rat', RatController::class);

    // Modul Pemeriksaan & Skor Kesehatan Koperasi
    Route::resource('pengawasan', PengawasanController::class);

    // Modul Matriks Temuan & Tindak Lanjut
    Route::get('/temuan', [TemuanController::class, 'index'])->name('temuan.index');
    Route::put('/temuan/{temuan}/tindak-lanjut', [TemuanController::class, 'updateTindakLanjut'])->name('temuan.update-tindak-lanjut');
    Route::put('/temuan/{temuan}/verifikasi', [TemuanController::class, 'verifikasi'])->name('temuan.verifikasi');
    Route::delete('/temuan/{temuan}', [TemuanController::class, 'destroy'])->name('temuan.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
