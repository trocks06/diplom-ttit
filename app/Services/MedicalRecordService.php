<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class MedicalRecordService extends BaseService
{
    public function __construct(MedicalRecord $model)
    {
        parent::__construct($model);
    }

    public function getForPatient(int $patientId)
    {
        return $this->model->whereHas('appointment', function($q) use ($patientId) {
            $q->where('patient_id', $patientId);
        })->with(['appointment.schedule.doctor.user', 'medical_files'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getForCurrentUser(User $user): Collection
    {
        if ($user->role->role_name !== 'Пациент') {
            throw ValidationException::withMessages([
                'user' => ['Доступ только для пациентов.'],
            ]);
        }

        return $this->getForPatient($user->patient->id);
    }
}
