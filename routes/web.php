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
use App\Http\Controllers\Admin\InvitationController as AdminInvitationController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\SessionRegistrationController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\CourseController;

/**
 * Student controllers
 */


use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\RegistrationController as StudentRegistrationController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\Student\InvitationController as StudentInvitationController;
use App\Http\Controllers\Student\SessionController as StudentSessionController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

// ✅ Public QR check-in (anyone scanning the code can access)
Route::get('/attendance/checkin/{token}', [AttendanceController::class, 'checkIn'])
    ->name('attendance.checkIn');

/*
|--------------------------------------------------------------------------
| Authenticated Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Smart redirect to the right dashboard
    Route::get('/dashboard', function () {
        return redirect()->route(
            Auth::user()->is_admin ? 'admin.dashboard' : 'student.dashboard'
        );
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */
    Route::prefix('student')->as('student.')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [StudentProfileController::class, 'update'])->name('profile.update');

        // Payments
        Route::get('payments', [StudentPaymentController::class, 'index'])->name('payments.index'); // <-- add
        Route::get('payments/create/{gown}', [StudentPaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [StudentPaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{id}/receipt', [StudentPaymentController::class, 'show'])->name('payments.receipt');

        // Registration
        Route::prefix('registration')->as('registration.')->group(function () {
            Route::get('/', [StudentRegistrationController::class, 'index'])->name('index');
            Route::post('/', [StudentRegistrationController::class, 'store'])->name('store');
            Route::put('{id}', [StudentRegistrationController::class, 'update'])->name('update');
            Route::delete('{id}', [StudentRegistrationController::class, 'destroy'])->name('destroy');
        });

        // Invitations
        Route::get('invitation', [StudentInvitationController::class, 'show'])->name('invitation.show');
        Route::get('invitation/download', [StudentInvitationController::class, 'download'])->name('invitation.download');
        Route::get('invitation/preview', [StudentInvitationController::class, 'preview'])->name('invitation.preview');

        // Notifications
        Route::get('notifications', [StudentNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id}/read', [StudentNotificationController::class, 'markAsRead'])->name('notifications.read');

        // Sessions
        Route::get('/session', [StudentSessionController::class, 'show'])->name('session.show');
        Route::post('/session/select', [StudentSessionController::class, 'select'])->name('session.select');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->as('admin.')
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->group(function () {

            Route::get('/', fn() => Inertia::render('admin/Dashboard'))->name('dashboard');

            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
            Route::resource('gowns', GownController::class);
            Route::resource('gown-collections', GownCollectionController::class)->only(['index', 'update']);
            Route::resource('sessions', ConvocationSessionController::class);
            Route::resource('registrations', SessionRegistrationController::class)->only(['index']);

            // Attendance (admin management only)
            Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('attendance/manual/{id}', [AttendanceController::class, 'manualCheckIn'])->name('attendance.manualCheckIn');
            Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');

            // Invitations
            Route::get('invitations', [AdminInvitationController::class, 'index'])->name('invitations.index');
            Route::get('invitations/create', [AdminInvitationController::class, 'create'])->name('invitations.create');
            Route::post('invitations', [AdminInvitationController::class, 'store'])->name('invitations.store');
            Route::post('invitations/{invitation}/revoke', [AdminInvitationController::class, 'revoke'])->name('invitations.revoke');

            // Reports
            Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

            // Notifications
            Route::resource('notifications', AdminNotificationController::class)->only(['index', 'create', 'store']);

            // Payments (admin index)
            Route::resource('payment', AdminPaymentController::class)->only(['index']);

            // Courses
            Route::resource('courses', CourseController::class);
        });
});

/*
|--------------------------------------------------------------------------
| Extra
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
