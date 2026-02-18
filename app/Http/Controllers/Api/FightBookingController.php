<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlightBooking;

class FightBookingController extends Controller
{
    public function store(Request $request)
{
    try {

        $booking = FlightBooking::create([
            'trip_type'      => $request->trip_type,
            'departure'      => $request->departure,
            'destination'    => $request->destination,
            'departure_date' => $request->departure_date,
            'return_date'    => $request->return_date,
            'cabin_class'    => $request->cabin_class,
            'airline'        => $request->airline,
            'adults'         => $request->adults,
            'children'       => $request->children,
            'infants'        => $request->infants,
            'name'           => $request->name,
            'email'          => $request->email,
            'mobile'         => $request->mobile,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Flight booking successfully',
        ], 200); // ✅ Created

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500); // ✅ Server Error
    }
}
}