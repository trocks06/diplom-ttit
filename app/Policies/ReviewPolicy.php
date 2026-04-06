<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Appointment;

class ReviewPolicy
{
    /**
     * Может ли пользователь оставить отзыв?
     */
    public function create(User $user, Appointment $appointment): bool
    {
        // Только пациент, чей это прием, и только если прием завершен
        return $user->id === $appointment->user_id && $appointment->status === 'completed';
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->appointment->user_id;
    }

    /**
     * Удаление: Обычно только автор или Админ
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->appointment->user_id || $user->role->role_name === 'Администратор';
    }
}
