<?php
namespace App\Filament\Resources\DataPelangganResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\DataPelangganResource;
use App\Filament\Resources\DataPelangganResource\Api\Transformers\DataPelangganTransformer;

class PaginationHandler extends Handlers {
      public static bool $public = true;
    public static string | null $uri = '/';
    public static string | null $resource = DataPelangganResource::class;


    /**
     * List of DataPelanggan
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

        return DataPelangganTransformer::collection($query);
    }
}
