<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Hotel;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use App\Models\PackageDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
    $cities = City::all(); // sab cities for dropdown (optional, ya aap AJAX pe populate karenge)
	 $hotels = Hotel::all();
    return view('admin.ummarhpackages.create', compact('cities', 'hotels'));
}

// AJAX request ke liye
public function getHotelsByCity($city)
{
    $hotels = DB::table('hotel_city')
        ->join('hotels', 'hotel_city.hotel_id', '=', 'hotels.id')
        ->where('hotel_city.city_id', $city)
        ->select('hotels.id','hotels.name')
        ->get();

    return response()->json($hotels);
}



    // Store new package
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('umrah_packages', 'package_name')
        ],
        'month' => 'required|integer|min:1|max:12',
        'price' => 'required|numeric|min:0',
        'visa_service' => 'required|string|max:255',
        'flight_info' => 'required|string|max:255',
        'description' => 'required|string',
        'stars' => 'required|integer|min:1',
        'status' => 'required|in:0,1',
        'cities' => 'required|array|min:1',
        'cities.*' => 'required|exists:cities,id',
        'hotels' => 'required|array|min:1', // Changed from hotel_id to hotels array
        'hotels.*' => 'required|exists:hotels,id',
        'days' => 'required|array|min:1',
        'days.*' => 'required|integer|min:1',
        'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
    ], [
        'name.required' => 'Package name is required.',
        'name.unique' => 'Package name already exists.',
        'image.required' => 'Package image is required.',
        'image.max' => 'Image size must be less than 2MB.',
        'stars.required' => 'Stars rating is required.',
        'stars.min' => 'Stars must be at least 1.',
        'stars.max' => 'Stars cannot exceed 5.',
        'cities.required' => 'At least one city is required.',
        'cities.*.required' => 'City selection is required.',
        'hotels.required' => 'At least one hotel is required.',
        'hotels.*.required' => 'Hotel selection is required for each city.',
        'days.required' => 'Nights information is required.',
        'days.*.required' => 'Each city must have number of nights.',
        'days.*.min' => 'Nights must be at least 1.',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    DB::beginTransaction();

    try {
        // IMAGE SAVE
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('admin/assets/images/umrah-packages'), $imageName);
            $imagePath = 'admin/assets/images/umrah-packages/' . $imageName;
        }

        // CREATE PACKAGE
        $package = UmrahPackage::create([
            'package_name' => $request->name,
            'month' => $request->month,
            'price_per_person' => $request->price,
            'visa_service' => $request->visa_service,
            'flight_info' => $request->flight_info,
            'description' => $request->description,
            'stars' => $request->stars,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        // PACKAGE DETAILS - Each city with its own hotel
        foreach ($request->cities as $index => $cityId) {
            // Make sure we have corresponding hotel for this city
            if (!isset($request->hotels[$index])) {
                throw new \Exception("Hotel not specified for city at position " . ($index + 1));
            }
            
            PackageDetail::create([
                'package_id' => $package->id,
                'city_id' => $cityId,
                'hotel_id' => $request->hotels[$index], // Use hotel for this specific city
                'time_duration' => $request->days[$index] . ' Nights',
            ]);
        }

        DB::commit();

        return redirect()->route('umrahpackages.index')
            ->with('success', 'Package created successfully!');

    } catch (\Throwable $e) {
        DB::rollBack();
        
        // Delete uploaded image if package creation failed
        if (isset($imagePath) && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
        
        return back()->with('error', 'Error creating package: ' . $e->getMessage())->withInput();
    }
}



    // Show form to edit package
    public function edit($id)
    {
        $package = UmrahPackage::with(['packageDetails'])->findOrFail($id);
        $hotels = Hotel::all();
        $cities = City::all();
        return view('admin.ummarhpackages.edit', compact('package', 'hotels', 'cities'));
    }

    // Update package
 public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name'          => 'required|string|max:255|unique:umrah_packages,package_name,' . $id,
        'month'         => 'required|integer',
        'price'         => 'required|numeric|min:1',
        'visa_service'  => 'required|string|max:255',
        'flight_info'   => 'required|string|max:255',
        'description'   => 'required|string',
        'stars'         => 'required|integer|min:1',
        'status'        => 'required|boolean',
        'cities'        => 'required|array|min:1',
        'cities.*'      => 'required|exists:cities,id',
        'hotels'        => 'required|array|min:1',
        'hotels.*'      => 'required|exists:hotels,id',
        'days'          => 'required|array|min:1',
        'days.*'        => 'required|integer|min:1|max:30',
        'image'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048'
    ], [
        'name.required' => 'Package name is required.',
        'name.unique' => 'Package name already exists.',
        'cities.required' => 'At least one city is required.',
        'cities.*.required' => 'City selection is required.',
        'hotels.required' => 'At least one hotel is required.',
        'hotels.*.required' => 'Hotel selection is required for each city.',
        'days.required' => 'Nights information is required.',
        'days.*.required' => 'Each city must have number of nights.',
        'days.*.min' => 'Nights must be at least 1.',
        'stars.min' => 'Stars must be at least 1.',
        'stars.max' => 'Stars cannot exceed 5.',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    DB::beginTransaction();
    try {
        $package = UmrahPackage::findOrFail($id);

        // IMAGE UPDATE
        $imagePath = $package->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($package->image && file_exists(public_path($package->image))) {
                unlink(public_path($package->image));
            }
            
            // Upload new image
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('admin/assets/images/umrah-packages'), $imageName);
            $imagePath = 'admin/assets/images/umrah-packages/' . $imageName;
        }

        // UPDATE PACKAGE
        $package->update([
            'package_name' => $request->name,
            'month' => $request->month,
            'price_per_person' => $request->price,
            'visa_service' => $request->visa_service,
            'flight_info' => $request->flight_info,
            'description' => $request->description,
            'stars' => $request->stars,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        // DELETE OLD DETAILS
        $package->packageDetails()->delete();

        // INSERT NEW DETAILS with hotels array
        foreach ($request->cities as $index => $cityId) {
            // Check if hotel exists for this index
            if (!isset($request->hotels[$index])) {
                throw new \Exception("Hotel not specified for city at position " . ($index + 1));
            }
            
            PackageDetail::create([
                'package_id' => $package->id,
                'city_id' => $cityId,
                'hotel_id' => $request->hotels[$index],
                'time_duration' => $request->days[$index] . ' Nights',
            ]);
        }

        DB::commit();
        return redirect()->route('umrahpackages.index')
            ->with('success', 'Package updated successfully!');

    } catch (\Throwable $e) {
        DB::rollBack();
        
        // If new image was uploaded but transaction failed, delete it
        if ($request->hasFile('image') && isset($imagePath) && $imagePath != $package->image) {
            if (file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }
        }
        
        return back()->with('error', 'Error updating package: ' . $e->getMessage())->withInput();
    }
}

    // Delete package
    public function destroy($id)
    {
        $package = UmrahPackage::findOrFail($id);
        $package->packageDetails()->delete();
        $package->delete();

        return redirect()->route('umrahpackages.index')->with('success', 'Package deleted successfully!');
    }

    // Toggle package status
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $package = UmrahPackage::find($request->id);
        if (!$package) {
            return response()->json(['success' => false, 'message' => 'Package not found!']);
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