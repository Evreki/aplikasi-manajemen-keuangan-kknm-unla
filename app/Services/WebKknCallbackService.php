<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebKknCallbackService
{
    protected string $baseUrl;

    public function __construct()
    {
        // URL Web KKN - bisa diubah di .env
        $this->baseUrl = config('services.webkkn.url', 'http://127.0.0.1:8000');
    }

    /**
     * Kirim callback ke Web KKN saat pembayaran di-approve
     */
    public function sendApproveCallback(int $kknPembayaranId, string $nim): array
    {
        return $this->sendCallback([
            'kkn_pembayaran_id' => $kknPembayaranId,
            'nim' => $nim,
            'status' => 'approved',
            'catatan' => 'ACC Keuangan, silahkan lanjut daftar',
        ]);
    }

    /**
     * Kirim callback ke Web KKN saat pembayaran ditolak
     */
    public function sendRejectCallback(int $kknPembayaranId, string $nim, string $alasan): array
    {
        return $this->sendCallback([
            'kkn_pembayaran_id' => $kknPembayaranId,
            'nim' => $nim,
            'status' => 'rejected',
            'catatan' => $alasan,
        ]);
    }

    /**
     * Kirim HTTP POST ke endpoint callback Web KKN
     */
    protected function sendCallback(array $data): array
    {
        try {
            $response = Http::timeout(10)->post(
                $this->baseUrl . '/api/payment-callback',
                $data
            );

            if ($response->successful()) {
                Log::info('WebKKN Callback: Success', $data);
                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning('WebKKN Callback: Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return ['success' => false, 'error' => 'HTTP ' . $response->status()];

        } catch (\Exception $e) {
            Log::error('WebKKN Callback: Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
