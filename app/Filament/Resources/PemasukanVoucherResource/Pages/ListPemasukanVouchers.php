<?php

namespace App\Filament\Resources\PemasukanVoucherResource\Pages;

use App\Filament\Resources\PemasukanVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPemasukanVouchers extends ListRecords
{
    protected static string $resource = PemasukanVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
