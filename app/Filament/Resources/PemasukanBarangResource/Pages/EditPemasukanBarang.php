<?php

namespace App\Filament\Resources\PemasukanBarangResource\Pages;

use App\Filament\Resources\PemasukanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPemasukanBarang extends EditRecord
{
    protected static string $resource = PemasukanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
