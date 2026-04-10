<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Общая логика: Админ может всё.
     * Если метод before возвращает true, остальные проверки не выполняются.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role?->role_name === 'Администратор') {
            return true;
        }
        return null;
    }

    /**
     * Можно ли просматривать запись?
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient?->user_id ||
            $user->id === $appointment->schedule?->doctor?->user_id;
    }

    public function updateStatus(User $user, Appointment $appointment): bool
    {
        return $user->role?->role_name === 'Администратор';
    }
}
