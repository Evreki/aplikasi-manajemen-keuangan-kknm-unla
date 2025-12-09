<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksi_keuangans', function (Blueprint $table) {
            $table->id();
            // ID Referensi dari Web KKN (Penting buat sinkronisasi)
            $table->unsignedBigInteger('kkn_pembayaran_id');

            // Data Mahasiswa
            $table->string('nim');
            $table->string('nama_mahasiswa');

            // Data Keuangan
            $table->decimal('total_bayar', 12, 2);
            $table->string('bukti_pembayaran_path'); // Path file gambar

            // Status Verifikasi (Default pending)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Slot untuk OCR (Kita siapin dari sekarang biar gak bongkar db lagi nanti)
            $table->json('ocr_data')->nullable();

            $table->timestamps();
        });
    }
};
