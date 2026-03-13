<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePembiayaanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'umkm';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'umkm_profile_id' => 'required|exists:umkm_profiles,id',
            'nominal_pengajuan' => 'required|numeric|min:1000000',
            'tenor_bulan' => 'required|integer|min:1',
            'bunga_persen' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'nominal_pengajuan.min' => 'Minimal pengajuan adalah Rp 1.000.000 ya broh',
            
        ];
    }
}
