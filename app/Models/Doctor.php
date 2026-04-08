<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctors';

    protected $casts = [
        'user_id' => 'int'
    ];

    protected $fillable = [
        'user_id',
        'license'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'doctor_specializations');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function averageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }

    public function reviews()
    {
        return Review::whereHas('appointment.schedule', function ($q) {
            $q->where('doctor_id', $this->id);
        });
    }
}
