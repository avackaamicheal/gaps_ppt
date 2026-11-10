<?php

namespace App\Http\Requests\Admin;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:property_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in(array_keys(Property::STATUSES))],
            'property_type' => ['nullable', 'string', 'max:100'],
            'is_featured' => ['nullable', 'boolean'],

            // Location
            'state' => ['required', 'string', 'max:100'],
            'lga' => ['required', 'string', 'max:100'],
            'neighbourhood' => ['required', 'string', 'max:100'],
            'street_address' => ['nullable', 'string', 'max:255'],

            // Land Attributes
            'plot_size' => ['nullable', 'string', 'max:100'],
            'plot_dimensions' => ['nullable', 'string', 'max:100'],
            'number_of_plots' => ['nullable', 'numeric', 'min:0'],
            'land_use' => ['nullable', 'string', 'max:100'],
            'title_type' => ['nullable', 'string', 'max:150'],
            'topography' => ['nullable', 'string', 'max:100'],
            'has_survey' => ['nullable', 'boolean'],

            // Building Attributes
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:255'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:255'],
            'toilets' => ['nullable', 'integer', 'min:0', 'max:255'],
            'parking_spaces' => ['nullable', 'integer', 'min:0', 'max:255'],
            'floors' => ['nullable', 'integer', 'min:0', 'max:255'],
            'building_condition' => ['nullable', 'string', 'max:100'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:100'],

            // Image Updates
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'cover_image_id' => ['nullable', 'integer'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],

            // Document Updates
            'documents' => ['nullable', 'array'],
            'documents.*.file' => ['required_with:documents', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:10240'],
            'documents.*.title' => ['nullable', 'string', 'max:255'],
            'documents.*.type' => ['nullable', 'string', 'max:100'],
            'documents.*.is_public' => ['nullable', 'boolean'],
            'delete_documents' => ['nullable', 'array'],
            'delete_documents.*' => ['integer'],
        ];
    }
}

