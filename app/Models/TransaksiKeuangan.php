<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TransaksiKeuangan extends Model
{
    use HasFactory;

    // KITA PERLU MENAMBAHKAN INI (IZIN TERTULIS)
    protected $fillable = [
        'kkn_pembayaran_id',
        'nim',
        'nama_mahasiswa',
        'no_telepon',
        'total_bayar',
        'bukti_pembayaran_path',
        'status',
        'ocr_data',
        'ocr_confidence',
    ];

    /**
     * Accessor untuk mendapatkan URL lengkap dari bukti pembayaran
     */
    public function getBuktiPembayaranUrlAttribute()
    {
        if ($this->bukti_pembayaran_path) {
            return asset('storage/' . $this->bukti_pembayaran_path);
        }
        return null;
    }
}
