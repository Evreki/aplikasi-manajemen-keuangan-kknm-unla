<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiKeuangan;
use Filament\Widgets\ChartWidget;

class StatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $approved = TransaksiKeuangan::where('status', 'approved')->count();
        $pending = TransaksiKeuangan::where('status', 'pending')->count();
        $rejected = TransaksiKeuangan::where('status', 'rejected')->count();

        return [
            'datasets' => [
                [
                    'data' => [$approved, $pending, $rejected],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => ['Disetujui', 'Pending', 'Ditolak'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
