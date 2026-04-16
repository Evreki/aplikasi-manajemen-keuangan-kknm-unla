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
    protected static ?string $navigationLabel = 'Transaksi Keuangan';

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = TransaksiKeuangan::where('status', 'pending')->count();
        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

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
                TextColumn::make('nim')->label('Mahasiswa')->description(fn($record) => $record->nama_mahasiswa)->searchable(),
                TextColumn::make('no_telepon')->label('No WA')->icon('heroicon-o-phone'),
                ImageColumn::make('bukti_pembayaran_path')->label('Bukti')->disk('public')->square()->height(50),
                TextColumn::make('total_bayar')->money('IDR')->label('Nominal'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
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
                // 1. TOMBOL APPROVE (Menggunakan Fonnte API)
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

                        // 2. Kirim WA via Fonnte
                        $fonnte = new \App\Services\FonnteService();
                        $webKkn = new \App\Services\WebKknCallbackService();

                        if (!empty($record->no_telepon)) {
                            $message = $fonnte->buildApproveMessage([
                                'nama_mahasiswa' => $record->nama_mahasiswa,
                                'kkn_pembayaran_id' => $record->kkn_pembayaran_id,
                                'total_bayar' => $record->total_bayar,
                            ]);

                            $waResult = $fonnte->sendMessage($record->no_telepon, $message);

                            if ($waResult['success']) {
                                Notification::make()->title('WA Terkirim via Fonnte')->success()->send();
                            } else {
                                Notification::make()->title('Gagal Kirim WA: ' . ($waResult['error'] ?? 'Unknown'))->warning()->send();
                            }
                        }

                        // 3. Callback ke Web KKN
                        $callbackResult = $webKkn->sendApproveCallback(
                            $record->kkn_pembayaran_id,
                            $record->nim
                        );

                        if ($callbackResult['success']) {
                            Notification::make()->title('Sinkronisasi Web KKN Sukses!')->success()->send();
                        } else {
                            Notification::make()->title('Gagal Sinkron Web KKN')->danger()->send();
                        }
                    }),

                // ==========================================================
                // 2. TOMBOL TOLAK (Menggunakan Fonnte API)
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

                        // 2. Kirim WA via Fonnte
                        $fonnte = new \App\Services\FonnteService();
                        $webKkn = new \App\Services\WebKknCallbackService();

                        if (!empty($record->no_telepon)) {
                            $message = $fonnte->buildRejectMessage([
                                'nama_mahasiswa' => $record->nama_mahasiswa,
                                'kkn_pembayaran_id' => $record->kkn_pembayaran_id,
                                'total_bayar' => $record->total_bayar,
                            ], $data['alasan']);

                            $waResult = $fonnte->sendMessage($record->no_telepon, $message);

                            if ($waResult['success']) {
                                Notification::make()->title('WA Penolakan Terkirim')->success()->send();
                            } else {
                                Notification::make()->title('Gagal Kirim WA')->warning()->send();
                            }
                        }

                        // 3. Callback ke Web KKN (buka akses upload ulang)
                        $callbackResult = $webKkn->sendRejectCallback(
                            $record->kkn_pembayaran_id,
                            $record->nim,
                            $data['alasan']
                        );

                        if ($callbackResult['success']) {
                            Notification::make()->title('Akses Upload Ulang Dibuka')->success()->send();
                        } else {
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
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiKeuangans::route('/'),
            'create' => Pages\CreateTransaksiKeuangan::route('/create'),
            'edit' => Pages\EditTransaksiKeuangan::route('/{record}/edit'),
        ];
    }
}
