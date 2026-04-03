<?php

namespace App\Services;

use App\Models\Doctor;

class DoctorService extends BaseService
{
    public function __construct(Doctor $doctor)
    {
        parent::__construct($doctor);
    }

    public function getAllWithRelations()
    {
        return $this->model->with(['user', 'specializations'])->get();
    }
}
