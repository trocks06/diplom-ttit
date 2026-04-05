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
        $doctors = $this->service->getAll();
        return AppointmentResource::collection($doctors);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();

        // Если это пациент, принудительно ставим его ID
        if (auth()->user()?->role->role_name === 'Пациент') {
            $data['patient_id'] = auth()->user()->patient->id;
        }

        $appointment = $this->service->create($data);
        return new AppointmentResource($appointment->load(['schedule.doctor.user', 'status']));
    }

    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment->load(['schedule', 'patient.user', 'status', 'medical_record']));
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {

        $updated = $this->service->update($appointment->id, [
            'status_id' => $request->status_id
        ]);

        return new AppointmentResource($updated->load(['status', 'schedule.doctor.user']));
    }
}
