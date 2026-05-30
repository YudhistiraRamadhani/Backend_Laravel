<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PiutangResource\Pages;
use App\Models\Piutang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PiutangResource extends Resource
{
    protected static ?string $model = Piutang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make() // Menggunakan Section (pengganti Card di V3)
                    ->schema([
                        Forms\Components\TextInput::make('nama_pelanggan')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('jumlah_hutang')
                            ->label('Jumlah Hutang')
                            ->numeric()
                            ->prefix('Rp') // Menambah visual Rp di input
                            ->required(),

                        Forms\Components\TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('harga')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                '1' => 'Lunas',         // Menggunakan string '1' agar sinkron dengan tabel
                                '0' => 'Belum Lunas',   // Menggunakan string '0' agar sinkron dengan tabel
                            ])
                            ->required()
                            ->native(false), // Tampilan dropdown lebih modern
                    ])
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('nama_pelanggan')
                ->label('Nama Pelanggan')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('jumlah_hutang')
                ->label('Jumlah Hutang')
                // Menggunakan numeric untuk menghilangkan IDR bawaan
                ->numeric(decimalPlaces: 0, decimalSeparator: ',', thousandsSeparator: '.')
                ->prefix('Rp ')
                ->sortable(),

            Tables\Columns\TextColumn::make('nama_barang')
                ->label('Nama Barang')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('harga')
                ->label('Harga')
                // Menggunakan numeric untuk menghilangkan IDR bawaan
                ->numeric(decimalPlaces: 0, decimalSeparator: ',', thousandsSeparator: '.')
                ->prefix('Rp ')
                ->sortable(),

            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    '1', 'lunas' => 'Lunas',
                    '0', 'belum_bayar' => 'Belum Lunas',
                    default => $state,
                })
                ->color(fn (string $state): string => match ($state) {
                    '1', 'lunas' => 'success',
                    '0', 'belum_bayar' => 'danger',
                    default => 'gray',
                }),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPiutangs::route('/'),
            'create' => Pages\CreatePiutang::route('/create'),
            'edit' => Pages\EditPiutang::route('/{record}/edit'),
        ];
    }
}
