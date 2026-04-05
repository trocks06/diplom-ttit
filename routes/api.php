<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SpecializationController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::apiResource('roles', RoleController::class);
Route::apiResource('specializations', SpecializationController::class);
Route::apiResource('statuses', StatusController::class);
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
Route::apiResource('patients', PatientController::class)->middleware('auth:sanctum');
Route::apiResource('doctors', DoctorController::class)->middleware('auth:sanctum');
Route::apiResource('schedules', ScheduleController::class)->middleware('auth:sanctum');
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout']);
Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
