<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:now'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.after' => 'The appointment start time must be in the future.',
            'end_time.after' => 'The appointment end time must be after the start time.',
        ];
    }
}
