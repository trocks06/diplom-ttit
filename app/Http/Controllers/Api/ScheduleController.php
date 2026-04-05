<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Models\Doctor;
use App\Services\ScheduleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ScheduleController extends Controller
{
    use AuthorizesRequests;

    protected ScheduleService $service;

    public function __construct(ScheduleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $roles = $this->service->getAll();
        return ScheduleResource::collection($roles);
    }

    public function show(Schedule $schedule)
    {
        return new ScheduleResource($schedule);
    }

    public function store(StoreScheduleRequest $request)
    {
        $schedule = $this->service->create($request->validated());
        return new ScheduleResource($schedule);
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->appointments()->exists()) {
            return response()->json(['error' => 'Нельзя удалить расписание, на которое записаны пациенты.'], 422);
        }
        $this->service->delete($schedule->id);
        return response()->json(['message' => 'Слот расписания удален.']);
    }
}
