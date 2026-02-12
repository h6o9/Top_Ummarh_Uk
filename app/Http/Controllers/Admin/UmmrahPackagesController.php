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
public function getHotelsByCity(Request $request)
{
    $cityId = $request->city_id;
    $hotels = DB::table('hotel_city')
        ->join('hotels', 'hotel_city.hotel_id', '=', 'hotels.id')
        ->where('hotel_city.city_id', $cityId)
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
            'stars' => 'required|integer',
            'status' => 'required|in:0,1',
            'hotel_id' => 'required|exists:hotels,id',
            'cities' => 'required|array|min:1',
            'cities.*' => 'required|exists:cities,id',
            'days' => 'required|array|min:1',
            'days.*' => 'required|integer|min:1',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Package name is required.',
            'name.unique' => 'Package name already exists.',
            'image.max' => 'Image size must be less than 2MB.',
            'stars.min' => 'Stars must be at least 1.',
            'stars.max' => 'Stars cannot exceed 5.',
            'days.*.required' => 'Each city must have number of nights.',
            'cities.*.required' => 'City selection is required.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // IMAGE SAVE
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('admin/assets/images'), $imageName);
                $imagePath = 'admin/assets/images/' . $imageName;
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

            // PACKAGE DETAILS
            foreach ($request->cities as $i => $cityId) {
                PackageDetail::create([
                    'package_id' => $package->id,
                    'city_id' => $cityId,
                    'hotel_id' => $request->hotel_id,
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
        'name'          => 'required|string|max:255',
        'month'         => 'required|integer|between:1,12',
        'price'         => 'required|numeric|min:1|max:100000',
        'visa_service'  => 'required|string|max:255',
        'flight_info'   => 'required|string|max:255',
        'description'   => 'required|string',
        'stars'         => 'required|integer',
        'status'        => 'required|boolean',
        'hotel_id'      => 'required|exists:hotels,id',
        'cities'        => 'required|array|min:1',
        'cities.*'      => 'required|exists:cities,id',
        'days'          => 'required|array|min:1',
        'days.*'        => 'required|integer|min:1|max:30',
        'image'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048'
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
            if ($package->image && file_exists(public_path($package->image))) {
                unlink(public_path($package->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('admin/assets/images'), $imageName);
            $imagePath = 'admin/assets/images/' . $imageName;
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

        // INSERT NEW DETAILS
        foreach ($request->cities as $i => $cityId) {
            PackageDetail::create([
                'package_id' => $package->id,
                'city_id' => $cityId,
                'hotel_id' => $request->hotel_id,
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