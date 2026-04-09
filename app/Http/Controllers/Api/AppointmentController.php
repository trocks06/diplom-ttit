<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use AuthorizesRequests;

    protected AppointmentService $service;

    public function __construct(AppointmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = auth()->user();
        $roleName = $user->role->role_name;
        $query = $this->service->getFilteredBuilder();
        if ($roleName === 'Врач') {
            $query->whereHas('schedule', fn($q) => $q->where('doctor_id', $user->doctor->id));
        } elseif ($roleName === 'Пациент') {
            $query->where('patient_id', $user->patient->id);
        }
        return AppointmentResource::collection($query->get());
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = $this->service->createFromRequest(
            $request->validated(),
            auth()->user()
        );
        return new AppointmentResource($appointment->load(['schedule.doctor.user', 'status']));
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        return new AppointmentResource($appointment->load(['schedule', 'patient.user', 'status', 'medical_record']));
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        $this->authorize('updateStatus', $appointment);
        $updated = $this->service->update($appointment->id, [
            'status_id' => $request->status_id
        ]);
        return new AppointmentResource($updated->load(['status', 'schedule.doctor.user']));
    }
}
