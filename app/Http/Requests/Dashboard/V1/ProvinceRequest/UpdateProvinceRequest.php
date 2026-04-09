<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\ProvinceRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'name_kh' => ['nullable', 'string', 'max:255'],
            'code' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('provinces', 'code')->ignore($this->route('province'))],
            'region' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Province name is required.',
            'code.required' => 'Province code is required.',
            'code.unique' => 'This province code is already in use.',
        ];
    }
}
