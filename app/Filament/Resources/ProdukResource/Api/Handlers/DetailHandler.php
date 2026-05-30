<?php

namespace App\Filament\Resources\ProdukResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\ProdukResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\ProdukResource\Api\Transformers\ProdukTransformer;

class DetailHandler extends Handlers
{
     public static bool $public = true;
    public static string | null $uri = '/{id}';
    public static string | null $resource = ProdukResource::class;


    /**
     * Show Produk
     *
     * @param Request $request
     * @return ProdukTransformer
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

        return new ProdukTransformer($query);
    }
}
