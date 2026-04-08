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
Route::apiResource('statuses', StatusController::class)->only(['index', 'show']);
Route::apiResource('doctors', DoctorController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'me']);
        Route::patch('/', [UserController::class, 'updateProfile']);
        Route::patch('avatar', [UserController::class, 'updateAvatar']);
        Route::delete('/', [UserController::class, 'deleteMe']);
    });

    // Мои записи на приём (пациент и врач видят свои)
    Route::get('my-appointments', [AppointmentController::class, 'myAppointments']);

    // Выход и смена пароля
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Верификация email
    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1');
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');

    // Уведомления (каждый видит свои, админ – все)
    Route::apiResource('notifications', NotificationController::class);

    // Записи на приём
    Route::apiResource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
    Route::post('appointments/{appointment}/review', [ReviewController::class, 'store']);

    // Медицинские записи и файлы
    Route::apiResource('medical-records', MedicalRecordController::class)->only(['show', 'update', 'destroy']);
    Route::post('appointments/{appointment}/medical-record', [MedicalRecordController::class, 'store']);
    Route::post('medical-records/{medical_record}/files', [MedicalFileController::class, 'store']);
    Route::patch('medical-files/{medical_file}', [MedicalFileController::class, 'update']);
    Route::delete('medical-files/{medical_file}', [MedicalFileController::class, 'destroy']);

    // Отзывы (изменение/удаление только автором или админом)
    Route::apiResource('reviews', ReviewController::class)->only(['update', 'destroy']);

    Route::apiResource('users', UserController::class)->only(['index', 'show'])
        ->middleware('role:Администратор');
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('role:Администратор');


    // Пациенты (управление только админ)
    Route::apiResource('patients', PatientController::class)->only(['store', 'update', 'destroy'])
        ->middleware('role:Администратор');
    // Просмотр списка и конкретного пациента доступен всем авторизованным
    Route::get('patients', [PatientController::class, 'index']);
    Route::get('patients/{patient}', [PatientController::class, 'show']);

    // Врачи (управление только админ)
    Route::apiResource('doctors', DoctorController::class)->only(['store', 'update', 'destroy'])
        ->middleware('role:Администратор');

    // Расписание (управление только админ)
    Route::apiResource('schedules', ScheduleController::class)->only(['store', 'update', 'destroy'])
        ->middleware('role:Администратор');
    // Просмотр слотов доступен всем авторизованным
    Route::get('schedules', [ScheduleController::class, 'index']);
    Route::get('schedules/{schedule}', [ScheduleController::class, 'show']);
    Route::get('doctor/schedules', [ScheduleController::class, 'mySchedules']);
    // Роли (только просмотр)
    Route::apiResource('roles', RoleController::class)->only(['index', 'show']);

    // Специализации (управление только админ)
    Route::apiResource('specializations', SpecializationController::class)
        ->only(['store', 'update', 'destroy'])
        ->middleware('role:Администратор');

    // Статусы (управление только админ)
    Route::apiResource('statuses', StatusController::class)
        ->only(['store', 'update', 'destroy'])
        ->middleware('role:Администратор');

    Route::apiResource('notifications', NotificationController::class)
        ->only(['store', 'update', 'destroy'])
        ->middleware('role:Администратор');

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/{notification}', [NotificationController::class, 'show']);

    // Отчёты (только админ)
    Route::middleware('role:Администратор')->prefix('reports')->group(function () {
        Route::get('patients', [ReportController::class, 'patients']);
        Route::get('doctors', [ReportController::class, 'doctors']);
        Route::get('canceled', [ReportController::class, 'canceled']);
        Route::get('satisfaction', [ReportController::class, 'satisfaction']);
    });
});
