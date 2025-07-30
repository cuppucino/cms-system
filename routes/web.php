<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route(
            Auth::user()->is_admin ? 'admin.dashboard' : 'student.dashboard'
        );
    })->name('dashboard');

    // General student routes
    Route::get('/student', function () {
        return Inertia::render('student/Dashboard');
    })->name('student.dashboard');


    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    // Admin routes
    Route::get('/admin', function () {
        return Inertia::render('admin/Dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

Route::resource('users', UserController::class);
Route::resource('roles', RoleController::class);
