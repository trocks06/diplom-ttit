<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function index()
    {
        $statuses = $this->statusService->getAll();
        return StatusResource::collection($statuses);
    }

    public function show(Status $status)
    {
        return new StatusResource($status);
    }
}
