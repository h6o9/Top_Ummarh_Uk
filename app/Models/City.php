<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

	  public function packageDetails()
    {
        return $this->hasMany(PackageDetail::class, 'city_id');
    }
}
