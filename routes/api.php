<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RegistrationController;
use App\Http\Controllers\Api\V1\OtpController;
use App\Http\Controllers\Api\V1\AdminRegistrationController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| /api/v1/...
|
*/

Route::prefix('v1')->group(function () {

    /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     *  PUBLIC ROUTES (No Auth Required)
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

    // Registration
    Route::post('/register', [RegistrationController::class, 'store']);
    Route::get('/registration/status', [RegistrationController::class, 'checkStatus']);
    Route::get('/registration/settings', [RegistrationController::class, 'publicSettings']);

    // OTP
    Route::post('/otp/send', [OtpController::class, 'send']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);

    // Admin Auth
    Route::post('/admin/login', [AuthController::class, 'login']);

    /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     *  PROTECTED ROUTES (Admin Auth Required)
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('admin')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Registrations Management
        Route::get('/registrations', [AdminRegistrationController::class, 'index']);
        Route::get('/registrations/export', [AdminRegistrationController::class, 'export']);
        Route::get('/registrations/{id}', [AdminRegistrationController::class, 'show']);
        Route::put('/registrations/{id}', [AdminRegistrationController::class, 'update']);
        Route::delete('/registrations/{id}', [AdminRegistrationController::class, 'destroy']);

        // Settings
        Route::get('/settings', [SettingsController::class, 'index']);
        Route::put('/settings', [SettingsController::class, 'update']);
        Route::post('/settings/open-now', [SettingsController::class, 'openNow']);
        Route::post('/settings/close-now', [SettingsController::class, 'closeNow']);
    });
});
