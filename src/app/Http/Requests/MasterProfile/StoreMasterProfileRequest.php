<?php

namespace App\Http\Requests\MasterProfile;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'appointment_duration' => ['nullable', 'integer', 'min:15', 'max:180'],
            'booking_advance_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'social_links' => ['nullable', 'array'],
        ];
    }
}
