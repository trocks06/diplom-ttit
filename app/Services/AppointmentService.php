<?php

namespace App\Services;

use App\Filters\FuzzySearch;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
                throw ValidationException::withMessages([ 'user' => ['Профиль пациента не найден.'], ]);
            }
            $data['patient_id'] = $user->patient->id;
        }
        $schedule = Schedule::findOrFail($data['schedule_id']);
        if (Appointment::where('schedule_id', $schedule->id)->exists()) {
            throw ValidationException::withMessages([
                'schedule_id' => ['Этот слот уже занят.'],
            ]);
        }
        $startTime = $schedule->start_time;
        $patientId = $data['patient_id'];
        $alreadyBooked = Appointment::where('patient_id', $patientId)
            ->whereHas('schedule', function ($query) use ($startTime) {
                $query->where('start_time', $startTime);
            })
            ->whereHas('status', function ($query) {
                $query->where('status_name', '!=', 'Отменено');
            })
            ->exists();
        if ($alreadyBooked) {
            throw ValidationException::withMessages([
                'schedule_id' => ['У вас уже есть запись на это время к другому специалисту.'],
            ]);
        }
        $status = Status::where('status_name', 'Запланировано')->first();
        $data['status_id'] = $status->id;
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

    public function getForUser(User $user): Collection
    {
        $query = $this->getFilteredBuilder();

        $roleName = $user->role->role_name;
        if ($roleName === 'Врач') {
            $query->whereHas('schedule', fn($q) => $q->where('doctor_id', $user->doctor->id));
        } elseif ($roleName === 'Пациент') {
            $query->where('patient_id', $user->patient->id);
        }

        return $query->get();
    }
}
