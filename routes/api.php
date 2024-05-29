<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\rateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

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
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/profile', [UserController::class, 'profile']);

//------------------------forget password-------------------
Route::post('forgotpassword', [UserController::class, 'forgotPassword']);
Route::post('resetpassword', [UserController::class, 'resetPassword']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/home', [UserController::class, 'redirect']);
Route::apiResource('products', ProductController::class); // api for products
Route::get('/product', [ProductController::class, 'show']);
Route::post('product-rate/{id}', [rateController::class, 'store']); // api for rating product
Route::apiResource('orders', OrderController::class);  // api for order show all orders, show specific order, make order, delete order 
Route::get('/order', [OrderController::class, 'show']);
