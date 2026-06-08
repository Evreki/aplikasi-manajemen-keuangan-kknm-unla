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
        Schema::table('transaksi_keuangans', function (Blueprint $table) {
            $table->unsignedBigInteger('verified_by')->nullable()->after('status');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->text('alasan_penolakan')->nullable()->after('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_keuangans', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verified_by', 'alasan_penolakan']);
        });
    }
};
