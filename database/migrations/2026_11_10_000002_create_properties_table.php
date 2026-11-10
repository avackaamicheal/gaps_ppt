<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('property_categories')->restrictOnDelete();
            $table->string('reference', 30)->unique()->index();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->longText('description');
            $table->decimal('price', 15, 2)->unsigned();
            $table->string('currency', 3)->default('NGN');
            $table->string('status', 30)->default('draft')->index();
            $table->string('property_type')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();

            // Nigerian Location
            $table->string('state')->index();
            $table->string('lga')->index();
            $table->string('neighbourhood')->index();
            $table->string('street_address')->nullable();

            // Land-specific Attributes
            $table->string('plot_size')->nullable();
            $table->string('plot_dimensions')->nullable();
            $table->decimal('number_of_plots', 8, 2)->nullable();
            $table->string('land_use')->nullable();
            $table->string('title_type')->nullable();
            $table->string('topography')->nullable();
            $table->boolean('has_survey')->default(false);

            // Building-specific Attributes
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('toilets')->nullable();
            $table->unsignedTinyInteger('parking_spaces')->nullable();
            $table->unsignedTinyInteger('floors')->nullable();
            $table->string('building_condition')->nullable();
            $table->json('features')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
