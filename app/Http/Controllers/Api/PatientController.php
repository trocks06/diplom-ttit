<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;

class PatientController extends Controller
{
    protected PatientService $service;

    public function __construct(PatientService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $patients = $this->service->getAll(['user']);
        return PatientResource::collection($patients);
    }

    public function store(StorePatientRequest $request)
    {
        $patient = $this->service->create($request->validated());
        return new PatientResource($patient);
    }

    public function show(Patient $patient)
    {
        return new PatientResource($patient->load(['user']));
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $updatedPatient = $this->service->update($patient->id, $request->validated());
        return new PatientResource($updatedPatient);
    }

    public function destroy(Patient $patient)
    {
        $this->service->delete($patient->id);
        return response()->json([
            "message" => "Профиль пациента успешно удален."
        ]);
    }
}
