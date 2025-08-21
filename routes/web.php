<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\GownController;
use App\Http\Controllers\Admin\GownCollectionController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect users to correct dashboard
    Route::get('/dashboard', function () {
        return redirect()->route(
            Auth::user()->is_admin ? 'admin.dashboard' : 'student.dashboard'
        );
    })->name('dashboard');

    // Student dashboard
    Route::get('/student', function () {
        return Inertia::render('student/Dashboard');
    })->name('student.dashboard');

    // Product routes (general users)
    Route::resource('products', ProductController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', function () {
            return Inertia::render('admin/Dashboard');
        })->name('dashboard');

        // Users / Roles / Gowns
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('gowns', GownController::class);
        Route::resource('gown-collections', GownCollectionController::class)->only(['index', 'update']);
    });




/*
|--------------------------------------------------------------------------
| Extra Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
