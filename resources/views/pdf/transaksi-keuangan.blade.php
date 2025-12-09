<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Keuangan KKN</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2933; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 12px; color: #6b7280; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; }
        tr:nth-child(even) { background: #fafafa; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 11px; text-transform: uppercase; }
        .badge.approved { background: #dcfce7; color: #166534; }
        .badge.pending { background: #fef3c7; color: #92400e; }
        .badge.rejected { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 24px; font-size: 11px; text-align: right; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Laporan Transaksi Keuangan KKN</h1>
    <p class="subtitle">Diperbarui {{ $generatedAt->locale('id')->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Waktu</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>No. Telepon</th>
            <th>Nominal</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($transaksi as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $item->nim }}</td>
                <td>{{ $item->nama_mahasiswa }}</td>
                <td>{{ $item->no_telepon ?? '-' }}</td>
                <td>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                <td>
                    <span class="badge {{ $item->status }}">
                        {{ strtoupper($item->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">
                    Tidak ada data transaksi untuk ditampilkan.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak otomatis oleh sistem keuangan KKN.</p>
</body>
</html>

