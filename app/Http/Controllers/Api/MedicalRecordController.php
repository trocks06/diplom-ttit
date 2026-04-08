<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Services\MedicalRecordService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MedicalRecordController extends Controller
{
    use AuthorizesRequests;

    protected MedicalRecordService $service;

    public function __construct(MedicalRecordService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = auth()->user();
        if ($user->role->role_name === 'Пациент') {
            $records = $this->service->getForPatient($user->patient->id);
        } elseif ($user->role->role_name === 'Врач') {
            $records = $this->service->getForDoctor($user->doctor->id);
        } else {
            $records = $this->service->getAll();
        }
        return MedicalRecordResource::collection($records);
    }

    public function store(StoreMedicalRecordRequest $request, Appointment $appointment)
    {
        $this->authorize('create', [MedicalRecord::class, $appointment]);
        $data = $request->validated();
        $data['appointment_id'] = $appointment->id;
        $record = $this->service->create($data);
        return new MedicalRecordResource($record->load('medical_files'));
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);
        return new MedicalRecordResource($medicalRecord->load(['appointment.schedule.doctor', 'medical_files']));
    }

    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);
        $medicalRecord->update($request->validated());
        return new MedicalRecordResource($medicalRecord);
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $this->authorize('delete', $medicalRecord);
        $medicalRecord->delete();
        return response()->json(['message' => 'Запись медкарты удалена']);
    }
}
