<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Cities relation
    public function cities()
    {
        return $this->belongsToMany(City::class, 'hotel_city', 'hotel_id', 'city_id');
    }
}