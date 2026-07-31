<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index'])->name('product');
Route::post('/products', [ProductController::class, 'store'])->name('product.store');

Route::put('/products/{product}', [ProductController::class, 'update'])->name('product.update');
Route::patch('/products/{product}', [ProductController::class, 'update'])->name('product.patch');

Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.destroy'); 