<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Appointment;
use Illuminate\Validation\ValidationException;

class ReviewService extends BaseService
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    /**
     * Создать отзыв для приёма с проверкой дубликата.
     */
    public function createForAppointment(Appointment $appointment, array $data): Review
    {
        if ($appointment->reviews()->exists()) {
            throw ValidationException::withMessages([
                'appointment' => ['Отзыв для этого приёма уже существует.'],
            ]);
        }

        $data['appointment_id'] = $appointment->id;

        return $this->create($data);
    }

    /**
     * Получить отзывы для конкретного врача
     */
    public function getForDoctor(int $doctorId)
    {
        return Review::whereHas('appointment.schedule', function($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->with('appointment.patient.user')->latest()->get();
    }
}
