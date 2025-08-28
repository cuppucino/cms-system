<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Admin controllers
 */
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
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;

/**
 * Student/controllers & shared
 */
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified'])->group(function () {

    // Smart redirect to the right dashboard
    Route::get('/dashboard', function () {
        return redirect()->route(
            Auth::user()->is_admin ? 'admin.dashboard' : 'student.dashboard'
        );
    })->name('dashboard');

    // General (if you still need this for users)
    Route::resource('products', ProductController::class)
        ->only(['index','create','store','edit','update','destroy']);

    /*
    |----------------------------------------------------------------------
    | Student
    |----------------------------------------------------------------------
    */
    Route::prefix('student')->as('student.')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [StudentProfileController::class, 'update'])->name('profile.update');

        // Payments (student)
        Route::get('payments/create/{gown}', [StudentPaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [StudentPaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{id}/receipt', [StudentPaymentController::class, 'show'])->name('payments.receipt');
    });

    /*
    |----------------------------------------------------------------------
    | Admin
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->as('admin.')
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->group(function () {

        Route::get('/', fn () => Inertia::render('admin/Dashboard'))->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('gowns', GownController::class);
        Route::resource('gown-collections', GownCollectionController::class)->only(['index','update']);
        Route::resource('sessions', ConvocationSessionController::class);
        Route::resource('registrations', SessionRegistrationController::class)->only(['index']);

        // Attendance
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/manual/{id}', [AttendanceController::class, 'manualCheckIn'])->name('attendance.manualCheckIn');
        Route::get('attendance/checkin/{token}', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
        Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');

        // Invitation
        Route::get('invitation/{id}', [InvitationLetterController::class, 'show'])->name('invitation.show');

        // Reports
        Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

        // Notifications
        Route::resource('notifications', NotificationController::class)->only(['index','create','store']);

        // Payments (admin index)
        Route::resource('payments', AdminPaymentController::class)->only(['index']);
    });
});

/*
|--------------------------------------------------------------------------
| Extra
|--------------------------------------------------------------------------
*/
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
