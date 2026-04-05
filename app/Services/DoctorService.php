<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class DoctorService extends BaseService
{
    public function __construct(Doctor $doctor)
    {
        parent::__construct($doctor);
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

    public function createDoctorWithUser(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            // 1. Создаем пользователя
            $user = User::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'patronymic' => $data['patronymic'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role_id'],
            ]);

            // 2. Создаем профиль врача, привязывая user_id
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'license' => $data['license'],
            ]);

            // 3. Если есть специальности, привязываем их
            if (!empty($data['specialization_ids'])) {
                $doctor->specializations()->sync($data['specialization_ids']);
            }

            return $doctor->load('user', 'specializations');
        });
    }
}
