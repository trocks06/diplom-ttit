<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\MedicalFileController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SpecializationController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Публичные роуты (Доступны всем)
|--------------------------------------------------------------------------
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Справочники (обычно нужны при регистрации или поиске врача без авторизации)
Route::get('specializations', [SpecializationController::class, 'index']);
Route::get('specializations/{specialization}', [SpecializationController::class, 'show']);
Route::get('statuses', [StatusController::class, 'index']);

// Восстановление пароля
Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail'])->middleware('guest');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('guest');


/*
|--------------------------------------------------------------------------
| Защищенные роуты (Только для авторизованных auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'me']);
        Route::patch('/', [UserController::class, 'updateProfile']);
        Route::patch('/avatar', [UserController::class, 'updateAvatar']);
        Route::delete('/', [UserController::class, 'deleteMe']);
    });
    Route::get('/my-appointments', [AppointmentController::class, 'myAppointments']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('throttle:6,1');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
    Route::post('appointments/{appointment}/review', [ReviewController::class, 'store']);
    Route::apiResource('medical-records', MedicalRecordController::class)->only(['show', 'update', 'destroy']);
    Route::post('/appointments/{appointment}/medical-record', [MedicalRecordController::class, 'store']);
    Route::post('/medical-records/{medical_record}/files', [MedicalFileController::class, 'store']);
    Route::delete('/medical-files/{medical_file}', [MedicalFileController::class, 'destroy']);
    Route::apiResource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('roles', RoleController::class)->only(['index', 'show']);
    Route::apiResource('specializations', SpecializationController::class)->except(['index', 'show']);
});


