<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiKeuangan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTransaksi = TransaksiKeuangan::count();
        $pending = TransaksiKeuangan::where('status', 'pending')->count();
        $approved = TransaksiKeuangan::where('status', 'approved')->count();
        $rejected = TransaksiKeuangan::where('status', 'rejected')->count();

        $totalPemasukan = TransaksiKeuangan::where('status', 'approved')->sum('total_bayar');
        $totalPending = TransaksiKeuangan::where('status', 'pending')->sum('total_bayar');

        return [
            Stat::make('Total Transaksi', $totalTransaksi)
                ->description('Semua pembayaran masuk')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Menunggu Verifikasi', $pending)
                ->description('Perlu diproses')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Disetujui', $approved)
                ->description('Pembayaran valid')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Ditolak', $rejected)
                ->description('Pembayaran ditolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                ->description('Pembayaran disetujui')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Pending Amount', 'Rp ' . number_format($totalPending, 0, ',', '.'))
                ->description('Menunggu verifikasi')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
