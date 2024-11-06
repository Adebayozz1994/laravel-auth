<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/sanctum/csrf-cookie', [\Laravel\Sanctum\Http\Controllers\CsrfCookieController::class, 'show']);
Route::middleware('auth:sanctum')->get('/user', function () {
    return response()->json([
        'status' => true,
        'user' => Auth::user(),
    ]);
});
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
Route::get('/logout', function () {
    Auth::logout();
    return response()->json([
        'status' => true,
        'message' => 'Logout successfully',
    ]);
})->middleware('auth:sanctum');

Route::middleware('guest')->group(function () {
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset'); // Ensure this route is defined
    // ... other routes
});

Route::put('password', [PasswordController::class, 'update'])->name('password.update');

Route::post('/uploadProfilePic',[AuthenticatedSessionController::class, 'uploadPicture']);

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
