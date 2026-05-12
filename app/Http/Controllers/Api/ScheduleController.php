<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateScheduleRequest;
use App\Http\Requests\GenerateSheduleRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    use AuthorizesRequests;

    protected ScheduleService $service;

    public function __construct(ScheduleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $schedules = $this->service->getQueryForUser(auth()->user())
            ->paginate($perPage);

        return ScheduleResource::collection($schedules);
    }

    public function show(Schedule $schedule)
    {
        return new ScheduleResource($schedule->load(['doctor.user']));
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

    public function generate(GenerateScheduleRequest $request)
    {
        $validated = $request->validated();
        $params = [
            'doctor_id' => $validated['doctor_id'],
            'start_date' => $validated['start_date'],
            '--days' => $validated['days'],
            '--start-time' => $validated['start_time'],
            '--end-time' => $validated['end_time'],
            '--slot-duration' => $validated['slot_duration'],
            '--weeks' => $validated['weeks'] ?? 1,
        ];
        if (!empty($validated['breaks'])) {
            $params['--breaks'] = $validated['breaks'];
        }
        Artisan::call('schedule:generate', $params);
        return response()->json(['message' => 'Расписание сгенерировано']);
    }
}
