<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyImageFactory extends Factory
{
    protected $model = PropertyImage::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'file_path' => 'properties/dummy/sample.jpg',
            'is_cover' => false,
            'sort_order' => 0,
            'caption' => fake()->sentence(3),
        ];
    }
}
