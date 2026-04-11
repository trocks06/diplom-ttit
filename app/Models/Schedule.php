<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
	protected $table = 'schedules';

	protected $casts = [
		'doctor_id' => 'int',
		'start_time' => 'datetime',
		'end_time' => 'datetime'
	];

	protected $fillable = [
		'doctor_id',
		'start_time',
		'end_time'
	];

	public function doctor()
	{
		return $this->belongsTo(Doctor::class);
	}

	public function appointments()
	{
		return $this->hasMany(Appointment::class);
	}
}
