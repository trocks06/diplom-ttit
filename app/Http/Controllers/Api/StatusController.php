<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use App\Services\StatusService;

class StatusController extends Controller
{
    protected StatusService $statusService;

    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = $this->statusService->getAll();
        return StatusResource::collection($statuses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStatusRequest $request)
    {
        $status = $this->statusService->create($request->validated());
        return new StatusResource($status);
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        return new StatusResource($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Status $status)
    {
        $updatedStatus = $this->statusService->update($status->id, $request->validated());
        return new StatusResource($updatedStatus);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        $status->delete();
        return response()->json([
            "message" => "Статус успешно удален."
        ]);
    }
}
