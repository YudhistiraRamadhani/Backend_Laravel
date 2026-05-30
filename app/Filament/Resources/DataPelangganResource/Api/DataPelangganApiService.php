<?php
namespace App\Filament\Resources\DataPelangganResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\DataPelangganResource;
use Illuminate\Routing\Router;


class DataPelangganApiService extends ApiService
{
    protected static string | null $resource = DataPelangganResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
