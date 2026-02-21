<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->string('reg_code')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->dropColumn('reg_code');
        });
    }
};