<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // Product routes
    Route::apiResource('products', ProductController::class);
    // Inventory routes
    Route::get('inventory', [InventoryController::class, 'index']);
    Route::post('inventory/{product}/stock-in', [InventoryController::class, 'stockIn']);
    // Sales routes (Steps 8 & 9)
    // Report routes (Step 10)
});
