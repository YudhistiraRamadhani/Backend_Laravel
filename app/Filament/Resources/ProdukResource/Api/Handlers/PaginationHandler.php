<?php
namespace App\Filament\Resources\ProdukResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\ProdukResource;
use App\Filament\Resources\ProdukResource\Api\Transformers\ProdukTransformer;

class PaginationHandler extends Handlers {
     public static bool $public = true;
    
    public static string | null $uri = '/';
    public static string | null $resource = ProdukResource::class;


    /**
     * List of Produk
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

        return ProdukTransformer::collection($query);
    }
}
