<?php

namespace App\Http\Requests\Admin;

use App\Models\Customer;
use App\Models\Enquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'source' => ['required', 'string', Rule::in(array_keys(Enquiry::SOURCES))],
            'status' => ['required', 'string', Rule::in(array_keys(Enquiry::STATUSES))],
            'property_id' => ['nullable', 'exists:properties,id'],
            'message' => ['required', 'string', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

