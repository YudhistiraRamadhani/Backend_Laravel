<?php

namespace App\Filament\Resources\PengeluaranBarangResource\Pages;

use App\Filament\Resources\PengeluaranBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengeluaranBarang extends EditRecord
{
    protected static string $resource = PengeluaranBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
