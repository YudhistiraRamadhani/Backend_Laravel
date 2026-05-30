<?php
namespace App\Filament\Resources\TransaksiResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\TransaksiResource;
use App\Filament\Resources\TransaksiResource\Api\Transformers\TransaksiTransformer;

class PaginationHandler extends Handlers {
    public static bool $public = true;
    public static string | null $uri = '/';
    public static string | null $resource = TransaksiResource::class;


    /**
     * List of Transaksi
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for($query)
        ->allowedFields($this->getAllowedFields() ?? [])
        ->allowedSorts($this->getAllowedSorts() ?? [])
        ->allowedFilters($this->getAllowedFilters() ?? [])
        ->allowedIncludes($this->getAllowedIncludes() ?? [])
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return TransaksiTransformer::collection($query);
    }
}
