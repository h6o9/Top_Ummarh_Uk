<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightBooking extends Model
{
    use HasFactory;
	protected $fillable = [
    'trip_type',
    'departure',
    'destination',
    'departure_date',
    'return_date',
    'cabin_class',
    'airline',
    'adults',
    'children',
    'infants',
    'name',
    'email',
    'mobile',
	'reg_code', // ✅ 
	'status',   // ✅
];

   protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
