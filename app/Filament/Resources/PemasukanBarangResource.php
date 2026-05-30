<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemasukanBarangResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PemasukanBarangResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationLabel = 'Pemasukan Barang';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Transaksi Masuk';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('jenis_barang', 'Barang')
            ->where('jenis_transaksi', 'Pemasukan');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Input Pemasukan Barang')
                    ->description('Ketik nama barang secara manual.')
                    ->schema([
                        // GANTI KE TEXT INPUT (KETIK MANUAL)
                        Forms\Components\TextInput::make('Nama_Barang')
                            ->label('Nama Barang')
                            ->placeholder('Contoh: Tempered Glass iPhone')
                            ->required(),

                        Forms\Components\TextInput::make('Harga')
                            ->label('Harga Beli')
                            ->numeric()
                            ->required()
                            ->prefix('Rp'),

                        Forms\Components\TextInput::make('Jumlah')
                            ->numeric()
                            ->required()
                            ->default(1),

                        Forms\Components\DatePicker::make('Tanggal')
                            ->default(now())
                            ->required(),

                        Forms\Components\Hidden::make('jenis_transaksi')->default('Pemasukan'),
                        Forms\Components\Hidden::make('jenis_barang')->default('Barang'),

                        Forms\Components\TextInput::make('nama_supplier')
                            ->label('Nama Supplier')
                            ->placeholder('Opsional'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('Nama_Barang')->searchable(),
                Tables\Columns\TextColumn::make('Jumlah')->alignCenter(),
                Tables\Columns\TextColumn::make('Harga')->money('idr'),
                Tables\Columns\TextColumn::make('nama_supplier')->label('Supplier'),
            ])
            ->defaultSort('Tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemasukanBarangs::route('/'),
            'create' => Pages\CreatePemasukanBarang::route('/create'),
            'edit' => Pages\EditPemasukanBarang::route('/{record}/edit')
        ];
    }
}
