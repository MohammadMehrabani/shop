<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::name('user.')->prefix('user')->group(function () {

    Route::post('authenticate', [UserAuthController::class, 'authenticate'])->name('authenticate');
    Route::post('send/otp', [UserAuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('verify/otp', [UserAuthController::class, 'verifyOtp'])->name('verifyOtp');
    Route::post('login', [UserAuthController::class, 'login'])->name('login');
    Route::post('register', [UserAuthController::class, 'register'])->name('register');
    Route::post('refresh', [UserAuthController::class, 'refresh'])->name('refreshToken');

    Route::group(['middleware' => ['jwt.verify:user']], function () {

        Route::get('me', [UserAuthController::class, 'me'])->name('me');
        Route::post('logout', [UserAuthController::class, 'logout'])->name('logout');

        Route::post('orders', [\App\Http\Controllers\User\OrderController::class, 'store'])->name('orders.store');
        Route::get('orders', [\App\Http\Controllers\User\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\User\OrderController::class, 'show'])->name('orders.show');

    });

});

Route::name('admin.')->prefix('admin')->group(function () {

    Route::post('login', [AdminAuthController::class, 'login'])->name('login');

    Route::group(['middleware' => ['jwt.verify:admin']], function () {

        Route::get('me', [AdminAuthController::class, 'me'])->name('me');

        // product endpoints
        Route::apiResource('products', ProductController::class);
        // order endpoints
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');

    });

});

Route::get('products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
