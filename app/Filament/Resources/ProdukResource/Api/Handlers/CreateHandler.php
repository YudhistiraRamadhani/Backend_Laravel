<?php
namespace App\Filament\Resources\ProdukResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ProdukResource;
use App\Filament\Resources\ProdukResource\Api\Requests\CreateProdukRequest;

class CreateHandler extends Handlers {
     public static bool $public = true;
    public static string | null $uri = '/';
    public static string | null $resource = ProdukResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Produk
     *
     * @param CreateProdukRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateProdukRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}
