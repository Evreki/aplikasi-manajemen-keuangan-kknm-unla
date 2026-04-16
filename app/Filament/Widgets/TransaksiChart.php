<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiKeuangan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Transaksi 7 Hari Terakhir';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo)->startOfDay();

            return [
                'date' => $date->format('d M'),
                'approved' => TransaksiKeuangan::where('status', 'approved')
                    ->whereDate('created_at', $date)
                    ->count(),
                'pending' => TransaksiKeuangan::where('status', 'pending')
                    ->whereDate('created_at', $date)
                    ->count(),
                'rejected' => TransaksiKeuangan::where('status', 'rejected')
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Disetujui',
                    'data' => $data->pluck('approved')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
                [
                    'label' => 'Pending',
                    'data' => $data->pluck('pending')->toArray(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => 'Ditolak',
                    'data' => $data->pluck('rejected')->toArray(),
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
