<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CustomerController;

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

Route::post('send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
Route::post('is-exist', [AuthController::class, 'isExist'])->name('is-exist');
Route::post('register-with-password', [AuthController::class, 'registerWithPassword'])->name('register-with-password');
Route::post('login-with-password', [AuthController::class, 'loginWithPassword'])->name('login-with-password');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

Route::middleware('auth:api')->group(function () {

    // Admin route
    Route::get('category-list', [AdminController::class, 'categoryList'])->name('category-list');
    Route::post('category-store', [AdminController::class,'categoryStore'])->name('category-store');
    Route::get('category-detail/{id}', [AdminController::class, 'categoryDetail'])->name('category-detail');
    Route::post('category-update', [AdminController::class,'categoryUpdate'])->name('category-update');

    Route::get('log-out', [CustomerController::class,'logout'])->name('log-out');
});