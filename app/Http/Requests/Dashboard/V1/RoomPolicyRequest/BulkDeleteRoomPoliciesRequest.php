<?php

namespace Modules\Hotel\Http\Requests\Dashboard\V1\RoomPolicyRequest;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteRoomPoliciesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuids' => ['required', 'array', 'min:1'],
            'uuids.*' => ['required', 'string', 'uuid', 'exists:hotel_room_policies,uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuids.required' => 'Please select at least one policy to delete.',
            'uuids.min' => 'Please select at least one policy to delete.',
            'uuids.*.exists' => 'One or more selected policies do not exist.',
        ];
    }
}
