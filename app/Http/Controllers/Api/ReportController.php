<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function patients(): JsonResponse
    {
        return response()->json($this->service->patientsReport());
    }

    public function doctors(): JsonResponse
    {
        return response()->json($this->service->doctorsReport());
    }

    public function canceled(): JsonResponse
    {
        return response()->json($this->service->canceledAppointmentsReport());
    }

    public function satisfaction(): JsonResponse
    {
        return response()->json($this->service->satisfactionReport());
    }
}
