<?php

namespace App\Filament\Resources\PengeluaranVoucherResource\Pages;

use App\Filament\Resources\PengeluaranVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengeluaranVouchers extends ListRecords
{
    protected static string $resource = PengeluaranVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
