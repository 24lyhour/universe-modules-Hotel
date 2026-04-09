<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Hotel\Enums\RoomStatusEnum;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'total_room' => ['nullable', 'integer', 'min:1'],
            'room_type' => ['nullable', 'string', 'max:100'],
            'room_number' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'bed_type' => ['nullable', 'string', 'max:50'],
            'bed_count' => ['nullable', 'integer', 'min:1'],
            'bathroom_count' => ['nullable', 'integer', 'min:0'],
            'room_size' => ['nullable', 'string', 'max:50'],
            'view' => ['nullable', 'string', 'max:100'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:255'],
            'is_available' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:' . implode(',', RoomStatusEnum::values())],
        ];
    }
}
