<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Services\DoctorService;

class DoctorController extends Controller
{
    protected DoctorService $service;

    public function __construct(DoctorService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $doctors = $this->service->getAll(['user']);
        return DoctorResource::collection($doctors);
    }

    public function store(StoreDoctorRequest $request)
    {
        $doctor = $this->service->create($request->validated());
        return new DoctorResource($doctor);
    }

    public function show(Doctor $doctor)
    {
        return new DoctorResource($doctor->load(['user', 'specializations']));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $updated = $this->service->update($doctor->id, $request->validated());
        return new DoctorResource($updated);
    }

    public function destroy(Doctor $doctor)
    {
        $this->service->delete($doctor->id);
        return response()->json(['message' => 'Доктор успешно удалён.']);
    }
}
