<?php
namespace App\Filament\Resources\DataPelangganResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\DataPelanggan;

/**
 * @property DataPelanggan $resource
 */
class DataPelangganTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
