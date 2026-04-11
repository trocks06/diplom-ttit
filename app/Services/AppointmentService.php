<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
        if (empty($data['patient_id'])) {
            if ($user->role->role_name !== 'Пациент') {
                throw ValidationException::withMessages([
                    'patient_id' => ['Поле patient_id обязательно для администратора.']
                ]);
            }
            if (!$user->patient) {
                throw ValidationException::withMessages([
                    'user' => ['Профиль пациента не найден.']
                ]);
            }
            $data['patient_id'] = $user->patient->id;
        }
        if ($user->role->role_name === 'Пациент') {
            $schedule = Schedule::findOrFail($data['schedule_id']);
            $startTime = $schedule->start_time;
            $alreadyBooked = Appointment::where('patient_id', $data['patient_id'])
                ->whereHas('schedule', fn($q) => $q->where('start_time', $startTime))
                ->whereHas('status', fn($q) => $q->where('status_name', '!=', 'Отменено'))
                ->exists();
            if ($alreadyBooked) {
                throw ValidationException::withMessages([
                    'schedule_id' => ['У вас уже есть запись на это время к другому специалисту.']
                ]);
            }
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
                AllowedFilter::callback('search', function (Builder $query, $value) {
                    $searchTerm = '%' . $value . '%';
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereHas('patient.user', function ($uq) use ($searchTerm) {
                            $uq->where('lastname', 'like', $searchTerm)
                                ->orWhere('firstname', 'like', $searchTerm)
                                ->orWhere('patronymic', 'like', $searchTerm);
                        })
                            ->orWhereHas('schedule.doctor.user', function ($uq) use ($searchTerm) {
                                $uq->where('lastname', 'like', $searchTerm)
                                    ->orWhere('firstname', 'like', $searchTerm)
                                    ->orWhere('patronymic', 'like', $searchTerm);
                            });
                    });
                }),
            ])
            ->allowedSorts(['created_at', 'id'])
            ->defaultSort('-created_at');
    }

    public function getQueryForUser(User $user): QueryBuilder
    {
        $query = $this->getFilteredBuilder();

        $roleName = $user->role->role_name;
        if ($roleName === 'Врач') {
            $query->whereHas('schedule', fn($q) => $q->where('doctor_id', $user->doctor->id));
        } elseif ($roleName === 'Пациент') {
            $query->where('patient_id', $user->patient->id);
        }

        return $query;
    }
}
