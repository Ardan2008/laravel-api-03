<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('jwt')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    });
});

// endpoint baca tetap publik supaya beranda bisa memanggilnya tanpa login
Route::get('/products', [ProductController::class, 'index'])->name('product');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('product.show');

Route::get('/kategoris', [KategoriController::class, 'index'])->name('kategori');
Route::get('/kategoris/{kategori}', [KategoriController::class, 'show'])->name('kategori.show');

// semua operasi tulis wajib lewat JWT
Route::middleware('jwt')->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('product.patch');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

    Route::post('/kategoris', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategoris/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::patch('/kategoris/{kategori}', [KategoriController::class, 'update'])->name('kategori.patch');
    Route::delete('/kategoris/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});