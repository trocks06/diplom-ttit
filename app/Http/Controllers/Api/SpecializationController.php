<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpecializationRequest;
use App\Http\Requests\UpdateSpecializationRequest;
use App\Http\Resources\SpecializationResource;
use App\Models\Specialization;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SpecializationResource::collection(Specialization::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecializationRequest $request)
    {
        return new SpecializationResource(Specialization::create($request->validated()));
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
        return new SpecializationResource($specialization->update($request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization)
    {
        $specialization->delete();
        return response()->json([
            "message" => "Специальность успешно удалена"
        ]);
    }
}
