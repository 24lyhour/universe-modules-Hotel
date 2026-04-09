<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\HotelRequest;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Hotel\Enums\HotelStatusEnum;

class StoreHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'provinces' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'star_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'price_level' => ['nullable', 'string', 'max:50'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'hotel_category_id' => ['nullable', 'exists:hotel_categories,id'],
            'logo_url' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'total_floors' => ['nullable', 'integer', 'min:0'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string', 'max:255'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['required', 'string', 'in:' . implode(',', HotelStatusEnum::values())],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The hotel name is required.',
            'address.required' => 'The hotel address is required.',
            'city.required' => 'The city is required.',
            'country.required' => 'The country is required.',
            'star_rating.required' => 'The star rating is required.',
            'star_rating.min' => 'The star rating must be at least 1.',
            'star_rating.max' => 'The star rating cannot exceed 5.',
            'price_per_night.required' => 'The price per night is required.',
            'status.in' => 'The status must be one of: ' . implode(', ', HotelStatusEnum::values()),
        ];
    }
}
