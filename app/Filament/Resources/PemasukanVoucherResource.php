<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemasukanVoucherResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PemasukanVoucherResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationLabel = 'Pemasukan Voucher';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Transaksi Masuk';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('jenis_barang', 'Voucher')
            ->where('jenis_transaksi', 'Pemasukan');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Input Pemasukan Voucher')
                    ->description('Ketik nama voucher secara manual untuk menghindari error dropdown.')
                    ->schema([
                        // GANTI KE TEXT INPUT (KETIK MANUAL)
                        Forms\Components\TextInput::make('Nama_Barang')
                            ->label('Nama Voucher')
                            ->placeholder('Contoh: Voucher Game 10k')
                            ->required(),

                        Forms\Components\TextInput::make('Harga')
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

                        // Logika otomatis agar tersimpan sebagai Voucher dan Pemasukan
                        Forms\Components\Hidden::make('jenis_transaksi')->default('Pemasukan'),
                        Forms\Components\Hidden::make('jenis_barang')->default('Voucher'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('Nama_Barang')->label('Nama Voucher')->searchable(),
                Tables\Columns\TextColumn::make('Jumlah')->alignCenter(),
                Tables\Columns\TextColumn::make('Harga')->money('idr'),
            ])
            ->defaultSort('Tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemasukanVouchers::route('/'),
            'create' => Pages\CreatePemasukanVoucher::route('/create'),
        ];
    }
}
