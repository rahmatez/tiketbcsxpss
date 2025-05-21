<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Midtrans Payment Notification Callback URL
Route::post('/payment/notification', [App\Http\Controllers\PaymentController::class, 'handleNotification']);

// Untuk testing status pembayaran
Route::get('/payment/check-status/{order}', [App\Http\Controllers\PaymentController::class, 'checkStatus']);

// Get cities by province
Route::get('/provinces/{province}/cities', function(App\Models\Province $province) {
    return $province->cities()->orderBy('name')->get();
});
