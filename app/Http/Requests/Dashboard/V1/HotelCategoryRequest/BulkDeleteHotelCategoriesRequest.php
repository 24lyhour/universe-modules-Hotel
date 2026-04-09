<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\HotelCategoryRequest;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteHotelCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuids' => ['required', 'array', 'min:1'],
            'uuids.*' => ['required', 'string', 'uuid', 'exists:hotel_categories,uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuids.required' => 'Please select at least one category to delete.',
            'uuids.min' => 'Please select at least one category to delete.',
            'uuids.*.exists' => 'One or more selected categories do not exist.',
        ];
    }
}
