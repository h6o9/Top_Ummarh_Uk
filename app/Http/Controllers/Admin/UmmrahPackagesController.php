<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Hotel;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use App\Models\PackageDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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



public function store(Request $request)
{
    DB::beginTransaction();

    try {

        // IMAGE SAVE
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('admin/assets/images'), $imageName);
            $imagePath = 'public/admin/assets/images/'.$imageName;
        }

        // INSERT PACKAGE
        $package = UmrahPackage::create([
            'package_name'     => $request->name,
            'month'            => $request->month,
            'price_per_person' => $request->price,
            'visa_service'     => $request->visa_service,
            'flight_info'      => $request->flight_info,
            'description'      => $request->description,
            'stars'            => $request->stars,
            'status'           => $request->status,
            'image'            => $imagePath,
        ]);

        // INSERT PACKAGE DETAILS
        foreach ($request->cities as $i => $cityId) {
            PackageDetail::create([
                'package_id'    => $package->id,
                'city_id'       => $cityId,
                'hotel_id'      => $request->hotel_id,
                'time_duration' => $request->days[$i] . ' Nights',
            ]);
        }

        DB::commit();

        return redirect()->route('umrahpackages.index')
            ->with('success', 'Package created successfully!');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage())->withInput();
    }
}    // Show form to edit package
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
    // $request->validate([
    //     'name' => 'required|string|max:255',
    //     'month' => 'required|string|max:255',
    //     'stars' => 'required|integer|min:1',
    //     'price' => 'required|numeric',
    //     'flight_info' => 'nullable|string',
    //     'visa_service' => 'required|string',
    //     'cities' => 'required|array',
    //     'days' => 'required|array',
    //     'hotel_id' => 'required|integer',
    //     'description' => 'required|string',
    //     'status' => 'required|boolean',
    //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    // ]);

    DB::beginTransaction();

    try {
        $package = UmrahPackage::findOrFail($id);

        // IMAGE UPDATE
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($package->image && file_exists(public_path($package->image))) {
                unlink(public_path($package->image));
            }

            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('admin/assets/images'), $imageName);
            $imagePath = 'public/admin/assets/images/'.$imageName;
        } else {
            $imagePath = $package->image; // keep old
        }

        // UPDATE PACKAGE
        $package->update([
            'package_name'     => $request->name,
            'month'            => $request->month,
            'price_per_person' => $request->price,
            'visa_service'     => $request->visa_service,
            'flight_info'      => $request->flight_info,
            'description'      => $request->description,
            'stars'            => $request->stars,
            'status'           => $request->status,
            'image'            => $imagePath,
        ]);

        // DELETE OLD PACKAGE DETAILS
        $package->packageDetails()->delete();

        // INSERT UPDATED PACKAGE DETAILS
        foreach ($request->cities as $i => $cityId) {
            PackageDetail::create([
                'package_id'    => $package->id,
                'city_id'       => $cityId,
                'hotel_id'      => $request->hotel_id,
                'time_duration' => $request->days[$i] . ' Nights',
            ]);
        }

        DB::commit();

        return redirect()->route('umrahpackages.index')
            ->with('success', 'Package updated successfully!');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage())->withInput();
    }
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