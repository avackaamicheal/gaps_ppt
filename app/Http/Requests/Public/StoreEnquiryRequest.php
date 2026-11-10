<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'bot_check' => ['nullable', 'string'], // Handled in controller to silently deceive bots
        ];
    }

    /**
     * Custom message for required fields.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your full name.',
            'phone.required' => 'Please provide your phone number so Victor can reach out to you.',
            'message.required' => 'Please leave a brief message or question about this property.',
        ];
    }
}
