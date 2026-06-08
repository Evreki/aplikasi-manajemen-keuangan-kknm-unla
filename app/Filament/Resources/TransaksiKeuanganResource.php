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

    protected static ?string $navigationIcon  = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transaksi Keuangan';

    // Navigasi dialihkan ke TransaksiPage yang sudah dikustomisasi
    protected static bool $shouldRegisterNavigation = false;

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
                Forms\Components\DateTimePicker::make('waktu_transfer')->label('Waktu Transfer (Tercantum di Bukti)'),
                Forms\Components\Toggle::make('is_kip')->label('Jalur KIP / Kerja Sama')->inline(false),
                Forms\Components\TextInput::make('bukti_pembayaran_path')->label('Bukti Pembayaran / Kartu KIP / Kerja Sama')->disabled(),
                Forms\Components\TextInput::make('status')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('waktu_transfer')->label('Waktu Transfer')->dateTime('d M Y, H:i')->sortable()->default('Belum Diset'),
                TextColumn::make('created_at')->label('Waktu Upload')->dateTime('d M Y, H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nim')->label('Mahasiswa')->description(fn($record) => $record->nama_mahasiswa)->searchable(),
                TextColumn::make('no_telepon')->label('No WA')->icon('heroicon-o-phone'),
                ImageColumn::make('bukti_pembayaran_path')
                    ->label('Bukti')
                    ->disk('public')
                    ->square()
                    ->height(50),
                TextColumn::make('total_bayar')->money('IDR')->label('Nominal'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                
                TextColumn::make('verifier.name')
                    ->label('Admin')
                    ->icon('heroicon-m-check-badge')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(null) // Disable default row link to edit page
            ->recordAction('view')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye'),

                // Zoom Action Button
                Tables\Actions\Action::make('perbesar_foto')
                    ->label('Zoom')
                    ->icon('heroicon-m-magnifying-glass-plus')
                    ->color('info')
                    ->modalHeading('Bukti Pembayaran')
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::Screen)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalContent(fn ($record) => view('components.image-modal', ['imageUrl' => \Illuminate\Support\Facades\Storage::disk('public')->url($record->bukti_pembayaran_path)])),

                // Approve Action
                Tables\Actions\Action::make('approve')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Transaksi')
                    ->modalDescription('Anda yakin transaksi ini valid?')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'verified_by' => auth()->id(),
                            'alasan_penolakan' => null,
                        ]);
                        Notification::make()->title('Transaksi Disetujui')->success()->send();
                    })
                    ->visible(fn($record) => $record->status === 'pending'),

                // Reject Action
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Struk tidak jelas atau nominal transfer kurang.'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'verified_by' => auth()->id(),
                            'alasan_penolakan' => $data['alasan_penolakan'],
                        ]);
                        Notification::make()->title('Transaksi Ditolak')->danger()->send();
                    })
                    ->visible(fn($record) => in_array($record->status, ['pending', 'approved'])),
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
                    ->label(fn($record) => $record->is_kip ? 'Kartu KIP / Kerja Sama' : 'Bukti Pembayaran / Struk')
                    ->disk('public')
                    ->height(320)
                    ->columnSpanFull(),

                \Filament\Infolists\Components\Actions::make([
                    \Filament\Infolists\Components\Actions\Action::make('perbesar_foto')
                        ->label('🔍 Perbesar Foto (Layar Penuh)')
                        ->color('info')
                        ->modalHeading('Bukti Pembayaran')
                        ->modalWidth(\Filament\Support\Enums\MaxWidth::Screen)
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->modalContent(fn ($record) => view('components.image-modal', ['imageUrl' => \Illuminate\Support\Facades\Storage::disk('public')->url($record->bukti_pembayaran_path)]))
                ])->columnSpanFull(),

                TextEntry::make('is_kip')
                    ->label('Jalur Pendaftaran')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'KIP / Kerja Sama' : 'Reguler')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->badge(),

                TextEntry::make('waktu_transfer')
                    ->label('Waktu Transfer Aktual')
                    ->dateTime('d M Y, H:i')
                    ->color('primary')
                    ->weight('bold'),

                TextEntry::make('created_at')
                    ->label('Waktu Gambar Diupload')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('nim')
                    ->label('NIM'),

                TextEntry::make('nama_mahasiswa')
                    ->label('Nama Mahasiswa'),

                TextEntry::make('no_telepon')
                    ->label('No WA'),

                TextEntry::make('keterangan_mahasiswa')
                    ->label('Keterangan / Presensi')
                    ->columnSpanFull()
                    ->placeholder('-'),

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
                
                TextEntry::make('verifier.name')
                    ->label('Diverifikasi Oleh')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->verified_by !== null),

                TextEntry::make('alasan_penolakan')
                    ->label('Alasan Penolakan')
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->visible(fn ($record) => $record->status === 'rejected' && $record->alasan_penolakan !== null)
                    ->columnSpanFull(),
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
