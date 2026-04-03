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
    protected DoctorService $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index()
    {
        $doctors = $this->doctorService->getAll();
        return DoctorResource::collection($doctors);
    }

    public function store(StoreDoctorRequest $request)
    {
        $doctor = $this->doctorService->create($request->validated());
        return new DoctorResource($doctor);
    }

    public function show(Doctor $doctor)
    {
        return new DoctorResource($doctor->load(['user', 'specializations']));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $updated = $this->doctorService->update($doctor->id, $request->validated());
        return new DoctorResource($updated);
    }

    public function destroy(Doctor $doctor)
    {
        $this->doctorService->delete($doctor->id);
        return response()->json(['message' => 'Доктор успешно удалён.']);
    }
}
