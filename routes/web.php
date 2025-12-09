<?php

use App\Http\Controllers\TransaksiKeuanganPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan/transaksi-keuangan', TransaksiKeuanganPdfController::class)
        ->name('transaksi-keuangan.pdf');
});
