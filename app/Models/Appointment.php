<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
	protected $table = 'appointments';

	protected $casts = [
		'patient_id' => 'int',
		'status_id' => 'int',
		'schedule_id' => 'int'
	];

	protected $fillable = [
		'patient_id',
		'status_id',
		'schedule_id'
	];

	public function patient()
	{
		return $this->belongsTo(Patient::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function schedule()
	{
		return $this->belongsTo(Schedule::class);
	}

	public function medical_record()
	{
		return $this->hasOne(MedicalRecord::class);
	}

	public function reviews()
	{
		return $this->hasMany(Review::class);
	}
}
