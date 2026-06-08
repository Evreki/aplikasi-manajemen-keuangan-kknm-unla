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
        'waktu_transfer',
        'bukti_pembayaran_path',
        'status',
        'is_kip',
        'keterangan_mahasiswa',
        'ocr_data',
        'ocr_confidence',
        'verified_by',
        'alasan_penolakan',
    ];

    protected $casts = [
        'waktu_transfer' => 'datetime',
        'is_kip' => 'boolean',
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

    /**
     * Relasi ke admin yang memverifikasi transaksi
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
