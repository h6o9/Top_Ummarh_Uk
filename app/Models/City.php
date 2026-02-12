<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_city', 'city_id', 'hotel_id');
    }
}