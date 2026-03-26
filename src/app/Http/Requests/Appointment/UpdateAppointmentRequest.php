<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'required', 'integer', 'exists:clients,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s'],
            'end_time' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
            'status' => ['sometimes', 'string', 'in:scheduled,completed,cancelled'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_paid' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
