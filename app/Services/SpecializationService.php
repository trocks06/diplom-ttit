<?php

namespace App\Services;

use App\Models\Specialization;

class SpecializationService extends BaseService
{
    public function __construct(Specialization $specialization)
    {
        parent::__construct($specialization);
    }
}
