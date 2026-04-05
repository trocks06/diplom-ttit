<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected AvatarService $avatarService;

    public function __construct(AvatarService $avatarService)
    {
        $this->avatarService = $avatarService;
    }

    public function register(array $data): User
    {
        $defaultRole = Role::where('role_name', 'Пациент')->firstOrFail();
        $data['role_id'] = $defaultRole->id;
        $data['verified'] = false;

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $data['avatar'] = $this->avatarService->upload($data['avatar']);
        }

        return DB::transaction(function () use ($data) {
            $user = User::create($data);
            $user->patient()->create([
                'user_id' => $user->id,
                 'address' => $data['address'] ?? null,
                 'birth_date' => $data['birth_date'] ?? null,
                 'gender' => $data['gender'] ?? null,
                 'allergies' => $data['allergies'] ?? null,
                 'chronic_diseases' => $data['chronic_diseases'] ?? null,
            ]);

            return $user;
        });
    }

    public function login(array $data): array
    {
        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => ['Неправильные почта или пароль.'],
            ]);
        }

        $user = Auth::user();
        $user->tokens()->delete();

        $token = $user->createToken("auth_token")->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Текущий пароль указан неверно.'],
            ]);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
        $user->tokens()->delete();
    }
}
