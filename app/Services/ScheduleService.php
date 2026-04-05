<?php

namespace App\Services;

use App\Models\Schedule;
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
        $this->checkOverlapping($data['doctor_id'], $data['start_time'], $data['end_time']);
        return parent::update($id, $data);
    }

    protected function checkOverlapping($doctorId, $start, $end, $excludeId = null)
    {
        $overlap = $this->model->where('doctor_id', $doctorId)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                    });
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
