<?php

namespace App\Filament\Resources\LaporanPenjualanResource\Pages;

use App\Filament\Resources\LaporanPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanPenjualan extends EditRecord
{
    protected static string $resource = LaporanPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Redirect kembali ke list setelah edit selesai
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
