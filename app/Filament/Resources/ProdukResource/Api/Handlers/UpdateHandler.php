<?php
namespace App\Filament\Resources\ProdukResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ProdukResource;
use App\Filament\Resources\ProdukResource\Api\Requests\UpdateProdukRequest;

class UpdateHandler extends Handlers {
     public static bool $public = true;
    public static string | null $uri = '/{id}';
    public static string | null $resource = ProdukResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Produk
     *
     * @param UpdateProdukRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateProdukRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}
