<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class DoctorService extends BaseService
{
    protected UserService $userService;

    public function __construct(Doctor $doctor, UserService $userService)
    {
        parent::__construct($doctor);
        $this->userService = $userService;
    }

    public function create(array $data): Model
    {
        $defaultRole = Role::where('role_name', 'Врач')->firstOrFail();
        $data['role_id'] = $defaultRole->id;
        return DB::transaction(function () use ($data) {
            $user = $this->userService->create($data);

            $doctor = $user->doctor()->create([
                'license' => $data['license'],
            ]);

            if (!empty($data['specialization_ids'])) {
                $doctor->specializations()->sync($data['specialization_ids']);
            }

            $doctor->load(['user.role', 'specializations']); // ← подгружаем role
            return $doctor;
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $doctor = $this->find($id);

            $this->userService->update($doctor->user_id, $data);

            $doctor->update(array_filter([
                'license' => $data['license'] ?? null,
            ]));

            if (isset($data['specialization_ids'])) {
                $doctor->specializations()->sync($data['specialization_ids']);
            }

            return $doctor->load(['user', 'specializations']);
        });
    }
}
