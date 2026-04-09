<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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

    public function getFilteredBuilder()
    {
        return QueryBuilder::for(Review::class)
            // Разрешаем подгрузку данных о приеме, враче и пациенте
            ->allowedIncludes([
                'appointment.patient.user',
                'appointment.schedule.doctor.user'
            ])

            ->allowedFilters([
                // 1. Фильтр по точной оценке (например, только "5")
                AllowedFilter::exact('rating'),

                // 2. Поиск по тексту отзыва
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where('comment', 'like', '%' . $value . '%');
                }),

                // 3. Фильтр по врачу (через сложную связь)
                AllowedFilter::callback('doctor_id', function ($query, $value) {
                    $query->whereHas('appointment.schedule', function ($q) use ($value) {
                        $q->where('doctor_id', $value);
                    });
                }),

                // 4. Фильтр по пациенту
                AllowedFilter::callback('patient_id', function ($query, $value) {
                    $query->whereHas('appointment', function ($q) use ($value) {
                        $q->where('patient_id', $value);
                    });
                }),

                // 5. Фильтр по дате (отзывы за период)
                AllowedFilter::callback('created_after', function ($query, $value) {
                    $query->where('created_at', '>=', Carbon::parse($value));
                }),
            ])

            // Сортировка: по дате или по оценке
            ->allowedSorts(['rating', 'created_at'])
            ->defaultSort('-created_at');
    }
}
