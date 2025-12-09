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
    Schema::table('transaksi_keuangans', function (Blueprint $table) {
        // Kita taruh setelah nama_mahasiswa biar rapi
        $table->string('no_telepon')->nullable()->after('nama_mahasiswa');
    });
}

public function down()
{
    Schema::table('transaksi_keuangans', function (Blueprint $table) {
        $table->dropColumn('no_telepon');
    });
}
};
