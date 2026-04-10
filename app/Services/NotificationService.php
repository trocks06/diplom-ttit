<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService extends BaseService
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }
    public function getForUser(int $userId): Collection
    {
        $query = Notification::query();
        if (auth()->user()?->role->role_name === 'Администратор') {
            return $query->with('user')->latest()->get();
        }

        return $query->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function markAsRead(Notification $notification, User $user): void
    {
        if ($notification->user_id === $user->id && !$notification->is_read) {
            $notification->update(['is_read' => true]);
        }
    }
}
