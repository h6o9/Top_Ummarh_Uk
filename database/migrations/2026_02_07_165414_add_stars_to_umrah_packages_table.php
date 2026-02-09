<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->tinyInteger('stars')->after('description')->default(5);
            // default 5 stars, integer 1-5
        });
    }

    public function down(): void
    {
        Schema::table('umrah_packages', function (Blueprint $table) {
            $table->dropColumn('stars');
        });
    }
};