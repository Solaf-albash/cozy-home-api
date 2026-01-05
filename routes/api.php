<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FavorityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('admin/login-as-default', [AdminController::class, 'loginAsDefaultAdmin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::patch('/admin/users/{user}/approve', [AdminController::class, 'approveUser'])->middleware('role:admin');
    Route::patch('/admin/users/{user}/reject', [AdminController::class, 'rejectUser'])->middleware('role:admin');
    Route::get('/admin/pending-users', [AdminController::class, 'getPendingUsers'])->middleware('role:admin');

    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'update']);

    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('my-bookings', [BookingController::class, 'index']);
    Route::put('bookings/{booking}', [BookingController::class, 'update']);

    Route::get('bookings/{booking}', [BookingController::class, 'show']);
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::get('bookings/{booking}', [BookingController::class, 'show']);


    Route::patch('owner/bookings/{booking}/approve', [BookingController::class, 'approve'])->middleware('role:owner');
    Route::patch('owner/bookings/{booking}/reject', [BookingController::class, 'reject'])->middleware('role:owner');
    Route::get('/owner/bookings', [BookingController::class, 'getOwnerBookings'])->middleware('role:owner');


    // --- Favorite Routes ---
    Route::get('/favorites', [FavorityController::class, 'index']);
    Route::post('/favorites/toggle/{apartment}', [FavorityController::class, 'toggle']);
    // --- Review Route ---
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store']);
});


Route::get('apartments', [ApartmentController::class, 'index']);
Route::get('apartments/{apartment}', [ApartmentController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:owner'])->group(function () {
    Route::post('apartments', [ApartmentController::class, 'store']);
    Route::put('apartments/{apartment}', [ApartmentController::class, 'update']); // لاحقاً
    Route::delete('apartments/{apartment}', [ApartmentController::class, 'destroy']); // لاحقاً
});
