<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class AppointmentService extends BaseService
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Model
    {
        // 1. Проверяем, не занято ли уже это время
        $isBooked = $this->model->where('schedule_id', $data['schedule_id'])
            ->whereHas('status', function($q) {
                $q->where('status_name', '!=', 'Отменен'); // Игнорируем отмененные
            })->exists();

        if ($isBooked) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Этот слот в расписании уже забронирован.',
            ]);
        }

        // 2. Устанавливаем статус по умолчанию (например, 'Scheduled')
        // Предполагаем, что у тебя в таблице statuses есть такая запись
        $defaultStatus = Status::where('status_name', 'Запланирован')->first();
        $data['status_id'] = $defaultStatus ? $defaultStatus->id : null;

        return parent::create($data);
    }
}
