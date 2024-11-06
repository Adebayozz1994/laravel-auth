<?php

use App\Http\Controllers\AdminAuth\AuthenticatedSessionController;
use App\Http\Controllers\AdminAuth\ConfirmablePasswordController;
use App\Http\Controllers\AdminAuth\EmailVerificationNotificationController;
use App\Http\Controllers\AdminAuth\NewPasswordController;
use App\Http\Controllers\AdminAuth\PasswordResetLinkController;
use App\Http\Controllers\AdminAuth\RegisteredAdminController;
use App\Http\Controllers\AdminAuth\EmailVerificationPromptController;
use App\Http\Controllers\AdminAuth\PasswordController;
use App\Http\Controllers\AdminAuth\VerifyEmailController;
use App\Http\Middleware\CheckAdminISHeadAdmin;
use App\Http\Middleware\CheckGuest;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('register', [RegisteredAdminController::class, 'create'])
                ->name('admin.register')->middleware(CheckAdminISHeadAdmin::class);

    Route::post('register', [RegisteredAdminController::class, 'store'])
                ->name('admin.register');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('admin.login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('admin.password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('admin.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('admin.password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('admin.password.store');
    Route::post('get', [AuthenticatedSessionController::class, 'getAdmin']);
});
Route::post('/admin/getAdmin', [AuthenticatedSessionController::class, 'getAdmin']);

Route::prefix('admin')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('admin.verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('admin.verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('admin.verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'updateAdminPassword'])->name('admin.password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('admin.logout');
});
