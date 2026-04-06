<?php

namespace App\Services;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

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
        if ($slot->is_booked && (isset($data['start_time']) || isset($data['end_time']))) {
            throw ValidationException::withMessages([
                'start_time' => 'Нельзя менять время у слота, на который уже записан пациент.',
            ]);
        }
        $this->checkOverlapping($data['doctor_id'], $data['start_time'], $data['end_time'], $id);
        return parent::update($id, $data);
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
}
