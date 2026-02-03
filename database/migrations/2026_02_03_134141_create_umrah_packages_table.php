<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umrah_packages', function (Blueprint $table) {
            $table->id();

            $table->string('month');                 // Month (e.g. Ramadan, March)
            $table->string('package_name');          // Package name
            $table->unsignedTinyInteger('stars');    // Hotel stars (1–5)
            $table->decimal('price_per_person', 10, 2); // Price per person
            $table->text('flight_info')->nullable(); // Flight details
            $table->boolean('visa_service')->default(false); // Visa included or not

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umrah_packages');
    }
};
