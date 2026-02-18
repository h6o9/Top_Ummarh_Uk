<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();

            // Trip Type
            $table->enum('trip_type', ['return', 'one_way', 'multicity'])->default('return');

            // Route
            $table->string('departure');       // leaving from
            $table->string('destination');     // going to

            // Dates
            $table->date('departure_date');
            $table->date('return_date')->nullable(); // null for one_way

            // Cabin & Airline
            $table->enum('cabin_class', ['economy', 'business', 'first'])->default('economy');
            $table->string('airline')->nullable();

            // Passengers
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->unsignedTinyInteger('infants')->default(0);

            // User Info
            $table->string('name');
            $table->string('email');
            $table->string('mobile');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};