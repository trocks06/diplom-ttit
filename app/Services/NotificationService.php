<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class NotificationService extends BaseService
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function markAsRead(Notification $notification, User $user): void
    {
        if ($notification->user_id === $user->id && !$notification->is_read) {
            $notification->update(['is_read' => true]);
        }
    }

    public function getQueryForUser(int $userId): Builder
    {
        $query = Notification::query();

        if (auth()->user()?->role->role_name === 'Администратор') {
            $query->with('user');
        } else {
            $query->where('user_id', $userId);
        }

        return $query->latest();
    }
}
