<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpecializationRequest;
use App\Http\Requests\UpdateSpecializationRequest;
use App\Http\Resources\SpecializationResource;
use App\Models\Specialization;
use App\Services\SpecializationService;

class SpecializationController extends Controller
{
    protected SpecializationService $service;

    public function __construct(SpecializationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $specializations = $this->service->getAll();
        return SpecializationResource::collection($specializations);
    }

    public function store(StoreSpecializationRequest $request)
    {
        $specialization = $this->service->create($request->validated());
        return new SpecializationResource($specialization);
    }

    public function show(Specialization $specialization)
    {
        return new SpecializationResource($specialization);
    }

    public function update(UpdateSpecializationRequest $request, Specialization $specialization)
    {
        $updatedSpecialization = $this->service->update($specialization->id, $request->validated());
        return new SpecializationResource($updatedSpecialization);
    }

    public function destroy(Specialization $specialization)
    {
        $this->service->delete($specialization->id);
        return response()->json([
            "message" => "Специальность успешно удалена."
        ]);
    }
}
