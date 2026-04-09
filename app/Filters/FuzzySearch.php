<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FuzzySearch implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $value = '%' . $value . '%';
        $query->where(function ($q) use ($value) {
            $q->whereHas('patient.user', function ($uq) use ($value) {
                $uq->where('lastname', 'like', $value)
                    ->orWhere('firstname', 'like', $value)
                    ->orWhere('patronymic', 'like', $value);
            })
            ->orWhereHas('schedule.doctor.user', function ($uq) use ($value) {
                $uq->where('lastname', 'like', $value)
                    ->orWhere('firstname', 'like', $value)
                    ->orWhere('patronymic', 'like', $value);
            });
        });
    }
}
