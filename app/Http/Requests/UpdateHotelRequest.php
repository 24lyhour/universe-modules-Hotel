<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['sometimes', 'required', 'string', 'max:500'],
            'city' => ['sometimes', 'required', 'string', 'max:100'],
            'country' => ['sometimes', 'required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'star_rating' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'price_per_night' => ['sometimes', 'required', 'numeric', 'min:0'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string', 'max:255'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
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
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
