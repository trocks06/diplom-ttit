<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Role;
use App\Sorts\SortByRating;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

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
            $doctor->load(['user.role', 'specializations']);
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

    public function getFilteredBuilder()
    {
        return QueryBuilder::for(Doctor::class)
            ->allowedIncludes(['user', 'specializations'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $searchTerm = '%' . $value . '%';
                    $query->whereHas('user', function ($q) use ($searchTerm) {
                        $q->where('lastname', 'like', $searchTerm)
                            ->orWhere('firstname', 'like', $searchTerm)
                            ->orWhere('patronymic', 'like', $searchTerm);
                    });
                }),
                AllowedFilter::callback('specialization_id', function ($query, $value) {
                    $values = is_array($value) ? $value : [$value];
                    $query->whereHas('specializations', function ($q) use ($values) {
                        $q->whereIn('specializations.id', $values);
                    });
                }),
            ])
            ->allowedSorts([
                'id',
                'created_at',
                AllowedSort::custom('rating', new SortByRating()),
            ])
            ->defaultSort('-created_at');
    }
}
