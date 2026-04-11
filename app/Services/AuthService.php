<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected PatientService $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function register(array $data): User
    {
        $patient = $this->patientService->create($data);
        $user = $patient->user;
        $user->sendEmailVerificationNotification();
        return $user;
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
        $user->load('role');
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
