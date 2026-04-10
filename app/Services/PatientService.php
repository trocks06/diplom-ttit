<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
        if (auth()->user()?->role->role_name === 'Администратор') {
            $data['email_verified_at'] = now();
        }
        return DB::transaction(function () use ($data) {
            $user = $this->userService->create($data);
            $patient = $user->patient()->create([
                'address'          => $data['address'],
                'gender'           => $data['gender'],
                'birth_date'       => $data['birth_date'],
                'allergies'        => $data['allergies'] ?? null,
                'chronic_diseases' => $data['chronic_diseases'] ?? null,
            ]);
            return $patient;
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $patient = $this->find($id);
            $this->userService->update($patient->user_id, $data);
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

    public function getFilteredBuilder()
    {
        return QueryBuilder::for(Patient::class)
            ->allowedIncludes(['user'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $searchTerm = '%' . $value . '%';
                    $query->whereHas('user', function ($q) use ($searchTerm) {
                        $q->where('lastname', 'like', $searchTerm)
                            ->orWhere('firstname', 'like', $searchTerm)
                            ->orWhere('patronymic', 'like', $searchTerm)
                            ->orWhere('phone', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm);
                    });
                }),
                AllowedFilter::exact('gender'),
                AllowedFilter::callback('has_allergies', function ($query, $value) {
                    if ($value === 'true' || $value === '1') {
                        $query->whereNotNull('allergies')->where('allergies', '!=', '');
                    } elseif ($value === 'false' || $value === '0') {
                        $query->where(function($q) {
                            $q->whereNull('allergies')->orWhere('allergies', '');
                        });
                    }
                }),
                AllowedFilter::callback('has_chronic_diseases', function ($query, $value) {
                    if ($value === 'true' || $value === '1') {
                        $query->whereNotNull('chronic_diseases')->where('chronic_diseases', '!=', '');
                    } elseif ($value === 'false' || $value === '0') {
                        $query->where(function($q) {
                            $q->whereNull('chronic_diseases')->orWhere('chronic_diseases', '');
                        });
                    }
                }),
            ])
            ->allowedSorts([
                'id',
                'created_at',
                'birth_date'
            ])
            ->defaultSort('-created_at');
    }
}
