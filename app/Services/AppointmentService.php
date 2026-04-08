<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class AppointmentService extends BaseService
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание записи с учётом роли пользователя.
     */
    public function createFromRequest(array $data, User $user): Model
    {
        if ($user->role->role_name === 'Пациент') {
            if (!$user->patient) {
                throw ValidationException::withMessages([
                    'user' => ['Профиль пациента не найден.'],
                ]);
            }
            $data['patient_id'] = $user->patient->id;
        }

        return $this->create($data);
    }

    public function create(array $data): Model
    {
        $isBooked = $this->model->where('schedule_id', $data['schedule_id'])
            ->whereHas('status', function($q) {
                $q->whereNotIn('status_name', ['Отменен', 'Отменён']);
            })->exists();

        if ($isBooked) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Этот слот в расписании уже забронирован.',
            ]);
        }
        $defaultStatus = Status::where('status_name', 'Запланирован')->first();
        $data['status_id'] = $defaultStatus ? $defaultStatus->id : null;

        return parent::create($data);
    }

    public function getForPatient(int $patientId): Collection
    {
        return $this->model->where('patient_id', $patientId)
            ->with(['schedule.doctor.user', 'status'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getForDoctor(int $doctorId): Collection
    {
        return $this->model->whereHas('schedule', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->with(['schedule.doctor.user', 'patient.user', 'status'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
