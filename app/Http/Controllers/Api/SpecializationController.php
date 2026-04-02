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
    protected SpecializationService $specializationService;

    public function __construct(SpecializationService $specializationService)
    {
        $this->specializationService = $specializationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specializations = $this->specializationService->getAll();
        return SpecializationResource::collection($specializations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecializationRequest $request)
    {
        $specialization = $this->specializationService->create($request->validated());
        return new SpecializationResource($specialization);
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization $specialization)
    {
        return new SpecializationResource($specialization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecializationRequest $request, Specialization $specialization)
    {
        $updatedSpecialization = $this->specializationService->update($specialization->id, $request->validated());
        return new SpecializationResource($updatedSpecialization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization)
    {
        $this->specializationService->delete($specialization->id);
        return response()->json([
            "message" => "Специальность успешно удалена."
        ]);
    }
}
