<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\UseResource;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $defaultRole = Role::where('role_name', 'Пациент')->first();
        if (!$defaultRole) {
            return response()->json([
                'message' => 'Роль пользователя не настроена в системе. Обратитесь к администратору.'
            ], 500);
        }
        $data = $request->validated();
        $data['role_id'] = $defaultRole->id;
        $data['verified'] = false;
        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;
        return (new UserResource($user))->additional(['token' => $token]);
    }

    public function login(LoginUserRequest $request)
    {
        if (!auth()->attempt($request->validated())) {
            return response()->json(['message' => 'Неправильные почта или пароль.'], 401);
        }
        $user = auth()->user();
        $user->tokens()->delete();
        $token = $user->createToken("Token of user: $user->lastname $user->firstname $user->patronymic" )->plainTextToken;
        return (new UserResource($user))->additional(['token' => $token]);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Вы вышли из системы.']);
    }
}
