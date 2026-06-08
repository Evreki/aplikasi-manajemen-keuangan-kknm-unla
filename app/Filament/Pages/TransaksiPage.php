<?php

namespace App\Filament\Pages;

use App\Models\TransaksiKeuangan;
use App\Services\FonnteService;
use App\Services\WebKknCallbackService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class TransaksiPage extends Page
{
    use WithPagination;

    protected static string $view       = 'filament.pages.transaksi-page';
    protected static string $layout     = 'layouts.dashboard';
    protected static string $routePath  = '/transaksi';
    protected static ?string $navigationIcon  = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transaksi Keuangan';
    protected static ?string $title           = 'Transaksi Keuangan';
    protected static ?int    $navigationSort  = 1;

    // ---------- Filter & Search ----------
    public string $filterStatus = 'all';
    public string $filterYear   = 'all';
    public string $search       = '';

    // ---------- View Detail Modal ----------
    public bool $showViewModal    = false;
    public ?int  $viewId          = null;

    // ---------- Approve Modal ----------
    public bool $showApproveModal = false;
    public ?int  $approveId       = null;

    // ---------- Reject Modal ----------
    public bool   $showRejectModal = false;
    public ?int    $rejectId       = null;
    public string  $alasanTolak   = '';

    // ---------- Edit Modal ----------
    public bool $showEditModal = false;
    public ?int $editId = null;
    public array $editData = [
        'kkn_pembayaran_id' => '',
        'nim' => '',
        'nama_mahasiswa' => '',
        'no_telepon' => '',
        'total_bayar' => '',
        'status' => 'pending',
    ];

    // ---------- Navigation Badge ----------
    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = TransaksiKeuangan::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // ---------- Computed: paginated list ----------
    #[Computed]
    public function transaksi()
    {
        return TransaksiKeuangan::query()
            ->when($this->search, function ($q) {
                $q->where('nim', 'like', "%{$this->search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$this->search}%")
                  ->orWhere('no_telepon', 'like', "%{$this->search}%");
            })
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterYear !== 'all', function ($q) {
                $q->whereYear('waktu_transfer', $this->filterYear);
            })
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function availableYears()
    {
        $years = TransaksiKeuangan::query()
            ->whereNotNull('waktu_transfer')
            ->selectRaw('YEAR(waktu_transfer) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return !empty($years) ? $years : [date('Y')];
    }

    // ---------- Computed: selected record for view modal ----------
    #[Computed]
    public function selectedRecord(): ?TransaksiKeuangan
    {
        return $this->viewId ? TransaksiKeuangan::find($this->viewId) : null;
    }

    // ---------- Computed: stats ----------
    #[Computed]
    public function stats(): array
    {
        $query = TransaksiKeuangan::query()
            ->when($this->filterYear !== 'all', function ($q) {
                $q->whereYear('waktu_transfer', $this->filterYear);
            });

        return [
            'total'    => (clone $query)->count(),
            'pending'  => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'pemasukan'=> (clone $query)->where('status', 'approved')->where('is_kip', false)->sum('total_bayar'),
        ];
    }

    // ---------- Filter ----------
    public function setFilter(string $status): void
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterYear(): void
    {
        $this->resetPage();
    }

    // ---------- View Detail ----------
    public function openView(int $id): void
    {
        $this->viewId        = $id;
        $this->showViewModal = true;
    }

    // ---------- Edit Transaksi ----------
    public function openEdit(int $id): void
    {
        $record = TransaksiKeuangan::find($id);
        if ($record) {
            $this->editId = $id;
            $this->editData = [
                'kkn_pembayaran_id' => $record->kkn_pembayaran_id,
                'nim' => $record->nim,
                'nama_mahasiswa' => $record->nama_mahasiswa,
                'no_telepon' => $record->no_telepon,
                'total_bayar' => $record->total_bayar,
                'status' => $record->status,
            ];
            $this->showEditModal = true;
        }
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editData.kkn_pembayaran_id' => 'required|numeric',
            'editData.nim' => 'required|string|max:255',
            'editData.nama_mahasiswa' => 'required|string|max:255',
            'editData.total_bayar' => 'required|numeric',
            'editData.status' => 'required|in:pending,approved,rejected',
        ], [
            'editData.kkn_pembayaran_id.required' => 'ID Pembayaran wajib diisi.',
            'editData.nim.required' => 'NIM wajib diisi.',
            'editData.nama_mahasiswa.required' => 'Nama wajib diisi.',
            'editData.total_bayar.required' => 'Total bayar wajib diisi.',
        ]);

        $record = TransaksiKeuangan::find($this->editId);
        if ($record) {
            $record->update($this->editData);
            Notification::make()->title('Data riwayat transaksi berhasil diupdate.')->success()->send();
        }

        $this->closeModals();
        unset($this->transaksi, $this->stats);
    }

    // ---------- Approve ----------
    public function openApprove(int $id): void
    {
        $this->approveId        = $id;
        $this->showApproveModal = true;
    }

    public function doApprove(): void
    {
        $record = TransaksiKeuangan::find($this->approveId);

        if (!$record || $record->status !== 'pending') {
            Notification::make()->title('Transaksi tidak valid atau sudah diproses.')->warning()->send();
            $this->closeModals();
            return;
        }

        $record->update([
            'status' => 'approved',
            'verified_by' => auth()->id(),
            'alasan_penolakan' => null
        ]);

        $fonnte = new FonnteService();
        $webKkn = new WebKknCallbackService();

        if (!empty($record->no_telepon)) {
            $message  = $fonnte->buildApproveMessage([
                'nama_mahasiswa'    => $record->nama_mahasiswa,
                'kkn_pembayaran_id' => $record->kkn_pembayaran_id,
                'total_bayar'       => $record->total_bayar,
            ]);
            $waResult = $fonnte->sendMessage($record->no_telepon, $message);

            Notification::make()
                ->title($waResult['success'] ? 'WA Terkirim via Fonnte' : 'Gagal Kirim WA: ' . ($waResult['error'] ?? ''))
                ->{$waResult['success'] ? 'success' : 'warning'}()
                ->send();
        }

        $cbResult = $webKkn->sendApproveCallback($record->kkn_pembayaran_id, $record->nim);

        Notification::make()
            ->title($cbResult['success'] ? 'Sinkronisasi Web KKN Sukses!' : 'Gagal Sinkron Web KKN')
            ->{$cbResult['success'] ? 'success' : 'danger'}()
            ->send();

        $this->closeModals();
        unset($this->transaksi, $this->stats);
    }

    // ---------- Reject ----------
    public function openReject(int $id): void
    {
        $this->rejectId        = $id;
        $this->showRejectModal = true;
    }

    public function doReject(): void
    {
        $this->validate(['alasanTolak' => 'required|min:5'], [
            'alasanTolak.required' => 'Alasan penolakan wajib diisi.',
            'alasanTolak.min'      => 'Alasan minimal 5 karakter.',
        ]);

        $record = TransaksiKeuangan::find($this->rejectId);

        if (!$record || $record->status !== 'pending') {
            Notification::make()->title('Transaksi tidak valid atau sudah diproses.')->warning()->send();
            $this->closeModals();
            return;
        }

        $record->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'alasan_penolakan' => $this->alasanTolak
        ]);

        $fonnte = new FonnteService();
        $webKkn = new WebKknCallbackService();

        if (!empty($record->no_telepon)) {
            $message  = $fonnte->buildRejectMessage([
                'nama_mahasiswa'    => $record->nama_mahasiswa,
                'kkn_pembayaran_id' => $record->kkn_pembayaran_id,
                'total_bayar'       => $record->total_bayar,
            ], $this->alasanTolak);
            $waResult = $fonnte->sendMessage($record->no_telepon, $message);

            Notification::make()
                ->title($waResult['success'] ? 'WA Penolakan Terkirim' : 'Gagal Kirim WA')
                ->{$waResult['success'] ? 'success' : 'warning'}()
                ->send();
        }

        $cbResult = $webKkn->sendRejectCallback($record->kkn_pembayaran_id, $record->nim, $this->alasanTolak);

        Notification::make()
            ->title($cbResult['success'] ? 'Akses Upload Ulang Dibuka' : 'Gagal Sinkron Web KKN')
            ->{$cbResult['success'] ? 'success' : 'danger'}()
            ->send();

        $this->closeModals();
        unset($this->transaksi, $this->stats);
    }

    // ---------- Close all modals ----------
    public function closeModals(): void
    {
        $this->showViewModal    = false;
        $this->showApproveModal = false;
        $this->showRejectModal  = false;
        $this->showEditModal    = false;
        $this->viewId           = null;
        $this->approveId        = null;
        $this->rejectId         = null;
        $this->editId           = null;
        $this->alasanTolak      = '';
    }

    // ---------- Exports ----------
    private function getExportData()
    {
        return TransaksiKeuangan::query()
            ->when($this->search, function ($q) {
                $q->where('nim', 'like', "%{$this->search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$this->search}%")
                  ->orWhere('no_telepon', 'like', "%{$this->search}%");
            })
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterYear !== 'all', function ($q) {
                $q->whereYear('waktu_transfer', $this->filterYear);
            })
            ->latest()
            ->get();
    }

    public function exportPdf()
    {
        $data = $this->getExportData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.transaksi-keuangan', [
            'transaksi' => $data,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');
        
        return response()->streamDownload(fn () => print($pdf->output()), 'transaksi-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportCsv()
    {
        $data = $this->getExportData();
        $csvHeader = ['ID', 'NIM', 'Nama Mahasiswa', 'No WA', 'Nominal', 'Status', 'Waktu Transfer', 'Waktu Upload'];
        
        return response()->streamDownload(function () use ($data, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($data as $row) {
                $timeToFix = $row->waktu_transfer ?? $row->created_at;
                fputcsv($file, [
                    $row->kkn_pembayaran_id,
                    $row->nim,
                    $row->nama_mahasiswa,
                    $row->no_telepon ?: '-',
                    $row->is_kip ? 'KIP/Kerja Sama' : $row->total_bayar,
                    strtoupper($row->status),
                    $timeToFix->format('Y-m-d H:i:s'),
                    $row->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        }, 'transaksi-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportExcel()
    {
        $data = $this->getExportData();
        $html = '<table border="1"><tr><th>ID</th><th>NIM</th><th>Nama Mahasiswa</th><th>No WA</th><th>Nominal</th><th>Status</th><th>Waktu Transfer</th><th>Waktu Upload</th></tr>';
        foreach ($data as $row) {
            $timeToFix = $row->waktu_transfer ?? $row->created_at;
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                $row->kkn_pembayaran_id,
                $row->nim,
                $row->nama_mahasiswa,
                $row->no_telepon ?: '-',
                $row->is_kip ? 'KIP/Kerja Sama' : $row->total_bayar,
                strtoupper($row->status),
                $timeToFix->format('Y-m-d H:i:s'),
                $row->created_at->format('Y-m-d H:i:s')
            );
        }
        $html .= '</table>';

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, 'transaksi-'.now()->format('Ymd-His').'.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }
}
