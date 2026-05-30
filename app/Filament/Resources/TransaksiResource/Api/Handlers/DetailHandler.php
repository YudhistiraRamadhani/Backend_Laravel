<?php

namespace App\Filament\Resources\TransaksiResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\TransaksiResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\TransaksiResource\Api\Transformers\TransaksiTransformer;

class DetailHandler extends Handlers
{
    public static bool $public = true;
    public static string | null $uri = '/{id}';
    public static string | null $resource = TransaksiResource::class;


    /**
     * Show Transaksi
     *
     * @param Request $request
     * @return TransaksiTransformer
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

        return new TransaksiTransformer($query);
    }
}
