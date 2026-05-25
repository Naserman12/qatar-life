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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/profile', [UserController::class, 'profile'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
        Route::post('/payment/create', [PaymentController::class, 'createPayment']);
        });
        Route::get('/payment/callback', [PaymentController::class, 'callback']);
Route::get('/products', [ProductController::class, 'index']);
// فلاتر
Route::get('/products/quran', [ProductController::class, 'quran']);
Route::get('/products/items', [ProductController::class, 'products']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('/cart', CartController::class);
    Route::apiResource('/products', ProductController::class)->except(['index']);
    Route::get('/admin/orders', [OrderController::class, 'allOrders']); // كل الطلبات4
    Route::put('/admin/orders/{id}', [OrderController::class, 'update']); // تحديث الطلب
     Route::post('/admin/orders/{order}/status', [OrderController::class, 'updateStatus']); // تحديث حالة الطلب
    Route::post('/admin/orders/{id}/confirm', [OrderController::class, 'confirmOrder']); // تأكيد الطلب
    Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy']); // حذف الطلب
    Route::apiResource('orders', OrderController::class);
    Route::get('/users', [UserController::class, 'index']); 
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

