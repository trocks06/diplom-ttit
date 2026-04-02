<?php

namespace App\Services;

use App\Models\Status;

class StatusService extends BaseService
{
    public function __construct(Status $status)
    {
        parent::__construct($status);
    }
}
