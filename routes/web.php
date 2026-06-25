<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\AuthAdmin;

Route::get('/', [HomeController::class,'index'])->name('home.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware([AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class,'index'])->name('admin.index');

    //brands
    Route::get('/brands', [AdminController::class,'brands'])->name('admin.brands');
    Route::get('/brand-add', [AdminController::class,'brandAdd'])->name('admin.brand.add');
    Route::post('/brand-store', [AdminController::class,'brandStore'])->name('admin.brand.store');
    Route::get('/brand-edit/{id}', [AdminController::class,'brandEdit'])->name('admin.brand.edit');
    Route::put('/brand-udpate/{id}', [AdminController::class,'brandUpdate'])->name('admin.brand.update');
    Route::delete('/brand-delete/{id}', [AdminController::class,'brandDelete'])->name('admin.brand.delete');

    //categories
    Route::get('/categories', [AdminController::class,'categories'])->name('admin.categories');
    Route::get('/category-add', [AdminController::class,'categoryAdd'])->name('admin.category.add');
    Route::post('/category-store', [AdminController::class,'categoryStore'])->name('admin.category.store');
    Route::get('/category-edit/{id}', [AdminController::class,'categoryEdit'])->name('admin.category.edit');
    Route::put('/category-udpate/{id}', [AdminController::class,'categoryUpdate'])->name('admin.category.update');
    Route::delete('/category-delete/{id}', [AdminController::class,'categoryDelete'])->name('admin.category.delete');

    //products
    Route::get('/products', [ProductController::class,'products'])->name('admin.products');
});

require __DIR__.'/auth.php';
