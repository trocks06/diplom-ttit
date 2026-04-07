<?php

// app/Http/Controllers/Api/MedicalRecordController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Services\MedicalRecordService;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    protected MedicalRecordService $service;

    public function __construct(MedicalRecordService $service)
    {
        $this->service = $service;
    }

    // Создание записи в медкарте для конкретного приёма (врач)
    public function store(StoreMedicalRecordRequest $request, Appointment $appointment)
    {
        // Проверка прав: только врач, который вёл приём, или админ
        $this->authorize('create', [MedicalRecord::class, $appointment]);

        $data = $request->validated();
        $data['appointment_id'] = $appointment->id;
        $record = $this->service->create($data);

        return new MedicalRecordResource($record->load('medical_files'));
    }

    // Просмотр медкарты пациента (пациент или врач)
    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);
        return new MedicalRecordResource($medicalRecord->load(['appointment.schedule.doctor', 'medical_files']));
    }

    // Обновление (врач или админ)
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);
        $medicalRecord->update($request->only(['diagnosis', 'treatment', 'notes']));
        return new MedicalRecordResource($medicalRecord);
    }

    // Удаление (админ)
    public function destroy(MedicalRecord $medicalRecord)
    {
        $this->authorize('delete', $medicalRecord);
        $medicalRecord->delete();
        return response()->json(['message' => 'Запись медкарты удалена']);
    }

    public function myRecords()
    {
        $user = auth()->user();
        if ($user->role->role_name !== 'Пациент') {
            return response()->json(['message' => 'Доступ только для пациентов'], 403);
        }

        $records = $this->service->getForPatient($user->patient->id);
        return MedicalRecordResource::collection($records);
    }
}
