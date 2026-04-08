<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Status;
use App\Services\AppointmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

        $appointments = match ($roleName) {
            'Администратор' => $this->service->getAll([
                'schedule.doctor.user',
                'status',
                'patient.user'
            ]),
            'Врач' => $this->service->getForDoctor($user->doctor->id),
            'Пациент' => $this->service->getForPatient($user->patient->id)
        };

        return AppointmentResource::collection($appointments);
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
