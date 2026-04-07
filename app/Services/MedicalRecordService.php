<?php

namespace App\Services;

use App\Models\MedicalRecord;

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
}
