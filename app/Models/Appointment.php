<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Appointment
 * 
 * @property int $id
 * @property int|null $patient_id
 * @property int|null $status_id
 * @property int|null $schedule_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Patient|null $patient
 * @property Status|null $status
 * @property Schedule|null $schedule
 * @property MedicalRecord|null $medical_record
 * @property Collection|Review[] $reviews
 *
 * @package App\Models
 */
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
