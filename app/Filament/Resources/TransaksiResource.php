<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('Nama_Barang')
                            ->label('Nama Barang')
                            ->required(),

                        Forms\Components\TextInput::make('Harga')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\TextInput::make('Jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->required(),

                        Forms\Components\DatePicker::make('Tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('jenis_transaksi')
                            ->label('Jenis Transaksi')
                            ->options([
                                'Pemasukan' => 'Pemasukan',
                                'Pengeluaran' => 'Pengeluaran',
                            ])
                            ->required(),

                        Forms\Components\Select::make('jenis_barang')
                            ->label('Kategori')
                            ->options([
                                'Barang' => 'Barang',
                                'Voucher' => 'Voucher',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('nama_supplier')
                            ->label('Nama Supplier')
                            ->nullable(),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Nama_Barang')
                    ->label('Nama Barang')
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->where('Nama_Barang', 'ilike', "%{$search}%");
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('Harga')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('Jumlah')
                    ->label('Jumlah')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(fn ($record) => $record->Harga * $record->Jumlah)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('Tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('jenis_transaksi')
                    ->label('Tipe')
                    ->color(fn (string $state): string => match ($state) {
                        'Pemasukan' => 'success',
                        'Pengeluaran' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\BadgeColumn::make('jenis_barang')
                    ->label('Kategori')
                    ->color(fn (string $state): string => match ($state) {
                        'Barang' => 'info',
                        'Voucher' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_transaksi')
                    ->label('Jenis Transaksi')
                    ->options([
                        'Pemasukan' => 'Pemasukan',
                        'Pengeluaran' => 'Pengeluaran',
                    ]),

                Tables\Filters\SelectFilter::make('jenis_barang')
                    ->label('Kategori')
                    ->options([
                        'Barang' => 'Barang',
                        'Voucher' => 'Voucher',
                    ]),

                // Filter Tanggal
                Tables\Filters\Filter::make('tanggal_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('Tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('Tanggal', '<=', $date),
                            );
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
            ->defaultSort('Tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
}
