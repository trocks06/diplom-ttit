<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use App\Services\StatusService;

class StatusController extends Controller
{
    protected StatusService $service;

    public function __construct(StatusService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $statuses = $this->service->getAll();
        return StatusResource::collection($statuses);
    }

    public function show(Status $status)
    {
        return new StatusResource($status);
    }
}
