<?php

use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Auth::routes();
Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::middleware(['auth'])->group(function (){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');  
});

Route::middleware(['auth', AuthAdmin::class])->group(function (){
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands'); 
    Route::get('/admin/brand/add', [AdminController::class, 'add_brand'])->name('admin.brand.add'); 
    Route::post('admin/brand/store', [AdminController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('admin/brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('admin/brand/update', [AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('admin/brand/{id}/delete', [AdminController::class, 'brand_delete'])->name('admin.brand.delete');

    Route::get('admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('admin/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('admin/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('admin/category/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category.edit');
});