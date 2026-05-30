<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataPelangganResource\Pages;
use App\Filament\Resources\DataPelangganResource\RelationManagers;
use App\Models\DataPelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataPelangganResource extends Resource
{
     public static bool $public = true;
    protected static ?string $model = DataPelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                //
                ->schema([
                    Forms\Components\TextInput::make('nama_pelanggan')
                        ->label('Nama Pelanggan')
                        ->required()
                        ->maxLength(255),

                    // Field No Whatsapp
                    Forms\Components\TextInput::make('no_whatsapp')
                        ->label('No. WhatsApp')
                        ->tel() // Menggunakan input type tel
                        ->required()
                        ->maxLength(255),

                    // Field Pesan Notifikasi
                    Forms\Components\TextInput::make('pesannotifikasi')
                        ->label('Pesan Notifikasi')
                        ->required()
                        ->maxLength(255),

                    // Field Tanggal Notifikasi
                    // Menggunakan DateTimePicker karena di gambar kolomnya bertipe timestamp
                    Forms\Components\DateTimePicker::make('tanggal_notifikasi')
                        ->label('Tanggal Notifikasi')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nama_pelanggan')->label('Nama Pelanggan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('no_whatsapp')->label('No. WhatsApp')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pesannotifikasi')->label('Pesan Notifikasi')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tanggal_notifikasi')->label('Tanggal Notifikasi')->dateTime()->searchable()

            ])
            ->filters([
                //
            ])
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataPelanggans::route('/'),
            'create' => Pages\CreateDataPelanggan::route('/create'),
            'edit' => Pages\EditDataPelanggan::route('/{record}/edit'),
        ];
    }
}
