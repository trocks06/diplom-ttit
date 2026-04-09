<?php

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SortByRating implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';
        $query->orderByRaw("
            (SELECT AVG(reviews.rating)
             FROM reviews
             INNER JOIN appointments ON appointments.id = reviews.appointment_id
             INNER JOIN schedules ON schedules.id = appointments.schedule_id
             WHERE schedules.doctor_id = doctors.id
            ) {$direction}
        ");
    }
}
