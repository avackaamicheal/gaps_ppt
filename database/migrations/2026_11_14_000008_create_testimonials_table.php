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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_role_or_title')->nullable(); // e.g. "Diaspora Investor, UK", "First-time Land Buyer"
            $table->string('client_location')->nullable(); // e.g. "London, UK", "Lekki Phase 1"
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5); // 1 - 5 stars
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
