<?php

namespace App\Http\Requests\Admin;

use App\Models\Inspection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'enquiry_id' => ['nullable', 'exists:enquiries,id'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'string', 'max:50'],
            'meeting_location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(array_keys(Inspection::STATUSES))],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

