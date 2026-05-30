<?php
namespace App\Filament\Resources\DataPelangganResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\DataPelangganResource;
use App\Filament\Resources\DataPelangganResource\Api\Requests\UpdateDataPelangganRequest;

class UpdateHandler extends Handlers {
     public static bool $public = true;
    public static string | null $uri = '/{id}';
    public static string | null $resource = DataPelangganResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update DataPelanggan
     *
     * @param UpdateDataPelangganRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateDataPelangganRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}
