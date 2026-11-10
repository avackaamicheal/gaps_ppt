<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'category_id' => PropertyCategory::factory(),
            'reference' => 'PROP-' . fake()->unique()->numberBetween(1000, 9999),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 10000000, 300000000),
            'currency' => 'NGN',
            'status' => Property::STATUS_PUBLISHED,
            'property_type' => 'Detached Duplex',
            'is_featured' => false,
            'published_at' => now(),
            'state' => 'Lagos',
            'lga' => 'Eti-Osa',
            'neighbourhood' => 'Lekki Phase 1',
            'street_address' => 'Off Admiralty Way',
            'bedrooms' => 4,
            'bathrooms' => 4,
            'toilets' => 5,
            'parking_spaces' => 3,
            'floors' => 2,
            'building_condition' => 'Newly Built',
            'features' => ['24/7 Security & CCTV', 'Fitted Kitchen with Heat Extractor', 'All Rooms En-suite'],
        ];
    }

    public function land(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_type' => 'Residential Plot',
            'plot_size' => '600 sqm',
            'plot_dimensions' => '60ft x 120ft',
            'number_of_plots' => 1,
            'land_use' => 'Residential',
            'title_type' => 'Certificate of Occupancy (C of O)',
            'topography' => '100% Dry Land',
            'has_survey' => true,
            'bedrooms' => null,
            'bathrooms' => null,
            'toilets' => null,
            'parking_spaces' => null,
            'floors' => null,
            'building_condition' => null,
            'features' => null,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Property::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Property::STATUS_SOLD,
        ]);
    }
}

