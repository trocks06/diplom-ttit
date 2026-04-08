<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role->role_name === 'Администратор') {
            return true;
        }
        return null;
    }

    public function create(User $user): bool
    {
        return $user->role?->role_name === 'Администратор';
    }

    public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }
}
