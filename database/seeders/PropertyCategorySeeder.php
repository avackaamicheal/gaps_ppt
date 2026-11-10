<?php

namespace Database\Seeders;

use App\Models\PropertyCategory;
use Illuminate\Database\Seeder;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Land',
                'slug' => 'land',
                'code_prefix' => 'LAND',
                'description' => 'Residential plots, commercial acreage, mixed-use parcels, and agricultural farmland across Nigeria.',
            ],
            [
                'name' => 'Residential',
                'slug' => 'residential',
                'code_prefix' => 'PROP',
                'description' => 'Detached duplexes, terrace houses, semi-detached units, apartments, and private residential estates.',
            ],
            [
                'name' => 'Commercial',
                'slug' => 'commercial',
                'code_prefix' => 'COMM',
                'description' => 'Commercial buildings, shopping complexes, office plazas, warehouses, and high-yield commercial hubs.',
            ],
        ];

        foreach ($categories as $data) {
            PropertyCategory::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
