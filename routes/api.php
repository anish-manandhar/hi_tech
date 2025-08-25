<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('/orders', OrderController::class);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateOrderStatus'])->name('order.status.update');
});
