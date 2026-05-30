<?php
namespace App\Filament\Resources\DataPelangganResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\DataPelangganResource;
use App\Filament\Resources\DataPelangganResource\Api\Requests\CreateDataPelangganRequest;

class CreateHandler extends Handlers {
     public static bool $public = true;
    public static string | null $uri = '/';
    public static string | null $resource = DataPelangganResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create DataPelanggan
     *
     * @param CreateDataPelangganRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateDataPelangganRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}
