<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReviewService extends BaseService
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

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

    public function getForDoctor(int $doctorId)
    {
        return Review::whereHas('appointment.schedule', function($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->with('appointment.patient.user')->latest()->get();
    }

    public function getFilteredBuilder()
    {
        return QueryBuilder::for(Review::class)
            ->allowedIncludes([
                'appointment.patient.user',
                'appointment.schedule.doctor.user'
            ])
            ->allowedFilters([
                AllowedFilter::exact('rating'),
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where('comment', 'like', '%' . $value . '%');
                }),
                AllowedFilter::callback('doctor_id', function ($query, $value) {
                    $query->whereHas('appointment.schedule', function ($q) use ($value) {
                        $q->where('doctor_id', $value);
                    });
                }),
                AllowedFilter::callback('patient_id', function ($query, $value) {
                    $query->whereHas('appointment', function ($q) use ($value) {
                        $q->where('patient_id', $value);
                    });
                }),
                AllowedFilter::callback('created_after', function ($query, $value) {
                    $query->where('created_at', '>=', Carbon::parse($value));
                }),
            ])
            ->allowedSorts(['rating', 'created_at'])
            ->defaultSort('-created_at');
    }

    public function getFilteredForIndex(): LengthAwarePaginator
    {
        $query = $this->getFilteredBuilder();
        $query->with(['appointment.schedule.doctor.user', 'appointment.patient.user']);
        return $query->paginate(15);
    }
}
