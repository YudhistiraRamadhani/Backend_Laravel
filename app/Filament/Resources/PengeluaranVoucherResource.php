<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengeluaranVoucherResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengeluaranVoucherResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationLabel = 'Pengeluaran Voucher';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Transaksi Keluar';

    public static function getEloquentQuery(): Builder
    {
        // Menampilkan data Voucher dan Kartu Provider yang merupakan Pengeluaran
        return parent::getEloquentQuery()
            ->where('jenis_transaksi', 'Pengeluaran')
            ->whereIn('jenis_barang', ['Voucher', 'Kartu Provider']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('jenis_barang')
                        ->label('Kategori')
                        ->options([
                            'Voucher' => 'Voucher',
                            'Kartu Provider' => 'Kartu Provider',
                        ])->required(),
                    Forms\Components\TextInput::make('Nama_Barang')
                        ->label('Nama Voucher/Kartu')
                        ->required(),
                    Forms\Components\TextInput::make('nama_supplier')
                        ->label('Supplier'),
                    Forms\Components\TextInput::make('Harga')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    Forms\Components\TextInput::make('Jumlah')
                        ->numeric()
                        ->required(),
                    Forms\Components\DatePicker::make('Tanggal')
                        ->default(now())
                        ->required(),
                    Forms\Components\Hidden::make('jenis_transaksi')->default('Pengeluaran'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('Nama_Barang')->label('Nama Barang/Voucher'),
                Tables\Columns\BadgeColumn::make('jenis_barang')
                    ->label('Kategori')
                    ->colors([
                        'warning' => 'Voucher',
                        'success' => 'Kartu Provider',
                    ]),
                Tables\Columns\TextColumn::make('Jumlah'),
                Tables\Columns\TextColumn::make('Harga')->money('idr'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengeluaranVouchers::route('/'),
            'create' => Pages\CreatePengeluaranVoucher::route('/create'),
            'edit' => Pages\EditPengeluaranVoucher::route('/{record}/edit'),
        ];
    }
}
