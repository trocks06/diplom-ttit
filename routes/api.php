<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\MedicalFileController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SpecializationController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [ResetPasswordController::class, 'reset']);
Route::apiResource('specializations', SpecializationController::class)->only(['index', 'show']);
Route::apiResource('doctors', DoctorController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('my')->group(function () {
        Route::get('profile', [UserController::class, 'me']);
        Route::patch('profile', [UserController::class, 'updateProfile']);
        Route::patch('profile/avatar', [UserController::class, 'updateAvatar']);
        Route::delete('profile', [UserController::class, 'deleteMe']);
    });
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1');
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::apiResource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
    Route::post('appointments/{appointment}/review', [ReviewController::class, 'store']);
    Route::apiResource('schedules', ScheduleController::class)->only(['index', 'show']);
    Route::apiResource('medical-records', MedicalRecordController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('appointments/{appointment}/medical-record', [MedicalRecordController::class, 'store']);
    Route::post('medical-records/{medical_record}/files', [MedicalFileController::class, 'store']);
    Route::patch('medical-files/{medical_file}', [MedicalFileController::class, 'update']);
    Route::delete('medical-files/{medical_file}', [MedicalFileController::class, 'destroy']);
    Route::apiResource('reviews', ReviewController::class)->only(['update', 'destroy']);
    Route::apiResource('notifications', NotificationController::class)->only(['index', 'show']);
    Route::apiResource('patients', PatientController::class)->only(['index', 'show']);
    Route::apiResource('doctors', DoctorController::class)->only(['index', 'show']);
    Route::get('medical-files/{medical_file}/download', [MedicalFileController::class, 'download'])
        ->name('medical-files.download');
});

Route::middleware(['auth:sanctum', 'role:Администратор'])->group(function () {
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'destroy']);
    Route::apiResource('patients', PatientController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('doctors', DoctorController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('schedules', ScheduleController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('specializations', SpecializationController::class)
        ->only(['store', 'update', 'destroy']);
    Route::apiResource('statuses', StatusController::class)->only(['index', 'show']);
    Route::post('notifications', [NotificationController::class, 'store']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::apiResource('roles', RoleController::class)->only(['index', 'show']);
    Route::prefix('reports')->group(function () {
        Route::get('patients', [ReportController::class, 'patients']);
        Route::get('doctors', [ReportController::class, 'doctors']);
        Route::get('canceled', [ReportController::class, 'canceled']);
        Route::get('satisfaction', [ReportController::class, 'satisfaction']);
    });
});
