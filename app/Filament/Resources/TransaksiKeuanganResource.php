<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiKeuanganResource\Pages;
use App\Models\TransaksiKeuangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransaksiKeuanganResource extends Resource
{
    protected static ?string $model = TransaksiKeuangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kkn_pembayaran_id')->required()->numeric(),
                Forms\Components\TextInput::make('nim')->required()->maxLength(255),
                Forms\Components\TextInput::make('nama_mahasiswa')->required()->maxLength(255),
                Forms\Components\TextInput::make('no_telepon')->maxLength(255),
                Forms\Components\TextInput::make('total_bayar')->required()->numeric(),
                Forms\Components\TextInput::make('bukti_pembayaran_path')->disabled(),
                Forms\Components\TextInput::make('status')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y, H:i')->sortable(),
                TextColumn::make('nim')->label('Mahasiswa')->description(fn ($record) => $record->nama_mahasiswa)->searchable(),
                TextColumn::make('no_telepon')->label('No WA')->icon('heroicon-o-phone'),
                ImageColumn::make('bukti_pembayaran_path')->label('Bukti')->disk('public')->square()->height(50),
                TextColumn::make('total_bayar')->money('IDR')->label('Nominal'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
            ])
            ->recordUrl(null) // Disable default row link to edit page
            ->recordAction('view')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye'),

                Tables\Actions\EditAction::make(),

                // ==========================================================
                // 1. TOMBOL APPROVE (Template Profesional)
                // ==========================================================
                Action::make('approve_wa')
                    ->label('Approve & WA')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pembayaran')
                    ->modalDescription('Yakin? Notifikasi WA akan dikirim ke mahasiswa.')
                    ->action(function ($record) {

                        // 1. Update Status Lokal
                        $record->update(['status' => 'approved']);

                        // 2. Kirim WA (Template Baru)
                        try {
                            $nomorTujuan = $record->no_telepon;

                            if (!empty($nomorTujuan)) {
                                // Format Rupiah
                                $rupiah = number_format($record->total_bayar, 0, ',', '.');

                                // ISI PESAN APPROVE
                                $pesan = "Halo {$record->nama_mahasiswa},\n\n" .
                                         "Selamat! Pembayaran KKNM Anda telah DISETUJUI. ✅\n\n" .
                                         "ID Pembayaran: #{$record->kkn_pembayaran_id}\n" .
                                         "Jumlah: Rp {$rupiah}\n\n" .
                                         "Catatan Admin: ACC Keuangan, silahkan lanjut daftar\n\n" .
                                         "Anda sekarang dapat melanjutkan ke tahap berikutnya yaitu mengisi formulir pendaftaran KKNM di http://kknm.unla.ac.id/pendaftaran .\n\n" .
                                         "Login di aplikasi untuk melanjutkan pendaftaran.\n\n" .
                                         "Terima kasih.";

                                Http::post('http://localhost:3000/send-message', [
                                    'number' => $nomorTujuan,
                                    'message' => $pesan,
                                ]);

                                Notification::make()->title('WA Terkirim')->success()->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()->title('Gagal Kirim WA')->warning()->send();
                        }

                        // 3. Callback ke Web KKN
                        try {
                            Http::post('http://127.0.0.1:8000/api/payment-callback', [
                                'kkn_pembayaran_id' => $record->kkn_pembayaran_id,
                                'status' => 'approved',
                            ]);
                            Notification::make()->title('Sinkronisasi Sukses!')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Gagal Sinkron Web KKN')->danger()->send();
                        }
                    }),

                // ==========================================================
                // 2. TOMBOL TOLAK (Template Profesional)
                // ==========================================================
                Action::make('reject_wa')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pembayaran')
                    ->form([
                        Forms\Components\Textarea::make('alasan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Contoh: Foto buram, Nominal kurang, Bukan bukti transfer.')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {

                        // 1. Update Status Lokal
                        $record->update(['status' => 'rejected']);

                        // 2. Kirim WA (Template Baru)
                        try {
                            $nomorTujuan = $record->no_telepon;

                            if(!empty($nomorTujuan)) {
                                // Format Rupiah
                                $rupiah = number_format($record->total_bayar, 0, ',', '.');

                                // ISI PESAN TOLAK
                                $pesan = "Halo {$record->nama_mahasiswa},\n\n" .
                                         "Mohon Maaf, Pembayaran KKNM Anda DITOLAK. ❌\n\n" .
                                         "ID Pembayaran: #{$record->kkn_pembayaran_id}\n" .
                                         "Jumlah: Rp {$rupiah}\n\n" .
                                         "Catatan Admin: {$data['alasan']}\n\n" .
                                         "Silakan LOGIN KEMBALI di http://kknm.unla.ac.id/login untuk mengupload ulang bukti pembayaran yang benar.\n\n" .
                                         "Terima kasih.";

                                Http::post('http://localhost:3000/send-message', [
                                    'number' => $nomorTujuan,
                                    'message' => $pesan,
                                ]);
                                Notification::make()->title('WA Penolakan Terkirim')->success()->send();
                            }
                        } catch (\Exception $e) {
                           Notification::make()->title('Gagal Kirim WA')->warning()->send();
                        }

                        // 3. Callback ke Web KKN
                        try {
                            Http::post('http://127.0.0.1:8000/api/payment-callback', [
                                'kkn_pembayaran_id' => $record->kkn_pembayaran_id,
                                'status' => 'rejected',
                                'catatan' => $data['alasan']
                            ]);

                            Notification::make()->title('Akses Upload Ulang Dibuka')->success()->send();

                        } catch (\Exception $e) {
                            Notification::make()->title('Gagal Sinkron Web KKN')->danger()->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                ImageEntry::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->height(320)
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('nim')
                    ->label('NIM'),

                TextEntry::make('nama_mahasiswa')
                    ->label('Nama Mahasiswa'),

                TextEntry::make('no_telepon')
                    ->label('No WA'),

                TextEntry::make('total_bayar')
                    ->label('Nominal')
                    ->money('IDR'),

                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [
            'index' => Pages\ListTransaksiKeuangans::route('/'),
            'create' => Pages\CreateTransaksiKeuangan::route('/create'),
            'edit' => Pages\EditTransaksiKeuangan::route('/{record}/edit'),
        ];
    }
}
