<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKeuanganResource\Pages;
use App\Models\LaporanKeuangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Summarizers\Summarizer;

class LaporanKeuanganResource extends Resource
{
    protected static ?string $model = LaporanKeuangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Laporan Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_supplier')
                            ->label('Nama Supplier')
                            ->placeholder('Opsional'),
                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('pendapatan')
                            ->label('Pendapatan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\TextInput::make('pengeluaran')
                            ->label('Pengeluaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_supplier')
                    ->label('Supplier')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->alignCenter()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->label('Total')
                    ),
                Tables\Columns\TextColumn::make('pendapatan')
                    ->label('Pendapatan')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->label('Total')
                        ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ),
                Tables\Columns\TextColumn::make('pengeluaran')
                    ->label('Pengeluaran')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('danger')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->label('Total')
                        ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ),
                Tables\Columns\TextColumn::make('laba_bersih')
                    ->label('')
                    ->getStateUsing(fn ($record) => null)
                    ->summarize(Summarizer::make()
                        ->label('')
                        ->using(function ($query) { // Builder type hint dihapus di sini untuk memperbaiki error
                            $totalPendapatan = $query->sum('pendapatan');
                            $totalPengeluaran = $query->sum('pengeluaran');
                            $totalLabaBersih = $totalPendapatan - $totalPengeluaran;

                            return $totalLabaBersih;
                        })
                        ->formatStateUsing(fn ($state) => $state >= 0
                            ? '✅ Total Laba Bersih: Rp ' . number_format($state, 0, ',', '.')
                            : '❌ Total Laba Bersih: -Rp ' . number_format(abs($state), 0, ',', '.'))
                    ),
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
                            return $query->whereMonth('tanggal', $data['value']);
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
                            return $query->whereYear('tanggal', $data['tahun']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeuangans::route('/'),
            'create' => Pages\CreateLaporanKeuangan::route('/create'),
            'edit' => Pages\EditLaporanKeuangan::route('/{record}/edit'),
        ];
    }
}
