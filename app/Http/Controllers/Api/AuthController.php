<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }
    public function register(RegisterUserRequest $request)
    {
        $user = $this->service->register($request->validated());
        $token = $user->createToken("auth_token")->plainTextToken;
        $user->load(['patient', 'role']);
        return (new UserResource($user))->additional(['token' => $token])
            ->response()
            ->setStatusCode(201);
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $result = $this->service->login($request->validated());
            $result['user']->load('role', 'patient', 'doctor');
            return (new UserResource($result['user']))->additional(['token' => $result['token']]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout()
    {
        $this->service->logout(auth()->user());
        return response()->json(['message' => 'Вы вышли из системы.']);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $this->service->changePassword(
                $request->user(),
                $request->current_password,
                $request->new_password
            );
            return response()->json(['message' => 'Пароль успешно изменён.']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        }
    }
}
