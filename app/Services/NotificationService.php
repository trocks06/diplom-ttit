<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationService extends BaseService
{
    // Переопределяем конструктор
    public function __construct(Notification $model)
    {
        // Передаем конкретную модель Notification в конструктор BaseService
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
}
