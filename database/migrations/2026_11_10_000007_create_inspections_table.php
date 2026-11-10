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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
            $table->date('scheduled_date')->index();
            $table->string('scheduled_time')->index(); // e.g. "10:00 AM", "14:30"
            $table->string('meeting_location');
            $table->string('status')->default('pending_confirmation')->index(); // pending_confirmation, confirmed, completed, cancelled, rescheduled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};

