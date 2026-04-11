<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ScheduleService extends BaseService
{
    public function __construct(Schedule $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Model
    {
        $this->checkOverlapping($data['doctor_id'], $data['start_time'], $data['end_time']);
        return parent::create($data);
    }

    public function update($id, array $data): Model
    {
        $slot = $this->find($id);
        $hasActiveAppointments = $slot->appointments()
            ->whereHas('status', function ($q) {
                $q->whereNotIn('status_name', ['Отменен', 'Отменён']);
            })->exists();
        if ($hasActiveAppointments && (isset($data['start_time']) || isset($data['end_time']))) {
            throw ValidationException::withMessages([
                'start_time' => 'Нельзя менять время у слота, на который уже записан пациент.',
            ]);
        }
        $doctorId = $data['doctor_id'] ?? $slot->doctor_id;
        $startTime = $data['start_time'] ?? $slot->start_time->format('d.m.Y H:i');
        $endTime = $data['end_time'] ?? $slot->end_time->format('d.m.Y H:i');
        $this->checkOverlapping($doctorId, $startTime, $endTime, $id);
        return parent::update($id, $data);
    }

    public function deleteSlot(Schedule $schedule): void
    {
        $hasActive = $schedule->appointments()
            ->whereHas('status', fn($q) => $q->whereNotIn('status_name', ['Отменен', 'Отменён']))
            ->exists();
        if ($hasActive) {
            throw ValidationException::withMessages([
                'schedule' => ['Нельзя удалить забронированный слот.'],
            ]);
        }
        $this->delete($schedule->id);
    }

    protected function checkOverlapping($doctorId, $start, $end, $excludeId = null)
    {
        $startTime = Carbon::createFromFormat('d.m.Y H:i', $start);
        $endTime = Carbon::createFromFormat('d.m.Y H:i', $end);
        $overlap = $this->model->where('doctor_id', $doctorId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
        if ($overlap) {
            throw ValidationException::withMessages([
                'start_time' => 'Этот временной интервал пересекается с уже существующим расписанием врача.',
            ]);
        }
    }

    public function getFilteredBuilder()
    {
        return QueryBuilder::for(Schedule::class)
            ->allowedIncludes(['doctor.user', 'appointments'])
            ->allowedFilters([
                AllowedFilter::exact('doctor_id'),
                AllowedFilter::callback('starts_after', function ($query, $value) {
                    $query->where('start_time', '>=', Carbon::parse($value));
                }),
                AllowedFilter::callback('ends_before', function ($query, $value) {
                    $query->where('end_time', '<=', Carbon::parse($value));
                }),
                AllowedFilter::callback('date', function ($query, $value) {
                    $query->whereDate('start_time', Carbon::parse($value));
                }),
                AllowedFilter::callback('is_free', function ($query, $value) {
                    if ($value === 'true' || $value === '1') {
                        $query->whereDoesntHave('appointments', function ($q) {
                            $q->where('status_id', '!=', 3);
                        });
                    }
                }),
            ])
            ->allowedSorts(['start_time', 'end_time'])
            ->defaultSort('start_time');
    }

    public function getQueryForUser(?User $user): QueryBuilder
    {
        $query = $this->getFilteredBuilder();
        $query->with(['doctor.user']);

        if ($user && $user->role->role_name === 'Врач') {
            $query->where('doctor_id', $user->doctor->id);
        } elseif (!$user || $user->role->role_name === 'Пациент') {
            $query->where('start_time', '>=', now());
        }

        return $query;
    }
}
