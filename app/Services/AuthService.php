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
    protected PatientService $patientService;

    public function __construct(PatientService $patientService)
    {
        // Теперь AuthService знает про PatientService
        $this->patientService = $patientService;
    }

    public function register(array $data): User
    {
        $defaultRole = Role::where('role_name', 'Пациент')->firstOrFail();
        $data['role_id'] = $defaultRole->id;
        $patient = $this->patientService->create($data);
        return $patient->user;
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
