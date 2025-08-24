<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\GownController;
use App\Http\Controllers\Admin\GownCollectionController;
use App\Http\Controllers\Admin\ConvocationSessionController;
use App\Http\Controllers\Admin\InvitationLetterController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\SessionRegistrationController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\NotificationController;



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

        // Users / Roles / Gowns / Sessions
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('gowns', GownController::class);
        Route::resource('gown-collections', GownCollectionController::class)->only(['index', 'update']);
        Route::resource('sessions', ConvocationSessionController::class);
        Route::resource('registrations', SessionRegistrationController::class)->only(['index']);


        // Attendance
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/manual/{id}', [AttendanceController::class, 'manualCheckIn'])->name('attendance.manualCheckIn');
        Route::get('attendance/checkin/{token}', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');

        // Invitation
        Route::get('invitation/{id}', [InvitationLetterController::class, 'show'])->name('invitation.show');

        //Report
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

        // Notifications
        Route::resource('notifications', NotificationController::class)->only(['index', 'create', 'store']);
    });




/*
|--------------------------------------------------------------------------
| Extra Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
