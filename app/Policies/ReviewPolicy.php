<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Appointment;

class ReviewPolicy
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
        // Только пациент, чей это приём, и только если приём завершён
        return $user->id === $appointment->patient?->user_id
            && $appointment->status?->status_name === 'Завершён' || 'Завершен';
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->appointment?->patient?->user_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->appointment?->patient?->user_id
            || $user->role?->role_name === 'Администратор';
    }
}
