<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function patientsReport(): array
    {
        $patients = Patient::with(['user'])
            ->withCount('appointments as total_appointments')
            ->withCount(['appointments as canceled_appointments' => function ($query) {
                $query->whereHas('status', fn($q) => $q->whereIn('status_name', ['Отменен', 'Отменён']));
            }])
            ->withMax('appointments as last_appointment_date', 'created_at')
            ->get()
            ->map(function ($patient) {
                return [
                    'patient_id' => $patient->id,
                    'name' => $patient->user->firstname . ' ' . $patient->user->lastname,
                    'total_appointments' => $patient->total_appointments,
                    'last_appointment' => $patient->last_appointment_date
                        ? Carbon::parse($patient->last_appointment_date)->format('d.m.Y')
                        : null,
                    'canceled_appointments' => $patient->canceled_appointments,
                ];
            });
        return $patients->toArray();
    }

    public function doctorsReport(): array
    {
        $doctors = Doctor::with([
            'user',
            'specializations',
            'schedules.appointments.status',
            'schedules.appointments.reviews'
        ])->get()
        ->map(function ($doctor) {
            $appointments = $doctor->schedules->flatMap->appointments;
            $reviews = $appointments->flatMap->reviews;
            return [
                'doctor_id' => $doctor->id,
                'name' => $doctor->user->firstname . ' ' . $doctor->user->lastname,
                'specializations' => $doctor->specializations->pluck('specialization_name'),
                'total_appointments' => $appointments->count(),
                'completed_appointments' => $appointments->where('status.status_name', 'Завершён')->count(),
                'average_rating' => round($reviews->avg('rating') ?? 0, 1),
                'reviews_count' => $reviews->count(),
            ];
        });

        return $doctors->toArray();
    }

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
