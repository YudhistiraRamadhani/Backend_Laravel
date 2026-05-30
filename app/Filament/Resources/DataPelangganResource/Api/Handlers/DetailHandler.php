<?php

namespace App\Filament\Resources\DataPelangganResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\DataPelangganResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\DataPelangganResource\Api\Transformers\DataPelangganTransformer;

class DetailHandler extends Handlers
{
     public static bool $public = true;
    public static string | null $uri = '/{id}';
    public static string | null $resource = DataPelangganResource::class;


    /**
     * Show DataPelanggan
     *
     * @param Request $request
     * @return DataPelangganTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new DataPelangganTransformer($query);
    }
}
