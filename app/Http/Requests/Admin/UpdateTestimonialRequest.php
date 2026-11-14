<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'client_role_or_title' => ['nullable', 'string', 'max:255'],
            'client_location' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:3000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

