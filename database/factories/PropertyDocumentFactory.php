<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyDocumentFactory extends Factory
{
    protected $model = PropertyDocument::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'title' => 'Certificate of Occupancy',
            'document_type' => 'Certificate of Occupancy (C of O)',
            'file_path' => 'properties/dummy/doc.pdf',
            'file_name' => 'c_of_o.pdf',
            'file_size' => 1024 * 1024,
            'mime_type' => 'application/pdf',
            'is_public' => false,
        ];
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }
}

