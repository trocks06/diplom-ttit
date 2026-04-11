<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

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

    public function getForDoctor(int $doctorId): Collection
    {
        return $this->model->whereHas('appointment.schedule', function($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->with(['appointment.patient.user', 'medical_files'])->latest()->get();
    }

    public function getForUser(User $user): Collection
    {
        $role = $user->role->role_name;
        if ($role === 'Пациент') {
            return $this->getForPatient($user->patient->id);
        } elseif ($role === 'Врач') {
            return $this->getForDoctor($user->doctor->id);
        }
        return $this->getAll();
    }
}
