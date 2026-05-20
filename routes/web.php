<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
    Route::resource('products', ProductController::class)->except(['show']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
