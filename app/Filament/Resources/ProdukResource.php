<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $pluralLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\TextInput::make('Nama_Barang')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('jenis_barang')
                            ->label('Kategori')
                            ->options([
                                'Produk' => 'Produk',
                                'Kartu Provider' => 'Kartu Provider',
                                'Voucher' => 'Voucher',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('Harga')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('Stok')
                            ->label('Stok')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
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
                Tables\Columns\BadgeColumn::make('jenis_barang')
                    ->label('Kategori')
                    ->color(fn (string $state): string => match ($state) {
                        'Produk' => 'info',
                        'Kartu Provider' => 'warning',
                        'Voucher' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('Harga')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('Stok')
                    ->label('Stok')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter Kategori dengan 3 pilihan
                Tables\Filters\SelectFilter::make('jenis_barang')
                    ->label('Kategori')
                    ->options([
                        'Produk' => 'Produk',
                        'Kartu Provider' => 'Kartu Provider',
                        'Voucher' => 'Voucher',
                    ]),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
