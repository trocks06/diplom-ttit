<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Отчёт по пациентам: количество приёмов, последний визит и т.д.
     */
    public function patientsReport(): array
    {
        $patients = Patient::with(['user', 'appointments'])->get()->map(function ($patient) {
            return [
                'patient_id' => $patient->id,
                'name' => $patient->user->firstname . ' ' . $patient->user->lastname,
                'total_appointments' => $patient->appointments->count(),
                'last_appointment' => $patient->appointments->max('created_at')?->format('d.m.Y'),
                'canceled_appointments' => $patient->appointments()
                    ->whereHas('status', fn($q) => $q->whereIn('status_name', ['Отменен', 'Отменён']))
                    ->count(),
            ];
        });

        return $patients->toArray();
    }

    /**
     * Отчёт по врачам: количество приёмов, средний рейтинг, загруженность.
     */
    public function doctorsReport(): array
    {
        $doctors = Doctor::with(['user', 'specializations', 'schedules.appointments'])
            ->get()
            ->map(function ($doctor) {
                $appointments = $doctor->schedules->flatMap->appointments;
                return [
                    'doctor_id' => $doctor->id,
                    'name' => $doctor->user->firstname . ' ' . $doctor->user->lastname,
                    'specializations' => $doctor->specializations->pluck('specialization_name'),
                    'total_appointments' => $appointments->count(),
                    'completed_appointments' => $appointments->where('status.status_name', 'Завершён')->count(),
                    'average_rating' => round($doctor->averageRating(), 1),
                    'reviews_count' => $doctor->reviewsCount(),
                ];
            });

        return $doctors->toArray();
    }

    /**
     * Отчёт по отменённым записям с группировкой по причинам (если есть поле reason).
     * Пока просто список отменённых.
     */
    public function canceledAppointmentsReport(): array
    {
        $canceled = Appointment::whereHas('status', fn($q) => $q->whereIn('status_name', ['Отменен', 'Отменён']))
            ->with(['patient.user', 'schedule.doctor.user'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn($app) => [
                'appointment_id' => $app->id,
                'patient' => $app->patient->user->firstname . ' ' . $app->patient->user->lastname,
                'doctor' => $app->schedule->doctor->user->firstname . ' ' . $app->schedule->doctor->user->lastname,
                'scheduled_time' => $app->schedule->start_time->format('d.m.Y H:i'),
                'canceled_at' => $app->updated_at->format('d.m.Y H:i'),
            ]);

        return $canceled->toArray();
    }

    /**
     * Отчёт по удовлетворённости: распределение рейтингов по врачам или общий.
     */
    public function satisfactionReport(): array
    {
        $avgRating = Review::avg('rating') ?? 0;
        $ratingDistribution = Review::select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return [
            'average_rating' => round($avgRating, 2),
            'distribution' => $ratingDistribution,
            'total_reviews' => Review::count(),
        ];
    }
}
