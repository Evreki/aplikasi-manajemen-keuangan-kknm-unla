<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class ReceivePaymentController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi request sederhana
        if (!$request->hasFile('bukti_pembayaran')) {
            return response()->json(['message' => 'File bukti pembayaran wajib ada'], 400);
        }

        try {
            // 2. Simpan File Gambar di folder 'public/bukti_transfer'
            $file = $request->file('bukti_pembayaran');
            // Nama file kita samakan dengan yang dikirim atau generate baru
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_transfer', $fileName, 'public');

            // 3. Simpan Data ke Database
            $transaksi = TransaksiKeuangan::create([
                'kkn_pembayaran_id' => $request->kkn_pembayaran_id,
                'nim'               => $request->nim,
                'nama_mahasiswa'    => $request->nama_mahasiswa,
                'no_telepon'        => $request->no_telepon,
                'total_bayar'       => $request->amount,
                'waktu_transfer'    => $request->waktu_transfer ?? $request->jam_transfer ?? now(), // Save actual transfer time, fallback to now()
                'is_kip'            => filter_var($request->is_kip, FILTER_VALIDATE_BOOLEAN), // Tangkap status KIP
                'keterangan_mahasiswa' => $request->keterangan_mahasiswa, // Capture keterangan
                'bukti_pembayaran_path' => $path,
                'status'            => 'pending'
            ]);

            // Ambil semua admin/super_admin untuk dikirimi notifikasi
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

            // Kirim notifikasi database untuk memicu Polling di dashboard
            Notification::make()
                ->title('Pembayaran Baru Masuk')
                ->body("Dari {$request->nama_mahasiswa} ({$request->nim})")
                ->success()
                ->sendToDatabase($admins);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diterima oleh Sistem Keuangan',
                'data_id' => $transaksi->id
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
