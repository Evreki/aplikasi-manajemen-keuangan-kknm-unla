<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKeuangan;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiKeuanganPdfController extends Controller
{
    public function __invoke()
    {
        $transaksi = TransaksiKeuangan::query()
            ->latest()
            ->get();

        $pdf = Pdf::loadView('pdf.transaksi-keuangan', [
            'transaksi' => $transaksi,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-transaksi-keuangan-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}

