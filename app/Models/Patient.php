<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class Patient extends Model
{
	protected $table = 'patients';

	protected $casts = [
		'user_id' => 'int',
		'birth_date' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'address',
		'gender',
		'allergies',
		'chronic_diseases',
		'birth_date'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function appointments()
	{
		return $this->hasMany(Appointment::class);
	}

    protected static function booted()
    {
        static::deleted(function ($patient) {
            $patient->user()->delete();
        });
    }
}
