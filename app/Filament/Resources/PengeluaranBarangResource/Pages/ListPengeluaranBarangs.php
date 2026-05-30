<?php

namespace App\Filament\Resources\PengeluaranBarangResource\Pages;

use App\Filament\Resources\PengeluaranBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengeluaranBarangs extends ListRecords
{
    protected static string $resource = PengeluaranBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
