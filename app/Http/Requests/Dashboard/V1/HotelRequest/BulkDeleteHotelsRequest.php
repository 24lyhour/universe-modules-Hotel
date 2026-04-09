<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\HotelRequest;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteHotelsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuids' => ['required', 'array', 'min:1'],
            'uuids.*' => ['required', 'string', 'uuid', 'exists:hotels,uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuids.required' => 'Please select at least one hotel to delete.',
            'uuids.min' => 'Please select at least one hotel to delete.',
            'uuids.*.exists' => 'One or more selected hotels do not exist.',
        ];
    }
}
