<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
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

        return User::create($data);
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

    private function uploadAvatar(UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }
}
