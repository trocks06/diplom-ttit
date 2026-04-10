<?php

namespace App\Services;

use App\Models\Specialization;
use Illuminate\Validation\ValidationException;

class SpecializationService extends BaseService
{
    public function __construct(Specialization $specialization)
    {
        parent::__construct($specialization);
    }

    public function delete(int $id): bool
    {
        $specialization = $this->find($id);
        if ($specialization->doctors()->exists()) {
            throw ValidationException::withMessages([
                'message' => ['Невозможно удалить специализацию, так как с ней связаны врачи.']
            ]);
        }
        return parent::delete($id);
    }
}
