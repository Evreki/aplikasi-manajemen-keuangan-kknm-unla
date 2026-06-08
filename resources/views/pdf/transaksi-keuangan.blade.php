<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan KKNM UNLA</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #111827; margin: -10px; }
        .kop-surat { width: 100%; border: none; margin-bottom: 10px; }
        .kop-surat td { border: none; padding: 5px; }
        .logo { width: 90px; height: auto; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .kop-text h2 { margin: 0; font-size: 14px; letter-spacing: 1px; font-weight: normal; }
        .kop-text h1 { margin: 4px 0; font-size: 22px; font-weight: bold; color: #1e3a8a; }
        .kop-text h3 { margin: 0; font-size: 14px; font-weight: bold; }
        .kop-text p { margin: 4px 0 0 0; font-size: 10px; color: #4b5563; }
        .garis-tebal { border-top: 3px solid #000; margin-top: 10px; margin-bottom: 2px; }
        .garis-tipis { border-top: 1px solid #000; margin-bottom: 20px; }
        
        .laporan-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 10px; color: #6b7280; margin-bottom: 20px; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-data th, .table-data td { border: 1px solid #9ca3af; padding: 6px 8px; text-align: left; }
        .table-data th { background: #f3f4f6; font-weight: bold; color: #1f2937; text-align: center; text-transform: uppercase; font-size: 9px; }
        .table-data tr:nth-child(even) { background: #f9fafb; }
        
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 9px; text-transform: uppercase; font-weight: bold; }
        .badge.approved { color: #166534; background: transparent; }
        .badge.pending { color: #92400e; background: transparent; }
        .badge.rejected { color: #991b1b; background: transparent; }
        
        .ttd-box { width: 100%; margin-top: 40px; border: none; }
        .ttd-box td { border: none; padding: 0; }
        
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: left; font-size: 9px; font-style: italic; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 5px; }
    </style>
</head>
<body>
    
    <!-- KOP SURAT -->
    <table class="kop-surat">
        <tr>
            <td style="width: 100px; text-align: left; padding: 0;">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo UNLA" style="width: 90px; height: auto;">
                @endif
            </td>
            <td style="text-align: center; padding-right: 100px;" class="kop-text">
                <h2>YAYASAN PENDIDIKAN KEPEMUDAAN</h2>
                <h1>UNIVERSITAS LANGLANGBUANA</h1>
                <h3>PANITIA PELAKSANA KULIAH KERJA NYATA MAHASISWA (KKNM)</h3>
                <p>Jl. Karapitan No. 116, Cikawao, Kec. Lengkong, Kota Bandung, Jawa Barat 40261</p>
            </td>
        </tr>
    </table>
    <div class="garis-tebal"></div>
    <div class="garis-tipis"></div>
    
    <!-- JUDUL -->
    <div class="laporan-title">LAPORAN MUTASI & PEMBAYARAN KKNM</div>
    <div class="subtitle">Filter Status: Keseluruhan &nbsp;&nbsp;|&nbsp;&nbsp; Dicetak pada: {{ $generatedAt->locale('id')->translatedFormat('d F Y - H:i') }} WIB</div>

    <!-- TABEL DATA -->
    <table class="table-data">
        <thead>
        <tr>
            <th style="width: 3%;">NO</th>
            <th style="width: 14%;">WAKTU TRANSFER</th>
            <th style="width: 14%;">WAKTU UPLOAD</th>
            <th style="width: 12%;">NIM</th>
            <th style="width: 25%;">NAMA MAHASISWA</th>
            <th style="width: 12%;">NOMINAL</th>
            <th style="width: 10%;">STATUS</th>
        </tr>
        </thead>
        <tbody>
        @php $totalSemua = 0; @endphp
        @forelse($transaksi as $index => $item)
            @php 
                $timeToFix = $item->waktu_transfer ?? $item->created_at; 
                // Hitung total khusus yang approved saja DAN BUKAN KIP
                if($item->status == 'approved' && !$item->is_kip) {
                    $totalSemua += $item->total_bayar;
                }
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $timeToFix->format('d/m/Y H:i') }}</td>
                <td class="text-center" style="color: #6b7280;">{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                <td class="text-center" style="font-family: monospace;">{{ $item->nim }}</td>
                <td>
                    {{ $item->nama_mahasiswa }}
                    @if($item->is_kip)
                        <br><span style="font-weight:bold; color: #16a34a; font-size:8px;">[JALUR KIP / KERJA SAMA]</span>
                    @endif
                </td>
                <td class="text-right">
                    @if($item->is_kip)
                        <span style="font-weight:bold; color: #16a34a; font-style:italic;">JALUR KIP / KERJA SAMA</span>
                    @else
                        Rp {{ number_format($item->total_bayar, 0, ',', '.') }}
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge {{ $item->status }}">
                        {{ strtoupper($item->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">
                    Tidak ada data transaksi untuk ditampilkan
                </td>
            </tr>
        @endforelse
        </tbody>
        @if($transaksi->count() > 0)
        <tfoot>
            <tr>
                <th colspan="5" class="text-right" style="padding-right: 15px; font-size: 10px;">TOTAL PEMASUKAN DARI TRANSAKSI DISETUJUI (APPROVED)</th>
                <th colspan="2" class="text-left" style="font-size: 11px; background: #dcfce7; color: #166534;">Rp {{ number_format($totalSemua, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- TANDA TANGAN -->
    <table class="ttd-box">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <p style="margin: 0;">Bandung, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                <p style="margin: 0; font-weight: bold;">Bagian Keuangan / Bendahara KKNM</p>
                
                <br><br><br><br>
                
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Admin Keuangan' }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        * Dokumen ini dibuat otomatis oleh Sistem Aplikasi Keuangan KKNM Universitas Langlangbuana dan valid tanpa cap basah. <br>
        * Kode Cetak: {{ Str::upper(Str::random(8)) }} / {{ now()->format('YmdHis') }}
    </div>
</body>
</html>

