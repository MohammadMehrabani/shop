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

    });

});

Route::name('admin.')->prefix('admin')->group(function () {

    Route::post('login', [AdminAuthController::class, 'login'])->name('login');

    Route::group(['middleware' => ['jwt.verify:admin']], function () {

        Route::get('me', [AdminAuthController::class, 'me'])->name('me');

        // product endpoints
        Route::apiResource('products', ProductController::class)->except('update');
        Route::post('products/{product}', [ProductController::class, 'update'])->name('products.update');

    });

});

Route::get('products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
