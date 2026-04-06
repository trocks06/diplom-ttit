<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\EmailVerificationController;
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
        Route::post('/avatar', [UserController::class, 'updateAvatar']);
    });

    // Аккаунт и безопасность
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Верификация Email
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('throttle:6,1');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');

    // Профиль текущего пользователя (удобно для фронтенда)
    Route::get('me', [UserController::class, 'me']);

    // Уведомления (мы уже обсудили логику внутри контроллера/политики)
    Route::apiResource('notifications', NotificationController::class);

    // Записи на прием
    Route::apiResource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);

    // Отзывы
    // Создание через вложенный роут (как ты хотел)
    Route::post('appointments/{appointment}/review', [ReviewController::class, 'store']);
    // Остальные действия через стандартный ресурс
    Route::apiResource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);

    // Пользователи, Пациенты, Врачи
    Route::apiResource('users', UserController::class);
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('doctors', DoctorController::class);

    // Расписание и роли
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('roles', RoleController::class)->only(['index', 'show']);

    // Дополнительные методы для специальностей (создание/удаление только для админа через политики)
    Route::apiResource('specializations', SpecializationController::class)->except(['index', 'show']);
});


