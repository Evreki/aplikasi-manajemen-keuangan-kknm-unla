<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReceivePaymentController; // <--- Pastikan baris ini ada!

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// INI ROUTE YANG PENTING
Route::post('/receive-payment', [ReceivePaymentController::class, 'store']);
