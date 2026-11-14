<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class SampleTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleProperty = Property::first();

        $testimonials = [
            [
                'client_name' => 'Engr. Babatunde Fashola',
                'client_role_or_title' => 'Diaspora Investor',
                'client_location' => 'Manchester, UK',
                'content' => 'Acquiring 2 commercial plots in Ibeju-Lekki while living in the UK felt daunting, but Victor handled the entire due diligence process with utmost integrity. Every document and beacon was verified before payment.',
                'rating' => 5,
                'property_id' => $sampleProperty?->id,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Dr. & Mrs. Ebere Nwosu',
                'client_role_or_title' => 'Homeowner',
                'client_location' => 'Ikoyi, Lagos',
                'content' => 'Victor helped us locate our dream detached duplex in Lekki Phase 1. Zero middleman drama, clear title search at Alausa, and direct negotiations with the developer. We closed smoothly within three weeks.',
                'rating' => 5,
                'property_id' => $sampleProperty?->id,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Chief Adeolu Adeleke',
                'client_role_or_title' => 'Commercial Developer',
                'client_location' => 'Abuja, Nigeria',
                'content' => 'Victor Akande is one of the very few honest independent realtors I have worked with in Lagos. He pointed out title encumbrances on a property I liked and advised against buying it. That level of transparency is rare.',
                'rating' => 5,
                'property_id' => null,
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::updateOrCreate(
                ['client_name' => $t['client_name']],
                $t
            );
        }
    }
}

