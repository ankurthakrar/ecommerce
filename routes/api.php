<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\GeneralController;

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

    // Admin ROUTE

    // Category 

    Route::get('category-list', [AdminController::class, 'categoryList'])->name('category-list');
    Route::post('category-store', [AdminController::class,'categoryStore'])->name('category-store');
    Route::get('category-detail/{id}', [AdminController::class, 'categoryDetail'])->name('category-detail');
    Route::post('category-update', [AdminController::class,'categoryUpdate'])->name('category-update');

    // Tags 

    Route::get('tag-list', [AdminController::class, 'tagList'])->name('tag-list');
    Route::post('tag-store', [AdminController::class,'tagStore'])->name('tag-store');
    Route::get('tag-detail/{id}', [AdminController::class, 'tagDetail'])->name('tag-detail');
    Route::post('tag-update', [AdminController::class,'tagUpdate'])->name('tag-update');
    
    // Product

    Route::get('product-list', [AdminController::class, 'productList'])->name('product-list');
    Route::post('product-store', [AdminController::class,'productStore'])->name('product-store');
    Route::get('product-detail/{id}', [AdminController::class, 'productDetail'])->name('product-detail');
    Route::post('product-update', [AdminController::class,'productUpdate'])->name('product-update');
    
    // USER ROUTE
    
    // Cart

    Route::get('cart-item-list', [CustomerController::class, 'cartItemList'])->name('cart-item-list');
    Route::post('cart-item-store', [CustomerController::class, 'cartItemStore'])->name('cart-item-store');
    Route::post('cart-item-delete', [CustomerController::class, 'cartItemDelete'])->name('cart-item-delete');
    
    // Address

    Route::get('address-list', [CustomerController::class, 'addressList'])->name('address-list');
    Route::post('address-store', [CustomerController::class, 'addressStore'])->name('address-store');
    Route::get('address-detail/{id}', [CustomerController::class, 'addressDetail'])->name('address-detail');
    Route::post('address-delete', [CustomerController::class, 'addressDelete'])->name('address-delete');
    Route::post('address-update', [CustomerController::class,'addressUpdate'])->name('address-update');
    
    // GENERAL ROUTE

    // GET ONLY CATEGORY AND SUBCATEGORY

    Route::get('get-parent-category-list', [GeneralController::class, 'getParentCategoryList'])->name('get-parent-category-list');
    Route::get('get-parent-subcategory-list/{id}', [GeneralController::class, 'getParentSubcategoryList'])->name('get-parent-subcategory-list');
    Route::get('get-category-tag-list', [GeneralController::class, 'getCategoryTagList'])->name('get-category-tag-list');

    Route::get('get-state-list', [GeneralController::class, 'getStateList'])->name('get-state-list');
    Route::get('get-city-list/{id}', [GeneralController::class, 'getCityList'])->name('get-city-list');

    // GET AND FILTER PRODUCT

    Route::post('get-product-list', [GeneralController::class, 'getProductList'])->name('get-product-list');

    
    Route::get('log-out', [CustomerController::class,'logout'])->name('log-out');
});


# ADMIN OR CLIENT SIDE SAME ROUTE

// Route::get('product-detail/{id}', [AdminController::class, 'productDetail'])->name('product-detail');