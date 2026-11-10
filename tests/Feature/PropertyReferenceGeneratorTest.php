<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Services\PropertyReferenceGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyReferenceGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyReferenceGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new PropertyReferenceGenerator();
    }

    public function test_it_generates_first_reference_for_category(): void
    {
        $landCategory = PropertyCategory::factory()->create([
            'name' => 'Land',
            'code_prefix' => 'LAND',
        ]);

        $reference = $this->generator->generate($landCategory);

        $this->assertEquals('LAND-0001', $reference);
    }

    public function test_it_increments_reference_sequentially(): void
    {
        $category = PropertyCategory::factory()->create([
            'name' => 'Residential',
            'code_prefix' => 'PROP',
        ]);

        Property::factory()->create([
            'category_id' => $category->id,
            'reference' => 'PROP-0001',
        ]);

        $reference = $this->generator->generate($category);

        $this->assertEquals('PROP-0002', $reference);
    }

    public function test_different_categories_have_independent_sequences(): void
    {
        $landCategory = PropertyCategory::factory()->create(['code_prefix' => 'LAND']);
        $commCategory = PropertyCategory::factory()->create(['code_prefix' => 'COMM']);

        Property::factory()->create([
            'category_id' => $landCategory->id,
            'reference' => 'LAND-0005',
        ]);

        $landRef = $this->generator->generate($landCategory);
        $commRef = $this->generator->generate($commCategory);

        $this->assertEquals('LAND-0006', $landRef);
        $this->assertEquals('COMM-0001', $commRef);
    }

    public function test_it_accounts_for_soft_deleted_properties(): void
    {
        $category = PropertyCategory::factory()->create(['code_prefix' => 'LAND']);

        $property = Property::factory()->create([
            'category_id' => $category->id,
            'reference' => 'LAND-0001',
        ]);

        $property->delete(); // Soft delete

        $nextRef = $this->generator->generate($category);

        $this->assertEquals('LAND-0002', $nextRef);
    }
}
