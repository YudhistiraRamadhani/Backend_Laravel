<?php

namespace App\Filament\Resources\DataPelangganResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDataPelangganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'nama_pelanggan' => 'required|string',
			'no_whatsapp' => 'required|string',
			'pesannotifikasi' => 'required|string',
			'tanggal_notifikasi' => 'required'
		];
    }
}
