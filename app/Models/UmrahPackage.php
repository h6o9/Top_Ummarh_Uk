<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmrahPackage extends Model
{
    use HasFactory;

	public function packageDetails()
    {
        return $this->hasMany(PackageDetail::class, 'package_id');
    }
}
