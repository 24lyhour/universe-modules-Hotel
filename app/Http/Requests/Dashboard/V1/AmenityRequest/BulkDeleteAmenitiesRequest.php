<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\AmenityRequest;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteAmenitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuids' => ['required', 'array', 'min:1'],
            'uuids.*' => ['required', 'string', 'uuid', 'exists:hotel_amenities,uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuids.required' => 'Please select at least one amenity to delete.',
            'uuids.min' => 'Please select at least one amenity to delete.',
            'uuids.*.exists' => 'One or more selected amenities do not exist.',
        ];
    }
}
