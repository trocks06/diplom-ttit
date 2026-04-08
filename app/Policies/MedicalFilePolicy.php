<?php

namespace App\Policies;

use App\Models\MedicalFile;
use App\Models\MedicalRecord;
use App\Models\User;

class MedicalFilePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role?->role_name === 'Администратор') {
            return true;
        }
        return null;
    }

    public function create(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->appointment?->schedule?->doctor?->user_id;
    }

    public function delete(User $user, MedicalFile $medicalFile): bool
    {
        return $user->id === $medicalFile->medical_record?->appointment?->schedule?->doctor?->user_id;
    }
}
