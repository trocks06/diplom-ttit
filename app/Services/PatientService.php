<?php

namespace App\Services;

use App\Models\Patient;

class PatientService extends BaseService
{
    public function __construct(Patient $patient)
    {
        parent::__construct($patient);
    }
}
