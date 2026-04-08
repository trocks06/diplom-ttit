<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;

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
        $schedules = $this->service->getAll(['doctor.user', 'appointments.status']);
        return ScheduleResource::collection($schedules);
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

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $updatedSchedule = $this->service->update($schedule->id, $request->validated());
        return new ScheduleResource($updatedSchedule);
    }

    public function destroy(Schedule $schedule)
    {
        try {
            $this->service->deleteSlot($schedule);
            return response()->json(['message' => 'Слот успешно удален.']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
