<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Product
Route::get('/products', [ProductController::class, 'index'])
    ->name('product');

Route::post('/products', [ProductController::class, 'store'])
    ->name('product.store');

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('product.show');

Route::put('/products/{product}', [ProductController::class, 'update'])
    ->name('product.update');

Route::patch('/products/{product}', [ProductController::class, 'update'])
    ->name('product.patch');

Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->name('product.destroy');

// Kategori
Route::get('/kategoris', [KategoriController::class, 'index'])
    ->name('kategori');

Route::post('/kategoris', [KategoriController::class, 'store'])
    ->name('kategori.store');

Route::get('/kategoris/{kategori}', [KategoriController::class, 'show'])
    ->name('kategori.show');

Route::put('/kategoris/{kategori}', [KategoriController::class, 'update'])
    ->name('kategori.update');

Route::patch('/kategoris/{kategori}', [KategoriController::class, 'update'])
    ->name('kategori.patch');

Route::delete('/kategoris/{kategori}', [KategoriController::class, 'destroy'])
    ->name('kategori.destroy');