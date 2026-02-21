<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{
    use HasFactory;
	    protected $fillable = [
        'name',
        'mobile',
        'destination',
        'check_in_date',
        'check_out_date',
        'rooms',
        'adults',
        'children',
        'status',
        'reg_code',
        'email',
    ];

}
