<?php

namespace App\Filament\Resources\PemasukanBarangResource\Pages;

use App\Filament\Resources\PemasukanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPemasukanBarangs extends ListRecords
{
    protected static string $resource = PemasukanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
