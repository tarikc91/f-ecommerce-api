<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\CategoryProductsController;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index']);
    Route::get('{publishedProduct}', [ProductsController::class, 'show']);
});

Route::get('categories/{category}/products', [CategoryProductsController::class, 'index']);
Route::post('orders', [OrdersController::class, 'store']);
