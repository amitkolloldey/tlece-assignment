<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::resource('products', ProductController::class)->middleware('auth');
Route::resource('orders', OrderController::class)->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/products/search/result', [ProductController::class, 'search']);
});

Route::delete('orders/{order}/remove_product', [OrderController::class, 'removeProduct'])->name('orders.remove_product');

Route::get('/invoices/{id}/generate', [InvoiceController::class, 'generate'])
    ->name('invoices.generate');
