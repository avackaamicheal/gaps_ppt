<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'client_role_or_title' => fake()->randomElement(['Diaspora Investor, UK', 'Commercial Buyer', 'Homeowner, Lagos', 'Land Developer']),
            'client_location' => fake()->city() . ', Nigeria',
            'content' => fake()->paragraph(2),
            'rating' => fake()->numberBetween(4, 5),
            'property_id' => null,
            'avatar_path' => null,
            'is_published' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}

