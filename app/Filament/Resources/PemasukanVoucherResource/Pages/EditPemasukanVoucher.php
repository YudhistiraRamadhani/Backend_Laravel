<?php

namespace App\Filament\Resources\PemasukanVoucherResource\Pages;

use App\Filament\Resources\PemasukanVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPemasukanVoucher extends EditRecord
{
    protected static string $resource = PemasukanVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
