<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/search/cars', [CarController::class, 'search']);
Route::name('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::name('user')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    Route::name('car')->group(function () {
        Route::post('/cars', [CarController::class, 'store']);
        Route::put('/cars/{car}', [CarController::class, 'update']);
        Route::patch('/cars/{car}', [CarController::class, 'patch']);
        Route::delete('/cars/{id}', [CarController::class, 'destroy']);
    });
    Route::name('payment')->group(function () {
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/rental/{rental}', [PaymentController::class, 'getByRental']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);

    });
    Route::name('rental')->group(function () {
        Route::post('/rentals', [RentalController::class, 'store']);
        Route::get('/rentals', [RentalController::class, 'index']);
        Route::get('/rentals/{rental}', [RentalController::class, 'show']);
        Route::get('/rentals/user/{user}', [RentalController::class, 'getByUser']);
        Route::get('/rentals/car/{car}', [RentalController::class, 'getByCar']);
        ;
    });//payment-cancel/32

});
Route::get('/payment-cancel/{rental}', [RentalController::class, 'cancel']);
Route::get('/payment-success/{rental}', [RentalController::class, 'success']);




Route::name('car')->group(function () {
Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{car}', [CarController::class, 'show']);

});
