<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegisterUserRequest $request)
    {
        $user = $this->authService->register($request->validated());
        $token = $user->createToken("auth_token")->plainTextToken;
        return (new UserResource($user))->additional(['token' => $token]);
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());
            return (new UserResource($result['user']))->additional(['token' => $result['token']]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout()
    {
        $this->authService->logout(auth()->user());
        return response()->json(['message' => 'Вы вышли из системы.']);
    }
}
