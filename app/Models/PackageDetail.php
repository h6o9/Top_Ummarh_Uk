<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{
    use HasFactory;
	protected $guarded = [];
	 public function package()
    {
        return $this->belongsTo(UmrahPackage::class, 'package_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
