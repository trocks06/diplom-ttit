<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Schedule
 *
 * @property int $id
 * @property int|null $doctor_id
 * @property Carbon|null $start_time
 * @property Carbon|null $end_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Doctor|null $doctor
 * @property Collection|Appointment[] $appointments
 *
 * @package App\Models
 */
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
