<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $endpoint = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', 'dynwNgQEmPkzLHoGMi6y');
    }

    /**
     * Kirim pesan WhatsApp via Fonnte API
     */
    public function sendMessage(string $target, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->endpoint, [
                        'target' => $target,
                        'message' => $message,
                    ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                Log::info('Fonnte: Pesan terkirim ke ' . $target);
                return ['success' => true, 'data' => $result];
            }

            Log::warning('Fonnte: Gagal kirim pesan', ['response' => $result]);
            return ['success' => false, 'error' => $result['reason'] ?? 'Unknown error'];

        } catch (\Exception $e) {
            Log::error('Fonnte: Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Template pesan untuk pembayaran disetujui
     */
    public function buildApproveMessage(array $data): string
    {
        $rupiah = number_format($data['total_bayar'], 0, ',', '.');

        return "Halo {$data['nama_mahasiswa']},\n\n" .
            "Selamat! Pembayaran KKNM Anda telah DISETUJUI. ✅\n\n" .
            "ID Pembayaran: #{$data['kkn_pembayaran_id']}\n" .
            "Jumlah: Rp {$rupiah}\n\n" .
            "Catatan Admin: ACC Keuangan, silahkan lanjut daftar\n\n" .
            "Silakan lanjut login di: http://kknm.unla.ac.id/pendaftaran\n\n" .
            "Terima kasih.";
    }

    /**
     * Template pesan untuk pembayaran ditolak
     */
    public function buildRejectMessage(array $data, string $alasan): string
    {
        $rupiah = number_format($data['total_bayar'], 0, ',', '.');

        return "Halo {$data['nama_mahasiswa']},\n\n" .
            "Mohon Maaf, Pembayaran KKNM Anda DITOLAK. ❌\n\n" .
            "ID Pembayaran: #{$data['kkn_pembayaran_id']}\n" .
            "Jumlah: Rp {$rupiah}\n\n" .
            "Alasan: {$alasan}\n\n" .
            "Silakan LOGIN KEMBALI di http://kknm.unla.ac.id/login untuk mengupload ulang bukti pembayaran yang benar.\n\n" .
            "Terima kasih.";
    }
}
