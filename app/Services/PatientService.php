<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PatientService extends BaseService
{
    protected UserService $userService;

    public function __construct(Patient $patient, UserService $userService)
    {
        parent::__construct($patient);
        $this->userService = $userService;
    }

    public function create(array $data): Model
    {
        $defaultRole = Role::where('role_name', 'Пациент')->firstOrFail();
        $data['role_id'] = $defaultRole->id;
        return DB::transaction(function () use ($data) {
            $user = $this->userService->create($data);
            $patient = $user->patient()->create([
                'address'          => $data['address'],
                'gender'           => $data['gender'],
                'birth_date'       => $data['birth_date'],
                'allergies'        => $data['allergies'] ?? null,
                'chronic_diseases' => $data['chronic_diseases'] ?? null,
            ]);
            $patient->load(['user.role']); // ← подгружаем user.role
            return $patient;
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $patient = $this->find($id);

            // Обновляем юзера через UserService
            $this->userService->update($patient->user_id, $data);

            // Обновляем специфику пациента
            $patient->update(array_filter([
                'address'    => $data['address'] ?? null,
                'gender'     => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'allergies'        => $data['allergies'] ?? null,
                'chronic_diseases' => $data['chronic_diseases'] ?? null,
            ]));

            return $patient->load('user');
        });
    }
}
