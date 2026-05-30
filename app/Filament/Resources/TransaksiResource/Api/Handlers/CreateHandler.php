<?php
namespace App\Filament\Resources\TransaksiResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TransaksiResource;
use App\Filament\Resources\TransaksiResource\Api\Requests\CreateTransaksiRequest;

class CreateHandler extends Handlers {
    public static bool $public = true;
    public static string | null $uri = '/';
    public static string | null $resource = TransaksiResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Transaksi
     *
     * @param CreateTransaksiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateTransaksiRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}
