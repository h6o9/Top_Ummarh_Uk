<?php

namespace App\Http\Controllers\Admin;

use App\Models\UmrahPackage;
use App\Models\PackageDetail;
use App\Models\Hotel;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UmmrahPackagesController extends Controller
{
    // Display all packages
    public function index()
    {
        $packages = UmrahPackage::with(['packageDetails.hotel', 'packageDetails.city'])->get();
        return view('admin.ummarhpackages.index', compact('packages'));
    }

    // Show form to create new package
    public function create()
    {
        $hotels = Hotel::all();
        $cities = City::all();
        return view('admin.ummarhpackages.create', compact('hotels','cities'));
    }

    // Store new package
    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'month' => 'required|string|max:255',
            'stars' => 'required|integer|min:1|max:5',
            'price_per_person' => 'required|numeric',
            'flight_info' => 'nullable|string',
            'visa_service' => 'required|boolean',
            'hotel_id' => 'required|array',
            'city_id' => 'required|array',
        ]);

        $package = UmrahPackage::create($request->only([
            'package_name','month','stars','price_per_person','flight_info','visa_service'
        ]));

        // Insert package details
        foreach($request->hotel_id as $index => $hotelId){
            PackageDetail::create([
                'package_id' => $package->id,
                'hotel_id' => $hotelId,
                'city_id' => $request->city_id[$index] ?? null
            ]);
        }

        return redirect()->route('umrahpackages.index')->with('success','Package created successfully!');
    }

    // Show form to edit package
    public function edit($id)
    {
        $package = UmrahPackage::with(['packageDetails'])->findOrFail($id);
        $hotels = Hotel::all();
        $cities = City::all();
        return view('admin.ummarhpackages.edit', compact('package','hotels','cities'));
    }

    // Update package
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'month' => 'required|string|max:255',
            'stars' => 'required|integer|min:1|max:5',
            'price_per_person' => 'required|numeric',
            'flight_info' => 'nullable|string',
            'visa_service' => 'required|boolean',
            'hotel_id' => 'required|array',
            'city_id' => 'required|array',
        ]);

        $package = UmrahPackage::findOrFail($id);
        $package->update($request->only([
            'package_name','month','stars','price_per_person','flight_info','visa_service'
        ]));

        // Delete old package details
        $package->packageDetails()->delete();

        // Insert updated package details
        foreach($request->hotel_id as $index => $hotelId){
            PackageDetail::create([
                'package_id' => $package->id,
                'hotel_id' => $hotelId,
                'city_id' => $request->city_id[$index] ?? null
            ]);
        }

        return redirect()->route('umrahpackages.index')->with('success','Package updated successfully!');
    }

    // Delete package
    public function destroy($id)
    {
        $package = UmrahPackage::findOrFail($id);
        $package->packageDetails()->delete();
        $package->delete();

        return redirect()->route('umrahpackages.index')->with('success','Package deleted successfully!');
    }

	public function toggleStatus(Request $request)
{
    $request->validate([
        'id' => 'required|integer'
    ]);

    $package = UmrahPackage::find($request->id);
    if(!$package){
        return response()->json(['success'=>false,'message'=>'Package not found!']);
    }

    // Toggle status
    $package->status = !$package->status;
    $package->save();

    return response()->json([
        'success' => true,
        'message' => $package->status ? 'Package Activated' : 'Package Deactivated',
        'status' => $package->status
    ]);
}
}