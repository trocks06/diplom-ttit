<?php

namespace App\Services;

use App\Filters\FuzzySearch;
use App\Models\Appointment;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AppointmentService extends BaseService
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

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

    public function getFilteredBuilder()
    {
        return QueryBuilder::for(Appointment::class)
            ->allowedIncludes(['status', 'patient.user', 'schedule.doctor.user'])
            ->allowedFilters([
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('patient_id'),
                AllowedFilter::callback('doctor_id', function ($query, $value) {
                    $query->whereHas('schedule', function ($q) use ($value) {
                        $q->where('doctor_id', $value);
                    });
                }),
                AllowedFilter::custom('search', new FuzzySearch()),
            ])
            ->allowedSorts(['created_at', 'id'])
            ->defaultSort('-created_at');
    }
}
