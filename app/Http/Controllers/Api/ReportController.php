<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function patients(): JsonResponse
    {
        return response()->json($this->reportService->patientsReport());
    }

    public function doctors(): JsonResponse
    {
        return response()->json($this->reportService->doctorsReport());
    }

    public function canceled(): JsonResponse
    {
        return response()->json($this->reportService->canceledAppointmentsReport());
    }

    public function satisfaction(): JsonResponse
    {
        return response()->json($this->reportService->satisfactionReport());
    }
}
