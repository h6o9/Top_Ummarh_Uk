<?php

use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\FormsBookingController;
use App\Http\Controllers\Api\GetUmmarhPackagesController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SideMenueController;
use App\Http\Controllers\SideMenuPermissionController;
use App\Http\Controllers\Api\TransportController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/roles', [RoleController::class, 'store']);

Route::post('/permissions', [PermissionController::class, 'store']);
Route::post('/sidemenue', [SideMenueController::class, 'store']);

Route::post('/permission-insert', [SideMenuPermissionController::class, 'assignPermissions']);

// Ummarh Packages 
Route::get('/ummarh-packages', [GetUmmarhPackagesController::class, 'index']);
// Flight Booking
Route::post('/flight-booking', [FormsBookingController::class, 'submitFlightBooking']);
// Hotel Booking
Route::post('/hotel-booking', [FormsBookingController::class, 'submitHotelBooking']);
// Custom Umrah
Route::post('/custom-umrah-quote', [FormsBookingController::class, 'submitUmrahBooking']);
// Transport Booking
Route::get('/transport/routes',           [TransportController::class, 'getRoutes']);
Route::get('/transport/vehicles',         [TransportController::class, 'getVehiclesByRoute']);
Route::get('/transport/rate',             [TransportController::class, 'getRate']);
Route::post('/transport/booking',         [TransportController::class, 'store']);

