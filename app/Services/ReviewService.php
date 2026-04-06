<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Appointment;

class ReviewService extends BaseService
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    /**
     * Получить отзывы для конкретного врача
     */
    public function getForDoctor(int $doctorId)
    {
        return Review::whereHas('appointment', function($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->with('appointment.user')->latest()->get();
    }
}
