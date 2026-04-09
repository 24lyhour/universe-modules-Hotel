<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\HotelCategoryRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name must be less than 255 characters.',
            'icon.max' => 'Icon must be less than 50 characters.',
            'group.max' => 'Group must be less than 100 characters.',
            'sort_order.min' => 'Sort order must be at least 0.',
        ];
    }
}
