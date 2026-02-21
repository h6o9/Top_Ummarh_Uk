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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Customer name
            $table->string('mobile', 20);                   // Mobile number
            $table->string('destination');                   // Arrival destination (city/district/airport)
            $table->date('check_in_date');                   // Check-in date
            $table->date('check_out_date');                  // Check-out date
            $table->unsignedTinyInteger('rooms')->default(1);   // Number of rooms
            $table->unsignedTinyInteger('adults')->default(1);  // Number of adults
            $table->unsignedTinyInteger('children')->default(0); // Number of children
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->dropColumn('reg_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};