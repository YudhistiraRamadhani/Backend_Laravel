<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengeluaranBarangResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengeluaranBarangResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationLabel = 'Pengeluaran Barang';
    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';
    protected static ?string $navigationGroup = 'Transaksi Keluar';

    public static function getEloquentQuery(): Builder
    {
        // PENTING: Filter ini memastikan data muncul di menu Pengeluaran
        return parent::getEloquentQuery()
            ->where('jenis_transaksi', 'Pengeluaran')
            ->where('jenis_barang', 'Barang');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('Nama_Barang')
                        ->required(),
                   Forms\Components\TextInput::make('Harga')
                        ->label('Harga')
                        ->numeric()
                        ->prefix('Rp '),


                    Forms\Components\TextInput::make('Jumlah')
                        ->numeric()
                        ->required(),
                    Forms\Components\DatePicker::make('Tanggal')
                        ->default(now())
                        ->required(),
                    // Field ini disembunyikan agar otomatis masuk ke kategori yang benar
                    Forms\Components\Hidden::make('jenis_transaksi')->default('Pengeluaran'),
                    Forms\Components\Hidden::make('jenis_barang')->default('Barang'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('Nama_Barang')->searchable(),
                Tables\Columns\TextColumn::make('Jumlah'),
           Tables\Columns\TextColumn::make('Harga')
    ->money('idr', true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengeluaranBarangs::route('/'),
            'create' => Pages\CreatePengeluaranBarang::route('/create'),
            'edit' => Pages\EditPengeluaranBarang::route('/{record}/edit'),
        ];
    }
}
