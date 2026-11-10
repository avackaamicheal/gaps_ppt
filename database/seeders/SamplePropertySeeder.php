<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Services\PropertyReferenceGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SamplePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landCategory = PropertyCategory::where('slug', 'land')->first();
        $residentialCategory = PropertyCategory::where('slug', 'residential')->first();
        $commercialCategory = PropertyCategory::where('slug', 'commercial')->first();

        $refGen = new PropertyReferenceGenerator();

        $properties = [
            [
                'category_id' => $landCategory->id,
                'title' => 'Prime 600 sqm Commercial Plot in Eleko Coastal Hub',
                'description' => "Exceptional investment land located along the bustling coastal road, directly opposite the Eleko Beach junction in Ibeju-Lekki. Ideal for commercial development, logistics warehouse, or high-yield residential estate.\n\nKey Highlights:\n- 100% dry table land, no sandfilling required\n- Clear excision and registered layout\n- Direct access to tarred dual-carriageway\n- High appreciation corridor near Dangote Refinery and Lekki Deep Sea Port.",
                'price' => 28000000,
                'currency' => 'NGN',
                'status' => Property::STATUS_PUBLISHED,
                'property_type' => 'Commercial Land',
                'is_featured' => true,
                'published_at' => now(),
                'state' => 'Lagos',
                'lga' => 'Ibeju-Lekki',
                'neighbourhood' => 'Eleko',
                'street_address' => 'KM 38 Lekki-Epe Expressway, Eleko Junction',
                'plot_size' => '600 sqm',
                'plot_dimensions' => '60ft x 120ft',
                'number_of_plots' => 1,
                'land_use' => 'Commercial',
                'title_type' => 'Certificate of Occupancy (C of O)',
                'topography' => '100% Dry Land',
                'has_survey' => true,
            ],
            [
                'category_id' => $residentialCategory->id,
                'title' => 'Ultra-Modern 5-Bedroom Detached Duplex with Swimming Pool & BQ',
                'description' => "Architectural masterpiece located in a secure gated estate in Lekki Phase 1. Designed for luxury living, this fully automated smart home offers exquisite finishes, expansive living areas, and state-of-the-art security.\n\nProperty Highlights:\n- All rooms en-suite with walk-in closets\n- Fully fitted Italian kitchen with marble island, heat extractor, and oven\n- Heated swimming pool with waterfall feature\n- Rooftop lounge terrace overlooking the Lekki skyline\n- 24/7 uninterrupted power supply and dedicated security gatehouse.",
                'price' => 220000000,
                'currency' => 'NGN',
                'status' => Property::STATUS_PUBLISHED,
                'property_type' => 'Detached Duplex',
                'is_featured' => true,
                'published_at' => now(),
                'state' => 'Lagos',
                'lga' => 'Eti-Osa',
                'neighbourhood' => 'Lekki Phase 1',
                'street_address' => 'Off Admiralty Way, Lekki Phase 1',
                'bedrooms' => 5,
                'bathrooms' => 5,
                'toilets' => 6,
                'parking_spaces' => 4,
                'floors' => 2,
                'building_condition' => 'Brand New / Off-Plan',
                'features' => [
                    '24/7 Electricity / Dedicated Transformer',
                    'Private Swimming Pool',
                    'Gated Security & CCTV',
                    'Fitted Kitchen with Heat Extractor',
                    'All Rooms En-suite',
                    'Automated Gate / Smart Home',
                    'Boys Quarters (BQ)',
                ],
            ],
            [
                'category_id' => $commercialCategory->id,
                'title' => 'Strategic 4-Floor Commercial Plaza on Major Arterial Road',
                'description' => "High-visibility commercial office plaza situated along a prime business route. Features open-plan flexible office suites, ground floor banking/retail halls, central air conditioning, passenger elevators, and basement parking for over 30 vehicles.",
                'price' => 550000000,
                'currency' => 'NGN',
                'status' => Property::STATUS_PUBLISHED,
                'property_type' => 'Commercial Plaza',
                'is_featured' => false,
                'published_at' => now(),
                'state' => 'Lagos',
                'lga' => 'Ikeja',
                'neighbourhood' => 'Ikeja GRA',
                'street_address' => 'Isaac John Street, Ikeja GRA',
                'floors' => 4,
                'parking_spaces' => 30,
                'building_condition' => 'Newly Built',
                'features' => [
                    '24/7 Electricity / Dedicated Transformer',
                    'Gated Security & CCTV',
                    'Ample Car Parking',
                    'Prepaid Meter Installed',
                ],
            ],
            [
                'category_id' => $landCategory->id,
                'title' => 'Draft / Under Preparation: 5 Acres Agricultural Estate in Epe',
                'description' => 'Draft listing under documentation verification. Will be published upon completion of survey charting.',
                'price' => 45000000,
                'currency' => 'NGN',
                'status' => Property::STATUS_DRAFT,
                'property_type' => 'Agricultural Farmland',
                'is_featured' => false,
                'published_at' => null,
                'state' => 'Lagos',
                'lga' => 'Epe',
                'neighbourhood' => 'Poka',
                'plot_size' => '5 Acres',
                'number_of_plots' => 30,
                'land_use' => 'Agricultural',
                'title_type' => 'Registered Survey Plan',
                'topography' => '100% Dry Land',
                'has_survey' => true,
            ],
        ];

        foreach ($properties as $data) {
            $cat = PropertyCategory::find($data['category_id']);
            $ref = $refGen->generate($cat);
            $slug = Str::slug($data['title']);

            if (Property::where('slug', $slug)->exists()) {
                $slug .= '-' . rand(10, 99);
            }

            $data['reference'] = $ref;
            $data['slug'] = $slug;

            Property::create($data);
        }
    }
}
