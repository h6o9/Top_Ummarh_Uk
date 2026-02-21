<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormsBookingController extends Controller
{
    // Flight Booking
    public function submitFlightBooking(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trip_type'      => 'required|string|max:50',
                'departure'      => 'required|string|max:100',
                'destination'    => 'required|string|max:100',
                'departure_date' => 'required|date|after_or_equal:today',
                'return_date'    => 'nullable|date|after_or_equal:departure_date',
                'cabin_class'    => 'required|string|max:50',
                'airline'        => 'required|string|max:100',
                'adults'         => 'required|integer|min:1',
                'children'       => 'nullable|integer|min:0',
                'infants'        => 'nullable|integer|min:0',
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|max:255',
                'mobile'         => 'required|string|max:20',
            ], [
                'trip_type.required' => 'Trip type is required',
                'departure.required' => 'Departure is required',
                'destination.required' => 'Destination is required',
                'departure_date.required' => 'Departure date is required',
                'departure_date.after_or_equal' => 'Departure date must be today or later',
                'return_date.after_or_equal' => 'Return date must be after departure date',
                'adults.min' => 'At least one adult is required',
                'email.email' => 'Email must be a valid email address',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422); 
            }

            $regCode = 'FlightBook-' . rand(100000, 999999);

            $booking = FlightBooking::create([
                'reg_code'       => $regCode,
                'trip_type'      => $request->trip_type,
                'departure'      => $request->departure,
                'destination'    => $request->destination,
                'departure_date' => $request->departure_date,
                'return_date'    => $request->return_date,
                'cabin_class'    => $request->cabin_class,
                'airline'        => $request->airline,
                'adults'         => $request->adults,
                'children'       => $request->children ?? 0,
                'infants'        => $request->infants ?? 0,
                'name'           => $request->name,
                'email'          => $request->email,
                'mobile'         => $request->mobile,
                'status'         => 'pending',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Flight booking created successfully',
                'data' => $booking
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Hotel Booking
    public function submitHotelBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'mobile'         => 'required|string|max:20',
            'destination'    => 'required|string|max:255',
            'check_in_date'  => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'rooms'          => 'required|integer|min:1|max:10',
            'adults'         => 'required|integer|min:1|max:10',
            'children'       => 'nullable|integer|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $regCode = 'HotelBook-' . rand(100000, 999999);

        try {
            $booking = HotelBooking::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'mobile'         => $request->mobile,
                'destination'    => $request->destination,
                'check_in_date'  => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'rooms'          => $request->rooms,
                'adults'         => $request->adults,
                'children'       => $request->children ?? 0,
                'status'         => 'pending',
                'reg_code'       => $regCode,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Hotel booking created successfully',
                'data' => $booking
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Custom Umrah Booking
    public function submitUmrahBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:100',
            'phone_number'     => 'required|string|max:20',
            'email'            => 'required|email|max:150',
            'no_of_passengers' => 'required|integer|min:1',
            'travel_date'      => 'required|date',
            'makkah_nights'    => 'required|integer|min:0',
            'madinah_nights'   => 'required|integer|min:0',
            'accommodation'    => 'required|string|max:100',
        ], [
            'name.required'             => 'Please enter your name.',
            'phone_number.required'     => 'Please enter your phone number.',
            'email.required'            => 'Please enter your email address.',
            'email.email'               => 'Please enter a valid email address.',
            'no_of_passengers.required' => 'Please enter the number of passengers.',
            'no_of_passengers.min'      => 'At least 1 passenger is required.',
            'travel_date.required'      => 'Please enter the travel date.',
            'travel_date.date'          => 'Please enter a valid date format.',
            'makkah_nights.required'    => 'Please enter Makkah nights.',
            'madinah_nights.required'   => 'Please enter Madinah nights.',
            'accommodation.required'    => 'Please select accommodation.',       
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $customCode = 'CustomUmmarh-' . rand(100000, 999999);

            $quote = DB::table('umrah_quote_requests')->insertGetId([
                'name'             => $request->name,
                'phone_number'     => $request->phone_number,
                'email'            => $request->email,
                'no_of_passengers' => $request->no_of_passengers,
                'travel_date'      => $request->travel_date,
                'makkah_nights'    => $request->makkah_nights,
                'madinah_nights'   => $request->madinah_nights,
                'accommodation'    => $request->accommodation,
                'custom_code'      => $customCode,
                'created_at'       => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Umrah quote request successfully submit ho gayi!',
                'data'    => ['id' => $quote],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kuch masla aaya, dobara koshish karein.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
