<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanPenjualanResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Summarizers\Sum;

class LaporanPenjualanResource extends Resource
{
    // Menggunakan model Transaksi sebagai sumber data laporan
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
   

    // Judul plural (untuk halaman index)
    protected static ?string $pluralLabel = 'Laporan Penjualan';

    // Judul singular
    protected static ?string $modelLabel = 'Laporan Penjualan';

    public static function getEloquentQuery(): Builder
    {
        // Memastikan hanya data dengan jenis_transaksi "Pemasukan" yang tampil
        return parent::getEloquentQuery()->where('jenis_transaksi', 'Pemasukan');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Edit Data Penjualan')
                    ->schema([
                        Forms\Components\TextInput::make('Nama_Barang')
                            ->label('Nama Barang/Voucher')
                            ->required(),
                        Forms\Components\TextInput::make('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('Jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->required(),
                        Forms\Components\DatePicker::make('Tanggal')
                            ->required(),
                        Forms\Components\Select::make('jenis_barang')
                            ->label('Kategori')
                            ->options([
                                'Barang' => 'Barang',
                                'Voucher' => 'Voucher',
                            ])
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Nama_Barang')
                    ->label('Nama Barang/Voucher')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('jenis_barang')
                    ->label('Kategori')
                    ->color(fn (string $state): string => match ($state) {
                        'Barang' => 'info',
                        'Voucher' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('Jumlah')
                    ->label('Jumlah')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('Harga')
                    ->label('Harga Satuan')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('total_penjualan')
                    ->label('Total Penjualan')
                    ->getStateUsing(fn ($record) => $record->Harga * $record->Jumlah)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->filters([
                // Filter Bulan
                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options([
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value']) {
                            return $query->whereMonth('Tanggal', $data['value']);
                        }
                        return $query;
                    }),

                // Filter Tahun menggunakan Select (Year Picker)
                Filter::make('tahun')
                    ->label('Tahun')
                    ->form([
                        Select::make('tahun')
                            ->label('Pilih Tahun')
                            ->options(function () {
                                $years = [];
                                $currentYear = now()->year;
                                for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->default(now()->year)
                            ->placeholder('Pilih Tahun'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['tahun'])) {
                            return $query->whereYear('Tanggal', $data['tahun']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('Tanggal', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanPenjualans::route('/'),
            'edit' => Pages\EditLaporanPenjualan::route('/{record}/edit'),
        ];
    }
}
