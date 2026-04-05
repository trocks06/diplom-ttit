<?php

namespace App\Services;

use App\Models\Doctor;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DoctorService extends BaseService
{
    public function __construct(Doctor $doctor)
    {
        parent::__construct($doctor);
    }

    public function createDoctor(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            $doctor = $this->model->create($data);

            if (!empty($data['specialization_ids'])) {
                $doctor->specializations()->sync($data['specialization_ids']);
            }

            return $doctor->load(['user', 'specializations']);
        });
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $doctor = parent::create($data);

            if (!empty($data['specialization_ids'])) {
                $doctor->specializations()->sync($data['specialization_ids']);
            }

            return $doctor->load('specializations');
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $doctor = parent::update($id, $data);

            if (isset($data['specialization_ids'])) {
                $doctor->specializations()->sync($data['specialization_ids']);
            }

            return $doctor->load('specializations');
        });
    }
}
