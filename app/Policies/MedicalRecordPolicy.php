<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\Appointment;

class MedicalRecordPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role?->role_name === 'Администратор') {
            return true;
        }
        return null;
    }

    public function create(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->schedule?->doctor?->user_id;
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->appointment?->patient?->user_id ||
            $user->id === $medicalRecord->appointment?->schedule?->doctor?->user_id;
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->appointment?->schedule?->doctor?->user_id;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->role?->role_name === 'Администратор' ||
            $user->id === $medicalRecord->appointment?->schedule?->doctor?->user_id;
    }
}
