<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoperasiController;
use App\Http\Controllers\RatController;
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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
