<?php

namespace Database\Factories;

use App\Models\PropertyCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyCategoryFactory extends Factory
{
    protected $model = PropertyCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'code_prefix' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 4)),
            'description' => fake()->sentence(),
        ];
    }
}
