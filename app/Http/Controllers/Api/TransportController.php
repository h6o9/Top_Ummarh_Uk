<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransportController extends Controller
{
    // ✅ Route aur Vehicle dropdown ke liye data
    public function getRoutes()
    {
        try {
            $routes = DB::table('transport_rates')
                ->select('route')
                ->distinct()
                ->get()
                ->pluck('route');

            return response()->json([
                'success' => true,
                'routes'  => $routes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Routes load nahi hue.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ✅ Route select karne ke baad vehicles load hon
    public function getVehiclesByRoute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $vehicles = DB::table('transport_rates')
                ->where('route', $request->route)
                ->select('vehicle', 'rate_per_passenger')
                ->get();

            return response()->json([
                'success'  => true,
                'vehicles' => $vehicles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicles load nahi hue.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ✅ Route + Vehicle ke basis par Rate fetch karo
    public function getRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route'   => 'required|string',
            'vehicle' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $rate = DB::table('transport_rates')
                ->where('route', $request->route)
                ->where('vehicle', $request->vehicle)
                ->first();

            if (!$rate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Is route aur vehicle ke liye koi rate nahi mila.',
                ], 404);
            }

            return response()->json([
                'success'           => true,
                'rate_per_passenger' => $rate->rate_per_passenger,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rate fetch nahi hua.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ✅ Form Submit - Booking Save karo
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route'      => 'required|string|max:150',
            'vehicle'    => 'required|string|max:100',
            'passengers' => 'required|integer|min:1',
        ], [
            'route.required'      => 'Route select karein.',
            'vehicle.required'    => 'Vehicle select karein.',
            'passengers.required' => 'Passengers ki tadad likhein.',
            'passengers.min'      => 'Kam az kam 1 passenger hona chahiye.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Rate fetch karo transport_rates table se
            $rateData = DB::table('transport_rates')
                ->where('route', $request->route)
                ->where('vehicle', $request->vehicle)
                ->first();

            if (!$rateData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Is route aur vehicle ke liye rate available nahi hai.',
                ], 404);
            }

            $ratePerPassenger = $rateData->rate_per_passenger;
            $totalAmount      = $ratePerPassenger * $request->passengers;

            // Booking save karo
            $bookingId = DB::table('transport_bookings')->insertGetId([
                'route'              => $request->route,
                'vehicle'            => $request->vehicle,
                'passengers'         => $request->passengers,
                'rate_per_passenger' => $ratePerPassenger,
                'total_amount'       => $totalAmount,
                'created_at'         => now(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Booking successfully ho gayi!',
                'data'     => [
                    'booking_id'         => $bookingId,
                    'route'              => $request->route,
                    'vehicle'            => $request->vehicle,
                    'passengers'         => $request->passengers,
                    'rate_per_passenger' => $ratePerPassenger . ' Riyal',
                    'total_amount'       => $totalAmount . ' Riyal',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking save nahi hui, dobara koshish karein.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}