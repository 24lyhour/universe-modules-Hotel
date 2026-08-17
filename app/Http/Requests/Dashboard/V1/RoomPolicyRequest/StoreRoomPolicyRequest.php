<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\RoomPolicyRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Policy title is required.',
            'title.max' => 'Policy title must be less than 255 characters.',
            'icon.max' => 'Icon must be less than 50 characters.',
            'sort_order.min' => 'Sort order must be at least 0.',
        ];
    }
}
