<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
// Users
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Api\UserController;

Route::apiResource('products', ProductController::class);
Route::apiResource('cart', CartController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']); // كل الطلبات
    Route::get('/admin/orders', [OrderController::class, 'allOrders']); // كل الطلبات
    Route::post('/admin/orders/{id}/confirm', [OrderController::class, 'confirmOrder']); // تأكيد الطلب
    Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy']); // حذف الطلب
    Route::apiResource('orders', OrderController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/profile', [UserController::class, 'profile'])->middleware('auth:sanctum');
