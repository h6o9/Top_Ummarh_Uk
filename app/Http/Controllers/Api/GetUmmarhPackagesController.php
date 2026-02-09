<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetUmmarhPackagesController extends Controller
{
    //

	public function index()
{
    try {
        $packages = UmrahPackage::with([
            'packageDetails.city',
            'packageDetails.hotel'
        ])->get();

        return response()->json([
            'status'  => true,
            'message' => 'Packages fetched successfully',
            'data'    => $packages
        ], 200);

    } catch (Throwable $e) {
        return response()->json([
            'status'  => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}
